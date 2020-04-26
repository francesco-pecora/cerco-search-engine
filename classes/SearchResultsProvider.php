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
}

?>