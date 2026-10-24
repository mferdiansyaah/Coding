
<?php
$barang = [
    [
        'nama' => '',
        'harga' => '',
        'jumlah' => '',
        'diskon' => 0.05
    ],
    [
        'nama' => '',
        'harga' => '',
        'jumlah' => '',
        'diskon' => 0.07
    ]
];

$member = 'tidak';
$totalHarga = 0;
$diskonMember = 0;
$totalBayar = 0;
$error = '';
$berhasil = false;

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member = $_POST['member'] ?? 'tidak';

    for ($i = 0; $i < 2; $i++) {
        $barang[$i]['nama'] = trim($_POST['nama'][$i] ?? '');
        $barang[$i]['harga'] = trim($_POST['harga'][$i] ?? '');
        $barang[$i]['jumlah'] = trim($_POST['jumlah'][$i] ?? '');
    }

    if (!in_array($member, ['ya', 'tidak'], true)) {
        $error = 'Pilihan member tidak valid.';
    } else {
        foreach ($barang as $item) {
            if ($item['nama'] === '') {
                $error = 'Nama kedua barang wajib diisi.';
                break;
            }

            if (
                $item['harga'] === '' ||
                !is_numeric($item['harga']) ||
                !is_finite((float)$item['harga']) ||
                (float)$item['harga'] <= 0
            ) {
                $error = 'Harga barang harus berupa angka lebih dari nol.';
                break;
            }

            if (
                $item['jumlah'] === '' ||
                filter_var($item['jumlah'], FILTER_VALIDATE_INT) === false ||
                (int)$item['jumlah'] <= 0
            ) {
                $error = 'Jumlah barang harus berupa bilangan bulat positif.';
                break;
            }
        }
    }

    if ($error === '') {
        foreach ($barang as $i => $item) {
            $harga = (float)$item['harga'];
            $jumlah = (int)$item['jumlah'];
            $diskon = $item['diskon'];

            $hargaSetelahDiskon = $harga * (1 - $diskon);
            $subtotal = $hargaSetelahDiskon * $jumlah;

            $barang[$i]['hargaAsli'] = $harga;
            $barang[$i]['jumlahBeli'] = $jumlah;
            $barang[$i]['hargaDiskon'] = $hargaSetelahDiskon;
            $barang[$i]['subtotal'] = $subtotal;

            $totalHarga += $subtotal;
        }

        $diskonMember = ($member === 'ya') ? $totalHarga * 0.05 : 0;
        $totalBayar = $totalHarga - $diskonMember;
        $berhasil = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Minimarket Online</title>

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
    --text: #f1f5ff;
    --muted: #94a3bd;
    --border: #243450;
}

body {
    min-height: 100vh;
    padding: 35px 16px;
    background:
        radial-gradient(ellipse at top, #152b50 0%, transparent 55%),
        var(--bg);
    color: var(--text);
    font-family: "Segoe UI", Arial, sans-serif;
}

.container {
    max-width: 760px;
    margin: 0 auto;
}

.header {
    text-align: center;
    margin-bottom: 28px;
}

.eyebrow {
    color: #69a5ff;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 3px;
    margin-bottom: 10px;
}

h1 {
    font-size: clamp(27px, 5vw, 36px);
    margin-bottom: 10px;
}

.subtitle {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.7;
}

.card {
    padding: 26px;
    margin-bottom: 22px;
    background: linear-gradient(145deg, #14213a, #0e192c);
    border: 1px solid var(--border);
    border-radius: 19px;
    box-shadow: 0 20px 55px #00000025;
}

h2 {
    font-size: 17px;
    margin-bottom: 22px;
}

.product {
    padding: 19px;
    margin-bottom: 16px;
    background: #0b1528;
    border: 1px solid #273955;
    border-radius: 14px;
}

.product-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
}

.product-title h3 {
    font-size: 14px;
}

.discount {
    color: #8ec5ff;
    background: #1d3b65;
    border-radius: 20px;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 700;
}

label {
    display: block;
    color: #c8d4e9;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 8px;
}

input, select {
    width: 100%;
    height: 45px;
    padding: 0 12px;
    border: 1px solid #2a3b59;
    border-radius: 9px;
    background: #080f1e;
    color: var(--text);
    font: inherit;
    outline: none;
}

input:focus, select:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px #3984ff22;
}

