import 'cookieconsent';
import Dropzone from 'dropzone';
import Routing from 'router';

const Camdram = {};
export default Camdram;

// Leaks to the global scope
window.Routing = Routing;
window.Camdram = Camdram;

// Basic date handling, rather than a 150 KB library.
Camdram.short_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
Camdram.parseISODate = function(yyyy_mm_dd) {
    if (yyyy_mm_dd == null) return null;
    const match = yyyy_mm_dd.match(/(\d{4,})-(\d\d)-(\d\d)/);
    const date = new Date();
    if (match === null) return null;
    date.setUTCFullYear(+match[1], match[2] - 1, +match[3]);
    return date;
};
Camdram.formatISODate = date => date.getUTCFullYear() +
    (date.getUTCMonth() < 9 ? '-0' : '-') +
    (date.getUTCMonth() + 1) +
    (date.getUTCDate() < 10 ? '-0' : '-') +
    date.getUTCDate();
Camdram.formatMMMYYYY = date =>
    Camdram.short_months[date.getUTCMonth()] + ' ' + date.getUTCFullYear();
Camdram.datePlusDays = function(date, days) {
    const result = new Date(date.valueOf());
    result.setUTCDate(result.getUTCDate() + days);
    return result;
};

// Ajax wrapper
Camdram.get = function(url, success, failure) {
    const xhr = new XMLHttpRequest();
    xhr.addEventListener("load", function() {
        if (this.status >= 200 && this.status < 300) {
            success.call(this, this.responseText);
        } else if (failure) {
            failure.call(this);
        }
    });
    if (failure) xhr.addEventListener("error", failure);
    xhr.open("GET", url);
    xhr.send();
};

let spun = false;
window.spinTheWorld = function() {
    const element = document.getElementsByTagName('body')[0];
    element.style['transition-duration'] = '1.5s';
    element.style['transition-timing-function'] = 'ease-in-out';
    if (spun) {
        element.style.transform = 'rotate(0deg)';
        spun = false;
    } else {
        element.style.transform = 'rotate(360deg)';
        spun = true;
    }
};

function doCookieConsent() {
    window.cookieconsent.initialise({
        "palette": {
            "popup": {
                 "background": "#fe5c1f",
                 "text": "#ffffff"
            },
            "button": {
                  "background": "#fff0c8"
            }
        },
        "content": {
            "href": Routing.generate('acts_camdram_privacy') + "#cookies"
        }
    });
}

function supportsDateInput() {
    const input = document.createElement(`input`);
    input.setAttribute(`type`, `date`);

    const notADateValue = `not-a-date`;
    input.setAttribute(`value`, notADateValue);

    return input.value !== notADateValue;
}

function showModalDialog(title, body) {
    body.insertAdjacentHTML('afterbegin', '<a aria-label="Close dialog" role="button" class="close-reveal-modal">&#215;</a><h5></h5>');
    body.children[0].addEventListener('click', hideModalDialog);
    body.children[1].innerText = title;
    const dialog = document.createElement('div');
    dialog.setAttribute('role', 'dialog');
    dialog.setAttribute('aria-modal', 'true');
    dialog.className = 'reveal-modal';
    dialog.appendChild(body);
    document.body.appendChild(dialog);
}

function hideModalDialog() {
    for (const el of document.querySelectorAll(".reveal-modal")) {
        el.parentNode.removeChild(el);
    }
}

function createTabContainers(elementsToFix) {
    $(".tabbed-content > .title", elementsToFix).click(function(e) {
        e.preventDefault();
        $(this.parentNode).children(".title.active").removeClass("active");
        this.classList.add("active");
    });
}

// This function is called on the document later, but also
// on extra elements as they are added to the page.
function fixHtml(elementsToFix){
    $('.news_media', elementsToFix).newsFeedMedia();
    $('.button-destructive').deleteLink();
    $(elementsToFix).entitySearch({auto: 1});
    createTabContainers();

    if (!supportsDateInput()) {
        // Inject custom datepicker on desktops
        // (Use on native datepicker on mobile)
        $('input[type=date]', elementsToFix).datepicker({
            dateFormat: 'yy-mm-dd',
            constrainInput: true
        });
        if (!document.getElementById('jquery-ui-theme')) {
            $('head').append($('<link rel="stylesheet" href="/jquery-ui.custom.css" type="text/css" id="jquery-ui-theme">'));
        }
    }

    $('.dropdown-link', elementsToFix).each(function() {
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
            }, 200);
        });
    });

    $('.flash-messages p').delay(2500).slideUp(300, function() {
        if (this.parentNode) this.parentNode.removeChild(this);
    });
}

