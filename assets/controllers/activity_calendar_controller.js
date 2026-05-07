import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        feedUrl: String,
        detailsBaseUrl: String,
    };

    connect() {
        const FullCalendar = window.FullCalendar;
        if (!FullCalendar || !this.feedUrlValue) {
            return;
        }

        this.detailsPanel = this.element.querySelector('[data-calendar-details]');
        this.calendarElement = this.element.querySelector('[data-calendar-root]');

        if (!this.calendarElement) {
            return;
        }

        this.calendar = new FullCalendar.Calendar(this.calendarElement, {
            initialView: 'dayGridMonth',
            height: 'auto',
            firstDay: 1,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek',
            },
            navLinks: true,
            eventDisplay: 'block',
            displayEventTime: false,
            events: async (info, successCallback, failureCallback) => {
                try {
                    const response = await fetch(this.feedUrlValue, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    if (!response.ok) {
                        throw new Error('Failed to fetch calendar data.');
                    }
                    successCallback(await response.json());
                } catch (error) {
                    failureCallback(error);
                }
            },
            dateClick: (arg) => this.showDateSummary(arg.dateStr),
            eventClick: (arg) => this.showEventDetails(arg.event),
            eventContent: () => {
                const dot = document.createElement('span');
                dot.className = 'calendar-event-dot';
                dot.setAttribute('aria-hidden', 'true');

                const wrapper = document.createElement('span');
                wrapper.className = 'calendar-event-marker';
                wrapper.appendChild(dot);

                return { domNodes: [wrapper] };
            },
            eventDidMount: (arg) => {
                arg.el.setAttribute('title', arg.event.title);
                arg.el.setAttribute('aria-label', arg.event.title);
            },
        });

        this.calendar.render();
        this.showCalendarHint();
    }

    disconnect() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }

    showCalendarHint() {
        if (!this.detailsPanel) {
            return;
        }
        this.detailsPanel.innerHTML = `
            <article class="calendar-detail-card">
                <p class="eyebrow">Activity Calendar</p>
                <h3>Select a day or event</h3>
                <p class="muted">Click any date or event to view scheduled activities and details.</p>
            </article>
        `;
    }

    showDateSummary(dateStr) {
        if (!this.detailsPanel || !this.calendar) {
            return;
        }

        const events = this.calendar.getEvents().filter((event) => {
            const datePart = event.start ? event.start.toISOString().slice(0, 10) : '';
            return datePart === dateStr;
        });

        if (events.length === 0) {
            this.detailsPanel.innerHTML = `
                <article class="calendar-detail-card">
                    <p class="eyebrow">Selected Day</p>
                    <h3>${dateStr}</h3>
                    <p class="muted">No activities scheduled for this day.</p>
                </article>
            `;
            return;
        }

        const cards = events
            .map((event) => this.renderEventCard(event))
            .join('');

        this.detailsPanel.innerHTML = `
            <article class="calendar-detail-card">
                <p class="eyebrow">Selected Day</p>
                <h3>${dateStr}</h3>
                <p class="muted">${events.length} activity event(s)</p>
            </article>
            ${cards}
        `;
    }

    showEventDetails(event) {
        if (!this.detailsPanel) {
            return;
        }

        this.detailsPanel.innerHTML = this.renderEventCard(event, true);
    }

    renderEventCard(event, includeDate = false) {
        const details = event.extendedProps || {};
        const status = details.status || 'PLANNED';
        const tripName = details.tripName || '-';
        const type = details.type || 'General';
        const location = details.location || '-';
        const cost = details.cost || '-';
        const participants = Number.isFinite(details.participants) ? details.participants : '-';
        const capacity = Number.isFinite(details.capacity) ? details.capacity : '-';
        const availableSeats = Number.isFinite(details.availableSeats) ? details.availableSeats : '-';
        const startText = event.start ? event.start.toLocaleString() : '-';
        const endText = event.end ? event.end.toLocaleString() : '-';
        const detailsUrl = details.detailsUrl || this.detailsBaseUrlValue || '#';

        return `
            <article class="calendar-detail-card">
                <div class="calendar-detail-head">
                    <h3>${event.title}</h3>
                    <span class="pill status-${String(status).toLowerCase()}">${status}</span>
                </div>
                <p class="muted"><i class="fa-solid fa-route btn-icon" aria-hidden="true"></i>${tripName}</p>
                <p class="muted"><i class="fa-solid fa-tag btn-icon" aria-hidden="true"></i>${type}</p>
                <p class="muted"><i class="fa-solid fa-location-dot btn-icon" aria-hidden="true"></i>${location}</p>
                ${includeDate ? `<p class="muted"><i class="fa-regular fa-clock btn-icon" aria-hidden="true"></i>${startText}${event.end ? ` - ${endText}` : ''}</p>` : ''}
                <div class="calendar-detail-meta">
                    <span><i class="fa-solid fa-wallet btn-icon" aria-hidden="true"></i>${cost}</span>
                    <span><i class="fa-solid fa-users btn-icon" aria-hidden="true"></i>${participants}/${capacity}</span>
                    <span><i class="fa-solid fa-chair btn-icon" aria-hidden="true"></i>${availableSeats} seats left</span>
                </div>
                <a class="btn btn-sm btn-primary" href="${detailsUrl}"><i class="fa-regular fa-eye btn-icon" aria-hidden="true"></i>Open Details</a>
            </article>
        `;
    }
}
