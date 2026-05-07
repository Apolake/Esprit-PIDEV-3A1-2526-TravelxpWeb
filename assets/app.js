import './stimulus_bootstrap.js';
import './styles/app.css';

const THEME_KEY = 'travelxp-theme';
const THEME_COOKIE = 'travelxp-theme';
let dynamicBackgroundStarted = false;
let themeStorageSyncBound = false;

const debounce = (callback, delay = 250) => {
    let timer = 0;
    return (...args) => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => callback(...args), delay);
    };
};

const isValidTheme = (value) => value === 'dark' || value === 'light';

const getThemeFromCookie = () => {
    try {
        const rawCookie = document.cookie
            .split(';')
            .map((entry) => entry.trim())
            .find((entry) => entry.startsWith(`${THEME_COOKIE}=`));

        if (!rawCookie) {
            return null;
        }

        const value = decodeURIComponent(rawCookie.split('=').slice(1).join('='));

        return isValidTheme(value) ? value : null;
    } catch {
        return null;
    }
};

const getStoredTheme = () => {
    let localTheme = null;
    try {
        localTheme = window.localStorage.getItem(THEME_KEY);
    } catch {
        localTheme = null;
    }

    if (isValidTheme(localTheme)) {
        return localTheme;
    }

    return getThemeFromCookie();
};

const setStoredTheme = (theme) => {
    if (!isValidTheme(theme)) {
        return;
    }

    try {
        window.localStorage.setItem(THEME_KEY, theme);
    } catch {
        // Ignore storage errors and keep runtime theme only.
    }

    try {
        document.cookie = `${THEME_COOKIE}=${encodeURIComponent(theme)}; path=/; max-age=31536000; samesite=lax`;
    } catch {
        // Ignore cookie errors and keep runtime theme only.
    }
};

const getPreferredTheme = () => {
    const stored = getStoredTheme();
    if (isValidTheme(stored)) {
        return stored;
    }

    return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
};

const applyTheme = (theme) => {
    const isLight = theme === 'light';
    document.documentElement.classList.toggle('light-theme', isLight);
    document.documentElement.dataset.theme = theme;
    document.body.classList.toggle('light-theme', isLight);
    document.body.dataset.theme = theme;
    document.dispatchEvent(new CustomEvent('themechange', { detail: { theme } }));
};

const syncThemeToggleLabel = () => {
    const toggle = document.querySelector('#theme-toggle');
    if (!toggle) {
        return;
    }

    const isLight = document.body.classList.contains('light-theme');
            toggle.textContent = isLight ? 'Light' : 'Dark';
    toggle.setAttribute('title', isLight ? 'Switch to dark mode' : 'Switch to light mode');
    toggle.setAttribute('aria-pressed', isLight ? 'true' : 'false');
};

function initThemeToggle() {
    applyTheme(getPreferredTheme());

    const toggle = document.querySelector('#theme-toggle');
    syncThemeToggleLabel();
    if (!themeStorageSyncBound) {
        themeStorageSyncBound = true;
        window.addEventListener('storage', (event) => {
            if (event.key !== THEME_KEY || !isValidTheme(event.newValue)) {
                return;
            }

            applyTheme(event.newValue);
            syncThemeToggleLabel();
        });
    }

    if (!toggle) {
        return;
    }

    if (toggle.dataset.boundThemeToggle === 'true') {
        return;
    }

    toggle.dataset.boundThemeToggle = 'true';
    toggle.addEventListener('click', () => {
        const nextTheme = document.body.classList.contains('light-theme') ? 'dark' : 'light';
        applyTheme(nextTheme);
        setStoredTheme(nextTheme);
        syncThemeToggleLabel();
    });
}

