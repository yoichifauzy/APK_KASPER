<?php
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['admin']);

include '../config/database.php';

$current_user_id = $_SESSION['id_user'];
$current_user_role = 'admin'; // force admin role for this view
$topic_id = null;
$topic_title = "Diskusi Topik";

if (isset($_GET['topic_id'])) {
    $topic_id = mysqli_real_escape_string($conn, $_GET['topic_id']);

    // Fetch topic details
    $query_topic = "SELECT title FROM discussion_topics WHERE id = '$topic_id'";
    $result_topic = mysqli_query($conn, $query_topic);
    if ($result_topic && mysqli_num_rows($result_topic) > 0) {
        $topic_data = mysqli_fetch_assoc($result_topic);
        $topic_title = htmlspecialchars($topic_data['title']);
    } else {
        header('Location: index_forum.php?status=error&message=Topic not found.');
        exit();
    }
} else {
    header('Location: index_forum.php?status=error&message=No topic selected for discussion.');
    exit();
}

// Fetch categories (kept for compatibility)
$categories = [];
$query_categories = "SELECT id, name FROM discussion_categories ORDER BY name";
$result_categories = mysqli_query($conn, $query_categories);
if ($result_categories) {
    while ($row = mysqli_fetch_assoc($result_categories)) {
        $categories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Chat: <?= $topic_title ?> - Kas Kelas (Admin)</title>
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
        .chat-container {
            height: 60vh;
            overflow-y: auto;
            border: 1px solid #e3e3e3;
            padding: 15px;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
        }

        .chat-message {
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 15px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .chat-message.mine {
            align-self: flex-end;
            background-color: #007bff;
            color: white;
            border-bottom-right-radius: 2px;
        }

        .chat-message.other {
            align-self: flex-start;
            background-color: #e9ecef;
            color: #333;
            border-bottom-left-radius: 2px;
        }

        .chat-message .sender-name {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .chat-message .timestamp {
            font-size: .75em;
            color: #6c757d;
            text-align: right;
            margin-top: 6px;
        }

        .chat-message.mine .timestamp {
            color: rgba(255, 255, 255, 0.8);
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

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Diskusi Topik: <?= $topic_title ?></h3>
                            <h6 class="op-7 mb-2">Obrolan Real-time untuk Topik Ini</h6>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <a href="index_forum.php" class="btn btn-secondary btn-round">Kembali ke Forum</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Obrolan</div>
                                </div>
                                <div class="card-body">
                                    <div class="chat-container" id="chatContainer"></div>
                                    <div class="chat-input mt-3">
                                        <form id="chatForm">
                                            <input type="hidden" name="topic_id" value="<?= htmlspecialchars($topic_id) ?>">
                                            <div class="input-group">
                                                <textarea class="form-control" id="chatMessage" name="message" placeholder="Ketik pesan Anda..." rows="2" required></textarea>
                                                <button class="btn btn-primary" type="submit">Kirim</button>
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

    <!-- Edit Chat Message Modal (admin can edit their own messages) -->
    <div class="modal fade" id="editChatMessageModal" tabindex="-1" aria-labelledby="editChatMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editChatMessageModalLabel">Edit Pesan Obrolan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editChatForm" method="POST" action="../operator/action_edit_chat_message.php">
                    <div class="modal-body">
                        <input type="hidden" name="message_id" id="editChatMessageId">
                        <div class="form-group"><label for="editChatMessageContent">Konten Pesan</label><textarea class="form-control" id="editChatMessageContent" name="message_content" rows="3" required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        const chatContainer = document.getElementById('chatContainer');
        const chatForm = document.getElementById('chatForm');
        const chatMessageInput = document.getElementById('chatMessage');
        const topicId = <?= json_encode($topic_id) ?>;
        const currentUserId = <?= json_encode($current_user_id) ?>;
        const currentUserRole = <?= json_encode($current_user_role) ?>; // 'admin'

        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function loadChatMessages() {
            $.ajax({
                url: '../operator/api_chat.php',
                method: 'GET',
                data: {
                    topic_id: topicId
                },
                success: function(response) {
                    const messages = response.messages || [];
                    chatContainer.innerHTML = '';
                    messages.forEach(function(msg) {
                        const messageElement = document.createElement('div');
                        messageElement.classList.add('chat-message');
                        messageElement.classList.add(msg.id_user == currentUserId ? 'mine' : 'other');

                        let senderName = msg.sender_name;
                        // force role name to 'admin' for admin user messages
                        let senderRole = (msg.id_user == currentUserId) ? currentUserRole : (msg.sender_role || 'user');
                        if (msg.id_user == currentUserId) senderName = 'Anda';

                        messageElement.innerHTML = `
                            <div class="sender-name">${senderName} <span class="badge bg-secondary">${senderRole}</span>
                                ${msg.id_user == currentUserId ? `
                                    <span class="message-actions ms-2">
                                        <i class="fas fa-pencil-alt text-warning cursor-pointer" data-bs-toggle="modal" data-bs-target="#editChatMessageModal" data-id="${msg.id_chat}" data-message="${msg.pesan}"></i>
                                        <i class="fas fa-trash-alt text-danger cursor-pointer ms-1" onclick="deleteChatMessage(${msg.id_chat})"></i>
                                    </span>
                                ` : ''}
                            </div>
                            <div>${msg.pesan}</div>
                            <div class="timestamp">${msg.waktu}</div>
                        `;
                        chatContainer.appendChild(messageElement);
                    });
                    scrollToBottom();
                },
                error: function(jqXHR, status, err) {
                    console.error('Failed to load chat:', jqXHR.responseText);
                }
            });
        }

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = chatMessageInput.value;
            if (message.trim() === '') return;
            $.ajax({
                url: '../operator/action_send_chat.php',
                method: 'POST',
                data: {
                    topic_id: topicId,
                    message: message
                },
                success: function(response) {
                    if (response.status === 'success') {
                        chatMessageInput.value = '';
                        loadChatMessages();
                    } else {
                        alert('Gagal mengirim pesan: ' + (response.message || ''));
                    }
                },
                error: function(jqXHR) {
                    console.error('Send failed:', jqXHR.responseText);
                    alert('Terjadi kesalahan saat mengirim pesan.');
                }
            });
        });

        var editChatMessageModal = document.getElementById('editChatMessageModal');
        editChatMessageModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var messageId = button.getAttribute('data-id');
            var messageContent = button.getAttribute('data-message');
            editChatMessageModal.querySelector('#editChatMessageId').value = messageId;
            editChatMessageModal.querySelector('#editChatMessageContent').value = messageContent;
        });

        function deleteChatMessage(id) {
            if (!confirm('Hapus pesan ini?')) return;
            $.ajax({
                url: '../operator/action_delete_chat_message.php',
                method: 'GET',
                data: {
                    id: id,
                    topic_id: topicId
                },
                success: function(res) {
                    if (res.status === 'success') loadChatMessages();
                    else alert('Gagal menghapus.');
                },
                error: function(jqXHR) {
                    console.error(jqXHR.responseText);
                }
            });
        }

        $(document).ready(function() {
            loadChatMessages();
            setInterval(loadChatMessages, 3000);
        });
    </script>
</body>

</html>