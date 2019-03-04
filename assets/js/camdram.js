import Dropzone from 'dropzone';
import Routing from 'router';
import Bloodhound from 'typeahead.js'

// Leak Routing to the global scope so that inline scripts work.
window.Routing = Routing;

;(function($, window) {
    String.prototype.truncate = function(length) {
        str = jQuery.trim(this).substring(0, length)
            .trim(this);
        if (this.length > length) str += '...';
        return str;
    }

    var doCookieConsent = function() {
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

    var supportsDateInput = function() {
        const input = document.createElement(`input`);
        input.setAttribute(`type`, `date`);

        const notADateValue = `not-a-date`;
        input.setAttribute(`value`, notADateValue);

        return !(input.value === notADateValue);
      }

    var showModalDialog = function(title, body) {
        $(body).prepend($('<h5/>').text(title))
               .prepend($('<a/>').attr('aria-label', 'Close dialog')
                                 .attr('role', 'button')
                                 .addClass('close-reveal-modal')
                                 .html('&#215;').click(hideModalDialog))

        $('<div/>').attr('role', 'dialog').attr('aria-modal', 'true')
                   .addClass('reveal-modal')
                   .append(body).appendTo('body');
    }

    var hideModalDialog = function() {
        $(".reveal-modal").remove();
    }

    var createTabContainers = function(elementsToFix) {
        $(".tabbed-content > .title", elementsToFix).click(function(e) {
            e.preventDefault();
            $(this.parentNode).children(".title.active").removeClass("active");
            this.classList.add("active");
        });
    }

    // This function is called on the document later, but also
    // on extra elements as they are added to the page.
    var fixHtml = function(elementsToFix){
        $('.news_media', elementsToFix).newsFeedMedia();
        $('.button-destructive').deleteLink();
        createTabContainers();

        if (!supportsDateInput()) {
            // Inject custom datepicker on desktops
            // (Use on native datepicker on mobile)
            $('input[type=date]', elementsToFix).datepicker({
                dateFormat: 'yy-mm-dd',
                constrainInput: true
            });
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
                },200);
            })
        });
    };

    $.fn.scrollTo = function(options) {
        var options = $.extend({
            speed: 500,
            threshold: 0.7,
            overshoot: 10
        }, options);

        var top = $('html').scrollTop();
        var max = $(this).offset().top + options.threshold * $(this).height();
        if (top > max) {
            $('html, body').animate({scrollTop: $(this).offset().top - options.overshoot}, options.speed);
        }
    }

    $.fn.newsFeedMedia = function() {
        this.each(function() {
            var $media = $(this);
            var $img = $media.parents('.news_link').find('img');
            var $panel = $media.parents('.news_link');
            $media = $media.remove().show();
            $img.addClass('has_media')
                .click(function() {
                    $panel.html($media);
                })
        });
    }

    $.fn.formMap = function(map) {
        $(this).each(function() {
            var $self = $(this);
            var $lat = $self.find('input').eq(0);
            var $long = $self.find('input').eq(1);
            $self.children().first().hide();

            var marker = new google.maps.Marker({
                map: map,
                title:"Selected Location",
                draggable: true
            });;
            if ($lat.val() && $long.val()) {
                var pos = new google.maps.LatLng($lat.val(), $long.val());
                marker.setPosition(pos);
                map.setCenter(pos);
                map.setZoom(17);
            }

            var updatePosition = function(animate) {
                return function(e) {
                    if (animate) marker.setMap(null);
                    marker.setPosition(e.latLng);
                    if (animate) {
                        marker.setAnimation(google.maps.Animation.DROP);
                        marker.setMap(map);
                    }
                    $lat.val(e.latLng.lat());
                    $long.val(e.latLng.lng());
                }
            }

            google.maps.event.addListener(map, 'click', updatePosition(true));
            google.maps.event.addListener(marker, 'dragend', updatePosition(false));

        })
    }

    $.fn.entitySearch = function(options) {
        if (options['auto']) {
           this.find('[data-entitysearch-route]').each(function() {
               $(this).entitySearch({
                   placeholder: 'start typing to search',
                   prefetch: this.getAttribute('data-entitysearch-prefetch'),
                   route: this.getAttribute('data-entitysearch-route')
               });
               // Ensure that entitySearch is fired only once per element,
               // regardless of how many times entitySearch({auto}) is called.
               this.removeAttribute('data-entitysearch-prefetch');
               this.removeAttribute('data-entitysearch-route');
           });
           return;
        }

        var options = $.extend({
            placeholder: 'start typing to search',
            prefetch : true
        }, options);
        var $self = $(this);

        var tokenize = function(str) {
            return $.trim(str).toLowerCase().replace(/[\(\)]/g, '').split(/[\s\-_]+/);
        }

        var filter = function(items) {
            for (var i in items) {
                items[i].tokens = tokenize(items[i].name).concat(tokenize(items[i].short_name));
            }
            return items;
        }

        var onValueSelect = function(event, datum) {
            $self.parent().siblings('input[type=hidden]').val(datum.id);
            $self.trigger('entitysearch:changed', [datum]);
        };

        console.log(Routing.generate(options.route, {q: '%QUERY', _format: 'json'}))

        var engine = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace('value'),
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
           console.log($self.parent().siblings('input[type=hidden]').val())
           $self.parent().siblings('input[type=hidden]').val('');
       }).attr('placeholder', options.placeholder);

    }

    $.fn.entityCollection = function(options) {
        var options = $.extend({
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
            }

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
            }

            $self.on('click', '[data-entitycollection]', rowSwapper);

            update_links();
            $self.children().each(options.initialiseRow);
        })
    }

    $.fn.deleteLink = function() {
        $(this).each(function() {
            var $self = $(this);
            var dialogtitle = this.getAttribute('data-title');
            var bodytext = this.getAttribute('data-text') || "";
            if (this.tagName.toLowerCase() == "a") {
                var href = $self.attr('href');

                // Remove href to prevent accidental ctrl/middle clicking
                $self.attr('href', '#');
                var action = function() { document.location = href; };
            } else if (this.tagName.toLowerCase() == "button") {
                var form = $self.parents('form:first').get(0);
                // Inactivate the form.
                form.setAttribute('data-oldaction', form.action);
                form.action = '';
                var action = function() {
                    form.action = form.getAttribute('data-oldaction');
                    form.submit();
                };
            } else {
                return;
            }

            $self.click(function(e) {
                e.preventDefault();

                showModalDialog(dialogtitle,
                    (bodytext ? $('<p/>').text(bodytext).append($("<br>")) : $("<p/>"))
                        .append($('<a/>').addClass('button').text('Yes').click(action))
                        .append(' ')
                        .append($('<a/>').addClass('button').text('No').click(hideModalDialog))
                    );
            })
        });
    }

    $.fn.endlessScroll = function(options) {
        var options = $.extend({
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
    }

    $(function() {
        fixHtml($(document));
        doCookieConsent();
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
    			  msgDiv.className = 'hidden'
    			  this.element.append(msgDiv);
    			  
    			  this.on('error', function(file, errorMessage, blah) {
			    	this.removeAllFiles();
			    	
			    	var errorText = 'Error uploading "' + file.name + '"';
			    	
			    	if (typeof errorMessage == 'string')
		    		{
			    		errorText += ':<br />' + errorMessage;
		    		}
			    	else if (typeof errorMessage.error == 'string')
		    		{
			    		errorText += ':<br />' + errorMessage.error;
		    		}
			    	msgDiv.innerHTML = errorText;
			    	msgDiv.className = 'alert-box alert round';
			      })
			      .on('addedfile', function() {
			    	  msgDiv.className = 'hidden';
			    	  msgDiv.innerHTML = '';
			      })
			      .on('success', function(file) {
			    	  this.destroy();
			    	  
			    	  msgDiv.innerHTML = '"' + file.name + '" uploaded'
			    	  		+ '<br />Reloading page...'
			    	  msgDiv.className = 'alert-box success round'
			    	  
		              location.reload();
    		      })
    		      .on("maxfilesexceeded", function(file) { 
    		    	  this.removeFile(file); 
    		      });
    		   }
    		};

})(jQuery, window);