function initDynamicBackground() {
    if (dynamicBackgroundStarted) {
        return;
    }

    const canvas = document.querySelector('#bg-canvas');
    if (!canvas) {
        return;
    }

    const context = canvas.getContext('2d');
    if (!context) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        return;
    }

    const getPalette = () => {
        if (document.body.classList.contains('light-theme')) {
            return ['79,108,218', '32,170,207', '149,86,223'];
        }

        return ['108,139,255', '62,225,255', '169,105,255'];
    };

    let colors = getPalette();
    const pointer = { x: -9999, y: -9999 };
    let particles = [];
    let rafId = 0;
    let width = 0;
    let height = 0;

    const createParticles = () => {
        const count = Math.min(95, Math.max(36, Math.floor((width * height) / 24000)));
        particles = Array.from({ length: count }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.45,
            vy: (Math.random() - 0.5) * 0.45,
            radius: 1 + Math.random() * 2.2,
            colorIndex: Math.floor(Math.random() * colors.length),
            pulse: Math.random() * Math.PI * 2,
        }));
    };

    const resize = () => {
        width = window.innerWidth;
        height = window.innerHeight;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        canvas.width = Math.floor(width * dpr);
        canvas.height = Math.floor(height * dpr);
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        context.setTransform(dpr, 0, 0, dpr, 0, 0);
        createParticles();
    };

    const render = () => {
        context.clearRect(0, 0, width, height);

        for (let i = 0; i < particles.length; i += 1) {
            const p = particles[i];
            p.pulse += 0.02;
            p.x += p.vx;
            p.y += p.vy;

            if (p.x < -20 || p.x > width + 20) {
                p.vx *= -1;
            }
            if (p.y < -20 || p.y > height + 20) {
                p.vy *= -1;
            }

            const dxMouse = pointer.x - p.x;
            const dyMouse = pointer.y - p.y;
            const mouseDist = Math.hypot(dxMouse, dyMouse);
            if (mouseDist < 130) {
                const force = (130 - mouseDist) / 1300;
                p.vx -= (dxMouse / (mouseDist || 1)) * force;
                p.vy -= (dyMouse / (mouseDist || 1)) * force;
            }

            p.vx *= 0.995;
            p.vy *= 0.995;

            const alpha = 0.12 + (Math.sin(p.pulse) + 1) * 0.08;
            const color = colors[p.colorIndex % colors.length];
            context.beginPath();
            context.fillStyle = `rgba(${color}, ${alpha.toFixed(3)})`;
            context.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            context.fill();
        }

        for (let i = 0; i < particles.length; i += 1) {
            const a = particles[i];
            for (let j = i + 1; j < particles.length; j += 1) {
                const b = particles[j];
                const dx = a.x - b.x;
                const dy = a.y - b.y;
                const distance = Math.hypot(dx, dy);
                if (distance < 130) {
                    const opacity = (130 - distance) / 130 * 0.1;
                    context.beginPath();
                    context.strokeStyle = `rgba(139, 167, 255, ${opacity.toFixed(3)})`;
                    context.lineWidth = 1;
                    context.moveTo(a.x, a.y);
                    context.lineTo(b.x, b.y);
                    context.stroke();
                }
            }
        }

        rafId = window.requestAnimationFrame(render);
    };

    window.addEventListener('resize', resize);
    window.addEventListener('mousemove', (event) => {
        pointer.x = event.clientX;
        pointer.y = event.clientY;
    });
    window.addEventListener('mouseleave', () => {
        pointer.x = -9999;
        pointer.y = -9999;
    });

    document.addEventListener('themechange', () => {
        colors = getPalette();
    });

    resize();
    render();
    dynamicBackgroundStarted = true;

    window.addEventListener('beforeunload', () => {
        if (rafId) {
            window.cancelAnimationFrame(rafId);
        }
    });
}

function initAdminUserAjaxFilters() {
    const form = document.querySelector('#admin-user-filters');
    const tableContainer = document.querySelector('#admin-users-table');
    if (!form || !tableContainer) {
        return;
    }

    if (form.dataset.boundAjaxFilters === 'true') {
        return;
    }

    form.dataset.boundAjaxFilters = 'true';

    const updateRows = async () => {
        const query = new URLSearchParams(new FormData(form)).toString();
        const url = `${form.action}?${query}`;
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            return;
        }

        tableContainer.innerHTML = await response.text();
        window.history.replaceState({}, '', url);
    };

    const debouncedUpdate = debounce(updateRows, 220);
    form.querySelectorAll('input, select').forEach((field) => {
        field.addEventListener('input', debouncedUpdate);
        field.addEventListener('change', debouncedUpdate);
    });
}

