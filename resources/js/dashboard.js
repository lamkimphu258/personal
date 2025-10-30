const formatDateRange = (start, end) => {
    const formatter = new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });

    const safeStart = new Date(`${start}T00:00:00`);
    const safeEnd = new Date(`${end}T00:00:00`);

    if (Number.isNaN(safeStart.getTime()) || Number.isNaN(safeEnd.getTime())) {
        return `${start} – ${end}`;
    }

    return `${formatter.format(safeStart)} – ${formatter.format(safeEnd)}`;
};

class LineChart {
    constructor(element) {
        this.element = element;
        this.canvas = element.querySelector('[data-line-chart-canvas]');
        this.loadingEl = element.querySelector('[data-line-chart-loading]');
        this.messageEl = element.querySelector('[data-line-chart-message]');
        this.emptyMessage = element.dataset.emptyMessage || 'No data available.';
        this.currentDataset = null;
        this.options = {};
        this.resizeHandler = () => this.render();

        if (element.dataset.initialLoading === 'true') {
            this.setLoading(true);
        }

        window.addEventListener('resize', this.resizeHandler);
    }

    setLoading(active) {
        if (!this.loadingEl) {
            return;
        }

        this.loadingEl.classList.toggle('hidden', !active);

        if (active) {
            this.showMessage(null);
        }
    }

    showMessage(message) {
        if (!this.messageEl) {
            return;
        }

        if (message === null) {
            this.messageEl.classList.add('hidden');
            this.messageEl.textContent = '';
        } else {
            this.messageEl.classList.remove('hidden');
            this.messageEl.textContent = message;
        }
    }

    update(dataset) {
        this.currentDataset = dataset;
        this.setLoading(false);
        this.render();
    }

    destroy() {
        window.removeEventListener('resize', this.resizeHandler);
    }

    render() {
        if (!this.canvas) {
            return;
        }

        const dataset = this.currentDataset;

        if (!dataset || !Array.isArray(dataset.points)) {
            this.clearCanvas();
            this.showMessage(this.emptyMessage);

            return;
        }

        if (dataset.meta && dataset.meta.hasTarget === false) {
            this.clearCanvas();
            this.showMessage(dataset.meta.message || this.emptyMessage);

            return;
        }

        if (dataset.points.length === 0) {
            this.clearCanvas();
            this.showMessage(this.emptyMessage);

            return;
        }

        this.showMessage(null);
        this.drawLine(dataset);
    }

    clearCanvas() {
        if (!this.canvas) {
            return;
        }

        const ctx = this.canvas.getContext('2d');

        if (!ctx) {
            return;
        }

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }

    drawLine(dataset) {
        const ctx = this.canvas.getContext('2d');

        if (!ctx) {
            return;
        }

        const width = this.canvas.clientWidth;
        const height = this.canvas.clientHeight;
        const deviceRatio = window.devicePixelRatio || 1;

        if (width === 0 || height === 0) {
            return;
        }

        this.canvas.width = Math.round(width * deviceRatio);
        this.canvas.height = Math.round(height * deviceRatio);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(deviceRatio, deviceRatio);
        ctx.clearRect(0, 0, width, height);

        const padding = {
            top: 28,
            right: 24,
            bottom: 48,
            left: 56,
        };

        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;

        const points = dataset.points.map((point) => ({
            label: String(point.label),
            value: Number(point.value),
        }));

        const values = points.map((point) => point.value);
        let minValue = Math.min(...values);
        let maxValue = Math.max(...values);

        if (!Number.isFinite(minValue) || !Number.isFinite(maxValue)) {
            return;
        }

        if (minValue === maxValue) {
            if (minValue === 0) {
                maxValue = 1;
            } else {
                const offset = Math.abs(minValue) * 0.1 || 1;
                minValue -= offset;
                maxValue += offset;
            }
        } else {
            const paddingAmount = (maxValue - minValue) * 0.1;
            minValue -= paddingAmount;
            maxValue += paddingAmount;
        }

        const valueRange = maxValue - minValue || 1;

        const getX = (index) => {
            if (points.length === 1) {
                return padding.left + chartWidth / 2;
            }

            const ratio = index / (points.length - 1);

            return padding.left + ratio * chartWidth;
        };

        const getY = (value) => {
            const ratio = (value - minValue) / valueRange;

            return padding.top + (1 - ratio) * chartHeight;
        };

        // Axes
        ctx.strokeStyle = 'rgba(100, 116, 139, 0.35)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padding.left, padding.top);
        ctx.lineTo(padding.left, padding.top + chartHeight);
        ctx.lineTo(padding.left + chartWidth, padding.top + chartHeight);
        ctx.stroke();

        // Horizontal grid lines and Y labels
        const yTickCount = 4;
        ctx.strokeStyle = 'rgba(71, 85, 105, 0.25)';
        ctx.fillStyle = '#94a3b8';
        ctx.font = '12px "Instrument Sans", ui-sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        const suffix = typeof dataset.valueSuffix === 'string' ? dataset.valueSuffix : '';
        const decimals = Number.isFinite(dataset.valueDecimals) ? Number(dataset.valueDecimals) : 0;

        for (let i = 0; i <= yTickCount; i += 1) {
            const ratio = i / yTickCount;
            const y = padding.top + ratio * chartHeight;
            const value = maxValue - ratio * (maxValue - minValue);

            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(padding.left + chartWidth, y);
            ctx.stroke();

            ctx.fillText(`${value.toFixed(decimals)}${suffix}`, padding.left - 10, y);
        }

        // X labels
        const xTickCount = Math.min(points.length, 6);
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';

        for (let i = 0; i < xTickCount; i += 1) {
            const index = xTickCount === 1
                ? 0
                : Math.round((points.length - 1) * (i / (xTickCount - 1)));

            const point = points[index];
            const x = getX(index);
            const label = this.formatLabel(point.label, Boolean(dataset.labelsAreDates));

            ctx.fillText(label, x, padding.top + chartHeight + 12);
        }

        // Line
        ctx.strokeStyle = '#34d399';
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
        ctx.beginPath();

        points.forEach((point, index) => {
            const x = getX(index);
            const y = getY(point.value);

            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });

        ctx.stroke();

        // Points
        ctx.fillStyle = '#22c55e';
        points.forEach((point, index) => {
            const x = getX(index);
            const y = getY(point.value);

            ctx.beginPath();
            ctx.arc(x, y, 3.5, 0, Math.PI * 2);
            ctx.fill();
        });
    }

