<?php

//  Context Framework Autoload Definition

function __context_autoload($class) {
  $file= 'context'.DIRECTORY_SEPARATOR.$class.'.php';
  if(is_file( dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php' )) {
    return require $file;
  }
}

spl_autoload_register('__context_autoload');

