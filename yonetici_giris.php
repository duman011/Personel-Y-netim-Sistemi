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
    die("Connection failed: " . $conn->connect_error);
}

// Kullanıcı girişini kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // SQL sorgusu ile kullanıcıyı kontrol et
    $sql = "SELECT * FROM adminler WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    // Eğer kullanıcı bulunduysa, giris_yapildi alanını güncelle ve yönlendir
    if ($result->num_rows > 0) {
        $update_sql = "UPDATE adminler SET giris_yapildi = 1 WHERE username = ? AND password = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $user, $pass);
        $update_stmt->execute();

        // Güncelleme başarılı mı kontrol et
        if ($update_stmt->affected_rows > 0) {
            header("Location: yonetici_ekrani.php");
            exit(); // Yönlendirme sonrası işlem bitmeli
        } else {
            $error = "Giriş kaydınız güncellenemedi!";
        }
    } else {
        $error = "Yanlış kullanıcı adı veya şifre!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli Girişi</title>
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
        .login-container {
            background: #2d3a51; /* Koyu arka plan */
            padding: 60px; /* Daha geniş padding */
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Daha belirgin gölge */
            width: 500px; /* Formu daha geniş yapalım */
            text-align: center;
            box-sizing: border-box;
        }
        h2 {
            color: #ffffff;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            padding: 14px; /* Daha fazla padding */
            width: 100%;
            margin: 12px 0; /* Daha fazla aralık */
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #3c4b60; /* Koyu gri mavi */
            color: #fff;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #74ebd5; /* Odaklanıldığında daha soft bir mavi */
        }
        .buttons-container {
            margin-top: 20px;
        }
        button {
            width: 48%;
            padding: 12px;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            display: inline-block;
            margin: 10px 1%;
        }
        .submit-button {
            background-color: #4caf50; /* Yeşil */
            width: 100%;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .back-button {
            background-color: #f44336; /* Kırmızı */
        }
        .back-button:hover {
            background-color: #d32f2f;
        }
        .add-button {
            background-color: #2196f3; /* Mavi */
        }
        .add-button:hover {
            background-color: #1976d2;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 15px;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Yönetici Girişi</h2>

        <!-- Giriş formu -->
        <form method="post">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required><br>
            <input type="password" name="password" placeholder="Şifre" required><br>
            <button type="submit" class="submit-button">Giriş Yap</button>
        </form>

        <div class="buttons-container">
             <button type="button" class="back-button" onclick="window.location.href='acilis.php';">Geri Git</button> 
             <button type="button" class="add-button" onclick="window.location.href='admin_ekle.php';">Admin Ekle</button> 
        </div>

        <?php
        // Hata mesajı göster
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
</body>
</html>
