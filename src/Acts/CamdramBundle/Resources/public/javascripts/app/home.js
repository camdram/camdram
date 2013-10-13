$(function() {

    if ($('#home-diary-container').length > 0) {

        var diary_queue = [];

        var load_new_diary = function(url, direction) {
            diary_queue.push({url: url, direction: direction});
            if (diary_queue.length == 1) {
                service_queue();
            }
        }
        function service_queue() {
            if (diary_queue.length == 0) return;
            var opts = diary_queue[0];

            $.get(opts.url, function(data) {

                var old_height = $('#home-diary-container').height();
                $('#home-diary').attr('id', 'home-diary-old').css({
                    'position': 'absolute',
                    'height': old_height,
                    'width': '100%'
                });
                var html = $('<div/>').attr('id', 'home-diary').html(data);
                $('#home-diary-container').append(html);
                $('#home-diary').show();
                var new_height = $('#home-diary-container').height();
                $('#home-diary').hide();
                $('#home-diary-container').height(old_height);

                var slide_diaries = function(cb) {
                    var hide_dir = (opts.direction === 'ltr') ? 'right' : 'left';
                    var show_dir = (opts.direction === 'rtl') ? 'right' : 'left';

                    $('#home-diary-old').hide('slide', {direction: hide_dir}, 200, function() {
                        $('#home-diary-old').remove();
                    });
                    $('#home-diary').hide().delay(50).show('slide', {direction: show_dir}, 200, function() {
                        if (typeof cb !== 'undefined') cb();
                    });
                }

                if (new_height > old_height) {
                    $('#home-diary-container').animate({height: new_height},100, slide_diaries(function() {
                        $('#home-diary-container').height('auto');
                        diary_queue.shift();
                        service_queue();
                    }));
                }
                else {
                    slide_diaries(function() {
                        $('#home-diary-container').animate({height: new_height},100, function() {
                            $('#home-diary-container').height('auto');
                            diary_queue.shift();
                            service_queue();
                        });
                    })
                }
            })
        }

        var get_limits = function() {
            var max_left = $('#home-diary-nav').offset().left + 30;
            var min_left = max_left - 50 + $('#home-diary-nav').width() - 60;
            $('#home-diary-nav li').each(function() {
                min_left -= $(this).outerWidth();
            })
            return [min_left, max_left];
        }
        var focus_current = function(time) {
            var left = - $('#home-diary-nav li.current').position().left + 60;

            var limits = get_limits();
            var limits_offset = $('#home-diary-nav').offset().left;
            if (left < limits[0] - limits_offset) left = limits[0] - limits_offset;
            if (left > limits[1] - limits_offset) left = limits[1] - limits_offset;

            $('#home-diary-nav ul').animate({'left': left}, time);
        }
        focus_current(0);
        var clickenable = true;
        $('#home-diary-nav li.week-link').click(function() {
            if (clickenable) {
                if ($(this).hasClass('current')) return;
                var id = $(this).attr('data-period');

                var last_sel = $('#home-diary-nav li.current');
                $('#home-diary-nav li').removeClass('current');
                $(this).addClass('current');
                var direction =  (last_sel.prevAll('.current').length > 0) ? 'ltr' : 'rtl';

                load_new_diary(Routing.generate('acts_camdram_diary_period', {id: id}), direction);

                var left = $(this).offset().left - $('#home-diary-nav').offset().left;
                var max_right = $('#home-diary-nav').width() - $(this).width() - 60;
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

        var $slider = $('#home-diary-nav ul');

        var limits = get_limits();
        $('#home-diary-nav ul').draggable({
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

        $('#home-diary-container .left-link a').click(function(e) {
            move(get_move_size());
            e.preventDefault();
        });
        $('#home-diary-container .right-link a').click(function(e) {
            move(-get_move_size());
            e.preventDefault();
        });
    }
})