<?php

// $Id: XMLConfigurator.php 481 2008-06-01 14:26:18Z aurelian $

class XMLConfigurator extends AbstractConfigurator {

  // SimpleXML
  private $sxe;
  
  // Environment, as SimpleXML
  private $env = null;

  private $properties = array();

  public function __construct($file, $environment) {
    parent::__construct( $file, $environment );

    // XXX: handle errors
    $this->sxe= simplexml_load_file($file);
    // find env in doc.
    foreach($this->sxe->environments->environment as $e) {
      if($e['name'] == $environment) {
        $this->env= $e;
        break;
      }
    }

    // XXX: custom ex.
    if($this->env === null) {
      throw new Exception( sprintf('Cannot load environment "%s" from %s', $environment, $file) );
    }

  }

  public function application_name() {
    return (string)trim($this->sxe['name']);
  }

  //
  // XXX:
  // -> check the usability of returning false, maybe we should throw an exception?
  // -> properties should be loaded in constructor
  // -> properties should have defaults
  //
  public function property($name, $env = false) {
    if(empty($this->properties)) $this->load_properties();
    $lookup = $env === false ? '__global_env' : (string)$this->env['name'];
    return isset($this->properties[$lookup][$name]) ? $this->properties[$lookup][$name] : false;
  }

  //
  // $properties=
  //
  //  array(1) {
  //    ["__global_env"]=>
  //    array(3) {
  //      ["load.paths"]=>
  //      string(0) ""
  //      ["plugin.autodiscovery"]=>
  //      bool(true)
  //      ["plugin.path"]=>
  //      string(14) "vendor/plugins"
  //    }
  //  }
  //
  private function load_properties() {
    foreach($this->sxe->properties->property as $prop) {
      $this->properties['__global_env'][(string)trim($prop['name'])]= $this->parse_prop_value($prop['value']);
    }
    if( 0 == sizeof($this->env->properties) ) return;
    foreach($this->env->properties->property as $prop) {
      $this->properties[(string)$this->env['name']][(string)trim($prop['name'])]= $this->parse_prop_value($prop['value']);
    }
  }

  //
  // transforms "true", "on" or "1" to (bool)true, "false", "off" or "0" to (bool)false
  //
  private function parse_prop_value($val) {
    $value= (string)trim($val);
    if(in_array($value, array('true', 'on', '1'))) return true;
    elseif(in_array($value, array('false', 'off', '0'))) return false;
    else return $value;
  }

  //
  // gets the routes as SXE objects
  //
  public function routes() {
    return $this->sxe->routes->route;
  }

  // here, route is a xml node
  //
  // should return array('segment_name'=>'segment_value', 'segment_name'=>'segment_value')
  //
  public function route_defaults($route) {
    $defaults= array();
    foreach($route->default as $def) {
      $defaults[(string)trim($def['name'])]= (string)trim($def['value']);
    }
    return $defaults;
  }

  // referes to a env. logger outputters
  public function logger_outputters() {
    if (is_null($this->env->logger->outputters)) return array();
    return $this->env->logger->outputters->outputter;
  }
  
  // referes to a env. logger formatter
  public function logger_formatter() {
    return (string)trim($this->env->logger->formatter);
  }

}

