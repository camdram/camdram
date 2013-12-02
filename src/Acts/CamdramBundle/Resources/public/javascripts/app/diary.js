var Camdram = Camdram || {};

Camdram.diary_date_format = 'YYYY-MM-DD';

Camdram.diary_server = {};
Camdram.diary_server.queue = [];
Camdram.diary_server.get_content = function(url, cb) {
    console.log(url);
    Camdram.diary_server.queue.push({
        url: url,
        callback: cb
    });
    if (Camdram.diary_server.queue.length === 1) {
        Camdram.diary_server.service_queue();
    }
}
Camdram.diary_server.service_queue = function() {
    if (Camdram.diary_server.queue.length > 0) {
        var opts = Camdram.diary_server.queue[0];
        $.get(opts.url, function(data) {
            opts.callback($(data));

            Camdram.diary_server.queue.shift();
            Camdram.diary_server.service_queue();
        })
    }
}
Camdram.diary_server.get_content_for_today = function(cb) {
    Camdram.diary_server.get_content(Routing.generate('acts_camdram_diary', {fragment: true}), cb);
}
Camdram.diary_server.get_content_by_dates = function(start, end, cb) {
    Camdram.diary_server.get_content(Routing.generate('acts_camdram_diary_date', {
        start: start.format('YYYY-MM-DD'),
        end: end.format(Camdram.diary_date_format),
        fragment: true
    }), cb);
}
Camdram.diary_server.get_content_by_period = function(year, period, end, cb) {
    Camdram.diary_server.get_content(Routing.generate('acts_camdram_diary_period', {
        year: year,
        period: period,
        end: end ? end.format(Camdram.diary_date_format) : null,
        fragment: true
    }), cb);
}

Camdram.diary = function() {
    this.is_loading = false;
    this.$diary = $('#diary');
    this.on_state_change = function(state, replace) {};
    this.state = {}
};
Camdram.diary.prototype.get_first_date = function() {
    return moment(this.$diary.children('.diary-week').first().attr('data-start') + ' 00:00');
};
Camdram.diary.prototype.get_last_date = function() {
    return moment(this.$diary.children('.diary-week').last().attr('data-end') + ' 00:00');
}
Camdram.diary.prototype.insert_content = function(html, cb) {
    var self = this;

    var new_start_at = new Date(html.filter('.diary-week').first().attr('data-start') + ' 00:00');
    var start_at = new Date(self.$diary.children().last().attr('data-start') + ' 00:00');

    if (new_start_at >= start_at) {
        self.$diary.append(html);
    }
    else {
        self.$diary.prepend(html);
    }

    //Ensure each period label appears exactly once
    self.$diary.children('.diary-period-label').each(function() {
        var start_at = $(this).attr('data-start');
        self.$diary.children('.diary-period-label[data-start='+start_at+']').slice(1).remove();
    });

    html.filter('.diary-week').hide().slideDown(300, cb);
}
Camdram.diary.prototype.change_state = function(state, replace) {
    this.state = state;
    this.on_state_change(state, replace);
}
Camdram.diary.prototype.load_from_state = function(state) {
    var self = this;
    if (state.year && state.period) {
        self.is_loading = true;
        Camdram.diary_server.get_content_by_period(state.year, state.period, state.end, function(data) {
            self.replace_content(data);
            self.is_loading = false;
        })
    }
    else if (state.start && state.end) {
        self.is_loading = true;
        Camdram.diary_server.get_content_by_dates(state.start, state.end, function(data) {
            self.replace_content(data);
            self.is_loading = false;
        })
    }
    else {
        self.is_loading = true;
        Camdram.diary_server.get_content_for_today(function(data) {
            self.replace_content(data);
            self.is_loading = false;
        })
    }
}
Camdram.diary.prototype.goto_today = function() {
    var self = this;
    self.is_loading = true;
    self.$diary.empty();
    Camdram.diary_server.get_content_for_today(function(data) {
        self.insert_content(data, function() {
            self.is_loading = false;
        });
    })
    this.change_state({}, true);
}
Camdram.diary.prototype.goto_period = function(year, period) {
    var self = this;
    self.is_loading = true;
    self.$diary.empty();
    Camdram.diary_server.get_content_by_period(year, period, null, function(data) {
        self.insert_content(data);
        self.is_loading = false;
    })
    this.change_state({year: year, period: period}, true);
}
Camdram.diary.prototype.load_previous_weeks = function(num_weeks) {
    var self = this;
    if (self.is_loading) return;
    self.is_loading = true;
    var start = this.get_first_date().subtract('days', 7*num_weeks);
    var end = this.get_first_date();
    Camdram.diary_server.get_content_by_dates(start, end, function(data) {
        self.insert_content(data, function() {
            self.is_loading = false;
        });
    })
    this.change_state({
        start: start.format(Camdram.diary_date_format),
        end: this.get_last_date().format(Camdram.diary_date_format)
    }, false);
}
Camdram.diary.prototype.load_next_weeks = function(num_weeks) {
    var self = this;
    if (self.is_loading) return;
    self.is_loading = true;
    var start = this.get_last_date();
    var end = this.get_last_date().add('days', 7*num_weeks);
    Camdram.diary_server.get_content_by_dates(start, end, function(data) {
        self.is_loading = false;
        self.insert_content(data);
    })
    var state = this.state;
    state.end = end.format(Camdram.diary_date_format);
    this.change_state(state);
}

