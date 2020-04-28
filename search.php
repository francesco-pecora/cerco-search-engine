<?php
    include("config.php");
    include("classes/SearchResultsProvider.php");
    include("classes/ImageResultsProvider.php");
    include("features/handleMath.php");

    $term = isset($_GET["term"]) ? $_GET["term"] : "";      // $term is the search from the user
    $type = isset($_GET["type"]) ? $_GET["type"] : "sites"; // $type is the tab in which the user is
    $page = isset($_GET["page"]) ? $_GET["page"] : 1;

    $mathResult = evaluateMathEquation($term);              // if evaluates, it's gonna display the result on the page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaina+2&display=swap" rel="stylesheet">
    <script
        src="https://code.jquery.com/jquery-3.5.0.min.js"
        integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ="
        crossorigin="anonymous">
    </script>
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
                            <input type="hidden" name="type" value="<?php echo $type ?>">
                            <input class="searchBox" type="text" name="term" value="<?php echo$term ?>"/>
                            <button class="searchButton">
                                <img src="assets/images/magnifying-glass-search.png"/>
                            </button>
                        </div>
                    </form>
                    <?php
                    if ($mathResult) {
                        echo "
                                <div class='mathResultContainer'>
                                    <p class='mathResult'>Result: $term = $mathResult</p> 
                                </div>
                            ";
                    }
                    ?>
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

        <div class="mainResultsSection">
            <?php

                if ($type == "sites") {
                    $resultsProvider = new SearchResultsProvider($conn);
                    $pageSize = 20;
                }
                else {
                    $resultsProvider = new ImageResultsProvider($conn);
                    $pageSize = 30;
                }

                $numResults =  $resultsProvider->getNumResults($term);

                echo "<p class='resultsCount'>$numResults results found.</p>";
                echo $resultsProvider->getResultsHTML($page, $pageSize, $term);
            ?>
        </div>

        <div class="paginationContainer">
            <div class="pageButtons">

                <div class="pageNumberContainer">
                    <img src="assets/images/start.png" alt="start">
                </div>

                <?php
                    $pagesToShow = 10;                            // num of pages you can navigate to at the bottom
                    $numPages = ceil($numResults / $pageSize);    // how many buttons we would need to navigate to all pages
                    $pagesLeft = min($pagesToShow, $numPages);

                    $currentPage = $page - floor($pagesToShow / 2);

                    if ($currentPage < 1) $currentPage = 1;
                    if ($currentPage + $pagesLeft > $numPages + 1) $currentPage = $numPages + 1 - $pagesLeft;

                    while($pagesLeft != 0 && $currentPage <= $numPages) {

                        if ($currentPage == $page) {              // how the selected page is displayed
                            echo "<div class='pageNumberContainer'>
                                    <img src='assets/images/e.png'>
                                    <span>$currentPage</span>
                                </div>";
                        } else {                                  // how the other pages are displayed
                            echo "<div class='pageNumberContainer'>
                                    <a href='search.php?term=$term&type=$type&page=$currentPage'>
                                        <img src='assets/images/e.png'>
                                        <span class='pageNumber'>$currentPage</span>
                                    </a>
                                </div>";
                        }

                        $currentPage++;
                        $pagesLeft--;
                    }
                ?>

                <div class="pageNumberContainer">
                    <img src="assets/images/end.png" alt="start">
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="assets/javascript/script.js"></script>
</body>
</html>
