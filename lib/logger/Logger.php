<?php

/**
 * The Logger.
 *
 * @package medick.logger
 * @author Aurelian Oancea
 */
class Logger extends Object implements ILogger {

  /** a fancy way of telling the level */
  const DEBUG = 0;
  const INFO  = 1;
  const WARN  = 2;
  const ERROR = 3;

  /** @var array list with allowed levels */
  private $levels = array('debug','info','warn','error');

  /** @var int default priority level */
  private $level = 0;

  /** @var Formatter */
  private $formatter;

  /** @var array list of IOutputters[] */
  private $outputters = array();

  /** @var LoggingEvent the event to log */
  private $event = NULL;

  /** @var int message level */
  private $messageLevel;

  public static function formatter(IConfigurator $config) {
    $class= ucfirst($config->logger_formatter()).'Formatter';
    return new $class;
  }

  public static function outputters(IConfigurator $config) {
    $instances= array();
    foreach($config->logger_outputters() as $outputter) {
    $class= ucfirst((string)trim($outputter['name'])) . 'Outputter';
    $klass = new $class( $outputter['level'] );
    foreach($outputter->property as $property) {
      $klass->setProperty( (string)trim($property['name']), (string)trim($property['value']) );
    }
    $klass->initialize();
    $instances[]= $klass;
    }
    return $instances;
  }

  /** __magic __overloading__ */
  public function __call($method, $message=false) {
    if ($message===false || sizeof($this->outputters) == 0) return;

    if (!in_array($method, $this->levels)) {
      trigger_error(
        sprintf('Call to undefined function: %s::%s(%s).', $this->getClassName(), $method, $message), E_USER_ERROR
      );
    }
    foreach ($this->levels as $_level=>$_name) {
      if($_name == $method) break;
    }
    if ($_level < $this->level) return;
    $this->messageLevel = $_level;
    $this->event = new LoggingEvent($message[0], $method);
    $this->notify();
  }

  /*
   * Experimental addon that acts as sprintf only with %s format for this Logger
   */ 
  public function debugf($e) {
    $args= func_get_args();
    $str = array_shift($args);
    if(sizeof($args) > 0) {
      while($line= array_shift($args)) {
        $str= preg_replace( '/%s/', $line, $str, 1 );
      }
    }
    return $this->debug( $str );
  }

  /**  Notify the outputters */
  public function notify() {
    foreach($this->outputters as $outputter) {
      $outputter->update($this);
    }
  }

  /**
   * Attach an outputter
   * 
   * @param IOutputter outputter the outputter
   */
  public function attach(IOutputter $outputter) {
    if ($this->contains($outputter)) return;
    $this->outputters[] = $outputter;
  }

  /**
   * Attach a bunch of outputters
   *
   * @param IOutputters[]
   */ 
  public function attachOutputters(Array $outputters) {
    foreach($outputters as $outputter) {
      $this->attach($outputter);
    }
  }

  /**
   * Check to see if the list outputters contains the given outputter.
   *
   * @param IOutputter outputter an outputter witch acts as an observer
   * @return bool
   */
  private function contains(IOutputter $outputter) {
    foreach ($this->outputters as $out) {
      if ($out->getClassName() == $outputter->getClassName()) {
        return true;
      }
    }
    return false;
  }

  /**
   * It gets the list with attached outputters
   * 
   * @return IOutputter[]
   */
  public function getOutputters() {
    return $this->outputters;
  }

  /**
   * It gets the last message level
   * 
   * @return int, the message level
   */
  public function getMessageLevel() {
    return $this->messageLevel;
  }

  /**
   * It gets the event
   * 
   * @return LoggingEvent
   */
  public function getEvent() {
    return $this->event;
  }

  /**
   * Set's the event formatter
   * 
   * @param Formatter formatter the formatter
   */
  public function setFormatter(Formatter $formatter) {
    $this->formatter = $formatter;
  }

  /**
   * It gets the formatter
   * 
   * @return Formatter
   */
  public function getFormatter() {
    return $this->formatter;
  }

  /**
   * It sets the logging level
   * 
   * @param Logger level the level, it can be Logger::DEBUG (0), Logger::INFO (1)...
   */
  public function setLevel($level) {
    $this->level = $level;
  }

  /**
   * It gets the level.
   * 
   * @return int the logging level
   */
  public function getLevel() {
    return $this->level;
  }
}
