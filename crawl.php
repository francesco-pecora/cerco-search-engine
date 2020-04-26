<?php
include("config.php");
include("classes/DomDocumentParser.php");

// GLOBAL VARIABLES
$alreadyCrawled = [];
$stillCrawling = [];
$alreadyFoundImages = [];


/**
 * checks if the url is already in the database.
 * 
 * @param $url -> link to the website to be checked
 * @return -> true if url already in database; false if not
 */
function existsInDatabase($url) {
    global $conn;

    $query = $conn->prepare("SELECT * FROM sites WHERE url = :url");

    // avoid sql injections
    $query->bindParam(":url", $url);
    $query->execute();

    return $query->rowCount() != 0; // returns true if url exists in db
}


/**
 * inserts the data into the database.
 * 
 * @param $url -> link to the website
 * @param $title -> title of the website
 * @param $description -> description of the website
 * @param $keywords -> keywords for the website
 * @return -> true if the query succesfully run; false if not
 */
function insertInDatabase($url, $title, $description, $keywords) {
    global $conn;

    $query = $conn->prepare("INSERT INTO sites (url, title, description, keywords) 
                             VALUES (:url, :title, :description, :keywords);");

    // avoid sql injections
    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();   // returns true if worked
}


/**
 * inserts the image into the database.
 * 
 * @param $url -> link to the website containing the image
 * @param $src -> link to the image
 * @param $alt -> message shown when image fails to load
 * @param $title -> message displayed hovering on image
 * @return -> true if the query succesfully run; false if not
 */
function insertImageInDatabase($url, $src, $alt, $title) {
    global $conn;

    $query = $conn->prepare("INSERT INTO images (siteUrl, imageUrl, alt, title) 
                             VALUES (:siteUrl, :imageUrl, :alt, :title);");

    // avoid sql injections
    $query->bindParam(":siteUrl", $url);
    $query->bindParam(":imageUrl", $src);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);

    return $query->execute();   // returns true if worked
}


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
    else if (substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    // relative url in the form "../path/to/file"
    else if (substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    // relative url in the form "path/to/file"
    else if (substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    // any other case which is still not an absolute url returns -1 to skip it when its called
    else return -1;

    return $src;
}


/**
 * gets the title tag of a webpage and returns its content (if exists)
 * 
 * @param $parser -> DomDocumentParser object
 * @return string title of web page
 */
function getUrlTitle($parser) {
    $titleArray = $parser->getTitleTags();
    
    // return if there is no title tag in the page
    if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) return;
    
    $title = $titleArray->item(0)->nodeValue;   // $title contains the title of the webpage
    $title = str_replace("\n", "", $title);     // remove \n from $title

    // don't wanna insert empty strings in database
    if ($title == "") return;

    return $title;
}


/**
 * gets the meta tags and returns desription and keywords of the website
 * 
 * @param $parser -> DomDocumentParser object
 * @return -> []
 */
function getUrlMeta($parser) {
    $metaArray = $parser->getMetatags();

    $description = "";
    $keywords = "";

    foreach ($metaArray as $meta) {
        if ($meta->getAttribute("name") == "description" || $meta->getAttribute("name") == "Description") {
            $description = $meta->getAttribute("content");
            $description = str_replace("\n", "", $description); // cleaning output
        }
        if ($meta->getAttribute("name") == "keywords") {
            $keywords = $metaArray->getAttribute("content");
            $keywords = str_replace("\n", "", $keywords);       // cleaning output
        }
    }

    return [$description, $keywords];
}


/**
 * gets the info about the images in the web page and stores the images in database
 * 
 * @param $parser -> DomDocumentParser object
 * @param $url -> link to the website
 */
function getUrlImages($parser, $url) {
    global $alreadyFoundImages;

    $imageArray = $parser->getImgTags();
    foreach ($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if (!$title && !$alt) {
            continue;
        }

        $src = convertRealtiveToAbsoluteUrl($src, $url);

        // avoiding to display images multiple times
        if (!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;

            insertImageInDatabase($url, $src, $alt, $title);
        }
    }
}


/**
 * gets the information about the website that needs to be displayed
 * 
 * @param $url -> link to the website
 */
function getUrlDetails($url) {
    $parser = new DomDocumentParser($url);
    
    // retrieve the title from the web page
    $title = getUrlTitle($parser);

    // retrieve the descrition and keywords from the web page
    $descAndKeywords = getUrlMeta($parser);
    $description = $descAndKeywords[0];
    $keywords = $descAndKeywords[1];

    // make sure we are not double-inserting websites in database
    if (existsInDatabase($url)) {
        echo "$url already exists <br><br>";
    }
    else if (insertInDatabase($url, $title, $description, $keywords)) {
        echo "[SUCCESS] $url <br><br>";
    }
    else {
        echo "[ERROR] Failed to insert $url <br><br>";
    }

    getUrlImages($parser, $url);
}


/**
 * followLinks parses a starting webpage and gets all the valid links present in that page,
 * then it performs the same operation recursively for every entry of the first list of links
 * 
 * @param $url -> starting url to parse
 */
function crawlUrl($url) {

    global $alreadyCrawled;
    global $stillCrawling;

    $parser = new DomDocumentParser($url);
    $linkList = $parser->getLinkTags();

    foreach ($linkList as $link) {
        $href = $link->getAttribute("href");

        // ingore when the <a> href value contains a # instead of a valid link
        if (strpos($href, "#") !== false) {
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

        if (!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href;   // append to the already crawled so we don't visit next time
            $stillCrawling[] = $href;

            getUrlDetails($href);
        }
        //else return;    // just stop FOR NOW when duplicate to avoid infinite loop
    }

    // remove already crawled url
    array_shift($stillCrawling);

    //recursively perform the same operation on every site
    foreach ($stillCrawling as $site) {
        crawlUrl($site);
    }
}

$startUrl = "https://www.redbull.com";
crawlUrl($startUrl);

?>