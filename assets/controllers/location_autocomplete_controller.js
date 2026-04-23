import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['origin', 'destination', 'originList', 'destinationList'];
    static values = {
        endpoint: String,
    };

    connect() {
        this.abortController = null;
        this.debounceTimer = null;
    }

    disconnect() {
        if (this.abortController) {
            this.abortController.abort();
            this.abortController = null;
        }
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = null;
        }
    }

    originInput(event) {
        this.scheduleFetch(event.target.value, this.originListTarget);
    }

    destinationInput(event) {
        this.scheduleFetch(event.target.value, this.destinationListTarget);
    }

    scheduleFetch(query, listTarget) {
        const trimmed = String(query || '').trim();
        if (trimmed.length < 1 || !this.endpointValue) {
            this.clearList(listTarget);
            return;
        }

        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }
        this.debounceTimer = setTimeout(() => this.fetchSuggestions(trimmed, listTarget), 220);
    }

    async fetchSuggestions(query, listTarget) {
        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        try {
            const url = new URL(this.endpointValue, window.location.origin);
            url.searchParams.set('q', query);

            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: this.abortController.signal,
            });
            if (!response.ok) {
                this.clearList(listTarget);
                return;
            }

            const data = await response.json();
            this.renderList(Array.isArray(data) ? data : [], listTarget);
        } catch (_) {
            this.clearList(listTarget);
        }
    }

    renderList(items, listTarget) {
        this.clearList(listTarget);
        items.forEach((item) => {
            if (!item || typeof item.value !== 'string') {
                return;
            }

            const option = document.createElement('option');
            option.value = item.value;
            option.label = typeof item.label === 'string' && item.label !== '' ? item.label : item.value;
            listTarget.appendChild(option);
        });
    }

    clearList(listTarget) {
        listTarget.innerHTML = '';
    }
}
