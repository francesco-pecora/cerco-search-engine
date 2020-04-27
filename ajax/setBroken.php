<?php
include("../config.php");

if (isset($_POST["src"])) {
    $query = $conn->prepare("UPDATE images 
                             SET broken = 1 
                             WHERE imageUrl = :src");
    $query->bindParam(":isrc", $_POST["src"]);
    $query->execute();
}
else {
    echo "[ERROR] No src passed to the page"
}
?>