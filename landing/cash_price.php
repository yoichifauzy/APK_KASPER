<?php
// landing/cash_price.php
// Halaman Cash Coding / Cash Price pada landing, mengikuti style Kaiadmin
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Cash Coding / Cash Price - KASPER</title>
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
        .card-kaidmin {
            border-left: .35rem solid #4e73df;
        }

        .price-table th,
        .price-table td {
            vertical-align: middle;
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
                        <a href="../index.php" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                        </div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>

                <?php include_once __DIR__ . '/layout_landing/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Cash Coding / Cash Price</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Landing</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Cash Price</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Daftar Cash Price</h4>
                                    <div class="d-flex gap-2">
                                        <button id="btn_reload_prices" class="btn btn-sm btn-outline-primary">Reload</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 d-flex">
                                        <input id="search_price" type="text" class="form-control form-control-sm me-2" placeholder="Cari kode / keterangan..." />
                                        <select id="filter_type" class="form-select form-select-sm" style="width:200px;">
                                            <option value="">Semua Tipe</option>
                                            <option value="pemasukan">Pemasukan</option>
                                            <option value="pengeluaran">Pengeluaran</option>
                                        </select>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm price-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:80px">Kode</th>
                                                    <th>Keterangan</th>
                                                    <th style="width:140px">Tipe</th>
                                                    <th style="width:140px" class="text-end">Harga (Rp)</th>
                                                    <th style="width:180px">Dibuat Oleh</th>
                                                </tr>
                                            </thead>
                                            <tbody id="price_list">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Memuat data...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title">Informasi</div>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small">Halaman ini menampilkan daftar Cash Coding / Cash Price yang digunakan di aplikasi dalam mode tampilan saja. Pengelolaan (tambah/edit/hapus) tersedia di panel Operator. Gunakan tombol "Reload" untuk memuat ulang data dari server.</p>

                                    <hr />

                                    <div class="mb-3">
                                        <h6 class="mb-1">Quick Actions</h6>
                                        <div class="d-grid gap-2">
                                            <a href="../operator/transaksi_list.php" class="btn btn-label-primary btn-round btn-sm"><i class="fa fa-list me-2"></i> Lihat Transaksi</a>
                                            <a href="../operator/balance_sheet.php" class="btn btn-label-success btn-round btn-sm"><i class="fa fa-wallet me-2"></i> Laporan Keuangan</a>
                                            <a href="../operator/transaksi_list.php" class="btn btn-label-warning btn-round btn-sm"><i class="fa fa-file-export me-2"></i> Export Harga</a>
                                        </div>
                                    </div>

                                    <hr />

                                    <div>
                                        <h6 class="mb-1">Contoh Kode</h6>
                                        <div class="small text-muted">Kode digunakan untuk mapping otomatis pada transaksi.</div>
                                        <ul class="small mt-2">
                                            <li><strong>CSH-001</strong> - Pembayaran tunai harian</li>
                                            <li><strong>COD-010</strong> - Coding layanan khusus</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Add/Edit removed on landing (view-only) -->

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

    <script>
        // Fetch data from landing API (read-only) and render table
        (function() {
            var dataCache = [];

            function formatRupiah(n) {
                return new Intl.NumberFormat('id-ID').format(n || 0);
            }

            function escapeHtml(s) {
                return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }

            function renderList(list) {
                var tbody = document.getElementById('price_list');
                tbody.innerHTML = '';
                if (!list || !list.length) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>';
                    return;
                }
                list.forEach(function(it) {
                    var tr = document.createElement('tr');
                    // map fields from kas: use id_kas as kode, keterangan/kategori as desc, jenis as type, jumlah as price
                    var kode = it.id_kas ? ('KAS-' + it.id_kas) : '-';
                    var desc = it.keterangan || it.kategori_nama || '-';
                    var tipe = it.jenis || '-';
                    var price = it.jumlah || 0;
                    // badge for type
                    var tipeBadge = '<span class="badge ' + (tipe === 'pemasukan' ? 'bg-success' : (tipe === 'pengeluaran' ? 'bg-danger' : 'bg-secondary')) + ' text-light text-uppercase small" style="font-size:11px;">' + escapeHtml(tipe) + '</span>';
                    tr.innerHTML = '<td class="text-nowrap">' + escapeHtml(kode) + '</td>' +
                        '<td>' + escapeHtml(desc) + '</td>' +
                        '<td>' + tipeBadge + '</td>' +
                        '<td class="text-end text-nowrap">Rp ' + formatRupiah(price) + '</td>' +
                        '<td>' + escapeHtml(it.dibuat_oleh || '-') + '</td>';
                    tbody.appendChild(tr);
                });
            }

            function applyFilters() {
                var q = (document.getElementById('search_price') || {}).value || '';
                q = q.trim().toLowerCase();
                var type = (document.getElementById('filter_type') || {}).value || '';
                var filtered = dataCache.filter(function(it) {
                    var kode = (it.id_kas ? ('KAS-' + it.id_kas) : '').toLowerCase();
                    var desc = (it.keterangan || it.kategori_nama || '').toLowerCase();
                    var ok = true;
                    if (type) ok = it.jenis === type;
                    if (q) ok = ok && (kode.indexOf(q) !== -1 || desc.indexOf(q) !== -1);
                    return ok;
                });
                renderList(filtered);
            }

            function loadData() {
                var btn = document.getElementById('btn_reload_prices');
                if (btn) btn.disabled = true;
                var tbody = document.getElementById('price_list');
                if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>';

                fetch('/APK_KAS/landing/api_cash_price.php', {
                        credentials: 'same-origin'
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(function(json) {
                        if (json.ok && Array.isArray(json.data)) {
                            dataCache = json.data;
                            applyFilters();
                        } else {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>';
                        }
                    })
                    .catch(function(err) {
                        console.error(err);
                        if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error memuat data</td></tr>';
                    })
                    .finally(function() {
                        if (btn) btn.disabled = false;
                    });
            }

            document.getElementById('btn_reload_prices').addEventListener('click', function() {
                loadData();
            });

            document.getElementById('search_price').addEventListener('input', function() {
                applyFilters();
            });

            document.getElementById('filter_type').addEventListener('change', function() {
                applyFilters();
            });

            // initial load
            loadData();
        })();
    </script>
</body>

</html>