<?php
//
// $Id: HTTPRequest.php 451 2007-08-01 14:57:17Z aurelian $
//

/**
 * A HTTPRequest
 *
 * This is known to work with PHP installed as mod_php with apache, 
 * for other types of installation please contact me at aurelian [ at ] locknet [ dot ] ro if you need advice!
 *
 * @todo unified Headers list (eg, convert all the headers to small caps)
 * @todo test with php as cgi and with php with lighttpd
 *
 * @package medick.action.controller
 * @subpackage http
 * @author Aurelian Oancea
 */
class HTTPRequest extends Request {

  /** @var string
      request method */
  private $method;
    
  /** @var Session */
  private $session;

  /** @var string
      path_info, /foo/bar.html */
  public $uri= '/';

  /** @var array
      the list of headers associated with this HTTPRequest */
  private $headers= array();

  /** @var array
      cookies list */
  private $cookies= array();
    
  public function __construct() {
    // figure-out the method
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
      $this->method= 'xhr';
    else $this->method= isset($_SERVER['REQUEST_METHOD'])? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
    // merge get/post and set parameters.
    $this->parameters(array_merge($_GET,$_POST));
    // set cookies (xxx--> cookie domain and other infos?)
    foreach ($_COOKIE as $cookie_name => $cookie_value) {
      $this->cookies[$cookie_name]= new HTTPCookie($cookie_name, $cookie_value);
    }
    // do the undoable
    unset($_REQUEST); unset($_GET); unset($_POST);

    // setup requestUri
    if (array_key_exists('PATH_INFO', $_SERVER) && $_SERVER['PATH_INFO'] != '' ) {
      $this->uri= $_SERVER['PATH_INFO'];
    } else {
      $this->uri= '/';
    }
    /*
    // this is for php as cgi where PATH_INFO is not available
    elseif (array_key_exists('ORIG_PATH_INFO', $_SERVER)) {
      // todo: it should be also tested for non root locations eg:
      // http://www.example.com/foo/medick/myapplication/project/create.html 
      // should substract only /project/create.html!
      // even if we don't use rewrite mode (rewrite=off in config file) this should work.
      $this->uri= $_SERVER['ORIG_PATH_INFO']; 
    } else {
      // fallback to REQUEST_URI
      $this->uri= $_SERVER['REQUEST_URI'];
      // $this->uri= substr($_SERVER['REQUEST_URI'], 8);
    }
    */
    // setup session and headers
    $this->session = new HTTPSession();
    $this->headers = getallheaders();
  }

  public function toString() {
    return strtoupper($this->method) . ' ' . $this->uri;
  }

  /**
   * Get the current request method
   *
   * @return string the method of this request (POST/GET/HEAD/DELETE/PUT)
   */ 
  public function method() {
    return $this->method;
  }
    
  /**
   * Check if this request was made using POST
   *
   * @return bool true if it's a POST
   */ 
  public function is_post() {
    return $this->method == 'post';
  }
    
  /**
   * Check if this Request was made using GET
   *
   * @return bool true if it was GET
   */ 
  public function is_get() {
    return $this->method == 'get';
  }
    
  /**
   * Check if this Request was made with an AJAX call (Xhr)
   *
   * @return bool true if it was Xhr
   */ 
  public function is_xhr() {
    return $this->method == 'xhr';
  }    
    
  /**
   * Gets an array of Cookies
   *
   * @return array
   */ 
  public function cookies() {
    return $this->cookies;
  }

  /**
   * It gets a cookie by it's name
   *
   * @param string cookie name
   * @return Cookie or FALSE if this Request don't have the requested cookie
   */ 
  public function cookie($name) {
    return isset($this->cookies[$name])? $this->cookies[$name] : null;
  }
    
  /**
   * It gets an array of headers associated with this request
   *
   * @return array
   */ 
  public function headers() {
    return $this->headers;
  }
    
  /**
   * It gets a header
   * 
   * @param strign name of the header to look for
   * @return string header value or FALSE if it don't have the header
   */ 
  public function header($name) {
    return isset($this->headers[$name])? $this->headers[ucfirst($name)] : null;
  }
    
  /**
   * It gets the Session
   * @return Session, the curent Session
   */
  public function session() {
    return $this->session;
  }

  /**
   * A wrapper around getallheaders apache function that gets a list
   * of headers associated with this HTTPRequest.
   *
   * @return array
   */
  // protected static function getAllHeaders() {
  //   return getallheaders();
  // }
}

