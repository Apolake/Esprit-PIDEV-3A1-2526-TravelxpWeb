import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'box',
        'toggleButton',
        'messages',
        'input',
        'status',
        'sendButton',
        'typing',
    ];

    static values = {
        propertyId: Number,
        chatUrl: String,
        open: Boolean,
    };

    connect() {
        this.isLoading = false;
        this.history = [];
        this.isOpen = this.hasOpenValue ? this.openValue : false;
        this.bootstrapHistoryFromDom();
        this.setOpenState(this.isOpen);
        this.scrollToBottom();
    }

    toggle() {
        this.setOpenState(!this.isOpen);
        if (this.isOpen) {
            this.scrollToBottom();
        }
    }

    useSuggestion(event) {
        event.preventDefault();

        if (!event.currentTarget) {
            return;
        }

        const text = event.currentTarget.dataset.suggestionText || '';
        if (!text) {
            return;
        }

        if (this.hasInputTarget) {
            this.inputTarget.value = text;
            this.inputTarget.focus();
        }

        this.sendMessage();
    }

    async sendMessage(event) {
        if (event) {
            event.preventDefault();
        }

        if (this.isLoading) {
            return;
        }

        const message = this.hasInputTarget ? this.inputTarget.value.trim() : '';
        if (!message) {
            this.setStatus('Type a question first.');
            return;
        }

        if (message.length > 500) {
            this.setStatus('Please keep messages under 500 characters.');

            return;
        }

        this.setOpenState(true);

        this.isLoading = true;
        this.toggleControls(true);
        this.showLoading();

        this.displayMessage('user', message);
        if (this.hasInputTarget) {
            this.inputTarget.value = '';
        }

        try {
            const response = await fetch(this.chatUrlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    message,
                    history: this.history.slice(-8),
                }),
            });

            const payload = await response.json();
            let reply = typeof payload.reply === 'string' && payload.reply.trim() !== ''
                ? payload.reply
                : 'I could not generate a recommendation right now.';
            const timestamp = typeof payload.timestamp === 'string' ? payload.timestamp : this.currentTime();

            if (this.isDuplicateAssistantReply(reply)) {
                reply = `${reply} I can also tailor this answer based on your budget or travel style.`;
            }

            this.hideLoading();

            await this.delay(280);

            this.displayMessage('bot', reply, timestamp);
            this.setStatus('');
        } catch (error) {
            this.hideLoading();
            console.error('Chatbot request failed', error);
            this.displayMessage('bot', 'Sorry, I could not reach the recommendation service right now.', this.currentTime());
            this.setStatus('The chatbot is temporarily unavailable.');
        } finally {
            this.isLoading = false;
            this.toggleControls(false);
            this.scrollToBottom();
        }
    }

    displayMessage(role, text, timestamp = null) {
        if (!this.hasMessagesTarget) {
            return null;
        }

        const finalTimestamp = timestamp || this.currentTime();
        const message = document.createElement('div');
        message.className = `chatbot-message ${role}`;
        message.dataset.role = role === 'bot' ? 'assistant' : 'user';
        message.dataset.content = text;
        message.dataset.timestamp = finalTimestamp;
        message.innerHTML = `
            <div class="chatbot-message-text">${this.renderMessageContent(text)}</div>
            <div class="chatbot-message-time">${this.escapeHtml(finalTimestamp)}</div>
        `;
        this.messagesTarget.appendChild(message);

        this.history.push({
            role: role === 'bot' ? 'assistant' : 'user',
            content: text,
            timestamp: finalTimestamp,
        });
        this.history = this.history.slice(-10);

        this.scrollToBottom();

        return message;
    }

    showLoading() {
        if (!this.hasMessagesTarget) {
            return null;
        }

        this.setStatus('AI is typing...');

        if (this.hasTypingTarget) {
            this.typingTarget.remove();
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'chatbot-message bot chatbot-typing-message';
        wrapper.setAttribute('data-chatbot-target', 'typing');

        const typing = document.createElement('span');
        typing.className = 'chatbot-typing';
        typing.setAttribute('aria-label', 'AI is typing');

        const dots = [0, 1, 2].map(() => {
            const dot = document.createElement('span');
            dot.className = 'chatbot-typing-dot';

            return dot;
        });

        dots.forEach((dot) => typing.appendChild(dot));
        wrapper.appendChild(typing);
        this.messagesTarget.appendChild(wrapper);
        this.scrollToBottom();

        return wrapper;
    }

    hideLoading() {
        if (this.hasTypingTarget) {
            this.typingTarget.remove();
        }
    }

    setStatus(message) {
        if (!this.hasStatusTarget) {
            return;
        }

        this.statusTarget.textContent = message;
    }

    toggleControls(disabled) {
        if (this.hasInputTarget) {
            this.inputTarget.disabled = disabled;
        }

        if (this.hasSendButtonTarget) {
            this.sendButtonTarget.disabled = disabled;
        }

        if (this.hasToggleButtonTarget) {
            this.toggleButtonTarget.disabled = disabled && this.isLoading;
        }
    }

    setOpenState(open) {
        this.isOpen = open;

        if (this.hasBoxTarget) {
            this.boxTarget.classList.toggle('is-open', open);
        }

        if (this.hasToggleButtonTarget) {
            this.toggleButtonTarget.setAttribute('aria-expanded', open ? 'true' : 'false');
            this.toggleButtonTarget.textContent = open ? '✕' : '💬';
            this.toggleButtonTarget.setAttribute('title', open ? 'Close chat' : 'Open chat');
        }
    }

    scrollToBottom() {
        if (!this.hasMessagesTarget) {
            return;
        }

        this.messagesTarget.scrollTop = this.messagesTarget.scrollHeight;
    }

    renderMessageContent(text) {
        const escaped = this.escapeHtml(String(text));
        const withBreaks = escaped.replace(/\n/g, '<br>');

        return withBreaks.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    }

    bootstrapHistoryFromDom() {
        if (!this.hasMessagesTarget) {
            return;
        }

        const items = this.messagesTarget.querySelectorAll('.chatbot-message');
        this.history = Array.from(items)
            .map((item) => {
                const role = item.dataset.role || (item.classList.contains('user') ? 'user' : 'assistant');
                const content = (item.dataset.content || item.textContent || '').trim();
                const timestamp = (item.dataset.timestamp || '').trim();

                return { role, content, timestamp };
            })
            .filter((entry) => ['user', 'assistant'].includes(entry.role) && entry.content !== '')
            .slice(-10);
    }

    isDuplicateAssistantReply(candidateReply) {
        const normalizedCandidate = this.normalizeReply(candidateReply);
        if (!normalizedCandidate) {
            return false;
        }

        for (let index = this.history.length - 1; index >= 0; index -= 1) {
            const entry = this.history[index];
            if (entry.role !== 'assistant') {
                continue;
            }

            return this.normalizeReply(entry.content) === normalizedCandidate;
        }

        return false;
    }

    normalizeReply(value) {
        return String(value)
            .toLowerCase()
            .replace(/<[^>]*>/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    currentTime() {
        return new Date().toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    delay(durationMs) {
        return new Promise((resolve) => {
            window.setTimeout(resolve, durationMs);
        });
    }

    escapeHtml(value) {
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
}