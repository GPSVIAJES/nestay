import { NestayAPI } from './api';

export const SearchModule = {
    state: {
        adults: 2,
        children: 0,
        rooms: 1,
        isOpen: false
    },

    initAutocomplete(inputId, resultsId) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);
        
        if (!input || !results) return;

        let timeout;
        input.addEventListener('input', e => {
            clearTimeout(timeout);
            const query = e.target.value.trim();
            if (query.length < 3) {
                results.style.display = 'none';
                return;
            }

            timeout = setTimeout(async () => {
                const data = await NestayAPI.autocomplete(query);
                this.renderAutocomplete(data, results, input);
            }, 300);
        });

        // Close on click outside
        document.addEventListener('click', e => {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                results.style.display = 'none';
            }
        });
    },

    renderAutocomplete(data, container, input) {
        if (!data?.data?.length) {
            container.style.display = 'none';
            return;
        }

        container.innerHTML = data.data.map(item => `
            <div class="autocomplete-item" data-id="${item.id}" data-type="${item.type}">
                <span class="icon">${item.type === 'hotel' ? '🏨' : '📍'}</span>
                <div class="info">
                    <div class="name">${item.full_name}</div>
                    <div class="type">${item.type}</div>
                </div>
            </div>
        `).join('');

        container.style.display = 'block';

        container.querySelectorAll('.autocomplete-item').forEach(el => {
            el.addEventListener('click', () => {
                input.value = el.querySelector('.name').textContent;
                container.style.display = 'none';
            });
        });
    },

    /**
     * GUEST DROPDOWN LOGIC
     */
    toggleGuestPanel() {
        const panel = document.getElementById('guest-panel');
        const trigger = document.getElementById('guest-trigger');
        const chevron = document.querySelector('.guest-chevron');

        this.state.isOpen = !this.state.isOpen;
        
        if (this.state.isOpen) {
            panel.classList.add('open');
            trigger.classList.add('open');
        } else {
            panel.classList.remove('open');
            trigger.classList.remove('open');
        }
    },

    adjustGuest(type, change) {
        if (type === 'adults') {
            this.state.adults = Math.max(1, this.state.adults + change);
            document.getElementById('gp-adults').textContent = this.state.adults;
            document.getElementById('adults-input').value = this.state.adults;
        } else if (type === 'rooms') {
            this.state.rooms = Math.max(1, this.state.rooms + change);
            document.getElementById('gp-rooms').textContent = this.state.rooms;
            document.getElementById('rooms-input').value = this.state.rooms;
        }

        this.updateSummary();
    },

    updateSummary() {
        const summary = document.getElementById('guest-summary');
        if (summary) {
            summary.textContent = `${this.state.adults} adultos · ${this.state.rooms} habitación`;
        }
    }
};
