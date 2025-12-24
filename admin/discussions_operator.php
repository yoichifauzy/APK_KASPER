<?php
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['admin']);

include '../config/database.php';

// Load operators list
$operators = [];
$qop = "SELECT id_user, username, nama_lengkap FROM user WHERE role = 'operator' ORDER BY nama_lengkap";
$resop = mysqli_query($conn, $qop);
if ($resop) while ($r = mysqli_fetch_assoc($resop)) $operators[] = $r;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Direct Chat - Operators (Admin)</title>
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
        .operators-list {
            max-height: 60vh;
            overflow-y: auto;
        }

        .operators-list .list-group-item {
            cursor: pointer;
        }

        .chat-container {
            height: 50vh;
            overflow-y: auto;
            border: 1px solid #e3e3e3;
            padding: 12px;
            background: #fff;
        }

        .chat-message {
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 12px;
            max-width: 75%;
        }

        .chat-message.admin {
            background: #007bff;
            color: #fff;
            align-self: flex-end;
        }

        /* Force admin messages to the right in case flex rules are overridden */
        .chat-message.admin {
            margin-left: auto;
            text-align: left;
            /* keep message text left-aligned inside bubble */
        }

        .chat-message.operator {
            background: #f1f3f5;
            color: #111;
            align-self: flex-start;
        }

        /* Ensure action icons are visible and clickable */
        .message-actions a {
            text-decoration: none;
            margin-left: 6px;
        }

        .message-actions i {
            font-size: 0.95rem;
            vertical-align: middle;
            pointer-events: none;
            /* let the anchor handle clicks */
        }

        .message-actions a {
            cursor: pointer;
        }

        .chat-message.admin .message-actions .fa-pencil-alt {
            color: #ffc107;
            /* warning / yellow */
        }

        .chat-message.admin .message-actions .fa-trash-alt {
            color: #dc3545;
            /* danger / red */
        }

        .chat-wrap {
            display: flex;
            gap: 20px;
        }

        .chat-column {
            flex: 1;
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
                        <a href="index.html" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
                        <div class="nav-toggle"><button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button><button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>
                <?php include 'layout_admin/navbar.php'; ?>
            </div>

            <div class="main-content">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Chat with Operators</h4>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="chat-wrap">
                                <div class="chat-column" style="max-width:320px">
                                    <h5>Operators</h5>
                                    <div class="list-group operators-list" id="operatorsList">
                                        <?php foreach ($operators as $op): ?>
                                            <a href="#" class="list-group-item list-group-item-action" data-id="<?= htmlspecialchars($op['id_user']) ?>"><?= htmlspecialchars($op['nama_lengkap'] ?: $op['username']) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="chat-column">
                                    <h5 id="chatWith">Select an operator to start</h5>
                                    <div class="chat-container d-flex flex-column" id="chatContainer"></div>
                                    <div class="mt-3" id="chatInputRow" style="display:none">
                                        <form id="privateChatForm">
                                            <input type="hidden" id="operatorId" name="operator_id" value="">
                                            <div class="input-group">
                                                <textarea class="form-control" id="privateMessage" name="message" rows="2" placeholder="Type your message to operator..."></textarea>
                                                <button class="btn btn-primary" type="submit">Send</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'layout_admin/footer.php'; ?>
        </div>
    </div>

    <!-- Edit Private Message Modal -->
    <div class="modal fade" id="editPrivateModal" tabindex="-1" aria-labelledby="editPrivateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPrivateModalLabel">Edit Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPrivateForm">
                    <div class="modal-body">
                        <input type="hidden" id="editMessageId" name="message_id" value="">
                        <div class="mb-2">
                            <label for="editMessageContent" class="form-label">Isi Pesan</label>
                            <textarea id="editMessageContent" name="message_content" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Private Message Modal -->
    <div class="modal fade" id="deletePrivateModal" tabindex="-1" aria-labelledby="deletePrivateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePrivateModalLabel">Hapus Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pesan ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <input type="hidden" id="deleteMessageId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmDeleteButton" class="btn btn-danger">Hapus</button>
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
        let selectedOperator = null;
        const chatContainer = document.getElementById('chatContainer');

        const currentAdminId = Number(<?= json_encode($_SESSION['id_user']) ?>);
        const currentAdminName = <?= json_encode($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Admin') ?>;

        function renderSingleMessage(msg) {
            const div = document.createElement('div');
            div.className = 'chat-message ' + (parseInt(msg.sender_id) === currentAdminId ? 'admin' : 'operator');

            const header = document.createElement('div');
            const nameEl = document.createElement('strong');
            nameEl.textContent = msg.sender_name || (parseInt(msg.sender_id) === currentAdminId ? currentAdminName : 'User');
            header.appendChild(nameEl);

            if (parseInt(msg.sender_id) === currentAdminId) {
                const actions = document.createElement('span');
                actions.className = 'ms-2 message-actions';
                actions.style.marginLeft = '8px';
                actions.innerHTML = '<a href="#" class="edit-private-message" title="Edit" data-id="' + msg.id + '" data-message="' + encodeURIComponent(msg.message) + '"><i class="fa fas fa-pencil-alt"></i></a>' +
                    '<a href="#" class="delete-private-message ms-2" title="Hapus" data-id="' + msg.id + '"><i class="fa fas fa-trash-alt"></i></a>';
                header.appendChild(actions);
            }

            const textEl = document.createElement('div');
            textEl.textContent = msg.message;
            const timeEl = document.createElement('div');
            timeEl.className = 'text-muted small';
            timeEl.textContent = msg.created_at;

            div.appendChild(header);
            div.appendChild(textEl);
            div.appendChild(timeEl);
            chatContainer.appendChild(div);
        }

        function renderMessages(messages) {
            chatContainer.innerHTML = '';
            messages.forEach(msg => renderSingleMessage(msg));
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function loadPrivateChat(operatorId) {
            fetch('api_private_chat.php?operator_id=' + encodeURIComponent(operatorId)).then(r => r.json()).then(res => {
                if (res.status === 'success') {
                    renderMessages(res.messages);
                } else {
                    alert('Failed to load messages: ' + res.message);
                }
            }).catch(err => {
                console.error(err);
                alert('Error loading messages');
            });
        }

        document.querySelectorAll('#operatorsList .list-group-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                selectedOperator = id;
                document.getElementById('operatorId').value = id;
                document.getElementById('chatWith').textContent = 'Chat with: ' + this.textContent.trim();
                document.getElementById('chatInputRow').style.display = 'block';
                loadPrivateChat(id);
            });
        });

        document.getElementById('privateChatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!selectedOperator) return alert('Select an operator first');
            const message = document.getElementById('privateMessage').value.trim();
            if (!message) return;
            fetch('action_send_private_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'operator_id=' + encodeURIComponent(selectedOperator) + '&message=' + encodeURIComponent(message)
                })
                .then(r => r.json()).then(res => {
                    if (res.status === 'success') {
                        // clear input
                        document.getElementById('privateMessage').value = '';
                        // If API returned id and created_at, optimistically append the message immediately
                        if (res.id) {
                            const msgObj = {
                                id: res.id,
                                sender_id: currentAdminId,
                                sender_name: currentAdminName,
                                message: message,
                                created_at: res.created_at || new Date().toISOString().slice(0, 19).replace('T', ' ')
                            };
                            renderSingleMessage(msgObj);
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        }
                        // still refresh from server to ensure consistency
                        loadPrivateChat(selectedOperator);
                    } else alert('Send failed: ' + res.message);
                }).catch(err => {
                    console.error(err);
                    alert('Error while sending');
                });
        });

        // Delegate clicks in chatContainer for edit/delete actions
        document.getElementById('chatContainer').addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-private-message');
            if (editBtn) {
                e.preventDefault();
                const id = editBtn.getAttribute('data-id');
                const encoded = editBtn.getAttribute('data-message') || '';
                const message = decodeURIComponent(encoded);
                document.getElementById('editMessageId').value = id;
                document.getElementById('editMessageContent').value = message;
                var modal = new bootstrap.Modal(document.getElementById('editPrivateModal'));
                modal.show();
                return;
            }
            const delBtn = e.target.closest('.delete-private-message');
            if (delBtn) {
                e.preventDefault();
                const id = delBtn.getAttribute('data-id');
                // show delete confirmation modal and store id
                document.getElementById('deleteMessageId').value = id;
                var deleteModal = new bootstrap.Modal(document.getElementById('deletePrivateModal'));
                deleteModal.show();
                return;
            }
        });

        // Edit private message submit
        document.getElementById('editPrivateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editMessageId').value;
            const content = document.getElementById('editMessageContent').value.trim();
            if (!content) return alert('Pesan tidak boleh kosong');
            const body = 'message_id=' + encodeURIComponent(id) + '&message_content=' + encodeURIComponent(content);
            fetch('action_edit_private_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body
                })
                .then(r => r.json()).then(res => {
                    if (res.status === 'success') {
                        // hide modal
                        var modalEl = document.getElementById('editPrivateModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        loadPrivateChat(selectedOperator);
                    } else alert('Edit failed: ' + res.message);
                }).catch(err => {
                    console.error(err);
                    alert('Error editing message');
                });
        });

        // Confirm delete button in modal
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            const id = document.getElementById('deleteMessageId').value;
            if (!id) return;
            fetch('action_delete_private_chat.php?id=' + encodeURIComponent(id)).then(r => r.json()).then(res => {
                if (res.status === 'success') {
                    // hide modal
                    var modalEl = document.getElementById('deletePrivateModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    // refresh chat
                    loadPrivateChat(selectedOperator);
                } else alert('Delete failed: ' + res.message);
            }).catch(err => {
                console.error(err);
                alert('Error deleting message');
            });
        });
    </script>
</body>

</html>