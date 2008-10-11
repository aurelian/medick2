<?php

// $Id: Router.php 482 2008-06-01 20:03:50Z aurelian $

class Router extends Object {

  // current Route
  private $route;

  // current context
  private $context;

  private function __construct( Route $route, ContextManager $context ) {
    $this->route= $route;
    $this->context= $context;
  }

  private function load_controller($controller, $path) {
    $controller_file = $path . 'app/controllers/' . $controller . '_controller.php';
    $controller_class= ucfirst($controller) . 'Controller';
    if(file_exists($controller_file)) {
      return array($controller_file, $controller_class);
    } else {
      return false;
    }
  }

  // should return a Controller Instance
  public function create_controller( $request ) {
    $loaded= false;
    foreach($this->context->load_paths() as $path) {
      if(list($controller_file, $controller_class)= $this->load_controller($request->parameter('controller'), $path)) {
        $loaded= true;
        break;
      }
    }

    if($loaded === false) {
      throw new Exception('Cannot load controller `' . $request->parameter('controller') . 
        '`, searched in `'.join(', ', $this->context->load_paths()).'`');
    }

    // load ApplicationController if any.
    foreach($this->context->load_paths() as $load_path) {
      if(file_exists($load_path.'app/controllers/application.php')) {
        require $load_path.'app/controllers/application.php';
        if(class_exists('ApplicationController')) break;
      }
    }

    require( $controller_file );

    if( false === class_exists($controller_class) ) {
      throw new Exception('Expected `' . $controller_file . '` to define `'.$controller_class.'`');
    }

    $rclass= new ReflectionClass($controller_class);

    if( false === ($rclass->getParentClass() || $rclass->getParentClass() == 'ApplicationController' 
      || $rclass->getParentClass() == 'ActionController')
    ) {
      throw new Exception('Expected `' . $controller_class . '` to extend ApplicationController(recommended) or ActionControler');
    }
    
    // XXX: the $path
    $this->context->logger()->debug(str_replace($path, 
      '${'.$this->context->config()->application_name().'}/', $controller_file) . ' --> ' . $controller_class
    );

    return $rclass->newInstance( $this->context );

  }

  /*
   * Should return a controller instance
   */ 
  public static function recognize(Request $request, ContextManager $context ) {
    // XXX: request URI hack, for medick installation in subfolders (e.g. `medick2` as base)
    // XXX: test with other servers and other types of PHP installations
    // $request->uri= substr($request->uri, strlen($context->config()->property('base', true)));

    $router= new Router( $context->map()->find_route( $request ), $context);
    return $router->create_controller( $request );
  }

}

