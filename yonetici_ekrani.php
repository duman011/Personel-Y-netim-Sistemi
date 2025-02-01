<?php
session_start();

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Çıkış yapma işlemi
if (isset($_POST['logout'])) {
    $sql = "UPDATE adminler SET giris_yapildi = 0 WHERE giris_yapildi = 1 LIMIT 1";
    if ($conn->query($sql)) {
        session_destroy();
        header("Location: yonetici_giris.php");
        exit();
    } else {
        echo "Hata oluştu: " . $conn->error;
    }
}

// Giriş yapan yönetici bilgilerini almak
$sql = "SELECT username, yonetici_id FROM adminler WHERE giris_yapildi = 1 LIMIT 1";
$result = $conn->query($sql);

if (!$result) {
    die("Sorgu hatası: " . $conn->error);
}

$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Ekranı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(45deg, #000080, #0000b3, #003366);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            text-align: center;
        }

        .button {
            display: inline-block;
            color: white;
            padding: 15px 30px;
            margin: 20px;
            font-size: 18px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }

        .button:hover {
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.4);
            transform: translateY(-5px);
        }

        .button:active {
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
            transform: translateY(3px);
        }

        .button.talepler {
            background-color: #007bff;
        }

        .button.talepler:hover {
            background-color: #0056b3;
        }

        .button.tablo {
            background-color: #28a745;
        }

        .button.tablo:hover {
            background-color: #218838;
        }

        .button.mesajlar {
            background-color: #17a2b8;
        }

        .button.mesajlar:hover {
            background-color: #117a8b;
        }

        .button.giris {
            background-color: #ffc107;
        }

        .button.giris:hover {
            background-color: #e0a800;
        }

        .heading {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 30px;
            background: linear-gradient(90deg, #0099ff, #003366);
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);
        }

        .logout-button {
            position: absolute;
            top: 20px;
            right: 30px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .logout-button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Çıkış Butonu -->
    <form action="" method="POST">
        <button type="submit" name="logout" class="logout-button">Çıkış Yap</button>
    </form>

    <div class="container">
        <div class="heading">
            Hoşgeldiniz, 
            <?php 
            echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); 
            echo " (" . intval($user['yonetici_id']) . ")"; 
            ?>!
        </div>
        <a href="calisan_talepleri.php" class="button talepler">Talepler</a>
        <a href="calisan_tablo.php" class="button tablo">Çalışan Tablosu</a>
        <a href="yonetici_mesajlarim.php" class="button mesajlar">Mesajlar</a>
        <a href="giris_yapanlar.php" class="button giris">Giriş İşlemleri</a>
    </div>
</body>
</html>
