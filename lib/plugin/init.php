<?php

// Plugin Framework Autoload Definition

function __plugin_autoload($class) {
  $file= 'plugin'.DIRECTORY_SEPARATOR.$class.'.php';
  if(is_file( dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php' )) {
    return require $file;
  }
}

spl_autoload_register('__plugin_autoload');

