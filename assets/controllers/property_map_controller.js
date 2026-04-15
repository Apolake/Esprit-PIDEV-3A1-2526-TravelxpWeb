import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['canvas', 'poiList', 'routeInfo'];
    static values = {
        latitude: Number,
        longitude: Number,
        apiKey: String,
        tilesUrl: String,
        placesUrl: String,
        routeUrl: String,
    };

    async connect() {
        const leafletReady = await this.ensureLeafletLoaded();
        if (!leafletReady || !window.L || !this.hasCanvasTarget || !this.hasApiKeyValue) {
            return;
        }

        if (!Number.isFinite(this.latitudeValue) || !Number.isFinite(this.longitudeValue)) {
            return;
        }

        this.map = window.L.map(this.canvasTarget).setView([this.latitudeValue, this.longitudeValue], 14);

        window.L.tileLayer(this.getMapTilesUrl(), {
            attribution: 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | <a href="https://openmaptiles.org/" target="_blank">&copy; OpenMapTiles</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap</a> contributors',
            maxZoom: 20,
            id: 'osm-bright',
        })
            .on('tileerror', () => {
                if (this.hasRouteInfoTarget) {
                    this.routeInfoTarget.textContent = 'Map tiles failed to load. Check map API key and URL.';
                }
            })
            .addTo(this.map);

        this.routeLayer = null;
        this.poiMarkers = [];
        window.L.marker([this.latitudeValue, this.longitudeValue]).addTo(this.map).bindPopup('Property location').openPopup();

        this.loadPlaces();
    }

    disconnect() {
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    }

    async loadPlaces() {
        if (!this.hasPlacesUrlValue) {
            return;
        }

        const url = new URL(this.placesUrlValue, window.location.origin);
        url.searchParams.set('lat', this.latitudeValue.toString());
        url.searchParams.set('lon', this.longitudeValue.toString());

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const places = Array.isArray(payload.items) ? payload.items : [];

            this.renderPlaces(places);
        } catch {
            // Keep map usable even when POI loading fails.
        }
    }

    renderPlaces(places) {
        if (!this.hasPoiListTarget) {
            return;
        }

        this.poiListTarget.innerHTML = '';

        if (places.length === 0) {
            this.poiListTarget.innerHTML = '<li class="poi-empty">No nearby places found.</li>';

            return;
        }

        const bounds = window.L.latLngBounds([[this.latitudeValue, this.longitudeValue]]);

        places.forEach((place, index) => {
            const lat = Number(place.latitude);
            const lon = Number(place.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
                return;
            }

            const marker = window.L.marker([lat, lon]).addTo(this.map);
            marker.bindPopup(`<strong>${this.escape(place.name || 'POI')}</strong><br>${this.escape(place.category || '')}`);
            this.poiMarkers.push(marker);
            bounds.extend([lat, lon]);

            const item = document.createElement('li');
            item.className = 'poi-item';
            item.innerHTML = `
                <div>
                    <strong>${this.escape(place.name || 'POI')}</strong>
                    <small>${this.escape(place.category || '')}</small>
                </div>
                <button type="button" class="btn" data-poi-index="${index}">Calculate route</button>
                <p class="poi-route" data-route-output="${index}"></p>
            `;

            const button = item.querySelector('button');
            if (button) {
                button.addEventListener('click', () => this.loadRoute(index, lat, lon));
            }

            this.poiListTarget.appendChild(item);
        });

        this.map.fitBounds(bounds.pad(0.18));
    }

    async loadRoute(index, toLat, toLon) {
        if (!this.hasRouteUrlValue) {
            return;
        }

        const output = this.poiListTarget.querySelector(`[data-route-output="${index}"]`);
        if (output) {
            output.textContent = 'Calculating route...';
        }

        const url = new URL(this.routeUrlValue, window.location.origin);
        url.searchParams.set('fromLat', this.latitudeValue.toString());
        url.searchParams.set('fromLon', this.longitudeValue.toString());
        url.searchParams.set('toLat', toLat.toString());
        url.searchParams.set('toLon', toLon.toString());

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                if (output) {
                    output.textContent = 'Unable to calculate route.';
                }

                return;
            }

            const payload = await response.json();
            const route = payload.route;

            if (!route || typeof route.distanceKm === 'undefined' || typeof route.durationMinutes === 'undefined') {
                if (output) {
                    output.textContent = 'Unable to calculate route.';
                }

                return;
            }

            const lineText = `${route.distanceKm} km away • ${route.durationMinutes} minutes by car`;
            if (output) {
                output.textContent = lineText;
            }
            if (this.hasRouteInfoTarget) {
                this.routeInfoTarget.textContent = lineText;
            }

            this.renderRouteGeometry(route.geometry);
        } catch {
            if (output) {
                output.textContent = 'Unable to calculate route.';
            }
        }
    }

    renderRouteGeometry(geometry) {
        if (!this.map || !geometry || !Array.isArray(geometry.coordinates)) {
            return;
        }

        const coordinates = geometry.coordinates;
        const isMultiLine = Array.isArray(coordinates[0]) && Array.isArray(coordinates[0][0]);

        const lines = isMultiLine ? coordinates : [coordinates];
        const latLngLines = lines
            .map((line) => line
                .filter((entry) => Array.isArray(entry) && entry.length >= 2)
                .map((entry) => [Number(entry[1]), Number(entry[0])])
                .filter((entry) => Number.isFinite(entry[0]) && Number.isFinite(entry[1])))
            .filter((line) => line.length > 1);

        if (latLngLines.length === 0) {
            return;
        }

        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
        }

        this.routeLayer = window.L.polyline(latLngLines, {
            color: '#1ea7ff',
            weight: 5,
            opacity: 0.85,
        }).addTo(this.map);

        this.map.fitBounds(this.routeLayer.getBounds().pad(0.12));
    }

    getMapTilesUrl() {
        const fallback = `https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${encodeURIComponent(this.apiKeyValue)}`;
        if (!this.hasTilesUrlValue || !this.tilesUrlValue.trim()) {
            return fallback;
        }

        const key = encodeURIComponent(this.apiKeyValue);
        let url = this.tilesUrlValue.trim();

        url = url.replace('{apiKey}', key);
        url = url.replace(/apiKey=["']?key["']?/i, `apiKey=${key}`);

        if (!/apiKey=/i.test(url)) {
            const separator = url.includes('?') ? '&' : '?';
            url = `${url}${separator}apiKey=${key}`;
        }

        return url;
    }

    escape(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    async ensureLeafletLoaded() {
        if (window.L) {
            return true;
        }

        try {
            if (!document.getElementById('leaflet-css')) {
                const css = document.createElement('link');
                css.id = 'leaflet-css';
                css.rel = 'stylesheet';
                css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(css);
            }

            if (!document.getElementById('leaflet-js')) {
                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.id = 'leaflet-js';
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.async = true;
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            }

            return !!window.L;
        } catch {
            if (this.hasRouteInfoTarget) {
                this.routeInfoTarget.textContent = 'Leaflet failed to load. Check network access to unpkg CDN.';
            }

            return false;
        }
    }
}
