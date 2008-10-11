<?php

// $Id: CLIRequest.php 477 2008-05-19 08:10:31Z aurelian $

class CLISession extends Object {
  
  public function start() {

  }

}

//
// xxx. should be able to handle:
//  
//  php script.php --uri=/foo/bar --method=GET
//
// just to make sure we emulate http requests.
// 
class CLIRequest extends Request {

  public $uri= "";

  private $session= null;

  public function __construct() {
    $this->uri= '/' . join('/', array_slice($_SERVER['argv'], 1, $_SERVER['argc']));

    $this->session= new CLISession();
  }

  public function session() {
    return $this->session;
  }

  public function toString() {
    return sprintf('cli: %s', $this->uri);
  }

}