    formatLabel(label, labelsAreDates) {
        if (!labelsAreDates) {
            return label.length > 18 ? `${label.slice(0, 17)}…` : label;
        }

        const parsed = new Date(`${label}T00:00:00`);

        if (Number.isNaN(parsed.getTime())) {
            return label;
        }

        return new Intl.DateTimeFormat(undefined, {
            month: 'short',
            day: 'numeric',
        }).format(parsed);
    }
}

class BarChart {
    constructor(element) {
        this.element = element;
        this.canvas = element.querySelector('[data-bar-chart-canvas]');
        this.loadingEl = element.querySelector('[data-bar-chart-loading]');
        this.messageEl = element.querySelector('[data-bar-chart-message]');
        this.emptyMessage = element.dataset.emptyMessage || 'No data available.';
        this.currentDataset = null;
        this.resizeHandler = () => this.render();

        if (element.dataset.initialLoading === 'true') {
            this.setLoading(true);
        }

        window.addEventListener('resize', this.resizeHandler);
    }

    setLoading(active) {
        if (!this.loadingEl) {
            return;
        }

        this.loadingEl.classList.toggle('hidden', !active);

        if (active) {
            this.showMessage(null);
        }
    }

    showMessage(message) {
        if (!this.messageEl) {
            return;
        }

        if (message === null) {
            this.messageEl.classList.add('hidden');
            this.messageEl.textContent = '';
        } else {
            this.messageEl.classList.remove('hidden');
            this.messageEl.textContent = message;
        }
    }

    update(dataset) {
        this.currentDataset = dataset;
        this.setLoading(false);
        this.render();
    }

    destroy() {
        window.removeEventListener('resize', this.resizeHandler);
    }

    render() {
        if (!this.canvas) {
            return;
        }

        const dataset = this.currentDataset;

        if (!dataset || !Array.isArray(dataset.points)) {
            this.clearCanvas();
            this.showMessage(this.emptyMessage);

            return;
        }

        if (dataset.points.length === 0) {
            this.clearCanvas();
            this.showMessage(this.emptyMessage);

            return;
        }

        this.showMessage(null);
        this.drawBars(dataset);
    }

    clearCanvas() {
        if (!this.canvas) {
            return;
        }

        const ctx = this.canvas.getContext('2d');

        if (!ctx) {
            return;
        }

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }

