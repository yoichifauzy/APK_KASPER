<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>OPERATOR KASPER - Scan Barcode Cash</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: ["simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <style>
        /* Debug log styling for scanner */
        #scanner_debug {
            font-family: monospace;
            background: #fff;
            border-radius: 6px;
        }

        #scanner_debug .debug-entry {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        #scanner_debug .debug-entry:last-child {
            border-bottom: 0;
        }

        #scanner_debug .debug-ts {
            color: #666;
            font-size: 12px;
            min-width: 86px;
        }

        #scanner_debug .debug-text {
            color: #333;
            font-size: 13px;
            flex: 1;
        }

        #scanner_debug .debug-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 6px;
            border-radius: 12px;
            color: #fff;
        }

        #scanner_debug .debug-starting {
            background: #0d6efd;
        }

        #scanner_debug .debug-scanning {
            background: #198754;
        }

        #scanner_debug .debug-processing {
            background: #fd7e14;
        }

        #scanner_debug .debug-done {
            background: #0d6efd;
        }

        #scanner_debug .debug-error {
            background: #dc3545;
        }

        #scanner_debug .debug-idle {
            background: #6c757d;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_operator/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                    </div>
                </div>
                <?php include 'layout_operator/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <main>
                        <div class="page-header">
                            <h3 class="fw-bold mb-3">Scan Barcode Cash</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Cash Management</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Scan Barcode Cash</a></li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-lg-7">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Camera Scanner</h4>
                                        <div>
                                            <small class="text-muted">Allow camera access when prompted</small>
                                            <select id="camera_select" class="form-select form-select-sm mt-1" style="min-width:220px; display:none;"></select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="scanner_container" style="min-height:320px; display:flex; align-items:center; justify-content:center; position:relative;">
                                            <!-- Video / scanner UI will be initialized here by JS -->
                                            <div id="scanner_placeholder" class="text-center text-muted">
                                                <i class="icon-camera" style="font-size:48px;"></i>
                                                <div class="mt-2">Camera akan tampil di sini setelah permission diberikan</div>
                                            </div>
                                            <div id="scanner" style="display:none; width:100%;"></div>
                                            <div id="scanner_status" style="position:absolute; left:12px; top:12px; z-index:20; display:none;">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                                                <small id="scanner_status_text" class="ms-2 text-primary">Scanning...</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="scanner controls">
                                                <button id="btn_start_scanner" class="btn btn-primary">Start</button>
                                                <button id="btn_stop_scanner" class="btn btn-secondary">Stop</button>
                                                <button id="btn_resume_scanner" class="btn btn-success" disabled>Resume</button>
                                                <button id="btn_switch_camera" class="btn btn-outline-secondary">Switch</button>
                                            </div>

                                            <div class="form-check form-check-inline ms-2">
                                                <input class="form-check-input" type="checkbox" id="chk_mirror">
                                                <label class="form-check-label small" for="chk_mirror">Mirror</label>
                                            </div>

                                            <div class="ms-2" style="min-width:160px;">
                                                <select id="decode_mode" class="form-select form-select-sm">
                                                    <option value="auto">Mode: Auto</option>
                                                    <option value="native">Prefer Native Detector</option>
                                                    <option value="lib">Library Only</option>
                                                </select>
                                            </div>

                                            <div class="ms-auto d-flex align-items-center gap-2">
                                                <label for="barcode_file" class="btn btn-outline-secondary btn-sm mb-0" style="cursor:pointer;">Choose Image</label>
                                                <input id="barcode_file" type="file" accept="image/*" class="d-none" />
                                                <button id="btn_scan_file" class="btn btn-info btn-sm">Upload & Scan</button>
                                                <button id="btn_refresh" class="btn btn-outline-primary btn-sm" title="Refresh">Refresh</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Hasil Scan</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="scan_result_empty" class="text-muted">Belum ada data. Scan barcode untuk melihat detail pembayaran.</div>
                                        <div id="scan_result" style="display:none;">
                                            <dl class="row">
                                                <dt class="col-5">Nama</dt>
                                                <dd class="col-7" id="res_nama">-</dd>

                                                <dt class="col-5">Jumlah</dt>
                                                <dd class="col-7" id="res_jumlah">-</dd>

                                                <dt class="col-5">Tanggal Bayar</dt>
                                                <dd class="col-7" id="res_tanggal">-</dd>

                                                <dt class="col-5">Status</dt>
                                                <dd class="col-7" id="res_status">-</dd>

                                                <dt class="col-5">Operator</dt>
                                                <dd class="col-7" id="res_operator">-</dd>

                                                <dt class="col-5">Tagihan</dt>
                                                <dd class="col-7" id="res_keterangan">-</dd>
                                            </dl>
                                            <div class="mt-2">
                                                <a id="res_bukti_link" href="#" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Bukti</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>

            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        // Integrate html5-qrcode scanner: start/stop camera and lookup payment via API
        (function() {
            var startBtn = document.getElementById('btn_start_scanner');
            var stopBtn = document.getElementById('btn_stop_scanner');
            var placeholder = document.getElementById('scanner_placeholder');
            var scannerDiv = document.getElementById('scanner');
            var html5QrCode = null;
            var _startTimeout = null;
            var _failureCount = 0;
            var _failureThreshold = 8; // attempts before switching strategy
            var _triedNative = false;
            var _triedLibOnly = false;

            // Scanner status helper
            function setScannerStatus(state, message) {
                // states: idle, starting, scanning, processing, done, error
                var statusBox = document.getElementById('scanner_status');
                var statusText = document.getElementById('scanner_status_text');
                if (!statusBox || !statusText) return;
                switch (state) {
                    case 'starting':
                        statusBox.style.display = '';
                        statusText.innerText = message || 'Memulai kamera...';
                        statusBox.querySelector('.spinner-border').style.display = '';
                        break;
                    case 'scanning':
                        statusBox.style.display = '';
                        statusText.innerText = message || 'Sedang scan...';
                        statusBox.querySelector('.spinner-border').style.display = '';
                        break;
                    case 'processing':
                        statusBox.style.display = '';
                        statusText.innerText = message || 'Memproses hasil...';
                        statusBox.querySelector('.spinner-border').style.display = '';
                        break;
                    case 'done':
                        statusBox.style.display = '';
                        statusText.innerText = message || 'Selesai';
                        statusBox.querySelector('.spinner-border').style.display = 'none';
                        break;
                    case 'error':
                        statusBox.style.display = '';
                        statusText.innerText = message || 'Terjadi kesalahan';
                        statusBox.querySelector('.spinner-border').style.display = 'none';
                        break;
                    default:
                        statusBox.style.display = 'none';
                        statusText.innerText = '';
                        break;
                }
                // append to debug log
                try {
                    appendDebug('status:' + state + (message ? ' - ' + message : ''));
                } catch (e) {}
            }

            function appendDebug(msg) {
                var d = document.getElementById('scanner_debug');
                if (!d) return;

                function escapeHtml(s) {
                    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                }
                var now = new Date();
                var ts = now.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                var entry = document.createElement('div');
                entry.className = 'debug-entry';

                // special formatting for status messages
                if (String(msg).indexOf('status:') === 0) {
                    var parts = msg.split(' - ');
                    var statusPart = parts[0].substr(7) || 'idle';
                    var rest = parts.slice(1).join(' - ');
                    // update the prominent current-status card
                    try {
                        var statusCardEl = document.getElementById('scanner_current_status');
                        var badgeEl = document.getElementById('scanner_current_status_badge');
                        var textEl = document.getElementById('scanner_current_status_text');
                        if (statusPart === 'idle') {
                            // hide card when idle to reduce visual noise
                            if (statusCardEl) statusCardEl.style.display = 'none';
                        } else {
                            if (statusCardEl) statusCardEl.style.display = '';
                            if (badgeEl) {
                                badgeEl.textContent = statusPart.toUpperCase();
                                // map to bootstrap badge colors for clarity
                                badgeEl.className = 'badge ' + (statusPart === 'error' ? 'bg-danger' : (statusPart === 'processing' ? 'bg-warning text-dark' : (statusPart === 'scanning' ? 'bg-success' : 'bg-primary')));
                            }
                            if (textEl) textEl.textContent = rest || '';
                        }
                    } catch (e) {}

                    // for non-idle statuses, also append to debug list
                    if (statusPart !== 'idle') {
                        var badge = document.createElement('span');
                        badge.className = 'debug-badge debug-' + statusPart;
                        badge.textContent = statusPart.toUpperCase();
                        var tsSpan = document.createElement('span');
                        tsSpan.className = 'debug-ts';
                        tsSpan.textContent = '[' + ts + ']';
                        var textSpan = document.createElement('span');
                        textSpan.className = 'debug-text';
                        textSpan.textContent = rest || '';
                        entry.appendChild(tsSpan);
                        entry.appendChild(badge);
                        entry.appendChild(textSpan);
                    } else {
                        // idle: do not add to debug log
                        return;
                    }
                } else {
                    var tsSpan = document.createElement('span');
                    tsSpan.className = 'debug-ts';
                    tsSpan.textContent = '[' + ts + ']';
                    var textSpan = document.createElement('span');
                    textSpan.className = 'debug-text';
                    textSpan.textContent = escapeHtml(msg);
                    entry.appendChild(tsSpan);
                    entry.appendChild(textSpan);
                }

                d.insertBefore(entry, d.firstChild);
                // keep only last 30
                while (d.children.length > 30) d.removeChild(d.lastChild);
            }

            function showMessage(title, text, icon) {
                if (window.swal) swal(title, text, icon || 'info');
                else alert((title ? title + '\n' : '') + (text || ''));
            }

            startBtn.addEventListener('click', function() {
                // show UI
                placeholder.style.display = 'none';
                scannerDiv.style.display = 'block';
                startBtn.disabled = true;
                stopBtn.disabled = false;

                if (html5QrCode) {
                    // already initialized
                    return startScanner();
                }

                html5QrCode = new Html5Qrcode( /* element id */ "scanner");

                // populate camera list if available
                var sel = document.getElementById('camera_select');
                if (Html5Qrcode.getCameras) {
                    Html5Qrcode.getCameras().then(function(cameras) {
                        if (sel && cameras && cameras.length) {
                            sel.style.display = '';
                            sel.innerHTML = '';
                            cameras.forEach(function(cam) {
                                var o = document.createElement('option');
                                o.value = cam.id;
                                o.text = cam.label || cam.id;
                                sel.appendChild(o);
                            });
                            // remember current selection or pick first
                            if (!sel.value && sel.options.length) sel.selectedIndex = 0;
                            appendDebug('cameras:' + cameras.length + ' found');
                        }
                    }).catch(function(e) {
                        console.warn('getCameras failed', e);
                        appendDebug('getCameras error: ' + (e && e.message ? e.message : e));
                    });
                }

                startScanner();
            });

            function startScanner() {
                // tune scanning config: higher fps and dynamic qrbox sized to scanner
                var boxWidth = 300;
                try {
                    var w = scannerDiv.clientWidth || window.innerWidth || 360;
                    boxWidth = Math.min(Math.max(Math.floor(w * 0.6), 220), 800);
                } catch (e) {}
                var config = {
                    fps: 20,
                    qrbox: {
                        width: boxWidth,
                        height: boxWidth // square scan area for QR-style codes
                    },
                    // prefer higher resolution to allow clearer frames for decoding
                    videoConstraints: {
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        },
                        facingMode: 'environment'
                    }
                };
                // prefer also using native BarcodeDetector if available and support 1D formats
                try {
                    if (window.Html5QrcodeSupportedFormats) {
                        config.formatsToSupport = [
                            Html5QrcodeSupportedFormats.QR_CODE,
                            Html5QrcodeSupportedFormats.CODE_128,
                            Html5QrcodeSupportedFormats.CODE_39,
                            Html5QrcodeSupportedFormats.EAN_13
                        ];
                    }
                    config.useBarCodeDetectorIfSupported = true;
                } catch (e) {
                    appendDebug('formats config error: ' + (e && e.message ? e.message : e));
                }
                // try to use back camera if available
                var constraints = {
                    facingMode: {
                        exact: "environment"
                    }
                };
                // choose camera: if device id selected, use it; otherwise use facingMode with videoConstraints
                var sel = document.getElementById('camera_select');
                var cameraArg = {
                    facingMode: 'environment'
                };
                if (sel && sel.value) {
                    // html5-qrcode accepts deviceId string
                    cameraArg = {
                        deviceId: {
                            exact: sel.value
                        }
                    };
                }

                // Html5Qrcode start accepts cameraIdOrConfig
                // set starting state and start a timeout to detect stuck starts
                setScannerStatus('starting');
                if (_startTimeout) try {
                    clearTimeout(_startTimeout);
                } catch (e) {}
                _startTimeout = setTimeout(function() {
                    appendDebug('start timeout');
                    setScannerStatus('error', 'Gagal memulai kamera (timeout). Coba switch camera atau periksa izin.');
                    try {
                        html5QrCode && html5QrCode.stop();
                    } catch (e) {}
                }, 6000);

                html5QrCode.start(
                    cameraArg,
                    config,
                    qrCodeSuccessCallback,
                    qrCodeErrorCallback
                ).then(function() {
                    // started successfully
                    appendDebug('start ok');
                    setScannerStatus('scanning', 'Sedang scan...');
                    if (_startTimeout) try {
                        clearTimeout(_startTimeout);
                        _startTimeout = null;
                    } catch (e) {}
                }).catch(function(err) {
                    console.error('start error', err);
                    appendDebug('start error: ' + (err && err.message ? err.message : err));
                    // fallback: try without environment constraint
                    setScannerStatus('error', 'Gagal membuka kamera, coba ganti kamera');
                    if (_startTimeout) try {
                        clearTimeout(_startTimeout);
                        _startTimeout = null;
                    } catch (e) {}
                    html5QrCode.start({
                        facingMode: "user"
                    }, config, qrCodeSuccessCallback, qrCodeErrorCallback).then(function() {
                        appendDebug('fallback start ok');
                        setScannerStatus('scanning', 'Sedang scan...');
                    }).catch(function(e) {
                        console.error('fallback start error', e);
                        appendDebug('fallback start error: ' + (e && e.message ? e.message : e));
                        showMessage('Gagal membuka kamera', 'Tidak dapat mengakses kamera. Periksa izin dan pastikan situs dijalankan di HTTPS atau localhost.', 'error');
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                        placeholder.style.display = 'flex';
                        scannerDiv.style.display = 'none';
                    });
                });
            }

            stopBtn.addEventListener('click', function() {
                stopScanner();
            });

            function stopScanner() {
                if (html5QrCode) {
                    html5QrCode.stop().then(function() {
                        html5QrCode.clear();
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                        scannerDiv.style.display = 'none';
                        placeholder.style.display = 'flex';
                        setScannerStatus('idle');
                        document.getElementById('btn_resume_scanner').disabled = false;
                    }).catch(function(err) {
                        console.warn('stop error', err);
                        appendDebug('stop error: ' + (err && err.message ? err.message : err));
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                    });
                } else {
                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                }
            }

            function qrCodeSuccessCallback(decodedText, decodedResult) {
                // stop scanner to avoid repeated scans
                if (html5QrCode) {
                    html5QrCode.stop().then(function() {
                        html5QrCode.clear();
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                        scannerDiv.style.display = 'none';
                        placeholder.style.display = 'flex';
                    }).catch(function(e) {
                        console.warn('stop after scan error', e);
                    });
                }

                // show processing state
                setScannerStatus('processing');

                // feedback: vibrate and play short beep if available
                try {
                    if (navigator.vibrate) navigator.vibrate(150);
                    // simple beep via WebAudio
                    var ctx = new(window.AudioContext || window.webkitAudioContext)();
                    var o = ctx.createOscillator();
                    var g = ctx.createGain();
                    o.type = 'sine';
                    o.frequency.value = 800;
                    o.connect(g);
                    g.connect(ctx.destination);
                    o.start(0);
                    g.gain.setValueAtTime(0.1, ctx.currentTime);
                    setTimeout(function() {
                        o.stop();
                        try {
                            ctx.close();
                        } catch (e) {}
                    }, 120);
                } catch (e) {
                    /* ignore audio errors */
                }

                // call lookup API
                var code = decodedText.trim();
                appendDebug('decoded:' + code);
                fetch('api_barcode_lookup.php?code=' + encodeURIComponent(code), {
                        credentials: 'same-origin'
                    })
                    .then(function(res) {
                        if (!res.ok) throw res;
                        return res.json();
                    })
                    .then(function(json) {
                        if (json.ok && json.payment) {
                            window.showScannedPayment(json.payment);
                            setScannerStatus('done', 'Sukses: data ditemukan');
                            document.getElementById('btn_resume_scanner').disabled = false;
                        } else if (json.error) {
                            setScannerStatus('done', 'Selesai: tidak ditemukan');
                            showMessage('Tidak ditemukan', 'Pembayaran untuk barcode ini tidak ditemukan.');
                        } else {
                            setScannerStatus('error', 'Respons tidak terduga');
                            showMessage('Error', 'Respons tidak terduga dari server.');
                        }
                    })
                    .catch(function(err) {
                        if (err instanceof Response) {
                            err.text().then(function(t) {
                                setScannerStatus('error', 'Gagal saat memanggil API');
                                appendDebug('lookup error:' + (t || err.statusText || err.status));
                                showMessage('Gagal', t || err.statusText || err.status, 'error');
                            });
                        } else {
                            setScannerStatus('error', 'Gagal saat memanggil API');
                            appendDebug('lookup exception:' + (err && err.message ? err.message : err));
                            showMessage('Gagal', 'Terjadi kesalahan saat memanggil API lookup.', 'error');
                            console.error(err);
                        }
                    });
            }

            function qrCodeErrorCallback(errorMessage) {
                // log QR errors to debug panel to help diagnose slow decoding
                appendDebug('qrError:' + errorMessage);
                // increment failure counter and consider strategy change
                _failureCount++;
                appendDebug('failureCount:' + _failureCount);
                // preprocessing disabled: no frame-capture retry

                if (_failureCount >= _failureThreshold) {
                    // auto-switch strategy depending on what we've tried
                    var mode = (document.getElementById('decode_mode') || {}).value || 'auto';
                    appendDebug('threshold reached, mode=' + mode);
                    if (mode === 'auto') {
                        if (!_triedNative) {
                            appendDebug('auto-switch -> prefer native');
                            _triedNative = true;
                            // switch to prefer native detector by restarting scanner
                            restartWithMode('native');
                        } else if (!_triedLibOnly) {
                            appendDebug('auto-switch -> library only (1D)');
                            _triedLibOnly = true;
                            restartWithMode('lib');
                        } else {
                            appendDebug('auto-switch: all strategies tried');
                        }
                    } else if (mode === 'native' && !_triedLibOnly) {
                        _triedLibOnly = true;
                        appendDebug('mode native -> fallback lib');
                        restartWithMode('lib');
                    } else if (mode === 'lib' && !_triedNative) {
                        _triedNative = true;
                        appendDebug('mode lib -> fallback native');
                        restartWithMode('native');
                    }
                    // reset counter so we don't rapidly switch
                    _failureCount = 0;
                }
            }

            // helper to populate result area with payment JSON (from API)
            window.showScannedPayment = function(payment) {
                document.getElementById('scan_result_empty').style.display = 'none';
                document.getElementById('scan_result').style.display = '';
                document.getElementById('res_nama').textContent = payment.nama_lengkap || '-';
                document.getElementById('res_jumlah').textContent = payment.jumlah !== null ? new Intl.NumberFormat().format(payment.jumlah) : '-';
                document.getElementById('res_tanggal').textContent = payment.tanggal_bayar || '-';
                document.getElementById('res_status').textContent = payment.status || '-';
                document.getElementById('res_operator').textContent = payment.ditambahkan_oleh_display || payment.dibuat_oleh || '-';
                document.getElementById('res_keterangan').textContent = payment.keterangan || '-';
                var buktiLink = document.getElementById('res_bukti_link');
                if (payment.bukti) {
                    buktiLink.href = '../upload/pembayaran/' + encodeURIComponent(payment.bukti);
                    buktiLink.style.display = '';
                } else {
                    buktiLink.style.display = 'none';
                }
            };

            // Handle uploaded file scanning
            var fileInput = document.getElementById('barcode_file');
            var btnScanFile = document.getElementById('btn_scan_file');

            function scanFileAndLookup(file) {
                if (!file) return showMessage('File tidak dipilih', 'Pilih file gambar barcode terlebih dahulu.', 'warning');
                showMessage('Scanning', 'Memindai gambar, tunggu sebentar...', 'info');

                // Use existing html5QrCode instance if available, otherwise create a temporary one
                var scannerForFile = html5QrCode;
                var temporary = false;
                if (!scannerForFile) {
                    scannerForFile = new Html5Qrcode("scanner");
                    temporary = true;
                }

                // html5-qrcode has scanFileV2 that returns results; try to use it
                var p = scannerForFile.scanFileV2 ? scannerForFile.scanFileV2(file, true) : scannerForFile.scanFile(file, true);
                Promise.resolve(p).then(function(result) {
                    // result can be array or object/string
                    var decoded = null;
                    if (Array.isArray(result) && result.length) decoded = result[0].decodedText || result[0].text || result[0];
                    else if (result && result.decodedText) decoded = result.decodedText;
                    else if (typeof result === 'string') decoded = result;

                    if (decoded) {
                        // call lookup API (same as camera flow)
                        fetch('api_barcode_lookup.php?code=' + encodeURIComponent(decoded.trim()), {
                                credentials: 'same-origin'
                            })
                            .then(function(res) {
                                if (!res.ok) throw res;
                                return res.json();
                            })
                            .then(function(json) {
                                if (json.ok && json.payment) {
                                    window.showScannedPayment(json.payment);
                                } else if (json.error) {
                                    showMessage('Tidak ditemukan', 'Pembayaran untuk barcode ini tidak ditemukan.');
                                } else {
                                    showMessage('Error', 'Respons tidak terduga dari server.');
                                }
                            })
                            .catch(function(err) {
                                if (err instanceof Response) {
                                    err.text().then(function(t) {
                                        showMessage('Gagal', t || err.statusText || err.status, 'error');
                                    });
                                } else {
                                    showMessage('Gagal', 'Terjadi kesalahan saat memanggil API lookup.', 'error');
                                    console.error(err);
                                }
                            });
                    } else {
                        showMessage('Gagal', 'Tidak dapat membaca barcode dari gambar.', 'error');
                    }
                }).catch(function(err) {
                    console.error('scanFile error', err);
                    showMessage('Gagal memindai gambar', (err && err.message) ? err.message : String(err), 'error');
                }).finally(function() {
                    if (temporary && scannerForFile) {
                        try {
                            scannerForFile.clear();
                        } catch (e) {}
                    }
                });
            }

            if (btnScanFile) {
                btnScanFile.addEventListener('click', function() {
                    var f = fileInput.files && fileInput.files[0];
                    if (!f) return showMessage('Pilih file dulu', 'Pilih file gambar barcode di komputer/HP Anda.', 'warning');
                    setScannerStatus('processing', 'Memindai gambar...');
                    scanFileAndLookup(f);
                });
            }

            // Refresh / reset button: clear displayed result, debug log and reset UI state
            var btnRefresh = document.getElementById('btn_refresh');
            if (btnRefresh) {
                btnRefresh.addEventListener('click', function() {
                    try {
                        // reset result area
                        document.getElementById('scan_result_empty').style.display = '';
                        document.getElementById('scan_result').style.display = 'none';
                        document.getElementById('res_nama').textContent = '-';
                        document.getElementById('res_jumlah').textContent = '-';
                        document.getElementById('res_tanggal').textContent = '-';
                        document.getElementById('res_status').textContent = '-';
                        document.getElementById('res_operator').textContent = '-';
                        document.getElementById('res_keterangan').textContent = '-';
                        var buktiLink = document.getElementById('res_bukti_link');
                        if (buktiLink) {
                            buktiLink.href = '#';
                            buktiLink.style.display = 'none';
                        }

                        // clear debug log
                        var dbg = document.getElementById('scanner_debug');
                        if (dbg) dbg.innerHTML = '';

                        // reset scanner UI state
                        try {
                            if (html5QrCode) {
                                // stop if running
                                html5QrCode.stop().catch(function() {});
                                try {
                                    html5QrCode.clear();
                                } catch (e) {}
                            }
                        } catch (e) {}

                        // toggle UI buttons
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                        var resume = document.getElementById('btn_resume_scanner');
                        if (resume) resume.disabled = true;

                        // show placeholder
                        placeholder.style.display = 'flex';
                        scannerDiv.style.display = 'none';

                        setScannerStatus('idle', 'Direset');
                        try {
                            appendDebug('manual:refresh');
                        } catch (e) {}
                    } catch (e) {
                        console.error('refresh error', e);
                        showMessage('Error', 'Gagal mereset tampilan scanner.');
                    }
                });
            }

            // helper: restart scanner with a chosen mode
            function restartWithMode(mode) {
                appendDebug('restartWithMode:' + mode);
                // update UI selector if present
                var sel = document.getElementById('decode_mode');
                if (sel) sel.value = mode;
                // stop and start scanner with the new preferences
                try {
                    stopScanner();
                } catch (e) {}
                setTimeout(function() {
                    document.getElementById('btn_resume_scanner').disabled = true;
                    document.getElementById('btn_start_scanner').disabled = true;
                    document.getElementById('btn_stop_scanner').disabled = false;
                    placeholder.style.display = 'none';
                    scannerDiv.style.display = 'block';
                    // flip tried flags according to mode
                    if (mode === 'native') {
                        _triedNative = true;
                    }
                    if (mode === 'lib') {
                        _triedLibOnly = true;
                    }
                    startScanner();
                }, 300);
            }

            // Frame capture retry removed (preprocessing disabled)

            // Resume scanner button
            var btnResume = document.getElementById('btn_resume_scanner');
            if (btnResume) {
                btnResume.addEventListener('click', function() {
                    // try to restart scanner with current selection
                    document.getElementById('btn_resume_scanner').disabled = true;
                    startBtn.disabled = true;
                    stopBtn.disabled = false;
                    placeholder.style.display = 'none';
                    scannerDiv.style.display = 'block';
                    setScannerStatus('starting');
                    startScanner();
                });
            }

            // Switch camera button: cycle through select options
            var btnSwitch = document.getElementById('btn_switch_camera');
            if (btnSwitch) {
                btnSwitch.addEventListener('click', function() {
                    var sel = document.getElementById('camera_select');
                    if (!sel || !sel.options || sel.options.length < 2) {
                        appendDebug('switchCamera: no multiple cameras');
                        showMessage('Tidak ada kamera lain', 'Tidak ditemukan kamera lain untuk berpindah.', 'info');
                        return;
                    }
                    var idx = sel.selectedIndex || 0;
                    idx = (idx + 1) % sel.options.length;
                    sel.selectedIndex = idx;
                    appendDebug('switchCamera -> ' + sel.options[idx].text);
                    // restart scanner to use new camera
                    stopScanner();
                    setTimeout(function() {
                        startBtn.disabled = true;
                        stopBtn.disabled = false;
                        placeholder.style.display = 'none';
                        scannerDiv.style.display = 'block';
                        startScanner();
                    }, 300);
                });
            }

            // Mirror toggle
            var chkMirror = document.getElementById('chk_mirror');
            if (chkMirror) {
                chkMirror.addEventListener('change', function() {
                    if (chkMirror.checked) {
                        scannerDiv.style.transform = 'scaleX(-1)';
                        appendDebug('mirror:on');
                    } else {
                        scannerDiv.style.transform = '';
                        appendDebug('mirror:off');
                    }
                });
            }

            // debug area - inject below scanner container
            var sc = document.getElementById('scanner_container');
            if (sc) {
                // wrapper to hold current status card + debug log
                var wrap = document.createElement('div');
                wrap.id = 'scanner_debug_wrap';
                wrap.style.marginTop = '8px';

                // current status card (hidden when idle)
                var statusCard = document.createElement('div');
                statusCard.id = 'scanner_current_status';
                statusCard.className = 'card bg-light mb-2';
                statusCard.style.display = 'none';
                statusCard.innerHTML = '<div class="card-body p-2" style="font-size:13px; display:flex; gap:8px; align-items:center;"><span id="scanner_current_status_badge" class="badge bg-secondary">STATUS</span><span id="scanner_current_status_text">&nbsp;</span></div>';

                var dbg = document.createElement('div');
                dbg.id = 'scanner_debug';
                dbg.style.maxHeight = '120px';
                dbg.style.overflow = 'auto';
                dbg.style.padding = '6px';
                dbg.style.borderTop = '1px solid #eee';

                wrap.appendChild(statusCard);
                wrap.appendChild(dbg);
                sc.appendChild(wrap);
            }

            // initialize status
            setScannerStatus('idle');
        })();
    </script>
    <!-- jQuery Scrollbar (required by theme) -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- Kaiadmin JS (enables sidebar toggles and other UI behaviors) -->
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>

</html>