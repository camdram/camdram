import L from 'leaflet'

L.Marker.prototype.options.icon = L.divIcon({
    className: 'custom-map-icon',
    html: '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="40px"><path fill="#49c" fill-rule="even-odd" d="M1.1,17A12,12 0 1 1 22.9,17L12,40Z"/><circle cx="12" cy="12" r="4" fill="#fff"/></svg>',
    iconSize:    [40, 40],
    iconAnchor:  [12, 40],
    popupAnchor: [0, -34],
    tooltipAnchor: [16, -28]
});

$(function() {
    $.fn.venueMap = function(action, opts) {
        opts = opts || {}
        var $self = $(this)
        var map = $self.data('map')

        if (!map) {
            var centre = opts.location || [52.20531, 0.12279]
            var zoom = opts.location ? 16 : 14
            map = L.map($(this).attr('id')).setView(centre, zoom);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            }).addTo(map);

            $self.data('map', map)
        }
        
        switch(action) {
            case 'add-marker':
                let marker = L.marker(opts.location)
                    .addTo(map).bindPopup(opts.content)
                if (opts.open) {
                    marker.openPopup()
                }
                return marker
            case 'select-marker':
                if (opts.marker) {
                    $self.scrollTo()
                    map.setView(opts.marker.getLatLng(), 17)
                    opts.marker.openPopup()
                }
                break
        }
    }
});
