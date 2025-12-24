<?php
// landing/programming.php
// Halaman Anak Jurusan: Programming (Teknologi Informasi)
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Jurusan Programming - Teknologi Informasi</title>
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
        .hero {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .hero .caption {
            padding: 1.25rem;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .feature-list li {
            margin-bottom: 0.5rem;
        }

        .tech-badge {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
        }

        .img-sample {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 6px;
        }

        .card-feature {
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
        }

        pre.sample-code {
            background: #0b1220;
            color: #d1e8ff;
            padding: 0.75rem;
            border-radius: 6px;
            overflow: auto;
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
                    <!-- Hero -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="hero position-relative">
                                <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1600&q=80" alt="Programming" class="w-100" style="height:300px;object-fit:cover;">
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="padding:1.25rem;">
                                    <div class="text-white">
                                        <h1 class="fw-bold">Jurusan Programming</h1>
                                        <p class="mb-0">Fokus pada pengembangan perangkat lunak: Web, Mobile, API, Cloud, dan elektronika sederhana (Arduino).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-8">

                            <div class="card card-feature p-3 mb-3">
                                <h4 class="section-title">Tentang Anak Jurusan Programming</h4>
                                <p>Program Programming adalah cabang dari Jurusan Teknologi Informasi yang menitikberatkan pada kemampuan membuat perangkat lunak, membangun API, memanfaatkan layanan cloud (Google Cloud, AWS), serta integrasi dengan perangkat keras sederhana seperti Arduino. Pembelajaran mencakup bahasa pemrograman populer seperti Java, JavaScript, Dart (Flutter), PHP, dan Python, serta praktik terbaik dalam pengembangan perangkat lunak modern.</p>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h4 class="section-title">Bahasa & Teknologi yang Dipelajari</h4>
                                <p>Fokus utama kurikulum mencakup:</p>
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <div class="p-2">
                                            <div class="fw-bold">Java</div>
                                            <div class="text-muted small">Pemrograman berorientasi objek, aplikasi backend, dan Android dasar (Java/Kotlin sebagai perbandingan).</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="p-2">
                                            <div class="fw-bold">JavaScript</div>
                                            <div class="text-muted small">Dasar JS, DOM, ES6+, dan framework modern (React atau Vue) untuk pengembangan web interaktif.</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="p-2">
                                            <div class="fw-bold">Dart (Flutter)</div>
                                            <div class="text-muted small">Pengembangan aplikasi mobile cross-platform menggunakan Flutter (Dart) — UI cepat & performa native.</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="p-2">
                                            <div class="fw-bold">PHP</div>
                                            <div class="text-muted small">Pengembangan server-side untuk website & API (Laravel/CodeIgniter sebagai contoh).</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="p-2">
                                            <div class="fw-bold">Python</div>
                                            <div class="text-muted small">Scripting, data processing, backend (Flask/Django), dan otomasi. Juga dasar machine learning pengantar.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="fw-bold mb-2">Contoh project yang dipelajari:</div>
                                    <ul class="feature-list">
                                        <li>Aplikasi Web: Sistem Informasi sederhana (CRUD, autentikasi, upload file).</li>
                                        <li>Aplikasi Mobile: Flutter app (to-do app, katalog produk) dan integrasi API.</li>
                                        <li>Proyek Fullstack: Backend API (REST/GraphQL) + Frontend SPA.</li>
                                        <li>Integrasi IoT: Arduino membaca sensor dan mengirim data ke server sederhana.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h4 class="section-title">API, Cloud & DevOps</h4>
                                <p>Pengajaran mencakup pembuatan dan konsumsi API, serta pemanfaatan layanan cloud untuk deployment, penyimpanan, dan fungsi serverless.</p>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <strong>API</strong>
                                        <p class="small text-muted">RESTful API menggunakan JSON, dokumentasi (OpenAPI/Swagger), otentikasi (JWT), dan praktik versioning.</p>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <strong>Google Cloud</strong>
                                        <p class="small text-muted">Penggunaan Cloud Run, App Engine, Firestore/Cloud SQL, dan fitur serverless untuk deployment proyek siswa.</p>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <strong>AWS</strong>
                                        <p class="small text-muted">S3, Lambda, EC2, RDS sebagai alternatif untuk memahami arsitektur cloud.</p>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <strong>DevOps Basics</strong>
                                        <p class="small text-muted">CI/CD sederhana (GitHub Actions), containerization dengan Docker, dan deployment ke layanan cloud.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h4 class="section-title">Networking & Arduino</h4>
                                <p>Dasar-dasar jaringan komputer (IP, subnetting, routing), alat analisa (Wireshark), serta pengenalan Arduino untuk prototyping hardware.</p>
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=60" alt="Networking" class="img-sample">
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <img src="https://images.unsplash.com/photo-1581091870626-3b7a3b3f7b7b?auto=format&fit=crop&w=800&q=60" alt="Arduino" class="img-sample">
                                    </div>
                                </div>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h4 class="section-title">Contoh Kode Singkat (API sederhana - Node/Express)</h4>
                                <pre class="sample-code">// contoh singkat: endpoint GET
app.get('/api/hello', (req, res) =&gt; {
  res.json({ message: 'Halo dari API' });
});
</pre>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h4 class="section-title">Kurikum & Jadwal</h4>
                                <p>Struktur kursus bisa meliputi modul praktik mingguan dan proyek akhir. Contoh topik per semester tersedia atas permintaan.</p>
                            </div>

                        </div>

                        <div class="col-lg-4">
                            <div class="card card-feature p-3 mb-3">
                                <h5 class="section-title">Ringkasan Cepat</h5>
                                <ul class="list-unstyled small">
                                    <li><strong>Bahasa:</strong> Java, JavaScript, Dart, PHP, Python</li>
                                    <li><strong>Platform:</strong> Web, Mobile (Flutter), Cloud (GCP/AWS)</li>
                                    <li><strong>Tools:</strong> Docker, GitHub, Postman, Wireshark</li>
                                    <li><strong>Hardware:</strong> Arduino (sensor dasar)</li>
                                </ul>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h5 class="section-title">Contoh Project</h5>
                                <ul class="small">
                                    <li>Sistem Informasi Sekolah (Laravel/PHP + MySQL)</li>
                                    <li>Mobile App Flutter untuk katalog & pendaftaran</li>
                                    <li>REST API dengan Node/Express atau Flask</li>
                                    <li>IoT sederhana: sensor DHT11 + server</li>
                                </ul>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h5 class="section-title">Sumber Belajar</h5>
                                <a href="https://developer.android.com/" target="_blank" rel="noopener">Android Developers</a>
                                <a href="https://flutter.dev/" target="_blank" rel="noopener">Flutter (Dart)</a>
                                <a href="https://nodejs.org/" target="_blank" rel="noopener">Node.js</a>
                                <a href="https://docs.python.org/3/" target="_blank" rel="noopener">Python Docs</a>
                                <a href="https://aws.amazon.com/" target="_blank" rel="noopener">AWS</a>
                                <a href="https://cloud.google.com/" target="_blank" rel="noopener">Google Cloud</a>
                            </div>

                            <div class="card card-feature p-3 mb-3">
                                <h5 class="section-title">Kontak</h5>
                                <p class="small text-muted mb-1">Untuk info lebih lanjut hubungi:</p>
                                <p class="mb-0"><strong>Email:</strong> prog-office@sekolah.example</p>
                                <p class="mb-0"><strong>Telp:</strong> (021) 555-0123</p>
                                <div class="mt-2"><a class="btn btn-sm btn-primary" href="mailto:prog-office@sekolah.example">Kirim Email</a></div>
                            </div>

                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-center small text-muted">Gambar dari Unsplash; konten ringkasan diadaptasi dari sumber-sumber publik (dokumentasi resmi & tutorial online).</div>
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