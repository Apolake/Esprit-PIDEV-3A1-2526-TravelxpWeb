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
    toggle.textContent = isLight ? '☀️' : '🌙';
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
            const payload = new URLSearchParams(payloadFactory());
            payload.set('_token', aiToken);
            payload.set('prompt', prompt);

            const response = await fetch(aiUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
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
            if (!params.get('booking[property]')) {
                totalNode.textContent = 'Choose a property and date to preview.';
                return;
            }

            const mapped = new URLSearchParams();
            mapped.set('propertyId', params.get('booking[property]') || '');
            mapped.set('bookingDate', params.get('booking[bookingDate]') || '');
            mapped.set('duration', params.get('booking[duration]') || '1');
            mapped.set('currency', params.get('booking[currency]') || 'USD');
            params.getAll('booking[services][]').forEach((value) => mapped.append('services', value));

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
        }, 180);

        bookingForm.querySelectorAll('input, select, textarea').forEach((field) => {
            field.addEventListener('input', refreshPreview);
            field.addEventListener('change', refreshPreview);
        });

        refreshPreview();

        bindAiPanel(bookingForm, () => {
            const formData = new FormData(bookingForm);
            const params = new URLSearchParams();
            params.set('propertyId', formData.get('booking[property]')?.toString() || '');
            params.set('bookingDate', formData.get('booking[bookingDate]')?.toString() || '');
            params.set('duration', formData.get('booking[duration]')?.toString() || '1');
            params.set('currency', formData.get('booking[currency]')?.toString() || 'USD');
            formData.getAll('booking[services][]').forEach((value) => params.append('services', value.toString()));
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
    initBookingExperience();
}

document.addEventListener('DOMContentLoaded', bootUI);
document.addEventListener('turbo:load', bootUI);
document.addEventListener('turbo:render', bootUI);
