<?php
require_once '../config/cek_login.php';
include '../config/database.php';
otorisasi(['admin', 'operator']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin - Roadmap</title>
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
    <link href="https://unpkg.com/vis-timeline@7.4.9/dist/vis-timeline-graph2d.min.css" rel="stylesheet" />

    <style>
        #roadmap-timeline {
            background: #fff;
            border-radius: 6px;
        }

        #roadmap-list .list-group-item {
            cursor: pointer;
        }

        .card-title-sub {
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* Status colors */
        .badge-status {
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 0.75rem;
        }

        .status-planned {
            background: #e9ecef;
            color: #495057;
        }

        .status-inprogress {
            background: #ffe8a3;
            color: #7a5a00;
        }

        .status-active {
            background: #cfe8ff;
            color: #05507a;
        }

        .status-done {
            background: #d4edda;
            color: #0b5e2b;
        }

        /* Timeline item custom content */
        .roadmap-item-content {
            padding: 4px 6px;
        }

        .roadmap-item-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .roadmap-item-dates {
            font-size: 0.78rem;
            color: #6c757d;
        }

        /* Color the vis timeline items by status */
        .vis-item.status-planned .vis-item-content {
            background: linear-gradient(90deg, #f8f9fa, #e9ecef);
            border: 1px solid #e6e6e6;
            color: #212529;
        }

        .vis-item.status-inprogress .vis-item-content {
            background: linear-gradient(90deg, #fff4d6, #ffe8a3);
            border: 1px solid #f0dca3;
            color: #7a5a00;
        }

        .vis-item.status-active .vis-item-content {
            background: linear-gradient(90deg, #e6f4ff, #cfe8ff);
            border: 1px solid #bfe1ff;
            color: #05507a;
        }

        .vis-item.status-done .vis-item-content {
            background: linear-gradient(90deg, #e9f7ec, #d4edda);
            border: 1px solid #c6e8c9;
            color: #0b5e2b;
        }

        /* Left border accent on milestone list */
        .milestone-item {
            border-left: 4px solid transparent;
        }

        .milestone-item.planned {
            border-left-color: #adb5bd;
        }

        .milestone-item.inprogress {
            border-left-color: #f0c36a;
        }

        .milestone-item.active {
            border-left-color: #70b8ff;
        }

        .milestone-item.done {
            border-left-color: #8fd19a;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_admin/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <?php include 'layout_admin/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <main>
                        <div class="page-header mb-3">
                            <h3 class="fw-bold">Analysis — Roadmap</h3>
                            <ul class="breadcrumbs mt-1">
                                <li class="nav-home"><a href="index.php"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Analysis</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Roadmap</a></li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title">Timeline</h4>
                                            <div class="card-title-sub">Drag items to change schedule</div>
                                        </div>
                                        <div class="card-tools d-flex align-items-center">
                                            <div class="btn-group me-2" role="group" aria-label="status-filter">
                                                <button class="btn btn-outline-secondary btn-sm status-filter active" data-status="all">All</button>
                                                <button class="btn btn-outline-secondary btn-sm status-filter" data-status="planned">Planned</button>
                                                <button class="btn btn-outline-secondary btn-sm status-filter" data-status="inprogress">In Progress</button>
                                                <button class="btn btn-outline-secondary btn-sm status-filter" data-status="done">Done</button>
                                            </div>
                                            <button id="btn_refresh" class="btn btn-outline-primary btn-sm"><i class="fa fa-sync"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="roadmap-timeline" style="height:420px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title">Milestones</h4>
                                            <div class="card-title-sub">Manage roadmap items</div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <button id="btn-new-item" class="btn btn-primary btn-block mb-3">New Item</button>
                                        <ul id="roadmap-list" class="list-group"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>

            <?php include 'layout_admin/footer.php'; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalRoadmap" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Roadmap Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formRoadmap">
                        <input type="hidden" name="id" />
                        <div class="mb-2"><label class="form-label">Title</label><input class="form-control" name="title" required></div>
                        <div class="mb-2"><label class="form-label">Description</label><textarea class="form-control" name="description"></textarea></div>
                        <div class="row">
                            <div class="col"><label class="form-label">Start</label><input type="date" class="form-control" name="start_date"></div>
                            <div class="col"><label class="form-label">End</label><input type="date" class="form-control" name="end_date"></div>
                        </div>
                        <div class="mb-2 mt-2"><label class="form-label">Progress</label>
                            <input type="range" min="0" max="100" class="form-range" name="progress" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="saveRoadmap" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script src="https://unpkg.com/vis-timeline@7.4.9/dist/vis-timeline-graph2d.min.js"></script>

    <script>
        $(function() {
            var timeline, items = new vis.DataSet();

            function loadItems(filterStatus) {
                $.getJSON('api_roadmap.php', function(res) {
                    if (!res.success) return;
                    items.clear();
                    var data = res.data || [];
                    if (filterStatus && filterStatus !== 'all') {
                        data = data.filter(function(d) {
                            return (d.status || 'planned') === filterStatus;
                        });
                    }
                    data.forEach(function(it) {
                        var content = '<div class="roadmap-item-content"><div class="roadmap-item-title">' + (it.title || 'Untitled') + '</div><div class="roadmap-item-dates">' + (it.start_date ? it.start_date + ' → ' + (it.end_date || '-') : 'No dates') + '</div></div>';
                        items.add({
                            id: it.id,
                            content: content,
                            start: it.start_date || null,
                            end: it.end_date || null,
                            className: 'status-' + ((it.status) || 'planned')
                        });
                    });
                    renderList(data);
                });
            }

            function renderList(data) {
                var $l = $('#roadmap-list');
                $l.empty();
                data.forEach(function(it) {
                    var status = (it.status || 'planned');
                    var badgeClass = 'status-' + status;
                    var $left = $('<div>').append($('<strong>').text(it.title)).append($('<div class="text-muted small mt-1">').text(it.start_date ? it.start_date + ' → ' + (it.end_date || '-') : 'No dates'));
                    var $badge = $('<span>').addClass('badge-status me-2').addClass(badgeClass).text((status === 'inprogress') ? 'In Progress' : (status === 'done') ? 'Done' : (status === 'active') ? 'Active' : 'Planned');
                    var $progress = $('<div class="mt-2">').append($('<div class="progress" style="height:8px">').append($('<div class="progress-bar bg-success" role="progressbar">').css('width', (it.progress || 0) + '%')));
                    var $li = $('<li class="list-group-item d-flex justify-content-between align-items-start milestone-item">').addClass(status)
                        .append($('<div>').append($badge).append($left).append($progress))
                        .append($('<div class="text-end">').append($('<button class="btn btn-sm btn-primary me-2 btn-edit">Edit</button>').data('item', it)).append($('<button class="btn btn-sm btn-danger btn-del">Del</button>').data('item', it)));
                    $l.append($li);
                });
            }

            var container = document.getElementById('roadmap-timeline');
            var options = {
                editable: true,
                stack: false,
                margin: {
                    item: 10
                }
            };
            timeline = new vis.Timeline(container, items, options);

            timeline.on('doubleClick', function(props) {
                if (props.item) openEdit(props.item);
            });
            timeline.on('itemmove', function(props) {
                /* save move */
                saveItemDates(props.item, props.start, props.end);
            });
            timeline.on('itemresize', function(props) {
                saveItemDates(props.item, props.start, props.end);
            });

            function saveItemDates(id, start, end) {
                $.ajax({
                    url: 'api_roadmap.php',
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        id: id,
                        start_date: start ? start.toISOString().slice(0, 10) : null,
                        end_date: end ? end.toISOString().slice(0, 10) : null
                    }),
                    success: function() {
                        loadItems();
                    }
                });
            }

            $('#btn-new-item').on('click', function() {
                $('#formRoadmap')[0].reset();
                $('#formRoadmap input[name=id]').val('');
                $('#modalRoadmap').modal('show');
            });

            $(document).on('click', '.btn-edit', function() {
                var it = $(this).data('item');
                openEdit(it);
            });
            $(document).on('click', '.btn-del', function() {
                var it = $(this).data('item');
                if (!confirm('Delete?')) return;
                $.ajax({
                    url: 'api_roadmap.php?id=' + it.id,
                    method: 'DELETE',
                    success: function() {
                        loadItems();
                    }
                });
            });

            function openEdit(it) {
                $('#formRoadmap input[name=id]').val(it.id);
                $('#formRoadmap input[name=title]').val(it.title);
                $('#formRoadmap textarea[name=description]').val(it.description);
                $('#formRoadmap input[name=start_date]').val(it.start_date);
                $('#formRoadmap input[name=end_date]').val(it.end_date);
                $('#formRoadmap input[name=progress]').val(it.progress || 0);
                $('#modalRoadmap').modal('show');
            }

            $('#saveRoadmap').on('click', function() {
                var data = {};
                $('#formRoadmap').serializeArray().forEach(function(f) {
                    data[f.name] = f.value || null;
                });
                var id = data.id;
                var method = id ? 'PUT' : 'POST';
                $.ajax({
                    url: 'api_roadmap.php',
                    method: method,
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    success: function() {
                        $('#modalRoadmap').modal('hide');
                        loadItems();
                    }
                });
            });

            $('#btn_refresh').on('click', function() {
                loadItems($('.status-filter.active').data('status') || 'all');
            });

            // status filter buttons
            $(document).on('click', '.status-filter', function() {
                $('.status-filter').removeClass('active');
                $(this).addClass('active');
                var s = $(this).data('status');
                loadItems(s);
            });

            loadItems('all');
        });
    </script>

</body>

</html>