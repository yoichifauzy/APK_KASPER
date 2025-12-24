<?php
// landing/organization.php
// Landing page showing organizational badges / emblems — Kaiadmin style
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Organisasi - Cash Coding</title>
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
    <!-- Font Awesome CDN to ensure icons are available on landing pages -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .org-card {
            cursor: pointer;
            transition: transform .12s, box-shadow .12s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .org-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .org-card .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.6rem;
            padding: 1rem;
        }

        .org-badge {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
            flex: 0 0 auto;
        }

        .org-badge img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
        }

        .org-badge .fallback-bd {
            display: none;
        }

        .org-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0;
        }

        .org-sub {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .org-desc {
            font-size: 0.85rem;
            color: #6c757d;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .search-org {
            max-width: 480px;
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
                    <div class="page-header mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold">Organisasi Sekolah</h3>
                            <p class="text-muted small mb-0">Kumpulan organisasi ekstrakurikuler & unit kegiatan. Klik kartu untuk melihat detail.</p>
                        </div>
                        <div>
                            <input id="searchOrg" class="form-control form-control-sm search-org" placeholder="Cari organisasi..." />
                        </div>
                    </div>

                    <div class="row" id="orgGrid">
                        <!-- Organization cards injected by JS -->
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-center text-muted small">Ingin menambahkan organisasi baru? Hubungi admin sekolah.</div>
                    </div>

                </div>
            </div>

            <footer class="footer">
                <?php include_once __DIR__ . '/layout_landing/footer.php'; ?>
            </footer>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="orgModal" tabindex="-1" aria-labelledby="orgModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orgModalLabel">Organization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div id="orgModalBadge" class="org-badge bg-primary"></div>
                        <div>
                            <h4 id="orgModalTitle" class="mb-1"></h4>
                            <div id="orgModalSub" class="text-muted small"></div>
                        </div>
                    </div>
                    <div id="orgModalDesc"></div>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Aktivitas</h6>
                            <ul id="orgModalActivities"></ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Kontak</h6>
                            <p id="orgModalContact" class="small text-muted"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button id="joinBtn" type="button" class="btn btn-primary">Gabung</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        // Organizations data
        const orgs = [{
                id: 'pataka',
                title: 'Organisasi PATAKA',
                sub: 'Tradisi & Upacara',
                color: '#0d6efd',
                desc: 'Bertanggung jawab pada tata upacara, bendera, dan tradisi sekolah.',
                activities: ['Upacara bendera', 'Latihan baris berbaris'],
                contact: 'pataka@school.example'
            },
            {
                id: 'padus',
                title: 'Organisasi PADUS',
                sub: 'Paduan Suara',
                color: '#6610f2',
                desc: 'Paduan suara sekolah; tampil pada acara-acara resmi.',
                activities: ['Latihan vokal', 'Konser kecil'],
                contact: 'padus@school.example'
            },
            {
                id: 'drumband',
                title: 'Organisasi Drumband',
                sub: 'Marching Band',
                color: '#198754',
                desc: 'Drumband untuk parade dan event olahraga.',
                activities: ['Latihan formasi', 'Pertunjukan'],
                contact: 'drumband@school.example'
            },
            {
                id: 'rohis',
                title: 'Organisasi Rohis',
                sub: 'Rohani Islam',
                color: '#0dcaf0',
                desc: 'Kegiatan keagamaan Islam: kajian, doa, dan kegiatan sosial.',
                activities: ['Kajian rutin', 'Bakti sosial'],
                contact: 'rohis@school.example'
            },
            {
                id: 'rokri',
                title: 'Organisasi Rokris',
                sub: 'Rohani Kristen',
                color: '#fd7e14',
                desc: 'Kegiatan rohani Kristen: kebaktian dan bakti sosial.',
                activities: ['Persekutuan doa', 'Pengabdian masyarakat'],
                contact: 'rokri@school.example'
            },
            {
                id: 'pcl',
                title: 'Organisasi PCL',
                sub: 'Programming Club',
                color: '#0b5ed7',
                desc: 'Klub pemrograman: workshop, lomba, dan project.',
                activities: ['Workshop coding', 'Hackathon'],
                contact: 'pcl@school.example'
            },
            {
                id: 'badminton',
                title: 'Organisasi Badminton',
                sub: 'Olahraga',
                color: '#20c997',
                desc: 'Tim badminton sekolah untuk kompetisi antar sekolah.',
                activities: ['Latihan rutin', 'Pertandingan'],
                contact: 'badminton@school.example'
            },
            {
                id: 'basket',
                title: 'Organisasi Basket',
                sub: 'Olahraga',
                color: '#fd3a4a',
                desc: 'Tim basket sekolah; fokus pada teknik dan team play.',
                activities: ['Latihan dribbling', 'Scrimmage'],
                contact: 'basket@school.example'
            },
            {
                id: 'volly',
                title: 'Organisasi Vollyball',
                sub: 'Olahraga',
                color: '#6f42c1',
                desc: 'Volly untuk siswa putra & putri.',
                activities: ['Latihan servis', 'Latihan passing'],
                contact: 'volly@school.example'
            },
            {
                id: 'soccer',
                title: 'Organisasi Sepakbola',
                sub: 'Olahraga',
                color: '#198754',
                desc: 'Tim sepakbola sekolah; latihan dan turnamen.',
                activities: ['Tactical training', 'Friendly match'],
                contact: 'soccer@school.example'
            },
            {
                id: 'futsal',
                title: 'Organisasi Futsal',
                sub: 'Olahraga',
                color: '#0d6efd',
                desc: 'Futsal untuk kompetisi antar kelas/sekolah.',
                activities: ['Latihan kecil', 'Turnamen internal'],
                contact: 'futsal@school.example'
            },
            {
                id: 'tenis',
                title: 'Organisasi Tenis Meja',
                sub: 'Olahraga',
                color: '#dc3545',
                desc: 'Pingpong/tenis meja untuk event dalam & luar sekolah.',
                activities: ['Latihan teknik', 'Turnamen'],
                contact: 'tenis@school.example'
            },
            {
                id: 'catur',
                title: 'Organisasi Catur',
                sub: 'Klub',
                color: '#0dcaf0',
                desc: 'Klub catur untuk pengembangan strategi dan logika.',
                activities: ['Latihan strategi', 'Lomba catur'],
                contact: 'catur@school.example'
            },
            {
                id: 'esport',
                title: 'Organisasi E-Sport',
                sub: 'Klub',
                color: '#6610f2',
                desc: 'Klub e-sport: tim gaming dan turnamen.',
                activities: ['Latihan tim', 'Turnamen online'],
                contact: 'esport@school.example'
            }
        ];

        function renderGrid(items) {
            const grid = document.getElementById('orgGrid');
            grid.innerHTML = '';
            items.forEach(o => {
                const col = document.createElement('div');
                col.className = 'col-sm-6 col-md-4 col-lg-3 mb-3';
                col.innerHTML = `
                    <div class="card org-card h-100" data-id="${o.id}">
                        <div class="card-body">
                            <div class="org-badge" style="background:${o.color}">${getIconHtml(o.id)}</div>
                            <div class="mt-1 org-title">${o.title}</div>
                            <div class="org-sub">${o.sub}</div>
                            <div class="org-desc mt-2">${o.desc}</div>
                        </div>
                    </div>
                `;
                // attach click handler
                col.querySelector('.org-card').addEventListener('click', () => showOrgDetail(o));
                grid.appendChild(col);
            });
        }

        function getIconHtml(id) {
            // map some ids to FontAwesome icons
            const map = {
                pataka: '<i class="fas fa-flag"></i>',
                padus: '<i class="fas fa-microphone-alt"></i>',
                drumband: '<i class="fas fa-drum"></i>',
                rohis: '<i class="fas fa-mosque"></i>',
                rokri: '<i class="fas fa-church"></i>',
                pcl: '<i class="fas fa-code"></i>',
                // For badminton we prefer a local SVG emblem (assets/badminton.svg).
                // Show the image when available; fallback to text 'BD' if it fails to load.
                badminton: '<img src="assets/badminton.svg" alt="Badminton" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'inline-block\'" /><span class="fw-bold fallback-bd">BD</span>',
                basket: '<i class="fas fa-basketball-ball"></i>',
                volly: '<i class="fas fa-volleyball-ball"></i>',
                soccer: '<i class="fas fa-futbol"></i>',
                futsal: '<i class="fas fa-shoe-prints"></i>',
                tenis: '<i class="fas fa-table-tennis"></i>',
                catur: '<i class="fas fa-chess"></i>',
                esport: '<i class="fas fa-gamepad"></i>'
            };
            return map[id] || '<i class="fas fa-users"></i>';
        }

        function showOrgDetail(o) {
            document.getElementById('orgModalLabel').textContent = o.title;
            const badge = document.getElementById('orgModalBadge');
            badge.style.background = o.color || '#0d6efd';
            badge.innerHTML = getIconHtml(o.id);
            document.getElementById('orgModalTitle').textContent = o.title;
            document.getElementById('orgModalSub').textContent = o.sub;
            document.getElementById('orgModalDesc').textContent = o.desc;
            const acts = document.getElementById('orgModalActivities');
            acts.innerHTML = '';
            (o.activities || []).forEach(a => {
                const li = document.createElement('li');
                li.textContent = a;
                acts.appendChild(li);
            });
            document.getElementById('orgModalContact').textContent = o.contact || '-';
            document.getElementById('joinBtn').setAttribute('data-org', o.id);
            var modal = new bootstrap.Modal(document.getElementById('orgModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderGrid(orgs);

            // search
            document.getElementById('searchOrg').addEventListener('input', function(e) {
                const q = (e.target.value || '').toLowerCase().trim();
                if (!q) {
                    renderGrid(orgs);
                    return;
                }
                const filtered = orgs.filter(o => (o.title + ' ' + o.sub + ' ' + o.desc).toLowerCase().includes(q));
                renderGrid(filtered);
            });

            // join button (example action)
            document.getElementById('joinBtn').addEventListener('click', function() {
                const oid = this.getAttribute('data-org');
                // placeholder: action to join org (requires backend). Show toast for now.
                const org = orgs.find(x => x.id === oid);
                if (org) {
                    // show notification
                    alert('Permintaan gabung ke ' + org.title + ' telah dikirim ke admin.');
                }
            });
        });
    </script>
</body>

</html>