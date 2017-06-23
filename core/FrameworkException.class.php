<?php

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

/**
* Base framework exception class
*/
final class FrameworkException extends \Exception {
    
    public function __construct(string $message, int $code = 0, \Throwable $previous = null) {
        
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        
        return "WebFramework Exception: {$this->message}\n";
    }
    
    /**
     * Renders the exception queue
     */
    public function renderAll() {
        
        $html = $this->getHeader();
        $html.= $this->getAll();
        $html.= $this->getFooter();
        
        echo $html;
    }
    
    /**
     * Gets the HTML string detail for all exceptions in queue
     * @return string
     */
    private function getAll() {
        
        $count = 2;
        $body = $this->getBody($this, 1);      
        
        $previous = $this->getPrevious();
        
        while ($previous !== null) {
            
            $body.= $this->getBody($previous, $count++);
            
            $previous = $previous->getPrevious();
        }
        
        return $body;
    }
    
    /**
     * Gets the HTML header 
     * @return string
     */
    private function getHeader() {
        
        $template = 
<<<EOD
<!DOCTYPE html>
<html>
    <head>
        <title>Niuware WebFramework Exception Found</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    </head>
    <body style="background-color:#fefefe;">
EOD;
        return $template;
    }
    
    /**
     * Gets the HTML footer
     * @return string
     */
    private function getFooter() {
        
        $template = '</body></html>';
        
        return $template;
    }
    
    /**
     * Gets the body for the exception
     * @param \Exception $exception
     * @param integer $count
     * @return string
     */
    private function getBody(\Exception $exception, $count) {
        
        $template = 
<<<EOD
    <div style="width:75%;border:1px solid #cccccc;background-color:#ffffff;margin:20px auto;border-radius:5px 5px;">
        <div style="font-size:1.5em;font-weight:lighter;padding:40px 30px;background-color:#f7f7f7;color:#4b4b4b;border-top:0;border-radius:5px 5px 0 0;border-bottom:1px dashed #ebebeb;">
            <div style="font-size:0.7em;color:#666666;">
EOD;
        $template.= get_class($exception);
        $template.=
<<<EOD
        (code: {$exception->getCode()})
            </div>
            {$count}. {$exception->getMessage()}
        </div>
        <div style="font-size:1em;padding:30px;line-height:1.8em;color:#181818;">
        <div style="font-size:1.2em;margin-bottom:10px;color:#4b4b4b;">
            File: {$exception->getFile()} at line {$exception->getLine()} <br />
            Trace:
        </div>
EOD;
        if (DEBUG_MODE === true) {
            $template.= nl2br($exception->getTraceAsString());
        }
        else {
            $template.= 'The trace is only visible when "debug mode" is enabled.';
        }
        
        $template.= '</div></div>';
        
        return $template;
    }
}