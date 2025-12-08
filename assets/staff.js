document.addEventListener('DOMContentLoaded', () => {

    // --- Page Nav Links ---
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'dashboard';

    document.querySelectorAll('.nav-link[data-page]').forEach(link => {
        if (link.dataset.page === activeTab) link.classList.add('active');

        link.addEventListener('click', e => {
            e.preventDefault();
            const page = link.dataset.page;

            document.querySelectorAll('.nav-link[data-page]').forEach(l => l.classList.remove('active'));
            link.classList.add('active');

            ['dashboard','loan','clearance','penalties'].forEach(sec => {
                const el = document.getElementById(`page-${sec}`);
                if (el) el.classList.add('d-none');
            });

            const activeSection = document.getElementById(`page-${page}`);
            if (activeSection) activeSection.classList.remove('d-none');
        });
    });

    // --- Borrow History Modal ---
    const borrowHistoryModalEl = document.getElementById('borrowHistoryModal');
    const borrowHistoryModal = borrowHistoryModalEl ? new bootstrap.Modal(borrowHistoryModalEl) : null;

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-history-btn') && borrowHistoryModal) {
            const userId = e.target.dataset.userId;

            fetch(`../../controllers/StaffController.php?action=getBorrowHistory&user_id=${userId}`)
                .then(res => res.json())
                .then(data => {
                    const tableBody = document.getElementById('borrow-history-table');
                    tableBody.innerHTML = '';

                    if (!data.length) {
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No borrow records found.</td></tr>`;
                    } else {
                        data.forEach(row => {
                            tableBody.innerHTML += `
                                <tr class="text-center">
                                    <td>${row.title}</td>
                                    <td>${row.borrow_date}</td>
                                    <td>${row.due_date}</td>
                                    <td>${row.return_date ?? '-'}</td>
                                    <td>${row.status}</td>
                                </tr>`;
                        });
                    }

                    borrowHistoryModal.show();
                })
                .catch(err => console.error(err));
        }
    });

});
