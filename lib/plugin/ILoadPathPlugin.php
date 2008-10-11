<?php

// $Id: ILoadPathPlugin.php 481 2008-06-01 14:26:18Z aurelian $

interface ILoadPathPlugin {

  /*
   * Should return a load path for app
   */ 
  public function load_path();
}