function initCardParallax() {
    const cards = document.querySelectorAll('.glass-card');
    cards.forEach((card) => {
        if (card.dataset.boundParallax === 'true') {
            return;
        }

        card.dataset.boundParallax = 'true';
        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            card.style.transform = `perspective(1100px) rotateY(${x * 6}deg) rotateX(${y * -6}deg) translateZ(0)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
}

function initGlobalAssistant() {
    const root = document.querySelector('#travelxp-assistant');
    if (!root || root.dataset.boundAssistant === 'true') {
        return;
    }

    const endpoint = (root.dataset.assistantEndpoint || '').trim();
    const csrfToken = (root.dataset.assistantCsrfToken || '').trim();
    const toggle = root.querySelector('[data-assistant-toggle]');
    const panel = root.querySelector('[data-assistant-panel]');
    const close = root.querySelector('[data-assistant-close]');
    const form = root.querySelector('[data-assistant-form]');
    const input = root.querySelector('[data-assistant-input]');
    const submit = root.querySelector('[data-assistant-submit]');
    const messages = root.querySelector('[data-assistant-messages]');

    if (!toggle || !panel || !close || !form || !input || !submit || !messages || endpoint === '' || csrfToken === '') {
        return;
    }

    root.dataset.boundAssistant = 'true';

    const history = [];
    const pushHistory = (role, content) => {
        history.push({ role, content });
        if (history.length > 14) {
            history.splice(0, history.length - 14);
        }
    };

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

    const normalizeHref = (href) => {
        const candidate = String(href || '').trim();
        if (candidate === '') {
            return '';
        }

        if (candidate.startsWith('/') && !candidate.startsWith('//')) {
            return candidate;
        }

        try {
            const parsed = new URL(candidate);
            if (parsed.protocol === 'http:' || parsed.protocol === 'https:') {
                return parsed.toString();
            }
        } catch {
            return '';
        }

        return '';
    };

    const buildAnchor = (href, label) => {
        const safeHref = normalizeHref(href);
        const safeLabel = escapeHtml(label);
        if (safeHref === '') {
            return safeLabel;
        }

        return `<a href="${escapeHtml(safeHref)}" data-turbo="false">${safeLabel}</a>`;
    };

    const formatAssistantMessage = (value) => {
        let rendered = escapeHtml(value);
        const linkPlaceholders = [];

        rendered = rendered.replace(/\[([^\]\n]+)\]\(([^)\s]+)\)/g, (_match, label, href) => {
            const token = `__ASSISTANT_LINK_${linkPlaceholders.length}__`;
            linkPlaceholders.push({
                token,
                html: buildAnchor(href, label),
            });

            return token;
        });

        rendered = rendered.replace(
            /(^|[\s(])(https?:\/\/[^\s<)]+|\/[A-Za-z0-9\-._~:/?#[\]@!$&'()*+,;=%]+)/g,
            (_match, prefix, rawUrl) => {
                if (rawUrl.startsWith('//')) {
                    return `${prefix}${escapeHtml(rawUrl)}`;
                }

                let url = rawUrl;
                let trailing = '';
                const trailingPunctuation = url.match(/[.,!?;:]+$/);
                if (trailingPunctuation) {
                    trailing = trailingPunctuation[0];
                    url = url.slice(0, -trailing.length);
                }

                const linked = buildAnchor(url, url);
                return `${prefix}${linked}${escapeHtml(trailing)}`;
            }
        );

        rendered = rendered.replace(/\*\*([^*\n][^*]*?)\*\*/g, '<strong>$1</strong>');
        rendered = rendered.replace(/\n/g, '<br>');

        for (const item of linkPlaceholders) {
            rendered = rendered.replace(item.token, item.html);
        }

        return rendered;
    };

    const appendMessage = (role, content) => {
        const message = document.createElement('article');
        message.classList.add('assistant-message');
        message.classList.add(role === 'assistant' ? 'assistant-message-bot' : 'assistant-message-user');
        message.innerHTML = formatAssistantMessage(content);
        messages.appendChild(message);
        messages.scrollTop = messages.scrollHeight;
        return message;
    };

    const openPanel = () => {
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        root.classList.add('assistant-open');
        input.focus();
    };

    const closePanel = () => {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        root.classList.remove('assistant-open');
    };

    toggle.addEventListener('click', () => {
        if (panel.hidden) {
            openPanel();
            return;
        }

        closePanel();
    });

    close.addEventListener('click', closePanel);

    const sendMessage = async () => {
        const message = input.value.trim();
        if (message === '') {
            return;
        }

        appendMessage('user', message);
        pushHistory('user', message);
        input.value = '';

        const typing = appendMessage('assistant', 'Thinking...');
        submit.disabled = true;
        input.disabled = true;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-Token': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message,
                    history: history.slice(-10),
                }),
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(typeof payload.error === 'string' ? payload.error : 'Assistant request failed.');
            }

            const reply = typeof payload.reply === 'string' && payload.reply.trim() !== ''
                ? payload.reply.trim()
                : 'I could not generate an answer right now. Please try again.';

            typing.remove();
            appendMessage('assistant', reply);
            pushHistory('assistant', reply);
        } catch (error) {
            const failure = error instanceof Error ? error.message : 'Assistant request failed.';
            typing.remove();
            appendMessage('assistant', failure);
        } finally {
            submit.disabled = false;
            input.disabled = false;
            input.focus();
        }
    };

    submit.addEventListener('click', (event) => {
        event.preventDefault();
        void sendMessage();
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            void sendMessage();
        }
    });
}

function createToast(message, kind = 'info') {
    const text = String(message || '').trim();
    if (text === '') {
        return;
    }

    let holder = document.querySelector('.toast-stack');
    if (!(holder instanceof HTMLElement)) {
        holder = document.createElement('div');
        holder.className = 'toast-stack';
        document.body.appendChild(holder);
    }

    const toast = document.createElement('div');
    toast.className = `toast-item toast-${kind}`;
    toast.textContent = text;
    holder.appendChild(toast);

    window.setTimeout(() => {
        toast.classList.add('hide');
        window.setTimeout(() => toast.remove(), 260);
    }, 3400);
}

function initLiveBlogSearch() {
    const input = document.querySelector('[data-live-search="true"]');
    if (!(input instanceof HTMLInputElement) || input.dataset.boundLiveSearch === 'true') {
        return;
    }

    const endpoint = input.dataset.liveSearchUrl;
    const targetSelector = input.dataset.liveSearchTarget;
    if (!endpoint || !targetSelector) {
        return;
    }

    const target = document.querySelector(targetSelector);
    if (!(target instanceof HTMLElement)) {
        return;
    }

    const loadingSelector = input.dataset.liveSearchLoading || '';
    const loadingNode = loadingSelector ? document.querySelector(loadingSelector) : null;

    const renderSuggestions = (suggestions) => {
        target.innerHTML = '';

        if (!Array.isArray(suggestions) || suggestions.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'suggestion-item muted';
            empty.textContent = 'No results';
            target.appendChild(empty);
            target.hidden = false;
            return;
        }

        suggestions.forEach((entry) => {
            if (!entry || typeof entry.id !== 'number') {
                return;
            }

            const a = document.createElement('a');
            a.className = 'suggestion-item';
            a.href = `/blogs/${entry.id}`;

            const strong = document.createElement('strong');
            strong.textContent = entry.title || '';

            const span = document.createElement('span');
            span.textContent = entry.excerpt || '';

            a.appendChild(strong);
            a.appendChild(span);
            target.appendChild(a);
        });

        target.hidden = false;
    };

    const runSearch = debounce(async () => {
        const value = input.value.trim();
        if (value.length < 2) {
            target.hidden = true;
            target.innerHTML = '';
            return;
        }

        if (loadingNode instanceof HTMLElement) {
            loadingNode.hidden = false;
        }

        try {
            const response = await fetch(`${endpoint}?q=${encodeURIComponent(value)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            renderSuggestions(payload.suggestions || []);
        } finally {
            if (loadingNode instanceof HTMLElement) {
                loadingNode.hidden = true;
            }
        }
    }, 260);

    input.addEventListener('input', runSearch);
    input.addEventListener('focus', runSearch);
    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Node)) {
            return;
        }

        if (!target.contains(event.target) && event.target !== input) {
            target.hidden = true;
        }
    });

    input.dataset.boundLiveSearch = 'true';
}

