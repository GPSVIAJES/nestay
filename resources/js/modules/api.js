/**
 * Nestay API Client
 * Centralized fetch wrapper for backend Laravel proxy endpoints.
 */

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const fetchWrap = async (endpoint, options = {}) => {
    const url = endpoint.startsWith('/') ? endpoint : `/api/${endpoint}`;
    
    const defaultHeaders = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
    };

    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers,
        },
    };

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            throw {
                status: response.status,
                message: data.message || 'Error en la petición',
                errors: data.errors || null
            };
        }

        return data;
    } catch (error) {
        console.error(`[NestayAPI] Error on ${url}:`, error);
        throw error;
    }
};

export const NestayAPI = {
    // Search & Suggestions
    suggest: (query, language = 'es') => 
        fetchWrap('suggest', { method: 'POST', body: JSON.stringify({ q: query, language }) }),

    searchHotels: (params) => 
        fetchWrap('search-hotels', { method: 'POST', body: JSON.stringify(params) }),

    getHotelDetails: (params) =>
        fetchWrap('hotel-details', { method: 'POST', body: JSON.stringify(params) }),

    // Booking Flow
    prebook: (bookHash) => 
        fetchWrap('prebook', { method: 'POST', body: JSON.stringify({ book_hash: bookHash }) }),

    book: (data) => 
        fetchWrap('book', { method: 'POST', body: JSON.stringify(data) }),

    getBookingStatus: (id) => 
        fetchWrap(`booking-status/${id}`, { method: 'GET' }),

    myBookings: (tab = 'upcoming', perPage = 10) => 
        fetchWrap('my-bookings', { method: 'GET', params: { tab, per_page: perPage } }),

    cancelBooking: (id) => 
        fetchWrap(`bookings/${id}/cancel`, { method: 'DELETE' }),
};
