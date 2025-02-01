<?php
// avans_iste.php

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Veritabanına bağlanılamadı: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kaydet'])) {
    // Çalışan bilgilerini çek
    $calisan_query = "SELECT id, ad FROM calisanlar WHERE giris_yapildi = 1 LIMIT 1";
    $calisan_result = $conn->query($calisan_query);

    if ($calisan_result && $calisan_result->num_rows > 0) {
        $calisan_row = $calisan_result->fetch_assoc();
        $calisan_id = $calisan_row['id'];
        $calisan_ad = $calisan_row['ad'];

        // Formdan gelen verileri al
        $talep_tarihi = $_POST['talep_tarihi'];
        $talep_edilen_tutar = $_POST['talep_edilen_tutar'];
        $aciklama = $_POST['aciklama'];
        $talep_durumu = "Beklemede";

        // Verileri tabloya ekle
        $insert_query = "INSERT INTO avans_talepleri (calisan_id, talep_tarihi, talep_edilen_tutar, talep_durumu, aciklama) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("isdss", $calisan_id, $talep_tarihi, $talep_edilen_tutar, $talep_durumu, $aciklama);

        if ($stmt->execute()) {
            $kayit_mesaji = "Teşekkürler $calisan_ad, avans talebiniz alınmıştır. En kısa sürede dönüş yapılacaktır.";
        } else {
            $kayit_mesaji = "Avans talebi kaydedilirken bir hata oluştu.";
        }
        $stmt->close();
    } else {
        $kayit_mesaji = "Aktif bir çalışan bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avans İste</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #2C3E50, #1F4D64, #48C9B0); /* Derin mavi-yeşil geçişli arka plan */
            color: #EAEAEA; /* Hafif gri tonlarında yazı rengi */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 900px;
            width: 90%;
            background: linear-gradient(135deg, #1ABC9C, #2C3E50); /* Koyu mavi-yeşil geçişli form arka planı */
            padding: 40px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.5); /* Güçlü gölgelendirme */
            border-radius: 20px;
            border: 3px solid transparent;
            background-clip: padding-box;
            background-image: linear-gradient(135deg, #16A085, #2C3E50); /* Kenar geçişli arka plan */
        }
        h1 {
            text-align: center;
            color: #E5E5E5; /* Yumuşak beyaz başlık rengi */
            font-size: 36px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Başlık gölgelendirme efekti */
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
            color: #F4F4F4; /* Nötr beyaz tonları */
        }
        input, textarea, button, .slider {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #BDC3C7; /* Hafif gri sınır */
            border-radius: 8px;
            font-size: 16px;
            background: #2C3E50; /* Koyu gri arka plan */
            color: #EAEAEA;
        }
        input[type="date"], input[type="number"] {
            background: #2C3E50;
            color: #EAEAEA;
        }
        button {
            background-color: #16A085; /* Para tonunda buton */
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 18px;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3); /* Buton gölgelendirme */
        }
        button:hover {
            background-color: #1ABC9C; /* Hover için daha canlı yeşil */
            box-shadow: 0px 6px 18px rgba(0, 0, 0, 0.4);
        }
        .back-button {
            background-color: #E74C3C; /* Çıkışla uyumlu kırmızı geri git butonu */
            font-size: 18px;
            padding: 18px;
            border-radius: 8px;
        }
        .back-button:hover {
            background-color: #C0392B; /* Koyu kırmızı hover */
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        .notification {
            background-color: #2ECC71; /* Başarılı bildirim */
            border: 1px solid #27AE60;
            color: #FFF;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 18px;
        }

        /* Kaydırıcı (slider) stil */
        .slider {
            width: 100%;
            height: 15px;
            background: #BDC3C7;
            border-radius: 8px;
            outline: none;
        }
        .slider::-webkit-slider-runnable-track {
            height: 15px;
            background: #34495E;
        }
        .slider::-webkit-slider-thumb {
            width: 20px;
            height: 20px;
            background: #1ABC9C;
            border-radius: 50%;
            cursor: pointer;
        }
        .slider::-moz-range-track {
            height: 15px;
            background: #34495E;
        }
        .slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #1ABC9C;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Avans İste</h1>
    <?php if (isset($kayit_mesaji)): ?>
        <div class="notification">
            <p><?php echo $kayit_mesaji; ?></p>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="talep_tarihi">Talep Tarihi:</label>
        <input type="date" id="talep_tarihi" name="talep_tarihi" required>

        <label for="talep_edilen_tutar">Talep Edilen Tutar:</label>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <input type="number" id="talep_edilen_tutar" name="talep_edilen_tutar" step="0.01" placeholder="Örn: 1000.50" required style="flex: 1;"/>
            <input type="range" id="slider" name="slider" min="0" max="5000" step="10" oninput="document.getElementById('talep_edilen_tutar').value = this.value" style="width: 100%;"/>
        </div>

        <label for="aciklama">Açıklama:</label>
        <textarea id="aciklama" name="aciklama" rows="4" placeholder="Ek bilgi girebilirsiniz..."></textarea>

        <div class="button-group">
            <button type="submit" name="kaydet">Talebi Gönder</button>
            <button type="button" class="back-button" onclick="confirmExit()">Geri Git</button>
        </div>
    </form>
</div>

<script>
    function confirmExit() {
        const confirmed = confirm("Kaydedilen bir işlem bulunamadı. Çıkmayı onaylıyor musunuz?");
        if (confirmed) {
            window.location.href = 'personel_ekrani.php';  // İlgili sayfaya yönlendirme
        }
    }
</script>
</body>
</html>

<?php $conn->close(); ?>
