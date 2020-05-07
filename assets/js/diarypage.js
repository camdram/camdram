import './base.js';
import Routing from 'router';

const q = document.querySelector.bind(document);
const qq = document.querySelectorAll.bind(document);

Camdram.diary_server = {};
Camdram.diary_server.get_content = function(url, cb) {
    $.get(url, cb);
};
Camdram.diary_server.get_content_for_today = function(cb) {
    Camdram.diary_server.get_content(Routing.generate('acts_camdram_diary', {fragment: true}), cb);
};
Camdram.diary_server.get_content_by_dates = function(start, end, cb) {
    Camdram.diary_server.get_content(Routing.generate('acts_camdram_diary_date', {
        start: Camdram.formatISODate(start),
        end: Camdram.formatISODate(end),
        fragment: true
    }), cb);
};
Camdram.diary_server.get_content_by_period = function(year, period, end, cb) {
    Camdram.diary_server.get_content(Routing.generate('acts_camdram_diary_period', {
        year: year,
        period: period,
        end: end ? Camdram.formatISODate(end) : null,
        fragment: true
    }), cb);
};

Camdram.diary = class {
    constructor() {
        this.is_loading = false;
        this.diary = q('#diary');
        this.state = {};
    }
    get first_date() {
        const el = q('#diary > .diary-week');
        return el ? Camdram.parseISODate(el.dataset.start) : null;
    }
    get last_date() {
        const weeks = qq('#diary > .diary-week');
        return weeks.length ? Camdram.parseISODate(weeks[weeks.length-1].dataset.end) : null;
    }
    insert_content(html, cb) {
        const div = document.createElement('div');
        const weeks = qq('#diary > .diary-week');
        const firstChild = this.diary.firstElementChild;
        const lastWeek = weeks.length ? weeks[weeks.length-1] : undefined;
        div.innerHTML = html;

        const new_start_at = Camdram.parseISODate(div.getElementsByClassName('diary-week')[0].dataset.start);
        const start_at = lastWeek ? Camdram.parseISODate(lastWeek.dataset.start) : null;
        // Converting to array first avoids looping over the HTMLCollection, which
        // is being implicitly modified when elements are moved out of it.
        const newElements = Array.prototype.slice.call(div.children);
        for (let el of newElements) {
            el.style.maxHeight = '0';
            el.style.transition = 'max-height 500ms ease-in';
            el.style.overflow = 'hidden';
            if (new_start_at >= start_at) {
                this.diary.appendChild(el);
            } else {
                this.diary.insertBefore(el, firstChild);
            }
        }
        window.setTimeout(() => {
            for (let el of newElements) el.style.maxHeight = '800px';
        }, 50);
        window.setTimeout(() => {
            for (let el of newElements) {
                el.style.maxHeight = null;
                el.style.overflow = null;
                el.style.transition = null;
            }
            if (cb) cb();
        }, 550);

        // Ensure each period label appears exactly once
        const seen_start_at = [];
        for (let label of this.diary.querySelectorAll('.diary-period-label')) {
            let start_at = label.dataset.start;
            if (seen_start_at.indexOf(start_at) >= 0) {
                label.parentNode.removeChild(label);
            } else seen_start_at.push(start_at);
        }
    }
    change_state(state, replace) {
        this.state = state;
        this.on_state_change(state, replace);
    }
    load_from_state(state) {
        if (state.year && state.period) {
            this.is_loading = true;
            this.diary.innerHTML = '';
            const end = state.end ? Camdram.parseISODate(state.end) : null;
            Camdram.diary_server.get_content_by_period(state.year, state.period, end,
                data => this.insert_content(data, () => this.is_loading = false));
        } else if (state.start && state.end) {
            this.is_loading = true;
            this.diary.innerHTML = '';
            Camdram.diary_server.get_content_by_dates(
                Camdram.parseISODate(state.start), Camdram.parseISODate(state.end),
                data => this.insert_content(data, () => this.is_loading = false));
        } else {
            this.is_loading = true;
            this.diary.innerHTML = '';
            Camdram.diary_server.get_content_for_today(
                data => this.insert_content(data, () => this.is_loading = false));
        }
    }
    goto_today() {
        this.is_loading = true;
        this.diary.innerHTML = '';
        Camdram.diary_server.get_content_for_today(
            data => this.insert_content(data, () => this.is_loading = false));
        this.change_state({}, false);
    }
    goto_period(year, period) {
        this.is_loading = true;
        this.diary.innerHTML = '';
        Camdram.diary_server.get_content_by_period(year, period, null, data => {
            this.insert_content(data);
            this.is_loading = false;
            this.change_state({year: year, period: period}, false);
        });
        this.change_state({}, false);
    }
    load_previous_weeks(num_weeks) {
        if (this.is_loading) return;
        this.is_loading = true;
        const start = Camdram.datePlusDays(this.first_date, -7*num_weeks);
        const end = this.first_date;
        Camdram.diary_server.get_content_by_dates(start, end,
            data => this.insert_content(data, () => this.is_loading = false));
        this.change_state({
            start: Camdram.formatISODate(start),
            end: Camdram.formatISODate(this.last_date)
        }, true);
    }
    load_next_weeks(num_weeks) {
        if (this.is_loading) return;
        this.is_loading = true;
        const start = this.last_date;
        const end = Camdram.datePlusDays(this.last_date, +7*num_weeks);
        Camdram.diary_server.get_content_by_dates(start, end,
            data => this.insert_content(data, () => this.is_loading = false));
        this.state.end = Camdram.formatISODate(end);
        this.change_state(this.state, true);
    }
    on_state_change(data, replace) {
        if (q('#diary > .diary-week') && history.pushState) {
            let url;
            if (data.year && data.period) {
                url = Routing.generate('acts_camdram_diary_period',data);
            } else if (data.start) {
                url = Routing.generate('acts_camdram_diary_date', data);
            } else {
                url = Routing.generate('acts_camdram_diary', data);
            }
            if (replace) {
                history.replaceState(data, document.title, url);
            } else {
                history.pushState(data, document.title, url);
            }
        }
    }
};

