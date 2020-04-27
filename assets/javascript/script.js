// GLOBAL
var timer;

$(document).ready(() => {
    
    $(".result").on("click", (event) => {
        var id = $(event.currentTarget).attr("data-linkID");
        var url = $(event.currentTarget).attr("href");
        
        if (!id) {
            alert("data-linkID attribute not found");
        }

        increaseLinkClicks(id, url);

        return false;   // prevents the page to load before we update the clicks
    });

    var grid = $(".imageResults");
    
    // the images are not visible until they are loaded
    grid.on("layoutComplete", () => {
        $(".gridItem img").css("visibility", "visible");
    });
    
    // masonry layout https://masonry.desandro.com/
    grid.masonry({
        itemSelector: ".gridItem",
        columnWidth: 200,
        gutter: 10,             // small border between pictures
        isInitLayout: false     // avoid bugs while updating images dynamically
    });
});

/**
 * loadImage loads an image from the database and handles the case with no image url (error)
 * 
 * @param {str} src -> image url 
 */
const loadImage = (src, className) => {
    var image = $("<img>");

    image.on("load", () => {
        $("." + className + " a").append(image);
        
        // update masonry every 1/2 second to avoid making the call for every image
        clearTimeout(timer);
        timer = setTimeout(() => {
            $(".imageResults").masonry();
        }, 500);
    });

    image.on("error", () => {
        $("." + className).remove();
        $.post("ajax/setBroken.php", {src: src});
    });

    image.attr("src", src);
};

/**
 * increaseLinkClicks sends the data to php to increase the clicks entry by one (to then rank)
 * 
 * @param {str} linkID -> database id of the entry
 * @param {str} url -> url to the website 
 */
const increaseLinkClicks = (linkID, url) => {
    $.post("ajax/updateLinkCount.php", {linkID: linkID})
    .done((res) => {
        if (res !== "") {
            alert(res);
            return;
        }
        window.location.href = url; // redirect to website clicked
    });
};