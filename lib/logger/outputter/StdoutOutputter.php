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
// $Id: StdoutOutputter.php 430 2007-01-22 07:58:24Z aurelian $
//
// ///////////////////////////////////////////////////////////////////////////////
// }}}

/**
 * StdoutOutputter
 * 
 * @package medick.logger
 * @subpackage outputter
 * @author Oancea Aurelian
 */
class StdoutOutputter extends Outputter {

    /** @var bool
        FALSE if the sapi name is cli */
    private $isCLI = FALSE;
    
    /** @var string
        end of line style (can be \n for cli or html) */
    private $eol;
    
    /** @var string
        buffer */
    private $output;

    /**
     * It initialize this Outputter
     */
    public function initialize() {
        if (php_sapi_name() == 'cli') {
            $this->isCLI = TRUE;
            $this->eol = "\n";
            $this->output .= $this->eol;
        } else {
            $this->output .= '<table border="1" style="font-family: verdana;font-size: 0.8em;background-color:white" width="100%"><tr><td>';
            $this->eol =  '</td></tr><tr><td>';
        }
    }

    /** 
     * It flushes (echoes) the output buffer on exit 
     */
    public function __destruct() {
        if ($this->isCLI) {
            $this->output .= $this->eol;
        } else {
            $this->output .= '</td></tr></table>';
        }
        echo $this->output;
    }

    /** 
     * It writes the message to the buffer 
     */
    protected function write($message) {
        $this->output .= $message . $this->eol;
    }

    /** 
     * It gets the output 
     */
    public function getOutput() {
        return $this->output;
    }
}
