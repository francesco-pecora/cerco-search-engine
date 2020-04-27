<?php

/**
 * class utilized to handle search results
 */
class SearchResultsProvider {
    
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
                                        FROM sites WHERE title LIKE :term
                                        OR url LIKE :term
                                        OR keywords LIKE :term
                                        OR description LIKE :term");
        
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

    public function getResultsHTML($page, $pageSize, $term) {

        $fromLimit = ($page -1) * $pageSize;

        $query = $this->conn->prepare("SELECT * FROM sites
                                        WHERE title LIKE :term
                                        OR url LIKE :term
                                        OR keywords LIKE :term
                                        OR description LIKE :term
                                        ORDER BY clicks DESC
                                        LIMIT :fromLimit, :pageSize");
        
        $term = "%" . $term . "%";
        $query->bindParam(":term", $term);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        $resultsHTML = "<div class='siteResults'>";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];

            $title = $this->trimField($title, 55);
            $description = $this->trimField($description, 230);

            $resultsHTML .= "<div class='resultContainer'>
                                <h3 class='title'>
                                    <a class='result' href='$url'>$title</a>
                                </h3>
                                <span class='url'>$url</span>
                                <span class='description'>$description</span>
                             </div>";
        }
        $resultsHTML .= "</div>";

        return $resultsHTML;
    }

    private function trimField($string, $characterLimit) {
        $dots = strlen($string) > $characterLimit ? " . . ." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }
}

?>