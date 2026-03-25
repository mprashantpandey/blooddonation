document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-nav-overlay');
    const openBtn = document.getElementById('admin-nav-open');
    const closeBtn = document.getElementById('admin-nav-close');

    if (sidebar && overlay && openBtn) {
        const setOpen = (open) => {
            sidebar.setAttribute('data-open', open ? 'true' : 'false');
            overlay.classList.toggle('hidden', !open);
            document.body.classList.toggle('overflow-hidden', open);
        };

        openBtn.addEventListener('click', () => setOpen(true));
        closeBtn?.addEventListener('click', () => setOpen(false));
        overlay.addEventListener('click', () => setOpen(false));

        window.addEventListener('resize', () => {
            if (window.matchMedia('(min-width: 1024px)').matches) {
                setOpen(false);
            }
        });
    }

    // DataTables init for admin pages.
    // We enhance only tables marked with `table.datatable` (desktop tables).
    const initDataTablesOnce = () => {
        const $ = window.jQuery;
        if (!($ && $.fn && $.fn.DataTable)) return false;

        const tables = document.querySelectorAll('table.datatable');
        if (!tables || tables.length === 0) return true; // nothing to do

        tables.forEach((table) => {
            try {
                if ($.fn.DataTable.isDataTable(table)) return;
            } catch (_) {
                // ignore; we'll just try to init
            }

            // Avoid initializing on hidden tables (Tailwind `hidden lg:block`),
            // which can cause bad widths/controls.
            const rect = table.getBoundingClientRect();
            if (rect.width === 0 && rect.height === 0) {
                return;
            }

            const headerCells = table.querySelectorAll('thead th').length;
            const lastCol = headerCells - 1;

            $(table).DataTable({
                responsive: false,
                pageLength: 10,
                lengthMenu: [10, 20, 50],
                autoWidth: false,
                order: [],
                searching: true,
                paging: true,
                pagingType: 'simple_numbers',
                dom: '<"dt-toolbar"f>rt<"dt-footer"p>',
                columnDefs: lastCol >= 0 ? [{ orderable: false, targets: [lastCol] }] : [],
                language: {
                    search: '',
                    searchPlaceholder: 'Search…',
                    paginate: { previous: 'Prev', next: 'Next' },
                    emptyTable: 'No records found',
                },
            });
        });

        return true;
    };

    // jQuery/DataTables are loaded from CDN with `defer`, so poll briefly.
    const ok = initDataTablesOnce();
    if (!ok) {
        const t = window.setInterval(() => {
            const done = initDataTablesOnce();
            if (done) window.clearInterval(t);
        }, 200);
        window.setTimeout(() => window.clearInterval(t), 3000);
    }
});
