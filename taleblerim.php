<?php
// Veritabanı bağlantısını yap
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Giriş yapan çalışanın ID'sini al
$calisanIdQuery = "SELECT id FROM calisanlar WHERE giris_yapildi = 1 LIMIT 1";
$calisanIdResult = $conn->query($calisanIdQuery);

$calisanId = $calisanIdResult->num_rows > 0 ? $calisanIdResult->fetch_assoc()['id'] : null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taleblerim</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #a8d8ea, #fef6e4);
        }
        .container {
            width: 80%;
            max-width: 1200px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #d1e8e4, #fef6e4);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f0f4f8;
            color: #333;
        }
        td {
            background: white;
            color: #333;
        }
        tr:hover td {
            background: #f9f9f9;
        }
        th {
            box-shadow: inset 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
        .status-pending {
            color: blue;
            font-weight: bold;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Taleblerim</h1>

        <!-- İzin Talepleri Tablosu -->
        <h2>İzin Talepleri</h2>
        <table>
            <thead>
                <tr>
                    <th>Açıklama</th>
                    <th>Başlangıç Tarihi</th>
                    <th>Bitiş Tarihi</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($calisanId) {
                    $izinQuery = "SELECT izin_turu, baslangic_tarihi, bitis_tarihi, onay_durumu FROM izin_talepleri WHERE calisan_id = $calisanId";
                    $izinResult = $conn->query($izinQuery);

                    if ($izinResult->num_rows > 0) {
                        while ($row = $izinResult->fetch_assoc()) {
                            $statusClass = $row['onay_durumu'] === 'Beklemede' ? 'status-pending' : ($row['onay_durumu'] === 'Onaylandı' ? 'status-approved' : 'status-rejected');
                            echo "<tr>
                                <td>{$row['izin_turu']}</td>
                                <td>{$row['baslangic_tarihi']}</td>
                                <td>{$row['bitis_tarihi']}</td>
                                <td class='$statusClass'>{$row['onay_durumu']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Talep bulunamadı</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Talep bulunamadı</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Avans Talepleri Tablosu -->
        <h2>Avans Talepleri</h2>
        <table>
            <thead>
                <tr>
                    <th>Açıklama</th>
                    <th>Talep Tarihi</th>
                    <th>Talep Tutarı</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($calisanId) {
                    $avansQuery = "SELECT aciklama, talep_tarihi, talep_edilen_tutar, talep_durumu FROM avans_talepleri WHERE calisan_id = $calisanId";
                    $avansResult = $conn->query($avansQuery);

                    if ($avansResult->num_rows > 0) {
                        while ($row = $avansResult->fetch_assoc()) {
                            $statusClass = $row['talep_durumu'] === 'Beklemede' ? 'status-pending' : ($row['talep_durumu'] === 'Onaylandı' ? 'status-approved' : 'status-rejected');
                            echo "<tr>
                                <td>{$row['aciklama']}</td>
                                <td>{$row['talep_tarihi']}</td>
                                <td>{$row['talep_edilen_tutar']}</td>
                                <td class='$statusClass'>{$row['talep_durumu']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Talep bulunamadı</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Talep bulunamadı</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Geri Git Butonu -->
        <a href="personel_ekrani.php" class="back-button">Geri Git</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
