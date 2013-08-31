$(document).ready(function() {

    $("#lang-menu-container").hide();

    $('html').click(function() {
        $("#lang-menu-container").hide();
    });

    $("#foot-languages").click(function(event) {
        $("#lang-menu-container").show();
        $("#lang-menu-container").position({
            my: "center bottom",
            at: "center top+5",
            of: $( "#foot-languages" ),
            collision: "none"
        });
       event.stopPropagation();
       event.preventDefault();
    })

})