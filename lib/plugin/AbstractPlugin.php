<?php

// $Id: AbstractPlugin.php 481 2008-06-01 14:26:18Z aurelian $

abstract class AbstractPlugin extends Object implements IPlugin {

  protected $context;

  protected $metadata;

  public function __construct( ContextManager $context ) {
    $this->context= $context;
  }

  public function is_type( $name ) {
    try {
      $k = new ReflectionClass($this->name());
      return $k->implementsInterface($name);
    } catch(ReflectionException $rfEx) {
      $this->context->logger()->debug('will skip plugin '.$this->name().', failed to check type `'.$name.'`: '.$rfEx->getMessage());
      return false;
    }
  }

}

