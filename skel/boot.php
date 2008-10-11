<?php
//
// $Id: boot.php 452 2007-08-15 08:06:49Z aurelian $
//

/**
 * It boots a medick application
 *
 * @package medick.core
 */

define( 'APP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR );

// medick framework path.
define( 'MEDICK_PATH', join( DIRECTORY_SEPARATOR, 
                             array(dirname(__FILE__), 'vendor', 'medick')));

// rewrite system include path
set_include_path( MEDICK_PATH . DIRECTORY_SEPARATOR . 'lib'   . DIRECTORY_SEPARATOR );

// this should depend on environment
error_reporting( E_ALL | E_STRICT | E_RECOVERABLE_ERROR );

// php 5.1 strict sdandards.
if (version_compare(PHP_VERSION, '5.1.0') > 0) {
    date_default_timezone_set('Europe/Madrid');
}

// load core
require 'medick/Medick.php';

Medick::prepare_application();

