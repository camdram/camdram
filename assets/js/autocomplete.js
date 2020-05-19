import Routing from 'router';

const q = document.querySelector.bind(document);
const qq = document.querySelectorAll.bind(document);

// Register the autocomplete system
Camdram.autocomplete = {
    cache   : {},   // Cache, for storing previous results
    items   : [],   // Stored result items
    timeout : null  // Timeout ID, for request debouncing
};
// On load register all requirements into views
window.addEventListener('DOMContentLoaded', event => {
    const searchfield = q('#searchfield');
    const search_form = q('#search_form');
    search_form.addEventListener('focus', function(e) {
        if (q('header .search').classList.contains('active')) return;
        Camdram.autocomplete.suggest(this);
        /* .active is to work around lack of :focus-within support and should eventually be removed */
        q('header .search').classList.add('active');
    }, true);
    search_form.addEventListener('blur', function(e) {
        if (e.relatedTarget && search_form.contains(e.relatedTarget)) {
            return;
        }
        window.setTimeout(function() {
            Camdram.autocomplete.drawControl(false);
            q('header .search').classList.remove('active');
        }, 100);
    }, true);
    searchfield.addEventListener('keyup', function(e) {
        const contents = q("#searchfield").value.toLowerCase();
        if (contents === 'zz' || contents === 'rr') spinTheWorld();
        if(e.keyCode == 38 || e.keyCode == 40)
            Camdram.autocomplete.shiftOption(this, (e.keyCode == 40));
        else if(e.keyCode == 13)
            Camdram.autocomplete.chooseOption(this);
        else if(e.keyCode != 37 && e.keyCode != 39)
            Camdram.autocomplete.suggest(this);
        e.preventDefault();
        return false;
    });
    searchfield.addEventListener('keydown', function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
        return true;
    });
    searchfield.addEventListener('paste', function() {
        Camdram.autocomplete.suggest(this);
    });
    q('#search_form .results').addEventListener('mousemove', e => {
        let li = e.target;
        while (li && li.tagName.toLowerCase() != 'li' && li != e.currentTarget) li = li.parentNode;
        if (li && li.tagName.toLowerCase() == 'li' && !li.classList.contains('active')) {
            for (const el of qq("#search_form .results ul li")) el.classList.remove('active');
            li.classList.add('active');
        }
    });
    q('#search_form .results').addEventListener('click', e => {
        let li = e.target;
        while (li && li.tagName.toLowerCase() != 'li' && li != e.currentTarget) li = li.parentNode;
        if (li && li.tagName.toLowerCase() == 'li' && !li.classList.contains('active')) {
            console.log(li.children[0]);
        }
    });
});

Camdram.autocomplete.chooseOption = function(e) {
    // If we do have a selected item then jump to it, otherwise just search
    const active_item = q("#search_form .results ul li.active");
    if (active_item) {
        q('#searchfield').value = active_item.querySelector('span').textContent;
        window.location.href = active_item.querySelector('a').href;
    } else {
        q('#search_form').submit();
    }
};

Camdram.autocomplete.shiftOption = function(field, down) {
    const firstItem = q("#search_form .results ul li");
    if (firstItem == null) return;

    const current = q("#search_form .results ul li.active");

    if (current == null) {
        if (down) firstItem.classList.add('active');
    } else if (down && current.nextElementSibling) {
        current.classList.remove('active');
        current.nextElementSibling.classList.add('active');
    } else if (!down && current.previousElementSibling) {
        current.classList.remove('active');
        current.previousElementSibling.classList.add('active');
    }
};

Camdram.autocomplete.drawControl = function(show) {
    const results = q("#search_form .results");

    if (show) {
        results.style.display = null;
        window.setTimeout(() => {
            results.classList.remove('hidden');
        }, 5);
    } else {
        results.classList.add('hidden');
        window.setTimeout(() => {
            results.style.display = 'none';
        }, 400);
    }
};

Camdram.autocomplete.suggest = function(field) {
    if (Camdram.autocomplete.timeout !== null) {
        window.clearTimeout(Camdram.autocomplete.timeout);
    }
    Camdram.autocomplete.timeout = window.setTimeout(Camdram.autocomplete.requestOptions, 100);
};