    drawBars(dataset) {
        const ctx = this.canvas.getContext('2d');

        if (!ctx) {
            return;
        }

        const width = this.canvas.clientWidth;
        const height = this.canvas.clientHeight;
        const deviceRatio = window.devicePixelRatio || 1;

        if (width === 0 || height === 0) {
            return;
        }

        this.canvas.width = Math.round(width * deviceRatio);
        this.canvas.height = Math.round(height * deviceRatio);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(deviceRatio, deviceRatio);
        ctx.clearRect(0, 0, width, height);

        const padding = {
            top: 28,
            right: 96,
            bottom: 28,
            left: 180,
        };

        const chartWidth = Math.max(0, width - padding.left - padding.right);
        const chartHeight = Math.max(0, height - padding.top - padding.bottom);

        const points = dataset.points.map((point) => ({
            label: String(point.label),
            value: Number(point.value),
        }));

        const values = points.map((point) => point.value);
        let maxValue = Math.max(...values);

        if (!Number.isFinite(maxValue) || maxValue <= 0) {
            maxValue = 1;
        }

        const step = points.length > 0 ? chartHeight / points.length : chartHeight;
        const baseBarHeight = Math.min(step * 0.6, 32);
        let barHeight = Math.max(
            Math.min(baseBarHeight, step - 6),
            Math.min(step * 0.8, 14),
        );

        if (!Number.isFinite(barHeight) || barHeight <= 0) {
            barHeight = Math.max(step * 0.6, 8);
        }
        const suffix = typeof dataset.valueSuffix === 'string' ? dataset.valueSuffix : '';
        const decimals = Number.isFinite(dataset.valueDecimals) ? Number(dataset.valueDecimals) : 0;

        // Background and vertical axis
        ctx.strokeStyle = 'rgba(100, 116, 139, 0.35)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padding.left, padding.top);
        ctx.lineTo(padding.left, padding.top + chartHeight);
        ctx.lineTo(padding.left + chartWidth, padding.top + chartHeight);
        ctx.stroke();

        // Vertical grid lines and value labels
        const xTickCount = 4;
        ctx.strokeStyle = 'rgba(71, 85, 105, 0.25)';
        ctx.fillStyle = '#94a3b8';
        ctx.font = '12px "Instrument Sans", ui-sans-serif';
        ctx.textBaseline = 'top';

        for (let i = 0; i <= xTickCount; i += 1) {
            const ratio = i / xTickCount;
            const x = padding.left + ratio * chartWidth;
            const value = maxValue * ratio;

            ctx.beginPath();
            ctx.moveTo(x, padding.top);
            ctx.lineTo(x, padding.top + chartHeight);
            ctx.stroke();

            ctx.textAlign = 'center';
            ctx.fillText(`${value.toFixed(decimals)}${suffix}`, x, padding.top + chartHeight + 8);
        }

        // Bars and labels
        points.forEach((point, index) => {
            const centerY = padding.top + step * index + step / 2;
            const y = centerY - barHeight / 2;
            const barLength = chartWidth * (point.value / maxValue);
            const label = this.truncateLabel(point.label);
            const valueText = `${point.value.toFixed(decimals)}${suffix}`;

            // Category label
            ctx.fillStyle = '#94a3b8';
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';
            ctx.fillText(label, padding.left - 16, centerY);

            // Bar background
            ctx.fillStyle = 'rgba(52, 211, 153, 0.18)';
            ctx.fillRect(padding.left, y, chartWidth, barHeight);

            // Bar fill
            ctx.fillStyle = '#34d399';
            ctx.fillRect(padding.left, y, barLength, barHeight);

            // Value label positioning
            const valueFitsInside = barLength > 64;
            ctx.textBaseline = 'middle';
            ctx.textAlign = valueFitsInside ? 'right' : 'left';
            ctx.fillStyle = valueFitsInside ? '#0f172a' : '#e2e8f0';
            const valueX = valueFitsInside
                ? padding.left + barLength - 10
                : padding.left + barLength + 10;

            ctx.fillText(valueText, valueX, centerY);
        });
    }

    truncateLabel(label) {
        if (label.length <= 24) {
            return label;
        }

        return `${label.slice(0, 23)}…`;
    }
}

class DashboardManager {
    constructor(root, config) {
        this.root = root;
        this.endpoint = config.endpoint;
        this.range = config.initialRange;
        this.data = config.initialData;
        this.charts = new Map();
        this.rangeSummaryEl = root.querySelector('[data-range-summary]');
        this.rangeButtons = Array.from(root.querySelectorAll('[data-range-button]'));
        this.customRangeForm = root.querySelector('[data-custom-range-form]');
        this.pendingRequest = null;

        this.initCharts();
        this.bindEvents();
        this.updateRangeSummary();
        this.updatePresetButtons(this.range?.preset);
        this.updateCustomRangeInputs();
        this.renderAll();
    }

    initCharts() {
        this.registerCharts('[data-line-chart]', (element) => new LineChart(element));
        this.registerCharts('[data-bar-chart]', (element) => new BarChart(element));
    }

