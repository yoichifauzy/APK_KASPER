<!-- Ensure main-panel uses column layout so footer stays at bottom
     without overlapping content. This keeps footer tidy and sticky
     when page content is short, while following content when long. -->
<style>
    .main-panel {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .main-panel>.container,
    .main-panel>.page-inner,
    .main-panel>.main-content {
        flex: 1 0 auto;
    }

    footer.footer {
        flex-shrink: 0;
    }

    /* Small visual tidy defaults */
    footer.footer .container-fluid {
        align-items: center;
        gap: .5rem;
    }
</style>

<footer class="footer bg-white border-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">
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
        <div class="small text-muted">
            Distributed by
            <a target="_blank" href="https://themewagon.com/">ThemeWagon</a>.
        </div>
    </div>
</footer>

<!-- Logout modal (Bootstrap / Kaiadmin style) -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Anda yakin ingin logout dari halaman admin? Anda akan diarahkan ke halaman depan setelah logout.
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
        // Open the Bootstrap modal when sidebar logout link clicked.
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
            var logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            logoutModal.show();
        }, false);
    })();
</script>