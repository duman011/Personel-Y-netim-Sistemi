<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";  // MySQL kullanıcı adı (root ya da sizin kullanıcı adınız)
$password = "";      // MySQL şifresi (boş bırakılabilir veya uygun şifreyi girin)
$dbname = "demo1";    // Veritabanı adı

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
    exit;
}

$parola = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tc_no'])) {
        $tc_no = trim($_POST['tc_no']);
        if (!ctype_digit($tc_no) || strlen($tc_no) !== 11) {
            $message = "Geçerli bir TC Kimlik Numarası giriniz!";
        } else {
            // TC kimlik numarasını veritabanında sorgula
            $stmt = $pdo->prepare("SELECT * FROM calisanlar WHERE tc_no = :tc_no");
            $stmt->bindParam(':tc_no', $tc_no);
            $stmt->execute();
            $personel = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($personel) {
                // TC no varsa, parola oluştur
                $parola = rand(100000, 999999); // 6 haneli rastgele bir parola oluştur

                // Parolayı veritabanına kaydet
                $updateStmt = $pdo->prepare("UPDATE calisanlar SET parola = :parola WHERE tc_no = :tc_no");
                $updateStmt->bindParam(':parola', $parola);
                $updateStmt->bindParam(':tc_no', $tc_no);
                $updateStmt->execute();

                $message = "Oluşturulan parola: " . $parola;
            } else {
                $message = "Personel bulunamadı!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parola Oluştur</title>
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
            background: #2d3a51; /* Koyu arka plan */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Daha belirgin gölge */
            width: 350px;
            text-align: center;
            box-sizing: border-box;
        }

        h1 {
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 12px;
            width: 100%;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #3c4b60; /* Koyu gri mavi */
            color: #fff;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #74ebd5; /* Odaklanıldığında daha soft bir mavi */
        }

        .button {
            background-color: #4e88b0; /* Koyu mavi */
            color: #fff;
            border: none;
            padding: 12px 25px;
            margin: 10px 5px;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 45%; /* Butonların boyutlarını eşitlemek için */
        }

        .button:hover {
            background-color: #6ba4d8; /* Hover efekti */
        }

        .buttons-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .message {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #e74c3c;
        }

        .copy-button {
            background-color: #4caf50;
            color: #fff;
            padding: 5px 10px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .copy-button:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("Parola kopyalandı!");
            }).catch(err => {
                alert("Parola kopyalanırken bir hata oluştu: " + err);
            });
        }
    </script>
</head>
<body>

<div class="form-container">
    <h1>Parola Oluştur</h1>

    <form method="POST">
        <input type="text" name="tc_no" placeholder="TC Kimlik No" required>
        <br>

        <div class="buttons-container">
            <button type="submit" class="button">Parola Oluştur</button>
            <button type="button" class="button" onclick="window.location.href='personel_giris.php'">Giriş Yap</button>
        </div>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $parola ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            <?php if ($parola): ?>
                <button class="copy-button" onclick="copyToClipboard('<?php echo $parola; ?>')">Kopyala</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
