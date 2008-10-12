<?php

class Error extends Exception {

  public $file, $line, $trace;

  public function __construct($message, $code, $file, $line, $trace ) {
    parent::__construct($message, $code);
    $this->file= $file;
    $this->line= $line;
    $this->trace= $trace;
  }

}

/**
 * The Medick Error Handler.
 *
 * @package medick.core
 * @author Aurelian Oancea
 */
class ErrorHandler extends Object {

  /**
   * Setup this ErrorHandler
   */
  public function __construct() {
    ini_set('docref_root', null);
    ini_set('docref_ext', null);
  }

  /**
   * Raise An Error
   * 
   * @param int errno
   * @param string errstr
   * @param string errfile
   * @param int errline
   * @throws Error
   */
  function raiseError($errno, $errstr, $errfile, $errline) {
    $errRep = error_reporting();
    if(($errno & $errRep) != $errno) return;

    $trace = debug_backtrace();
    array_shift($trace);
    throw new Error( $errstr, $errno, $errfile, $errline, $trace );
  }
}
