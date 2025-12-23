document.addEventListener('DOMContentLoaded', () => {

    const modalEl = document.getElementById('serviceFeeModal');
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('serviceFeeForm');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    /* ------------------ ADD ------------------ */
    document.getElementById('addServiceFeeBtn').addEventListener('click', () => {
        form.reset();
        document.getElementById('fee_id').value = '';
        document.getElementById('is_active').checked = true;
        document.getElementById('serviceFeeModalLabel').innerText = 'Add Service Fee';
        modal.show();
    });

    /* ------------------ EDIT ------------------ */
    document.addEventListener('click', async e => {
        if (e.target.closest('.editBtn')) {

            const row = e.target.closest('tr');
            const id = row.dataset.id;

            const res = await fetch(`/service-fees/${id}`);
            const data = await res.json();

            document.getElementById('fee_id').value = data.id;
            document.getElementById('fee_type').value = data.fee_type;
            document.getElementById('percentage').value = data.percentage;
            document.getElementById('is_active').checked = data.is_active == 1;

            document.getElementById('serviceFeeModalLabel').innerText = 'Edit Service Fee';
            modal.show();
        }
    });

    /* ------------------ SAVE (CREATE / UPDATE) ------------------ */
    form.addEventListener('submit', async e => {
        e.preventDefault();

        const id = document.getElementById('fee_id').value;

        const payload = {
            fee_type: document.getElementById('fee_type').value,
            percentage: document.getElementById('percentage').value,
            is_active: document.getElementById('is_active').checked ? 1 : 0
        };

        const url = id ? `/service-fees/${id}` : `/service-fees`;
        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (res.ok) {
            modal.hide();
            location.reload();
        } else {
            alert(result.message || 'Failed to save service fee');
            console.error(result);
        }
    });

    /* ------------------ DELETE ------------------ */
    document.addEventListener('click', async e => {
        if (e.target.closest('.deleteBtn')) {

            if (!confirm('Delete this service fee?')) return;

            const row = e.target.closest('tr');
            const id = row.dataset.id;

            const res = await fetch(`/service-fees/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                row.remove();
            } else {
                alert('Failed to delete service fee');
            }
        }
    });

});