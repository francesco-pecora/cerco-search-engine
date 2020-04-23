<?php

include("classes/DomDocumentParser.php");

/**
 * convertRealtiveToAbsoluteUrl converts the relative link (not 
 * containing the full http form) to absolute link (complete form).
 * 
 * @param $src -> the link
 * @param $url -> the website where the link was found
 * @return -> converted link
 */
function convertRealtiveToAbsoluteUrl($src, $url){

    $scheme = parse_url($url)["scheme"];     // the scheme is either http or https
    $host = parse_url($url)["host"];         // the host is the www.whateverwebsite.com part of an url

    // relative url in the form "//path/to/file"
    if (substr($src, 0, 2) == "//") {
        $src = $scheme . ":" . $src;
    }
    // relative url in the form "/path/to/file"
    else if (substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    }
    // relative url in the form "./path/to/file"
    else if(substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    // relative url in the form "../path/to/file"
    else if(substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    // relative url in the form "path/to/file"
    else if (substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    // any other case which is still not an absolute url returns -1 to skip it when its called
    else {
        return -1;
    }

    return $src;
}

/**
 * followLinks parses a starting webpage and gets all the valid links present in that page
 * 
 * @param $url -> starting url to parse
 */
function followLinks($url){
    $parser = new DomDocumentParser($url);
    $linkList = $parser->getLinks();

    foreach($linkList as $link){
        $href = $link->getAttribute("href");

        // ingore when the <a> href value contains a # instead of a valid link
        if(strpos($href, "#") !== false) {
            continue;
        }
        // ignore when the <a> href value starts with "javascript:"
        else if (substr($href, 0, 11) == "javascript:") {
            continue;
        }

        $href = convertRealtiveToAbsoluteUrl($href, $url);

        // skip links which are not any type of relative url and does not get converted
        if ($href == -1) {
            continue;
        }

        echo $href . "<br>";
    }
}

$startUrl = "http://www.apple.com";
followLinks($startUrl);

?>