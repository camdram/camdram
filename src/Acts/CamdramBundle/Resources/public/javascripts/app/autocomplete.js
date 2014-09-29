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
    }).blur(function(e) {
        window.setTimeout(function() {
            Camdram.autocomplete.drawControl(false);
        }, 100)
    }).keyup(function(e) {
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
    $('#search_form .results li').live('mousemove', function() {
        if (!$(this).hasClass('active')) {
            $("#search_form .results ul li").removeClass('active');
            $(this).addClass('active');
        }
    });
    $('#search_form .fulltext a').click(function(e) {
        e.preventDefault();
        $('#search_form').submit();
    })
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
    current.removeClass('active');

    if (down && current.length == 0) {
        $results.first().addClass('active');
    }
    else if (down && current.next().length > 0) {
        current.next().addClass('active');
    }
    else if (!down && current.prev().length > 0) {
        current.prev().addClass('active');
    }
}

Camdram.autocomplete.drawControl = function(show) {
    $results = $("#search_form .results");

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
        var url = Routing.generate('autocomplete_entity', {_format: 'json', q: typed, limit: 10, autocomplete: true});
        $.getJSON(url, function(data) {
            Camdram.autocomplete.displayResults(typed, data);
            Camdram.autocomplete.cache[typed] = data;
            $("#search_form .fa-spinner").fadeOut(100);
        });
    }
}

Camdram.autocomplete.displayResults = function(query, items) {
    // Store the results
    $("#search_form .results ul li:not(.fulltext)").remove();
    // Draw out the elements
    if (items.length > 0) {
        $("#search_form .noresults").hide();
        for (var i = 0; i < items.length; i++) {
            var result = items[i];
            var item = $("<li/>");

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
            var string = result.show_count = ' (' + result.show_count + ' show';
            if (parseInt(result.show_count) != 1) string += 's';

            var date = moment.unix(result.index_date);
            if (date.isValid()) {
                if (date.isBefore(moment().subtract(6, 'months'))) {
                    string += ' until ' + date.format('MMM YYYY');
                } else {
                    string += ', still active';
                }
            }
            string += ')';

            $('<em/>').text(string).appendTo(link);
	    }

            if (result.entity_type == 'show') {
                var date = moment.unix(result.index_date);
                if (date.isValid()) {
                    $('<em/>').text(' (' + date.format('MMM YYYY') + ')').appendTo(link);
                }
            }

            // Add item into the page
            $("#search_form .results ul").append(item);
        }
        Camdram.autocomplete.drawControl(true, (items.length * 40));
    } else {
        $("#search_form .noresults").show();
        $('#search_form .noresults .query').text(query);
        Camdram.autocomplete.drawControl(true);
    }
    $('#search_form .query').text(query);
}
