import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

window.whenChartReady = function (callback) {
    const run = () => callback(window.ApexCharts);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
};

window.__apexInstances = window.__apexInstances || {};

window.destroyApexChart = function (keyOrEl) {
    const key = typeof keyOrEl === 'string' ? keyOrEl : (keyOrEl?.id || null);
    if (key && window.__apexInstances[key]) {
        try {
            window.__apexInstances[key].destroy();
        } catch (e) {
            // ignore
        }
        delete window.__apexInstances[key];
    }
};

window.renderApexChart = function (el, options, key = null) {
    if (!el) return null;
    const instanceKey = key || el.id || `apex-${Date.now()}`;
    window.destroyApexChart(instanceKey);
    el.innerHTML = '';
    const chart = new ApexCharts(el, options);
    chart.render();
    window.__apexInstances[instanceKey] = chart;
    return chart;
};

/** Clone Apex runtime config for maximize/modal re-render */
window.pickApexConfig = function (chart) {
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
};

const DEFAULT_COLORS = [
    '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
    '#EC4899', '#06B6D4', '#22C55E', '#A855F7', '#FB7185'
];

function renderAllCharts() {
    const cplCpmkData = window.cplCpmkData || [];

    cplCpmkData.forEach((cpl, idx) => {
        const chartElement = document.getElementById('cplBarChart' + idx);
        if (!chartElement || !cpl.cpmk_data) return;

        const labels = cpl.cpmk_data.map((item) => {
            const parts = item.label.split(' - ');
            return parts.length > 1 ? `${parts[0]}\n${parts[1]}` : parts[0];
        });

        const allKomponenIds = new Set();
        cpl.cpmk_data.forEach((item) => {
            Object.keys(item.komponen_nilai || {}).forEach((id) => allKomponenIds.add(parseInt(id, 10)));
        });

        const series = Array.from(allKomponenIds).sort().map((komponenId, index) => {
            let componentName = `Komponen ${komponenId}`;
            for (const cpmkItem of cpl.cpmk_data) {
                if (cpmkItem.komponen_info && cpmkItem.komponen_info[komponenId]) {
                    componentName = cpmkItem.komponen_info[komponenId].nama || componentName;
                    break;
                }
            }

            return {
                name: componentName,
                data: cpl.cpmk_data.map((item) => item.komponen_nilai[komponenId] || 0),
                color: DEFAULT_COLORS[index % DEFAULT_COLORS.length],
            };
        });

        window.renderApexChart(chartElement, {
            chart: {
                type: 'bar',
                stacked: true,
                height: 350,
                toolbar: { show: false },
                fontFamily: '"DM Sans", ui-sans-serif, system-ui, sans-serif',
            },
            series,
            xaxis: {
                categories: labels,
                title: { text: 'CPMK' },
                labels: { style: { fontSize: '11px' } },
            },
            yaxis: {
                min: 0,
                max: 100,
                title: { text: 'Nilai' },
            },
            legend: { position: 'top' },
            title: {
                text: 'Komponen Nilai CPMK',
                align: 'center',
                style: { fontSize: '15px', fontWeight: 600 },
            },
            dataLabels: { enabled: false },
            plotOptions: {
                bar: { borderRadius: 0, columnWidth: '55%' },
            },
            tooltip: {
                y: { formatter: (val) => val },
            },
        }, `cplBarChart${idx}`);
    });
}

