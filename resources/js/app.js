import './bootstrap';

import Alpine from 'alpinejs';
import Toastify from 'toastify-js';
import Chart from 'chart.js/auto';

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

document.addEventListener('DOMContentLoaded', function () {
    const cplCpmkData = window.cplCpmkData || [];

    // Render bar chart per CPL
    cplCpmkData.forEach((cpl, idx) => {
        const chartElement = document.getElementById('cplBarChart' + idx);
        if (chartElement && cpl.cpmk_labels && cpl.cpmk_nilai) {
            new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: cpl.cpmk_labels,
                    datasets: [{
                        label: 'Capaian Mahasiswa',
                        data: cpl.cpmk_nilai,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: `Grafik Capaian ${cpl.cpl_label}`,
                            font: { size: 18 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Nilai: ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Nilai'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Indikator CPMK'
                            }
                        }
                    }
                }
            });
        }
    });
});

