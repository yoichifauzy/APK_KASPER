</div>
<!--   Core JS Files   -->
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Chart JS -->
<script src="../assets/js/plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

<!-- Datatables -->
<script src="../assets/js/plugin/datatables/datatables.min.js"></script>

<!-- Bootstrap Notify -->
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- jQuery Vector Maps -->
<script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
<script src="../assets/js/plugin/jsvectormap/world.js"></script>

<!-- Sweet Alert -->
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="../assets/js/setting-demo.js"></script>
<script src="../assets/js/demo.js"></script>
<script>
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: 'line',
        height: '70',
        width: '100%',
        lineWidth: '2',
        lineColor: '#177dff',
        fillColor: 'rgba(23, 125, 255, 0.14)'
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: 'line',
        height: '70',
        width: '100%',
        lineWidth: '2',
        lineColor: '#f3545d',
        fillColor: 'rgba(243, 84, 93, .14)'
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: 'line',
        height: '70',
        width: '100%',
        lineWidth: '2',
        lineColor: '#ffa534',
        fillColor: 'rgba(255, 165, 52, .14)'
    });
</script>
<!-- Sidebar behavior moved from sidebar include: logout confirm, mobile close, and fallback bindings -->
<script>
    (function() {
        // Ensure jQuery is present
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;

        // Logout handling (uses Bootstrap modal if present, else SweetAlert, else direct)
        var logoutLink = document.getElementById('logout-user');
        var logoutModal = document.getElementById('logoutModal');
        var confirmBtn = document.getElementById('confirm-logout');
        var bsModal = null;
        if (typeof bootstrap !== 'undefined' && logoutModal) {
            try {
                bsModal = new bootstrap.Modal(logoutModal);
            } catch (e) {
                bsModal = null;
            }
        }
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                var url = logoutLink.getAttribute('data-logout-url') || '../auth/logout.php';
                if (bsModal) {
                    bsModal.show();
                    return;
                }
                if (typeof swal !== 'undefined') {
                    swal({
                        title: 'Konfirmasi Logout',
                        text: 'Apakah Anda yakin ingin keluar dari sesi ini?',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'Batal',
                                visible: true,
                                className: 'btn btn-secondary'
                            },
                            confirm: {
                                text: 'Logout',
                                visible: true,
                                className: 'btn btn-danger'
                            }
                        },
                        dangerMode: true
                    }).then(function(willLogout) {
                        if (willLogout) window.location.href = url;
                    });
                    return;
                }
                window.location.href = url;
            });
        }
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                var url = logoutLink ? logoutLink.getAttribute('data-logout-url') || '../auth/logout.php' : '../auth/logout.php';
                try {
                    if (bsModal) bsModal.hide();
                } catch (e) {}
                window.location.href = url;
            });
        }

        // Mobile-friendly sidebar behavior and fallback bindings
        function closeMobileSidebar() {
            $('html').removeClass('sidenav-toggled topbar-toggled');
            $('body').removeClass('sidebar_minimize');
        }

        // Bind close on nav link click (small screens) and clicking outside
        $('.sidebar .nav a').on('click', function() {
            if ($(window).width() < 992) closeMobileSidebar();
        });
        $(document).on('click touchstart', function(e) {
            if ($(window).width() >= 992) return;
            var $t = $(e.target);
            if ($t.closest('.sidebar').length === 0 && $t.closest('.nav-toggle').length === 0 && $t.closest('.btn-toggle').length === 0) {
                closeMobileSidebar();
            }
        });

        // Fallback bindings for toggle buttons (if kaiadmin scripts don't attach)
        $('.btn-toggle.toggle-sidebar').off('click.fallback').on('click.fallback', function() {
            $('body').toggleClass('sidebar_minimize');
        });
        $('.btn-toggle.sidenav-toggler').off('click.fallback').on('click.fallback', function() {
            $('html').toggleClass('sidenav-toggled');
        });
        $('.topbar-toggler.more').off('click.fallback').on('click.fallback', function() {
            $('html').toggleClass('topbar-toggled');
        });

        // Initialize custom scrollbar if plugin available
        try {
            if ($.fn.scrollbar) {
                $('.sidebar-wrapper.scrollbar, .sidebar-wrapper.scrollbar-inner').scrollbar();
            }
        } catch (e) {}
    })();
</script>
</body>

</html>