<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";  // MySQL kullanıcı adı (root ya da sizin kullanıcı adınız)
$password = "";      // MySQL şifresi (boş bırakılabilir veya uygun şifreyi girin)
$dbname = "demo1";    // Veritabanı adı


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Çalışan ID'sini bulma
$calisan_id = null;
$sql = "SELECT id FROM calisanlar WHERE giris_yapildi = 1 LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $calisan_id = $row['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $izin_sebebi = $_POST['izin_sebebi'];
    $baslangic_tarihi = $_POST['baslangic_tarihi'];
    $bitis_tarihi = $_POST['bitis_tarihi'];

    if ($calisan_id) {
        $stmt = $conn->prepare("INSERT INTO izin_talepleri (calisan_id, izin_turu, baslangic_tarihi, bitis_tarihi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $calisan_id, $izin_sebebi, $baslangic_tarihi, $bitis_tarihi);

        if ($stmt->execute()) {
            echo '<div id="notification">
                    <p>İzin talebiniz başarıyla kaydedildi.</p>
                    <button id="okButton">Tamam</button>
                  </div>';
        } else {
            echo "Hata: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Aktif bir kullanıcı bulunamadı.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İzin Talebi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #2d3a51;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            box-sizing: border-box;
        }
        h1 {
            color: #ffffff;
            margin-bottom: 20px;
        }
        label {
            color: #ffffff;
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"], input[type="date"] {
            padding: 10px;
            width: 100%;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            background-color: #3c4b60;
            color: #fff;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="date"]:focus {
            border-color: #74ebd5;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4e88b0;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #6ba4d8;
        }
        #notification {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            display: none;
        }
        #okButton {
            background-color: #155724;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        #okButton:hover {
            background-color: #0c3d13;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>İzin Talep Formu</h1>
        <form method="POST" action="">
            <label for="izin_sebebi">İzin Sebebi:</label>
            <input type="text" id="izin_sebebi" name="izin_sebebi" required>

            <label for="baslangic_tarihi">Başlangıç Tarihi:</label>
            <input type="date" id="baslangic_tarihi" name="baslangic_tarihi" required>

            <label for="bitis_tarihi">Bitiş Tarihi:</label>
            <input type="date" id="bitis_tarihi" name="bitis_tarihi" required>

            <button type="submit">Gönder</button>
        </form>

        <div id="notification">
            <p>İzin talebiniz başarıyla kaydedildi.</p>
            <button id="okButton">Tamam</button>
        </div>
    </div>

    <script>
        const notification = document.getElementById('notification');
        const okButton = document.getElementById('okButton');

        if (notification) {
            notification.style.display = 'block';
            okButton.addEventListener('click', () => {
                window.location.href = 'personel_ekrani.php';
            });
        }
    </script>
</body>
</html>
