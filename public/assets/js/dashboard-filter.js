document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            const group = this.closest('.filter-group');
            const metric = group.dataset.metric;
            const period = this.dataset.period;
            const countElement = group.nextElementSibling; // Assuming h4.metric-value is immediately after the filter group div
            // Or better selector:
            const metricValueElement = group.parentElement.querySelector('.metric-value');

            // Update UI active state
            group.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-primary');

            // Fetch data
            fetch(`/dashboard/metric?metric=${metric}&period=${period}`)
                .then(response => response.json())
                .then(data => {
                    if (metricValueElement) {
                        metricValueElement.textContent = data.count;
                    }
                })
                .catch(error => {
                    console.error('Error fetching metric:', error);
                });
        });
    });
});
