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
            delay: 100,
            select: function(item) {},
            display: function(li, item) {},
            open: function(e, ui) {},
            close: function(e, ui) {},
            appendTo: null
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
                    options.select.apply($self, [ui.item]);
                    return false;
                },
                appendTo: options.appendTo,
                open: options.open,
                close: options.close

            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                ul.attr('id', 'main_search_autocomplete');
                var li = $( "<li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a>" + item.name + "</a>" )
                    .addClass('autocomplete_item')
                options.display(li, item);
                return li.appendTo( ul );
            };;
        })
    }

    $.fn.entitySearch = function(options) {
       var options = $.extend({
           select: function(item) {
               $(this).siblings('input[type=hidden]').val(item.id);
           },
           placeholder: 'start typing to search'
       }, options)
        console.log(options);
       $(this).camdramAutocomplete(options)

       $(this).each(function() {
           $(this).change(function() {
               $(this).siblings('input[type=hidden]').val('');
           })
       })
    }

    $.fn.entityCollection = function(options) {

        var options = $.extend({
            select: function(item) {
                var container = $(this).parents('.entity_collection_container');
                $(this).val('');

                if ($('input[value=' + item.id + ']', container).length > 0) return;

                var $input = $('.autocomplete_input', container);
                var new_li = $('.template', container).clone();
                new_li.find('.collection_name').html(item.name);
                new_li.find('.collection_id').val(item.id);
                new_li.removeClass('template');
                $('ul', container).append(new_li);
            },
            placeholder: 'start typing to search'
        }, options);

        $('.autocomplete_input', this).camdramAutocomplete(options);

        $(this).each(function() {
            $('ul', this).find('.entity_collection_remove').live('click', function() {
                $(this).parent().remove();
                return false;
            })
        })
    }
    $(function() {
        $('.news_media').newsFeedMedia();
        $('#main_search_box').camdramAutocomplete({
            select: function(item) { document.location = Routing.generate('get_entity', {id: item.id}); },
            display: function(li, item) {
                var type = $('<small>'+item.entity_type+'</small>').addClass('entity_'+item.entity_type);
                li.children().prepend(type);
            },
            appendTo: '#search_form'
        });
        $('a.fancybox').fancybox();
    });

})(jQuery, window);