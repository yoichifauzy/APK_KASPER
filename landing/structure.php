<?php
// landing/structure.php
// Organizational structure page for Cash Coding / KASPER
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Struktur Organisasi - Cash Coding</title>
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
        /* Small adjustments for the org chart area */
        #chart_div {
            width: 100%;
            min-height: 420px;
            overflow: auto;
            padding: 20px 0;
        }

        .org-controls {
            margin-bottom: 1rem;
        }

        .org-card .card-body {
            padding: .9rem;
        }

        .node-photo {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: .5rem;
        }

        .org-note {
            font-size: .92rem;
            color: #6c757d;
        }
    </style>

    <!-- Google Charts loader (OrgChart) from Google's CDN -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
                    <div class="page-header mb-3">
                        <h3 class="fw-bold">Struktur Organisasi</h3>
                        <ul class="breadcrumbs mt-1">
                            <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Landing</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Struktur</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card org-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="card-title">Struktur Organisasi KASPER</h4>
                                        <p class="org-note mb-0">Tampilan struktur organisasi interaktif (Google OrgChart). Anda dapat memuat struktur dari sumber JSON eksternal atau menggunakan contoh bawaan.</p>
                                    </div>
                                    <div class="org-controls d-flex align-items-center">
                                        <button id="btnReset" class="btn btn-outline-secondary btn-sm">Gunakan Contoh</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="chart_div"></div>
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

    <script>
        // Default sample structure data (nodes with id, name, title, parent)
        const sampleData = [{
                id: 'ketua',
                name: 'NAJWAN',
                title: 'Ketua Kelas',
                parent: '',
                photo: ''
            },
            {
                id: 'wakil',
                name: 'RAIHAN',
                title: 'Wakil Ketua',
                parent: 'ketua',
                photo: ''
            },
            {
                id: 'bendahara',
                name: 'A FAUZI',
                title: 'Bendahara',
                parent: 'ketua',
                photo: ''
            },
            {
                id: 'sekretaris',
                name: 'APRIL',
                title: 'Sekretaris',
                parent: 'ketua',
                photo: ''
            },
            {
                id: 'div_perlengkapan',
                name: 'DANAR',
                title: 'Ketua Divisi Perlengkapan',
                parent: 'wakil',
                photo: ''
            },
            {
                id: 'div_peralatan',
                name: 'DZAKY',
                title: 'Ketua Divisi Peralatan',
                parent: 'wakil',
                photo: ''
            },
            {
                id: 'div_komunikasi',
                name: 'SYAWAL',
                title: 'Ketua Divisi Komunikasi',
                parent: 'wakil',
                photo: ''
            },
            {
                id: 'div_kreatif',
                name: 'HAGI',
                title: 'Ketua Divisi Kreatif',
                parent: 'wakil',
                photo: ''
            },
            {
                id: 'anggota_1',
                name: 'TANGGUH',
                title: 'Anggota',
                parent: 'div_perlengkapan',
                photo: ''
            },
            {
                id: 'anggota_2',
                name: 'ALVIN',
                title: 'Anggota',
                parent: 'div_peralatan',
                photo: ''
            },
            {
                id: 'anggota_3',
                name: 'FERDI',
                title: 'Anggota',
                parent: 'div_komunikasi',
                photo: ''
            },
            {
                id: 'anggota_4',
                name: 'ULUM',
                title: 'Anggota',
                parent: 'div_kreatif',
                photo: ''
            },
            {
                id: 'anggota_5',
                name: 'AZMI',
                title: 'Anggota',
                parent: 'div_kreatif',
                photo: ''
            },
            {
                id: 'anggota_6',
                name: 'ERINT',
                title: 'Anggota',
                parent: 'div_kreatif',
                photo: ''
            }
        ];

        // Load Google OrgChart
        google.charts.load('current', {
            packages: ["orgchart"]
        });
        google.charts.setOnLoadCallback(function() {
            drawChartFromData(sampleData);
            populateMemberList(sampleData);
        });

        // Draw org chart from array of nodes
        function drawChartFromData(nodes) {
            const data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');

            // Helper map: id -> formatted HTML
            const map = {};
            nodes.forEach(n => {
                // Create HTML for node (small photo + name + title)
                const photoHtml = n.photo ? `<img class="node-photo" src="${n.photo}" alt="">` : '';
                const label = `<div style="display:flex;align-items:center"><div>${photoHtml}</div><div><strong>${escapeHtml(n.name)}</strong><div style="font-size:0.85em;color:#666">${escapeHtml(n.title)}</div></div></div>`;
                map[n.id] = label;
            });

            nodes.forEach(n => {
                const parent = n.parent && n.parent.length ? map[n.parent] : '';
                data.addRow([{
                    v: n.id,
                    f: map[n.id]
                }, parent ? n.parent : '', '']);
            });

            const chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
            chart.draw(data, {
                allowHtml: true,
                nodeClass: 'org-node'
            });
        }

        // Populate the member cards list under the chart
        function populateMemberList(nodes) {
            const container = document.getElementById('member_list');
            container.innerHTML = '';
            nodes.forEach(n => {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                const photo = n.photo ? `<img src="${n.photo}" class="img-fluid rounded-circle" style="width:48px;height:48px;object-fit:cover">` : `<div class="avatar avatar-sm bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px">${(n.name||'')[0]||'?'}</div>`;
                col.innerHTML = `
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">${photo}</div>
                            <div>
                                <div class="fw-bold">${escapeHtml(n.name)}</div>
                                <div class="text-muted small">${escapeHtml(n.title)}</div>
                                <div class="text-muted small">Parent: ${escapeHtml(n.parent || '—')}</div>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(col);
            });
        }

        // Escape helper
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, function(s) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                })[s];
            });
        }

        // Reset to sample (uses sampleData by default)
        document.getElementById('btnReset').addEventListener('click', function() {
            drawChartFromData(sampleData);
            populateMemberList(sampleData);
        });
    </script>
</body>

</html>