Camdram.diary_selector = function(diary) {
    this.diary = diary;
    this.$years_select = $('#years');
    this.$periods_select = $('#periods');
};
Camdram.diary_selector.prototype.year = function(val) {
    if (!arguments.length) return this.$years_select.val();
    else this.$years_select.val(val);
}
Camdram.diary_selector.prototype.period = function(val) {
    if (!arguments.length) return this.$periods_select.val();
    else this.$periods_select.val(val);
}
Camdram.diary_selector.prototype.update_years = function() {
    var self = this;
    var previous_period = this.period();
    $('#periods').attr('disabled', 'disabled');
    $.get(Routing.generate('get_time-period', {'year': self.year(), '_format' : 'json'}), function(periods) {
        self.$periods_select.empty();
        $.each(periods, function(key, period) {
            self.$periods_select.append($('<option/>').val(period.slug).text(period.name));
        })
        self.period(previous_period);
        self.$periods_select.removeAttr('disabled');
        self.update_diary();
    })
}
Camdram.diary_selector.prototype.update_diary = function() {
    this.diary.goto_period(this.year(), this.period(), true);
}
Camdram.diary_selector.prototype.attach_events = function() {
    var self = this;
    $('#periods').change(function() { self.update_diary.apply(self) });
    $('#years').change(function() { self.update_years.apply(self); });
    $('#diary_jump_form').submit(function(e) {
        e.preventDefault();
        self.update_years();
    })
}

$(function() {
    var diary = new Camdram.diary();
    var selector = new Camdram.diary_selector(diary);

    diary.on_state_change = function(data, replace) {
        if (history.pushState) {
            if (data.year && data.period) {
                var url = Routing.generate('acts_camdram_diary_period',data);
            }
            else if (data.start) {
                var url = Routing.generate('acts_camdram_diary_date', data);
            }
            else {
                var url = Routing.generate('acts_camdram_diary', data);
            }
            if (replace === true) {
                history.replaceState(data, document.title, url);
            }
            else {
                history.pushState(data, document.title, url);
            }
        }
    }

    $(window).bind('popstate', function(e) {
        var data = e.originalEvent.state;
        if (data) diary.load_from_state(data);
    })

    $(window).endlessScroll({
        callback: function() {
            if (!$('#diary').is(':empty')) {
                diary.load_next_weeks(6);
            }
        }
    })

    selector.attach_events();
    $('#load_previous').click(function(e) {
        e.preventDefault();
        diary.load_previous_weeks(1);
    });

    $('#load_today').click(function(e) {
        e.preventDefault();
        diary.goto_today();
    })


})