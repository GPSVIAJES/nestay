import './bootstrap';

import Alpine from 'alpinejs';

import { NestayAPI } from './modules/api';
import { SearchModule } from './modules/search';
import { HotelModule } from './modules/hotel';
import { BookingModule } from './modules/booking';

window.Alpine = Alpine;
window.NestayAPI = NestayAPI;
window.SearchModule = SearchModule;
window.HotelModule = HotelModule;
window.BookingModule = BookingModule;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    // Reveal animations observer
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('vis');
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
});

/**
 * Global Utility Functions
 */
window.formatDate = (d) => {
    if (!d) return '';
    return new Date(d + 'T00:00:00').toLocaleDateString('es-ES', {
        weekday: 'short',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
