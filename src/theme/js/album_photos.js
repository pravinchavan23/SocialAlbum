/**
 * Created by pravin chavan
 */

$(document).ready(function() {
    // light gallery configuration
    let lightGallery = $("#gallery").lightGallery({
        thumbnail: true,
        animateThumb: false,
        share: false,
        actualSize: false
    });

    // back button click action
    $('#backBtn').on('click', function(e){
        e.preventDefault();
        if(lightGallery.data('lightGallery'))
            lightGallery.data('lightGallery').destroy(true);
        window.history.back();
    });
});