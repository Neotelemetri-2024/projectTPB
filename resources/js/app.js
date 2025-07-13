import './bootstrap';

import Alpine from 'alpinejs';
import Toastify from 'toastify-js';

window.Alpine = Alpine;
window.Toastify = Toastify;

Alpine.start();

// Show toasts for Laravel flash messages
document.addEventListener('DOMContentLoaded', function() {
    // Success messages
    const successMessage = document.querySelector('meta[name="success-message"]');
    if (successMessage) {
        Toastify({
            text: successMessage.getAttribute('content'),
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #10b981, #059669)",
            }
        }).showToast();
    }
    
    // Error messages
    const errorMessage = document.querySelector('meta[name="error-message"]');
    if (errorMessage) {
        Toastify({
            text: errorMessage.getAttribute('content'),
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ef4444, #dc2626)",
            }
        }).showToast();
    }
});

// Helper function to show toast manually
window.showToast = function(message, type = 'success') {
    const backgroundColor = type === 'success' 
        ? "linear-gradient(to right, #10b981, #059669)"
        : "linear-gradient(to right, #ef4444, #dc2626)";
    
    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        style: {
            background: backgroundColor,
        }
    }).showToast();
};
