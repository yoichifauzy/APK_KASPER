<?php
// landing/label_cash.php
// Landing page "Label Cash" — view-only listing derived from operator/payment_list.php
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Label Cash - KASPER</title>
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
    <link rel="stylesheet" href="../assets/js/plugin/datatables/datatables.min.css" />
    <style>
        .card-kaidmin {
            border-left: .35rem solid #4e73df;
        }

        .table-responsive {
            overflow-x: auto;
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
                        <h3 class="fw-bold mb-3">Label Cash</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Landing</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Label Cash</a></li>
                        </ul>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Label Cash (View Only)</h4>
                                <span class="text-muted small">Menampilkan ringkasan pembayaran / kas per tanggal dan kategori</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="me-2 mb-0">Jenis</label>
                                <select id="filter_jenis" class="form-select form-select-sm me-2">
                                    <option value="pemasukan">Pemasukan</option>
                                    <option value="pengeluaran">Pengeluaran</option>
                                </select>
                                <button id="btn_reload" class="btn btn-outline-primary btn-sm">Reload</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="labelTable" class="table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Jumlah Transaksi</th>
                                            <th class="text-end">Jumlah Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="label_tbody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-6 col-md-4">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-success bubble-shadow-small"><i class="fas fa-arrow-down"></i></div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category">Total Pemasukan</p>
                                                        <h4 id="total_pemasukan" class="card-title">Rp 0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-danger bubble-shadow-small"><i class="fas fa-arrow-up"></i></div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category">Total Pengeluaran</p>
                                                        <h4 id="total_pengeluaran" class="card-title">Rp 0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-primary bubble-shadow-small"><i class="fas fa-wallet"></i></div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category">Sisa Uang</p>
                                                        <h4 id="sisa_uang" class="card-title">Rp 0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        (function() {
            var table = null;

            function formatRupiah(n) {
                return new Intl.NumberFormat('id-ID').format(n || 0);
            }

            function load(jenis) {
                var tbody = document.getElementById('label_tbody');
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>';
                fetch('/APK_KAS/landing/api_label_cash.php?jenis=' + encodeURIComponent(jenis), {
                        credentials: 'same-origin'
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(function(json) {
                        if (json.ok) {
                            var data = json.data || [];
                            tbody.innerHTML = '';
                            data.forEach(function(r, i) {
                                var tr = document.createElement('tr');
                                tr.innerHTML = '<td>' + (i + 1) + '</td><td>' + (r.tanggal || '-') + '</td><td>' + (r.kategori_nama || '-') + '</td><td>' + (parseInt(r.payments_count || 0)) + '</td><td class="text-end">Rp ' + formatRupiah(parseFloat(r.total_jumlah || 0)) + '</td>';
                                tbody.appendChild(tr);
                            });

                            // totals
                            var t = json.totals || {};
                            document.getElementById('total_pemasukan').textContent = 'Rp ' + formatRupiah(t.total_pemasukan || 0);
                            document.getElementById('total_pengeluaran').textContent = 'Rp ' + formatRupiah(t.total_pengeluaran || 0);
                            document.getElementById('sisa_uang').textContent = 'Rp ' + formatRupiah(t.sisa_uang || 0);

                            // initialize datatable (re-init safe)
                            try {
                                if (table) {
                                    table.destroy();
                                }
                            } catch (e) {}
                            table = $('#labelTable').DataTable({
                                responsive: true,
                                pageLength: 12,
                                ordering: false
                            });
                        } else {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>';
                        }
                    }).catch(function(err) {
                        console.error(err);
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error memuat data</td></tr>';
                    });
            }

            document.getElementById('btn_reload').addEventListener('click', function() {
                load((document.getElementById('filter_jenis') || {}).value || 'pemasukan');
            });
            document.getElementById('filter_jenis').addEventListener('change', function() {
                load(this.value);
            });

            // initial
            load(document.getElementById('filter_jenis').value || 'pemasukan');
        })();
    </script>
</body>

</html>