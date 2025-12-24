<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);

include '../config/database.php';

// default date range: last 30 days
$default_end = date('Y-m-d');
$default_start = date('Y-m-d', strtotime('-29 days'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin - Income Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <style>
        .kpi {
            min-height: 70px;
        }

        .chart-card {
            height: 320px;
        }

        .chart-card canvas {
            display: block;
            width: 100% !important;
        }

        .chart-small {
            height: 140px !important;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_admin/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>

                <!-- Navbar Header -->
                <?php include 'layout_admin/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Income Report</h4>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Financial Reports</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Income Report</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form id="filterForm" class="row g-2 align-items-end">
                                        <div class="col-auto">
                                            <label class="form-label small">From</label>
                                            <input type="date" id="fromDate" name="from" class="form-control" value="<?= $default_start ?>">
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label small">To</label>
                                            <input type="date" id="toDate" name="to" class="form-control" value="<?= $default_end ?>">
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label small">Bulan</label>
                                            <select id="monthSelect" class="form-select">
                                                <option value="">-</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label small">Tahun</label>
                                            <select id="yearSelect" class="form-select">
                                                <option value="">-</option>
                                            </select>
                                        </div>
                                        <!-- category filter removed: report is for income only -->
                                        <div class="col-auto">
                                            <button id="applyBtn" class="btn btn-primary">Apply</button>
                                            <button id="resetBtn" type="button" class="btn btn-secondary">Reset</button>
                                        </div>
                                        <div class="col-auto ms-auto d-flex gap-2">
                                            <a id="exportCsv" class="btn btn-outline-primary btn-sm" href="#">Export CSV</a>
                                            <a id="exportPdf" class="btn btn-outline-secondary btn-sm" href="#">Export PDF</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 col-6">
                            <div class="card kpi">
                                <div class="card-body">
                                    <small class="text-muted">Total Income (All-time)</small>
                                    <h4 id="kpi_total">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card kpi">
                                <div class="card-body">
                                    <small class="text-muted">Income This Month</small>
                                    <h4 id="kpi_month">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card kpi">
                                <div class="card-body">
                                    <small class="text-muted">Income Today</small>
                                    <h4 id="kpi_today">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card kpi">
                                <div class="card-body">
                                    <small class="text-muted">Avg / Transaction</small>
                                    <h4 id="kpi_avg">-</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card chart-card">
                                <div class="card-body">
                                    <canvas id="incomeChart" style="height:260px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card chart-card">
                                <div class="card-body">
                                    <div id="overallWrapper">
                                        <canvas id="overallChart" style="height:140px;margin-bottom:12px;"></canvas>
                                        <div id="overallEmpty" class="text-center text-muted" style="display:none;padding:28px 0;">Tidak ada data untuk grafik keseluruhan.</div>
                                    </div>
                                    <div id="categoryWrapper">
                                        <canvas id="categoryChart" style="height:140px;"></canvas>
                                        <div id="categoryEmpty" class="text-center text-muted" style="display:none;padding:28px 0;">Tidak ada data pengeluaran per kategori.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="transactionsTable" class="table table-striped table-sm" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Operator</th>
                                                <th>Proof</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php include 'layout_admin/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>
    <script>
        $(function() {
            var $from = $('#fromDate'),
                $to = $('#toDate');

            var $month = $('#monthSelect'),
                $year = $('#yearSelect');

            // populate month and year selects
            var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            for (var m = 1; m <= 12; m++) {
                $month.append($('<option>', {
                    value: m,
                    text: monthNames[m - 1]
                }));
            }
            var startYear = 2025;
            var currentYear = new Date().getFullYear();
            // ensure we include future years up to 2027, or up to current year if it's greater
            var endYear = Math.max(currentYear, startYear + 2); // will include 2025,2026,2027 at least
            for (var y = endYear; y >= startYear; y--) {
                $year.append($('<option>', {
                    value: y,
                    text: y
                }));
            }

            function setRangeFromMonthYear(m, y) {
                if (!m || !y) return;
                var first = new Date(y, m - 1, 1);
                var last = new Date(y, m, 0);

                function fmt(d) {
                    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                }
                $from.val(fmt(first));
                $to.val(fmt(last));
            }

            $month.on('change', function() {
                var m = $(this).val();
                var y = $year.val();
                if (m && y) {
                    setRangeFromMonthYear(parseInt(m), parseInt(y));
                    loadSummary();
                    table.ajax.reload();
                }
            });
            $year.on('change', function() {
                var y = $(this).val();
                var m = $month.val();
                if (m && y) {
                    setRangeFromMonthYear(parseInt(m), parseInt(y));
                    loadSummary();
                    table.ajax.reload();
                }
            });

            function formatRupiah(num) {
                if (num === null) return '-';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(num);
            }

            var incomeChart = null,
                categoryChart = null,
                overallChart = null;

            function loadSummary() {
                $.getJSON('api_income_report.php', {
                    action: 'summary',
                    from: $from.val(),
                    to: $to.val()
                }, function(resp) {
                    if (resp.ok) {
                        // show all-time total income in KPI
                        $('#kpi_total').text(formatRupiah(resp.summary.all_income || resp.summary.total || 0));
                        // show month label, total, count and avg in the month KPI card
                        var monthLabel = resp.summary.month_label || new Date().toLocaleString(undefined, {
                            month: 'long',
                            year: 'numeric'
                        });
                        var monthTotal = formatRupiah(resp.summary.month);
                        var monthCount = (typeof resp.summary.month_count !== 'undefined') ? resp.summary.month_count : '-';
                        var monthAvg = (typeof resp.summary.month_avg !== 'undefined') ? formatRupiah(resp.summary.month_avg) : '-';
                        $('#kpi_month').html(monthLabel + '<br><small>' + monthTotal + ' &middot; ' + monthCount + ' trx &middot; Avg ' + monthAvg + '</small>');
                        $('#kpi_today').text(formatRupiah(resp.summary.today));
                        $('#kpi_avg').text(formatRupiah(resp.summary.avg));

                        // income & expense line chart
                        var ctx = document.getElementById('incomeChart').getContext('2d');
                        var labels = resp.chart.labels || [];
                        var incomeData = resp.chart.income || [];
                        var expenseData = resp.chart.expense || [];
                        if (incomeChart) incomeChart.destroy();
                        incomeChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Pemasukan',
                                        data: incomeData,
                                        borderColor: '#198754',
                                        backgroundColor: 'rgba(25,135,84,0.08)',
                                        fill: true,
                                        tension: 0.3,
                                        pointRadius: 2
                                    },
                                    {
                                        label: 'Pengeluaran',
                                        data: expenseData,
                                        borderColor: '#dc3545',
                                        backgroundColor: 'rgba(220,53,69,0.08)',
                                        fill: true,
                                        tension: 0.3,
                                        pointRadius: 2
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                },
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                var v = context.parsed.y;
                                                var label = context.dataset.label || '';
                                                return label + ': ' + new Intl.NumberFormat('id-ID', {
                                                    style: 'currency',
                                                    currency: 'IDR',
                                                    minimumFractionDigits: 0
                                                }).format(v);
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            maxRotation: 0,
                                            autoSkip: true,
                                            maxTicksLimit: 12
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return new Intl.NumberFormat('id-ID', {
                                                    style: 'currency',
                                                    currency: 'IDR',
                                                    minimumFractionDigits: 0
                                                }).format(value);
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        // overall income vs expense (doughnut)
                        var totIncome = resp.summary.range_income || 0;
                        var totExpense = resp.summary.range_expense || 0;
                        // if both totals are zero, show empty placeholder instead of chart
                        if ((totIncome === 0 || totIncome === '0') && (totExpense === 0 || totExpense === '0')) {
                            if (overallChart) {
                                overallChart.destroy();
                                overallChart = null;
                            }
                            $('#overallChart').hide();
                            $('#overallEmpty').show();
                        } else {
                            $('#overallEmpty').hide();
                            $('#overallChart').show();
                            var ctxo = document.getElementById('overallChart').getContext('2d');
                            if (overallChart) overallChart.destroy();
                            overallChart = new Chart(ctxo, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Pemasukan', 'Pengeluaran'],
                                    datasets: [{
                                        data: [totIncome, totExpense],
                                        backgroundColor: ['#198754', '#dc3545']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    var v = context.parsed;
                                                    return context.label + ': ' + new Intl.NumberFormat('id-ID', {
                                                        style: 'currency',
                                                        currency: 'IDR',
                                                        minimumFractionDigits: 0
                                                    }).format(v);
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // category chart (pengeluaran per kategori)
                        var catLabels = resp.category.labels || [];
                        var catData = resp.category.data || [];
                        var sumCat = 0;
                        if (Array.isArray(catData) && catData.length) {
                            for (var i = 0; i < catData.length; i++) {
                                sumCat += Number(catData[i]) || 0;
                            }
                        }

                        if (!Array.isArray(catData) || catData.length === 0 || sumCat === 0) {
                            if (categoryChart) {
                                categoryChart.destroy();
                                categoryChart = null;
                            }
                            $('#categoryChart').hide();
                            $('#categoryEmpty').show();
                        } else {
                            $('#categoryEmpty').hide();
                            $('#categoryChart').show();
                            var ctx2 = document.getElementById('categoryChart').getContext('2d');
                            if (categoryChart) categoryChart.destroy();
                            categoryChart = new Chart(ctx2, {
                                type: 'bar',
                                data: {
                                    labels: catLabels,
                                    datasets: [{
                                        label: 'Pengeluaran per Kategori',
                                        data: catData,
                                        backgroundColor: ['#dc3545', '#fd7e14', '#6f42c1', '#0d6efd', '#198754'],
                                        borderColor: ['#dc3545', '#fd7e14', '#6f42c1', '#0d6efd', '#198754'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    var v = context.parsed.x;
                                                    return new Intl.NumberFormat('id-ID', {
                                                        style: 'currency',
                                                        currency: 'IDR',
                                                        minimumFractionDigits: 0
                                                    }).format(v);
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            ticks: {
                                                callback: function(value) {
                                                    return new Intl.NumberFormat('id-ID', {
                                                        style: 'currency',
                                                        currency: 'IDR',
                                                        minimumFractionDigits: 0
                                                    }).format(value);
                                                }
                                            }
                                        },
                                        y: {
                                            ticks: {
                                                autoSkip: false
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
            }

            var table = $('#transactionsTable').DataTable({
                processing: true,
                serverSide: false,
                order: [
                    [1, 'asc']
                ],
                ajax: {
                    url: 'api_income_report.php?action=transactions_simple',
                    dataSrc: 'data',
                    data: function(d) {
                        // send filters as query params for the simple endpoint
                        return {
                            from: $from.val(),
                            to: $to.val()
                        };
                    }
                },
                columns: [{ // row number
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1 + (meta.settings._iDisplayStart || 0);
                        }
                    },
                    {
                        data: 'tanggal_bayar'
                    },
                    {
                        data: 'nama_lengkap'
                    },
                    {
                        data: 'kategori'
                    },
                    {
                        data: 'jumlah',
                        render: function(v) {
                            return new Intl.NumberFormat('id-ID').format(v);
                        }
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'operator'
                    },
                    {
                        data: 'bukti',
                        render: function(v) {
                            return v ? '<a href="../upload/pembayaran/' + encodeURIComponent(v) + '" target="_blank" class="btn btn-sm btn-outline-primary">Bukti</a>' : ''
                        }
                    }
                ]
            });

            $('#applyBtn').on('click', function(e) {
                e.preventDefault();
                loadSummary();
                table.ajax.reload();
            });
            $('#resetBtn').on('click', function() {
                $from.val('<?= $default_start ?>');
                $to.val('<?= $default_end ?>');
                loadSummary();
                table.ajax.reload();
            });

            $('#exportCsv').on('click', function(e) {
                e.preventDefault();
                var url = 'api_income_report.php?action=export_csv&from=' + encodeURIComponent($from.val()) + '&to=' + encodeURIComponent($to.val());
                window.location = url;
            });
            $('#exportPdf').on('click', function(e) {
                e.preventDefault();
                var url = 'api_income_report.php?action=export_pdf&from=' + encodeURIComponent($from.val()) + '&to=' + encodeURIComponent($to.val());
                window.open(url, '_blank');
            });

            // initial load
            loadSummary();
        });
    </script>
</body>

</html>