.field {
    margin-bottom: 15px;
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.member-info {
    color: var(--muted);
    font-size: 12px;
    margin-top: 9px;
}

.actions {
    display: flex;
    gap: 12px;
    margin-top: 22px;
}

button, .reset {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 47px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
}

button {
    flex: 1;
    border: none;
    color: white;
    background: linear-gradient(100deg, #347cff, #2463e8);
}

button:hover {
    filter: brightness(1.12);
}

.reset {
    padding: 0 20px;
    color: #d1dbee;
    background: #17243a;
    border: 1px solid #344561;
}

.error {
    padding: 14px;
    margin-bottom: 20px;
    color: #ffc1c1;
    background: #7f1d1d30;
    border: 1px solid #ef444466;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.6;
}

.result-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    padding: 13px 0;
    border-bottom: 1px solid #273955;
    font-size: 13px;
    line-height: 1.6;
}

.result-row span:first-child {
    color: var(--muted);
}

.result-row strong {
    text-align: right;
    overflow-wrap: anywhere;
}

.total {
    margin-top: 20px;
    padding: 22px;
    background: linear-gradient(135deg, #15305b, #10213c);
    border: 1px solid #3984ff66;
    border-radius: 14px;
}

.total-label {
    color: #a7c8ff;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
}

.total-value {
    font-size: clamp(25px, 5vw, 34px);
    font-weight: 800;
    margin-top: 9px;
    overflow-wrap: anywhere;
}

.footer {
    text-align: center;
    color: #71819c;
    font-size: 11px;
    line-height: 1.8;
    margin-top: 25px;
}

@media (max-width: 500px) {
    .card {
        padding: 18px;
    }

    .product {
        padding: 14px;
    }

    .grid {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .actions {
        flex-direction: column;
    }

    .reset {
        min-height: 43px;
    }
}
</style>
</head>

<body>
<main class="container">

    <header class="header">
        <h1>Minimarket Online</h1>
        <p class="subtitle">
            Hitung harga belanja dan diskon member dengan mudah.<br>
            Masukkan dua barang untuk melihat total pembayaran.
        </p>
    </header>

    <section class="card">
        <h2>Informasi Belanja</h2>

        <?php if ($error !== ''): ?>
            <div class="error" role="alert">
                <strong>Input belum valid</strong><br>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php foreach ($barang as $i => $item): ?>
                <div class="product">
                    <div class="product-title">
                        <h3>Barang <?= $i + 1 ?></h3>
                        <span class="discount">
                            Diskon <?= (int)($item['diskon'] * 100) ?>%
                        </span>
                    </div>

                    <div class="field">
                        <label for="nama<?= $i ?>">Nama Barang</label>
                        <input
                            type="text"
                            id="nama<?= $i ?>"
                            name="nama[<?= $i ?>]"
                            maxlength="100"
                            placeholder="Contoh: Sabun Cuci"
                            value="<?= e($item['nama']) ?>"
                            required
                        >
                    </div>

                    <div class="grid">
                        <div class="field">
                            <label for="harga<?= $i ?>">Harga Satuan (Rp)</label>
                            <input
                                type="number"
                                id="harga<?= $i ?>"
                                name="harga[<?= $i ?>]"
                                min="1"
                                step="any"
                                placeholder="9000"
                                value="<?= e($item['harga']) ?>"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="jumlah<?= $i ?>">Jumlah Beli</label>
                            <input
                                type="number"
                                id="jumlah<?= $i ?>"
                                name="jumlah[<?= $i ?>]"
                                min="1"
                                step="1"
                                placeholder="3"
                                value="<?= e($item['jumlah']) ?>"
                                required
                            >
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="field">
                <label for="member">Apakah pembeli merupakan member?</label>
                <select id="member" name="member">
                    <option value="tidak"
                        <?= $member === 'tidak' ? 'selected' : '' ?>>
                        Bukan Member
                    </option>
                    <option value="ya"
                        <?= $member === 'ya' ? 'selected' : '' ?>>
                        Ya, Saya Member
                    </option>
                </select>
                <p class="member-info">
                    Member mendapatkan diskon tambahan 5% dari total
                    harga setelah diskon masing-masing barang.
                </p>
            </div>

            <div class="actions">
                <button type="submit">Hitung Total Belanja</button>
                <a class="reset" href="soal2.php">Reset</a>
            </div>
        </form>
    </section>

    <?php if ($berhasil): ?>
        <section class="card" aria-live="polite">
            <h2>Rincian Pembayaran</h2>

            <?php foreach ($barang as $i => $item): ?>
                <div class="result-row">
                    <span>
                        <?= e($item['nama']) ?><br>
                        <?= rupiah($item['hargaAsli']) ?> ×
                        <?= $item['jumlahBeli'] ?><br>
                        Harga satuan setelah diskon
                        <?= (int)($item['diskon'] * 100) ?>%:
                        <?= rupiah($item['hargaDiskon']) ?>
                    </span>
                    <strong><?= rupiah($item['subtotal']) ?></strong>
                </div>
            <?php endforeach; ?>

            <div class="result-row">
                <span>Total setelah diskon barang</span>
                <strong><?= rupiah($totalHarga) ?></strong>
            </div>

            <div class="result-row">
                <span>Diskon member (5%)</span>
                <strong>- <?= rupiah($diskonMember) ?></strong>
            </div>

            <div class="total">
                <p class="total-label">TOTAL YANG HARUS DIBAYAR</p>
                <p class="total-value"><?= rupiah($totalBayar) ?></p>
            </div>
        </section>
    <?php endif; ?>

    <footer class="footer">
        <p>Minimarket Online &bull; PHP Mini Project</p>
        <p>Perhitungan harga, diskon barang, dan diskon member</p>
    </footer>

</main>
</body>
</html>
