
<?php
$bil1 = '';
$bil2 = '';
$operator = '+';
$hasil = null;
$error = '';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatAngka($angka) {
    if (is_string($angka)) {
        return $angka;
    }

    if (abs($angka) < 1e-12) {
        return '0';
    }

    return rtrim(rtrim(number_format($angka, 10, '.', ''), '0'), '.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bil1 = trim($_POST['bil1'] ?? '');
    $bil2 = trim($_POST['bil2'] ?? '');
    $operator = $_POST['operator'] ?? '+';

    $operatorValid = ['+', '-', '*', '/', '%'];

    if ($bil1 === '' || $bil2 === '') {
        $error = 'Silakan isi kedua bilangan terlebih dahulu.';
    } elseif (!is_numeric($bil1) || !is_numeric($bil2)) {
        $error = 'Kedua input harus berupa angka yang valid.';
    } elseif (!in_array($operator, $operatorValid, true)) {
        $error = 'Operator yang dipilih tidak valid.';
    } else {
        $a = (float)$bil1;
        $b = (float)$bil2;

        if (!is_finite($a) || !is_finite($b)) {
            $error = 'Bilangan terlalu besar atau tidak valid.';
        } else {
            switch ($operator) {
                case '+':
                    $hasil = $a + $b;
                    break;

                case '-':
                    $hasil = $a - $b;
                    break;

                case '*':
                    $hasil = $a * $b;
                    break;

                case '/':
                    if ($b == 0.0) {
                        $error = 'Pembagian dengan nol tidak diperbolehkan.';
                    } else {
                        $hasil = $a / $b;
                    }
                    break;

                case '%':
                    if ($b == 0.0) {
                        $error = 'Modulus dengan nol tidak diperbolehkan.';
                    } else {
                        $hasil = fmod($a, $b);
                    }
                    break;
            }

            if ($hasil !== null && !is_finite($hasil)) {
                $hasil = null;
                $error = 'Hasil perhitungan berada di luar batas angka.';
            }
        }
    }
}

$namaOperator = [
    '+' => 'Penjumlahan',
    '-' => 'Pengurangan',
    '*' => 'Perkalian',
    '/' => 'Pembagian',
    '%' => 'Modulus'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator | Dark Blue</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            color-scheme: dark;
            --bg: #080f1e;
            --panel: #111c31;
            --input: #0b1528;
            --blue: #3984ff;
            --blue-light: #69a5ff;
            --text: #f1f5ff;
            --muted: #94a3bd;
            --border: #243450;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 32px 16px;
            background:
                radial-gradient(ellipse at top, #152b50 0%, transparent 55%),
                var(--bg);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 520px;
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
        }

        .eyebrow {
            color: var(--blue-light);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 9px;
        }

        h1 {
            font-size: clamp(28px, 6vw, 36px);
            letter-spacing: -1px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .card {
            padding: 28px;
            background: linear-gradient(145deg, #14213a, #0e192c);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: 0 25px 70px #00000035;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .field {
            margin-bottom: 19px;
        }

        label {
            display: block;
            color: #c8d4e9;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 9px;
        }

        input, select {
            width: 100%;
            height: 50px;
            padding: 0 14px;
            border: 1px solid #2a3b59;
            border-radius: 11px;
            background: var(--input);
            color: var(--text);
            font: inherit;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus, select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px #3984ff22;
        }

        input::placeholder {
            color: #64748b;
        }

        .hint {
            color: #7788a4;
            font-size: 11px;
            margin-top: 7px;
        }

        .actions {
            display: flex;
            gap: 11px;
            margin-top: 25px;
        }

        button, .reset {
            min-height: 48px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 11px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: .2s;
        }

        button {
            flex: 1;
            border: none;
            background: linear-gradient(100deg, #347cff, #2463e8);
            color: white;
            box-shadow: 0 7px 20px #2563eb30;
        }

        button:hover {
            filter: brightness(1.12);
            transform: translateY(-1px);
        }

        .reset {
            padding: 0 20px;
            border: 1px solid #344561;
            color: #d1dbee;
            background: #17243a;
        }

        .reset:hover {
            background: #243550;
        }

        .result {
            margin-top: 23px;
            padding: 21px;
            border: 1px solid #3984ff66;
            border-radius: 15px;
            background: linear-gradient(135deg, #15305b, #10213c);
            animation: appear .3s ease;
        }

        .result-label {
            color: #a7c8ff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .equation {
            color: #d5e3ff;
            font-size: 14px;
            margin-top: 12px;
            overflow-wrap: anywhere;
        }

        .result-value {
            color: #ffffff;
            font-size: clamp(28px, 7vw, 38px);
            font-weight: 800;
            letter-spacing: -1px;
            overflow-wrap: anywhere;
            margin-top: 8px;
        }

        .error {
            margin-top: 20px;
            padding: 14px;
            color: #ffc1c1;
            background: #7f1d1d30;
            border: 1px solid #ef444466;
            border-radius: 11px;
            font-size: 13px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 23px;
            color: #71819c;
            font-size: 11px;
            text-align: center;
            line-height: 1.8;
        }

        @keyframes appear {
            from { opacity: 0; transform: translateY(7px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 420px) {
            .card {
                padding: 21px 17px;
            }

            .actions {
                flex-direction: column;
            }

            .reset {
                min-height: 44px;
            }
        }
    </style>
</head>

<body>
    <main class="container">
        <header class="header">
            <h1>Kalkulator Digital</h1>
            <p class="subtitle">
                Perhitungan sederhana dengan tampilan modern.<br>
                Masukkan angka dan pilih operator yang kamu inginkan.
            </p>
        </header>

        <section class="card">
            <h2 class="section-title">Input Perhitungan</h2>

            <form method="POST" action="">
                <div class="field">
                    <label for="bil1">Bilangan Pertama</label>
                    <input
                        type="number"
                        id="bil1"
                        name="bil1"
                        step="any"
                        placeholder="Contoh: 25"
                        value="<?= e($bil1) ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="bil2">Bilangan Kedua</label>
                    <input
                        type="number"
                        id="bil2"
                        name="bil2"
                        step="any"
                        placeholder="Contoh: 5"
                        value="<?= e($bil2) ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="operator">Pilih Operator</label>
                    <select id="operator" name="operator">
                        <?php foreach ($namaOperator as $simbol => $nama): ?>
                            <option
                                value="<?= e($simbol) ?>"
                                <?= $operator === $simbol ? 'selected' : '' ?>
                            >
                                <?= e($nama) ?> (<?= e($simbol) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="actions">
                    <button type="submit">= &nbsp; Hitung Sekarang</button>
                    <a class="reset" href="data.php">Reset</a>
                </div>
            </form>

            <?php if ($error !== ''): ?>
                <div class="error" role="alert">
                    <strong>Perhitungan gagal</strong><br>
                    <?= e($error) ?>
                </div>
            <?php elseif ($hasil !== null): ?>
                <div class="result" aria-live="polite">
                    <p class="result-label">Hasil Perhitungan</p>
                    <p class="equation">
                        <?= e(formatAngka((float)$bil1)) ?>
                        <?= e($operator) ?>
                        <?= e(formatAngka((float)$bil2)) ?>
                        =
                    </p>
                    <p class="result-value">
                        <?= e(formatAngka($hasil)) ?>
                    </p>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <p>PHP Calculator &bull; Simple, Fast &amp; Reliable</p>
            <p>Created by Muhammad Ferdiansyah</p>
        </footer>
    </main>
</body>
</html>
