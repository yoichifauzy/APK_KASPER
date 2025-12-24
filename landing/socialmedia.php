<?php
// landing/socialmedia.php
// Halaman: Badge / Thumbnail untuk social media & developer tools
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Sosial & Developer Tools</title>
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        .sm-card {
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .thumb {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
        }

        .card-body {
            padding: 0.8rem 0.9rem;
        }

        .sm-title {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sm-desc {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.35rem;
        }

        .sm-links {
            margin-top: 0.6rem;
        }

        .badge-tool {
            background: #0d6efd;
            color: #fff;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
            font-size: 0.8rem;
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

            <div class="container mt-3">
                <div class="page-inner">
                    <div class="page-header d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="fw-bold">Sosial & Developer Tools</h3>
                            <p class="text-muted small mb-0">Kartu ringkas dengan thumbnail dan referensi resmi: YouTube, TikTok, Instagram, GitHub, Postman.</p>
                        </div>
                    </div>

                    <div class="grid mb-3">
                        <!-- YouTube -->
                        <div class="sm-card">
                            <img class="thumb" src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1200&q=80" alt="YouTube">
                            <div class="card-body">
                                <div class="sm-title"><i class="fab fa-youtube fa-lg text-danger"></i> YouTube</div>
                                <div class="sm-desc">Platform video terbesar untuk tutorial, kursus singkat, dan channel resmi. Gunakan channel resmi seperti Google Developers, freeCodeCamp, Traversy Media untuk materi programming.</div>
                                <div class="sm-links">
                                    <a href="https://www.youtube.com/" target="_blank" rel="noopener" class="small">Kunjungi YouTube</a>
                                </div>
                            </div>
                        </div>

                        <!-- TikTok -->
                        <div class="sm-card">
                            <img class="thumb" src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/tiktok.svg" alt="TikTok logo">
                            <div class="card-body">
                                <div class="sm-title"><i class="fab fa-tiktok fa-lg" style="color:#000"></i> TikTok</div>
                                <div class="sm-desc">Platform konten singkat yang juga memuat micro-tutorial dan tips singkat. Cari tagar edukasi seperti #programming, #learncoding.</div>
                                <div class="sm-links"><a href="https://www.tiktok.com/" target="_blank" rel="noopener" class="small">Kunjungi TikTok</a></div>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="sm-card">
                            <img class="thumb" src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/instagram.svg" alt="Instagram logo">
                            <div class="card-body">
                                <div class="sm-title"><i class="fab fa-instagram fa-lg text-warning"></i> Instagram</div>
                                <div class="sm-desc">Visual storytelling dan dokumentasi proyek. Banyak developer membagikan snippet, carousel tutorial, dan portofolio singkat di Instagram.</div>
                                <div class="sm-links"><a href="https://www.instagram.com/" target="_blank" rel="noopener" class="small">Kunjungi Instagram</a></div>
                            </div>
                        </div>

                        <!-- GitHub -->
                        <div class="sm-card">
                            <img class="thumb" src="https://images.unsplash.com/photo-1517433456452-f9633a875f6f?auto=format&fit=crop&w=1200&q=80" alt="GitHub">
                            <div class="card-body">
                                <div class="sm-title"><i class="fab fa-github fa-lg"></i> GitHub</div>
                                <div class="sm-desc">Platform utama untuk menyimpan kode, kolaborasi, issue tracking, dan hosting portofolio (GitHub Pages). Referensi: official docs dan GitHub Guides.</div>
                                <div class="sm-links"><a href="https://github.com/" target="_blank" rel="noopener" class="small">Kunjungi GitHub</a></div>
                            </div>
                        </div>

                        <!-- Postman removed per request -->

                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center small text-muted">Sumber dan rujukan diambil dari dokumentasi resmi platform dan materi edukasi publik (YouTube, GitHub Guides, Instagram creators). Gambar diambil dari Unsplash and icon CDN (hotlink).</div>
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
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
</body>

</html>