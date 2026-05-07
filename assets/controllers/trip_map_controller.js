import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        centerLat: Number,
        centerLng: Number,
        activities: Array,
        centerLabel: String,
        showCenterMarker: Boolean,
    };

    connect() {
        const root = this.element.querySelector('[data-trip-map-root]');
        if (!root) {
            return;
        }

        if (typeof window.L === 'undefined') {
            root.innerHTML = '<div class="empty-state">Map library failed to load. Refresh the page and try again.</div>';
            return;
        }

        const hasCenter = Number.isFinite(this.centerLatValue) && Number.isFinite(this.centerLngValue);
        if (!hasCenter) {
            root.innerHTML = '<div class="empty-state">Location coordinates are not available for this trip yet.</div>';
            return;
        }

        try {
            this.map = window.L.map(root, {
                zoomControl: true,
            }).setView([this.centerLatValue, this.centerLngValue], 9);

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(this.map);

            const bounds = window.L.latLngBounds([[this.centerLatValue, this.centerLngValue]]);
            const showCenterMarker = this.hasShowCenterMarkerValue ? this.showCenterMarkerValue : true;
            if (showCenterMarker) {
                const tripIcon = window.L.divIcon({
                    className: 'trip-map-marker trip-destination-marker',
                    html: '<i class="fa-solid fa-location-dot" aria-hidden="true"></i>',
                    iconSize: [26, 26],
                    iconAnchor: [13, 26],
                });
                const centerLabel = this.centerLabelValue && this.centerLabelValue.trim() !== ''
                    ? this.centerLabelValue
                    : 'Trip destination';

                window.L.marker([this.centerLatValue, this.centerLngValue], { icon: tripIcon })
                    .addTo(this.map)
                    .bindPopup(`<strong>${centerLabel}</strong>`);
            }

            const activities = Array.isArray(this.activitiesValue) ? this.activitiesValue : [];
            activities.forEach((item) => {
                if (!item || !Number.isFinite(item.lat) || !Number.isFinite(item.lng)) {
                    return;
                }

                const marker = window.L.marker([item.lat, item.lng]).addTo(this.map);
                const title = this.escapeHtml(item.title ?? 'Activity');
                const type = this.escapeHtml(item.type ?? 'General');
                const status = this.escapeHtml(item.status ?? '-');
                const date = this.escapeHtml(item.date ?? '-');
                const time = this.escapeHtml(item.time ?? '');
                const detailsUrl = this.escapeHtml(item.detailsUrl ?? '#');
                const content = `
                    <div class="trip-map-popup">
                        <strong>${title}</strong>
                        <p>${type} | ${status}</p>
                        <p>${date} ${time}</p>
                        <a href="${detailsUrl}">View details</a>
                    </div>
                `;
                marker.bindPopup(content);
                bounds.extend([item.lat, item.lng]);
            });

            if (bounds.isValid()) {
                this.map.fitBounds(bounds.pad(0.2));
            }
        } catch (_) {
            root.innerHTML = '<div class="empty-state">Unable to render map right now.</div>';
        }
    }

    disconnect() {
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    }

    escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
}
