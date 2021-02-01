import 'cookieconsent';
import {install as hotkey_install} from '@github/hotkey';
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
                 "background": "#fb51b7",
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
function fixHtml(elementsToFix) {
    for (const link of elementsToFix.querySelectorAll('.button-destructive')) {
        Camdram.makeLinkSafetyDialog(link);
    }
    for (const searchContainer of elementsToFix.querySelectorAll('[data-entitysearch-route]')) {
        const autocomplete = new Camdram.autocomplete(searchContainer);
    }
    for (const entityCollection of elementsToFix.querySelectorAll('[data-entitycollection-init]')) {
        Camdram.entityCollection(entityCollection);
    }
    createTabContainers(elementsToFix);

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

Camdram.entityCollection = function(el) {
    const options = {
        max_items: 100,
        min_items: 1,
        add_link_selector: '.add_link',
        ...JSON.parse(el.dataset.entitycollectionInit || '{}')
    };

    delete el.dataset.entitycollectionInit;
    let index = el.children.length;
    const add_link = el.parentNode.querySelector(options.add_link_selector);

    const update_links = function() {
        const uparrows = el.querySelectorAll('[data-entitycollection="moveup"]');
        const downarrows = el.querySelectorAll('[data-entitycollection="movedown"]');
        const removeLinks = el.querySelectorAll('.remove_link');
        for (const removeLink of removeLinks) {
            removeLink.style.visibility = (removeLinks.length > options.min_items) ? 'visible' : 'hidden';
        }
        add_link.style.visibility = (removeLinks.length < options.max_items) ? 'visible' : 'hidden';

        for (let i = 0; i < uparrows.length; i++) {
            uparrows[i].style.visibility = i ? 'visible' : 'hidden';
        }
        for (let i = 0; i < downarrows.length; i++) {
            downarrows[i].style.visibility = (i < downarrows.length - 1) ? 'visible' : 'hidden';
        }
    };

    const initialiseRow = function(index, row) {
        const event = new CustomEvent('entitycollection:newrow', {
            bubbles: true, details: {index: index}});
        row.dispatchEvent(event);
    }

    add_link.addEventListener('click', e => {
        e.preventDefault();
        let row = document.createElement('div');
        row.innerHTML = el.dataset.prototype.replace(/__name__/g, index);
        row = row.children[0];
        el.appendChild(row);
        fixHtml(row);
        update_links();
        initialiseRow(index, row);
        index++;
    });

    el.addEventListener('click', e => {
        let target = e.target.closest('.remove_link');
        if (target) {
            e.preventDefault();
            while (target && target.parentNode != el) target = target.parentNode;
            el.removeChild(target);
            update_links();
        } else if ((target = e.target.closest('[data-entitycollection]'))) {
            e.preventDefault();
            const action = target.dataset.entitycollection;
            let myRow = e.target;
            while (myRow && myRow.parentNode != el) myRow = myRow.parentNode;
            const otherRow = (action === 'moveup') ? myRow.previousElementSibling : myRow.nextElementSibling;

            const myInput = myRow.querySelector('input[name]');
            const otherInput = otherRow.querySelector("input[name]");
            [myInput.value, otherInput.value] = [otherInput.value, myInput.value];
        }
    });

    update_links();
    let i = 0;
    for (const child of el.children) initialiseRow(i++, child);
};

Camdram.makeLinkSafetyDialog = function(link) {
    const dialogtitle = link.getAttribute('data-title');
    const bodytext = link.getAttribute('data-text') || "";
    let action;
    if (link.tagName.toLowerCase() == "a") {
        const href = link.getAttribute('href');

        // Remove href to prevent accidental ctrl/middle clicking
        link.setAttribute('href', '#');
        action = function() { document.location = href; };
    } else if (link.tagName.toLowerCase() == "button") {
        let form = link;
        while (form.tagName.toLowerCase() != "form") form = form.parentNode;
        // Inactivate the form.
        form.setAttribute('data-oldaction', form.action);
        form.action = '';
        action = function() {
            form.action = form.getAttribute('data-oldaction');
            form.submit();
        };
    } else return;

    link.addEventListener('click', e => {
        e.preventDefault();

        const dialogBody = document.createElement('p');
        if (bodytext) dialogBody.innerText = bodytext + '\n';
        dialogBody.insertAdjacentHTML('beforeend', '<a class="button">Yes</a>');
        dialogBody.lastElementChild.addEventListener('click', action);
        dialogBody.insertAdjacentHTML('beforeend', ' <a class="button">No</a>');
        dialogBody.lastElementChild.addEventListener('click', hideModalDialog);

        showModalDialog(dialogtitle, dialogBody);
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

document.addEventListener('DOMContentLoaded', function() {
    fixHtml(document);
    doCookieConsent();
    document.body.addEventListener('keydown', e => {
        if (e.key == "Esc" || e.key == "Escape") hideModalDialog();
    });
    // Install all the hotkeys on the page
    for (const el of document.querySelectorAll('[data-hotkey]')) {
        hotkey_install(el);
    }
});

Dropzone.options.imageUpload = {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 4, // MiB
    chunking: true,
    chunkSize: 1000000,
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
