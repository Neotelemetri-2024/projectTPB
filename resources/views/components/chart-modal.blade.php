<!-- Chart Maximize Modal Component -->
<div id="chart-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with animation -->
    <div class="absolute inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity duration-300" onclick="closeChartModal()"></div>
    
    <!-- Modal content with animation -->
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] transform transition-all duration-300 scale-95 opacity-0" id="modal-content">
            <!-- Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900"></h3>
                <button onclick="closeChartModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Chart Container -->
            <div class="p-6">
                <div class="relative h-[70vh]">
                    <canvas id="modal-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global chart instance for modal
let modalChartInstance = null;

// Function to maximize a chart
function maximizeChart(chartId, title) {
    const modal = document.getElementById('chart-modal');
    const modalContent = document.getElementById('modal-content');
    
    if (modal && modalContent) {
        // Set title
        document.getElementById('modal-title').textContent = title;
        
        // Show modal with animation
        modal.classList.remove('hidden');
        
        // Trigger animation after a small delay
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Hide original chart
        const originalChart = document.getElementById(chartId);
        if (originalChart) {
            originalChart.classList.add('hidden');
        }
        
        // Destroy previous chart instance if exists
        if (modalChartInstance) {
            modalChartInstance.destroy();
        }
        
        // Create new chart in modal
        const modalChartCtx = document.getElementById('modal-chart');
        if (modalChartCtx) {
            // Get chart data based on chartId
            let chartData;
            let chartType;
            let chartOptions;
            
            switch(chartId) {
                case 'historyChart':
                    chartData = window.chartData;
                    chartType = 'line';
                    chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Rata-rata Nilai'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tahun Ajaran'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    };
                    break;
                case 'cplChart':
                    chartData = window.cplAchievementData;
                    chartType = 'bar';
                    chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Rata-rata Pencapaian (%)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Kode CPL'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    };
                    break;
                case 'gradeChart':
                    chartData = window.matkulPerformanceData;
                    chartType = 'bar';
                    chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Mahasiswa'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Grade'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 8,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    };
                    break;
                case 'courseTypeChart':
                    chartData = window.courseTypeData;
                    chartType = 'pie';
                    chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    };
                    break;
                case 'completionChart':
                    chartData = window.courseCompletionData;
                    chartType = 'bar';
                    chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Tingkat Kelulusan (%)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Kode Mata Kuliah'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    };
                    break;
                case 'topStudentsChart':
                    chartData = window.topStudentsData;
                    chartType = 'bar';
                    chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Rata-rata Nilai'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'NIM Mahasiswa'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    };
                    break;
            }
            
            // Create new chart in modal
            if (chartData && chartType) {
                modalChartInstance = new Chart(modalChartCtx.getContext('2d'), {
                    type: chartType,
                    data: chartData,
                    options: chartOptions
                });
            }
        }
    }
}

// Function to close the modal
function closeChartModal() {
    const modal = document.getElementById('chart-modal');
    const modalContent = document.getElementById('modal-content');
    
    if (modal && modalContent) {
        // Add animation for closing
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        // Hide modal after animation
        setTimeout(() => {
            modal.classList.add('hidden');
            
            // Show original chart
            const originalChartId = modal.dataset.originalChartId;
            if (originalChartId) {
                const originalChart = document.getElementById(originalChartId);
                if (originalChart) {
                    originalChart.classList.remove('hidden');
                }
            }
            
            // Destroy modal chart instance
            if (modalChartInstance) {
                modalChartInstance.destroy();
                modalChartInstance = null;
            }
        }, 300);
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeChartModal();
    }
});
</script>
