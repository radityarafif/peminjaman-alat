<?php
require 'config.php';
if (!empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = q("SELECT * FROM users WHERE username=?", [trim($_POST['username'])])->fetch_assoc();
    if ($u && password_verify($_POST['password'], $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'nama' => $u['nama'], 'role' => $u['role']];
        logAkt('Login');
        header('Location: index.php');
        exit;
    }
    $err = 'Username atau password salah';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Peminjaman Alat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #10242e;
            --board: #16313d;
            --board-dot: #27495a;
            --paper: #f6f4ef;
            --line: #d9d5ca;
            --text: #1b2b33;
            --muted: #5f6f76;
            --signal: #f2b705;
            --signal-dark: #c99700;
            --danger-bg: #fde8e6;
            --danger: #a3281d;
        }

        * {
            box-sizing: border-box;
            margin: 0
        }

        html,
        body {
            height: 100%
        }

        body {
            font-family: 'Figtree', system-ui, sans-serif;
            color: var(--text);
            background: var(--paper);
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            min-height: 100vh;
        }

        /* Panel kiri: papan peralatan (pegboard) */
        .board {
            position: relative;
            background-color: var(--board);
            background-image: radial-gradient(var(--board-dot) 2.5px, transparent 3px);
            background-size: 34px 34px;
            background-position: 17px 17px;
            color: #fff;
            padding: 56px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .brand {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand i {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--signal);
            display: grid;
            place-items: center;
        }

        .brand svg {
            width: 20px;
            height: 20px;
            stroke: var(--ink);
            fill: none;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .hero h1 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 800;
            font-size: clamp(2.6rem, 5vw, 4.4rem);
            line-height: 1;
            letter-spacing: -.03em;
            max-width: 10ch;
        }

        .hero p {
            margin-top: 22px;
            max-width: 34ch;
            color: #b9ccd4;
            line-height: 1.6;
        }

        /* Label gantung bergaya tag inventaris */
        .tag {
            position: absolute;
            right: -18px;
            bottom: 120px;
            transform: rotate(-6deg);
            background: var(--signal);
            color: var(--ink);
            padding: 14px 56px 14px 34px;
            border-radius: 12px 0 0 12px;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .28);
        }

        .tag::before {
            content: "";
            position: absolute;
            left: 12px;
            top: 50%;
            width: 10px;
            height: 10px;
            margin-top: -5px;
            border-radius: 50%;
            background: var(--board);
        }

        .tag small {
            display: block;
            font: 500 .78rem 'Figtree', sans-serif;
            opacity: .75
        }

        /* Panel kanan: form */
        .panel {
            display: grid;
            place-items: center;
            padding: 40px 28px
        }

        .form {
            width: 100%;
            max-width: 380px
        }

        .form h2 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .form .sub {
            color: var(--muted);
            margin: 8px 0 30px;
            line-height: 1.5
        }

        .alert {
            background: var(--danger-bg);
            color: var(--danger);
            border-left: 4px solid var(--danger);
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: .95rem;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: .92rem;
            margin-bottom: 8px
        }

        .field {
            margin-bottom: 20px;
            position: relative
        }

        input {
            width: 100%;
            font: inherit;
            color: var(--text);
            background: #fff;
            border: 1.5px solid var(--line);
            border-radius: 10px;
            padding: 14px 16px;
            transition: border-color .15s, box-shadow .15s;
        }

        input:focus {
            outline: none;
            border-color: var(--ink);
            box-shadow: 0 0 0 4px rgba(242, 183, 5, .35);
        }

        .toggle {
            position: absolute;
            right: 8px;
            bottom: 8px;
            border: 0;
            background: transparent;
            color: var(--muted);
            font: 600 .82rem 'Figtree', sans-serif;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        .toggle:hover {
            color: var(--ink);
            background: #efece4
        }

        .toggle:focus-visible {
            outline: 2px solid var(--ink)
        }

        button.masuk {
            width: 100%;
            margin-top: 6px;
            font: 700 1rem 'Figtree', sans-serif;
            background: var(--ink);
            color: #fff;
            border: 0;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            box-shadow: 0 4px 0 #000;
            transition: transform .1s, box-shadow .1s, background .15s;
        }

        button.masuk:hover {
            background: #17343f
        }

        button.masuk:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 #000
        }

        button.masuk:focus-visible {
            outline: 3px solid var(--signal);
            outline-offset: 3px
        }

        .foot {
            margin-top: 28px;
            font-size: .85rem;
            color: var(--muted)
        }

        /* Animasi masuk: satu kali saja untuk label */
        @media (prefers-reduced-motion:no-preference) {
            .tag {
                animation: swing 1.1s cubic-bezier(.2, .9, .3, 1.2) .2s both
            }

            @keyframes swing {
                from {
                    transform: rotate(8deg) translateX(40px);
                    opacity: 0
                }

                to {
                    transform: rotate(-6deg);
                    opacity: 1
                }
            }
        }

        @media (max-width:860px) {
            body {
                grid-template-columns: 1fr
            }

            .board {
                padding: 28px;
                min-height: 230px
            }

            .hero h1 {
                font-size: 2.3rem
            }

            .hero p,
            .tag {
                display: none
            }
        }
    </style>
</head>

<body>

    <section class="board" aria-hidden="true">
        <div class="brand">
            <i><svg viewBox="0 0 24 24">
                    <path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4z" />
                </svg></i>
            Peminjaman Alat
        </div>
        <div class="hero">
            <h1>Alat siap, tinggal pinjam.</h1>
            <p>Ajukan peminjaman, pantau statusnya, dan kembalikan tepat waktu dalam satu tempat.</p>
        </div>
        <div class="tag">Tersedia hari ini<small>Cek stok setelah masuk</small></div>
    </section>

    <main class="panel">
        <form class="form" method="post" action="">
            <h2>Masuk</h2>
            <p class="sub">Gunakan akun yang diberikan admin.</p>

            <?php if ($err): ?>
                <div class="alert" role="alert"><?= e($err) ?></div>
            <?php endif; ?>

            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                <button type="button" class="toggle" id="toggle" aria-controls="password">Tampilkan</button>
            </div>

            <button class="masuk" type="submit">Masuk</button>
            <p class="foot">Lupa password? Hubungi admin.</p>
        </form>
    </main>

    <script>
        const pw = document.getElementById('password');
        const tg = document.getElementById('toggle');
        tg.addEventListener('click', () => {
            const show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            tg.textContent = show ? 'Sembunyikan' : 'Tampilkan';
        });
    </script>
</body>

</html>