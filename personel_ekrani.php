<?php
session_start();

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";  // MySQL kullanıcı adı (root ya da sizin kullanıcı adınız)
$password = "";      // MySQL şifresi (boş bırakılabilir veya uygun şifreyi girin)
$dbname = "demo1";    // Veritabanı adı

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Çıkış yapma işlemi
if (isset($_POST['logout'])) {
    // Çıkış yapıldığında giris_yapildi'yi 0 yap
    $sql = "UPDATE calisanlar SET giris_yapildi = 0 WHERE giris_yapildi = 1 LIMIT 1";
    if ($conn->query($sql)) {
        // Oturumdan çıkış yap ve acilis.php'ye yönlendir
        session_destroy();
        header("Location: acilis.php");
        exit();
    } else {
        echo "Hata oluştu: " . $conn->error;
    }
}

// Giriş yapan kişinin bilgilerini almak
$sql = "SELECT * FROM calisanlar WHERE giris_yapildi = 1 LIMIT 1"; 
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
    <title>Personel Ekranı</title>
    <style>
        /* Genel Stil */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #3498db); /* Mavi tonları geçişi */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            overflow: hidden;
        }

        .container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1); /* Hafif şeffaf arka plan */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5); /* Derinlik arttırıldı */
            width: 95%;
            max-width: 1200px;
        }

        header h1 {
            color: #fff;
            font-size: 48px;
            font-weight: bold;
            text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.6);
        }

        .buttons, .bottom-buttons {
            margin-top: 30px;
        }

        .button {
            display: inline-block;
            text-decoration: none;
            padding: 30px 60px;
            font-size: 24px;
            margin: 20px 15px;
            border-radius: 12px;
            color: white;
            transition: background-color 0.4s, transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            width: 200px; /* Eşit boyutlar */
            text-align: center;
        }

        .izin-al {
            background: linear-gradient(135deg, #1f77b4,rgb(152, 33, 163)); /* Mavi tonları geçişi */
        }

        .avans-iste {
            background: linear-gradient(135deg, #2980b9,rgb(60, 218, 81)); /* Mavi tonları geçişi */
        }

        .mesajlarim {
            background: linear-gradient(135deg, #3498db,rgb(202, 26, 26)); /* Mavi tonları geçişi */
        }

        .taleblerim {
            background: linear-gradient(135deg, #2c7bbf,rgb(228, 164, 26)); /* Mavi tonları geçişi */
        }

        .button:hover {
            opacity: 0.9;
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
        }

        .bottom-buttons {
            margin-top: 30px;
            display: flex;
            justify-content: space-evenly;
            width: 100%;
        }

        /* Çıkış Butonu */
        .logout-button {
            position: absolute;
            top: 10px;
            right: 20px;
            padding: 15px 30px;
            font-size: 24px;
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s, transform 0.2s;
        }

        .logout-button:hover {
            background-color: #c9302c;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <!-- Çıkış Butonu -->
    <form action="personel_ekrani.php" method="POST">
        <button type="submit" name="logout" class="logout-button">Çıkış Yap</button>
    </form>

    <div class="container">
        <header>
            <h1>Hoşgeldiniz, <?php echo htmlspecialchars($user['ad'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        </header>

        <div class="buttons">
            <a href="izin_al.php" class="button izin-al">İzin Al</a>
            <a href="avans_iste.php" class="button avans-iste">Avans İste</a>
        </div>

        <div class="bottom-buttons">
            <a href="mesajlarim.php" class="button mesajlarim">Mesajlar</a>
            <a href="taleblerim.php" class="button taleblerim">Taleblerim</a>
        </div>
    </div>
</body>
</html>
