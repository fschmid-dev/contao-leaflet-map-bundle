/*
 * Contao Leaflet Map Bundle: draws every .leaflet-map[data-leaflet-map] from its JSON data block.
 * Optional: consent before loading (OpenStreetMap tiles) and filter buttons (e.g. per day).
 * Markers without filter values are shown for every filter.
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'leafletMapBundle-acceptLoad';

    function init(container) {
        const dataElement = container.querySelector('.leaflet-map__data');
        if (!dataElement || container._leaflet_map) {
            return;
        }

        const data = JSON.parse(dataElement.textContent);
        container.querySelector('.leaflet-map__data-privacy')?.remove();

        const map = L.map(container).setView(data.center, data.zoom);
        container._leaflet_map = map;

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);

        const markers = data.markers.map((item) => {
            const marker = L.marker([item.lat, item.lng]);
            if (item.popup) {
                marker.bindPopup(item.popup);
            }
            marker.addTo(map);

            return { marker, filter: item.filter || [] };
        });

        if (data.autoZoom && markers.length) {
            map.fitBounds(L.featureGroup(markers.map((m) => m.marker)).getBounds(), { padding: [25, 25] });
        }

        const filters = Object.entries(data.filters || {});
        if (filters.length) {
            container.parentNode.insertBefore(createFilter(map, markers, filters, data.filterAll), container);
        }
    }

    function createFilter(map, markers, filters, allLabel) {
        const nav = document.createElement('div');
        nav.className = 'leaflet-map__filter';
        nav.setAttribute('role', 'group');

        const buttons = [['', allLabel], ...filters].map(([value, label]) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'leaflet-map__filter-button';
            button.textContent = label;
            button.setAttribute('aria-pressed', String('' === value));
            button.addEventListener('click', () => {
                buttons.forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
                markers.forEach(({ marker, filter }) => {
                    const visible = '' === value || !filter.length || filter.includes(value);
                    if (visible && !map.hasLayer(marker)) {
                        marker.addTo(map);
                    } else if (!visible && map.hasLayer(marker)) {
                        marker.remove();
                    }
                });
            });
            nav.appendChild(button);

            return button;
        });

        return nav;
    }

    function start() {
        document.querySelectorAll('[data-leaflet-map]').forEach((container) => {
            const data = JSON.parse(container.querySelector('.leaflet-map__data')?.textContent || '{}');

            if (!data.acceptLoad || sessionStorage.getItem(STORAGE_KEY)) {
                init(container);
                return;
            }

            container.querySelector('.leaflet-map__data-privacy-accept')?.addEventListener('click', () => {
                try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
                init(container);
            });
        });
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
