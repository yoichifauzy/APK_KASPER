<?php
// landing/class_school.php
// Classroom seating map (14 seats) — Kaiadmin style
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Denah Kelas - Cash Coding</title>
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
        /* Classroom layout styles */
        .classroom-wrap {
            padding: 20px 0;
        }

        .board {
            background: linear-gradient(180deg, #ffffff, #f8f9fa);
            border: 2px solid #ced4da;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 -6px 10px rgba(0, 0, 0, 0.03);
        }

        .teacher-desk {
            background: #f1f3f5;
            border: 1px solid #e9ecef;
            padding: 8px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .seats {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 18px;
        }

        .seat-row {
            display: flex;
            gap: 18px;
            justify-content: center;
        }

        .seat {
            width: 110px;
            height: 70px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: default;
            position: relative;
            transition: transform .12s, box-shadow .12s;
        }

        .seat:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
        }

        .seat .name {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .seat .meta {
            font-size: 0.78rem;
            color: #6c757d;
        }

        .seat .seat-no {
            position: absolute;
            top: 6px;
            right: 8px;
            font-size: 0.75rem;
            color: #adb5bd;
        }

        .seat-empty {
            background: linear-gradient(180deg, #ffffff, #fafafa);
        }

        .legend {
            margin-top: 12px;
        }

        /* responsive */
        @media (max-width:768px) {
            .seat {
                width: 88px;
                height: 66px;
            }

            .seat-row {
                gap: 12px;
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

            <div class="container">
                <div class="page-inner">
                    <div class="page-header mb-3">
                        <h3 class="fw-bold">Denah Kelas</h3>
                        <ul class="breadcrumbs mt-1">
                            <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Landing</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Denah Kelas</a></li>
                        </ul>
                    </div>

                    <div class="row classroom-wrap">
                        <div class="col-md-10 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="card-title mb-0">Denah Kelas - Rombel</h5>
                                            <p class="text-muted small mb-0">Arah depan: papan tulis & meja dosen (atas).</p>
                                        </div>
                                        <div class="text-end">
                                            <div class="teacher-desk">Meja Dosen</div>
                                        </div>
                                    </div>

                                    <div class="board mb-3">Papan Tulis</div>

                                    <div class="seats" id="seatsContainer">
                                        <!-- Rows will be injected by JS -->
                                    </div>

                                    <div class="legend mt-3">
                                        <span class="badge bg-light text-dark me-2">&nbsp;</span> Kursi terisi
                                        <span class="ms-3 badge bg-white text-muted border">&nbsp;</span> Kosong
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
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        // Sample seating data (14 seats). Map seatId -> owner name (empty means seat empty)
        const seatOwners = {
            'S1': 'Muhammad Dzaky',
            'S2': 'Danar Wahyu',
            'S3': '',
            'S4': 'Hagi Sugara',
            'S5': 'Alvi Makmun',
            'S6': 'Pauzul Ulum',
            'S7': 'Syawaludin',
            'S8': 'Aprilia Dwi',
            'S9': 'Najwan Caesar',
            'S10': 'Erintya Dwi',
            'S11': 'Azmi Anindya',
            'S12': 'Ahm Fauzi',
            'S13': 'Tangguh P',
            'S14': 'Ferdi F',
            'S15': 'Raihan AP'

        };

        // Seat layout as rows (top is front near board): use 4,4,4,2 arrangement
        const seatRows = [
            ['S1', 'S2', 'S3', 'S4'],
            ['S5', 'S6', 'S7', 'S8'],
            ['S9', 'S10', 'S11', 'S12'],
            ['S13', 'S14', 'S15', 'S16']
        ];

        function createSeatElement(id) {
            const name = seatOwners[id] || '';
            const el = document.createElement('div');
            el.className = 'seat' + (name ? '' : ' seat-empty');
            el.setAttribute('data-seat', id);
            el.setAttribute('data-bs-toggle', 'tooltip');
            el.setAttribute('data-bs-placement', 'top');
            el.setAttribute('title', name ? name : 'Kosong');

            el.innerHTML = `
                <div class="seat-no">${id}</div>
                <div class="name">${name ? name : '---'}</div>
                <div class="meta">${name ? 'Siswa' : 'Kosong'}</div>
            `;

            return el;
        }

        function renderSeats() {
            const container = document.getElementById('seatsContainer');
            container.innerHTML = '';
            seatRows.forEach(row => {
                const rowWrap = document.createElement('div');
                rowWrap.className = 'seat-row';
                row.forEach(seatId => {
                    rowWrap.appendChild(createSeatElement(seatId));
                });
                container.appendChild(rowWrap);
            });

            // init bootstrap tooltips for dynamic elements
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderSeats();

            // Optional: show a small floating label near cursor on hover
            const float = document.createElement('div');
            float.style.position = 'fixed';
            float.style.pointerEvents = 'none';
            float.style.padding = '6px 8px';
            float.style.background = 'rgba(0,0,0,0.75)';
            float.style.color = '#fff';
            float.style.fontSize = '0.85rem';
            float.style.borderRadius = '4px';
            float.style.display = 'none';
            document.body.appendChild(float);

            document.querySelectorAll('.seat').forEach(s => {
                s.addEventListener('mousemove', e => {
                    const name = s.getAttribute('title') || '';
                    float.textContent = name;
                    float.style.left = (e.clientX + 12) + 'px';
                    float.style.top = (e.clientY + 12) + 'px';
                    float.style.display = 'block';
                });
                s.addEventListener('mouseleave', () => {
                    float.style.display = 'none';
                });
            });
        });
    </script>
</body>

</html>