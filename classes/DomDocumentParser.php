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
     * getLinkTag gets all the <a> tags in the html document
     * 
     * @return -> []
     */
    public function getLinkTags() {
        return $this->doc->getElementsByTagName("a");
    }

    /**
     * getTitleTag gets the <title> tags in the html document
     * 
     * @return -> []
     */
    public function getTitleTags() {
        return $this->doc->getElementsByTagName("title");
    }

    /**
     * getMetaTag gets the <meta> tags in the html document
     * 
     * @return -> []
     */
    public function getMetaTags() {
        return $this->doc->getElementsByTagName("meta");
    }

    /**
     * getImgTag gets the <img> tags in the html document
     * 
     * @return -> []
     */
    public function getImgTags() {
        return $this->doc->getElementsByTagName("img");
    }
}

?>