<?php

// $Id: ExecutionChain.php 477 2008-05-19 08:10:31Z aurelian $

class ExecutionChain extends Object {

  private $chain;

  private $controller = null;

  private $context;

  public function __construct( ActionController $controller, ContextManager $context ) {
    // create the basic structure
    $this->chain = array(
      'before' => array(),
      'action' => array(), /* [0]-> ReflectionMethod, [1]-> array(Method Arguments) */
      'after'  => array()
    );

    $this->controller= $controller;
    $this->context= $context;
  }

  // adds something to this chain, to be used in plugins?
  // XXX: maybe add at position should also help?
  public function add($type, $value) {
    if( false === ($type == 'before' || $type == 'after'))
      throw new ChainError('Cannot add ' . $type . ' to ExecutionChain, only `before` or `after` are allowed');
    $this->chain[$type][]= $value;
  }

  /*
   * 1. figure-out the action
   * 2. collect parameters and assign
   * 3. invoke the action
   */ 
  public function validate_action(Request $request) {
    try {
      $rmethod= new ReflectionMethod( $this->controller, $request->parameter('action') );
    } catch(ReflectionException $rfEx) {
      throw new ChainError($rfEx->getMessage());
    }

    if( !$rmethod->isPublic() || $rmethod->isStatic() ) {
      throw new ChainError( 
        'Method `'.$rmethod->getName().'` should be declared as public on class instance `'.get_class($this->controller).'`' );
    }

    $rparams= $rmethod->getParameters();
    $action_args= array();

    foreach($rparams as $arg) {
      $arg_name = $arg->getName();
      $arg_value= $request->parameter( $arg_name );

      // XXX: detect behavior on Merb / Django
      if(null === $arg_value) {
        if($arg->isOptional())
          continue;
        else
          throw new ChainError('Mandatory agrument `' . $arg_name . '` for action `'.$rmethod->getName().'` not in request!');
      }
      $action_args[$arg_name]= $arg_value;
    }

    // ready to fire this action later
    $this->chain['action']= array($rmethod, $action_args);
    // return true for now
    return true;

  }

  private function debug_action_parameters($args) {
    $buff = array();
    foreach($args as $name=>$value) {
      $buff[]= sprintf( '%s: "%s"', $name, $value );
    }
    return '{'.join(',', $buff) . '}';
  }

  public function exec_before() {
    foreach($this->chain['before'] as $filter) {
      // 1. try to execute filter as a method

      // 2. try to create a class instance and call execute on it

      // 3. if the filter render an action or redirected, halt the execution chain
      // if($this->controller->action_performed === true) {
      //   $this->context->logger()->info( 'Execution chain halted by `' . $filter . '`' );
      // }
    }
  }

  public function exec_action() {
    
    $this->context->logger()->debug(sprintf('ready to invoke action `%s` with arguments `%s` (+ %.3f sec.)',
      $this->chain['action'][0]->getName(), $this->debug_action_parameters($this->chain['action'][1]), $this->context->timer()->tick()
    ));

    return $this->chain['action'][0]->invokeArgs( $this->controller, $this->chain['action'][1] );
  }

  public function exec_after() {
    foreach($this->chain['after'] as $filter) {
      // 1. try to execute filter as a method

      // 2. try to create a class instance and call execute on it

      // 3. the return value is not important, go to the next one
    }
  }

}
