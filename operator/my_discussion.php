<?php
require_once '../config/cek_login.php';
// All roles can access the chat for a topic
otorisasi(['admin', 'operator', 'user']);

include '../config/database.php';

$current_user_id = $_SESSION['id_user'];
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
        // Topic not found, redirect or show error
        header('Location: index_forum.php?status=error&message=Topic not found.');
        exit();
    }
} else {
    // No topic_id provided, redirect or show error
    header('Location: index_forum.php?status=error&message=No topic selected for discussion.');
    exit();
}

// Fetch categories for the edit modal dropdown (if needed, keeping for consistency)
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
    <title>Chat: <?= $topic_title ?> - Kas Kelas</title>
    <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport" />
    <link
        rel="icon"
        href="../assets/img/kaiadmin/favicon.ico"
        type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
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

    <!-- Custom CSS for Chat -->
    <style>
        .chat-container {
            height: 60vh; /* Adjust as needed */
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
            font-weight: bold;
            margin-bottom: 2px;
        }
        .chat-message .timestamp {
            font-size: 0.75em;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
        }
        .chat-message.mine .timestamp {
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'layout_operator/sidebar.php'; ?>
        <!-- End Sidebar -->


        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img
                                src="../assets/img/kaiadmin/logo_light.svg"
                                alt="navbar brand"
                                class="navbar-brand"
                                height="20" />
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
                <?php include 'layout_operator/navbar.php'; ?>
                <!-- End Navbar -->


            </div>

            <!-- main-content -->
            <div class="container">
                <div class="page-inner">
                    <div
                        class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
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
                                    <div class="chat-container" id="chatContainer">
                                        <!-- Chat messages will be loaded here -->
                                    </div>
                                    <div class="chat-input mt-3">
                                        <form id="chatForm" method="POST" action="action_send_chat.php">
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


            <!-- Footer -->
            <?php include 'layout_operator/footer.php'; ?>
            <!-- end Footer -->
        </div>

        <!-- Custom template | don't include it in your project! -->

        <!-- End Custom template -->
    </div>

    <!-- Edit Chat Message Modal -->
    <div class="modal fade" id="editChatMessageModal" tabindex="-1" aria-labelledby="editChatMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editChatMessageModalLabel">Edit Pesan Obrolan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="action_edit_chat_message.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="message_id" id="editChatMessageId">
                        <div class="form-group">
                            <label for="editChatMessageContent">Konten Pesan</label>
                            <textarea class="form-control" id="editChatMessageContent" name="message_content" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>
    <script>
        const chatContainer = document.getElementById('chatContainer');
        const chatForm = document.getElementById('chatForm');
        const chatMessageInput = document.getElementById('chatMessage');
        const topicId = <?= json_encode($topic_id) ?>;
        const currentUserId = <?= json_encode($current_user_id) ?>;

        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function loadChatMessages() {
            $.ajax({
                url: 'api_chat.php', // We will create this API endpoint
                method: 'GET',
                data: { topic_id: topicId },
                success: function(response) {
                    // jQuery automatically parses JSON if Content-Type is application/json
                    const messages = response.messages; // Correctly access the messages array
                    chatContainer.innerHTML = ''; // Clear existing messages
                    messages.forEach(function(msg) {
                        const messageElement = document.createElement('div');
                        messageElement.classList.add('chat-message');
                        messageElement.classList.add(msg.id_user == currentUserId ? 'mine' : 'other');

                        let senderName = msg.sender_name;
                        let senderRole = msg.sender_role;
                        if (msg.id_user == currentUserId) {
                            senderName = 'Anda';
                        }

                        messageElement.innerHTML = `
                            <div class="sender-name">${senderName} <span class="badge bg-secondary">${senderRole}</span>
                                ${msg.id_user == currentUserId ? `
                                    <span class="message-actions ms-2">
                                        <i class="fas fa-pencil-alt text-warning cursor-pointer" data-bs-toggle="modal" data-bs-target="#editChatMessageModal"
                                            data-id="${msg.id_chat}" data-message="${msg.pesan}"></i>
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
                }
            });
        }

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = chatMessageInput.value;
            if (message.trim() === '') return;

            $.ajax({
                url: 'action_send_chat.php',
                method: 'POST',
                data: {
                    topic_id: topicId,
                    message: message
                },
                success: function(response) {
                    console.log('Raw response from action_send_chat.php:', response); // Debugging line
                    // jQuery automatically parses JSON if Content-Type is application/json
                    const result = response; // No need for JSON.parse(response)
                    console.log('Parsed result from action_send_chat.php:', result); // Debugging line
                    if (result.status === 'success') {
                        chatMessageInput.value = ''; // Clear input
                        loadChatMessages(); // Reload messages to show the new one
                    } else {
                        alert('Gagal mengirim pesan: ' + result.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    console.log('Server response text:', jqXHR.responseText); // Debugging line
                    alert('Terjadi kesalahan saat mengirim pesan. Lihat konsol untuk detail.');
                }
            });
        });

        // JavaScript for Edit Chat Message Modal
        var editChatMessageModal = document.getElementById('editChatMessageModal');
        editChatMessageModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var messageId = button.getAttribute('data-id');
            var messageContent = button.getAttribute('data-message');

            var modalTitle = editChatMessageModal.querySelector('.modal-title');
            var messageIdInput = editChatMessageModal.querySelector('#editChatMessageId');
            var messageContentTextarea = editChatMessageModal.querySelector('#editChatMessageContent');

            modalTitle.textContent = 'Edit Pesan Obrolan';
            messageIdInput.value = messageId;
            messageContentTextarea.value = messageContent;
        });

        // JavaScript for SweetAlert Delete Chat Message Confirmation
        function deleteChatMessage(id) {
            swal({
                title: 'Apakah Anda yakin?',
                text: "Pesan obrolan ini akan dihapus secara permanen!",
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: 'Batal',
                        visible: true,
                        className: 'btn btn-danger'
                    },
                    confirm: {
                        text: 'Ya, Hapus!',
                        visible: true,
                        className: 'btn btn-success'
                    }
                }
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: 'action_delete_chat_message.php',
                        method: 'GET', // Or POST, but GET is fine for simple deletion with ID
                        data: { id: id, topic_id: topicId },
                        success: function(response) {
                            const result = response; // jQuery already parses JSON
                            if (result.status === 'success') {
                                swal('Dihapus!', 'Pesan Anda telah dihapus.', {
                                    icon: 'success',
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-success'
                                        }
                                    }
                                });
                                loadChatMessages(); // Refresh chat after successful deletion
                            } else {
                                swal('Gagal!', 'Gagal menghapus pesan: ' + result.message, {
                                    icon: 'error',
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-danger'
                                        }
                                    }
                                });
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            swal('Error!', 'Terjadi kesalahan saat menghapus pesan.', {
                                icon: 'error',
                                buttons: {
                                    confirm: {
                                        className: 'btn btn-danger'
                                    }
                                }
                            });
                            console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                        }
                    });
                } else {
                    swal.close();
                }
            });
        }

        // Initial load and periodic refresh
        $(document).ready(function() {
            loadChatMessages();
            setInterval(loadChatMessages, 3000); // Refresh every 3 seconds
        });
    </script>
</body>

</html>