function initGrammarTools() {
    const buttons = document.querySelectorAll('[data-grammar-btn="true"]');
    buttons.forEach((button) => {
        if (!(button instanceof HTMLButtonElement) || button.dataset.boundGrammar === 'true') {
            return;
        }

        button.dataset.boundGrammar = 'true';
        button.addEventListener('click', async () => {
            const endpoint = button.dataset.grammarUrl;
            const targetSelector = button.dataset.grammarTarget;
            const languageSelector = button.dataset.grammarLanguage;
            const csrfToken = button.dataset.grammarToken || '';
            if (!endpoint || !targetSelector) {
                return;
            }

            const textarea = document.querySelector(targetSelector);
            if (!(textarea instanceof HTMLTextAreaElement)) {
                return;
            }

            const languageNode = languageSelector ? document.querySelector(languageSelector) : null;
            const language = languageNode instanceof HTMLSelectElement ? languageNode.value : 'en-US';
            const currentText = textarea.value;
            if (currentText.trim() === '') {
                createToast('Write text first before grammar correction.', 'warning');
                return;
            }

            button.disabled = true;
            const originalLabel = button.textContent;
            button.textContent = 'Fixing...';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        text: currentText,
                        language,
                        _token: csrfToken,
                    }),
                });

                const payload = await response.json();
                if (!response.ok) {
                    createToast(payload.message || 'Grammar service failed.', 'danger');
                    return;
                }

                if (payload.changed && typeof payload.correctedText === 'string') {
                    const shouldApply = window.confirm('Replace your draft with corrected version?');
                    if (shouldApply) {
                        textarea.value = payload.correctedText;
                        createToast('Grammar correction applied.', 'success');
                    }
                } else {
                    createToast(payload.message || 'No changes suggested.', 'info');
                }
            } catch {
                createToast('Grammar service is unavailable right now.', 'danger');
            } finally {
                button.disabled = false;
                button.textContent = originalLabel || 'Fix Grammar';
            }
        });
    });
}

