<?php
include("../config.php");

if (isset($_POST["imageURL"])) {
    $query = $conn->prepare("UPDATE images 
                             SET clicks = clicks + 1 
                             WHERE imageUrl = :imageUrl");
    $query->bindParam(":imageUrl", $_POST["imageURL"]);
    $query->execute();
}
else {
    echo "[ERROR] No image url passed to page";
}
?>