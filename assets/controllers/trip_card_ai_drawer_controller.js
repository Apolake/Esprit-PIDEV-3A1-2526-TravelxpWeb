import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'drawer',
        'title',
        'meta',
        'messages',
        'outputTitle',
        'outputBody',
        'questionButton',
        'chatInput',
        'sendButton',
    ];

    connect() {
        this.currentEndpoint = '';
        this.currentToken = '';
        this.currentTripName = '';
        this.currentTripId = 0;
        this.chatHistory = [];
        this.isLoading = false;
        this.boundKeydown = (event) => {
            if (event.key === 'Escape') {
                this.close();
            }
        };
        document.addEventListener('keydown', this.boundKeydown);
    }

    disconnect() {
        document.removeEventListener('keydown', this.boundKeydown);
    }

    openFromCard(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const endpoint = String(button.dataset.aiEndpoint || '').trim();
        const token = String(button.dataset.aiToken || '').trim();
        const tripId = Number(button.dataset.tripId || 0);
        const tripName = String(button.dataset.tripName || 'Trip Assistant').trim();
        const route = String(button.dataset.tripRoute || '').trim();
        const dateRange = String(button.dataset.tripDateRange || '').trim();
        const status = String(button.dataset.tripStatus || '').trim();
        const budget = String(button.dataset.tripBudget || '').trim();

        if (!endpoint || !token || !this.hasDrawerTarget) {
            return;
        }

        this.currentEndpoint = endpoint;
        this.currentToken = token;
        this.currentTripName = tripName;
        this.currentTripId = Number.isFinite(tripId) ? tripId : 0;
        this.chatHistory = [];

        if (this.hasTitleTarget) {
            this.titleTarget.textContent = `AI for ${tripName}`;
        }
        if (this.hasMetaTarget) {
            const metaParts = [route, dateRange, status, budget].filter((part) => part !== '');
            this.metaTarget.textContent = metaParts.join(' | ');
        }

        this.renderOutput('Trip Assistant', 'Select a preset question or ask your own question for this trip.');
        this.clearMessages();
        this.pushMessage('assistant', `You are now chatting about "${tripName}". Ask anything trip-related.`);
        this.drawerTarget.hidden = false;
        this.drawerTarget.classList.add('is-open');
        if (this.hasChatInputTarget) {
            this.chatInputTarget.value = '';
            this.chatInputTarget.focus();
        }
    }

    close(event) {
        if (event) {
            event.preventDefault();
        }
        if (!this.hasDrawerTarget) {
            return;
        }
        this.drawerTarget.classList.remove('is-open');
        this.drawerTarget.hidden = true;
    }

    closeOnBackdrop(event) {
        if (event.target === this.drawerTarget) {
            this.close();
        }
    }

    async askPreset(event) {
        event.preventDefault();
        if (!this.currentEndpoint || this.isLoading) {
            return;
        }

        const button = event.currentTarget;
        const questionKey = String(button.dataset.questionKey || '').trim();
        const questionText = String(button.textContent || '').trim();
        if (!questionKey) {
            return;
        }

        this.pushMessage('user', questionText);
        this.setLoadingState(button, true, 'Thinking...');
        this.renderOutput('Trip Assistant', `Preparing guidance for ${this.currentTripName || 'this trip'}...`);

        try {
            const response = await fetch(this.currentEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    token: this.currentToken,
                    questionKey,
                    tripId: this.currentTripId,
                    history: this.chatHistory,
                }),
            });

            if (!response.ok) {
                let errorMessage = 'Unable to answer right now.';
                try {
                    const errData = await response.json();
                    if (typeof errData.error === 'string' && errData.error.trim() !== '') {
                        errorMessage = errData.error.trim();
                    }
                } catch (_) {
                    // Ignore JSON parse errors for non-json responses.
                }
                this.renderOutput('Trip Assistant', errorMessage);
                this.pushMessage('assistant', `[AI Error] ${errorMessage}`);
                return;
            }

            const data = await response.json();
            const question = typeof data.question === 'string' ? data.question : 'Trip Assistant';
            const answer = typeof data.answer === 'string' ? data.answer : 'No answer returned.';
            this.renderOutput(question, answer);
            this.pushMessage('assistant', answer);
        } catch (_) {
            this.renderOutput('Trip Assistant', 'Unable to answer right now.');
        } finally {
            this.setLoadingState(button, false);
        }
    }

    async sendMessage(event) {
        event.preventDefault();
        if (!this.currentEndpoint || this.isLoading || !this.hasChatInputTarget) {
            return;
        }

        const message = String(this.chatInputTarget.value || '').trim();
        if (!message) {
            return;
        }

        this.chatInputTarget.value = '';
        this.pushMessage('user', message);
        const sendButton = this.hasSendButtonTarget ? this.sendButtonTarget : null;
        this.setLoadingState(sendButton, true, 'Sending...');
        this.renderOutput('Trip Assistant', `Thinking about "${this.currentTripName || 'this trip'}"...`);

        try {
            const response = await fetch(this.currentEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    token: this.currentToken,
                    message,
                    tripId: this.currentTripId,
                    history: this.chatHistory,
                }),
            });

            if (!response.ok) {
                let errorMessage = 'Unable to answer right now.';
                try {
                    const errData = await response.json();
                    if (typeof errData.error === 'string' && errData.error.trim() !== '') {
                        errorMessage = errData.error.trim();
                    }
                } catch (_) {
                    // Ignore JSON parse errors for non-json responses.
                }
                this.renderOutput('Trip Assistant', errorMessage);
                this.pushMessage('assistant', `[AI Error] ${errorMessage}`);
                return;
            }

            const data = await response.json();
            const answer = typeof data.answer === 'string' ? data.answer : 'No answer returned.';
            this.renderOutput(data.question || 'Trip Assistant', answer);
            this.pushMessage('assistant', answer);
        } catch (_) {
            const fallback = 'Unable to answer right now.';
            this.renderOutput('Trip Assistant', fallback);
            this.pushMessage('assistant', fallback);
        } finally {
            this.setLoadingState(sendButton, false);
            if (this.hasChatInputTarget) {
                this.chatInputTarget.focus();
            }
        }
    }

    clearMessages() {
        if (this.hasMessagesTarget) {
            this.messagesTarget.innerHTML = '';
        }
    }

    pushMessage(role, content) {
        const text = String(content || '').trim();
        if (!text) {
            return;
        }

        if (role === 'assistant' || role === 'user') {
            this.chatHistory.push({ role, content: text });
            if (this.chatHistory.length > 16) {
                this.chatHistory = this.chatHistory.slice(-16);
            }
        }

        if (!this.hasMessagesTarget) {
            return;
        }

        const item = document.createElement('article');
        item.className = `trip-ai-message ${role === 'user' ? 'trip-ai-message-user' : 'trip-ai-message-assistant'}`;
        const label = role === 'user' ? 'You' : 'AI';
        item.innerHTML = `<p class="trip-ai-message-label">${label}</p><p class="trip-ai-message-body"></p>`;
        const body = item.querySelector('.trip-ai-message-body');
        if (body) {
            body.textContent = text;
        }
        this.messagesTarget.appendChild(item);
        this.messagesTarget.scrollTop = this.messagesTarget.scrollHeight;
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
}
