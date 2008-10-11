<?php

// $Id: ITemplateEngine.php 477 2008-05-19 08:10:31Z aurelian $

interface ITemplateEngine {

  public function partial($partial);

  public function render($file);

  public function assign($name, $value);

}

