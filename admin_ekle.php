<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";  // MySQL kullanıcı adı (root ya da sizin kullanıcı adınız)
$password = "";      // MySQL şifresi (boş bırakılabilir veya uygun şifreyi girin)
$dbname = "demo1";    // Veritabanı adı

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Form gönderimi kontrolü
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen veriyi al
    $user = $conn->real_escape_string($_POST['username']);
    $pass = $conn->real_escape_string($_POST['password']);



    // SQL sorgusu
    $sql = "INSERT INTO adminler (username, password) VALUES ('$user', '$pass')";

    if ($conn->query($sql) === TRUE) {
        $message = "✅ Yeni admin başarıyla eklendi.";
    } else {
        $message = "❌ Hata: " . $conn->error;
    }
}

// Bağlantıyı kapat
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Ekle</title>
    <style>
        /* Genel sayfa stili */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #ffffff;
        }

        /* Form kutusunun stili */
        .container {
            background: #2d3a51; /* Koyu arka plan */
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: #333333;
        }

        h2 {
            margin-bottom: 20px;
            color: #0984e3; /* Yönetici Girişi başlığına uyacak şekilde mavi */
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: bold;
            text-align: left;
            color: #ffffff;
        }

        input {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease;
            background-color: #3c4b60; /* Koyu gri mavi */
            color: #fff;
        }

        input:focus {
            border-color: #74ebd5; /* Mavi odak rengi */
        }

        button {
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            background-color: #4e88b0; /* Koyu mavi */
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #6ba4d8; /* Hover efekti */
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive tasarım */
        @media (max-width: 768px) {
            .container {
                padding: 20px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Ekle</h2>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Hata') === false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="admin_ekle.php" method="POST">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" name="username" id="username" placeholder="Kullanıcı adı giriniz" required>
            
            <label for="password">Şifre:</label>
            <input type="password" name="password" id="password" placeholder="Şifre giriniz" required>
            
            <button type="submit">Ekle</button>
            <button type="button" onclick="window.location.href='yonetici_giris.php';">Geri Dön</button>
        </form>
    </div>
</body>
</html>
