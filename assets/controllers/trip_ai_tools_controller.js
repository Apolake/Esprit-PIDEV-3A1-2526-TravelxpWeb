import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'outputTitle',
        'outputBody',
        'description',
        'tripName',
        'origin',
        'destination',
        'startDate',
        'endDate',
        'status',
        'budget',
        'currency',
        'notes',
    ];

    static values = {
        adminEndpoint: String,
        userEndpoint: String,
        csrfToken: String,
        tripId: Number,
    };

    connect() {
        this.isLoading = false;
    }

    async generateAdmin(event) {
        event.preventDefault();
        if (!this.hasAdminEndpointValue || this.isLoading) {
            return;
        }

        const button = event.currentTarget;
        const tool = String(button.dataset.tool || '').trim();
        if (!tool) {
            return;
        }

        this.setLoadingState(button, true, 'Generating...');
        this.renderOutput('AI Assistant', 'Generating suggestion...');

        try {
            const payload = {
                token: this.csrfTokenValue || '',
                tool,
                tripId: this.hasTripIdValue ? this.tripIdValue : null,
                context: this.collectTripContext(),
            };

            const response = await this.postJson(this.adminEndpointValue, payload);
            if (!response.ok) {
                this.renderOutput('AI Assistant', 'Unable to generate AI output right now.');
                return;
            }

            const data = await response.json();
            const title = typeof data.title === 'string' ? data.title : 'AI Assistant';
            const content = typeof data.content === 'string' ? data.content : '';
            this.renderOutput(title, content || 'No output returned.');

            if (tool === 'description' && this.hasDescriptionTarget && content.trim() !== '') {
                this.descriptionTarget.value = content.trim();
                this.descriptionTarget.dispatchEvent(new Event('input', { bubbles: true }));
            }
        } catch (_) {
            this.renderOutput('AI Assistant', 'Unable to generate AI output right now.');
        } finally {
            this.setLoadingState(button, false);
        }
    }

    async askUser(event) {
        event.preventDefault();
        if (!this.hasUserEndpointValue || this.isLoading) {
            return;
        }

        const button = event.currentTarget;
        const questionKey = String(button.dataset.questionKey || '').trim();
        if (!questionKey) {
            return;
        }

        this.setLoadingState(button, true, 'Thinking...');
        this.renderOutput('Trip Assistant', 'Preparing answer...');

        try {
            const response = await this.postJson(this.userEndpointValue, {
                token: this.csrfTokenValue || '',
                questionKey,
                tripId: this.hasTripIdValue ? this.tripIdValue : null,
            });
            if (!response.ok) {
                this.renderOutput('Trip Assistant', 'Unable to answer right now.');
                return;
            }

            const data = await response.json();
            const title = typeof data.question === 'string' ? data.question : 'Trip Assistant';
            const body = typeof data.answer === 'string' ? data.answer : 'No answer returned.';
            this.renderOutput(title, body);
        } catch (_) {
            this.renderOutput('Trip Assistant', 'Unable to answer right now.');
        } finally {
            this.setLoadingState(button, false);
        }
    }

    collectTripContext() {
        return {
            tripName: this.readTargetValue('tripName'),
            origin: this.readTargetValue('origin'),
            destination: this.readTargetValue('destination'),
            startDate: this.readTargetValue('startDate'),
            endDate: this.readTargetValue('endDate'),
            status: this.readTargetValue('status'),
            budgetAmount: this.readTargetValue('budget'),
            currency: this.readTargetValue('currency'),
            description: this.readTargetValue('description'),
            notes: this.readTargetValue('notes'),
        };
    }

    readTargetValue(targetName) {
        const hasTargetFn = `has${targetName.charAt(0).toUpperCase()}${targetName.slice(1)}Target`;
        const targetRef = `${targetName}Target`;
        if (!this[hasTargetFn] || !this[targetRef]) {
            return '';
        }

        return String(this[targetRef].value || '').trim();
    }

    renderOutput(title, body) {
        if (this.hasOutputTitleTarget) {
            this.outputTitleTarget.textContent = title;
        }
        if (this.hasOutputBodyTarget) {
            this.outputBodyTarget.textContent = body;
        }
    }

    setLoadingState(button, loading, loadingLabel = 'Loading...') {
        this.isLoading = loading;
        if (!button) {
            return;
        }

        if (loading) {
            button.dataset.originalLabel = button.textContent || '';
            button.disabled = true;
            button.textContent = loadingLabel;
            return;
        }

        button.disabled = false;
        if (button.dataset.originalLabel) {
            button.textContent = button.dataset.originalLabel;
        }
    }

    async postJson(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });
    }
}
