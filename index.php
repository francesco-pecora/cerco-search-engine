<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Search for websites and images">
    <meta name="author" content="Francesco Pecora">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaina+2&display=swap" rel="stylesheet">
    <title>Cerco</title>
</head>
<body>
    <div class="wrapper indexPage">
        <div class="mainSection">
            <div class="logoContainer">
                <img src="./assets/images/cerco-logo.png" alt="">
            </div>
            <div class="searchContainer">
                <form action="search.php" method="GET">
                    <input class="searchBox" type="text" name="term">
                    <input class="searchButton" type="submit" value="Search">
                </form>       
            </div>
        </div>
    </div>
</body>
</html>
