import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['map', 'status', 'button'];

    static values = {
        lat: Number,
        lng: Number,
        routeUrl: String,
        placesUrl: String,
        apiKey: String,
        tilesUrl: String,
    };

    connect() {
        this.map = null;
        this.routeLayer = null;
        this.userMarker = null;
        this.propertyMarker = null;
        this.placeMarkers = [];
        this.originalButtonText = null;

        this.initMap();
    }

    async initMap() {
        if (!this.hasMapTarget || this.map) {
            return;
        }

        const loaded = await this.loadLeaflet();
        if (!loaded) {
            this.setStatus('Map failed to load.', true);
            return;
        }

        if (!Number.isFinite(this.latValue) || !Number.isFinite(this.lngValue)) {
            this.setStatus('Property coordinates are unavailable.', true);
            return;
        }

        if (!this.hasApiKeyValue || !String(this.apiKeyValue).trim()) {
            this.setStatus('Map disabled: missing Geoapify API key.', true);
            return;
        }

        this.map = window.L.map(this.mapTarget).setView([this.latValue, this.lngValue], 12);

        window.L.tileLayer(this.getTilesUrl(), {
            attribution: 'Powered by Geoapify | OpenStreetMap contributors',
            maxZoom: 20,
        }).addTo(this.map);

        this.propertyMarker = window.L.marker([this.latValue, this.lngValue], {
            icon: this.createCircleIcon('#ef4444', '#ffffff'),
        })
            .addTo(this.map)
            .bindPopup('Property location');

        window.requestAnimationFrame(() => {
            if (this.map) {
                this.map.invalidateSize();
            }
        });

        this.setStatus('Map ready. Click the button to calculate route and nearby places.');
    }

    async calculateRoute() {
        if (!navigator.geolocation) {
            this.setStatus('Geolocation is not supported by this browser.', true);
            return;
        }

        await this.initMap();
        if (!this.map) {
            return;
        }

        this.setLoading(true);

        try {
            const position = await this.getCurrentPosition();
            const userLat = Number(position.coords.latitude);
            const userLng = Number(position.coords.longitude);

            this.updateUserMarker(userLat, userLng);

            const route = await this.fetchRoute(userLat, userLng, this.latValue, this.lngValue);
            if (!route || !route.geometry) {
                throw new Error('Route data is empty.');
            }

            this.drawRoute(route.geometry);
            this.setStatus(`Route loaded: ${route.distanceKm} km, ${route.durationMinutes} min.`);

            await this.loadNearbyPlaces();
        } catch (error) {
            this.setStatus(this.humanizeError(error), true);
        } finally {
            this.setLoading(false);
        }
    }

    async loadNearbyPlaces() {
        if (!this.hasPlacesUrlValue || !this.placesUrlValue.trim()) {
            return;
        }

        const url = new URL(this.placesUrlValue, window.location.origin);
        url.searchParams.set('lat', String(this.latValue));
        url.searchParams.set('lon', String(this.lngValue));

        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!response.ok) {
            throw new Error(`Nearby places request failed (${response.status}).`);
        }

        const payload = await response.json();
        const items = Array.isArray(payload.items) ? payload.items : [];

        this.placeMarkers.forEach((marker) => this.map.removeLayer(marker));
        this.placeMarkers = [];

        items.forEach((item) => {
            const lat = Number(item.latitude);
            const lng = Number(item.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                return;
            }

            const category = String(item.category || '');
            const marker = window.L.marker([lat, lng], {
                icon: this.createCircleIcon(this.poiColor(category), '#ffffff'),
            }).addTo(this.map);

            const name = this.escapeHtml(String(item.name || 'Nearby place'));
            const address = this.escapeHtml(String(item.address || ''));
            const categoryLabel = this.escapeHtml(category || 'Unknown category');
            marker.bindPopup(`<strong>${name}</strong><br>${categoryLabel}${address ? `<br>${address}` : ''}`);
            this.placeMarkers.push(marker);
        });
    }

    async fetchRoute(fromLat, fromLng, toLat, toLng) {
        if (!this.hasRouteUrlValue || !this.routeUrlValue.trim()) {
            throw new Error('Route endpoint is not configured.');
        }

        const url = new URL(this.routeUrlValue, window.location.origin);
        url.searchParams.set('fromLat', String(fromLat));
        url.searchParams.set('fromLon', String(fromLng));
        url.searchParams.set('toLat', String(toLat));
        url.searchParams.set('toLon', String(toLng));

        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!response.ok) {
            throw new Error(`Route request failed (${response.status}).`);
        }

        const payload = await response.json();
        if (!payload.route) {
            throw new Error('No route was returned by the server.');
        }

        return payload.route;
    }

    drawRoute(geometry) {
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
            this.routeLayer = null;
        }

        this.routeLayer = window.L.geoJSON(geometry, {
            style: { color: '#2563eb', weight: 5, opacity: 0.9 },
        }).addTo(this.map);

        const bounds = this.routeLayer.getBounds();
        if (bounds && bounds.isValid()) {
            this.map.fitBounds(bounds, { padding: [32, 32] });
        }
    }

    updateUserMarker(lat, lng) {
        if (this.userMarker) {
            this.map.removeLayer(this.userMarker);
        }

        this.userMarker = window.L.marker([lat, lng], {
            icon: this.createCircleIcon('#2563eb', '#ffffff'),
        })
            .addTo(this.map)
            .bindPopup('Your location');
    }

    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0,
            });
        });
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
        } catch (_) {
            return false;
        }
    }

    getTilesUrl() {
        if (this.hasTilesUrlValue && this.tilesUrlValue.trim()) {
            return this.tilesUrlValue.replace('{apiKey}', encodeURIComponent(this.apiKeyValue));
        }

        return `https://maps.geoapify.com/v1/tile/carto/{z}/{x}/{y}.png?apiKey=${encodeURIComponent(this.apiKeyValue)}`;
    }

    setStatus(message, isError = false) {
        if (!this.hasStatusTarget) {
            return;
        }

        this.statusTarget.textContent = message;
        this.statusTarget.classList.toggle('is-error', isError);
    }

    setLoading(isLoading) {
        if (!this.hasButtonTarget) {
            return;
        }

        if (!this.originalButtonText) {
            this.originalButtonText = this.buttonTarget.textContent;
        }

        this.buttonTarget.disabled = isLoading;
        this.buttonTarget.textContent = isLoading ? 'Locating and calculating route...' : this.originalButtonText;
    }

    humanizeError(error) {
        if (error && typeof error.code === 'number') {
            if (error.code === 1) {
                return 'Location permission denied. Allow geolocation and try again.';
            }
            if (error.code === 2) {
                return 'Location unavailable. Check your device GPS/network and retry.';
            }
            if (error.code === 3) {
                return 'Location request timed out. Try again.';
            }
        }

        if (error instanceof Error && error.message) {
            return error.message;
        }

        return 'Something went wrong while loading route data.';
    }

    createCircleIcon(color, borderColor) {
        return window.L.divIcon({
            className: 'route-map-marker',
            html: `<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:${color};border:2px solid ${borderColor};box-shadow:0 0 0 1px rgba(0,0,0,0.2);"></span>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });
    }

    poiColor(category) {
        if (category.startsWith('tourism.sights')) {
            return '#f59e0b';
        }
        if (category.startsWith('catering.restaurant')) {
            return '#ef4444';
        }
        if (category.startsWith('entertainment')) {
            return '#22c55e';
        }

        return '#8b5cf6';
    }

    escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value;

        return element.innerHTML;
    }
}
