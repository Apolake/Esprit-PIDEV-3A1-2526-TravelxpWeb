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

function initThemeToggle() {
    applyTheme(getPreferredTheme());

    const toggle = document.querySelector('#theme-toggle');

    const syncLabel = () => {
        if (!toggle) {
            return;
        }

        const isLight = document.body.classList.contains('light-theme');
        toggle.textContent = isLight ? '☀️' : '🌙';
        toggle.setAttribute('title', isLight ? 'Switch to dark mode' : 'Switch to light mode');
        toggle.setAttribute('aria-pressed', isLight ? 'true' : 'false');
    };

    syncLabel();
    if (!themeStorageSyncBound) {
        themeStorageSyncBound = true;
        window.addEventListener('storage', (event) => {
            if (event.key !== THEME_KEY || !isValidTheme(event.newValue)) {
                return;
            }

            applyTheme(event.newValue);
            syncLabel();
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
        syncLabel();
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

function bootUI() {
    initThemeToggle();
    initDynamicBackground();
    initAdminUserAjaxFilters();
    initCardParallax();
}

document.addEventListener('DOMContentLoaded', bootUI);
document.addEventListener('turbo:load', bootUI);
