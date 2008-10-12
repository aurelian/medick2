<?php

// $Id: Medick.php 482 2008-06-01 20:03:50Z aurelian $

require 'medick/Object.php';
require 'medick/ErrorHandler.php';

//
// Create a way to unload frameworks?
//
class Medick extends Object {

  private static $frameworks = array();

  public static function prepare_application() {
    
    set_error_handler( array(new ErrorHandler(), 'raiseError') );

    Medick::load_framework('context');
    Medick::load_framework('logger');
    Medick::load_framework('plugin');
    Medick::load_framework('action_controller');
    Medick::load_framework('action_view');
    Medick::load_framework('active_record');
  }

  //
  // XXX-> define autoload function inline?
  // XXX-> register autoload function here?
  // XXX-> check for the existence of autoload function?
  //
  public static function load_framework( $name ) {
    // avoid loading the same framework twice
    if(in_array($name, Medick::$frameworks)) return;

    // if(function_exists('__'.$name.'_autoload')) {
    //   spl_autoload_register( '__'.$name.'_autoload' );
    // } else {
      // require la fisierul init din eg. active/record/init.php
      $init_file= MEDICK_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 
        str_replace( '_' , DIRECTORY_SEPARATOR, $name ) . DIRECTORY_SEPARATOR . 'init.php';
      if(false === file_exists($init_file))
        throw new Exception('Cannot load framework `'.$name.'` from `'.$init_file.'`, no such file.');
      require $init_file;
    //}

    Medick::$frameworks[]= $name;
  }

  public static function framework_loaded($name) {
    return isset(Medick::$frameworks[$name]);
  }

  public static function version() {
    return '2.0.22';
  }

  public static function dump($o) {
    echo "<pre>\n";var_dump($o);echo "\n</pre>";
    die();
  }

}
