<?php

// $Id: $

/**
 * It is a Response that a medick application will always try to build.
 * 
 * A Dispatcher will know how to dump the buffer of this response back to the user.
 * 
 * @package medick.action.controller
 * @author Aurelian Oancea
 */
class Response extends Object {
  
  /** @var string
    response content */
  public $content;

  /** 
   * Echos the content (buffer)
   */
  public function dump() {
    echo $this->content;
  }

}

