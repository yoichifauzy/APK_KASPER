<?php
// landing/contact.php
// Contact page for Cash Coding (Landing) — Kaiadmin style
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Contact - Cash Coding - KASPER</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"]
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <style>
        .contact-card .card-body {
            padding: 1rem 1.25rem;
        }

        .contact-method {
            font-size: 0.95rem;
        }

        .contact-hero {
            background: linear-gradient(90deg, #0d6efd0f, #6610f20f);
            border-left: 4px solid #0d6efd;
        }

        .contact-list .list-group-item {
            border: 0;
            padding-left: 0;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include_once __DIR__ . '/layout_landing/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="../index.php" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
                        <div class="nav-toggle"><button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button><button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>

                <?php include_once __DIR__ . '/layout_landing/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Contact — Cash Coding</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Landing</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Contact</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card contact-card">
                                <div class="card-header contact-hero">
                                    <div class="card-title">
                                        <h4>Kontak Developer & Dukungan</h4>
                                        <p class="mb-0 text-muted">Informasi kontak developer dan saluran dukungan teknis untuk aplikasi KASPER.</p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3">Jika Anda memerlukan bantuan teknis, ingin melaporkan bug, atau ingin berkolaborasi, silakan gunakan salah satu saluran di bawah ini. Mohon sertakan detail langkah reproduksi dan tangkapan layar bila memungkinkan.</p>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6 class="mb-2">Informasi Developer</h6>
                                            <ul class="list-unstyled contact-method">
                                                <li><strong>Nama:</strong> Yoichi Fauzy (owner repo)</li>
                                                <li><strong>GitHub:</strong> <a href="https://github.com/yoichifauzy" target="_blank">github.com/yoichifauzy</a></li>
                                                <li><strong>Email (support):</strong> <a href="mailto:dev@yourdomain.example">dev@yourdomain.example</a> <small class="text-muted">(ganti dengan email resmi)</small></li>
                                                <li><strong>Telegram/WA:</strong> <span class="text-muted">+62 8xx xxxx xxxx</span> <small class="text-muted">(opsional)</small></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-2">Jam Dukungan</h6>
                                            <p class="small text-muted mb-2">Senin–Jumat, 09:00–17:00 WIB. Untuk permintaan darurat, sertakan kata <strong>URGENT</strong> pada subject email.</p>

                                            <h6 class="mb-2">Tiket & Pelaporan Bug</h6>
                                            <p class="small text-muted">Untuk pelaporan bug dan fitur request, buka issue pada repository GitHub dan sertakan langkah reproduksi, versi aplikasi, serta environment (PHP, MySQL, browser).</p>
                                            <a href="https://github.com/yoichifauzy/APK_KASPER/issues" class="btn btn-outline-primary btn-sm" target="_blank">Buka Issue di GitHub</a>
                                        </div>
                                    </div>

                                    <hr />

                                    <h6>Informasi Teknis Singkat (untuk tim IT)</h6>
                                    <ul>
                                        <li>Lokasi file konfigurasi DB: <code>config/database.php</code></li>
                                        <li>Folder upload bukti pembayaran: <code>upload/</code> (pastikan writable oleh webserver)</li>
                                        <li>File log PHP: periksa <code>php_error.log</code> atau file log webserver</li>
                                    </ul>

                                    <hr />

                                    <h6>Cara Cepat Menghubungi</h6>
                                    <ol>
                                        <li>Kirim email ke <a href="mailto:dev@yourdomain.example">dev@yourdomain.example</a> dengan subject "Support: [HALAMAN] [SINGKAT DESKRIPSI]".</li>
                                        <li>Untuk isu kode: buat issue di GitHub dan tag <code>bug</code> atau <code>enhancement</code>.</li>
                                        <li>Jika ini permintaan fitur komersial, sebutkan kebutuhan dan anggaran estimasi di email.</li>
                                    </ol>

                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <div class="card-title">Kontribusi & Lisensi</div>
                                </div>
                                <div class="card-body">
                                    <p class="small text-muted">Project ini berada pada repository publik. Untuk berkontribusi:</p>
                                    <ol>
                                        <li>Fork repository <code>yoichifauzy/APK_KASPER</code>.</li>
                                        <li>Buat branch feature/bugfix dengan nama jelas.</li>
                                        <li>Ajukan pull request dengan deskripsi perubahan dan testing steps.</li>
                                    </ol>
                                    <p class="small text-muted mb-0">Catatan lisensi: Pastikan memeriksa file <code>LICENSE</code> di repo untuk aturan distribusi.</p>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title">Saluran Tambahan</div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group contact-list">
                                        <li class="list-group-item">Github: <a href="https://github.com/yoichifauzy" target="_blank">github.com/yoichifauzy</a></li>
                                        <li class="list-group-item">Email Support: <a href="mailto:dev@yourdomain.example">dev@yourdomain.example</a></li>
                                        <li class="list-group-item">Docs / README: <a href="../README.md">README.md (repo)</a></li>
                                    </ul>
                                    <hr />
                                    <a href="mailto:dev@yourdomain.example" class="btn btn-primary btn-block">Kirim Email Dukungan</a>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <div class="card-title">Catatan Keamanan</div>
                                </div>
                                <div class="card-body small text-muted">
                                    - Untuk permintaan data sensitif, verifikasi identitas pengguna sebelum memberikan akses. <br />
                                    - Jangan mengirimkan password melalui email biasa.
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <footer class="footer">
                <?php include_once __DIR__ . '/layout_landing/footer.php'; ?>
            </footer>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>

</html>