function initTranslationTools() {
    const buttons = document.querySelectorAll('[data-translate-btn="true"]');
    buttons.forEach((button) => {
        if (!(button instanceof HTMLButtonElement) || button.dataset.boundTranslate === 'true') {
            return;
        }

        button.dataset.boundTranslate = 'true';
        button.addEventListener('click', () => {
            const panelSelector = button.dataset.translateTarget;
            const panel = panelSelector ? document.querySelector(panelSelector) : null;
            if (!(panel instanceof HTMLElement)) {
                return;
            }

            panel.hidden = false;
            panel.dataset.translateUrl = button.dataset.translateUrl || '';
            panel.dataset.translateToken = button.dataset.translateToken || '';
            panel.dataset.translateOriginal = button.dataset.translateOriginal || '';
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            const originalTextNode = panel.querySelector('[data-translate-original-text="true"]');
            if (originalTextNode instanceof HTMLElement) {
                originalTextNode.textContent = button.dataset.translateOriginal || '';
            }

            const resultNode = panel.querySelector('[data-translate-result="true"]');
            if (resultNode instanceof HTMLElement) {
                resultNode.textContent = 'Choose a language and click Translate now.';
            }
        });
    });

    const runButtons = document.querySelectorAll('[data-translate-run="true"]');
    runButtons.forEach((button) => {
        if (!(button instanceof HTMLButtonElement) || button.dataset.boundTranslateRun === 'true') {
            return;
        }

        button.dataset.boundTranslateRun = 'true';
        button.addEventListener('click', async () => {
            const panel = button.closest('.tool-panel');
            if (!(panel instanceof HTMLElement)) {
                return;
            }

            const endpoint = panel.dataset.translateUrl || '';
            const csrfToken = panel.dataset.translateToken || '';
            const languageNode = panel.querySelector('[data-translate-language="true"]');
            const resultNode = panel.querySelector('[data-translate-result="true"]');

            if (!endpoint || !(languageNode instanceof HTMLSelectElement) || !(resultNode instanceof HTMLElement)) {
                return;
            }

            button.disabled = true;
            const label = button.textContent;
            button.textContent = 'Translating...';
            resultNode.textContent = 'Loading...';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        target: languageNode.value,
                        _token: csrfToken,
                    }),
                });
                const payload = await response.json();
                if (!response.ok) {
                    resultNode.textContent = payload.message || 'Translation failed.';
                    createToast('Translation failed.', 'danger');
                    return;
                }

                resultNode.textContent = payload.translatedText || 'No translated text returned.';
                if (payload.error) {
                    createToast(payload.error, 'warning');
                } else {
                    createToast('Translation ready.', 'success');
                }
            } catch {
                resultNode.textContent = 'Translation service is unavailable right now.';
                createToast('Translation service is unavailable right now.', 'danger');
            } finally {
                button.disabled = false;
                button.textContent = label || 'Translate now';
            }
        });
    });
}

