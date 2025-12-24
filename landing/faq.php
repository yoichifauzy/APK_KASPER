<?php
// landing/faq.php
// FAQ page for Cash Coding (Landing) — Kaiadmin style
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>FAQ - Cash Coding - KASPER</title>
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
        .faq-card .card-body {
            padding: 1rem 1.25rem;
        }

        .faq-accordion .accordion-button {
            font-weight: 600;
        }

        .faq-note {
            font-size: 0.95rem;
            color: #6c757d;
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
                        <h3 class="fw-bold mb-3">FAQ — Cash Coding</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Landing</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">FAQ</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card faq-card">
                                <div class="card-header">
                                    <h4 class="card-title">Pertanyaan Umum</h4>
                                    <p class="faq-note mb-0">Informasi ringkas seputar role, fitur aplikasi, dan contoh usecase.</p>
                                </div>
                                <div class="card-body">
                                    <div class="accordion faq-accordion" id="faqAccordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faqHeading1">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                                                    Apa perbedaan role Admin, Operator, dan User (Mahasiswa)?
                                                </button>
                                            </h2>
                                            <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    <strong>Admin:</strong> Memiliki hak penuh untuk mengelola akun admin/operator, konfigurasi sistem, backup, dan melihat audit log. Cocok untuk manajemen dan pemilik aplikasi.
                                                    <br /><strong>Operator:</strong> Bertanggung jawab atas operasi harian — verifikasi pembayaran, pengelolaan transaksi, pencarian barcode, dan pembuatan laporan keuangan.
                                                    <br /><strong>User/Mahasiswa:</strong> Pengguna akhir yang dapat melihat saldo, melakukan pembayaran, mengikuti diskusi, dan mengirimkan pesan.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faqHeading2">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                                    Bagaimana alur pembayaran (usecase) di aplikasi?
                                                </button>
                                            </h2>
                                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    - User membuat/menjalankan pembayaran melalui antarmuka pembayaran (atau scan barcode).<br />
                                                    - Sistem menyimpan entri di tabel <code>pembayaran</code> dan terkait ke tabel <code>kas</code> untuk rekap. <br />
                                                    - Operator memverifikasi bukti (jika ada) dan menandai pembayaran sebagai diterima; laporan dan neraca diperbarui otomatis.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faqHeading3">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                                    Di mana saya dapat mengelola harga (Cash Price) dan label?
                                                </button>
                                            </h2>
                                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Pengelolaan harga dan label dilakukan di panel Operator / Admin (folder <code>operator/</code> atau <code>admin/</code>). Halaman landing menampilkan data dalam mode <em>view-only</em>. Untuk melakukan perubahan, silakan masuk sebagai Operator atau Admin dan buka menu <strong>Kelola Kas / Cash Price</strong>.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faqHeading4">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                                    Apa yang harus dilakukan jika terjadi error koneksi database?
                                                </button>
                                            </h2>
                                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    - Pastikan konfigurasi <code>config/database.php</code> benar (host, user, pass, nama DB).<br />
                                                    - Jika muncul error "mysqli object is already closed", biasanya karena koneksi ditutup sebelum layout menggunakan $conn; buka halaman yang relevan dan pastikan tidak memanggil <code>$conn->close()</code> sebelum include layout. <br />
                                                    - Hubungi admin server jika masalah konektivitas berlanjut.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faqHeading5">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                                    Bagaimana keamanan akses dan hak peran (authorization)?
                                                </button>
                                            </h2>
                                            <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Akses dikontrol melalui sesi dan helper di <code>config/cek_login.php</code>. Fungsi <code>otorisasi()</code> membatasi halaman berdasarkan role (admin, operator). Selalu logout setelah selesai menggunakan akun dengan hak tinggi.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faqHeading6">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
                                                    Bagaimana cara menghubungi dukungan atau berkontribusi pada project?
                                                </button>
                                            </h2>
                                            <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    Untuk dukungan, hubungi tim IT atau admin aplikasi (kontak tersedia pada halaman Contact). Untuk kontribusi kode, gunakan repository utama (owner: yoichifauzy) — fork, buat branch, dan ajukan pull request dengan deskripsi perubahan.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faqHeading7">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse7" aria-expanded="false" aria-controls="faqCollapse7">
                                                    Informasi Aplikasi (Lengkap)
                                                </button>
                                            </h2>
                                            <div id="faqCollapse7" class="accordion-collapse collapse" aria-labelledby="faqHeading7" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    <h6>Ringkasan</h6>
                                                    <p>APK_KASPER adalah aplikasi manajemen kas dan pembayaran berbasis web, dirancang untuk sekolah atau organisasi kecil. Fitur utamanya meliputi pencatatan kas (pemasukan/pengeluaran), modul pembayaran, verifikasi bukti, forum diskusi, dan laporan keuangan.</p>

                                                    <h6>Arsitektur & Teknologi</h6>
                                                    <ul>
                                                        <li>Frontend: HTML/CSS/Bootstrap (Kaiadmin theme), jQuery, DataTables.</li>
                                                        <li>Backend: PHP (procedural + simple organized includes), MySQL/MariaDB.</li>
                                                        <li>Libraries: html5-qrcode untuk pemindaian barcode pada halaman operator.</li>
                                                        <li>Folder utama: <code>operator/</code>, <code>admin/</code>, <code>landing/</code>, <code>config/</code>, <code>upload/</code>.</li>
                                                    </ul>

                                                    <h6>Database (sekilas)</h6>
                                                    <p>Beberapa tabel penting:</p>
                                                    <ul>
                                                        <li><code>pembayaran</code>: menyimpan transaksi pembayaran pengguna.</li>
                                                        <li><code>kas</code>: rekap kas (pemasukan / pengeluaran) yang dapat berhubungan dengan pembayaran.</li>
                                                        <li><code>kas_kategori</code>: kategori untuk entri kas.</li>
                                                        <li><code>user</code>: data pengguna (admin/operator/mahasiswa).</li>
                                                        <li><code>announcements</code>, <code>chat</code>, <code>discussion_topics</code> untuk fitur komunikasi.</li>
                                                    </ul>

                                                    <h6>API & Endpoints yang tersedia (read-only landing)</h6>
                                                    <p>Landing pages menyediakan API publik/internal untuk menampilkan data (view-only):</p>
                                                    <ul>
                                                        <li><code>/landing/api_cash_price.php</code>: data kas yang dipakai pada halaman Cash Price.</li>
                                                        <li><code>/landing/api_label_cash.php</code>: ringkasan pembayaran per tanggal/kategori (dipakai pada Label Cash).</li>
                                                        <li>Operator/admin memiliki endpoint CRUD di folder <code>operator/</code> dan <code>admin/</code> (memerlukan otorisasi session).</li>
                                                    </ul>

                                                    <h6>Keamanan & Otorisasi</h6>
                                                    <ul>
                                                        <li>Autentikasi berbasis sesi PHP (`config/cek_login.php`).</li>
                                                        <li>Fungsi helper <code>otorisasi()</code> membatasi akses halaman menurut role.</li>
                                                        <li>Jangan jalankan sistem di HTTP publik tanpa HTTPS; fitur kamera membutuhkan HTTPS atau localhost.</li>
                                                    </ul>

                                                    <h6>Deployment & Environment</h6>
                                                    <p>Dianjurkan:</p>
                                                    <ul>
                                                        <li>Web server: Apache atau Nginx dengan PHP 8+.</li>
                                                        <li>Database: MySQL 5.7+ atau MariaDB.</li>
                                                        <li>Pastikan folder <code>upload/</code> dapat ditulis untuk menyimpan bukti pembayaran.</li>
                                                    </ul>

                                                    <h6>Backup & Maintenance</h6>
                                                    <p>Lakukan backup database secara berkala (mysqldump atau backup DB provider). Simpan arsip file <code>upload/</code> jika bukti pembayaran penting.</p>

                                                    <h6>Logging & Troubleshooting</h6>
                                                    <ul>
                                                        <li>Periksa <code>php_error.log</code> / webserver log saat terjadi masalah.</li>
                                                        <li>Pesan umum: "mysqli object is already closed" — artinya kode menutup koneksi sebelum include layout; jangan panggil <code>$conn->close()</code> sebelum semua include selesai.</li>
                                                    </ul>

                                                    <h6>Extensibility</h6>
                                                    <p>Untuk menambah fitur baru, ikuti pola struktur file: gunakan <code>config/database.php</code> untuk koneksi, buat page di folder sesuai role, dan gunakan prepared statements untuk query.</p>

                                                    <h6>Usecases / Contoh Alur</h6>
                                                    <ol>
                                                        <li>Student melakukan pembayaran via halaman pembayaran — data tersimpan ke <code>pembayaran</code>.</li>
                                                        <li>Operator memverifikasi bukti/nominal — operator menggunakan halaman scan barcode untuk mempercepat pencarian.</li>
                                                        <li>Admin mengelola operator dan membuat laporan bulanan melalui menu admin.</li>
                                                    </ol>

                                                    <h6>Kontak & Kontribusi</h6>
                                                    <p>Untuk kontribusi, fork repo, buat branch, dan ajukan PR. Untuk dukungan operasional, hubungi admin sistem.</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title">Butuh bantuan cepat?</div>
                                </div>
                                <div class="card-body">
                                    <p class="small text-muted">Jika Anda admin/operator dan membutuhkan bantuan teknis, pertimbangkan langkah berikut:</p>
                                    <ul class="small">
                                        <li>Periksa error log server (PHP & MySQL).</li>
                                        <li>Pastikan konfigurasi database di <code>config/database.php</code> up-to-date.</li>
                                        <li>Gunakan halaman operator untuk tindakan CRUD—landing bersifat informatif.</li>
                                    </ul>
                                    <hr />
                                    <a href="../admin/manage_admin.php" class="btn btn-label-primary btn-sm">Panel Admin</a>
                                    <a href="../operator/dashboard_operator.php" class="btn btn-label-success btn-sm ms-2">Panel Operator</a>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <div class="card-title">Catatan Sistem</div>
                                </div>
                                <div class="card-body small text-muted">
                                    - Pastikan situs berjalan di <strong>localhost</strong> atau <strong>HTTPS</strong> untuk mengaktifkan fitur kamera (scanner).<br />
                                    - Backup database secara berkala untuk mencegah kehilangan data.
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