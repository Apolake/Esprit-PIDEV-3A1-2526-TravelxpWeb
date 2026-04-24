import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'address',
        'city',
        'country',
        'suggestions',
        'map',
        'postal',
        'toggleButton',
        'status',
        'coordinatesOutput',
        'latitudeInput',
        'longitudeInput',
    ];

    static values = {
        apiKey: String,
        lat: Number,
        lng: Number,
        autocompleteUrl: String,
        reverseUrl: String,
        placesUrl: String,
        routingUrl: String,
        tilesUrl: String,
    };

    connect() {
        this.mapInstance = null;
        this.propertyMarker = null;
        this.poiMarkers = [];
        this.mapVisible = false;
        this.autocompleteTimer = null;
        this.inputGeocodeTimer = null;
        this.abortController = null;
        this.isUpdatingFromMap = false;
        this.isUpdatingFromInput = false;
        this.lastAddressCitySignature = '';
        this.boundDocumentClick = (event) => {
            if (!this.element.contains(event.target)) {
                this.hideSuggestions();
            }
        };

        const initialAddress = this.hasAddressTarget ? this.addressTarget.value.trim() : '';
        const initialCity = this.hasCityTarget ? this.cityTarget.value.trim() : '';
        this.lastAddressCitySignature = `${initialAddress.toLowerCase()}|${initialCity.toLowerCase()}`;

        this.syncCoordinatesOutput();
        document.addEventListener('click', this.boundDocumentClick);
    }

    disconnect() {
        document.removeEventListener('click', this.boundDocumentClick);
        window.clearTimeout(this.autocompleteTimer);
        window.clearTimeout(this.inputGeocodeTimer);
        if (this.abortController) {
            this.abortController.abort();
        }
    }

    async initMap() {
        this.mapVisible = !this.mapVisible;
        if (this.hasMapTarget) {
            this.mapTarget.classList.toggle('is-hidden', !this.mapVisible);
        }

        if (this.hasToggleButtonTarget) {
            this.toggleButtonTarget.textContent = this.mapVisible ? 'Hide map picker' : 'Pick location on map';
        }

        if (!this.mapVisible) {
            return;
        }

        const loaded = await this.loadLeaflet();
        if (!loaded) {
            return;
        }

        if (!this.mapInstance) {
            this.createMap();
            await this.loadNearbyPlaces();
        } else {
            this.mapInstance.invalidateSize();
        }
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
            if (this.hasStatusTarget) {
                this.statusTarget.textContent = 'Leaflet failed to load.';
            }

            return false;
        }
    }

    createMap() {
        if (!this.hasLatitudeInputTarget || !this.hasLongitudeInputTarget) {
            return;
        }

        if (!this.hasApiKeyValue || !this.apiKeyValue.trim()) {
            if (this.hasStatusTarget) {
                this.statusTarget.textContent = 'Map picker disabled: missing Geoapify map key.';
            }

            return;
        }

        const lat = Number.parseFloat(this.latitudeInputTarget.value);
        const lng = Number.parseFloat(this.longitudeInputTarget.value);
        const startLat = Number.isFinite(lat) ? lat : (this.hasLatValue ? this.latValue : 48.1500327);
        const startLng = Number.isFinite(lng) ? lng : (this.hasLngValue ? this.lngValue : 11.5753989);

        this.mapInstance = window.L.map(this.mapTarget).setView([startLat, startLng], Number.isFinite(lat) && Number.isFinite(lng) ? 14 : 10);

        window.L.tileLayer(this.getTilesUrl(), {
            attribution: 'Powered by Geoapify | OpenStreetMap contributors',
            maxZoom: 20,
        }).addTo(this.mapInstance);

        this.propertyMarker = window.L.marker([startLat, startLng], { icon: this.createPoiIcon('property') })
            .addTo(this.mapInstance)
            .bindPopup('Property location');

        this.setCoordinates(startLat, startLng, { centerMap: false });

        this.mapInstance.on('click', async (event) => {
            if (this.isUpdatingFromInput) {
                return;
            }

            this.isUpdatingFromMap = true;
            const clickedLat = event.latlng.lat;
            const clickedLng = event.latlng.lng;
            this.setCoordinates(clickedLat, clickedLng, { centerMap: false });

            try {
                await this.selectAddress({
                    latitude: clickedLat,
                    longitude: clickedLng,
                }, true);
            } finally {
                this.isUpdatingFromMap = false;
            }
        });
    }

    handleAddressInput() {
        this.searchAddress();
        this.scheduleMapUpdateFromInputs();
    }

    scheduleMapUpdateFromInputs() {
        window.clearTimeout(this.inputGeocodeTimer);
        this.inputGeocodeTimer = window.setTimeout(() => {
            this.updateMapFromInputs();
        }, 400);
    }

    async updateMapFromInputs() {
        if (this.isUpdatingFromMap || this.isUpdatingFromInput || !this.hasAutocompleteUrlValue || !this.autocompleteUrlValue.trim()) {
            return;
        }

        const address = this.hasAddressTarget ? this.addressTarget.value.trim() : '';
        const city = this.hasCityTarget ? this.cityTarget.value.trim() : '';
        const country = this.hasCountryTarget ? this.countryTarget.value.trim() : '';
        const signature = `${address.toLowerCase()}|${city.toLowerCase()}`;

        if ((!address && !city) || signature === this.lastAddressCitySignature) {
            return;
        }

        const query = [address, city, country].filter(Boolean).join(', ');
        if (query.length < 3) {
            return;
        }

        this.isUpdatingFromInput = true;
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = 'Geocoding address...';
        }

        try {
            const url = new URL(this.autocompleteUrlValue, window.location.origin);
            url.searchParams.set('q', query);

            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                throw new Error('Request failed');
            }

            const data = await response.json();
            const items = Array.isArray(data.items) ? data.items : [];
            if (!items.length) {
                return;
            }

            const first = items[0];
            const latitude = Number(first.latitude);
            const longitude = Number(first.longitude);
            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return;
            }

            this.setCoordinates(latitude, longitude, { centerMap: true });
            this.lastAddressCitySignature = signature;
        } catch (_) {
            if (this.hasStatusTarget) {
                this.statusTarget.textContent = 'Address lookup failed.';
            }
        } finally {
            this.isUpdatingFromInput = false;
        }
    }

    searchAddress() {
        const query = this.hasAddressTarget ? this.addressTarget.value.trim() : '';
        window.clearTimeout(this.autocompleteTimer);

        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }

        this.autocompleteTimer = window.setTimeout(async () => {
            if (!this.hasAutocompleteUrlValue || !this.autocompleteUrlValue.trim()) {
                return;
            }

            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();

            try {
                const url = new URL(this.autocompleteUrlValue, window.location.origin);
                url.searchParams.set('q', query);

                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    signal: this.abortController.signal,
                });

                if (!response.ok) {
                    throw new Error('Autocomplete failed');
                }

                const payload = await response.json();
                this.displaySuggestions(Array.isArray(payload.items) ? payload.items : []);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    this.hideSuggestions();
                }
            }
        }, 260);
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

    async selectAddress(item, fromMapClick = false) {
        let location = item;

        if (fromMapClick && this.hasReverseUrlValue && this.reverseUrlValue.trim()) {
            try {
                const url = new URL(this.reverseUrlValue, window.location.origin);
                url.searchParams.set('lat', String(item.latitude));
                url.searchParams.set('lon', String(item.longitude));
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const payload = await response.json();
                if (payload.item) {
                    location = payload.item;
                }
            } catch (_) {
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
        if (this.hasPostalTarget && location.postalCode) {
            this.postalTarget.value = location.postalCode;
        }

        const address = this.hasAddressTarget ? this.addressTarget.value.trim() : '';
        const city = this.hasCityTarget ? this.cityTarget.value.trim() : '';
        this.lastAddressCitySignature = `${address.toLowerCase()}|${city.toLowerCase()}`;

        if (Number.isFinite(Number(location.latitude)) && Number.isFinite(Number(location.longitude))) {
            this.setCoordinates(Number(location.latitude), Number(location.longitude), { centerMap: !fromMapClick });
        }

        this.hideSuggestions();
        await this.loadNearbyPlaces();
    }

    async loadNearbyPlaces() {
        if (!this.mapInstance || !this.hasPlacesUrlValue || !this.placesUrlValue.trim() || !this.hasLatitudeInputTarget || !this.hasLongitudeInputTarget) {
            return;
        }

        const lat = Number.parseFloat(this.latitudeInputTarget.value);
        const lng = Number.parseFloat(this.longitudeInputTarget.value);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        try {
            const url = new URL(this.placesUrlValue, window.location.origin);
            url.searchParams.set('lat', String(lat));
            url.searchParams.set('lon', String(lng));

            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                throw new Error('Places request failed');
            }

            const payload = await response.json();
            const items = Array.isArray(payload.items) ? payload.items : [];

            this.poiMarkers.forEach((marker) => this.mapInstance.removeLayer(marker));
            this.poiMarkers = [];

            items.forEach((poi) => {
                const poiLat = Number(poi.latitude);
                const poiLng = Number(poi.longitude);
                if (!Number.isFinite(poiLat) || !Number.isFinite(poiLng)) {
                    return;
                }

                const marker = window.L.marker([poiLat, poiLng], {
                    icon: this.createPoiIcon(String(poi.category || '')),
                }).addTo(this.mapInstance);

                marker.bindPopup(`<strong>${this.escapeHtml(poi.name || 'POI')}</strong><br>${this.escapeHtml(poi.category || '')}`);
                this.poiMarkers.push(marker);
            });
        } catch (_) {
        }
    }

    hideSuggestions() {
        if (!this.hasSuggestionsTarget) {
            return;
        }

        this.suggestionsTarget.innerHTML = '';
        this.suggestionsTarget.classList.remove('is-open');
    }

    setCoordinates(latitude, longitude, options = {}) {
        if (!this.hasLatitudeInputTarget || !this.hasLongitudeInputTarget) {
            return;
        }

        const centerMap = options.centerMap !== false;
        const lat = Number(latitude.toFixed(7));
        const lng = Number(longitude.toFixed(7));

        this.latitudeInputTarget.value = String(lat);
        this.longitudeInputTarget.value = String(lng);

        this.latitudeInputTarget.dispatchEvent(new Event('change', { bubbles: true }));
        this.longitudeInputTarget.dispatchEvent(new Event('change', { bubbles: true }));

        if (this.mapInstance && this.propertyMarker) {
            this.propertyMarker.setLatLng([lat, lng]);
            if (centerMap) {
                this.mapInstance.setView([lat, lng], 14);
            }
        }

        this.syncCoordinatesOutput();
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = `Selected: ${lat}, ${lng}`;
        }
    }

    syncCoordinatesOutput() {
        if (!this.hasCoordinatesOutputTarget || !this.hasLatitudeInputTarget || !this.hasLongitudeInputTarget) {
            return;
        }

        const lat = Number.parseFloat(this.latitudeInputTarget.value);
        const lng = Number.parseFloat(this.longitudeInputTarget.value);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            this.coordinatesOutputTarget.textContent = 'Selected coordinates: -';
            return;
        }

        this.coordinatesOutputTarget.textContent = `Selected coordinates: ${lat.toFixed(7)}, ${lng.toFixed(7)}`;
    }

    getTilesUrl() {
        const fallback = `https://maps.geoapify.com/v1/tile/carto/{z}/{x}/{y}.png?apiKey=${encodeURIComponent(this.apiKeyValue)}`;
        if (!this.hasTilesUrlValue || !this.tilesUrlValue.trim()) {
            return fallback;
        }

        let url = this.tilesUrlValue.trim().replace('{apiKey}', encodeURIComponent(this.apiKeyValue));
        if (!/apiKey=/i.test(url)) {
            url += (url.includes('?') ? '&' : '?') + `apiKey=${encodeURIComponent(this.apiKeyValue)}`;
        }

        return url;
    }

    createPoiIcon(category) {
        const color = category.startsWith('tourism.sights')
            ? '#f59e0b'
            : category.startsWith('catering.restaurant')
                ? '#ef4444'
                : category.startsWith('entertainment')
                    ? '#22c55e'
                    : category === 'property'
                        ? '#3b82f6'
                        : '#8b5cf6';

        return window.L.divIcon({
            className: 'poi-marker-icon',
            html: `<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:${color};border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,0.2);"></span>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });
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
