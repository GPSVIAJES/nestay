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

/**
 * CUSTOM CURSOR LOGIC
 */
document.addEventListener('DOMContentLoaded', () => {
    const cursor = document.getElementById('cursor');
    const ring = document.getElementById('cursor-ring');

    if (cursor && ring) {
        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
            
            // Subtle delay for the ring
            setTimeout(() => {
                ring.style.left = e.clientX + 'px';
                ring.style.top = e.clientY + 'px';
            }, 50);
        });

        // Hover effects
        const interactiveElements = 'a, button, .stab, .pcard, .rcard, .dcard, .guest-trigger';
        document.querySelectorAll(interactiveElements).forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor.style.transform = 'translate(-50%, -50%) scale(1.5)';
                ring.style.transform = 'translate(-50%, -50%) scale(0.6)';
                ring.style.borderColor = 'var(--t)';
            });
            el.addEventListener('mouseleave', () => {
                cursor.style.transform = 'translate(-50%, -50%) scale(1)';
                ring.style.transform = 'translate(-50%, -50%) scale(1)';
                ring.style.borderColor = 'rgba(224, 122, 95, .5)';
            });
        });
    }

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
