<?php
    $term = isset($_GET["term"]) ? $_GET["term"] : "";      // $term is the search from the user
    $type = isset($_GET["type"]) ? $_GET["type"] : "sites"; // $type is the tab in which the user is
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaina+2&display=swap" rel="stylesheet">
    <title>Cerco</title>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="headerContent">
                
                <div class="logoContainer">
                    <a href="index.php"><img src="./assets/images/cerco-logo.png" alt=""></a>
                </div>
                
                <div class="searchContainer">
                    <form action="search.php" method="GET">
                        <div class="searchBarContainer">
                            <input class="searchBox" type="text" name="term" value="<?php echo$term ?>"/>
                            <button class="searchButton">
                                <img src="assets/images/magnifying-glass-search.png"/>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="tabsContainer">
                <ul class="tabsList">
                    <!-- Depending on the type, we select a different tab -->
                    <li class="<?php echo$type == 'sites' ? 'active' : '' ?>"><a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a></li>
                    <li class="<?php echo$type == 'images' ? 'active' : '' ?>"><a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
