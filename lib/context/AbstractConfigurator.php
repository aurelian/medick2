<?php

/*
 * Should contain default values so the application can then run without a config file
 *
 * Should provide overwritable methods to access default values
 *
 */ 
abstract class AbstractConfigurator extends Object implements IConfigurator {

  protected $file;

  protected $environment;

  //
  // XXX: don't throw anything and load default values
  //
  public function __construct( $file, $environment ) {
    if(!is_file($file)) {
      throw new Exception( sprintf('Cannot load configuration file %s', $file) );
    }
    $this->file= $file;
    $this->environment= $environment;
  }

  public function file() {
    return $this->file;
  }

  public function environment() {
    return $this->environment;
  }

}
