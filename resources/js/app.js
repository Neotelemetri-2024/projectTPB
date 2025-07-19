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

function renderCplDistribusiBarChart() {
    const cplCpmkData = window.cplCpmkData || [];
    const chartElement = document.getElementById('cplDistribusiBarChart');
    if (!chartElement || cplCpmkData.length === 0) return;

    // Siapkan data: label = CPL, stack = CPMK
    const cplLabels = cplCpmkData.map(cpl => cpl.cpl_label);
    // Kumpulkan semua label CPMK unik dari seluruh CPL
    const allCpmkLabels = Array.from(new Set(cplCpmkData.flatMap(cpl => cpl.cpmk_data.map(cpmk => cpmk.label))));
    // Siapkan dataset: satu dataset per CPMK, data per CPL (proporsi nilai CPMK terhadap nilai CPL, total bar CPL = nilai_cpl)
    const datasets = allCpmkLabels.map((cpmkLabel, idx) => {
        const data = cplCpmkData.map(cpl => {
            const cpmk = cpl.cpmk_data.find(c => c.label === cpmkLabel);
            // Proporsi nilai CPMK terhadap total nilai CPL
            if (cpmk && cpl.total_nilai_cpl > 0) {
                return (cpmk.total_nilai / cpl.total_nilai_cpl) * cpl.nilai_cpl;
            }
            return 0;
        });
        const colors = [
            'rgba(239, 68, 68, 0.8)', // Red
            'rgba(59, 130, 246, 0.8)', // Blue
            'rgba(245, 158, 11, 0.8)', // Amber
            'rgba(16, 185, 129, 0.8)', // Green
            'rgba(139, 92, 246, 0.8)', // Purple
            'rgba(251, 191, 36, 0.8)', // Yellow
            'rgba(34, 197, 94, 0.8)', // Emerald
            'rgba(236, 72, 153, 0.8)', // Pink
            'rgba(14, 165, 233, 0.8)', // Sky
            'rgba(168, 85, 247, 0.8)', // Violet
            'rgba(251, 113, 133, 0.8)' // Rose
        ];
        return {
            label: cpmkLabel,
            data: data,
            backgroundColor: colors[idx % colors.length],
            borderColor: 'transparent',
            borderWidth: 0,
            stack: 'Stack 0',
            order: idx
        };
    });
    // Sumbu Y tetap satuan 0-100, tooltip tampilkan satuan (tanpa %)

    if (window.cplDistribusiChartInstance) {
        window.cplDistribusiChartInstance.destroy();
    }
    window.cplDistribusiChartInstance = new Chart(chartElement, {
        type: 'bar',
        data: {
            labels: cplLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Nilai CPL (Stack Bar)',
                    font: { size: 15, weight: '600', family: 'Inter, system-ui, -apple-system, sans-serif' },
                    color: '#374151',
                    padding: { top: 10, bottom: 10 }
                },
                legend: {
                    display: false // Sembunyikan legend
                },
                tooltip: {
                    bodyFont: { size: 12, family: 'Inter, system-ui, -apple-system, sans-serif' },
                    titleFont: { size: 12, family: 'Inter, system-ui, -apple-system, sans-serif' },
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
                padding: { left: 10, right: 10, top: 10, bottom: 10 }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'CPL',
                        font: { size: 13, weight: '600', family: 'Inter, system-ui, -apple-system, sans-serif' },
                        color: '#374151',
                        padding: { top: 6 }
                    },
                    ticks: {
                        font: { size: 11, family: 'Inter, system-ui, -apple-system, sans-serif' },
                        color: '#6B7280',
                        maxRotation: 0,
                        minRotation: 0,
                        padding: 6
                    },
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Nilai',
                        font: { size: 13, weight: '600', family: 'Inter, system-ui, -apple-system, sans-serif' },
                        color: '#374151',
                        padding: { bottom: 6 }
                    },
                    ticks: {
                        font: { size: 11, family: 'Inter, system-ui, -apple-system, sans-serif' },
                        color: '#6B7280',
                        padding: 6,
                        stepSize: 10
                    },
                    grid: { color: 'rgba(107, 114, 128, 0.2)', drawBorder: false, lineWidth: 1 }
                }
            },
            elements: {
                bar: {
                    borderRadius: 0,
                    borderSkipped: false
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

function renderCplRadarChartDistribusi() {
    const cplCpmkData = window.cplCpmkData || [];
    const chartElement = document.getElementById('cplRadarChartDistribusi');
    if (!chartElement || cplCpmkData.length === 0) return;

    const labels = cplCpmkData.map(cpl => cpl.cpl_label);
    const data = cplCpmkData.map(cpl => cpl.nilai_cpl);

    if (window.cplRadarDistribusiChartInstance) {
        window.cplRadarDistribusiChartInstance.destroy();
    }
    window.cplRadarDistribusiChartInstance = new Chart(chartElement, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai CPL',
                data: data,
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                borderColor: '#2563eb',
                borderWidth: 2,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#2563eb',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Radar Nilai CPL',
                    font: { size: 15, weight: '600', family: 'Inter, system-ui, -apple-system, sans-serif' },
                    color: '#374151',
                    padding: { top: 10, bottom: 10 }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    bodyFont: { size: 12, family: 'Inter, system-ui, -apple-system, sans-serif' },
                    titleFont: { size: 12, family: 'Inter, system-ui, -apple-system, sans-serif' },
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    titleColor: '#F9FAFB',
                    bodyColor: '#F9FAFB',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return `${context.chart.data.labels[context.dataIndex]}: ${context.parsed.r}`;
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
                        font: { size: 12, family: 'Inter, system-ui, -apple-system, sans-serif' },
                        color: '#374151'
                    },
                    ticks: {
                        stepSize: 20,
                        font: { size: 11, family: 'Inter, system-ui, -apple-system, sans-serif' },
                        color: '#6B7280',
                        showLabelBackdrop: false
                    }
                }
            },
            elements: {
                line: {
                    borderWidth: 2.5
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Panggil fungsi renderCplDistribusiBarChart di DOMContentLoaded dan resize
document.addEventListener('DOMContentLoaded', function () {
    renderAllCharts();
    renderCplDistribusiBarChart();
    renderCplRadarChartDistribusi();
});
window.addEventListener('resize', function () {
    renderAllCharts();
    renderCplDistribusiBarChart();
    renderCplRadarChartDistribusi();
});

