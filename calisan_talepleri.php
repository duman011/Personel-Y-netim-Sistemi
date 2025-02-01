<?php
// calisan_talepleri.php

// Veritabanı bağlantısını yapıyoruz
$host = 'localhost';
$username = 'root';
$password = ''; // Şifrenizi buraya yazın
$database = 'demo1';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // İzin taleplerini güncelle
    if (isset($_POST['izin'])) {
        foreach ($_POST['izin'] as $calisan_id => $durum) {
            $stmt = $conn->prepare("UPDATE izin_talepleri SET onay_durumu = ? WHERE calisan_id = ? AND onay_durumu = 'Beklemede'");
            $stmt->bind_param('si', $durum, $calisan_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Avans taleplerini güncelle
    if (isset($_POST['avans'])) {
        foreach ($_POST['avans'] as $calisan_id => $durum) {
            $stmt = $conn->prepare("UPDATE avans_talepleri SET talep_durumu = ? WHERE calisan_id = ? AND talep_durumu = 'Beklemede'");
            $stmt->bind_param('si', $durum, $calisan_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Güncelleme sonrası yönlendirme
    header('Location: calisan_talepleri.php');
    exit;
}

// İzin taleplerini al
$izinQuery = "SELECT i.calisan_id, i.izin_turu, i.baslangic_tarihi, i.bitis_tarihi, i.onay_durumu, c.tc_no, c.ad, c.soyad
               FROM izin_talepleri i
               JOIN calisanlar c ON i.calisan_id = c.id
               WHERE i.onay_durumu = 'Beklemede'";
$izinResult = $conn->query($izinQuery);

// Avans taleplerini al
$avansQuery = "SELECT a.calisan_id, a.talep_tarihi, a.talep_edilen_tutar, a.aciklama, a.talep_durumu, c.tc_no, c.ad, c.soyad
               FROM avans_talepleri a
               JOIN calisanlar c ON a.calisan_id = c.id
               WHERE a.talep_durumu = 'Beklemede'";
$avansResult = $conn->query($avansQuery);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışan Talepleri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f2f5;
            color: #333;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #218838;
        }

        select {
            padding: 5px;
        }
    </style>
</head>
<body>

<h2>İzin Talepleri</h2>
<form method="POST" action="">
    <table>
        <thead>
            <tr>
                <th>TC No</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Açıklama</th>
                <th>Başlangıç Tarihi</th>
                <th>Bitiş Tarihi</th>
                <th>Cevap</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($izinResult->num_rows > 0): ?>
                <?php while ($row = $izinResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['tc_no'] ?></td>
                        <td><?= $row['ad'] ?></td>
                        <td><?= $row['soyad'] ?></td>
                        <td><?= $row['izin_turu'] ?></td>
                        <td><?= $row['baslangic_tarihi'] ?></td>
                        <td><?= $row['bitis_tarihi'] ?></td>
                        <td>
                            <select name="izin[<?= $row['calisan_id'] ?>]">
                                <option value="Beklemede" <?= $row['onay_durumu'] == 'Beklemede' ? 'selected' : '' ?>>Beklemede</option>
                                <option value="Onaylandı">Onaylandı</option>
                                <option value="Reddedildi">Reddedildi</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Beklemede olan izin talebi bulunmamaktadır.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Avans Talepleri</h2>
    <table>
        <thead>
            <tr>
                <th>TC No</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Talep Tarihi</th>
                <th>Talep Tutarı</th>
                <th>Açıklama</th>
                <th>Cevap</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($avansResult->num_rows > 0): ?>
                <?php while ($row = $avansResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['tc_no'] ?></td>
                        <td><?= $row['ad'] ?></td>
                        <td><?= $row['soyad'] ?></td>
                        <td><?= $row['talep_tarihi'] ?></td>
                        <td><?= $row['talep_edilen_tutar'] ?></td>
                        <td><?= $row['aciklama'] ?></td>
                        <td>
                            <select name="avans[<?= $row['calisan_id'] ?>]">
                                <option value="Beklemede" <?= $row['talep_durumu'] == 'Beklemede' ? 'selected' : '' ?>>Beklemede</option>
                                <option value="Onaylandı">Onaylandı</option>
                                <option value="Reddedildi">Reddedildi</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Beklemede olan avans talebi bulunmamaktadır.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button type="submit" class="btn">Kaydet</button>
</form>

</body>
</html>

<?php
$conn->close();
?>
