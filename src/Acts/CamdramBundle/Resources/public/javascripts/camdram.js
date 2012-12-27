$.fn.scrollTo = function(options) {
    var options = $.extend({
        speed: 500,
        threshold: 0.7,
        overshoot: 10
    }, options);

    var top = $('body').scrollTop();
    var max = $(this).offset().top + options.threshold * $(this).height();
    if (top > max) {
        $('html, body').animate({scrollTop: $(this).offset().top - options.overshoot}, options.speed);
    }
}

$.fn.addMarkers = function(info_boxes) {
    var $self = this;

    var close_all = function() {
        $.each(info_boxes, function(key, val) {
            var infobox = window[val.box_id];
            infobox.close();
        })
    }

    $.each(info_boxes, function(key, box) {
        $img = $('<img/>')
            .addClass('marker_img')
            .attr('src', box.image)
            .click(function() {
                close_all();

                var map = window[box.map_id];
                var marker = window[box.marker_id];
                var infobox = window[box.box_id];
                map.setZoom(17);
                infobox.open(map, marker);

                $(map.getDiv()).scrollTo();
            })

        $('#venue-' + box.slug + ' .marker_column').append($img);
    });
}

$.fn.newsFeedMedia = function() {
    this.each(function() {
        var $media = $(this);
        var $img = $media.parents('.news_link').find('img');
        var $panel = $media.parents('.news_link');
        $media = $media.remove().show();
        $img.addClass('has_media')
            .click(function() {
                $panel.html($media);
            })
    });
}

$(function() {
   $('.news_media').newsFeedMedia();
});