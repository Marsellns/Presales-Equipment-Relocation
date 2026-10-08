window.SimasterChartPalette = Object.freeze({
    series: Object.freeze([
        '#3B82F6',
        '#14B8A6',
        '#8B5CF6',
        '#F59E0B',
        '#F97316',
        '#D977A8',
        '#06B6D4',
        '#6366F1',
    ]),
});

window.SimasterChartMetrics = Object.freeze({
    share(value, values) {
        const numericValue = Number(value);
        const total = (values || []).reduce((sum, item) => {
            const candidate = item && typeof item === 'object' ? item.y : item;
            const numeric = Number(candidate);
            return Number.isFinite(numeric) ? sum + Math.abs(numeric) : sum;
        }, 0);

        if (!Number.isFinite(numericValue) || total === 0) return null;

        return (Math.abs(numericValue) / total) * 100;
    },
    format(percentage, digits = 1) {
        if (percentage === null || percentage === undefined) return '—';

        return Number(percentage).toLocaleString('id-ID', {
            maximumFractionDigits: digits,
        }) + '%';
    },
    highcharts(point) {
        return this.share(point?.y, point?.series?.data || []);
    },
});
