<?php
//
// $Id: ILogger.php 431 2007-06-12 14:37:19Z aurelian $
//

/**
 * Logger interface
 * 
 * @package medick.logger
 * @author Aurelian Oancea
 */
interface ILogger {

    /**
     * Attaches an Output Appender.
     * 
     * @param Outputter appender
     */
    function attach(IOutputter $outputter);

    /**
     * Notifies an outputter
     */
    function notify();
}