function initAiSummarizer() {
    const buttons = document.querySelectorAll('[data-ai-summarize-btn="true"]');
    buttons.forEach((button) => {
        if (!(button instanceof HTMLButtonElement) || button.dataset.boundSummarize === 'true') {
            return;
        }

        button.dataset.boundSummarize = 'true';
        button.addEventListener('click', async () => {
            const endpoint = button.dataset.aiSummarizeUrl;
            const targetSelector = button.dataset.aiSummarizeTarget;
            const csrfToken = button.dataset.aiSummarizeToken || '';
            const target = targetSelector ? document.querySelector(targetSelector) : null;
            if (!endpoint || !(target instanceof HTMLElement)) {
                return;
            }

            target.hidden = false;
            const renderSummaryState = (message, isMuted = false) => {
                target.innerHTML = '';
                const heading = document.createElement('h3');
                heading.textContent = 'AI Summary';
                const paragraph = document.createElement('p');
                paragraph.textContent = message;
                if (isMuted) {
                    paragraph.classList.add('muted');
                }
                target.appendChild(heading);
                target.appendChild(paragraph);
            };

            const renderSummary = (summaryText) => {
                target.innerHTML = '';
                const heading = document.createElement('h3');
                heading.textContent = 'AI Summary';
                target.appendChild(heading);

                const lines = String(summaryText || 'No summary available.').split(/\r?\n/);
                lines.forEach((line) => {
                    const p = document.createElement('p');
                    p.textContent = line;
                    target.appendChild(p);
                });
            };

            renderSummaryState('Generating summary...', true);
            button.disabled = true;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({ _token: csrfToken }),
                });

                const payload = await response.json();
                if (!response.ok) {
                    renderSummaryState(payload.message || 'Unable to generate summary.', true);
                    createToast('AI summarize failed.', 'danger');
                    return;
                }

                renderSummary(payload.summary || 'No summary available.');
                createToast('Summary generated.', 'success');
            } catch {
                renderSummaryState('AI service is unavailable right now.', true);
                createToast('AI service is unavailable right now.', 'danger');
            } finally {
                button.disabled = false;
            }
        });
    });
}

function initNotificationMenu() {
    const menu = document.querySelector('[data-notification-menu]');
    if (!menu) {
        return;
    }

    const toggle = menu.querySelector('[data-notification-toggle]');
    const dropdown = menu.querySelector('[data-notification-dropdown]');
    if (!toggle || !dropdown) {
        return;
    }

    if (menu.dataset.boundNotificationMenu === 'true') {
        return;
    }

    menu.dataset.boundNotificationMenu = 'true';
    const closeMenu = () => {
        dropdown.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', (event) => {
        event.preventDefault();
        const isOpen = !dropdown.hidden;
        if (isOpen) {
            closeMenu();
            return;
        }

        dropdown.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
    });

    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target)) {
            closeMenu();
        }
    });
}

