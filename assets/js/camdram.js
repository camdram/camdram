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
    $(body).prepend($('<h5/>').text(title))
           .prepend($('<a/>').attr('aria-label', 'Close dialog')
                             .attr('role', 'button')
                             .addClass('close-reveal-modal')
                             .html('&#215;').click(hideModalDialog));

    $('<div/>').attr('role', 'dialog').attr('aria-modal', 'true')
               .addClass('reveal-modal')
               .append(body).appendTo('body');
}

function hideModalDialog() {
    $(".reveal-modal").remove();
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

$.fn.scrollTo = function(options) {
    options = $.extend({
        speed: 500,
        threshold: 0.7,
        overshoot: 10
    }, options);

    var top = $('html').scrollTop();
    var max = $(this).offset().top + options.threshold * $(this).height();
    if (top > max) {
        $('html, body').animate({scrollTop: $(this).offset().top - options.overshoot}, options.speed);
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

    options = $.extend({
        placeholder: 'start typing to search',
        prefetch : true
    }, options);
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
    options = $.extend({
        max_items: 100,
        min_items: 1,
        initialiseRow: function() {},
        add_link_selector: '.add_link'
    }, options);

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

            showModalDialog(dialogtitle,
                (bodytext ? $('<p/>').text(bodytext).append($("<br>")) : $("<p/>"))
                    .append($('<a/>').addClass('button').text('Yes').click(action))
                    .append(' ')
                    .append($('<a/>').addClass('button').text('No').click(hideModalDialog))
                );
        });
    });
};

$.fn.endlessScroll = function(options) {
    options = $.extend({
        distance: 500,
        interval: 200,
        callback: function() {}
    }, options);

    var $window = $(window),
        $document = $(document),
        $self = $(this);

    var checkScrollPosition = function() {
        var top = $document.height() - $window.height() - options.distance;
        if ($window.scrollTop() >= top) {
            options.callback.apply($self);
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
        + '<div class="dz-filename alert-box round">Uploading <span data-dz-name></span></div>'
        + '<div class="dz-size" data-dz-size></div>'
        + '<img data-dz-thumbnail />'
        + '</div>'
        + ' <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>'
        + '</div>',
    init: function() {
        var msgDiv = Dropzone.createElement('<div/>');
        msgDiv.className = 'hidden';
        this.element.append(msgDiv);

        this.on('error', function(file, errorMessage, blah) {
            this.removeAllFiles();

            var errorText = 'Error uploading "' + file.name + '"';

            if (typeof errorMessage == 'string') {
                errorText += ':<br />' + errorMessage;
            } else if (typeof errorMessage.error == 'string') {
              errorText += ':<br />' + errorMessage.error;
            }
            msgDiv.innerHTML = errorText;
            msgDiv.className = 'alert-box alert round';
        }).on('addedfile', function() {
            msgDiv.className = 'hidden';
            msgDiv.innerHTML = '';
        }).on('success', function(file) {
            this.destroy();
            msgDiv.innerTest = `“${file.name}” uploaded\nReloading page...`;
            msgDiv.className = 'alert-box success round';
            location.reload();
        }).on("maxfilesexceeded", function(file) {
            this.removeFile(file);
        });
    }
};