Camdram.flashMessage = function (status, text) {
    const div = document.querySelector('.flash-messages');
    const message = document.createElement('p');
    message.className = status === 'success' ? 'flash-success' : 'flash-error';
    message.textContent = text;
    div.insertBefore(message, null);
    fixHtml(div);
};

Camdram.scrollTo = function(el, options) {
    options = {
        speed: 500,
        threshold: 0.7,
        overshoot: 10,
        ...options
    };

    var top = window.pageYOffset;
    var max = $(el).offset().top + options.threshold * $(el).height();
    if (top > max) {
        $('html, body').animate({scrollTop: $(el).offset().top - options.overshoot}, options.speed);
    }
};

$.fn.newsFeedMedia = function() {
    this.each(function() {
        var $media = $(this);
        var $img = $media.parents('.news_link').find('img');
        var $panel = $media.parents('.news_link');
        $media = $media.remove().show();
        $img.addClass('has_media')
            .click(function() {
                $panel.html($media);
            });
    });
};

$.fn.entitySearch = function(options) {
    if (options.auto) {
       this.find('[data-entitysearch-route]').each(function() {
           $(this).entitySearch({
               placeholder: 'start typing to search',
               prefetch: this.getAttribute('data-entitysearch-prefetch') == 'true',
               route: this.getAttribute('data-entitysearch-route')
           });
       });
       // Ensure that entitySearch is fired only once per element,
       // regardless of how many times entitySearch({auto}) is called.
       this.find('[data-entitysearch-prefetch]').removeAttr('data-entitysearch-prefetch');
       this.find('[data-entitysearch-route]').removeAttr('data-entitysearch-route');
       return;
    }

    options = {
        placeholder: 'start typing to search',
        prefetch: true,
        ...options
    };
    var $self = $(this);

    var tokenize = function(str) {
        return $.trim(str).toLowerCase().replace(/[\(\)]/g, '').split(/[\s\-_]+/);
    };

    var filter = function(items) {
        for (var i in items) {
            items[i].tokens = tokenize(items[i].name).concat(tokenize(items[i].short_name));
        }
        return items;
    };

    var onValueSelect = function(event, datum) {
        $self.parent().siblings('input[type=hidden]').val(datum.id);
        $self.trigger('entitysearch:changed', [datum]);
    };


    var engine = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: options.prefetch ? {url: Routing.generate(options.route, {_format: 'json'}), filter: filter} : null,
        remote: {
            url: Routing.generate(options.route, {q: 'QUERY', _format: 'json'}),
            wildcard: 'QUERY',
            filter: filter
        }
    });
    engine.initialize();

    $self.typeahead(null, {
       name: options.route,
       valueKey: 'name',
       source: engine,
       display: 'name'
   }).on('typeahead:autocompleted', onValueSelect).on('typeahead:selected', onValueSelect);

   $(this).change(function() {
       $self.parent().siblings('input[type=hidden]').val('');
   }).attr('placeholder', options.placeholder);

};

$.fn.entityCollection = function(options) {
    options = {
        max_items: 100,
        min_items: 1,
        initialiseRow: function() {},
        add_link_selector: '.add_link',
        ...options
    };

    $(this).each(function() {
        var $self = $(this);
        var index = $(this).children().length;
        var $add_link = $(options.add_link_selector, $self.parent());

        var update_links = function() {
            var uparrows = $('[data-entitycollection="moveup"]', $self);
            var downarrows = $('[data-entitycollection="movedown"]', $self);
            if ($('.remove_link', $self).length > options.min_items) {
                $('.remove_link', $self).css('visibility', 'visible');
            }
            else {
                $('.remove_link', $self).css('visibility', 'hidden');
            }

            if ($('.remove_link', $self).length < options.max_items) {
                $add_link.css('visibility', 'visible');
            }
            else {
                $add_link.css('visibility', 'hidden');
            }
            uparrows.not(':first').css('visibility', 'visible');
            uparrows.first().css('visibility', 'hidden');
            downarrows.not(':last').css('visibility', 'visible');
            downarrows.last().css('visibility', 'hidden');
        };

        $add_link.click(function(e) {
            e.preventDefault();
            var html = $self.attr('data-prototype').replace(/__name__/g, index);
            var $row = $(html);
            $self.append($row);
            fixHtml($row);
            update_links();
            options.initialiseRow(index, $row);
            index++;
        });

        $self.on('click', '.remove_link', function(e) {
            e.preventDefault();
            $(this).parentsUntil($self).remove();
            update_links();
        });

        var rowSwapper = function(e) {
            e.preventDefault();
            var action = this.getAttribute('data-entitycollection');
            var $myRow = $(this).parentsUntil($self).last();
            var myInput = $myRow.find("input[name]").get(0);
            var otherInput = (action === 'moveup' ? $myRow.prev() : $myRow.next()
                  ).find("input[name]").get(0);
            var temp = myInput.value;
            myInput.value = otherInput.value;
            otherInput.value = temp;
        };

        $self.on('click', '[data-entitycollection]', rowSwapper);

        update_links();
        $self.children().each(options.initialiseRow);
    });
};

