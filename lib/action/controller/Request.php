<?php
//
// $Id: Request.php 447 2007-07-23 17:44:43Z aurelian $
//

/**
 * It is an incoming request from the user
 * 
 * Contains various informations about the Request Parameters
 * @see HTTPRequest, Dispatcher, Route
 * @package medick.action.controller
 * @author Aurelian Oancea
 */
class Request extends Object {

  /** @var array
      current request parameters */
  private $params = array();
   
  public function parameter($name) {
    return (isset($this->params[$name]) && trim($this->params[$name]) != '')? $this->params[$name] : null;
  }

  /**
   * It adds an array of parameters on this Request
   *
   * @param array parameters, parameters name/value pairs
   * @return void
   */ 
  public function parameters( Array $parameters=array() ) {
    foreach ($parameters as $name=>$value) {
      $this->params[$name]= $value;
    }
  }
    
  /**
   * Gets a string representation of this Object
   *
   * @return string
   */ 
  public function toString() {
    $buff = "{".$this->class_name()."}-->\n";
    foreach ($this->params as $name=>$value) {
      $buff .= "[{$name}=";
      if (is_array($value)) {
        $buff .= "\n\t[Array:\n";
          foreach ($value as $k=>$v) {
            $buff .= "\t\t[{$k}=";
            if (is_array($v)) {
              $buff .= "[Array]\n";
              continue;
            }
            if (strlen($v)>75) {
              $buff .= substr(str_replace("","\n",$v),0,75) ." .....]\n";
            } else {
              $buff .= "$v]\n";
            }
          }
          $buff .= "]]\n";
      } else {
        $buff .= "{$value}]";
      }
    }
    return $buff;
  }
}