Camdram.autocomplete.requestOptions = function() {
    if (Camdram.autocomplete.change_count > 0) {
        return;
    }
    const typed = q('#searchfield').value;
    if (typed.length < 2) {
        Camdram.autocomplete.drawControl(false);
        return;
    }

    if (typeof Camdram.autocomplete.cache[typed] != 'undefined') {
        //We've done this request before, so load the results from the cache
        Camdram.autocomplete.displayResults(typed, Camdram.autocomplete.cache[typed]);
    } else {
        q("#search_form .fa-spinner").classList.add('show');
        // Activate the field
        var url = Routing.generate('search_entity', {mode: '.json', q: typed, limit: 10});
        Camdram.get(url, responseText => {
            const data = JSON.parse(responseText);
            Camdram.autocomplete.displayResults(typed, data, false);
            Camdram.autocomplete.cache[typed] = data;
            q("#search_form .fa-spinner").classList.remove('show');
        }, () => { // error
            Camdram.autocomplete.displayResults(typed, [], true);
            q("#search_form .fa-spinner").classList.remove('show');
        });
    }
};

Camdram.autocomplete.displayResults = function(query, items, error) {
    const prevResultCount = qq("#search_form .results ul li").length;

    q("#search_form .results ul").innerHTML = '';
    q("#search_form .noresults").style.display = 'none';
    q("#search_form .error").style.display = 'none';

    let first_item = true;

    // Draw out the elements
    if (items.length > 0) {
        let i = 0;
        for (const result of items) {
            const item = document.createElement("li");
            item.innerHTML = '<a class="resultText"><i></i><span></span></a>';

            // Autoselect the first item
            if (first_item) {
                first_item = false;
                item.classList.add('active');
            }

            // Add in the text
            const link = item.children[0];
            link.href = Routing.generate('get_'+result.entity_type, {identifier: result.slug});

            // Add in the icon
            let icon_class = 'fa fa-user';
            switch (result.entity_type) {
                case 'show':    icon_class = "fa fa-ticket"; break;
                case 'venue':   icon_class = "fa fa-building"; break;
                case 'society': icon_class = "fa fa-briefcase"; break;
            }
            link.children[0].className = icon_class;
            link.children[1].textContent = result.name;

            if (result.entity_type == 'person' && result.show_count > 0) {
                const from = Camdram.parseISODate(result.first_active);
                const till = Camdram.parseISODate(result.last_active);
                const now_ms = Date.now();
                const fromString = Camdram.formatMMMYYYY(from);
                const tillString = Camdram.formatMMMYYYY(till);
                const SIX_MONTHS = 180*86400*1000;
                const em = document.createElement('em');
                em.textContent = `\xA0(${result.show_count} ${result.show_count == 1 ? 'show' : 'shows'}, `;

                if (now_ms - from.valueOf() < SIX_MONTHS && now_ms - till.valueOf() < SIX_MONTHS) {
                    // All activity within past six months
                    em.textContent += 'active currently)';
                } else if (fromString === tillString) {
                    // All activity in same month
                    em.textContent += `active ${fromString})`;
                } else if (now_ms - till.valueOf() < SIX_MONTHS) {
                    // Active both within and before past six months
                    em.textContent += `active since ${fromString})`;
                } else {
                    // Active only prior to past six months
                    em.textContent += `active ${fromString}–${tillString})`;
                }

                link.appendChild(em);
            }

            if (result.entity_type == 'show' && result.start_at != '') {
                const date = Camdram.parseISODate(result.start_at);
                const em = document.createElement('em');
                if (date) {
                    em.textContent = '\xA0(' + Camdram.formatMMMYYYY(date) + ')';
                    link.appendChild(em);
                }
            }

            if (i >= prevResultCount) {
                item.classList.add('hidden');
            }
            // Add item into the page
            q("#search_form .results ul").appendChild(item);
            i++;
        }
        const allResultsItem = document.createElement('li');
        if (i >= prevResultCount) allResultsItem.classList.add('hidden');
        allResultsItem.innerHTML = '<a class="resultText"><i></i><span>See all results...</span></a>';
        allResultsItem.children[0].href = Routing.generate('search_entity', {q: query});
        q("#search_form .results ul").appendChild(allResultsItem);
        Camdram.autocomplete.drawControl(true);
        window.setTimeout(() => {
            for (const el of qq("#search_form .results ul .hidden")) {
                el.classList.remove('hidden');
            }
        }, 10);
    } else if (error || items.error) {
        q("#search_form .error").textContent = items.error ?
            'An error occured. ' + items.error :
            'Search is not available at the moment';
        q("#search_form .error").style.display = null;
        Camdram.autocomplete.drawControl(true);
    } else {
        q("#search_form .noresults").style.display = null;
        q('#search_form .noresults .query').textContent = query;
        Camdram.autocomplete.drawControl(true);
    }
};
