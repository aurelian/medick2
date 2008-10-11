<?php

class TemplateNotFoundError extends Exception {

}

// $Id: PHPTemplateEngine.php 482 2008-06-01 20:03:50Z aurelian $

class PHPTemplateEngine extends AbstractTemplateEngine {

  private $route_helpers= false;

  public function partial($partial) {

  }

  // $template-> path to file is searched trought load_paths
  public function render( $template ) {
    foreach($this->context->load_paths() as $path) {
      if(file_exists($path.'app/views/'.$template)) return $this->render_template_file($path.'app/views/'.$template);
    }
    throw new TemplateNotfoundError( 'Cannot render `'.$template.'` searched in ' . join(', ', $this->context->load_paths()) );
  }

  // $file -> complete path to a template file, ignoring load_paths
  public function render_template_file( $file ) {
    // XXX: load helper['s]
    if (sizeof($this->vars) > 0) {
      extract($this->vars,EXTR_SKIP);
    }

    if($this->route_helpers === false) {
      $this->create_route_helpers();
    }

    $this->context->logger()->debug(sprintf('render `%s` (+ %.3f sec.)', $file, $this->context->timer()->tick()));
    ob_start();
    include_once( $file );
    $c = ob_get_contents();
    ob_end_clean();
    $this->context->logger()->debug(sprintf('template parsed (+ %.3f sec.)', $this->context->timer()->tick()));
    return $c;
  }

  private function create_route_helpers() {
    $buff = "";
    foreach($this->context->map()->routes() as $route) {
      $buff .= "function {$route->name()}_path() {return '/{$this->context->config()->property('base', true)}{$route->definition()}';}";
    }
    eval($buff);
    $this->route_helpers= true;
  }

}

