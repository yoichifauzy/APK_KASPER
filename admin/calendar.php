<?php
require_once '../config/cek_login.php';
include '../config/database.php';
otorisasi(['admin']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Calendar - Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
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
            max-width: 100%;
        }

        .fc-event {
            cursor: pointer;
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

                <!-- Mobile breadcrumb: visible only on small screens to improve navigation -->
                <div class="d-lg-none bg-light py-2 px-3 border-top mobile-breadcrumb">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-link p-0 me-2 sidenav-toggler" aria-label="Open sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <nav aria-label="breadcrumb" style="flex:1">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard_admin.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Calendar</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Calendar</h3>
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
            <?php include 'layout_admin/footer.php'; ?>
        </div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="modalEvent" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEvent">
                    <div class="modal-body">
                        <input type="hidden" name="id" />
                        <div class="mb-2"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Start</label><input name="start" type="datetime-local" class="form-control"></div>
                        <div class="mb-2"><label class="form-label">End</label><input name="end" type="datetime-local" class="form-control"></div>
                        <div class="mb-2"><label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="meeting">Meeting</option>
                                <option value="milestone">Milestone</option>
                                <option value="deadline">Deadline</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Owner (ketik id atau nama)</label>
                            <input name="owner_id" id="event_owner" class="form-control" placeholder="Ketikan owner id atau nama">
                        </div>
                        <div class="mb-2"><label class="form-label">Participants (comma-separated IDs)</label>
                            <input name="participants" id="event_participants" class="form-control" placeholder="e.g. 2,5,8">
                        </div>
                        <div class="mb-2"><label class="form-label">Background Color</label>
                            <input name="bg_color" id="event_bg_color" type="color" class="form-control form-control-color" value="#3a87ad" title="Choose background color">
                        </div>
                        <div class="mb-2"><label class="form-label">Text Color</label>
                            <input name="text_color" id="event_text_color" type="color" class="form-control form-control-color" value="#ffffff" title="Choose text color">
                        </div>
                        <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_delete_event" class="btn btn-danger" style="display:none">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
            var eventModal = new bootstrap.Modal(document.getElementById('modalEvent'));
            var form = document.getElementById('formEvent');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                initialView: 'dayGridMonth',
                events: function(fetchInfo, successCallback) {
                    $.getJSON('api_calendar.php', {
                            start: fetchInfo.startStr,
                            end: fetchInfo.endStr
                        })
                        .done(function(res) {
                            if (res && res.success) {
                                var items = res.data.map(function(r) {
                                    return {
                                        id: r.id,
                                        title: r.title,
                                        start: r.start_datetime || r.start || r.start_date,
                                        end: r.end_datetime || r.end,
                                        backgroundColor: r.bg_color || '',
                                        textColor: r.text_color || '',
                                        extendedProps: {
                                            description: r.description,
                                            type: r.type,
                                            owner: r.owner_id,
                                            participants: r.participants
                                        }
                                    };
                                });
                                successCallback(items);
                            } else {
                                successCallback([]);
                            }
                        }).fail(function() {
                            successCallback([]);
                        });
                },
                editable: true,
                selectable: true,
                dateClick: function(info) {
                    form.reset();
                    form.querySelector('input[name=id]').value = '';
                    form.querySelector('input[name=start]').value = info.dateStr + 'T00:00';
                    form.querySelector('input[name=end]').value = info.dateStr + 'T01:00';
                    document.getElementById('btn_delete_event').style.display = 'none';
                    eventModal.show();
                },
                eventClick: function(info) {
                    var ev = info.event;
                    form.reset();
                    form.querySelector('input[name=id]').value = ev.id;
                    form.querySelector('input[name=title]').value = ev.title;
                    form.querySelector('input[name=start]').value = ev.start ? ev.start.toISOString().slice(0, 16) : '';
                    form.querySelector('input[name=end]').value = ev.end ? ev.end.toISOString().slice(0, 16) : '';
                    form.querySelector('select[name=type]').value = ev.extendedProps.type || 'other';
                    form.querySelector('input[name=owner_id]').value = ev.extendedProps.owner || '';
                    form.querySelector('input[name=participants]').value = ev.extendedProps.participants || '';
                    // colors
                    try {
                        var bg = ev.backgroundColor || ev.extendedProps.bg_color || '';
                        var txt = ev.textColor || ev.extendedProps.text_color || '';
                        if (bg) form.querySelector('input[name=bg_color]').value = bg;
                        if (txt) form.querySelector('input[name=text_color]').value = txt;
                    } catch (e) {}
                    form.querySelector('textarea[name=description]').value = ev.extendedProps.description || '';
                    document.getElementById('btn_delete_event').style.display = 'inline-block';
                    eventModal.show();
                },
                eventDidMount: function(info) {
                    // Apply inline styles if colors provided (safety for different builds)
                    try {
                        if (info.event.backgroundColor) info.el.style.backgroundColor = info.event.backgroundColor;
                        if (info.event.textColor) info.el.style.color = info.event.textColor;
                    } catch (e) {
                        // ignore
                    }
                }
            });

            calendar.render();

            // Form submit -> POST or PUT JSON
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var data = {};
                new FormData(form).forEach(function(value, key) {
                    data[key] = value;
                });
                // Ensure color values are passed (color inputs produce hex values)
                if (!data.bg_color && document.getElementById('event_bg_color')) data.bg_color = document.getElementById('event_bg_color').value;
                if (!data.text_color && document.getElementById('event_text_color')) data.text_color = document.getElementById('event_text_color').value;
                var method = data.id ? 'PUT' : 'POST';
                $.ajax({
                    url: 'api_calendar.php',
                    method: method,
                    data: JSON.stringify(data),
                    contentType: 'application/json'
                }).done(function() {
                    calendar.refetchEvents();
                    eventModal.hide();
                }).fail(function(xhr) {
                    alert('Gagal menyimpan event: ' + (xhr.responseText || xhr.statusText));
                });
            });

            // Delete
            $('#btn_delete_event').on('click', function() {
                var id = form.querySelector('input[name=id]').value;
                if (!id) return;
                if (!confirm('Hapus event ini?')) return;
                $.ajax({
                    url: 'api_calendar.php?id=' + encodeURIComponent(id),
                    method: 'DELETE'
                }).done(function() {
                    calendar.refetchEvents();
                    eventModal.hide();
                }).fail(function(xhr) {
                    alert('Gagal menghapus: ' + (xhr.responseText || xhr.statusText));
                });
            });
        });
    </script>
</body>

</html>