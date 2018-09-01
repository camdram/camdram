import Routing from 'router';
import $ from 'jquery';

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

        $('li.week-link', $nav).click(function() {
            if ($(this).hasClass('current')) return;
            var date = $(this).attr('data-week-start');

            var last_sel = $('li.current', $nav);
            $('li', $nav).removeClass('current');
            $(this).addClass('current');
            var direction =  (last_sel.prevAll('.current').length > 0) ? 'ltr' : 'rtl';

            load_new_diary(Routing.generate('acts_camdram_diary_single_week', {date: date}), direction);
        });

        $('.diary-expand', $nav).click(function() {
            document.querySelector('#home-diary-nav').classList.add('expanded');
        });
    }
})
