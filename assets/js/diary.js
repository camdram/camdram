;(function() {
    var maxHeight = 35;
    var maxWeekHeight = 300;

    var shrinkDiaryItems = function(target) {
        $('.diary-item', target).each(function() {
            var $self = $(this);
            if ($self.is(':visible') && $self.height() > maxHeight) {
                var originalHeight = $self.height() + 10;
                $self.addClass('diary-item-hidden').css({
                    'overflow': 'hidden',
                    'height': maxHeight
                })
                $self.mouseenter(function() {
                    var pos = $self.position();
                    $('.diary-item').css({'z-index': ''});
                    $self.animate({'height': originalHeight, 'marginBottom': -originalHeight+maxHeight}, 200);
                    $self.css({'z-index': 1, 'position': 'relative'});
                    $self.removeClass('diary-item-hidden');
                }).mouseleave(function() {
                    $self.animate({'height': maxHeight, 'marginBottom': 0}, 200, function() {
                        $self.css({'z-index': '', 'position': ''});
                        $self.addClass('diary-item-hidden');
                    });
                })
            }
        });
        $('.diary-content', target).each(function() {
            var $self = $(this);
            if ($self.is(':visible') && $self.height() > maxWeekHeight) {
                $self.addClass('diary-content-overflow');
                $self.css({'height': maxWeekHeight});
            }
        })
    }
    shrinkDiaryItems('body');
})();