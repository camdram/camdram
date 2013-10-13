$(function() {
    var get_last_date = function() {
        var date = new Date($('#diary').children().last().attr('data-end'));
        return date.toISOString();
    }
    var get_first_date = function() {
        var date = new Date($('#diary').children().first().attr('data-start'));
        return date.toISOString();
    }
    var get_first_group = function() {
        return $('#diary').children().first().attr('data-group');
    }
    var get_first_year = function() {
        return new Date($('#diary').children().first().attr('data-start')).getFullYear();
    }

    window.add_diary_history = function(year, period, replace) {
        if (history.pushState) {
            var data = {year: year, period: period};
            if (year && period) {
                var url = Routing.generate('acts_camdram_diary_select',data);
            }
            else {
                var url = Routing.generate('acts_camdram_diary');
            }
            if (replace === true) {
                history.replaceState(data, document.title, url);
            }
            else {
                history.pushState(data, document.title, url);
            }
        }
    }

    var update_years = function(cb) {
        var previous_val = $('#periods').val();
        $('#periods').attr('disabled', 'disabled');
        $.get(Routing.generate('get_time-period', {'year': $('#years').val(), '_format' : 'json'}), function(groups) {
            $('#periods').empty();
            $.each(groups, function(key, group) {
                $('#periods').append($('<option/>').val(group.slug).text(group.name));
            })
            $('#periods').val(previous_val);
            $('#periods').removeAttr('disabled');
            if (typeof cb == 'function') {
                cb();
            }
            else {
                load_new_data($('#years').val(), $('#periods').val(), true);
            }
        })
    }

    var update_jump = function() {
        var update_selected_period = function() {
            $('#periods').val(get_first_group());
        }

        if (get_first_year() != $('#years').val()) {
            $('#years').val(get_first_year())
            update_years(function() {
                update_selected_period();
            })
        }
        else {
            update_selected_period();
        }

    }

    var load_new_data = function(year, period, add_to_history) {
        if (!loading) {
            $('#diary').slideUp(300).empty();
            loading = true;
            $.get(Routing.generate('acts_camdram_diary_content',{year: year, period: period}), function(data) {
                $('#diary').html(data)
                $('#diary').slideDown(300, function() {
                    loading = false;
                });
                if (add_to_history) add_diary_history(year, period);
                if ($('#periods').val() == period) update_jump();
                end_limit = false;
                start_limit = false;
            })
        }
    }

    $(window).bind('popstate', function(e) {
        var data = e.originalEvent.state;
        if (data) {
            load_new_data(data.year, data.period, false);
        }
    })

    var loading = false;
    var end_limit = false;
    var start_limit = false;

    var load_next = function() {
        if (!loading && !end_limit) {
            loading = true;
            $.get(Routing.generate('acts_camdram_diary_relative', {direction: 'next', last_date: get_last_date()}), function(data) {
                if ($.trim(data) == '') {
                    end_limit = true;
                }
                else {
                    var html = $(data);
                    $('#diary').append(html);
                    html.hide().slideDown(500, function() {
                        loading = false;
                    });
                }
            })
        }
    };
    $('#periods').change(function() {
        load_new_data($('#years').val(), $('#periods').val(), false);
    })
    $('#years').change(update_years);
    $('#diary_jump_form').submit(function(e) {
        e.preventDefault();
        load_new_data($('#years').val(), $('#periods').val(), false);
    })

    $(window).endlessScroll({
        callback: load_next
    })
    $('#load_previous').click(function(e) {
        e.preventDefault();
        if (!start_limit && !loading) {
            loading = true;
            $.get(Routing.generate('acts_camdram_diary_relative', {direction: 'previous', last_date: get_first_date()}), function(data) {
                if ($.trim(data) == '') {
                    start_limit = true;
                }
                else {
                    var html = $(data);
                    $('#diary').prepend(html)
                    html.hide().slideDown(200, function() {
                        loading = false;
                    });
                    update_jump();
                    add_diary_history(get_first_year(), get_first_group());
                }
            })
        }
    });

    $('#load_today').click(function(e) {
        e.preventDefault();
        if (!loading) {
            load_new_data(null, null, true);
        }
    })

})