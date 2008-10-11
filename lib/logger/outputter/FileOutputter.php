<?php
//
// $Id: FileOutputter.php 430 2007-01-22 07:58:24Z aurelian $
//

/**
 * It writes logging messages to a file
 * 
 * @package medick.logger
 * @subpackage outputter
 * @author Oancea Aurelian
 */
class FileOutputter extends Outputter {

    /** @var resource
        file handler */
    private $handler;

    /**
     * Initialize this outputter
     * 
     * @param int, level, this outputter individual level
     * @param string the file to write on
     */
    public function initialize() {
        $file= $this->getProperty('path');
        if (!is_file($file)) {
            touch($file);
        }
        $this->handler = fopen($file, 'a');
    }

    /**
     * Closes the handler on exit
     */
    public function __destruct() {
        if ($this->handler) {
            fclose($this->handler);
        }
    }

    /** 
     * It writes the message
     *
     * @param string message
     */
    protected function write($message) {
        if (flock($this->handler, LOCK_EX|LOCK_NB)) {
            fwrite($this->handler, $message . "\n");
        }
        return;
    }
}
