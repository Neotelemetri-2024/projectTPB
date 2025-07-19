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
        // Responsive chart height dan font sizes
        const isMobile = window.innerWidth < 768;
        const isTablet = window.innerWidth >= 768 && window.innerWidth < 1024;
        const isDesktop = window.innerWidth >= 1024;
        
        // Chart height yang lebih proporsional
        const chartHeight = isMobile ? 280 : (isTablet ? 380 : 350);
        
        // Font sizes yang konsisten dengan tema aplikasi
        const fontTitle = isMobile ? 13 : (isTablet ? 15 : 16);
        const fontLegend = isMobile ? 11 : (isTablet ? 12 : 13);
        const fontAxis = isMobile ? 11 : (isTablet ? 12 : 13);
        const fontTicks = isMobile ? 9 : (isTablet ? 10 : 11);
        const fontLabels = isMobile ? 9 : (isTablet ? 10 : 11);
        
        // Stack bar chart
        const chartElement = document.getElementById('cplBarChart' + idx);
        if (chartElement && cpl.cpmk_data) {
            chartElement.style.height = chartHeight + 'px';
            chartElement.style.minHeight = chartHeight + 'px';
            
            // Siapkan data untuk stack bar chart
            const labels = cpl.cpmk_data.map(item => item.label);
            const datasets = [];
            
            // Buat dataset untuk setiap komponen dengan urutan stacking yang benar
            const komponenOrder = [5, 4, 3, 2, 1]; // Tugas, TB, UTS, UAS, Kuis (dari atas ke bawah)
            const komponenNames = ['Tugas', 'TB', 'UTS', 'UAS', 'Kuis'];
            const colors = [
                'rgba(139, 92, 246, 0.8)', // Purple - Tugas
                'rgba(16, 185, 129, 0.8)', // Green - TB
                'rgba(245, 158, 11, 0.8)', // Amber - UTS
                'rgba(59, 130, 246, 0.8)', // Blue - UAS
                'rgba(239, 68, 68, 0.8)'  // Red - Kuis
            ];
            
            komponenOrder.forEach((komponenId, index) => {
                const data = cpl.cpmk_data.map(item => item.komponen_nilai[komponenId] || 0);
                
                datasets.push({
                    label: komponenNames[index],
                    data: data,
                    backgroundColor: colors[index],
                    borderColor: 'transparent',
                    borderWidth: 0,
                    stack: 'Stack 0',
                    order: index // Urutan stacking yang konsisten
                });
            });
            
            chartInstances.push(new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: `Komponen Nilai CPMK`,
                            font: { 
                                size: fontTitle,
                                weight: '600',
                                family: 'Inter, system-ui, -apple-system, sans-serif'
                            },
                            color: '#374151',
                            padding: {
                                top: isMobile ? 10 : 15,
                                bottom: isMobile ? 10 : 15
                            }
                        },
                        legend: {
                            position: isMobile ? 'bottom' : 'top',
                            align: 'center',
                            labels: {
                                font: { 
                                    size: fontLegend,
                                    family: 'Inter, system-ui, -apple-system, sans-serif'
                                },
                                padding: isMobile ? 10 : 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: '#6B7280'
                            }
                        },
                        tooltip: {
                            bodyFont: { 
                                size: fontLegend,
                                family: 'Inter, system-ui, -apple-system, sans-serif'
                            },
                            titleFont: { 
                                size: fontLegend,
                                family: 'Inter, system-ui, -apple-system, sans-serif'
                            },
                            padding: isMobile ? 10 : 15,
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            titleColor: '#F9FAFB',
                            bodyColor: '#F9FAFB',
                            borderColor: '#374151',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: isMobile ? 10 : 15,
                            right: isMobile ? 10 : 15,
                            top: isMobile ? 10 : 15,
                            bottom: isMobile ? 10 : 15
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'CPMK',
                                font: { 
                                    size: fontAxis,
                                    weight: '600',
                                    family: 'Inter, system-ui, -apple-system, sans-serif'
                                },
                                color: '#374151',
                                padding: {
                                    top: isMobile ? 6 : 10
                                }
                            },
                            ticks: {
                                font: { 
                                    size: fontTicks,
                                    family: 'Inter, system-ui, -apple-system, sans-serif'
                                },
                                color: '#6B7280',
                                maxRotation: isMobile ? 45 : (isTablet ? 30 : 0),
                                minRotation: isMobile ? 45 : (isTablet ? 30 : 0),
                                padding: isMobile ? 6 : 10,
                                callback: function(value, index, values) {
                                    const label = this.getLabelForValue(value);
                                    // Untuk tablet, potong label jika terlalu panjang
                                    if (isTablet && label.length > 15) {
                                        return label.substring(0, 12) + '...';
                                    }
                                    return label;
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nilai',
                                font: { 
                                    size: fontAxis,
                                    weight: '600',
                                    family: 'Inter, system-ui, -apple-system, sans-serif'
                                },
                                color: '#374151',
                                padding: {
                                    bottom: isMobile ? 6 : 10
                                }
                            },
                            ticks: {
                                font: { 
                                    size: fontTicks,
                                    family: 'Inter, system-ui, -apple-system, sans-serif'
                                },
                                color: '#6B7280',
                                padding: isMobile ? 6 : 10,
                                stepSize: isMobile ? 5 : 10
                            },
                            grid: {
                                color: 'rgba(107, 114, 128, 0.2)',
                                drawBorder: false,
                                lineWidth: 1
                            }
                        }
                    },
                    elements: {
                        bar: {
                            borderRadius: 0, // Hilangkan border radius untuk stacking yang lebih mulus
                            borderSkipped: false
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
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

