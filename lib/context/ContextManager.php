<?php

class Timer extends Object {

  private $init;
  private $start;
  private $end;

  public function __construct( $start ) {
    $this->init = $start;
    $this->start= $start;
    $this->end= null;
  }

  public function tick($from_init= false) {
    $this->end= microtime(true);
    $r= $from_init? (float)$this->end-(float)$this->init : (float)$this->end - (float)$this->start;
    $this->start= $this->end;
    return $r;
  }

}

// $Id: ContextManager.php 481 2008-06-01 14:26:18Z aurelian $

class ContextManager extends Object {

  // log everyware!
  private $logger;

  // the config parser/loaded, to have access to configuration options
  private $config;

  // a Map, you get access to Routes using this!
  private $map;
  
  // a Timer to benchmark critical points
  private $timer;
  
  // IPlugin[]
  private $plugins;

  // load_paths[]
  private $load_paths= array();

  private function __construct( IConfigurator $config ) {
    $this->config= $config;
    // configure the logger
    $this->logger= new Logger();
    $this->logger->setFormatter( Logger::formatter($this->config) );
    $this->logger->attachOutputters( Logger::outputters($this->config) );
    // ready?
    $ip= (array_key_exists('REMOTE_ADDR',$_SERVER))? $_SERVER['REMOTE_ADDR']: '0.0.0.0';
    $this->logger->debug("\t[".time() . "] `" . $this->config->environment() . "` env. from " . 
      str_replace(APP_PATH, '${'.$this->config->application_name().'}/', $this->config->file()) . " loaded for `${ip}`");
    
    // create a Map for routes.
    $this->map= new Map( $this );

    // load plugins
    $this->plugins = Plugins::discover( $this );
  }

  public function load_paths() {
    if(sizeof($this->load_paths) == 0) {
      foreach( $this->plugins as $plugin ) {
        if($plugin->is_type('ILoadPathPlugin')) $this->load_paths[]= $plugin->load_path();
      }
      $this->load_paths[]= APP_PATH;
    }
    return $this->load_paths;
  }

  public function plugins() {
    return $this->plugins;
  }

  public function logger() {
    return $this->logger;
  }

  public function config() {
    return $this->config;
  }

  public function map() {
    return $this->map;
  }

  public function timer($start= null) {
    if($this->timer===null) {
      $this->timer= new Timer($start===null? microtime(true) : $start);
    }
    return $this->timer;
  }

  public static function load( $file, $environment ) {
    $start= microtime(true);
    // XXX: factory based on the file type for configurator
    $context= new ContextManager(new XMLConfigurator( $file, $environment ));
    $context->timer($start)->tick();
    return $context;
  }

}
