<?php

class DomDocumentParser {

    private $doc;

    public function __construct($url){
        // option is needed to request web pages. User-Agent is how the website knows who visited it
        $options = [
            'http'=>['method'=>"GET", 'header' => "User-Agent: cercoBot/0.1\n"]
        ];
        $context = stream_context_create($options);
        
        $this->doc = new DomDocument();   // to perform actions on web pages
        @$this->doc->loadHTML(file_get_contents($url, false, $context));
    }

    /**
     * gets all the <a> tags in the html document
     * 
     * @return -> []
     */
    public function getLinks() {
        return $this->doc->getElementsByTagName("a");
    }
}

?>