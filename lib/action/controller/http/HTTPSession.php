<?php
//
// $Id: Session.php 431 2007-06-12 14:37:19Z aurelian $
//

/**
 * A wrapper around PHP session handling
 *
 * @package medick.action.controller
 * @subpackage http
 * @author Aurelian Oancea
 */
class HTTPSession extends Object {

  /** @var bool
     started flag */
  private $isStarted = false;

  /** @var ISessionContainer
      the session container */
  private $container = null;
  
  /** @var string
      Session name */
  public $name = 'msessid';
    
  /**
   * Constructor, creates a new session object
   *
   * @throws IllegalStateException if the session is started
   */
  public function __construct() {
    if ($this->isStarted) throw new IllegalStateException('Session already Started!');
  }

  /**
   * Starts a new Session
   *
   * Also, it setup our session preferences
   * @return Session
   * @throws IllegalStateException if the session is already started
   */
  public function start() {
    if ($this->isStarted) throw new IllegalStateException('Session already Started!');
    
    // XXX: more settings from xml config file
    // session_cache_limiter("nocache");
    // XXX: allow name to be passed in xml config file as web.session.name
    session_name($this->name);
    if ($this->container !== null) {
      session_set_save_handler(
        array($this->container, 'open'),
        array($this->container, 'close'),
        array($this->container, 'read'),
        array($this->container, 'write'),
        array($this->container, 'destroy'),
        array($this->container, 'gc'));
    }
    session_start();
    $this->isStarted= true;
    return $this;
  }

  /**
   * Sets a session variable
   *
   * @param string name, the name of the session variable
   * @param mixed value, the value of the variable to set
   * @return void
   */
  public function put($name, $value) {
    $this->checkState();
    $_SESSION[$name] = $value;
  }

  /**
   * Gets a session variable value
   *
   * @param string name, the name of the session variable
   * @return null if the variable is not set, or mixed, the variable value
   */
  public function get($name) {
    $this->checkState();
    return isset($_SESSION[$name])? $_SESSION[$name] : null;
  }

  /**
   * Remove the session value with the given name
   *
   * @param string name, the name of the session variable
   * @return void
   */
  public function remove($name) {
    $this->checkState();
    // unset($_SESSION[$name]);
    session_unregister($name);
  }

  /**
   * It gets the session id
   *
   * @return mixed, the session id
   */
  public function id(){
    $this->checkState();
    return session_id();
  }

  /**
   * It sets the session container
   *
   * @param ISessionContainer container to set
   * @return void
   */
  public function container(ISessionContainer $container) {
    $this->container= $container;
    return $this;
  }

  /**
   * It dumps the session
   *
   * @return array
   */
  public function toArray() {
    $this->checkState();
    return $_SESSION;
  }

  /**
   * It checks the session state
   *
   * This method is called internally to ensure that the session is started before using it.
   * @return TRUE if the session is started
   */
  protected function checkState() {
    // XXX: review!, I plan to pass a context to start so it will know how to setup it's internal state, then this will throw an exception!
    // XXX: the session should be started only from the ActionController
    if ($this->isStarted === false) $this->start();
    return true;
  }
}

