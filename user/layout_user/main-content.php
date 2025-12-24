<?php
// Main content for user role — includes dashboard charts and summaries
if (!isset($conn)) {
    @include_once __DIR__ . '/../../config/database.php';
}

// Load necessary components: summary cards, recent transactions, and the stacked chart
?>

<div class="main-content">
    <div class="page-header">
        <h4 class="page-title">Dashboard</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Overview</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Total Transaksi</h6>
                    <?php
                    $totalTrans = 0;
                    if (isset($conn) && isset($_SESSION['id_user'])) {
                        $uid = intval($_SESSION['id_user']);
                        $q = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM pembayaran WHERE id_user = '$uid'");
                        if ($q) {
                            $r = mysqli_fetch_assoc($q);
                            $totalTrans = intval($r['cnt']);
                        }
                    }
                    ?>
                    <h2 class="mb-0"><?php echo number_format($totalTrans); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5>(Stacked per Bulan)</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPembayaranUser" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Recent Transactions</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($conn) && isset($_SESSION['id_user'])) {
                                $uid = intval($_SESSION['id_user']);
                                $tq = mysqli_query($conn, "SELECT id_pembayaran, tanggal, keterangan, jumlah FROM pembayaran WHERE id_user = '$uid' ORDER BY tanggal DESC LIMIT 10");
                                $no = 1;
                                if ($tq) while ($row = mysqli_fetch_assoc($tq)) {
                                    echo '<tr>';
                                    echo '<td>' . $no++ . '</td>';
                                    echo '<td>' . date('d M Y', strtotime($row['tanggal'])) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['keterangan']) . '</td>';
                                    echo '<td>' . number_format($row['jumlah']) . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">My Summary</div>
                <div class="card-body">
                    <p>Saldo Kas: <?php echo isset($conn) ? number_format(0) : '0'; ?></p>
                    <p>Tagihan Aktif: <?php echo isset($conn) ? number_format(0) : '0'; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/chart.min.js"></script>
<script>
    // Fetch dataset for the stacked chart. The endpoint returns aggregated data for the current user as permitted.
    fetch('../operator/api_payment_status.php')
        .then(r => r.json())
        .then(data => {
            const ctx = document.getElementById('chartPembayaranUser').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels || [],
                    datasets: data.datasets || []
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
            });
        }).catch(err => console.error('Chart load error', err));
</script>