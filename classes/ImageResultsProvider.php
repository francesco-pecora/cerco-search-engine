<?php

/**
 * class utilized to handle images results
 */
class ImageResultsProvider {
    
    private $conn;
    
    public function __construct($conn){
        $this->conn = $conn;
    }

    /**
     * getNumResults fetches the number of results received from the database based on the search term from the user
     * 
     * @param $term -> str
     * @return -> int
     */
    public function getNumResults($term) {
        $query = $this->conn->prepare("SELECT COUNT(*) as total
                                        FROM images 
                                        WHERE (title LIKE :term
                                        OR alt LIKE :term)
                                        AND broken = 0");
        
        /*
        Adding % symbols otherwise the LIKE function in the query would only count
        the results in which the term from the user is exactly the same as the one
        from the user. We wanna allow for other words around it (e.g. the term is
        in a longer sentence).
        */
        $term = "%" . $term . "%";
        $query->bindParam(":term", $term);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];
    }

    /**
     * getResultsHTML puts all the information into an html string to be displayed in the page
     * 
     * @param $page -> current page the user is visiting
     * @param $pageSize -> number of results we want to show each page
     * @param $term -> the search input from the user
     * @return -> str containing the hrml
     */
    public function getResultsHTML($page, $pageSize, $term) {

        $fromLimit = ($page -1) * $pageSize;

        $query = $this->conn->prepare("SELECT * FROM images    
                                        WHERE (title LIKE :term
                                        OR alt LIKE :term)
                                        AND broken = 0
                                        ORDER BY clicks DESC
                                        LIMIT :fromLimit, :pageSize");
        
        $term = "%" . $term . "%";
        $query->bindParam(":term", $term);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        $resultsHTML = "<div class='imageResults'>";
        $count = 0;     // to keep track of the image with an ID to load it in resultsHTML
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $id = $row["id"];
            $imageUrl = $row["imageUrl"];
            $siteUrl = $row["siteUrl"];
            $title = $row["title"];
            $alt = $row["alt"];

            if ($title) $displaytext = $title;
            else if ($alt) $displaytext = $alt;
            else $displaytext = $imageUrl;

            $resultsHTML .= "<div class='gridItem image$count'>
                                <a href='$imageUrl'>
                                    <script>
                                        $(document).ready(() => {
                                            loadImage(\"$imageUrl\", \"image$count\");
                                        });
                                    </script>
                                    <span class='details'>$displaytext</span>
                                </a>
                             </div>";
        }
        $resultsHTML .= "</div>";

        return $resultsHTML;
    }
}

?>