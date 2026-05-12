    </div><!-- /.admin-inner -->
</div><!-- /.admin-content -->
</div><!-- /.admin-wrapper -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
// Admin-specific JS
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar overlay on mobile
    const sidebar = document.getElementById('adminSidebar');
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992 && sidebar && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && e.target.id !== 'sidebarToggle') {
                sidebar.classList.remove('open');
            }
        }
    });

    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm || 'Apakah Anda yakin?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
</body>
</html>
