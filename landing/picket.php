<?php
// landing/picket.php
// Halaman Piket Kelas - Kanban 5 kolom (4 anggota tiap kolom)
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Piket Kelas - Kanban</title>
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        .kanban-board {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding: 1rem 0;
            overflow: auto;
        }

        .kanban-column {
            min-width: 240px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .kanban-column .col-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem;
        }

        .kanban-column .col-title {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .kanban-list {
            min-height: 120px;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            padding: 0.5rem;
        }

        .picket-card {
            background: #fff;
            border-radius: 8px;
            padding: 0.6rem;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.04);
            cursor: grab;
        }

        .member-name {
            font-weight: 600;
        }

        .member-role {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .kanban-footer {
            padding: 0.6rem;
            text-align: center;
        }

        .controls {
            margin-bottom: 0.8rem;
        }

        /* responsive small screens: allow columns to be full width */
        @media (max-width:768px) {
            .kanban-column {
                min-width: 80%;
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
                    <div class="page-header d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="fw-bold">Piket Kelas</h3>
                            <p class="text-muted small mb-0">Kelola jadwal piket kebersihan kelas menggunakan Kanban. Geser anggota untuk mereset tugas.</p>
                        </div>
                        <div></div>
                    </div>

                    <div class="kanban-board" id="kanbanBoard" aria-live="polite">
                        <!-- Columns injected by JS -->
                    </div>



                </div>
            </div>

            <footer class="footer">
                <?php include_once __DIR__ . '/layout_landing/footer.php'; ?>
            </footer>
        </div>
    </div>

    <!-- Optional modal to show member details -->
    <div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="memberInfo"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        // Default columns and members (5 columns, 4 members each)
        const defaultBoard = [{
                id: 'col-a',
                title: 'SENIN',
                members: [{
                    name: 'Syawalludin Nopaliansyah'
                }, {
                    name: 'Erintia Widya Pangestika'
                }, {
                    name: 'Tangguh Putra Mahardika'
                }]
            },
            {
                id: 'col-b',
                title: 'SELASA',
                members: [{
                    name: 'Danar Wahyu sudrajat'
                }, {
                    name: 'Najwan Caesar Fistiansyah'
                }, {
                    name: 'Ferdi Ferdiansyah'
                }]
            },
            {
                id: 'col-c',
                title: 'RABU',
                members: [{
                    name: 'Ahmad Fauzi'
                }, {
                    name: 'Raihan Ananda Permadi'
                }, {
                    name: 'Azmi Anindya'
                }]
            },
            {
                id: 'col-d',
                title: 'KAMIS',
                members: [{
                    name: 'Muhammad Pauzul Ulum'
                }, {
                    name: 'Apprilia Dwiyani'
                }, {
                    name: 'Muhammad Dzaky Ramdhani'
                }]
            },
            {
                id: 'col-e',
                title: 'JUMAT',
                members: [{
                    name: 'Hagi Sugara Putra'
                }, {
                    name: 'Mochammad Alvin Makmun'
                }]
            }
        ];

        function createColumn(col) {
            const container = document.createElement('div');
            container.className = 'kanban-column';
            container.dataset.colId = col.id;

            const header = document.createElement('div');
            header.className = 'col-header';
            const title = document.createElement('div');
            title.className = 'col-title';
            title.textContent = col.title;
            const count = document.createElement('div');
            count.className = 'text-muted small';
            count.textContent = col.members.length + ' anggota';
            header.appendChild(title);
            header.appendChild(count);

            const list = document.createElement('div');
            list.className = 'kanban-list';
            list.id = col.id;

            col.members.forEach(m => {
                const card = document.createElement('div');
                card.className = 'picket-card';
                card.draggable = false;
                card.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="member-name">${escapeHtml(m.name)}</div>
                            <div class="member-role">Anggota</div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-light btn-detail" title="Detail"><i class="fas fa-info-circle"></i></button>
                        </div>
                    </div>
                `;
                list.appendChild(card);
            });

            const footer = document.createElement('div');
            footer.className = 'kanban-footer';
            footer.innerHTML = '<small class="text-muted">Drag untuk memindahkan</small>';

            container.appendChild(header);
            container.appendChild(list);
            container.appendChild(footer);
            return container;
        }

        function renderBoard(board) {
            const boardEl = document.getElementById('kanbanBoard');
            boardEl.innerHTML = '';
            board.forEach(col => boardEl.appendChild(createColumn(col)));

            // initialize Sortable on each list and allow dragging between lists
            board.forEach(col => {
                const listEl = document.getElementById(col.id);
                Sortable.create(listEl, {
                    group: 'kanban',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                });
            });

            // attach detail handlers
            document.querySelectorAll('.btn-detail').forEach((btn, idx) => {
                btn.addEventListener('click', (e) => {
                    const card = e.target.closest('.picket-card');
                    const name = card.querySelector('.member-name').textContent;
                    document.getElementById('memberInfo').textContent = name + ' — anggota piket. Ubah di backend jika perlu.';
                    var modal = new bootstrap.Modal(document.getElementById('memberModal'));
                    modal.show();
                });
            });
        }

        function getBoardState() {
            const cols = Array.from(document.querySelectorAll('.kanban-column'));
            return cols.map(c => {
                const id = c.dataset.colId || '';
                const title = c.querySelector('.col-title')?.textContent || '';
                const members = Array.from(c.querySelectorAll('.member-name')).map(n => ({
                    name: n.textContent.trim()
                }));
                return {
                    id,
                    title,
                    members
                };
            });
        }

        // Local storage functions removed — page will render default board only

        function escapeHtml(unsafe) {
            return unsafe.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // render default board (no localStorage controls)
            renderBoard(defaultBoard);
        });
    </script>
</body>

</html>