import Routing from 'router';
import moment from 'moment';

// Register the autocomplete system
var Camdram = Camdram || {};
Camdram.autocomplete = {};
// Stored result items
Camdram.autocomplete.items = [];
//Timeout return value, for request debouncing
Camdram.autocomplete.timeout;
//Cache, for storing previous results
Camdram.autocomplete.cache = {}
//Short month strings (used for displaying show dates)
Camdram.autocomplete.short_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
// On load register all requirements into views
$(function() {
    $("#searchfield").focus(function(e) {
        Camdram.autocomplete.suggest(this);
        /* .active is to work around lack of :focus-within support and should eventually be removed */
        document.querySelector('header .search').classList.add('active');
    }).blur(function(e) {
        window.setTimeout(function() {
            Camdram.autocomplete.drawControl(false);
            document.querySelector('header .search').classList.remove('active');
        }, 100)
    }).keyup(function(e) {
        const contents = $("#searchfield")[0].value.toLowerCase();
        if (contents === 'zz' || contents === 'rr') spinTheWorld();
        if(e.keyCode == 38 || e.keyCode == 40)
            Camdram.autocomplete.shiftOption(this, (e.keyCode == 40));
        else if(e.keyCode == 13)
            Camdram.autocomplete.chooseOption(this);
        else if(e.keyCode != 37 && e.keyCode != 39)
            Camdram.autocomplete.suggest(this);
        e.preventDefault();
        return false;
    }).keydown(function(e) {
        if(e.keyCode == 13)
        {
            e.preventDefault();
            return false;
        }
        return true;
    }).on('paste', function() {
            Camdram.autocomplete.suggest(this);
    });
    $('#search_form .results').on('mousemove', 'li', function() {
        if (!$(this).hasClass('active')) {
            $("#search_form .results ul li").removeClass('active');
            $(this).addClass('active');
        }
    });
});

Camdram.autocomplete.chooseOption = function(e) {
    // If we do have a selected item then jump to it, otherwise just search
    var $active_item = $("#search_form .results ul li.active");
    if($active_item.length == 0) {
        $('#search_form').submit();
    }
    else {
        $('#searchfield').val($active_item.find('span').text());
        Camdram.autocomplete.drawControl(false);
        window.location.href = $active_item.children('a').attr('href');
    }
};

Camdram.autocomplete.shiftOption = function(field, down) {
    // Find currently active item
    var $results = $("#search_form .results ul li");
    if($results.length == 0) return;
    // Find currently selected one
    var current = $results.filter('.active');

    if (down && current.length == 0) {
        $results.first().addClass('active');
    }
    else if (down && current.next().length > 0) {
        current.removeClass('active');
        current.next().addClass('active');
    }
    else if (!down && current.prev().length > 0) {
        current.removeClass('active');
        current.prev().addClass('active');
    }
}

Camdram.autocomplete.drawControl = function(show) {
    var $results = $("#search_form .results");

    if(show) {
        $results.show().stop();
        var previous_height = $results.height();
        $results.css('height', 'auto');
        var height =  $results.height();

        $results.css('height', previous_height).animate({"height": height + "px", "opacity": 1.0}, 200);
    }
    else $results.animate({'opacity' : 0.0, 'height' : '0px'}, 200, function() { $(this).css({'display': 'none'})});
}

Camdram.autocomplete.suggest = function(field) {
    if (Camdram.autocomplete.timeout > 0) {
        window.clearTimeout(Camdram.autocomplete.timeout);
    }
    Camdram.autocomplete.timeout = window.setTimeout(Camdram.autocomplete.requestOptions, 100);
}