Camdram.diary_selector = class {
    constructor(diary) {
        this.diary = diary;
        this.years_select = q('#years');
        this.periods_select = q('#periods');
    }
    year(val) {
        if (!arguments.length) return this.years_select.value;
        else this.years_select.value = val;
    }
    period(val) {
        if (!arguments.length) return this.periods_select.value;
        else this.periods_select.value = val;
    }
    update_years() {
        const previous_period = this.period();
        this.periods_select.setAttribute('disabled', 'disabled');
        $.get(Routing.generate('get_time-period', {'year': this.year(), '_format' : 'json'}), periods => {
            this.periods_select.innerHTML = '';
            for (const period of periods) {
                const option = document.createElement('option');
                option.value = period.slug;
                option.textContent = period.name;
                this.periods_select.appendChild(option);
            }
            this.period(previous_period);
            this.periods_select.removeAttribute('disabled');
            this.update_diary();
        });
    }
    update_diary() {
        this.diary.goto_period(this.year(), this.period(), true);
    }
    attach_events() {
        this.periods_select.addEventListener('change', () => this.update_diary());
        this.years_select.addEventListener('change', () => this.update_years());
        q('#diary_jump_form').addEventListener('submit', e => {
            e.preventDefault();
            this.update_years();
        });
    }
};


$(function() {
    const diary = new Camdram.diary();
    const selector = new Camdram.diary_selector(diary);

    window.addEventListener('popstate', e => {
        const data = e.state;
        if (data) diary.load_from_state(data);
    });

    $(window).endlessScroll({callback: () => {
        if (diary.diary.childNodes.length && !q('.diary-week[style*="transition"]')) {
            diary.load_next_weeks(6);
        }
    }});

    selector.attach_events();
    $('#load_previous').click(function(e) {
        e.preventDefault();
        diary.load_previous_weeks(1);
    });

    $('#load_today').click(function(e) {
        e.preventDefault();
        diary.goto_today();
    });

});
