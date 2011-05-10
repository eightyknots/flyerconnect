<?php

/*
 * The display class contains features and methods
 * to drive page HTML
 * 
 * @author Chester Li <me@yectep.com>
 * 
 */
class UX {
    
    // Environment variables
    private $env;
    public  $head;
    
    /*
     * The display class
     * 
     * @param string $main Either "reg" or "lite". Sets the environment type.
     */
    public function __construct($display = 'reg') {
        
        // All we need to do is set the environment variabe
        $this->env = (($display == 'lite') ? $display : 'main');
        
    }
    
    /*
     * Gets a static snippet of HTML
     * 
     * @param string $snippet The snippet name, stored in private/html/: based on environment
     * @return string|false Returns false if the snippet file is not found and don't force nice errors
     */
    public function showHtmlSnippet($snippet, $vars = array(), $nice = true) {
        
        if (file_exists(PTR."html/".$snippet.".".$this->env.".html")) {
            // First we get the page
            $unparsed = file_get_contents(PTR."html/".$snippet.".".$this->env.".html");
            // Replace strings as performed by the actual page code
            $keys = array();
            foreach(array_keys($vars) as $index=>$val) {
                array_push($keys, "__%".$val."%__");
            }
            
            $final = str_replace($keys, $vars, $unparsed);
            $this->pushClear();
            return $final;
        } else {
            return ($nice ? "A required HTML snippet was not found." : false);
        }
        
    }
    
    /*
     * Includes a <script></script> import of a JavaScript URL
     * 
     * @param string $url The JavaScript URL to import
     * @param bool $ext If false, will import via FC2's very own fr.php
     */
    public function pushJavascript($url, $ext = false) {
        
        if ($ext) {
            $this->push('<script type="text/javascript" src="'.$url.'"></script>');
        } else {
            $this->push('<script type="text/javascript" src="/fr.php?js,'.$url.'"></script>');
        }
        
    }
    
    /*
     * Includes a <link /> import of a CSS page
     * 
     * @param string $url The CSS URL to import
     * @param bool $ext If false, will import via FC2's very own fr.php
     */
    public function pushCss($url, $ext = false) {
        
        if ($ext) {
            $this->push('<link href="'.$url.'" type="text/css" rel="stylesheet" />');
        } else {
            $this->push('<link href="/fr.php?css,'.$url.'" type="text/css" rel="stylesheet" />');
        }
        
    }
    
    /*
     * Resets the push/head environment variable for the next pageload
     * 
     */
    public function pushClear() {
        $this->head = '';
    }
    
    
    /*
     * Pushes to the head environment variable
     *
     */
    protected function push($push) {
        $this->head .= $push."\n";
    }
    
    
}

?>
