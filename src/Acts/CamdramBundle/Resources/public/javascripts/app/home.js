$(function() {

    if ($('#home-diary-container').length > 0) {

        var diary_queue = [];

        var $container = $('#home-diary-container');
        var $diary = $('#home-diary');
        var $nav = $('#home-diary-nav');
        var $overlay = $('.overlay', $container);

        var load_new_diary = function(url, direction) {
            diary_queue.push({url: url, direction: direction});
            if (diary_queue.length == 1) {
                service_queue();
            }
        }
        function service_queue() {
            if (diary_queue.length == 0) return;
            var opts = diary_queue[0];

            $overlay.height($diary.height()).fadeIn(200);

            $.get(opts.url, function(data) {

                var old_height = $container.height();
                $diary.attr('id', 'home-diary-old').css({
                    'position': 'absolute',
                    'height': old_height,
                    'width': '100%'
                });
                $diary = $('<div/>').attr('id', 'home-diary').html(data);
                $container.append($diary);
                $diary.show();
                var new_height = $container.height();
                $diary.hide();
                $container.height(old_height);

                var slide_diaries = function(cb) {
                    var hide_dir = (opts.direction === 'ltr') ? 'right' : 'left';
                    var show_dir = (opts.direction === 'rtl') ? 'right' : 'left';

                    $('#home-diary-old').hide('slide', {direction: hide_dir}, 200, function() {
                        $('#home-diary-old').remove();
                    });
                    $diary.hide().delay(50).show('slide', {direction: show_dir}, 200, function() {
                        if (typeof cb !== 'undefined') cb();
                    });
                }

                $overlay.height($diary.height()).hide();
                if (new_height > old_height) {
                    $container.animate({height: new_height},100, slide_diaries(function() {
                        $container.height('auto');
                        diary_queue.shift();
                        service_queue();
                    }));
                }
                else {
                    slide_diaries(function() {
                        $container.animate({height: new_height},100, function() {
                            $container.height('auto');
                            diary_queue.shift();
                            service_queue();
                        });
                    })
                }
            })
        }

        var get_limits = function() {
            var max_left = $nav.offset().left + 30;
            var min_left = max_left - 50 + $nav.width() - 60;
            $('li', $nav).each(function() {
                min_left -= $(this).outerWidth();
            })
            return [min_left, max_left];
        }
        var focus_current = function(time) {
            var left = - $('li.current', $nav).position().left + 60;

            var limits = get_limits();
            var limits_offset = $('#home-diary-nav').offset().left;
            if (left < limits[0] - limits_offset) left = limits[0] - limits_offset;
            if (left > limits[1] - limits_offset) left = limits[1] - limits_offset;

            $('ul', $nav).animate({'left': left}, time);
        }
        focus_current(0);
        var clickenable = true;
        $('li.week-link', $nav).click(function() {
            if (clickenable) {
                if ($(this).hasClass('current')) return;
                var date = $(this).attr('data-week-start');

                var last_sel = $('li.current', $nav);
                $('li', $nav).removeClass('current');
                $(this).addClass('current');
                var direction =  (last_sel.prevAll('.current').length > 0) ? 'ltr' : 'rtl';

                load_new_diary(Routing.generate('acts_camdram_diary_single_week', {date: date}), direction);

                var left = $(this).offset().left - $nav.offset().left;
                var max_right = $nav.width() - $(this).width() - 60;
                if ($(document).width() < 500) {
                    if (left < 40 || left > max_right) {
                        focus_current(200);
                    }
                }
                else {
                    if (left < 40) move(200);
                    if (left > max_right) move(-200);
                }
            }
        });

        //Slider functions

        var $slider = $('ul', $nav);

        var limits = get_limits();
        $slider.draggable({
            axis: 'x',
            containment: [limits[0], 0, limits[1], 0],
            distance: 10,
            start: function() { clickenable = false;},
            stop: function() { window.setTimeout(function() { clickenable = true; }, 100); }
        })
        $(window).resize(function() {
            var limits = get_limits();
            $slider.draggable( "option", "containment", [limits[0], 0, limits[1], 0]);
        });

        var move = function(offset) {
            var left = parseInt($slider.css('left'));
            left += offset;
            var limits = get_limits();
            var limits_offset = $('#home-diary-nav').offset().left;
            if (left < limits[0] - limits_offset) left = limits[0] - limits_offset;
            if (left > limits[1] - limits_offset) left = limits[1] - limits_offset;

            $slider.animate({left: left}, 200);
        }

        var get_move_size = function() {
            return $('#wrapper > section').width() / 3;
        }

        $('.left-link a', $container).click(function(e) {
            move(get_move_size());
            e.preventDefault();
        });
        $('.right-link a', $container).click(function(e) {
            move(-get_move_size());
            e.preventDefault();
        });
    }
})