Camdram.autocomplete.requestOptions = function() {
    if (Camdram.autocomplete.change_count > 0) {
        return;
    }
    var typed = $('#searchfield').val();
    if(typed.length < 2) {
        Camdram.autocomplete.drawControl(false);
        return;
    }

    if (typeof Camdram.autocomplete.cache[typed] != 'undefined') {
        //We've done this request before, so load the results from the cache
        Camdram.autocomplete.displayResults(typed, Camdram.autocomplete.cache[typed])
    }
    else {
        $("#search_form .fa-spinner").fadeIn(100);
        // Activate the field
        var url = Routing.generate('search_entity', {_format: 'json', q: typed, limit: 10});
        $.getJSON(url, function(data) {
            Camdram.autocomplete.displayResults(typed, data, false);
            Camdram.autocomplete.cache[typed] = data;
            $("#search_form .fa-spinner").fadeOut(100);
        })
        .fail(function()
        {
            Camdram.autocomplete.displayResults(typed, [], true);
            $("#search_form .fa-spinner").fadeOut(100);
        });
    }
}

Camdram.autocomplete.displayResults = function(query, items, error) {
    // Store the results
    $("#search_form .results ul li").remove();
    $("#search_form .noresults").hide();
    $("#search_form .error").hide();

    var first_item = true;

    // Draw out the elements
    if (items.length > 0) {

        for (var i = 0; i < items.length; i++) {
            var result = items[i];
            var item = $("<li/>");

            // Autoselect the first item
            if (first_item) {
                first_item = false;
                item.addClass('active');
            }

            // Add in the text
            var link = $("<a/>")
                .attr('href', Routing.generate('get_'+result.entity_type, {identifier: result.slug}))
                .addClass('resultText')
                .appendTo(item)
                .click(function(e) {
                    e.preventDefault();
                    Camdram.autocomplete.chooseOption(e);
                });

            // Add in the icon
            switch (result.entity_type) {
                case 'show' : var icon_class = "fa fa-ticket"; break;
                case 'venue' : var icon_class = "fa fa-building"; break;
                case 'society' : var icon_class = "fa fa-briefcase"; break;
                default: var icon_class = 'fa fa-user';
            }
            $("<i/>").addClass(icon_class).appendTo(link);

            $('<span/>').text(result.name).appendTo(link);

            if (result.entity_type == 'person' && result.show_count > 0) {
                var from = moment(result.first_active);
                var till = moment(result.last_active);
                var now_ms = Date.now();
                var fromString = from.format('MMM YYYY');
                var tillString = till.format('MMM YYYY');
                var string = ` (${result.show_count} ${result.show_count == 1 ? 'show' : 'shows'}, `;
                const SIX_MONTHS = 180*86400*1000;

                if (now_ms - from.valueOf() < SIX_MONTHS && now_ms - till.valueOf() < SIX_MONTHS) {
                    // All activity within past six months
                    string += 'active currently)';
                } else if (fromString === tillString) {
                    // All activity in same month
                    string += `active ${fromString})`;
                } else if (now_ms - till.valueOf() < SIX_MONTHS) {
                    // Active both within and before past six months
                    string += `active since ${fromString})`;
                } else {
                    // Active only prior to past six months
                    string += `active ${fromString}–${tillString})`;
                }

                $('<em/>').text(string).appendTo(link);
            }

            if (result.entity_type == 'show' && result.start_at != '') {
                var date = moment(result.start_at);
                if (date.isValid()) {
                    $('<em/>').text(' (' + date.format('MMM YYYY') + ')').appendTo(link);
                }
            }

            // Add item into the page
            $("#search_form .results ul").append(item);
        }
        let allResultsItem = $('<li><a class="resultText"><span>See all results...</span></a></li>');
        allResultsItem.find('a')
            .attr('href', `${Routing.generate('acts_camdram_search_search')}?q=${encodeURIComponent(query)}`)
            .click(function(e) {
                e.preventDefault();
                Camdram.autocomplete.chooseOption(e);
            });
        $("#search_form .results ul").append(allResultsItem);
        Camdram.autocomplete.drawControl(true, (items.length + 1) * 40);
    } else if (error) {
        $("#search_form .error").show();
        Camdram.autocomplete.drawControl(true);
    } else {
        $("#search_form .noresults").show();
        $('#search_form .noresults .query').text(query);
        Camdram.autocomplete.drawControl(true);
    }
}
