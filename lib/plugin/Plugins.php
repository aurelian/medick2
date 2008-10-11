<?php

// $Id: Plugins.php 481 2008-06-01 14:26:18Z aurelian $

class Plugins extends Object {

  static private $registry= array();

  public static function loaded($plugin_name) {
    return isset(Plugins::$registry[$plugin_name]);
  }

  public static function add( IPlugin $plugin ) {
    Plugins::$registry[$plugin->name()]= $plugin;
  }


  // should return IPlugin[]
  public static function discover( ContextManager $context ) {
    // XXX: try to load plugins from <plugins> section
    if($context->config()->property( 'plugin.autodiscovery' ) === false) return array();

    $context->logger()->debug( strtolower(__METHOD__) . ' [hint: set `plugin.autodiscovery` to false to disable plugins]');

    // plugins.path then fall to default
    $plugins_path= $context->config()->property( 'plugin.path' )? 
      $context->config()->property('plugin.path') : MEDICK_PATH . '/../../vendor/plugins';

    foreach(new DirectoryIterator( $plugins_path ) as $plugin_path) {
      Plugins::load_plugin($context, $plugin_path);
    }
    return Plugins::$registry;
  }

  private static function load_plugin(ContextManager $context, DirectoryIterator $plugin_path) {
    $plugin_load_file = $plugin_path->getPathname() . DIRECTORY_SEPARATOR . 'init.php';
    if( $plugin_path->isDir() && is_file($plugin_load_file) && require($plugin_load_file)) {
      $class= Plugins::plugin_class_name( $plugin_path );
      try {
        $klass = new ReflectionClass($class);
        Plugins::add( $klass->newInstance($context) );
        $context->logger()->debugf('%s --> %s', 
            str_replace(MEDICK_PATH, '${'.$context->config()->application_name().'}', $plugin_load_file), $class );
       } catch(ReflectionException $rfEx) {
         $context->logger()->warn('failed to load plugin `' . $plugin_path->getFilename() . '`: ' . $rfEx->getMessage());
      }
    }
  }

  // XXX: to be moved to Utils future framework (Inflector)
  private static function plugin_class_name(DirectoryIterator $plugin_path) {
    $plugin_name= $plugin_path->getFilename();
    return str_replace(" ", "", ucwords(str_replace("_", " ", $plugin_name))) . 'Plugin';
  }

}

