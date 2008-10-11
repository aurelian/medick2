<?php

// $Id: IPlugin.php 482 2008-06-01 20:03:50Z aurelian $

interface IPlugin {

  public function __construct( ContextManager $context );

  /*
   * Should return the plugin metadata array
   */
  public function metadata();

  public function name();

  public function is_type($name);

}

