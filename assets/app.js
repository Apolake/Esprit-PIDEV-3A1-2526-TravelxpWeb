import './stimulus_bootstrap.js';
import './styles/app.css';

const debounce = (callback, delay = 250) => {
    let timer = 0;
    return (...args) => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => callback(...args), delay);
    };
};

function initAdminUserAjaxFilters() {
    const form = document.querySelector('#admin-user-filters');
    const tableContainer = document.querySelector('#admin-users-table');
    if (!form || !tableContainer) {
        return;
    }

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

document.addEventListener('DOMContentLoaded', () => {
    initAdminUserAjaxFilters();
    initCardParallax();
});