$.fn.deleteLink = function() {
    $(this).each(function() {
        const dialogtitle = this.getAttribute('data-title');
        const bodytext = this.getAttribute('data-text') || "";
        let action;
        if (this.tagName.toLowerCase() == "a") {
            const href = this.getAttribute('href');

            // Remove href to prevent accidental ctrl/middle clicking
            this.setAttribute('href', '#');
            action = function() { document.location = href; };
        } else if (this.tagName.toLowerCase() == "button") {
            let form = this;
            while (form.tagName.toLowerCase() != "form") form = form.parentNode;
            // Inactivate the form.
            form.setAttribute('data-oldaction', form.action);
            form.action = '';
            action = function() {
                form.action = form.getAttribute('data-oldaction');
                form.submit();
            };
        } else return;

        $(this).click(function(e) {
            e.preventDefault();

            const dialogBody = document.createElement('p');
            if (bodytext) dialogBody.innerText = bodytext + '\n';
            dialogBody.insertAdjacentHTML('beforeend', '<a class="button">Yes</a>');
            dialogBody.lastElementChild.addEventListener('click', action);
            dialogBody.insertAdjacentHTML('beforeend', ' <a class="button">No</a>');
            dialogBody.lastElementChild.addEventListener('click', hideModalDialog);

            showModalDialog(dialogtitle, dialogBody);
        });
    });
};

Camdram.endlessScroll = function(options) {
    options = {
        distance: 500,
        interval: 200,
        callback: function() {},
        ...options
    };

    const checkScrollPosition = function() {
        if (window.pageYOffset + window.innerHeight +
            options.distance >= document.body.scrollHeight) {
            options.callback.apply(this);
        }
    };

    setInterval(checkScrollPosition, options.interval);
};

$(function() {
    fixHtml($(document));
    doCookieConsent();
    document.body.addEventListener('keydown', e => {
        if (e.key == "Esc" || e.key == "Escape") hideModalDialog();
    });
});

Dropzone.options.imageUpload = {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 2, // MB
    createImageThumbnails: true,
    thumbnailWidth: 120,
    thumbnailHeight: 120,
    resizeWidth: 1024,
    maxFiles: 1,
    acceptedFiles: 'image/*',
    dictDefaultMessage: 'Click to upload image',
    previewTemplate: '<div class="dz-preview dz-file-preview">'
        + '<div class="dz-details">'
        + '<div class="dz-filename">Uploading <span data-dz-name></span></div>'
        + '<div class="dz-size" data-dz-size></div>'
        + '<img data-dz-thumbnail />'
        + '</div>'
        + ' <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>'
        + '</div>',
    init: function() {
        const msgDiv = document.createElement('div');
        msgDiv.style.display = 'none';
        this.element.appendChild(msgDiv);

        this.on('error', function(file, errorMessage, blah) {
            this.removeAllFiles();

            let errorText = 'Error uploading "' + file.name + '"';

            if (typeof errorMessage == 'string') {
                errorText += ':\n' + errorMessage;
            } else if (typeof errorMessage.error == 'string') {
                errorText += ':\n' + errorMessage.error;
            }
            msgDiv.innerText = errorText;
            msgDiv.style.display = null;
        }).on('addedfile', function() {
            msgDiv.style.display = 'none';
            msgDiv.innerText = '';
        }).on('success', function(file) {
            this.destroy();
            msgDiv.innerText = `“${file.name}” uploaded\nReloading page...`;
            msgDiv.style.display = null;
            location.reload();
        }).on("maxfilesexceeded", function(file) {
            this.removeFile(file);
        });
    }
};
