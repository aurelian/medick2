<?php

// $Id: Route.php 482 2008-06-01 20:03:50Z aurelian $

// XXX: Route Segment
class __Segment extends Object {

  private $name;

  private $is_dynamic;

  public function __construct($name, $is_dynamic) {
    $this->name= $name;
    $this->is_dynamic= (bool)$is_dynamic;
  }

  public function name() {
    return $this->name;
  }

  public function is_dynamic() {
    return $this->is_dynamic;
  }

}

class Route extends Object {

  private $definition;

  private $segments;

  private $requirements;

  private $merges;

  private $id;

  private $name;

  // private static $old_merges   = array();
  // private static $old_defaults = array();

  // $name -> route name
  // $definition -> route signature :controller/foo/:id.html
  // $defaults -> default values
  // $requirements -> a route requirement
  public function __construct( $name, $definition, Array $defaults= array(), Array $requirements= array() ) {
    $this->name = $name;
    $this->definition   = $definition;
    $this->requirements = $requirements;
    $this->defaults     = $defaults;
    $this->id           = md5($this->definition);
    // internal structures
    $this->segments     = array();
    $this->merges       = array();
  }

  public function name() {
    return $this->name;
  }

  public function definition() {
    return $this->definition;
  }

  public function toString() {
    return sprintf('(%s:%s)-> %s', $this->name, $this->id, $this->definition);
  }

  private function load_segments() {
    $parts= explode('/', trim($this->definition, '/'));
    foreach ($parts as $key=>$element) {
      if (preg_match('/:[a-z0-9_\-]+/',$element, $match)) {
        $segment= new __Segment(substr(trim($match[0]), 1), true);
      } else {
        $segment= new __Segment($element, false);
      }
      $this->segments[]= $segment;
    }
  }

  private function merge(Request $request) {
    if( sizeof($this->merges) > 0 ) $request->parameters( $this->merges );
  }

  public function merges() {
    return $this->merges;
  }

  private function defaults(Request $request) {
    if( sizeof($this->defaults) > 0 ) $request->parameters($this->defaults);
  }

  // a Route is valid if it has a controller/action + all the requirements are meet
  // xxx. add requirements
  private function validate( Request $request ) {
    return !($request->parameter('controller') === null) || !($request->parameter('action') === null);
  }

  public function match( Request $request ) {
    $parts = ($request->uri === null)? array(): explode('/', trim($request->uri,'/'));
    // no. of parts
    $p_size= count($parts);
    // load segments,
    $this->load_segments();
    // no. of segments
    $s_size= count($this->segments);
    // if we have more parameters passed, as expected.
    if ( $p_size > $s_size ) {
      return false;
    }

    if( $p_size != 0 ) {
      for($i=0;$i<$s_size;$i++) {
        // access corresponding part.
        if(!isset($parts[$i])) continue;
        $segment= $this->segments[$i];
        $part   = $parts[$i];
        // if segment is not dynamic and segment name is not equal to current part without extension
        // eg. /foo defined while /bar requested :p
        if( !$segment->is_dynamic() && $segment->name () != $this->strip_ext($part) ) return false;
        // if a requirement is set on this segment and if it's not meet
        elseif( isset( $this->requirements[$segment->name()] )  &&
          !preg_match( $this->requirements[$segment->name()], $part )
        ) return false;
        // ready to merge then, but only if segment is not empty, xxx. identify from where we got an empty segment eg. /
        elseif(trim($segment->name())!='') $this->merges[$segment->name()] = $this->strip_ext($part);
        // nothing more
      }
    }
    
    // merge request parameters
    $this->merge( $request );

    // load default values
    $this->defaults( $request );

    // validate 
    return $this->validate( $request );
  }

  //
  // if 
  // -> c.bar is passed, c is returned :)
  // -> yahoo.html => yahoo
  //
  private function strip_ext($on) {
    if (false === strpos($on, '.html')) {
      $part = $on;
    } else {
      list($part)= explode('.', $on);
    }
    return $part;
  }

}

