<?php
//
// $Id: DefaultFormatter.php 431 2007-06-12 14:37:19Z aurelian $
//

/**
 * Default Logger formatter
 * 
 * @package medick.logger
 * @subpackage formatter
 * @author Aurelian Oancea
 */
class DefaultFormatter extends Formatter {
    /** @see medick.logger.formatter.Formatter::format(medick.logger.LoggingEvent event) */
    public function format(LoggingEvent $event) {
        return $event->level . " >> " . $event->message;
    }
}
