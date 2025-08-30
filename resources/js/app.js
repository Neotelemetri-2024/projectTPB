import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
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
            const labels = cpl.cpmk_data.map(item => {
                // Split label menjadi kode CPMK dan nama mata kuliah
                const parts = item.label.split(' - ');
                const kodeCpmk = parts[0]; // CPMK-1, CPMK-2, etc.
                const namaMatkul = parts[1] || ''; // Ekonomi Teknik

                // Return array untuk multi-line label
                return [kodeCpmk, namaMatkul];
            });
            const datasets = [];

            // Ambil semua komponen ID yang ada dari data
            const allKomponenIds = new Set();
            cpl.cpmk_data.forEach(item => {
                Object.keys(item.komponen_nilai).forEach(id => {
                    allKomponenIds.add(parseInt(id));
                });
            });

            // Warna untuk chart (rotasi otomatis jika lebih dari 5 komponen)
            const colors = [
                'rgba(239, 68, 68, 0.8)',  // Red - Kuis
                'rgba(59, 130, 246, 0.8)', // Blue - UAS
                'rgba(245, 158, 11, 0.8)', // Amber - UTS
                'rgba(16, 185, 129, 0.8)', // Green - TB
                'rgba(139, 92, 246, 0.8)', // Purple - Tugas
                'rgba(236, 72, 153, 0.8)', // Pink
                'rgba(14, 165, 233, 0.8)', // Sky
                'rgba(34, 197, 94, 0.8)',  // Emerald
                'rgba(168, 85, 247, 0.8)', // Violet
                'rgba(251, 113, 133, 0.8)' // Rose
            ];

            // Buat dataset untuk setiap komponen berdasarkan ID yang ada
            Array.from(allKomponenIds).sort().forEach((komponenId, index) => {
                const data = cpl.cpmk_data.map(item => item.komponen_nilai[komponenId] || 0);

                // Ambil nama komponen dari database (jika tersedia) atau gunakan fallback
                let componentName = `Komponen ${komponenId}`;

                // Cari nama komponen dari komponen_info yang tersedia
                for (let cpmkItem of cpl.cpmk_data) {
                    if (cpmkItem.komponen_info && cpmkItem.komponen_info[komponenId]) {
                        componentName = cpmkItem.komponen_info[komponenId].nama || `Komponen ${komponenId}`;
                        break;
                    }
                }

                datasets.push({
                    label: componentName,
                    data: data,
                    backgroundColor: colors[index % colors.length],
                    borderColor: 'transparent',
                    borderWidth: 0,
                    stack: 'Stack 0',
                    order: index
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
                                autoSkip: false, // Paksa tampilkan semua labels
                                maxTicksLimit: false // Tidak ada batas jumlah ticks
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            max: 100, // Pastikan rentang grafik sampai 100
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

    // Siapkan data: label = CPL, data = nilai CPMK tertinggi per CPL
    const cplLabels = cplCpmkData.map(cpl => cpl.cpl_label);

    // Dataset tunggal: nilai CPMK tertinggi per CPL
    const data = cplCpmkData.map(cpl => {
        // Nilai CPL sudah berdasarkan CPMK tertinggi dari controller
        return cpl.nilai_cpl;
    });

    const datasets = [{
        label: 'Nilai CPL (CPMK Tertinggi)',
        data: data,
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(245, 158, 11, 0.8)',   // Amber
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(139, 92, 246, 0.8)',   // Purple
            'rgba(251, 191, 36, 0.8)',   // Yellow
            'rgba(34, 197, 94, 0.8)',    // Emerald
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(14, 165, 233, 0.8)',   // Sky
            'rgba(168, 85, 247, 0.8)',   // Violet
            'rgba(251, 113, 133, 0.8)'   // Rose
        ].slice(0, cplLabels.length),
        borderColor: 'transparent',
        borderWidth: 0
    }];
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
                    text: 'Distribusi Nilai CPL (Berdasarkan CPMK Tertinggi)',
                    font: { size: 15, weight: '600', family: 'Inter, system-ui, -apple-system, sans-serif' },
                    color: '#374151',
                    padding: { top: 10, bottom: 10 }
                },
                legend: {
                    display: false // Tidak perlu legend karena hanya 1 dataset
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
                            const cplData = cplCpmkData[context.dataIndex];
                            // Tampilkan CPMK mana yang tertinggi
                            const cpmkTertinggi = cplData.cpmk_data.reduce((max, cpmk) =>
                                cpmk.nilai_normal > max.nilai_normal ? cpmk : max
                            );
                            return [
                                `Nilai CPL: ${context.parsed.y.toFixed(2)}`,
                                `CPMK Tertinggi: ${cpmkTertinggi.label}`,
                            ];
                        }
                    }
                }
            },
            layout: {
                padding: { left: 10, right: 10, top: 10, bottom: 10 }
            },
            scales: {
                x: {
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
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Nilai CPL',
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
                    text: 'Radar Nilai CPL (Berdasarkan CPMK Tertinggi)',
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
                            return `${context.chart.data.labels[context.dataIndex]}: ${context.parsed.r.toFixed(2)} (CPMK Tertinggi)`;
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

