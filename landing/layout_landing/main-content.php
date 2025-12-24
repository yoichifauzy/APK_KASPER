<div class="page-inner">
    <style>
        /* Landing main content small polish */
        .landing-summary {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        }

        .landing-summary .summary-body {
            padding: 1rem 1.25rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .landing-summary .summary-text {
            flex: 1;
        }

        .landing-summary .summary-actions {
            flex: 0 0 auto;
        }

        .landing-summary .app-icon {
            width: 72px;
            height: 72px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: #fff;
            font-size: 28px;
        }

        .landing-cta {
            min-width: 140px;
        }

        .stat-card .icon-big {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        @media (max-width:768px) {
            .landing-summary .summary-body {
                flex-direction: column;
                align-items: flex-start;
            }

            .landing-summary .summary-actions {
                width: 100%;
            }
        }
    </style>
    <div
        class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Dashboard</h3>
            <h6 class="op-7 mb-2">Free Bootstrap 5 Admin Dashboard</h6>
        </div>

    </div>
    <!-- App summary + login -->


    <!-- App summary + Login CTA (styled) -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card landing-summary">
                <div class="summary-body">
                    <div class="app-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="summary-text">
                        <h4 class="mb-1">APK_KAS — Aplikasi Manajemen Kas & Sekolah</h4>
                        <p class="text-muted small mb-1">Kelola transaksi kas, laporan keuangan, forum diskusi, sistem poin, dan administrasi operator serta admin.</p>
                        <div class="text-muted small">Dibangun dengan PHP & MySQL dan antarmuka Kaiadmin/Bootstrap. Fitur: transaksi, export Excel/PDF, kalender, roadmap, dan API landing read-only.</div>
                    </div>
                    <div class="summary-actions text-end">
                        <a href="/APK_KAS/auth/login.php" class="btn btn-primary landing-cta mb-2">Login</a>
                        <div><a href="/APK_KAS/landing/faq.php" class="btn btn-outline-secondary btn-sm">Pelajari lebih lanjut</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>