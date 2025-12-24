<?php
// landing/teknologiinformasi.php
// Halaman Jurusan Teknologi Informasi - lengkap, detail, terstruktur
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Jurusan Teknologi Informasi</title>
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
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .hero .overlay {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.25));
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 0.75rem;

            .card-feature {
                border-radius: 8px;
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
            }

            .resources a {
                display: block;
                margin-bottom: 0.35rem;
            }

            .img-sample {
                width: 100%;
                height: 180px;
                object-fit: cover;
                border-radius: 6px;
            }

            .badge-topic {
                background: #0d6efd;
                color: #fff;
                padding: 0.35rem 0.6rem;
                border-radius: 6px;
                font-size: 0.8rem;
            }
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
                                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1600&q=80" alt="Teknologi Informasi" class="w-100" style="height:300px;object-fit:cover;">
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="padding:1.25rem;">
                                    <div class="text-white">
                                        <h1 class="fw-bold">Jurusan Teknologi Informasi</h1>
                                        <p class="mb-0">Membangun kompetensi digital: pemrograman, jaringan, keamanan siber, dan pengembangan aplikasi.</p>
                                        <div class="mt-2">
                                            <span class="badge-topic me-2">Kompetensi Kejuruan</span>
                                            <span class="badge-topic">Proyek Kolaboratif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <!-- Overview -->
                            <div class="card card-feature mb-3 p-3">
                                <h4 class="section-title">Ringkasan Jurusan</h4>
                                <p>Jurusan Teknologi Informasi mempersiapkan peserta didik menjadi tenaga yang memahami dasar-dasar teknologi informasi dan komunikasi, termasuk pemrograman, administrasi jaringan, pengembangan web, database, serta prinsip keamanan informasi. Kurikulum biasanya mengombinasikan teori dan praktik melalui laboratorium, proyek, dan magang.</p>

                                <h5 class="mt-3">Tujuan Pembelajaran</h5>
                                <ul>
                                    <li>Menguasai dasar pemrograman dan pengembangan aplikasi sederhana.</li>
                                    <li>Mampu merancang dan mengelola jaringan komputer dasar.</li>
                                    <li>Mengerti konsep basis data dan penerapannya.</li>
                                    <li>Mengenal prinsip keamanan siber dasar dan praktik aman.</li>
                                    <li>Mengembangkan soft skills seperti kerja tim, manajemen proyek, dan komunikasi teknis.</li>
                                </ul>

                                <h5 class="mt-3">Kurikulum Singkat</h5>
                                <div class="accordion" id="curriculumAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                Semester 1-2: Dasar-dasar & Pemrograman
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#curriculumAccordion">
                                            <div class="accordion-body">
                                                Mata pelajaran: Matematika dasar, Algoritma & Pemrograman (Python), Pengenalan Sistem Komputer, Dasar Jaringan.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                Semester 3-4: Pengembangan Aplikasi & Basis Data
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#curriculumAccordion">
                                            <div class="accordion-body">
                                                Mata pelajaran: Pengembangan Web (HTML/CSS/JS), Pemrograman Berorientasi Objek (Java/PHP), Basis Data (MySQL), Praktikum Lab.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Semester 5-6: Jaringan, Keamanan & Proyek Akhir
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#curriculumAccordion">
                                            <div class="accordion-body">
                                                Mata pelajaran: Jaringan Lanjut, Keamanan Siber Dasar, DevOps & Deployment, Proyek Akhir (kerjasama industri/ magang).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tools & Labs -->
                            <div class="card card-feature mb-3 p-3">
                                <h4 class="section-title">Laboratorium & Tools</h4>
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <div class="d-flex gap-2 align-items-start">
                                            <i class="fab fa-python fa-2x text-primary"></i>
                                            <div>
                                                <strong>Bahasa Pemrograman</strong>
                                                <div class="text-muted small">Python, Java, PHP, JavaScript</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="d-flex gap-2 align-items-start">
                                            <i class="fas fa-database fa-2x text-success"></i>
                                            <div>
                                                <strong>Basis Data</strong>
                                                <div class="text-muted small">MySQL, PostgreSQL, SQLite</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="d-flex gap-2 align-items-start">
                                            <i class="fas fa-network-wired fa-2x text-warning"></i>
                                            <div>
                                                <strong>Jaringan</strong>
                                                <div class="text-muted small">Cisco Packet Tracer, Wireshark</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="d-flex gap-2 align-items-start">
                                            <i class="fas fa-shield-alt fa-2x text-danger"></i>
                                            <div>
                                                <strong>Keamanan</strong>
                                                <div class="text-muted small">Praktik keamanan dasar, pengujian penetrasi sederhana</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Projects -->
                            <div class="card card-feature mb-3 p-3">
                                <h4 class="section-title">Proyek & Portofolio</h4>
                                <p>Peserta didik didorong menyelesaikan proyek praktis sebagai bukti kompetensi. Contoh proyek:</p>
                                <ul>
                                    <li>Aplikasi web sederhana (sistem informasi sekolah, perpustakaan).</li>
                                    <li>Situs portofolio pribadi dengan hosting gratis / GitHub Pages.</li>
                                    <li>Mini-project IoT sederhana terhubung dengan backend.</li>
                                    <li>Proyek keamanan: analisa kerentanan sederhana pada lab isolasi.</li>
                                </ul>

                                <div class="row g-2 mt-2">
                                    <div class="col-sm-6">
                                        <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&q=60" alt="Project web" class="img-sample">
                                    </div>
                                    <div class="col-sm-6">
                                        <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=800&q=60" alt="Coding teamwork" class="img-sample">
                                    </div>
                                </div>
                            </div>

                            <!-- Career Paths -->
                            <div class="card card-feature mb-3 p-3">
                                <h4 class="section-title">Peluang Karir</h4>
                                <p>Lulusan dapat bekerja sebagai: </p>
                                <div class="row">
                                    <div class="col-sm-6 mb-2"><i class="fas fa-code text-primary me-2"></i> Web Developer</div>
                                    <div class="col-sm-6 mb-2"><i class="fas fa-server text-success me-2"></i> Database Administrator</div>
                                    <div class="col-sm-6 mb-2"><i class="fas fa-network-wired text-warning me-2"></i> Network Technician</div>
                                    <div class="col-sm-6 mb-2"><i class="fas fa-user-shield text-danger me-2"></i> IT Support / Security Analyst (entry)</div>
                                </div>
                            </div>

                            <!-- FAQs -->
                            <div class="card card-feature mb-3 p-3">
                                <h4 class="section-title">FAQ</h4>
                                <div class="accordion" id="faqAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="faq1">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="false">Apa saja prasyarat mengikuti jurusan ini?</button>
                                        </h2>
                                        <div id="faqOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body">Minat pada teknologi, kemampuan dasar matematika, dan kemauan praktik. Sekolah menyediakan pengenalan awal untuk yang belum memiliki pengalaman.</div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="faq2">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false">Apakah ada sertifikasi yang diberikan?</button>
                                        </h2>
                                        <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body">Beberapa sekolah bekerjasama dengan penyelenggara sertifikasi (mis. Cisco, Microsoft) untuk program tambahan; namun kurikulum inti fokus pada kompetensi dasar.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end col-lg-8 -->

                        <div class="col-lg-4">
                            <!-- Quick facts -->
                            <div class="card card-feature mb-3 p-3">
                                <h5 class="section-title">Fakta Singkat</h5>
                                <ul class="list-unstyled small">
                                    <li><strong>Durasi:</strong> 2-3 tahun (tergantung program)</li>
                                    <li><strong>Praktikum:</strong> 60% waktu pembelajaran</li>
                                    <li><strong>Magang:</strong> Tersedia di semester akhir</li>
                                    <li><strong>Kerjasama:</strong> Industri lokal & penyelenggara IT</li>
                                </ul>
                            </div>

                            <!-- Resources -->
                            <div class="card card-feature mb-3 p-3 resources">
                                <h5 class="section-title">Referensi & Sumber Belajar</h5>
                                <a href="https://developer.mozilla.org/" target="_blank" rel="noopener">MDN Web Docs — Web development</a>
                                <a href="https://www.w3schools.com/" target="_blank" rel="noopener">W3Schools — Tutorial HTML/CSS/JS</a>
                                <a href="https://www.freecodecamp.org/" target="_blank" rel="noopener">freeCodeCamp — Projects & Exercises</a>
                                <a href="https://www.coursera.org/" target="_blank" rel="noopener">Coursera — Kursus IT & CS</a>
                                <a href="https://unsplash.com/s/photos/programming" target="_blank" rel="noopener">Unsplash — Gambar relevan</a>
                            </div>

                            <!-- Contact / Enrollment Info -->
                            <div class="card card-feature p-3 mb-3">
                                <h5 class="section-title">Kontak & Pendaftaran</h5>
                                <p class="small text-muted mb-1">Untuk informasi pendaftaran, silakan hubungi:</p>
                                <p class="mb-0"><strong>Email:</strong> ti-office@sekolah.example</p>
                                <p class="mb-0"><strong>Telp:</strong> (021) 555-0100</p>
                                <div class="mt-2"><a class="btn btn-sm btn-primary" href="mailto:ti-office@sekolah.example">Kirim Email</a></div>
                            </div>

                        </div> <!-- end col-lg-4 -->
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-center small text-muted">Sumber referensi publik diambil dari dokumentasi dan sumber-sumber pendidikan umum (MDN, freeCodeCamp, Coursera, Unsplash).</div>
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
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

</body>

</html>