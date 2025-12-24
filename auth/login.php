<?php
session_start();
include '../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard_admin.php");
    } elseif ($_SESSION['role'] == 'operator') {
        header("Location: ../operator/dashboard_operator.php");
    } else {
        header("Location: ../user/dashboard_user.php");
    }
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            // Store additional display info to use in navbar/layouts
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'] ?? $user['username'];
            $_SESSION['profile_picture'] = $user['profile_picture'] ?? null;

            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard_admin.php");
            } elseif ($user['role'] == 'operator') {
                header("Location: ../operator/dashboard_operator.php");
            } else {
                header("Location: ../user/dashboard_user.php");
            }
            exit();
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Login - APK KASPER</title>
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
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <style>
        /* Animated background for login page */
        .auth-bg-wrapper {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            /* behind content */
            pointer-events: none;
        }

        .auth-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, #071029 0%, #081427 40%, #041d2b 100%);
            opacity: 0.92;
            filter: blur(8px);
            transform: scale(1.05);
        }

        /* Floating shapes to give Kaiadmin-like animated feel */
        .auth-bg .shape {
            position: absolute;
            width: 360px;
            height: 360px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20%;
            backdrop-filter: blur(2px);
            mix-blend-mode: overlay;
            animation: floatY 12s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .auth-bg .shape.shape-1 {
            left: -80px;
            top: -40px;
            background: linear-gradient(45deg, rgba(114, 46, 204, 0.16), rgba(59, 130, 246, 0.08));
            animation-duration: 14s;
        }

        .auth-bg .shape.shape-2 {
            right: -120px;
            top: 40px;
            width: 420px;
            height: 420px;
            background: linear-gradient(45deg, rgba(236, 72, 153, 0.12), rgba(124, 58, 237, 0.07));
            animation-duration: 18s;
            animation-delay: 2s;
        }

        .auth-bg .shape.shape-3 {
            left: 20%;
            bottom: -80px;
            width: 520px;
            height: 520px;
            background: linear-gradient(45deg, rgba(34, 197, 94, 0.09), rgba(16, 185, 129, 0.05));
            animation-duration: 20s;
            animation-delay: 4s;
        }

        @keyframes floatY {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.9;
            }

            50% {
                transform: translateY(-28px) rotate(6deg);
                opacity: 1;
            }

            100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.9;
            }
        }

        /* Ensure the login card sits above the animated background */
        .wrapper,
        .container {
            position: relative;
            z-index: 1;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.98);
        }

        /* Respect reduced motion preference */
        @media (prefers-reduced-motion: reduce) {
            .auth-bg .shape {
                animation: none;
            }
        }
    </style>
</head>

<body>

    <!-- Animated background for login page -->
    <div class="auth-bg-wrapper" aria-hidden="true">
        <canvas id="authParticles" style="position:absolute;inset:0;width:100%;height:100%;display:block;pointer-events:none;"></canvas>
        <div class="auth-bg">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header text-center">
                        <!-- Centered logo above title -->
                        <div class="mb-3">
                            <img src="../assets/img/kaiadmin/icon.png" alt="Kaiadmin Logo" style="width:84px;height:auto;" />
                        </div>
                        <div class="card-title fw-bold text-primary h4 mb-0">Selamat Datang di KASPER</div>
                        <div class="text-muted small">Masuk untuk mengelola akun dan data Anda</div>
                    </div>
                    <form method="POST" action="" class="card-body">
                        <?php if (isset($error)) : ?>
                            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <div class="form-group mb-3">
                            <label for="username">Username</label>
                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                id="username"
                                placeholder="Masukkan username"
                                required
                                autofocus />
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="password"
                                placeholder="Password"
                                required />
                        </div>

                        <div class="card-action">
                            <button type="submit" name="login" class="btn btn-success">Login</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script>
        // Particle background for login page
        (function() {
            const canvas = document.getElementById('authParticles');
            if (!canvas) return;

            // Respect reduced motion
            const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            if (mediaQuery.matches) {
                canvas.style.display = 'none';
                return;
            }

            const ctx = canvas.getContext('2d');
            let width, height, DPR;

            function resize() {
                DPR = window.devicePixelRatio || 1;
                width = canvas.clientWidth || window.innerWidth;
                height = canvas.clientHeight || window.innerHeight;
                canvas.width = Math.floor(width * DPR);
                canvas.height = Math.floor(height * DPR);
                ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
            }

            const rand = (min, max) => Math.random() * (max - min) + min;

            // white palette (we'll vary opacity per particle)
            const PALETTE = [
                [255, 255, 255],
            ];

            class Particle {
                constructor() {
                    this.reset(true);
                }
                reset(init) {
                    this.x = rand(-50, width + 50);
                    this.y = rand(-50, height + 50);
                    const speed = rand(6, 30);
                    const angle = rand(0, Math.PI * 2);
                    this.vx = Math.cos(angle) * speed * 0.03;
                    this.vy = Math.sin(angle) * speed * 0.03;
                    this.size = rand(2, 6);
                    this.life = rand(6, 18);
                    this.age = init ? rand(0, this.life) : 0;
                    // base opacity per particle to vary brightness
                    this.baseOpacity = rand(0.35, 0.85);
                }
                draw(ctx) {
                    ctx.save();
                    // additive blend for brighter particles where they overlap
                    ctx.globalCompositeOperation = 'lighter';
                    ctx.beginPath();
                    const radius = this.size * 10;
                    const g = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, radius);
                    // center vivid white, fade to transparent
                    // Compute opacity based on age/life, clamp to [0,1]
                    const lifeRatio = this.life > 0 ? Math.max(0, Math.min(1, this.age / this.life)) : 0;
                    const centerAlpha = Math.max(0, Math.min(1, this.baseOpacity * (1 - lifeRatio)));
                    g.addColorStop(0, `rgba(255,255,255, ${centerAlpha})`);
                    g.addColorStop(1, 'rgba(255,255,255,0)');
                    ctx.fillStyle = g;
                    ctx.arc(this.x, this.y, radius, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.restore();
                }
                step(dt) {
                    this.x += this.vx * dt;
                    this.y += this.vy * dt;
                    this.age += dt * 0.01;
                    if (this.age > this.life) this.reset(false);
                }
            }

            let particles = [];
            const MAX = 120; // increased for stronger visual density

            function init() {
                resize();
                particles = [];
                for (let i = 0; i < MAX; i++) particles.push(new Particle());
            }

            let last = performance.now();

            function frame(now) {
                const dt = now - last;
                last = now;
                ctx.clearRect(0, 0, width, height);

                // subtle backdrop tint (slightly lighter so particles pop)
                ctx.fillStyle = 'rgba(6,10,22,0.06)';
                ctx.fillRect(0, 0, width, height);

                for (let p of particles) {
                    p.step(dt);
                    p.draw(ctx);
                }

                requestAnimationFrame(frame);
            }

            window.addEventListener('resize', () => {
                resize();
                // reposition particles to avoid odd gaps
                for (let p of particles) p.reset(true);
            });

            init();
            requestAnimationFrame(frame);
        })();
    </script>
</body>

</html>