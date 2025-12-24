<footer class="footer">
    <div class="container-fluid d-flex justify-content-between">
        <nav class="pull-left">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="http://www.themekita.com">
                        ThemeKita
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"> Help </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"> Licenses </a>
                </li>
            </ul>
        </nav>
        <div class="copyright">
            2024, made with <i class="fa fa-heart heart text-danger"></i> by
            <a href="http://www.themekita.com">ThemeKita</a>
        </div>
        <div>
            Distributed by
            <a target="_blank" href="https://themewagon.com/">ThemeWagon</a>.
        </div>
    </div>
</footer>

<!-- Logout modal (Bootstrap) -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Anda yakin ingin logout dari halaman ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="logoutConfirmBtn" href="../auth/logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Intercept clicks on the sidebar logout link and show modal
        document.addEventListener('click', function(e) {
            var el = e.target;
            while (el && el !== document.body) {
                if (el.id === 'logout-link') break;
                el = el.parentElement;
            }
            if (!el || el.id !== 'logout-link') return;
            e.preventDefault();
            var logoutUrl = el.getAttribute('data-logout-url') || el.getAttribute('href') || '../auth/logout.php';
            var confirmBtn = document.getElementById('logoutConfirmBtn');
            if (confirmBtn) confirmBtn.setAttribute('href', logoutUrl);
            var logoutModalEl = document.getElementById('logoutModal');
            if (logoutModalEl) {
                var logoutModal = new bootstrap.Modal(logoutModalEl);
                logoutModal.show();
            }
        }, false);
    })();
</script>

<!-- Custom template (Settings) available on all operator pages -->
<div class="custom-template" id="customTemplate">
    <div class="title">Settings</div>
    <div class="custom-content">
        <!-- Settings controls removed -->
    </div>
    <div class="custom-toggle">
        <i class="icon-settings"></i>
    </div>
</div>

<!-- Ensure settings JS runs on operator pages -->
<script src="../assets/js/setting-demo.js"></script>
<script src="../assets/js/demo.js"></script>