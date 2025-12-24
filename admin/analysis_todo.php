<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);

include '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin - Analysis ToDo</title>
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
        .kanban-board {
            display: flex;
            gap: 16px;
        }

        .kanban-column {
            background: #f7f7f9;
            border: 1px solid #e1e8;
            border-radius: 6px;
            padding: 12px;
            width: 33%;
            min-height: 320px;
        }

        .kanban-column h5 {
            margin-top: 0;
        }

        .kanban-item {
            background: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
            cursor: move;
            display: block;
        }

        .kanban-item.dragging {
            opacity: 0.6;
        }

        .kanban-meta {
            font-size: 12px;
            color: #6c757d;
        }

        .kanban-column .items {
            min-height: 48px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_admin/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.php" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                        </div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>

                <?php include 'layout_admin/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <main>
                        <div class="page-header mb-3">
                            <h3 class="fw-bold">Analysis — Idea Board</h3>
                            <ul class="breadcrumbs mt-1">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Analysis</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Idea Board</a></li>
                            </ul>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Idea Board / ToDo (Kanban)</h4>
                                    <span class="text-muted small">Drag cards between columns to change status and order</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm" id="btn_refresh"><i class="fa fa-sync"></i> Refresh</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <h5 class="mb-2">Tambah Ide</h5>
                                            <input id="todo_title" class="form-control mb-2" placeholder="Judul ide">
                                            <textarea id="todo_desc" class="form-control mb-2" placeholder="Deskripsi (opsional)"></textarea>
                                            <input id="todo_due" type="date" class="form-control mb-2">
                                            <div class="d-grid">
                                                <button id="btn_add" class="btn btn-primary">Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="kanban-board" id="board">
                                            <div class="kanban-column" data-status="todo">
                                                <h5>To Do</h5>
                                                <div class="items" id="col_todo"></div>
                                            </div>
                                            <div class="kanban-column" data-status="inprogress">
                                                <h5>In Progress</h5>
                                                <div class="items" id="col_inprogress"></div>
                                            </div>
                                            <div class="kanban-column" data-status="done">
                                                <h5>Done</h5>
                                                <div class="items" id="col_done"></div>
                                            </div>
                                        </div>
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

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <!-- jQuery UI for sortable (drag/drop) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

    <script>
        $(function() {
            function escapeHtml(text) {
                return text ? text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
            }

            function renderItem(item) {
                var el = $('<div>').addClass('kanban-item').attr('data-id', item.id);
                var meta = '<div class="kanban-meta">' + (item.due_date ? item.due_date : '') + '</div>';
                var actions = '<div class="mt-2 d-flex justify-content-end gap-1"><button class="btn btn-sm btn-secondary btn-edit">Edit</button><button class="btn btn-sm btn-danger btn-delete">Hapus</button></div>';
                el.html('<strong>' + escapeHtml(item.title) + '</strong>' + meta + '<div style="margin-top:6px">' + escapeHtml(item.description || '') + '</div>' + actions);
                return el;
            }

            function setupSortable() {
                if (!$.fn.sortable) {
                    console.error('jQuery UI sortable not available');
                    return;
                }
                // destroy existing to avoid duplicate handlers
                try {
                    $('.kanban-column .items').sortable('destroy');
                } catch (e) {}
                $('.kanban-column .items').sortable({
                    connectWith: '.kanban-column .items',
                    placeholder: 'ui-state-highlight',
                    forcePlaceholderSize: true,
                    start: function(event, ui) {
                        console.log('sortable start', ui.item.data('id'));
                        ui.item.addClass('dragging');
                    },
                    stop: function(event, ui) {
                        console.log('sortable stop', ui.item.data('id'));
                        ui.item.removeClass('dragging');
                        sendReorder();
                    }
                }).disableSelection();
                console.log('sortable initialized on', $('.kanban-column .items').length, 'lists');
            }

            function loadAll() {
                $.get('api_todo.php', function(res) {
                    if (!res.success) return alert('Gagal memuat data');
                    $('#col_todo,#col_inprogress,#col_done').empty();
                    res.data.forEach(function(item) {
                        $('#col_' + item.status).append(renderItem(item));
                    });
                    setupSortable();
                }, 'json');
            }

            $('#btn_add').click(function() {
                var title = $('#todo_title').val();
                var desc = $('#todo_desc').val();
                var due = $('#todo_due').val();
                if (!title) return alert('Isi judul');
                $.ajax({
                    url: 'api_todo.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        title: title,
                        description: desc,
                        due_date: due
                    }),
                    success: function(r) {
                        if (r.success) {
                            $('#todo_title,#todo_desc,#todo_due').val('');
                            loadAll();
                        } else alert('Gagal: ' + (r.message || ''));
                    }
                });
            });

            $('#btn_refresh').click(function(e) {
                e.preventDefault();
                loadAll();
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.stopPropagation();
                if (!confirm('Hapus item?')) return;
                var id = $(this).closest('.kanban-item').data('id');
                $.ajax({
                    url: 'api_todo.php?id=' + id,
                    method: 'DELETE',
                    success: function(r) {
                        if (r.success) loadAll();
                        else alert('Gagal');
                    }
                });
            });

            $(document).on('click', '.btn-edit', function(e) {
                e.stopPropagation();
                var id = $(this).closest('.kanban-item').data('id');
                $.get('api_todo.php', {
                    status: ''
                }, function(res) {
                    if (!res.success) return alert('Gagal');
                    var item = res.data.find(function(it) {
                        return parseInt(it.id) === parseInt(id);
                    });
                    if (!item) return alert('Item tidak ditemukan');
                    $('#modal_todo_id').val(item.id);
                    $('#modal_todo_title').val(item.title);
                    $('#modal_todo_desc').val(item.description);
                    $('#modal_todo_due').val(item.due_date);
                    $('#modal_todo_status').val(item.status);
                    var myModal = new bootstrap.Modal(document.getElementById('modal_todo'));
                    myModal.show();
                }, 'json');
            });

            $('#modal_save').click(function() {
                var id = $('#modal_todo_id').val();
                var title = $('#modal_todo_title').val();
                var desc = $('#modal_todo_desc').val();
                var due = $('#modal_todo_due').val();
                var status = $('#modal_todo_status').val();
                $.ajax({
                    url: 'api_todo.php',
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        id: id,
                        title: title,
                        description: desc,
                        due_date: due,
                        status: status
                    }),
                    success: function(r) {
                        if (r.success) {
                            var m = bootstrap.Modal.getInstance(document.getElementById('modal_todo'));
                            m.hide();
                            loadAll();
                        } else alert('Gagal menyimpan');
                    }
                });
            });

            function sendReorder() {
                var items = [];
                ['todo', 'inprogress', 'done'].forEach(function(status) {
                    $('#col_' + status + ' .kanban-item').each(function(i, el) {
                        items.push({
                            id: $(el).data('id'),
                            status: status,
                            position: i + 1
                        });
                    });
                });
                $.ajax({
                    url: 'api_todo.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        action: 'reorder',
                        items: items
                    }),
                    success: function(r) {
                        if (!r.success) alert('Gagal menyimpan urutan');
                    }
                });
            }

            loadAll();
        });
    </script>

    <!-- Edit modal -->
    <div class="modal fade" id="modal_todo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Idea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal_todo_id">
                    <div class="mb-2"><label class="form-label">Judul</label><input id="modal_todo_title" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Deskripsi</label><textarea id="modal_todo_desc" class="form-control"></textarea></div>
                    <div class="mb-2"><label class="form-label">Due Date</label><input id="modal_todo_due" type="date" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Status</label>
                        <select id="modal_todo_status" class="form-select">
                            <option value="todo">todo</option>
                            <option value="inprogress">inprogress</option>
                            <option value="done">done</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="modal_save" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>