function initBookingExperience() {
    const bookingForm = document.querySelector('#booking-form-enhanced');
    const existingAiForm = document.querySelector('#booking-ai-existing-form');

    const bindAiPanel = (container, payloadFactory) => {
        if (!container || container.dataset.boundBookingAi === 'true') {
            return;
        }

        container.dataset.boundBookingAi = 'true';
        const submitButton = container.querySelector('[data-booking-ai-submit]');
        const promptField = container.querySelector('[data-booking-ai-prompt]');
        const responseNode = container.querySelector('[data-booking-ai-response]');
        const providerNode = container.querySelector('[data-booking-ai-provider]');
        const aiUrl = container.dataset.aiUrl;
        const aiToken = container.dataset.aiToken;

        if (!submitButton || !promptField || !responseNode || !providerNode || !aiUrl || !aiToken) {
            return;
        }

        submitButton.addEventListener('click', async () => {
            const prompt = promptField.value.trim();
            if (!prompt) {
                responseNode.textContent = 'Enter a question first.';
                return;
            }

            responseNode.textContent = 'Thinking...';
            submitButton.disabled = true;

            try {
                const payload = new URLSearchParams(payloadFactory());
                payload.set('_token', aiToken);
                payload.set('prompt', prompt);

                const response = await fetch(aiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: payload.toString(),
                });
                const result = await response.json();

                if (!response.ok) {
                    responseNode.textContent = result.error || 'AI assistance is unavailable right now.';
                    return;
                }

                providerNode.textContent = result.provider || 'TravelXP assistant';
                responseNode.textContent = result.answer || 'No advice returned.';
            } catch {
                responseNode.textContent = 'AI assistance is unavailable right now.';
            } finally {
                submitButton.disabled = false;
            }
        });
    };

    if (bookingForm && bookingForm.dataset.boundBookingPreview !== 'true') {
        bookingForm.dataset.boundBookingPreview = 'true';
        const previewUrl = bookingForm.dataset.previewUrl;
        const totalNode = document.querySelector('[data-preview-total]');
        const seasonNode = document.querySelector('[data-preview-season]');
        const timingNode = document.querySelector('[data-preview-timing]');
        const servicesNode = document.querySelector('[data-preview-services]');
        const offerNode = document.querySelector('[data-preview-offer]');
        const narrativeNode = document.querySelector('[data-preview-narrative]');

        const collectFormPayload = () => {
            const formData = new FormData(bookingForm);
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value !== '') {
                    params.append(key, value.toString());
                }
            }

            return params;
        };

        const refreshPreview = debounce(async () => {
            if (!previewUrl || !totalNode || !seasonNode || !timingNode || !servicesNode || !offerNode || !narrativeNode) {
                return;
            }

            const params = collectFormPayload();
            const propertyId = params.get('booking[property]') || bookingForm.dataset.propertyId || '';
            if (!propertyId) {
                totalNode.textContent = 'Choose a property and date to preview.';
                return;
            }

            const mapped = new URLSearchParams();
            mapped.set('propertyId', propertyId);
            mapped.set('bookingDate', params.get('booking[bookingDate]') || '');
            mapped.set('duration', params.get('booking[duration]') || '1');
            mapped.set('currency', params.get('booking[currency]') || 'USD');
            params.getAll('booking[services][]').forEach((value) => mapped.append('services[]', value));

            try {
                const response = await fetch(`${previewUrl}?${mapped.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const result = await response.json();

                if (!response.ok) {
                    totalNode.textContent = result.error || 'Pricing preview unavailable.';
                    return;
                }

                const snapshot = result.snapshot || {};
                totalNode.textContent = result.formattedConvertedTotal || 'Unavailable';
                seasonNode.textContent = snapshot.seasonalLabel || 'Standard season rate';
                timingNode.textContent = snapshot.timingLabel || 'Standard booking window';
                servicesNode.textContent = `$${Number(snapshot.serviceTotal || 0).toFixed(2)}`;
                offerNode.textContent = Number(snapshot.offerDiscountPercent || 0) > 0
                    ? `${Number(snapshot.offerDiscountPercent).toFixed(2)}% off`
                    : 'No active offer applied';
                narrativeNode.textContent = snapshot.narrative || '';
            } catch {
                totalNode.textContent = 'Pricing preview unavailable.';
            }
        }, 180);

        bookingForm.querySelectorAll('input, select, textarea').forEach((field) => {
            field.addEventListener('input', refreshPreview);
            field.addEventListener('change', refreshPreview);
        });
        refreshPreview();

        bindAiPanel(bookingForm, () => {
            const formData = new FormData(bookingForm);
            const params = new URLSearchParams();
            params.set('propertyId', formData.get('booking[property]')?.toString() || bookingForm.dataset.propertyId || '');
            params.set('bookingDate', formData.get('booking[bookingDate]')?.toString() || '');
            params.set('duration', formData.get('booking[duration]')?.toString() || '1');
            params.set('currency', formData.get('booking[currency]')?.toString() || 'USD');
            formData.getAll('booking[services][]').forEach((value) => params.append('services[]', value.toString()));
            return params;
        });
    }

    bindAiPanel(existingAiForm, () => {
        const params = new URLSearchParams();
        if (existingAiForm?.dataset.bookingId) {
            params.set('bookingId', existingAiForm.dataset.bookingId);
        }

        return params;
    });
}

function bootUI() {
    initThemeToggle();
    initDynamicBackground();
    initAdminUserAjaxFilters();
    initCardParallax();
    initGlobalAssistant();
    initLiveBlogSearch();
    initGrammarTools();
    initTranslationTools();
    initAiSummarizer();
    initNotificationMenu();
    initBookingExperience();
}

document.addEventListener('DOMContentLoaded', bootUI);
document.addEventListener('turbo:load', bootUI);
document.addEventListener('turbo:render', bootUI);
