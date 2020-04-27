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

});

// send data to php to update database
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