<?php

// $Id: Dispatcher.php 482 2008-06-01 20:03:50Z aurelian $

class Dispatcher extends Object {

  private $context;

  private $plugins;

  public function __construct(ContextManager $context) {
    $this->context= $context;
  }

  public function dispatch() {
    $request = (php_sapi_name()=='cli')? new CLIRequest() : new HTTPRequest();
    $response= new HTTPResponse();

    $this->context->logger()->debug( 
      sprintf('medick v.$%s ready to dispatch (took %.3f sec. to boot)', Medick::version(), $this->context->timer()->tick()));

    try {
      Router::recognize( $request, $this->context )->process( $request, $response )->dump();
      $this->context->logger()->debug(sprintf( "\t[%s] done in %.3f sec.", time(), $this->context->timer()->tick(true)));
    } catch(Exception $ex) {
      $message= sprintf( '[%s] --> %s in %s line %s', get_class($ex), $ex->getMessage(), $ex->getFile(), $ex->getLine() );
      $this->context->logger()->warn( sprintf('Request processing failed after %.3f seconds', $this->context->timer()->tick()) );
      $this->context->logger()->warn( $message );
      // try to process with exception
      echo php_sapi_name()=='cli'? $message : '<pre>'.$message.'</pre>';
      echo php_sapi_name()=='cli'? $ex->getTraceAsString(): '<pre style="font-size:85%">' . $ex->getTraceAsString() . '</pre>';
    }
  }

}

