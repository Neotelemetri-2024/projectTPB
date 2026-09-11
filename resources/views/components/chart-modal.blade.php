<!-- Chart Maximize Modal Component -->
<div id="chart-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity duration-300" onclick="closeChartModal()"></div>

    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] transform transition-all duration-300 scale-95 opacity-0" id="modal-content">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900"></h3>
                <button onclick="closeChartModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="relative h-[70vh]">
                    <div id="modal-chart" class="h-full w-full"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let modalChartInstance = null;

function resolveApexInstance(chartId) {
    return (window.__apexInstances && window.__apexInstances[chartId])
        || (window.chartInstances && window.chartInstances[chartId])
        || window[chartId]
        || window[`${chartId}Instance`]
        || null;
}

function pickApexConfig(chart) {
    // Prefer runtime config (has series after render); fall back to construction opts
    const cfg = (chart && chart.w && chart.w.config) ? chart.w.config : (chart && chart.opts ? chart.opts : null);
    if (!cfg) return null;

    const safe = (value) => {
        try {
            return JSON.parse(JSON.stringify(value));
        } catch (e) {
            return undefined;
        }
    };

    const chartOpts = safe(cfg.chart) || {};
    delete chartOpts.id;
    delete chartOpts.events;
    delete chartOpts.animations;
    chartOpts.height = '100%';
    chartOpts.width = '100%';
    chartOpts.toolbar = Object.assign({}, chartOpts.toolbar || {}, { show: true });

    const options = { chart: chartOpts };

    ['series', 'labels', 'xaxis', 'yaxis', 'colors', 'stroke', 'fill', 'markers',
        'legend', 'plotOptions', 'dataLabels', 'tooltip', 'grid', 'annotations',
        'title', 'subtitle', 'theme', 'responsive'].forEach((key) => {
        if (cfg[key] !== undefined) {
            const copied = safe(cfg[key]);
            if (copied !== undefined) options[key] = copied;
        }
    });

    // Apex sometimes keeps live series only in globals
    if ((!options.series || !options.series.length) && chart.w && chart.w.globals) {
        const g = chart.w.globals;
        if (Array.isArray(g.series) && g.series.length) {
            if (typeof g.series[0] === 'number') {
                options.series = g.series.slice();
            } else {
                options.series = (g.seriesNames || []).map((name, i) => ({
                    name: name || `Series ${i + 1}`,
                    data: Array.isArray(g.series[i]) ? g.series[i].slice() : [],
                }));
            }
        }
        if ((!options.labels || !options.labels.length) && Array.isArray(g.labels)) {
            options.labels = g.labels.slice();
        }
    }

    return options;
}

function maximizeChart(chartId, title) {
    const modal = document.getElementById('chart-modal');
    const modalContent = document.getElementById('modal-content');
    const modalChartEl = document.getElementById('modal-chart');

    if (!modal || !modalContent || !modalChartEl) {
        console.error('Chart modal elements not found');
        return;
    }

    if (!window.ApexCharts) {
        console.error('ApexCharts is not available on this page');
        return;
    }

        document.getElementById('modal-title').textContent = title;
        modal.classList.remove('hidden');
    modal.dataset.originalChartId = chartId;

        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);

    // Keep original chart visible; modal shows a clone
    if (modalChartInstance) {
        try { modalChartInstance.destroy(); } catch (e) { /* ignore */ }
        modalChartInstance = null;
    }
    modalChartEl.innerHTML = '';

    const sourceChart = resolveApexInstance(chartId);
    if (!sourceChart) {
        console.error('Original Apex chart not found for ID:', chartId, {
            apexKeys: Object.keys(window.__apexInstances || {}),
            chartKeys: Object.keys(window.chartInstances || {}),
        });
        return;
    }

            setTimeout(() => {
        try {
            const options = pickApexConfig(sourceChart);
            if (!options || !options.series || (Array.isArray(options.series) && options.series.length === 0)) {
                throw new Error('Cloned Apex options missing series data');
            }
            modalChartInstance = new window.ApexCharts(modalChartEl, options);
            modalChartInstance.render().then(() => {
                if (modalChartInstance && typeof modalChartInstance.resize === 'function') {
                    modalChartInstance.resize();
                }
            });
        } catch (error) {
            console.error('Error creating modal Apex chart:', error);
        }
    }, 150);
}

function closeChartModal() {
    const modal = document.getElementById('chart-modal');
    const modalContent = document.getElementById('modal-content');

    if (!modal || !modalContent) return;

        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');

            if (modalChartInstance) {
            try { modalChartInstance.destroy(); } catch (e) { /* ignore */ }
                modalChartInstance = null;
            }

        const modalChartEl = document.getElementById('modal-chart');
        if (modalChartEl) modalChartEl.innerHTML = '';
        }, 300);
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('chart-modal');
        if (modal && !modal.classList.contains('hidden')) {
        closeChartModal();
        }
    }
});
</script>
