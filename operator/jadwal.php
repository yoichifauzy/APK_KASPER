<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Jadwal Kegiatan - KASPER</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() { sessionStorage.fonts = true; },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    
    <style>
        #calendar {
            max-width: 1100px;
            margin: 0 auto;
        }
        .fc-event {
            cursor: pointer;
        }
        .fc-event-main {
            color: white !important;
        }
        /* Custom colors for categories */
        .fc-event[data-category="pembayaran"] { background-color: #dc3545; border-color: #dc3545; }
        .fc-event[data-category="ujian"] { background-color: #ffc107; border-color: #ffc107; }
        .fc-event[data-category="rapat"] { background-color: #0dcaf0; border-color: #0dcaf0; }
        .fc-event[data-category="umum"] { background-color: #198754; border-color: #198754; }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'layout_operator/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="dashboard_operator.php" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
                        <div class="nav-toggle"><button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button><button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>
                <?php include 'layout_operator/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Jadwal Kegiatan</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Agenda</a></li>
                        </ul>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Tambah Agenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="eventForm">
                    <div class="modal-body">
                        <input type="hidden" id="event_id" name="id">
                        <input type="hidden" id="action" name="action" value="add">
                        <div class="mb-3">
                            <label for="event_title" class="form-label">Judul Agenda</label>
                            <input type="text" class="form-control" id="event_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="event_description" class="form-label">Deskripsi (Opsional)</label>
                            <textarea class="form-control" id="event_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_start" class="form-label">Waktu Mulai</label>
                                <input type="datetime-local" class="form-control" id="event_start" name="start_datetime" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="event_end" class="form-label">Waktu Selesai</label>
                                <input type="datetime-local" class="form-control" id="event_end" name="end_datetime" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="event_category" class="form-label">Kategori</label>
                            <select class="form-select" id="event_category" name="category" required>
                                <option value="umum">Umum</option>
                                <option value="pembayaran">Pembayaran</option>
                                <option value="ujian">Ujian</option>
                                <option value="rapat">Rapat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="deleteEventBtn" class="btn btn-danger me-auto" style="display:none;">Hapus</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            var eventForm = document.getElementById('eventForm');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                initialView: 'dayGridMonth',
                events: 'api_jadwal.php',
                editable: true,
                selectable: true,
                dateClick: function(info) {
                    eventForm.reset();
                    $('#action').val('add');
                    $('#eventModalLabel').text('Tambah Agenda');
                    $('#deleteEventBtn').hide();
                    $('#event_start').val(info.dateStr + 'T00:00');
                    $('#event_end').val(info.dateStr + 'T01:00');
                    eventModal.show();
                },
                eventClick: function(info) {
                    eventForm.reset();
                    $('#action').val('update');
                    $('#eventModalLabel').text('Edit Agenda');
                    $('#deleteEventBtn').show();
                    
                    $('#event_id').val(info.event.id);
                    $('#event_title').val(info.event.title);
                    $('#event_description').val(info.event.extendedProps.description || '');
                    $('#event_category').val(info.event.extendedProps.category);

                    // Format dates for datetime-local input
                    var start = new Date(info.event.start);
                    var end = new Date(info.event.end || info.event.start);
                    start.setMinutes(start.getMinutes() - start.getTimezoneOffset());
                    end.setMinutes(end.getMinutes() - end.getTimezoneOffset());
                    $('#event_start').val(start.toISOString().slice(0,16));
                    $('#event_end').val(end.toISOString().slice(0,16));

                    eventModal.show();
                },
                eventDidMount: function(info) {
                    // Add category as a data attribute for styling
                    info.el.setAttribute('data-category', info.event.extendedProps.category);
                }
            });
            calendar.render();

            // Handle Form Submission
            eventForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                fetch('api_jadwal.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        calendar.refetchEvents();
                        eventModal.hide();
                    } else {
                        swal("Gagal!", data.message || "Terjadi kesalahan.", "error");
                    }
                });
            });

            // Handle Delete Event
            $('#deleteEventBtn').on('click', function() {
                var eventId = $('#event_id').val();
                swal({
                    title: 'Anda yakin?',
                    text: "Agenda ini akan dihapus secara permanen!",
                    icon: 'warning',
                    buttons: {
                        cancel: { text: 'Batal', visible: true },
                        confirm: { text: 'Ya, Hapus' }
                    },
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', eventId);
                        fetch('api_jadwal.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                calendar.refetchEvents();
                                eventModal.hide();
                            } else {
                                swal("Gagal!", data.message || "Gagal menghapus agenda.", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
