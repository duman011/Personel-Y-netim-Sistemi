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

// Çalışan ekleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ekle'])) {
    $tc_no = $_POST['tc_no'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $departman = $_POST['departman'];
    $maas = $_POST['maas'];

    // TC No kontrolü (11 haneli olmalı)
    if (strlen($tc_no) != 11) {
        $message = "TC No 11 haneli olmalıdır!";
    } else {
        $sql = "INSERT INTO calisanlar (tc_no, ad, soyad, departman, maas) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $tc_no, $ad, $soyad, $departman, $maas);
        $stmt->execute();
        $stmt->close();
        $message = "Çalışan başarıyla eklendi!";
    }
}

// Çalışan silme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sil'])) {
    $tc_no = $_POST['tc_no'];

    $sql = "DELETE FROM calisanlar WHERE tc_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tc_no);
    if ($stmt->execute()) {
        $message = "Çalışan başarıyla silindi!";
    } else {
        $message = "Çalışan bulunamadı!";
    }
    $stmt->close();
}

// Çalışan güncelleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guncelle'])) {
    $id = $_POST['id'];
    $tc_no = $_POST['tc_no'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $departman = $_POST['departman'];
    $maas = $_POST['maas'];

    // TC No kontrolü (11 haneli olmalı)
    if (strlen($tc_no) != 11) {
        $message = "TC No 11 haneli olmalıdır!";
    } else {
        $sql = "UPDATE calisanlar SET tc_no = ?, ad = ?, soyad = ?, departman = ?, maas = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $tc_no, $ad, $soyad, $departman, $maas, $id);
        $stmt->execute();
        $stmt->close();
        $message = "Çalışan başarıyla güncellendi!";
    }
}

// Arama işlemi
$searchQuery = '';
if (isset($_POST['arama'])) {
    $searchQuery = $_POST['arama'];
}

$sql = "SELECT * FROM calisanlar WHERE tc_no LIKE ? OR ad LIKE ? OR soyad LIKE ? OR departman LIKE ? OR maas LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%$searchQuery%";
$stmt->bind_param("sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #3a4f70, #536d88); /* Koyu mavi tonları */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .container {
            width: 80%;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .form-container, .table-container {
            background: #2d3a51; /* Koyu arka plan */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Daha belirgin gölge */
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 30px;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 16px;
        }
        input[type="text"], input[type="number"] {
            background-color: #3c4b60;
            color: white;
        }
        button {
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 10px; /* Butonlar arasına mesafe eklemek için */
        }
        button:hover {
            opacity: 0.8;
        }
        .ekle {
            background-color: #28a745; /* Yeşil */
            color: white;
        }
        .sil {
            background-color: #dc3545; /* Kırmızı */
            color: white;
        }
        .guncelle {
            background-color: #007bff; /* Mavi */
            color: white;
        }
        .geri {
            background-color: #f0ad4e; /* Sarımsı renk */
            color: white;
            margin-top: 10px;
        }
        .geri:hover {
            opacity: 0.8;
        }
        .button-container {
            display: flex; /* Butonları yan yana dizmek için */
            justify-content: flex-start;
        }
        .table-container {
            overflow-x: auto;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }
        .message {
            color: green;
            font-size: 14px;
            text-align: center;
        }
        .search-container {
            margin-bottom: 20px; /* Arama kutusu ile tablonun arasındaki boşluğu azaltalım */
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="search-container">
            <form method="post">
                <input type="text" name="arama" placeholder="Arama..." value="<?php echo $searchQuery; ?>"><br>
                <button type="submit">Ara</button>
            </form>
        </div>

        <form method="post" onsubmit="return validateForm()">
            <input type="hidden" name="id" id="id" value=""> <!-- ID'yi gizli tut -->
            <input type="text" name="tc_no" id="tc_no" placeholder="TC No" required pattern="\d{11}" title="TC No 11 haneli olmalıdır."><br>
            <input type="text" name="ad" id="ad" placeholder="Ad" required><br>
            <input type="text" name="soyad" id="soyad" placeholder="Soyad" required><br>
            <input type="text" name="departman" id="departman" placeholder="Departman" required><br>
            <input type="number" name="maas" id="maas" placeholder="Maaş" required><br>

            <div class="button-container">
                <button type="submit" name="ekle" class="ekle">Çalışan Ekle</button>
                <button type="submit" name="sil" class="sil">Çalışan Sil</button>
                <button type="submit" name="guncelle" class="guncelle">Çalışan Güncelle</button>
            </div>
        </form>
        <?php
        if (isset($message)) {
            echo "<p class='message'>$message</p>";
        }
        ?>

        <!-- Geri git butonunu ekle -->
        <div class="button-container">
        <button type="button" onclick="window.location.href='yonetici_ekrani.php';" class="geri">Geri Git</button>
        </div>
    </div>

    <div class="table-container">
        <h2>Çalışanlar</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TC No</th>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>Departman</th>
                    <th>Maaş</th>
                    <th>Seç</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['tc_no']}</td>";
                    echo "<td>{$row['ad']}</td>";
                    echo "<td>{$row['soyad']}</td>";
                    echo "<td>{$row['departman']}</td>";
                    echo "<td>{$row['maas']}</td>";
                    echo "<td><button type='button' onclick='fillForm({$row['id']}, \"{$row['tc_no']}\", \"{$row['ad']}\", \"{$row['soyad']}\", \"{$row['departman']}\", {$row['maas']})'>Seç</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Formu tablodan seçilen satırla doldur
        function fillForm(id, tc_no, ad, soyad, departman, maas) {
            document.querySelector('#id').value = id;
            document.querySelector('#tc_no').value = tc_no;
            document.querySelector('#ad').value = ad;
            document.querySelector('#soyad').value = soyad;
            document.querySelector('#departman').value = departman;
            document.querySelector('#maas').value = maas;
        }

        // Formu doğrulama fonksiyonu
        function validateForm() {
            const tcNo = document.getElementById('tc_no').value;
            if (tcNo.length !== 11) {
                alert('TC No 11 haneli olmalıdır!');
                return false;
            }
            return true;
        }

        // Geri git fonksiyonu
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
