import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['address', 'city', 'country', 'suggestions', 'map', 'status', 'latitudeInput', 'longitudeInput'];

    static values = {
        apiKey: String,
        lat: Number,
        lng: Number,
        tilesUrl: String,
        placesUrl: String,
        routeUrl: String,
    };

    connect() {
        this.map = null;
        this.propertyMarker = null;
        this.poiMarkers = [];
        this.routeLayers = [];
        this.debounceTimer = null;
        this.abortController = null;
        this.documentClickHandler = (event) => {
            if (!this.element.contains(event.target)) {
                this.hideSuggestions();
            }
        };

        this.ensureCoordinateFields();
        this.setupAutocomplete();
        document.addEventListener('click', this.documentClickHandler);

        if (this.hasMapTarget) {
            this.initMap();
        }
    }

    disconnect() {
        document.removeEventListener('click', this.documentClickHandler);
        window.clearTimeout(this.debounceTimer);
        if (this.abortController) {
            this.abortController.abort();
        }
    }

    async initMap() {
        if (!this.hasMapTarget) {
            return;
        }

        if (!this.hasApiKeyValue || !this.apiKeyValue.trim()) {
            if (this.hasStatusTarget) {
                this.statusTarget.textContent = 'Map cannot load: missing Geoapify map API key.';
            }

            return;
        }

        const loaded = await this.loadLeaflet();
        if (!loaded) {
            return;
        }

        this.createMap();
        window.requestAnimationFrame(() => {
            if (this.map) {
                this.map.invalidateSize();
            }
        });
        await this.loadNearbyPlaces();
    }

    async loadLeaflet() {
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
        } catch (error) {
            console.error('Failed to load Leaflet CDN assets', error);
            if (this.hasStatusTarget) {
                this.statusTarget.textContent = 'Leaflet failed to load.';
            }

            return false;
        }
    }

    createMap() {
        const coords = this.getCoordinates();
        const lat = Number.isFinite(coords.lat) ? coords.lat : (this.hasLatValue ? this.latValue : 48.1500327);
        const lng = Number.isFinite(coords.lng) ? coords.lng : (this.hasLngValue ? this.lngValue : 11.5753989);

        this.map = window.L.map(this.mapTarget).setView([lat, lng], 12);
        window.L.tileLayer(this.getTilesUrl(), {
            attribution: 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | <a href="https://openmaptiles.org/" target="_blank">&copy; OpenMapTiles</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap</a> contributors',
            maxZoom: 20,
            id: 'osm-bright',
        })
            .on('tileerror', () => {
                if (this.hasStatusTarget) {
                    this.statusTarget.textContent = 'Map tiles failed to load. Check map key and tile URL.';
                }
            })
            .addTo(this.map);

        this.propertyMarker = window.L.circleMarker([lat, lng], {
            radius: 8,
            color: '#2563eb',
            fillColor: '#2563eb',
            fillOpacity: 0.9,
            weight: 2,
        }).addTo(this.map).bindPopup('Property location');

        this.map.on('click', (event) => {
            this.setCoordinates(event.latlng.lat, event.latlng.lng);
            this.selectAddress({ latitude: event.latlng.lat, longitude: event.latlng.lng }, true);
        });

        if (this.hasStatusTarget) {
            this.statusTarget.textContent = 'Map loaded. Click nearby markers to see route details.';
        }
    }

    async loadNearbyPlaces() {
        if (!this.map) {
            return;
        }

        const coords = this.getCoordinates();
        if (!Number.isFinite(coords.lat) || !Number.isFinite(coords.lng)) {
            return;
        }

        const url = this.hasPlacesUrlValue && this.placesUrlValue.trim()
            ? new URL(this.placesUrlValue, window.location.origin)
            : new URL('https://api.geoapify.com/v2/places');

        if (this.hasPlacesUrlValue && this.placesUrlValue.trim()) {
            url.searchParams.set('lat', String(coords.lat));
            url.searchParams.set('lon', String(coords.lng));
        } else {
            url.searchParams.set('categories', 'tourism.sights,catering.restaurant,entertainment');
            url.searchParams.set('filter', `circle:${coords.lng},${coords.lat},4000`);
            url.searchParams.set('bias', `proximity:${coords.lng},${coords.lat}`);
            url.searchParams.set('limit', '20');
            url.searchParams.set('apiKey', this.apiKeyValue);
        }

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                throw new Error(`Places API returned ${response.status}`);
            }

            const payload = await response.json();
            const features = Array.isArray(payload.features)
                ? payload.features
                : (Array.isArray(payload.items)
                    ? payload.items.map((item) => ({
                        properties: {
                            name: item.name,
                            categories: [item.category],
                        },
                        geometry: {
                            coordinates: [item.longitude, item.latitude],
                        },
                    }))
                    : []);

            this.poiMarkers.forEach((marker) => this.map.removeLayer(marker));
            this.poiMarkers = [];

            for (const feature of features) {
                const properties = feature?.properties || {};
                const coordinates = feature?.geometry?.coordinates || [];
                if (!Array.isArray(coordinates) || coordinates.length < 2) {
                    continue;
                }

                const poiLng = Number(coordinates[0]);
                const poiLat = Number(coordinates[1]);
                if (!Number.isFinite(poiLat) || !Number.isFinite(poiLng)) {
                    continue;
                }

                const category = Array.isArray(properties.categories) ? properties.categories[0] : '';
                const marker = window.L.marker([poiLat, poiLng], { icon: this.createPoiIcon(category) }).addTo(this.map);

                let popup = `<strong>${this.escapeHtml(properties.name || 'POI')}</strong><br>${this.escapeHtml(category || '')}`;
                const route = await this.calculateRoute(poiLat, poiLng);
                if (route) {
                    popup += `<br>${route.distanceKm} km • ${route.durationMinutes} min`;
                    if (route.geometry && Array.isArray(route.geometry.coordinates)) {
                        this.drawRoute(route.geometry);
                    }
                }

                marker.bindPopup(popup);
                this.poiMarkers.push(marker);
            }
        } catch (error) {
            console.error('Failed to load nearby places', error);
        }
    }

    async calculateRoute(toLat, toLng) {
        const coords = this.getCoordinates();
        if (!Number.isFinite(coords.lat) || !Number.isFinite(coords.lng)) {
            return null;
        }

        const url = this.hasRouteUrlValue && this.routeUrlValue.trim()
            ? new URL(this.routeUrlValue, window.location.origin)
            : new URL('https://api.geoapify.com/v1/routing');

        if (this.hasRouteUrlValue && this.routeUrlValue.trim()) {
            url.searchParams.set('fromLat', String(coords.lat));
            url.searchParams.set('fromLon', String(coords.lng));
            url.searchParams.set('toLat', String(toLat));
            url.searchParams.set('toLon', String(toLng));
        } else {
            url.searchParams.set('waypoints', `${coords.lat},${coords.lng}|${toLat},${toLng}`);
            url.searchParams.set('mode', 'drive');
            url.searchParams.set('apiKey', this.apiKeyValue);
        }

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                throw new Error(`Routing API returned ${response.status}`);
            }

            const payload = await response.json();
            if (payload.route) {
                return {
                    distanceKm: payload.route.distanceKm,
                    durationMinutes: payload.route.durationMinutes,
                    geometry: payload.route.geometry || null,
                };
            }

            const feature = Array.isArray(payload.features) ? payload.features[0] : null;
            if (!feature || !feature.properties) {
                return null;
            }

            const distanceMeters = Number(feature.properties.distance || 0);
            const durationSeconds = Number(feature.properties.time || 0);

            return {
                distanceKm: (distanceMeters / 1000).toFixed(1),
                durationMinutes: Math.round(durationSeconds / 60),
                geometry: feature.geometry || null,
            };
        } catch (error) {
            console.error('Failed to calculate route', error);

            return null;
        }
    }

    createPoiIcon(category = '') {
        let color = '#6366f1';
        if (category.startsWith('tourism.sights')) {
            color = '#f59e0b';
        } else if (category.startsWith('catering.restaurant')) {
            color = '#ef4444';
        } else if (category.startsWith('entertainment')) {
            color = '#22c55e';
        }

        return window.L.divIcon({
            className: 'geoapify-poi-icon',
            html: `<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:${color};border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,0.2);"></span>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });
    }

    setupAutocomplete() {
        this.hideSuggestions();
    }

    searchAddress() {
        const query = this.hasAddressTarget ? this.addressTarget.value.trim() : '';
        window.clearTimeout(this.debounceTimer);

        if (query.length < 2 || !this.hasApiKeyValue || !this.apiKeyValue.trim()) {
            this.hideSuggestions();

            return;
        }

        this.debounceTimer = window.setTimeout(async () => {
            if (this.abortController) {
                this.abortController.abort();
            }

            this.abortController = new AbortController();
            const url = new URL('https://api.geoapify.com/v1/geocode/autocomplete');
            url.searchParams.set('text', query);
            url.searchParams.set('limit', '6');
            url.searchParams.set('apiKey', this.apiKeyValue);

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    signal: this.abortController.signal,
                });
                if (!response.ok) {
                    throw new Error(`Autocomplete API returned ${response.status}`);
                }

                const payload = await response.json();
                const results = Array.isArray(payload.results) ? payload.results : [];
                this.displaySuggestions(results.map((item) => ({
                    address: item.address_line1 || item.formatted || '',
                    city: item.city || item.county || '',
                    country: item.country || '',
                    latitude: Number(item.lat),
                    longitude: Number(item.lon),
                    formatted: item.formatted || '',
                })));
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Autocomplete lookup failed', error);
                }
                this.hideSuggestions();
            }
        }, 300);
    }

    displaySuggestions(items) {
        if (!this.hasSuggestionsTarget) {
            return;
        }

        this.suggestionsTarget.innerHTML = '';
        if (!items.length) {
            this.hideSuggestions();

            return;
        }

        const fragment = document.createDocumentFragment();
        items.forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'geo-suggestion';
            button.textContent = item.formatted || item.address || 'Unknown address';
            button.addEventListener('click', () => this.selectAddress(item));
            fragment.appendChild(button);
        });

        this.suggestionsTarget.appendChild(fragment);
        this.suggestionsTarget.classList.add('is-open');
    }

    async selectAddress(item, reverse = false) {
        let location = item;

        if (reverse && Number.isFinite(item.latitude) && Number.isFinite(item.longitude)) {
            try {
                const url = new URL('https://api.geoapify.com/v1/geocode/reverse');
                url.searchParams.set('lat', String(item.latitude));
                url.searchParams.set('lon', String(item.longitude));
                url.searchParams.set('apiKey', this.apiKeyValue);

                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (response.ok) {
                    const payload = await response.json();
                    const first = Array.isArray(payload.results) ? payload.results[0] : null;
                    if (first) {
                        location = {
                            ...location,
                            address: first.address_line1 || first.formatted || location.address || '',
                            city: first.city || first.county || location.city || '',
                            country: first.country || location.country || '',
                        };
                    }
                }
            } catch (error) {
                console.error('Reverse geocoding failed', error);
            }
        }

        if (this.hasAddressTarget && location.address) {
            this.addressTarget.value = location.address;
            this.addressTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (this.hasCityTarget && location.city) {
            this.cityTarget.value = location.city;
            this.cityTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (this.hasCountryTarget && location.country) {
            this.countryTarget.value = location.country;
            this.countryTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (Number.isFinite(location.latitude) && Number.isFinite(location.longitude)) {
            this.setCoordinates(location.latitude, location.longitude);
            if (this.map && this.propertyMarker) {
                this.propertyMarker.setLatLng([location.latitude, location.longitude]);
                this.map.setView([location.latitude, location.longitude], 14);
            }

            await this.loadNearbyPlaces();
        }

        this.hideSuggestions();
    }

    ensureCoordinateFields() {
        if (this.hasLatitudeInputTarget) {
            this.latitudeInputTarget.type = 'hidden';
        }
        if (this.hasLongitudeInputTarget) {
            this.longitudeInputTarget.type = 'hidden';
        }
    }

    hideSuggestions() {
        if (!this.hasSuggestionsTarget) {
            return;
        }

        this.suggestionsTarget.innerHTML = '';
        this.suggestionsTarget.classList.remove('is-open');
    }

    setCoordinates(lat, lng) {
        if (this.hasLatitudeInputTarget) {
            this.latitudeInputTarget.value = String(lat);
        }
        if (this.hasLongitudeInputTarget) {
            this.longitudeInputTarget.value = String(lng);
        }

        if (this.hasStatusTarget) {
            this.statusTarget.textContent = `Selected: ${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}`;
        }
    }

    getCoordinates() {
        const lat = this.hasLatitudeInputTarget
            ? Number.parseFloat(this.latitudeInputTarget.value)
            : (this.hasLatValue ? this.latValue : NaN);
        const lng = this.hasLongitudeInputTarget
            ? Number.parseFloat(this.longitudeInputTarget.value)
            : (this.hasLngValue ? this.lngValue : NaN);

        return { lat, lng };
    }

    drawRoute(geometry) {
        if (!this.map || !geometry || !Array.isArray(geometry.coordinates)) {
            return;
        }

        const coords = geometry.coordinates;
        const isMultiLine = Array.isArray(coords[0]) && Array.isArray(coords[0][0]);
        const lines = isMultiLine ? coords : [coords];
        const latLngs = lines
            .map((line) => line
                .filter((entry) => Array.isArray(entry) && entry.length >= 2)
                .map((entry) => [Number(entry[1]), Number(entry[0])])
                .filter((entry) => Number.isFinite(entry[0]) && Number.isFinite(entry[1])))
            .filter((line) => line.length > 1);

        if (!latLngs.length) {
            return;
        }

        const layer = window.L.polyline(latLngs, {
            color: '#0ea5e9',
            weight: 4,
            opacity: 0.75,
        }).addTo(this.map);

        this.routeLayers.push(layer);
        if (this.routeLayers.length > 3) {
            const old = this.routeLayers.shift();
            if (old) {
                this.map.removeLayer(old);
            }
        }
    }

    getTilesUrl() {
        const fallback = `https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${encodeURIComponent(this.apiKeyValue)}`;
        if (!this.hasTilesUrlValue || !this.tilesUrlValue.trim()) {
            return fallback;
        }

        let url = this.tilesUrlValue.trim();
        if (url.includes('{apiKey}')) {
            url = url.replace('{apiKey}', encodeURIComponent(this.apiKeyValue));
        }

        if (!/apiKey=/i.test(url)) {
            const separator = url.includes('?') ? '&' : '?';
            url = `${url}${separator}apiKey=${encodeURIComponent(this.apiKeyValue)}`;
        }

        return url;
    }

    escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
}
