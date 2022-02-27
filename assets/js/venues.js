import "./base.js";
import { DivIcon, Map, Marker, TileLayer } from "../../node_modules/leaflet/dist/leaflet-src.esm.js";
import "leaflet/dist/leaflet.css";
import "../scss/venues.scss";

const q = document.querySelector.bind(document);
const qq = document.querySelectorAll.bind(document);
const maps = {};

Marker.prototype.options.icon = new DivIcon({
    className: "custom-map-icon",
    html: '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="40px"><path fill="#49c" d="M1.1,17A12,12 0 1 1 22.9,17L12,40Z"/><circle cx="12" cy="12" r="4" fill="#fff"/></svg>',
    iconSize:    [40, 40],
    iconAnchor:  [12, 40],
    popupAnchor: [0, -34],
    tooltipAnchor: [16, -28]
});

function venueMap(elId, action, opts) {
    opts = opts || {};
    let map = maps[elId];

    if (!map) {
        const centre = opts.location || [52.20531, 0.12279];
        const zoom = opts.location ? 16 : 14;
        map = (new Map(elId)).setView(centre, zoom);

        (new TileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        })).addTo(map);

        maps[elId] = map;
    }

    switch (action) {
    case "add-marker": {
        const marker = (new Marker(opts.location)).addTo(map).bindPopup(opts.content);
        if (opts.open) {
            marker.openPopup();
        }
        return marker;
    }
    case "select-marker":
        if (opts.marker) {
            document.getElementById(elId).scrollIntoView();
            map.setView(opts.marker.getLatLng(), 17);
            opts.marker.openPopup();
        }
        break;
    }
}

window.addEventListener("DOMContentLoaded", () => {
    const mapMainId = "map-main";
    if (document.getElementById(mapMainId)) {
        venueMap(mapMainId);

        for (const el of qq(".venue")) {
            const marker = venueMap(mapMainId, "add-marker", {
                "location": [el.dataset.latitude, el.dataset.longitude],
                "content": el.querySelector(".venue-name").innerHTML,
            });
            el.querySelector(".marker").addEventListener("click", _e => {
                venueMap(mapMainId, "select-marker", {marker: marker});
            });
        }
    }

    const venueMapId = "venue-map";
    if (document.getElementById(venueMapId)) {
        const item = q('[itemtype="http://schema.org/PerformingArtsTheater"]');
        // Specifically using HTML we already trust, not innerText.
        const nameHTML = item.querySelector('[itemprop="name"]').innerHTML;
        const latitude = item.querySelector('[itemprop="latitude"]').getAttribute("content");
        const longitude = item.querySelector('[itemprop="longitude"]').getAttribute("content");

        venueMap(venueMapId, "add-marker", {
            "location": [latitude, longitude],
            "content": nameHTML,
            "open": true,
        });
    }
});