    registerCharts(selector, factory) {
        const elements = this.root.querySelectorAll(selector);

        elements.forEach((element) => {
            const key = element.dataset.chartKey || element.dataset.chartId;

            if (!key || this.charts.has(key)) {
                return;
            }

            const chart = factory(element);
            this.charts.set(key, chart);
        });
    }

    bindEvents() {
        this.rangeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const { preset } = button.dataset;

                if (!preset) {
                    return;
                }

                this.applyPreset(preset);
            });
        });

        if (this.customRangeForm) {
            this.customRangeForm.addEventListener('submit', (event) => {
                event.preventDefault();

                const formData = new FormData(this.customRangeForm);
                const start = String(formData.get('start') || '');
                const end = String(formData.get('end') || '');

                if (!start || !end) {
                    return;
                }

                this.applyCustomRange(start, end);
            });
        }
    }

    applyPreset(preset) {
        this.fetchRange({ preset });
    }

    applyCustomRange(start, end) {
        this.fetchRange({ start, end });
    }

    async fetchRange(params) {
        if (!this.endpoint) {
            return;
        }

        const url = new URL(this.endpoint, window.location.origin);

        Object.entries(params).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            }
        });

        this.setChartsLoading(true);
        this.setRangeSummary('Loading…');

        try {
            if (this.pendingRequest) {
                this.pendingRequest.abort();
            }

            const controller = new AbortController();
            this.pendingRequest = controller;

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                },
                signal: controller.signal,
                cache: 'no-store',
            });

            if (!response.ok) {
                throw new Error(`Request failed with ${response.status}`);
            }

            const payload = await response.json();

            this.range = payload.range;
            this.data = payload.data;
            this.renderAll();
            this.updatePresetButtons(this.range?.preset);
            this.updateCustomRangeInputs();
            this.updateRangeSummary();
        } catch (error) {
            console.error('Failed to load dashboard data', error);
            this.setRangeSummary('Unable to refresh data. Please try again.');
        } finally {
            this.setChartsLoading(false);
            this.updatePresetButtons(this.range?.preset);
            this.updateCustomRangeInputs();
            this.pendingRequest = null;
        }
    }

    setChartsLoading(isLoading) {
        this.charts.forEach((chart) => {
            chart.setLoading(isLoading);
        });
    }

    renderAll() {
        this.charts.forEach((chart, key) => {
            const dataset = this.data?.[key] || { points: [] };

            chart.update(dataset);
        });
    }

    updateRangeSummary() {
        if (!this.rangeSummaryEl || !this.range) {
            return;
        }

        const { start, end } = this.range;

        if (!start || !end) {
            this.rangeSummaryEl.textContent = '';

            return;
        }

        this.rangeSummaryEl.textContent = `Showing ${formatDateRange(start, end)}`;
    }

    setRangeSummary(message) {
        if (!this.rangeSummaryEl) {
            return;
        }

        this.rangeSummaryEl.textContent = message;
    }

    updatePresetButtons(activePreset) {
        this.rangeButtons.forEach((button) => {
            const { preset } = button.dataset;
            const isActive = preset === activePreset;

            button.dataset.active = isActive ? 'true' : 'false';
        });
    }

    updateCustomRangeInputs() {
        if (!this.customRangeForm || !this.range) {
            return;
        }

        const startInput = this.customRangeForm.querySelector('input[name="start"]');
        const endInput = this.customRangeForm.querySelector('input[name="end"]');

        if (startInput && this.range.start) {
            startInput.value = this.range.start;
        }

        if (endInput && this.range.end) {
            endInput.value = this.range.end;
        }
    }
}

const bootstrapDashboard = () => {
    const root = document.querySelector('[data-dashboard-root]');

    if (!root) {
        return;
    }

    let initialRange;
    let initialData;
    let endpoint;

    try {
        initialRange = root.dataset.initialRange
            ? JSON.parse(root.dataset.initialRange)
            : window.dashboardConfig?.initialRange;
    } catch (error) {
        initialRange = window.dashboardConfig?.initialRange;
    }

    try {
        initialData = root.dataset.initialData
            ? JSON.parse(root.dataset.initialData)
            : window.dashboardConfig?.initialData;
    } catch (error) {
        initialData = window.dashboardConfig?.initialData;
    }

    endpoint = root.dataset.dashboardEndpoint || window.dashboardConfig?.endpoint;

    if (!initialRange || !initialData || !endpoint) {
        console.warn('Dashboard configuration missing. Skipping boot.');
        return;
    }

    new DashboardManager(root, {
        initialRange,
        initialData,
        endpoint,
    });
};

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    bootstrapDashboard();
} else {
    document.addEventListener('DOMContentLoaded', bootstrapDashboard);
}
