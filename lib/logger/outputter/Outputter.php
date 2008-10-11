<?php
// {{{ License
// ///////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2005 - 2007 Oancea Aurelian < aurelian [ at ] locknet [ dot ] ro >
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//
//   * Redistributions of source code must retain the above copyright notice,
//   this list of conditions and the following disclaimer.
//   * Redistributions in binary form must reproduce the above copyright notice,
//   this list of conditions and the following disclaimer in the documentation
//   and/or other materials provided with the distribution.
//   * Neither the name of Oancea Aurelian nor the names of his contributors may
//   be used to endorse or promote products derived from this software without
//   specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
// DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
// FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
// DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
// SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
// CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
// OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
// OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
//
// $Id: Outputter.php 430 2007-01-22 07:58:24Z aurelian $
//
// ///////////////////////////////////////////////////////////////////////////////
// }}}

/**
 * Abstract Outputter
 * 
 * @package medick.logger
 * @subpackage outputter
 * @author Oancea Aurelian
 */
abstract class Outputter extends Object implements IOutputter {

    /** @var int
        individual outputter level*/
    protected $level;

    /** @var array
        this outputter properties */ 
    private $properties= array();

    /**
     * Creates a new Outputter
     * 
     * @param int level the minimum level to respond
     */ 
    public function __construct($level) {
        $this->level= $level;
    }

    public function hasProperty($property) {
        if (isset($this->properties[$property]) && $this->properties[$property] != '') {
            return TRUE;
        } else {
            throw new Exception('Outputter::properties dosent have `' . $property . '` as property');
        }
    }

    public function getProperty($property) {
        return $this->hasProperty($property) ? $this->properties[$property] : null;
    }

    public function setProperty($property, $value) {
        $this->properties[$property]= $value;
    }

    public function setProperties(Array $properties) {
        $this->properties= $properties;
    }

    /**
     * Receive the Logger update and writes the log event using the formatter
     */
    public function update(ILogger $logger) {
        if ($this->level <= $logger->getMessageLevel()) {
            $this->write($logger->getFormatter()->format($logger->getEvent()));
        }
    }

    // {{{ abstract methods
    /** Initialize this specific outputter */
    public abstract function initialize();
    /** It writes the message */
    protected abstract function write($message);
    // }}}
}
