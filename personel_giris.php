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
    $tc_no = $_POST['tc_no'];
    $parola = $_POST['parola'];

    // TC numarasını INT türüne dönüştür
    $tc_no = intval($tc_no);  // Burada TC numarasını tam sayıya dönüştürüyoruz

    // SQL sorgusu ile kullanıcıyı kontrol et
    $sql = "SELECT * FROM calisanlar WHERE tc_no = ? AND parola = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $tc_no, $parola); // tc_no'yu INT olarak bağla
    $stmt->execute();
    $result = $stmt->get_result();

    // Eğer kullanıcı bulunduysa, giriş bilgilerini kullanıcı_girisler tablosuna kaydet
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $giris_yapan_id = $row['tc_no'];  // Burada doğru TC numarasını alıyoruz
        $ad = $row['ad']; // Adı al
        $tarih = date('Y-m-d H:i:s'); // Şu anki tarih ve saati al

        // Kullanıcı giriş bilgilerini kullanıcı_girisler tablosuna ekle
        $insert_sql = "INSERT INTO kullanıcı_girisler (giris_yapan_id, ad, tarih) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sss", $giris_yapan_id, $ad, $tarih); 
        $insert_stmt->execute();

        // 'giris_yapildi' sütununu 1 yap
        $update_sql = "UPDATE calisanlar SET giris_yapildi = 1 WHERE tc_no = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $tc_no); // tc_no'yu INT olarak güncelle
        $update_stmt->execute();

        // Başarılı giriş, personel_ekrani.php'ye yönlendir
        header("Location: personel_ekrani.php");
        exit();
    } else {
        $error = "Yanlış TC No veya parola!";
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
    <title>Personel Giriş</title>
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
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Daha belirgin gölge */
            width: 300px;
            text-align: center;
            box-sizing: border-box;
        }
        h2 {
            color: #ffffff;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            padding: 12px;
            width: 100%;
            margin: 10px 0;
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
        button {
            width: 100%;
            padding: 12px;
            background-color: #4e88b0; /* Koyu mavi */
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #6ba4d8; /* Hover efekti */
        }
        .secondary-button {
            margin-top: 10px;
            background-color: #007bff;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            color: white;
            font-size: 16px;
        }
        .secondary-button:hover {
            background-color: #0056b3;
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
        <h2>Personel Giriş</h2>

        <!-- Giriş formu -->
        <form action="personel_giris.php" method="post">
            <input type="text" name="tc_no" placeholder="TC No" required><br>
            <input type="password" name="parola" placeholder="Parola" required><br>
            <button type="submit">Giriş Yap</button>
        </form>

        <button class="secondary-button" onclick="window.location.href='parola_olustur.php';">Parola Oluştur</button>

        <?php
        // Hata mesajı göster
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
</body>
</html>
