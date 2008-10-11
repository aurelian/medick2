<?php

//  ActionController Framework Autoload Definition

function __action_controller_autoload($class) {

  // special case
  if($class == 'ActionController') {
    return require 'action/controller/Base.php';
  }

  $base= dirname(__FILE__) . DIRECTORY_SEPARATOR;

  // action/controller/http
  if(strpos(strtolower($class), 'http') !== false && is_file($base.'http'.DIRECTORY_SEPARATOR.$class.'.php') ) {
    return require 'action'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'http'.DIRECTORY_SEPARATOR.$class.'.php';
  }

  // action/controller/http
  if(strpos(strtolower($class), 'cli') !== false && is_file($base.'cli'.DIRECTORY_SEPARATOR.$class.'.php') ) {
    return require 'action'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'cli'.DIRECTORY_SEPARATOR.$class.'.php';
  }

  // the rest
  $file= 'action'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$class.'.php';
  if(is_file( dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php' )) {
    return require $file;
  }
}

spl_autoload_register('__action_controller_autoload');

