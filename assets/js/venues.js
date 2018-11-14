import L from 'leaflet'

//Hack to make marker images work with webpack versioning
//https://github.com/PaulLeCam/react-leaflet/issues/255#issuecomment-269750542
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
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