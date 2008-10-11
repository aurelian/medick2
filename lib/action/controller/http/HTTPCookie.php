<?php
//
// $Id: Cookie.php 444 2007-07-20 17:57:43Z aurelian $
//

/**
 * It's a HTTPCookie 
 *
 * @package medick.action.controller
 * @subpackage http
 * @author Aurelian Oancea
 */

class HTTPCookie extends Object {
    
    /** @var string
        Cookie name */ 
    private $name;

    /** @var string
        Cookie value */ 
    private $value;

    /** @var int
        Cookie expire */ 
    private $expire;
    
    /** @var string
        Cookie path */ 
    private $path;
    
    /** @var string 
        Cookie domain */
    private $domain;

    /** @var bool
        Cookie secure */ 
    private $secure;
    
  /**
   * Creates A new Cookie
   *
   * XXX: fix path and domain somehow for eg. Medick installation not in root folder or on subdomains
   *
   * @param string Cookie name
   * @param string Cookie value
   * @param int 
   * @param string Cookie path
   * @param string Cookie domain
   * @param bool    
   */ 
  public function __construct($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false) {
    $this->name   = $name;
    $this->value  = $value;
    $this->expire = $expire;
    $this->path   = $path;
    $this->domain = $domain;
    $this->secure = $secure;
  }

  public function name() {
    return $this->name;
  }

  public function value() {
    return $this->value;
  }

  public function toString() {
    return (
      $this->name . '=' . 
      ($this->value === '' ? 'deleted' : $this->value).
      ($this->expire !== 0 ? '; expires=' . gmdate('D, d-M-Y H:i:s \G\M\T', $this->expire) : '').
      ($this->path !== '' ? '; path=' . $this->path : '').
      ($this->domain !== '' ? '; domain=' . $this->domain : '').
      ($this->secure ? '; secure' : '')
    );
  }
}

