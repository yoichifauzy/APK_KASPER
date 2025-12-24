<?php
require_once __DIR__ . '/../config/cek_login.php';
// only operator/admin should access scanner/review tool
otorisasi(['admin', 'operator']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scan Barcode Review</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        #reader {
            width: 100%;
            max-width: 600px;
            margin: auto
        }

        #result {
            margin-top: 1rem;
        }
    </style>
</head>

<body class="p-3">
    <div class="container">
        <h4>Scan Barcode - Review Pembayaran</h4>
        <p>Pindai barcode pembayaran menggunakan kamera. Hasil akan ditampilkan di bawah.</p>

        <div class="mb-2">
            <label for="cameraSelect" class="form-label">Pilih Kamera</label>
            <select id="cameraSelect" class="form-select form-select-sm"></select>
        </div>
        <div id="reader"></div>
        <div class="mt-2">
            <button id="btnStart" class="btn btn-primary btn-sm">Start</button>
            <button id="btnStop" class="btn btn-secondary btn-sm">Stop</button>
        </div>
        <div id="result" class="card d-none">
            <div class="card-body">
                <h5 class="card-title">Detail Pembayaran</h5>
                <div id="payment_info"></div>
                <div class="mt-3">
                    <button id="btnRefresh" class="btn btn-secondary btn-sm">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script>
        const resultCard = document.getElementById('result');
        const paymentInfo = document.getElementById('payment_info');

        function showPayment(data) {
            paymentInfo.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            resultCard.classList.remove('d-none');
        }

        function lookupBarcode(code) {
            $.ajax({
                url: 'api_barcode_lookup.php',
                method: 'GET',
                data: {
                    code: code
                },
                dataType: 'json'
            }).done(function(resp) {
                if (resp.ok) {
                    showPayment(resp.payment);
                } else {
                    alert('Lookup failed: ' + (resp.error || 'unknown'));
                }
            }).fail(function(xhr) {
                alert('Lookup error: ' + xhr.status + ' ' + xhr.statusText);
            });
        }

        const html5QrCode = new Html5Qrcode("reader");
        const qrConfig = {
            fps: 10,
            qrbox: 250
        };

        // populate camera selector and allow choosing
        Html5Qrcode.getCameras().then(cameras => {
            const $sel = document.getElementById('cameraSelect');
            if (!cameras || cameras.length === 0) {
                alert('Tidak menemukan kamera');
                return;
            }
            cameras.forEach((cam, idx) => {
                const opt = document.createElement('option');
                opt.value = cam.id;
                opt.text = cam.label || ('Camera ' + (idx + 1));
                $sel.appendChild(opt);
            });
            // start the first camera by default
            $sel.selectedIndex = 0;
            startScanning($sel.value);

            // restart on selection change
            $sel.addEventListener('change', function() {
                stopScanning().then(() => startScanning(this.value));
            });

            document.getElementById('btnStart').addEventListener('click', function() {
                startScanning($sel.value);
            });
            document.getElementById('btnStop').addEventListener('click', function() {
                stopScanning();
            });
        }).catch(err => {
            console.error(err);
            alert('Error camera init: ' + err);
        });

        function startScanning(cameraId) {
            html5QrCode.start(cameraId, qrConfig, qrCodeMessage => {
                lookupBarcode(qrCodeMessage);
                // stop scanning after one detection
                stopScanning();
            }).catch(err => {
                console.error('Start failed', err);
                alert('Gagal mengakses kamera: ' + err);
            });
        }

        function stopScanning() {
            return html5QrCode.stop().catch(function() {
                // ignore errors when stopping a non-started scanner
            });
        }

        document.getElementById('btnRefresh').addEventListener('click', function() {
            resultCard.classList.add('d-none');
            paymentInfo.innerHTML = '';
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    html5QrCode.start(cameras[0].id, qrConfig, qrCodeMessage => {
                        lookupBarcode(qrCodeMessage);
                        html5QrCode.stop();
                    });
                }
            });
        });
    </script>
</body>

</html>