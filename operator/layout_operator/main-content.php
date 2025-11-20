<div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Dashboard</h3>
                <h6 class="op-7 mb-2">Ringkasan Informasi Keuangan dan Aktivitas</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <a href="pembayaran.php" class="btn btn-primary btn-round">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Transaksi
                </a>
            </div>
        </div>

        <?php include 'grafik_arus_kas.php'; ?>


        <?php
        // --- QUERIES FOR KPI CARDS ---


        // 1. Total Aset Kas (Overall)
        $query_total_income = "SELECT SUM(jumlah) AS total FROM pembayaran";
        $result_total_income = mysqli_query($conn, $query_total_income);
        $total_income = mysqli_fetch_assoc($result_total_income)['total'] ?? 0;

        $query_total_expense = "SELECT SUM(jumlah) AS total FROM kas WHERE jenis = 'pengeluaran'";
        $result_total_expense = mysqli_query($conn, $query_total_expense);
        $total_expense = mysqli_fetch_assoc($result_total_expense)['total'] ?? 0;

        $total_aset_kas = $total_income - $total_expense;

        // 2. Pemasukan (Bulan Ini)
        $bulan_ini = date('Y-m');
        $query_monthly_income = "SELECT SUM(jumlah) AS total FROM pembayaran WHERE DATE_FORMAT(tanggal_bayar, '%Y-%m') = '$bulan_ini'";
        $result_monthly_income = mysqli_query($conn, $query_monthly_income);
        $pemasukan_bulan_ini = mysqli_fetch_assoc($result_monthly_income)['total'] ?? 0;

        // 3. Pengeluaran (Bulan Ini)
        $query_monthly_expense = "SELECT SUM(jumlah) AS total FROM kas WHERE jenis = 'pengeluaran' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'";
        $result_monthly_expense = mysqli_query($conn, $query_monthly_expense);
        $pengeluaran_bulan_ini = mysqli_fetch_assoc($result_monthly_expense)['total'] ?? 0;

        // --- LOGIC FOR "SUDAH LUNAS" CARD AND MODAL ---
        $modal_month = $_GET['modal_month'] ?? date('m');
        $modal_year = $_GET['modal_year'] ?? date('Y');

        $students = [];
        $res_students = $conn->query("SELECT id_user, nama_lengkap FROM user WHERE role = 'user' AND status='aktif' ORDER BY nama_lengkap");
        while ($row = $res_students->fetch_assoc()) {
            $students[] = $row;
        }

        // Get the main bill for the filtered month (for the modal)
        $main_bill_modal = null;
        $stmt_bill_modal = $conn->prepare("SELECT id_kas, keterangan, jumlah FROM kas WHERE jenis = 'pemasukan' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY id_kas ASC LIMIT 1");
        $stmt_bill_modal->bind_param("ii", $modal_month, $modal_year);
        $stmt_bill_modal->execute();
        $result_bill_modal = $stmt_bill_modal->get_result();
        if ($result_bill_modal->num_rows > 0) {
            $main_bill_modal = $result_bill_modal->fetch_assoc();
        }
        $stmt_bill_modal->close();

        $paid_users_for_modal = [];
        if ($main_bill_modal) {
            // --- LOGIKA JIKA ADA TAGIHAN UTAMA ---
            foreach ($students as $student) {
                $id_user = $student['id_user'];
                $required_amount = $main_bill_modal['jumlah'];

                $total_paid = 0;
                $stmt_paid_modal = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
                $stmt_paid_modal->bind_param("ii", $id_user, $main_bill_modal['id_kas']);
                $stmt_paid_modal->execute();
                $stmt_paid_modal->bind_result($sum_paid);
                if ($stmt_paid_modal->fetch()) {
                    $total_paid = $sum_paid ?? 0;
                }
                $stmt_paid_modal->close();

                if ($total_paid >= $required_amount) {
                    $paid_users_for_modal[] = ['nama_lengkap' => $student['nama_lengkap']];
                }
            }
        } else {
            // --- LOGIKA JIKA TIDAK ADA TAGIHAN UTAMA ---
            // Cek pembayaran apa pun yang dilakukan mahasiswa di bulan ini
            foreach ($students as $student) {
                $id_user = $student['id_user'];
                $total_paid = 0;
                $is_late_payment_found = false;

                $stmt_payments_modal = $conn->prepare("SELECT jumlah, status FROM pembayaran WHERE id_user = ? AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?");
                $stmt_payments_modal->bind_param("iii", $id_user, $modal_month, $modal_year);
                $stmt_payments_modal->execute();
                $result_payments_modal = $stmt_payments_modal->get_result();

                while ($row = $result_payments_modal->fetch_assoc()) {
                    $total_paid += $row['jumlah'];
                    if ($row['status'] === 'telat') {
                        $is_late_payment_found = true;
                    }
                }
                $stmt_payments_modal->close();

                // Jika ada pembayaran di bulan ini, anggap sebagai "lunas"
                if ($total_paid > 0) {
                    $paid_users_for_modal[] = ['nama_lengkap' => $student['nama_lengkap']];
                }
            }
        }

        // Calculate paid count for the FILTERED month for the KPI card
        // Use modal_month and modal_year so card shows filtered data
        $paid_count_for_kpi = 0;
        $kpi_month = $modal_month;
        $kpi_year = $modal_year;
        $main_bill_kpi = null;
        $stmt_bill_kpi = $conn->prepare("SELECT id_kas, jumlah FROM kas WHERE jenis = 'pemasukan' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY id_kas ASC LIMIT 1");
        $stmt_bill_kpi->bind_param("ii", $kpi_month, $kpi_year);
        $stmt_bill_kpi->execute();
        $result_bill_kpi = $stmt_bill_kpi->get_result();
        if ($result_bill_kpi->num_rows > 0) {
            // --- LOGIKA JIKA ADA TAGIHAN UTAMA ---
            $main_bill_kpi = $result_bill_kpi->fetch_assoc();
            foreach ($students as $student) {
                $total_paid_kpi = 0;
                $stmt_paid_kpi = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
                $stmt_paid_kpi->bind_param("ii", $student['id_user'], $main_bill_kpi['id_kas']);
                $stmt_paid_kpi->execute();
                $stmt_paid_kpi->bind_result($sum_paid_kpi);
                if ($stmt_paid_kpi->fetch()) {
                    $total_paid_kpi = $sum_paid_kpi ?? 0;
                }
                $stmt_paid_kpi->close();
                if ($total_paid_kpi >= $main_bill_kpi['jumlah']) {
                    $paid_count_for_kpi++;
                }
            }
        } else {
            // --- LOGIKA JIKA TIDAK ADA TAGIHAN UTAMA ---
            // Cek pembayaran apa pun yang dilakukan mahasiswa di bulan ini
            foreach ($students as $student) {
                $total_paid_kpi = 0;
                $stmt_payments_kpi = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?");
                $stmt_payments_kpi->bind_param("iii", $student['id_user'], $kpi_month, $kpi_year);
                $stmt_payments_kpi->execute();
                $stmt_payments_kpi->bind_result($sum_paid_kpi);
                if ($stmt_payments_kpi->fetch()) {
                    $total_paid_kpi = $sum_paid_kpi ?? 0;
                }
                $stmt_payments_kpi->close();

                // Jika ada pembayaran di bulan ini, anggap sebagai "lunas"
                if ($total_paid_kpi > 0) {
                    $paid_count_for_kpi++;
                }
            }
        }
        $stmt_bill_kpi->close();

        if (!function_exists('formatRupiah')) {
            function formatRupiah($amount)
            {
                return 'Rp ' . number_format($amount, 0, ',', '.');
            }
        }
        ?>
        <!-- Filter Section for Anggota Lunas Card -->
        <form method="GET" action="dashboard_operator.php" class="mb-3">
            <div class="row align-items-center">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0" style="white-space: nowrap;">Filter Lunas:</label>
                        <select name="modal_month" class="form-select form-select-sm">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($modal_month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="modal_year" class="form-select form-select-sm">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= ($modal_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="row row-card-no-pd">
            <!-- Card 1-3 -->
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="fw-bold text-uppercase">Total Aset Kas</h6>
                                <p class="text-muted">Saldo Akhir Keseluruhan</p>
                            </div>
                            <h4 class="text-primary fw-bold"><?php echo formatRupiah($total_aset_kas); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="fw-bold text-uppercase">Pemasukan (Bulan Ini)</h6>
                                <p class="text-muted">Periode <?= date('F Y') ?></p>
                            </div>
                            <h4 class="text-success fw-bold"><?php echo formatRupiah($pemasukan_bulan_ini); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="fw-bold text-uppercase">Pengeluaran (Bulan Ini)</h6>
                                <p class="text-muted">Periode <?= date('F Y') ?></p>
                            </div>
                            <h4 class="text-danger fw-bold"><?php echo formatRupiah($pengeluaran_bulan_ini); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 4: Anggota Sudah Lunas -->
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-uppercase">Anggota Lunas</h6>
                                <p class="text-muted"><?= date('F Y', mktime(0, 0, 0, $modal_month, 1, $modal_year)) ?></p>
                                <h4 class="text-success fw-bold mb-0"><?php echo $paid_count_for_kpi; ?> Orang</h4>
                            </div><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paid-modal"><i class="fas fa-eye"></i> Lihat</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Paid Users -->
        <div class="modal fade" id="paid-modal" tabindex="-1" aria-labelledby="paidModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paidModalLabel">Daftar Anggota Sudah Lunas</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="GET" action="dashboard_operator.php" class="row g-3 align-items-center mb-3">
                            <div class="col-auto"><label for="modal_month" class="col-form-label">Bulan:</label></div>
                            <div class="col-auto"><select name="modal_month" id="modal_month" class="form-select"><?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>" <?= ($modal_month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option><?php endfor; ?></select></div>
                            <div class="col-auto"><label for="modal_year" class="col-form-label">Tahun:</label></div>
                            <div class="col-auto"><select name="modal_year" id="modal_year" class="form-select"><?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?><option value="<?= $y ?>" <?= ($modal_year == $y) ? 'selected' : '' ?>><?= $y ?></option><?php endfor; ?></select></div>
                            <div class="col-auto"><button type="submit" class="btn btn-primary">Terapkan Filter</button></div>
                        </form>
                        <p>Daftar ini menampilkan anggota yang sudah lunas untuk periode <strong><?= date('F', mktime(0, 0, 0, $modal_month, 10)) ?> <?= $modal_year ?></strong>.
                            <?php if ($main_bill_modal): ?>
                                Berdasarkan tagihan wajib: <strong><?= htmlspecialchars($main_bill_modal['keterangan']) ?></strong>
                            <?php else: ?>
                                Berdasarkan pembayaran apapun yang dilakukan di bulan tersebut.
                            <?php endif; ?></p>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                    </tr>
                                </thead>
                                <tbody><?php if (empty($paid_users_for_modal)): ?><tr>
                                            <td colspan="2" class="text-center"><?php if ($main_bill_modal): ?>Belum ada anggota yang lunas untuk periode ini.<?php else: ?>Tidak ada tagihan wajib untuk periode ini.<?php endif; ?></td>
                                        </tr><?php else: ?><?php $no = 1;
                                                            foreach ($paid_users_for_modal as $user): ?><tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($user['nama_lengkap']); ?></td>
                                        </tr><?php endforeach; ?><?php endif; ?></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
                </div>
            </div>
        </div>

        <?php
        // --- DATA FOR MONTHLY INCOME CHART ---
        $chart_month = $_GET['chart_month'] ?? date('m');
        $chart_year = $_GET['chart_year'] ?? date('Y');
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $chart_month, $chart_year);
        $chart_labels = [];
        $chart_data = array_fill(1, $days_in_month, 0);
        for ($day = 1; $day <= $days_in_month; $day++) {
            $chart_labels[] = $day;
        }
        $q_chart = "SELECT DAY(tanggal_bayar) as day, SUM(jumlah) as total FROM pembayaran WHERE MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ? GROUP BY DAY(tanggal_bayar)";
        $stmt_chart = $conn->prepare($q_chart);
        $stmt_chart->bind_param("ii", $chart_month, $chart_year);
        $stmt_chart->execute();
        $result_chart = $stmt_chart->get_result();
        while ($row = $result_chart->fetch_assoc()) {
            $chart_data[intval($row['day'])] = $row['total'];
        }
        $stmt_chart->close();
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Grafik Pemasukan Bulanan</h4>
                            <form method="GET" action="dashboard_operator.php" class="d-flex align-items-center"><select name="chart_month" class="form-select form-select-sm me-2"><?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>" <?= ($chart_month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option><?php endfor; ?></select><select name="chart_year" class="form-select form-select-sm me-2"><?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?><option value="<?= $y ?>" <?= ($chart_year == $y) ? 'selected' : '' ?>><?= $y ?></option><?php endfor; ?></select><button type="submit" class="btn btn-primary btn-sm">Filter</button></form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px; width:100%"><canvas id="monthlyIncomeChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // --- DATA FOR PIE CHART STATUS PEMBAYARAN ---
        $pie_month = isset($_GET['pie_month']) ? intval($_GET['pie_month']) : (isset($_GET['modal_month']) ? intval($_GET['modal_month']) : date('m'));
        $pie_year = isset($_GET['pie_year']) ? intval($_GET['pie_year']) : (isset($_GET['modal_year']) ? intval($_GET['modal_year']) : date('Y'));

        $pie_chart_data = ['Lunas' => 0, 'Telat' => 0, 'Belum Lunas' => 0];

        // Ambil satu tagihan utama untuk bulan yang dipilih
        $main_bill_pie = null;
        $stmt_bill_pie = $conn->prepare("SELECT id_kas, jumlah FROM kas WHERE jenis = 'pemasukan' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY id_kas ASC LIMIT 1");
        $stmt_bill_pie->bind_param("ii", $pie_month, $pie_year);
        $stmt_bill_pie->execute();
        $result_bill_pie = $stmt_bill_pie->get_result();
        if ($result_bill_pie->num_rows > 0) {
            $main_bill_pie = $result_bill_pie->fetch_assoc();
        }
        $stmt_bill_pie->close();

        if (count($students) > 0) {
            $lunas_count = 0;
            $telat_count = 0;
            $paid_students_pie = [];

            if ($main_bill_pie) {
                // --- LOGIKA JIKA ADA TAGIHAN UTAMA ---
                $id_kas_pie = $main_bill_pie['id_kas'];
                $required_amount_pie = $main_bill_pie['jumlah'];

                foreach ($students as $student) {
                    $id_user_pie = $student['id_user'];
                    $total_paid_pie = 0;
                    $stmt_paid_pie = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
                    $stmt_paid_pie->bind_param("ii", $id_user_pie, $id_kas_pie);
                    $stmt_paid_pie->execute();
                    $stmt_paid_pie->bind_result($sum_paid_pie);
                    if ($stmt_paid_pie->fetch()) {
                        $total_paid_pie = $sum_paid_pie ?? 0;
                    }
                    $stmt_paid_pie->close();

                    if ($total_paid_pie >= $required_amount_pie) {
                        $paid_students_pie[] = $id_user_pie;
                        $is_late_pie = false;
                        $stmt_late_pie = $conn->prepare("SELECT 1 FROM pembayaran WHERE id_user = ? AND id_kas = ? AND status = 'telat' LIMIT 1");
                        $stmt_late_pie->bind_param("ii", $id_user_pie, $id_kas_pie);
                        $stmt_late_pie->execute();
                        if ($stmt_late_pie->fetch()) {
                            $is_late_pie = true;
                        }
                        $stmt_late_pie->close();

                        if ($is_late_pie) {
                            $telat_count++;
                        } else {
                            $lunas_count++;
                        }
                    }
                }
            } else {
                // --- LOGIKA JIKA TIDAK ADA TAGIHAN UTAMA ---
                foreach ($students as $student) {
                    $id_user_pie = $student['id_user'];
                    $payments_in_month_pie = [];
                    $stmt_payments_pie = $conn->prepare("SELECT status FROM pembayaran WHERE id_user = ? AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?");
                    $stmt_payments_pie->bind_param("iii", $id_user_pie, $pie_month, $pie_year);
                    $stmt_payments_pie->execute();
                    $result_payments_pie = $stmt_payments_pie->get_result();
                    while ($row = $result_payments_pie->fetch_assoc()) {
                        $payments_in_month_pie[] = $row;
                    }
                    $stmt_payments_pie->close();

                    if (!empty($payments_in_month_pie)) {
                        $paid_students_pie[] = $id_user_pie;
                        $is_late_payment_found_pie = false;
                        foreach ($payments_in_month_pie as $payment) {
                            if ($payment['status'] === 'telat') {
                                $is_late_payment_found_pie = true;
                                break;
                            }
                        }
                        if ($is_late_payment_found_pie) {
                            $telat_count++;
                        } else {
                            $lunas_count++;
                        }
                    }
                }
            }

            $pie_chart_data['Lunas'] = $lunas_count;
            $pie_chart_data['Telat'] = $telat_count;
            $pie_chart_data['Belum Lunas'] = count($students) - count(array_unique($paid_students_pie));
        }
        ?>
        <!-- Debug Info (hapus setelah selesai debugging) -->
        <!-- pie_month: <?= $pie_month ?>, pie_year: <?= $pie_year ?>, main_bill_pie: <?= $main_bill_pie ? 'Ada' : 'Tidak Ada' ?>, total_students: <?= count($students) ?>, lunas: <?= $pie_chart_data['Lunas'] ?>, telat: <?= $pie_chart_data['Telat'] ?>, belum_lunas: <?= $pie_chart_data['Belum Lunas'] ?> --> <!-- Grafik Pie Status Pembayaran -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Status Pembayaran Siswa</h4>
                            <form method="GET" action="dashboard_operator.php" class="d-flex align-items-center gap-2">
                                <select name="pie_month" class="form-select form-select-sm">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= ($pie_month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="pie_year" class="form-select form-select-sm">
                                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                        <option value="<?= $y ?>" <?= ($pie_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ringkasan Status Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted">Periode: <?= date('F Y', mktime(0, 0, 0, $pie_month, 1, $pie_year)) ?></h6>
                            <?php if (!$main_bill_pie): ?>
                                <p class="text-warning small mt-2"><i class="fas fa-info-circle"></i> Tidak ada tagihan wajib untuk periode ini. Data ditampilkan berdasarkan pembayaran yang dilakukan siswa.</p>
                            <?php endif; ?>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-circle text-success me-2"></i>Lunas</span>
                                <strong><?= $pie_chart_data['Lunas'] ?> Orang</strong>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-circle text-info me-2"></i>Telat</span>
                                <strong><?= $pie_chart_data['Telat'] ?> Orang</strong>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-circle text-danger me-2"></i>Belum Lunas</span>
                                <strong><?= $pie_chart_data['Belum Lunas'] ?> Orang</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span><strong>Total Siswa</strong></span>
                            <strong><?= count($students) ?> Orang</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // --- DATA FOR INTERACTIVE EXPENSE CHART ---
        $expense_details = [];
        $query_expense_details = "
            SELECT
                kk.nama as category_name,
                k.keterangan as expense_description,
                k.jumlah as expense_amount
            FROM kas k
            JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori
            WHERE k.jenis = 'pengeluaran' AND k.jumlah > 0 AND k.keterangan IS NOT NULL AND k.keterangan != ''
            ORDER BY kk.nama, k.tanggal DESC;
        ";

        $result_expense_details = mysqli_query($conn, $query_expense_details);

        if ($result_expense_details) {
            while ($row = mysqli_fetch_assoc($result_expense_details)) {
                $category = $row['category_name'];
                if (!isset($expense_details[$category])) {
                    $expense_details[$category] = [];
                }
                $expense_details[$category][] = [
                    'keterangan' => $row['expense_description'],
                    'jumlah' => $row['expense_amount']
                ];
            }
        }
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 id="expenseChartTitle" class="card-title">Grafik Pengeluaran per Kategori</h4>
                            <button id="back-to-categories" class="btn btn-secondary btn-sm" style="display:none;">&larr; Kembali</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="category-filters" class="mb-3">
                            <!-- Filter badges will be inserted here by JavaScript -->
                        </div>
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="expenseByCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Status Pembayaran per Anggota -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center w-100">
                            <form method="GET" action="dashboard_operator.php" class="d-flex align-items-center gap-2">
                                <select name="bar_chart_month" class="form-select form-select-sm me-2">
                                    <?php
                                    // Definisikan variabel ini di dashboard_operator.php sebelum bagian ini
                                    $bar_chart_month = $_GET['bar_chart_month'] ?? date('m');
                                    $bar_chart_year = $_GET['bar_chart_year'] ?? date('Y');
                                    for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= ($bar_chart_month == $m) ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <select name="bar_chart_year" class="form-select form-select-sm me-2">
                                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                        <option value="<?= $y ?>" <?= ($bar_chart_year == $y) ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            </form>
                            <h4 class="card-title ms-auto mb-0">Grafik Status Pembayaran per Anggota</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($user_payment_summary_data['labels'])): ?>
                            <div class="text-center p-4">
                                <p class="text-muted">Tidak ada data pembayaran untuk ditampilkan pada tahun <?= $summary_year ?>.</p>
                                <p class="text-muted small">Pastikan ada tagihan (Kas Pemasukan) yang telah dibuat untuk tahun ini.</p>
                            </div>
                        <?php else: ?>
                            <div class="chart-container" style="position: relative; height:400px; width:100%">
                                <canvas id="userPaymentSummaryChart"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Radar Aktivitas Operator -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Grafik Radar Aktivitas Operator</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="operatorActivityRadarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Modal auto-open script
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('modal_month') || urlParams.has('modal_year')) {
                    var paidModal = new bootstrap.Modal(document.getElementById('paid-modal'));
                    paidModal.show();
                }

                // Monthly Income Chart script
                var monthlyIncomeCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
                var monthlyIncomeChart = new Chart(monthlyIncomeCtx, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($chart_labels) ?>,
                        datasets: [{
                            label: "Pemasukan",
                            borderColor: '#177dff',
                            pointBackgroundColor: '#177dff',
                            backgroundColor: 'rgba(23, 125, 255, 0.2)',
                            borderWidth: 2,
                            data: <?= json_encode(array_values($chart_data)) ?>,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                minimumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value, index, values) {
                                        return new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(value);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Payment Status Pie Chart script
                var paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
                var chartLabels = <?= json_encode(array_keys($pie_chart_data)) ?>;
                var chartValues = <?= json_encode(array_values($pie_chart_data)) ?>;
                console.log('Pie Chart Data:', {
                    labels: chartLabels,
                    values: chartValues
                });

                var paymentStatusChart = new Chart(paymentStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartValues,
                            backgroundColor: ['#2ecc71', '#3498db', '#e74c3c'],
                            borderColor: ['#27ae60', '#2980b9', '#c0392b'],
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + ' Orang';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });

                // --- INTERACTIVE EXPENSE CHART SCRIPT ---
                const expenseData = <?= json_encode($expense_details) ?>;
                const expenseCtx = document.getElementById('expenseByCategoryChart').getContext('2d');
                const expenseChartTitle = document.getElementById('expenseChartTitle');
                const categoryFiltersContainer = document.getElementById('category-filters');
                const backButton = document.getElementById('back-to-categories');
                let expenseChart;

                function formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                }

                const chartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) label += formatCurrency(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => formatCurrency(value)
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                };

                function renderCategoryView() {
                    const categoryLabels = Object.keys(expenseData);
                    const categoryTotals = categoryLabels.map(category => {
                        return expenseData[category].reduce((sum, item) => sum + parseFloat(item.jumlah), 0);
                    });

                    const chartData = {
                        labels: categoryLabels,
                        datasets: [{
                            label: "Total Pengeluaran",
                            backgroundColor: '#f3545d',
                            borderColor: '#f3545d',
                            data: categoryTotals,
                        }]
                    };

                    if (expenseChart) {
                        expenseChart.data = chartData;
                        expenseChart.update();
                    } else {
                        expenseChart = new Chart(expenseCtx, {
                            type: 'bar',
                            data: chartData,
                            options: chartOptions
                        });
                    }

                    expenseChartTitle.textContent = 'Grafik Pengeluaran per Kategori';
                    backButton.style.display = 'none';
                    categoryFiltersContainer.style.display = 'block';
                }

                function renderDetailView(category) {
                    const details = expenseData[category];
                    const detailLabels = details.map(item => item.keterangan);
                    const detailData = details.map(item => item.jumlah);

                    expenseChart.data.labels = detailLabels;
                    expenseChart.data.datasets[0].data = detailData;
                    expenseChart.update();

                    expenseChartTitle.textContent = 'Detail Pengeluaran: ' + category;
                    backButton.style.display = 'block';
                    categoryFiltersContainer.style.display = 'none';
                }

                function createCategoryFilters() {
                    categoryFiltersContainer.innerHTML = ''; // Clear existing filters
                    const categories = Object.keys(expenseData);
                    if (categories.length === 0) {
                        categoryFiltersContainer.innerHTML = '<p class="text-muted">Belum ada data pengeluaran berkategori untuk ditampilkan.</p>';
                        return;
                    }
                    categories.forEach(category => {
                        const button = document.createElement('button');
                        button.className = 'btn btn-outline-primary btn-sm me-2 mb-2';
                        button.textContent = category;
                        button.onclick = () => renderDetailView(category);
                        categoryFiltersContainer.appendChild(button);
                    });
                }

                // Initial setup
                createCategoryFilters();
                renderCategoryView();

                backButton.addEventListener('click', renderCategoryView);

                // --- USER PAYMENT SUMMARY BAR CHART SCRIPT ---
                const summaryData = <?= json_encode($user_payment_summary_data) ?>;
                const userPaymentSummaryCtx = document.getElementById('userPaymentSummaryChart');

                if (userPaymentSummaryCtx) {
                    new Chart(userPaymentSummaryCtx.getContext('2d'), {
                        type: 'bar',
                        data: summaryData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += context.parsed.y.toFixed(2) + '%';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    stacked: true,
                                    max: 100,
                                    ticks: {
                                        callback: function(value) {
                                            return value + '%'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // --- RADAR CHART FOR OPERATOR ACTIVITY ---
                var totalPengumuman = <?= $total_pengumuman ?? 0 ?>;
                var totalAgenda = <?= $total_agenda ?? 0 ?>;
                var operatorActivityRadarCtx = document.getElementById('operatorActivityRadarChart').getContext('2d');
                new Chart(operatorActivityRadarCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Jumlah Pengumuman', 'Jumlah Agenda'],
                        datasets: [{
                            label: 'Total',
                            data: [totalPengumuman, totalAgenda],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Aktivitas Operator'
                            }
                        },
                        scales: {
                            r: {
                                angleLines: {
                                    display: true
                                },
                                suggestedMin: 0,
                                ticks: {
                                    beginAtZero: true
                                }
                            }
                        }
                    }
                });
            });
        </script>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pengumuman Terbaru</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Tema</th>
                                        <th scope="col">Isi</th>
                                        <th scope="col">Tanggal Posting</th>
                                        <th scope="col">Label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Ambil 5 pengumuman terbaru
                                    $query_announcements = "SELECT tema, isi, tanggal_posting, label FROM announcements ORDER BY tanggal_posting DESC LIMIT 5";
                                    $result_announcements = mysqli_query($conn, $query_announcements);

                                    if (mysqli_num_rows($result_announcements) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_announcements)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['tema']) . "</td>";
                                            // Batasi panjang isi
                                            $isi_pendek = strlen($row['isi']) > 50 ? substr($row['isi'], 0, 50) . "..." : $row['isi'];
                                            echo "<td>" . htmlspecialchars($isi_pendek) . "</td>";
                                            echo "<td>" . date('d M Y', strtotime($row['tanggal_posting'])) . "</td>";
                                            echo "<td>";
                                            if (!empty($row['label'])) {
                                                $badge_class = $row['label'] == 'PENTING' ? 'badge bg-danger' : 'badge bg-secondary';
                                                echo "<span class='" . $badge_class . "'>" . htmlspecialchars($row['label']) . "</span>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>Tidak ada pengumuman.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>