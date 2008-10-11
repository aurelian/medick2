<?php

// try {
//   Medick::depends_on('context');
// } catch(Exception $ex) {
//   Medick::load_framework('context');
// }

// Logger Framework AutoLoadder

function __logger_autoload($class) {
  $base = dirname(__FILE__).DIRECTORY_SEPARATOR;
  // logger/outputter
  if(strpos(strtolower($class), 'outputter') !== false && is_file($base.'outputter'.DIRECTORY_SEPARATOR.$class.'.php') ) {
    return require 'logger'.DIRECTORY_SEPARATOR.'outputter'.DIRECTORY_SEPARATOR.$class.'.php';
  }
  // logger/formatter
  if(strpos(strtolower($class), 'formatter') !== false && is_file($base.'formatter'.DIRECTORY_SEPARATOR.$class.'.php') ) {
    return require 'logger'.DIRECTORY_SEPARATOR.'formatter'.DIRECTORY_SEPARATOR.$class.'.php';
  }
  // the rest
  if(is_file( dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php' )) {
    return require 'logger'.DIRECTORY_SEPARATOR.$class.'.php';
  }
}

spl_autoload_register('__logger_autoload');

