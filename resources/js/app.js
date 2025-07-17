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

let chartInstances = [];

function renderAllCharts() {
    // Destroy all previous chart instances
    chartInstances.forEach(chart => chart.destroy());
    chartInstances = [];
    const cplCpmkData = window.cplCpmkData || [];
    cplCpmkData.forEach((cpl, idx) => {
        // Responsive chart height
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
        // Penyesuaian chartHeight dan font size agar proporsional
        const chartHeight = isMobile ? 210 : (isTablet ? 260 : 320);
        const fontTitle = isMobile ? 14 : (isTablet ? 17 : 20);
        const fontLegend = isMobile ? 11 : (isTablet ? 13 : 16);
        const fontAxis = isMobile ? 11 : (isTablet ? 13 : 16);
        const fontTicks = isMobile ? 9 : (isTablet ? 11 : 14);
        const fontLabel = isMobile ? 9 : (isTablet ? 11 : 13);
        // Bar chart
        const chartElement = document.getElementById('cplBarChart' + idx);
        if (chartElement && cpl.cpmk_labels && cpl.cpmk_nilai) {
            chartElement.style.height = chartHeight + 'px';
            chartElement.style.minHeight = chartHeight + 'px';
            chartInstances.push(new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: cpl.cpmk_labels,
                    datasets: [{
                        label: 'Capaian Mata Kuliah',
                        data: cpl.cpmk_nilai,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: `Bar Capaian CPMK`,
                            font: { size: fontTitle }
                        },
                        legend: {
                            labels: {
                                font: { size: fontLegend }
                            }
                        },
                        tooltip: {
                            bodyFont: { size: fontLegend },
                            titleFont: { size: fontLegend },
                            callbacks: {
                                label: function(context) {
                                    return `Nilai: ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Nilai',
                                font: { size: fontAxis }
                            },
                            ticks: {
                                font: { size: fontTicks }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Indikator CPMK',
                                font: { size: fontAxis }
                            },
                            ticks: {
                                font: { size: fontTicks }
                            }
                        }
                    }
                }
            }));
        }
        // Radar chart
        const radarElement = document.getElementById('cplRadarChart' + idx);
        if (radarElement && cpl.cpmk_labels && cpl.cpmk_nilai) {
            radarElement.style.height = chartHeight + 'px';
            radarElement.style.minHeight = chartHeight + 'px';
            chartInstances.push(new Chart(radarElement, {
                type: 'radar',
                data: {
                    labels: cpl.cpmk_labels,
                    datasets: [{
                        label: 'Capaian Mata Kuliah',
                        data: cpl.cpmk_nilai,   
                        fill: true,
                        backgroundColor: 'rgba(37, 99, 235, 0.15)',
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#2563eb',
                        pointRadius: 3.5,
                        pointHoverRadius: 5.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: `Radar Capaian CPMK`,
                            font: { size: fontTitle }
                        },
                        legend: {
                            labels: {
                                font: { size: fontLegend }
                            }
                        },
                        tooltip: {
                            bodyFont: { size: fontLegend },
                            titleFont: { size: fontLegend },
                            callbacks: {
                                label: function(context) {
                                    return `${context.chart.data.labels[context.dataIndex]}: ${context.parsed.r.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        r: {
                            angleLines: { display: true },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            pointLabels: {
                                display: true,
                                font: { size: fontLabel }
                            },
                            ticks: {
                                stepSize: 20,
                                font: { size: fontTicks }
                            }
                        }
                    },
                    elements: {
                        line: {
                            borderWidth: 2.5
                        }
                    }
                }
            }));
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    renderAllCharts();
});
window.addEventListener('resize', function () {
    renderAllCharts();
});

