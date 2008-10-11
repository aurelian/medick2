<?php

class Object {

  public function class_name() {
    return get_class($this);
  }

  public function toString() {
    return $this->class_name();   
  }

  public function __toString() {
    return $this->toString();
  }

}

