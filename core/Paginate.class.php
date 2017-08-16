<?php

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

use Niuware\WebFramework\HttpRequest;
use Illuminate\Database\Eloquent\Collection;

/**
 * Paginates data collections into chunks
 */
final class Paginate {
    
    private $attributes = [];
    
    private $data;
    
    public function __construct() {
        
        $this->itemsPerPage = 15;
        $this->currentPage = 1;
        $this->previousPage = 1;
        $this->nextPage = 1;
        $this->url = '';
        $this->totalPages = 1;
    }
    
    public function __get($name) {
            
        return $this->attributes[$name];
    }
    
    public function __set($name, $value) {
            
        $this->attributes[$name] = $value;
    }
    
    /**
     * Generates the pagination
     * @param type $data
     * @param type $request
     */
    private function init($data, $request) {
        
        $this->totalPages = $data->chunk($this->itemsPerPage)->count();

        if ($this->totalPages > 1) {
            
            $this->currentPage = $request->p ?? 1;
            
            if ($this->currentPage > $this->totalPages) {
                
                $this->currentPage = 1;
            }

            $this->previousPage = $this->currentPage - 1;
            $this->url = preg_replace('/([\?|&]p=\d*)/i', '', $request->headers()['Request-Uri']);
            
            if (strpos($this->url, '/?') === false) {
                
                $this->url.= '/?';
            }
            else {
                
                $this->url.= '&';
            }
            
            $this->url.= 'p=';
            
            if ($this->previousPage <= 0) {
                
                $this->previousPage = 1;
            }
            
            $this->nextPage = $this->currentPage + 1;
            
            if ($this->nextPage > $this->totalPages) {
                
                $this->nextPage = $this->totalPages;
            }
        }

        $this->data = $data->forPage($this->currentPage, $this->itemsPerPage);
    }
    
    /**
     * Paginates the data
     * @param Illuminate\Database\Eloquent\Collection $data
     * @param HttpRequest $request
     */
    public function paginate(Collection $data, HttpRequest $request) {
  
        $this->init($data, $request);
    }
    
    /**
     * Returns the chunk of data for the current HttpRequest
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function data() {
        
        return $this->data; 
    }
    
    /**
     * Returns the string for the 'previous link'
     * @param string $label
     * @param string $itemClass
     * @param string $linkClass
     * @return string
     */
    public function previousLink($label = '&lt;', $itemClass = 'page-item', $linkClass = 'page-link') {
        
        $html.= '<li class="' . $itemClass . '">';
        $html.= '<a class="' . $linkClass . '" href="';
        $html.= $this->url . $this->previousPage . '"';
        $html.= ($this->currentPage == 1) ? ' style="cursor:default;"' : '';
        $html.= '>';
        $html.= $label;
        $html.= '</a>'; 
        $html.= '</li>';
        
        return $html;
    }
    
    /**
     * Returns the string for the 'next link'
     * @param string $label
     * @param string $itemClass
     * @param string $linkClass
     * @return string
     */
    public function nextLink($label = '&gt;', $itemClass = 'page-item', $linkClass = 'page-link') {
        
        $html.= '<li class="' . $itemClass . '">';
        $html.= '<a class="' . $linkClass . '" href="';
        $html.= $this->url . $this->nextPage . '"';
        $html.= ($this->currentPage == $this->totalPages) ? ' style="cursor:default;"' : '';
        $html.= '>';
        $html.= $label;
        $html.= '</a>'; 
        $html.= '</li>';
        
        return $html;
    }
        
    /**
     * Renders the HTML code for the pagination
     * @param string $previousLabel
     * @param string $nextLabel
     * @param string $itemClass
     * @param string $linkClass
     * @param string $activeClass
     */
    public function render($previousLabel = '&lt;', $nextLabel = '&gt;', $itemClass = 'page-item', $linkClass = 'page-link', $activeClass = 'active') {
        
        $html = $this->previousLink($previousLabel, $itemClass, $linkClass, $activeClass);
        
        for ($i = 1; $i <= $this->totalPages; $i++) {
            
            $html.= '<li class="' . $itemClass . ' ';
            $html.= ($i == $this->currentPage) ? $activeClass : '';
            $html.= '">';
            $html.= '<a class="' . $linkClass . '" href="';
            $html.= $this->url . $i;
            $html.= '">' . $i . '</a></li>';
        }
        
        $html.= $this->nextLink($nextLabel, $itemClass, $linkClass, $activeClass);
        
        echo $html;
    }
    
    /**
     * Verifies if it is necessary to render the pagination HTML
     * @return boolean
     */
    public function shouldRender() {
        
        if ($this->totalPages > 1) {
            
            return true;
        }
        
        return false;
    }
}
