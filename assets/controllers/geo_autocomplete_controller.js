import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['addressInput', 'cityInput', 'countryInput', 'postalInput', 'suggestions'];
    static values = {
        endpoint: String,
        minLength: { type: Number, default: 2 },
    };

    connect() {
        this.abortController = null;
        this.debounceTimer = null;
        this.documentClickHandler = this.handleDocumentClick.bind(this);
        document.addEventListener('click', this.documentClickHandler);
    }

    disconnect() {
        document.removeEventListener('click', this.documentClickHandler);
        if (this.abortController) {
            this.abortController.abort();
        }
        window.clearTimeout(this.debounceTimer);
    }

    search() {
        const query = this.addressInputTarget.value.trim();
        if (query.length < this.minLengthValue) {
            this.clearSuggestions();

            return;
        }

        window.clearTimeout(this.debounceTimer);
        this.debounceTimer = window.setTimeout(() => {
            this.fetchSuggestions(query);
        }, 220);
    }

    async fetchSuggestions(query) {
        if (!this.hasEndpointValue) {
            return;
        }

        if (this.abortController) {
            this.abortController.abort();
        }

        this.abortController = new AbortController();

        try {
            const url = new URL(this.endpointValue, window.location.origin);
            url.searchParams.set('q', query);

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: this.abortController.signal,
            });

            if (!response.ok) {
                this.clearSuggestions();

                return;
            }

            const payload = await response.json();
            const items = Array.isArray(payload.items) ? payload.items : [];
            this.renderSuggestions(items);
        } catch {
            this.clearSuggestions();
        }
    }

    renderSuggestions(items) {
        if (!this.hasSuggestionsTarget) {
            return;
        }

        this.suggestionsTarget.innerHTML = '';
        if (items.length === 0) {
            this.suggestionsTarget.classList.remove('is-open');

            return;
        }

        const fragment = document.createDocumentFragment();
        items.forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'geo-suggestion';
            button.textContent = item.formatted || item.address || 'Unknown address';
            button.addEventListener('click', () => this.selectSuggestion(item));
            fragment.appendChild(button);
        });

        this.suggestionsTarget.appendChild(fragment);
        this.suggestionsTarget.classList.add('is-open');
    }

    selectSuggestion(item) {
        if (this.hasAddressInputTarget && item.address) {
            this.addressInputTarget.value = item.address;
            this.addressInputTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (this.hasCityInputTarget && item.city) {
            this.cityInputTarget.value = item.city;
            this.cityInputTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (this.hasCountryInputTarget && item.country) {
            this.countryInputTarget.value = item.country;
            this.countryInputTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (this.hasPostalInputTarget && item.postalCode) {
            this.postalInputTarget.value = item.postalCode;
            this.postalInputTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }

        this.clearSuggestions();
    }

    clearSuggestions() {
        if (!this.hasSuggestionsTarget) {
            return;
        }

        this.suggestionsTarget.innerHTML = '';
        this.suggestionsTarget.classList.remove('is-open');
    }

    handleDocumentClick(event) {
        if (!this.element.contains(event.target)) {
            this.clearSuggestions();
        }
    }
}
