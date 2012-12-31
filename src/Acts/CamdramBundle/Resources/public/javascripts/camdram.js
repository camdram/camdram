;(function($, window) {
    String.prototype.truncate = function(length) {
        str = jQuery.trim(this).substring(0, length)
            .trim(this);
        if (this.length > length) str += '...';
        return str;
    }

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

    $.fn.formMap = function(map) {
        $(this).each(function() {
            var $self = $(this);
            var $lat = $self.find('input').eq(0);
            var $long = $self.find('input').eq(1);
            $self.children().first().hide();

            var marker = new google.maps.Marker({
                map: map,
                title:"Selected Location",
                draggable: true
            });;
            if ($lat.val() && $long.val()) {
                var pos = new google.maps.LatLng($lat.val(), $long.val());
                marker.setPosition(pos);
                map.setCenter(pos);
                map.setZoom(17);
            }

            var updatePosition = function(animate) {
                return function(e) {
                    if (animate) marker.setMap(null);
                    marker.setPosition(e.latLng);
                    if (animate) {
                        marker.setAnimation(google.maps.Animation.DROP);
                        marker.setMap(map);
                    }
                    $lat.val(e.latLng.lat());
                    $long.val(e.latLng.lng());
                }
            }

            google.maps.event.addListener(map, 'click', updatePosition(true));
            google.maps.event.addListener(marker, 'dragend', updatePosition(false));

        })
    }

    $.fn.camdramAutocomplete = function(options) {
        var options = $.extend({
            route: 'get_entities',
            minLength: 2,
            delay: 100
        }, options);

        var cache = {};

        $(this).each(function() {
            var $self = $(this);
            var url = '';

            $self.autocomplete({
                minLength: options.minLength,
                delay: options.delay,
                source: function(req, resp) {
                    var query = req.term;
                    if (typeof cache[query] != 'undefined') {
                        resp(cache[query]);
                    }
                    else {
                        var url = Routing.generate(options.route, {_format: 'json', q: req.term, limit: 10, autocomplete: true});
                        $.getJSON(url, function(data) {
                            resp(data);
                        })
                    }

                },
                focus: function(event, ui) {
                    $self.val( ui.item.name );
                    return false;
                },
                select: function(event, ui) {
                    $self.val( ui.item.name );
                    document.location = Routing.generate('get_entity', {id: ui.item.id});

                    return false;
                }

            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                ul.attr('id', 'main_search_autocomplete');
                return $( "<li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a><small>"+item.entity_type+"</small>" + item.name + "</a>" )
                    .addClass('autocomplete_item')
                    .appendTo( ul );
            };;
        })
    }

   $('.news_media').newsFeedMedia();
   $('#main_search_box').camdramAutocomplete();

})(jQuery, window);