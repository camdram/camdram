;(function($, window) {
    String.prototype.truncate = function(length) {
        str = jQuery.trim(this).substring(0, length)
            .trim(this);
        if (this.length > length) str += '...';
        return str;
    }

    var short_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

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

    $.fn.entitySearch = function(options) {
       var options = $.extend({
           placeholder: 'start typing to search'
       }, options);
        var $self = $(this);

        var tokenize = function(str) {
            return $.trim(str).toLowerCase().replace(/[\(\)]/g, '').split(/[\s\-_]+/);
        }

        var filter = function(items) {
            for (var i in items) {
                items[i].tokens = tokenize(items[i].name).concat(tokenize(items[i].short_name));
            }
            return items;
        }

        $self.typeahead({
           name: options.route,
           valueKey: 'name',
           prefetch: {url: Routing.generate(options.route, {_format: 'json'}), filter: filter},
           remote: {url: Routing.generate(options.route, {q: 'QUERY'}), wildcard: 'QUERY', filter: filter}
       }).on('typeahead:selected', function (object, datum) {
            $self.parent().siblings('input[type=hidden]').val(datum.id);
       });

       $(this).change(function() {
           $self.parent().siblings('input[type=hidden]').val('');
       }).attr('placeholder', options.placeholder);

    }

    $.fn.entityCollection = function(options) {
        var options = $.extend({
            max_items: 100,
            min_items: 1,
            initialiseRow: function() {},
            add_link_selector: '.add_link'
        }, options);

        $(this).each(function() {
            var $self = $(this);
            var index = $(this).children().length;
            var $add_link = $(options.add_link_selector, $self.parent());

            var update_links = function() {
                if ($('.remove_link', $self).length > options.min_items) {
                    $('.remove_link', $self).show();
                }
                else {
                    $('.remove_link', $self).hide();
                }

                if ($('.remove_link', $self).length < options.max_items) {
                    $add_link.show();
                }
                else {
                    $add_link.hide();
                }
            }

            $add_link.click(function(e) {
                e.preventDefault();
                var html = $self.attr('data-prototype').replace(/__name__/g, index);
                $row = $(html);
                $self.append($row);
                update_links();
                options.initialiseRow.apply($row);
                index++;
            })

            $('.remove_link', $self).live('click', function(e) {
                e.preventDefault();
                $(this).parentsUntil($self).remove();
                update_links();
            })

            update_links();
            $self.children().each(options.initialiseRow);
        })
    }

    $.fn.endlessScroll = function(options) {
        var options = $.extend({
            distance: 500,
            interval: 200,
            callback: function() {}
        }, options);

        var $window = $(window),
            $document = $(document),
            $self = $(this);

        var checkScrollPosition = function() {
            var top = $document.height() - $window.height() - options.distance;
            if ($window.scrollTop() >= top) {
                options.callback.apply($self);
            }
        };

        setInterval(checkScrollPosition, options.interval);
    }

    $(function() {
        $(document).foundation();
        $('.news_media').newsFeedMedia();
        $('a.fancybox').fancybox();

        $('.dropdown-link').each(function() {
            var $link = $(this);
            var $dropdown = $('.topbar-dropdown', $link);
            $dropdown.hide();
            var hideEnabled = true;

            $link.mouseenter(function() {
                $dropdown.css({
                    'position': 'absolute',
                    'top': $link.offset().top + $link.height(),
                    'left': $link.offset().left + $link.outerWidth() - $dropdown.outerWidth()
                }).show();
                $dropdown.show();
            }).mouseleave(function() {
                    if (hideEnabled) $dropdown.hide();
                });
            $('input', $dropdown).bind('invalid',function() {
                hideEnabled = false;
                window.setTimeout(function() {
                    hideEnabled = true;
                },200);
            })
        })
    });

})(jQuery, window);