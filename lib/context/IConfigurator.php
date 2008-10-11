<?php

interface IConfigurator {

  public function __construct($file, $environment);

  public function environment();

  public function file();

  // return Array
  public function logger_outputters();

  // return string
  public function logger_formatter();

  // retrun mixed
  // -> $env false: lookup in global namespace
  // -> $env true : use the current environment
  public function property($name, $env= false);

  // return Array
  public function routes();

}

