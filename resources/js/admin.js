import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Filler,
    Tooltip,
    Legend,
    BarController,
    BarElement,
    DoughnutController,
    ArcElement,
} from 'chart.js';

Chart.register(
    LineController, LineElement, PointElement, LinearScale, CategoryScale,
    Filler, Tooltip, Legend, BarController, BarElement, DoughnutController, ArcElement
);

window.Chart = Chart;

// Revenue line chart
window.initRevenueChart = function (canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Revenue',
                data,
                borderColor: '#1a42e0',
                borderWidth: 2.5,
                pointRadius: 3,
                pointBackgroundColor: '#1a42e0',
                fill: true,
                backgroundColor: (context) => {
                    const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, context.chart.height);
                    gradient.addColorStop(0, 'rgba(26,66,224,0.15)');
                    gradient.addColorStop(1, 'rgba(26,66,224,0)');
                    return gradient;
                },
                tension: 0.4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' $' + ctx.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 }),
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 10 }, color: '#9A9A94' },
                    border: { display: false },
                },
                y: {
                    grid: { color: 'rgba(224,223,216,0.6)', drawBorder: false },
                    ticks: {
                        font: { family: 'Poppins', size: 10 },
                        color: '#9A9A94',
                        callback: (v) => '$' + (v >= 1000 ? (v / 1000) + 'k' : v),
                    },
                    border: { display: false },
                },
            },
        },
    });
};

// Enrolments bar chart
window.initEnrolmentsChart = function (canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Enrolments',
                data,
                backgroundColor: 'rgba(26,66,224,0.15)',
                borderColor: '#1a42e0',
                borderWidth: 1.5,
                borderRadius: 3,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 10 }, color: '#9A9A94' },
                    border: { display: false },
                },
                y: {
                    grid: { color: 'rgba(224,223,216,0.6)' },
                    ticks: { font: { family: 'Poppins', size: 10 }, color: '#9A9A94', precision: 0 },
                    border: { display: false },
                },
            },
        },
    });
};