function renderCplDistribusiBarChart() {
    const cplCpmkData = window.cplCpmkData || [];
    const chartElement = document.getElementById('cplDistribusiBarChart');
    if (!chartElement || cplCpmkData.length === 0) return;

    const categories = cplCpmkData.map((cpl) => cpl.cpl_label);
    const data = cplCpmkData.map((cpl) => cpl.nilai_cpl);

    window.renderApexChart(chartElement, {
        chart: {
            type: 'bar',
            height: 380,
            toolbar: { show: false },
            fontFamily: '"DM Sans", ui-sans-serif, system-ui, sans-serif',
        },
        series: [{ name: 'Nilai CPL (CPMK Tertinggi)', data }],
        colors: DEFAULT_COLORS.slice(0, categories.length),
        plotOptions: {
            bar: {
                distributed: true,
                borderRadius: 0,
                columnWidth: '50%',
            },
        },
        xaxis: {
            categories,
            title: { text: 'CPL' },
        },
        yaxis: {
            min: 0,
            max: 100,
            title: { text: 'Nilai CPL' },
        },
        legend: { show: false },
        title: {
            text: 'Distribusi Nilai CPL (Berdasarkan CPMK Tertinggi)',
            align: 'center',
            style: { fontSize: '15px', fontWeight: 600 },
        },
        dataLabels: { enabled: false },
        tooltip: {
            custom: function ({ dataPointIndex }) {
                const cplData = cplCpmkData[dataPointIndex];
                if (!cplData?.cpmk_data?.length) {
                    return `<div class="px-3 py-2">Nilai CPL: ${Number(cplData?.nilai_cpl || 0).toFixed(2)}</div>`;
                }
                const cpmkTertinggi = cplData.cpmk_data.reduce((max, cpmk) =>
                    cpmk.nilai_normal > max.nilai_normal ? cpmk : max
                );
                return `<div class="px-3 py-2 text-sm">
                    <div><strong>Nilai CPL:</strong> ${Number(cplData.nilai_cpl).toFixed(2)}</div>
                    <div><strong>CPMK Tertinggi:</strong> ${cpmkTertinggi.label}</div>
                </div>`;
            },
        },
    }, 'cplDistribusiBarChart');
}

function renderCplRadarChartDistribusi() {
    const cplCpmkData = window.cplCpmkData || [];
    const chartElement = document.getElementById('cplRadarChartDistribusi');
    if (!chartElement || cplCpmkData.length === 0) return;

    const categories = cplCpmkData.map((cpl) => cpl.cpl_label);
    const data = cplCpmkData.map((cpl) => cpl.nilai_cpl);

    window.renderApexChart(chartElement, {
        chart: {
            type: 'radar',
            height: 380,
            toolbar: { show: false },
            fontFamily: '"DM Sans", ui-sans-serif, system-ui, sans-serif',
        },
        series: [{ name: 'Nilai CPL', data }],
        xaxis: { categories },
        yaxis: { min: 0, max: 100, tickAmount: 5 },
        colors: ['#2563eb'],
        fill: { opacity: 0.15 },
        markers: { size: 4 },
        stroke: { width: 2 },
        legend: { show: false },
        title: {
            text: 'Radar Nilai CPL (Berdasarkan CPMK Tertinggi)',
            align: 'center',
            style: { fontSize: '15px', fontWeight: 600 },
        },
    }, 'cplRadarChartDistribusi');
}

function renderCplCharts() {
    renderCplDistribusiBarChart();
    renderCplRadarChartDistribusi();

    const chartElements = Array.from(document.querySelectorAll('[id^="cplBarChart"]'));
    if (!('IntersectionObserver' in window)) {
        renderAllCharts();
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const idx = Number(entry.target.id.replace('cplBarChart', ''));
            const cpl = (window.cplCpmkData || [])[idx];
            if (cpl) {
                const originalData = window.cplCpmkData;
                window.cplCpmkData = originalData.map((item, itemIdx) => itemIdx === idx ? item : {});
                renderAllCharts();
                window.cplCpmkData = originalData;
            }
            observer.unobserve(entry.target);
        });
    }, { rootMargin: '200px 0px' });

    chartElements.forEach((element) => observer.observe(element));
}

document.addEventListener('DOMContentLoaded', renderCplCharts, { once: true });

// Flush queued chart callbacks only after helpers (renderApexChart, etc.) exist.
const pendingChartReady = Array.isArray(window.__chartReadyQueue)
    ? window.__chartReadyQueue.slice()
    : [];
window.__chartReadyQueue = [];
window.__chartReadyQueue.push = function (callback) {
    window.whenChartReady(callback);
    return 0;
};
pendingChartReady.forEach((callback) => window.whenChartReady(callback));
