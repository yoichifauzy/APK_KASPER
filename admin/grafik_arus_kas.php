<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Grafik Arus Kas (7 Hari Terakhir)</div>
                <div class="card-category">Menampilkan total pemasukan dan pengeluaran setiap hari.</div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="arusKasChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('arusKasChart').getContext('2d');
        var arusKasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: "Kas Masuk",
                    borderColor: '#177dff',
                    pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
                    pointRadius: 4,
                    backgroundColor: 'rgba(23, 125, 255, 0.1)',
                    legendColor: '#177dff',
                    fill: true,
                    data: <?php echo json_encode($chart_inflow); ?>
                }, {
                    label: "Kas Keluar",
                    borderColor: '#f3545d',
                    pointBackgroundColor: 'rgba(243, 84, 93, 0.6)',
                    pointRadius: 4,
                    backgroundColor: 'rgba(243, 84, 93, 0.1)',
                    legendColor: '#f3545d',
                    fill: true,
                    data: <?php echo json_encode($chart_outflow); ?>
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                },
                tooltips: {
                    bodySpacing: 4,
                    mode: "index",
                    intersect: 0,
                    xPadding: 10,
                    yPadding: 10,
                    caretPadding: 10
                },
                layout: {
                    padding: {
                        left: 15,
                        right: 15,
                        top: 15,
                        bottom: 15
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: "#999",
                            fontStyle: "500",
                            padding: 10,
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        gridLines: {
                            drawTicks: false,
                            display: false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            padding: 10,
                            fontColor: "#999",
                            fontStyle: "500"
                        }
                    }]
                }
            }
        });
    });
</script>