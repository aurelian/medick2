#!/usr/bin/env php
<?php

function make_path() {
  $__x = func_get_args();
  return join( DIRECTORY_SEPARATOR, $__x );
}

function create_folder($path) {
  try {
    mkdir( $path, 0755, true );
    p('created', $path);
  } catch(Error $err) {
    p('exists', $path);
  }
}

function create_file($path, $file, $target, Array $vars= array()) {
  if(sizeof($vars) > 0)
    extract($vars, EXTR_SKIP);

  switch(end(explode('.', $file))) {
    case 'php':
      $header= "<?php\n";
      break;
    case 'xml':
      $header= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
      break;
    default:
      $header= "";
      break;
  }

  $from_file= make_path( dirname(__FILE__), '..', 'skel', $file );
  $to_file  = make_path( $path, $target );
  $hash     = file_exists($to_file)? md5( file_get_contents($to_file) ) : null;

  ob_start();
  include_once( $from_file );
  $c = ob_get_contents();
  ob_end_clean();

  $contents= $header . $c;
  
  if( md5($contents) == $hash )
    p('identical', $to_file);
  else
    file_put_contents( $to_file, $contents );

}

function p($action, $message) {
  echo '[ '. $action . " ]=> " . $message . "\n";
}

function usage( ConsoleOptions $c) {
  echo sprintf( "%s, usage:\n\n  * generate [generator]\n  * task [a task]\n\n
Generators:\n  * app [name] will generate a medick2 skeleton application in [name]\n", $c->getScriptName() );
  exit();
}

function generate_application( $app_path ) {
  $app_name     = strtolower(end(split(DIRECTORY_SEPARATOR, $app_path)));
  $app_real_path= realpath($app_path);
  p('info', sprintf('will generate application %s in %s ', ucfirst($app_name), $app_real_path));

  create_folder( $app_real_path );
  foreach(array('app', 'public', 'config', 'vendor', 'lib', 'log') as $f) {
    create_folder( make_path($app_real_path, $f) );
  }

  $medick_path= realpath( make_path(dirname(__FILE__), '..') );

  create_file( $app_real_path, 'boot.php', 'boot.php', 
    array('app_real_path' => $app_real_path, 'medick_path' => $medick_path));
  create_file( $app_real_path, 'config.xml', make_path('config', $app_name.'.xml'), 
    array('app_name' => $app_name, 'app_real_path' => $app_real_path) );
  create_file( $app_real_path, 'htaccess', make_path('public', '.htaccess'), 
    array('app_name' => $app_name));
  create_file( $app_real_path, 'index.php', make_path('public', 'index.php'), 
    array('app_name' => $app_name, 'app_real_path' => $app_real_path));

  exit();
}

class Object {  }

error_reporting(E_ALL|E_NOTICE|E_RECOVERABLE_ERROR);

require make_path( dirname(__FILE__), '..', 'lib', 'core', 'ErrorHandler.php' );
require make_path( dirname(__FILE__), '..', 'lib', 'utils', 'ConsoleOptions.php' );

set_error_handler( array(new ErrorHandler(), 'raiseError') );


$c= new ConsoleOptions( ); //$_SERVER['argv'] );

$c->setNoValueFor('generate');
$c->load( $_SERVER['argv'] );

$c->alias('generate', '-g');
$c->alias('app');
$c->alias('controller', '-c');

$c->alias('task', '-t');


if( $c->has( 'generate' ) ) {

  // we need to run a generator
  if($c->has('app'))
    generate_application( $c->get() );
  elseif($c->has('controller'))
    p( 'controller', 'Not Implemented' );
  else
    usage( $c );
  exit(0);

} elseif( $c->has('task') ) {
  p('umm', 'Not Implemented :|');
  exit(127);
} else {
  usage( $c );
}

p( date('H:i:s'), 'options loaded ' . $c->getScriptName());

var_dump( $c->getOptions() );

// array_shift( $_SERVER['argv'] );

