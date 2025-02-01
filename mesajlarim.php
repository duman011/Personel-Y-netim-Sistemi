<?php 
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$database = "demo1";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Adminler tablosundaki kullanıcı adlarını ve id'leri çek
$adminQuery = "SELECT username, yonetici_id FROM adminler";
$admins = $conn->query($adminQuery);

// Çalışanlar tablosunda giriş yapılan TC numarasını al
$tcQuery = "SELECT tc_no FROM calisanlar WHERE giris_yapildi = 1 LIMIT 1";
$tcResult = $conn->query($tcQuery);

if ($tcResult->num_rows > 0) {
    $tc_no = $tcResult->fetch_assoc()['tc_no'];
} else {
    die("Giriş yapan kullanıcı bulunamadı.");
}

// Seçilen alıcıyı al (Get parametresi varsa, yoksa null olarak ayarlanır)
$selected_alici = isset($_GET['alici']) ? $_GET['alici'] : null;

// POST verileri alındıktan sonra kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen veriler
    $alici_id = $_POST['alici'] ?? null;
    $mesaj = $_POST['mesaj'] ?? null;

    // Alıcı id ve mesaj boş değilse, veri ekle
    if ($alici_id && $mesaj && $tc_no) {
        $stmt = $conn->prepare("INSERT INTO mesajlar (gonderen, alici, msj, tarih) VALUES (?, ?, ?, NOW())");

        if (!$stmt) {
            die("Sorgu hazırlama hatası: " . $conn->error);
        }

        $stmt->bind_param("iis", $tc_no, $alici_id, $mesaj);

        if (!$stmt->execute()) {
            die("Mesaj gönderilirken bir hata oluştu: " . $stmt->error);
        }

        $success = "Mesaj gönderildi.";
        $stmt->close();
    } else {
        $error = "Lütfen tüm alanları doldurun.";
    }
}

// Mesajları al (seçilen alıcıya göre filtrele ve tarihe göre sıralanmış şekilde getir)
$messagesQuery = "SELECT * FROM mesajlar WHERE (gonderen = ? OR alici = ?) AND (gonderen = ? OR alici = ?) ORDER BY tarih DESC";  // En yeni mesajlar önce
$stmt = $conn->prepare($messagesQuery);
$stmt->bind_param("iiii", $tc_no, $tc_no, $selected_alici, $selected_alici);
$stmt->execute();
$messagesResult = $stmt->get_result();

// Alıcı adını almak
if ($selected_alici) {
    $aliciQuery = "SELECT username FROM adminler WHERE yonetici_id = ?";
    $aliciStmt = $conn->prepare($aliciQuery);
    $aliciStmt->bind_param("i", $selected_alici);
    $aliciStmt->execute();
    $aliciResult = $aliciStmt->get_result();
    $aliciName = $aliciResult->fetch_assoc()['username'] ?? 'Seçilen Alıcı';
    $aliciStmt->close();
} else {
    $aliciName = 'Seçilen Alıcı';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesajlaşma Sistemi</title>
    <style>
                              
  body {
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(135deg, #a0c4ff, #b7c6d4, #e1e8f0);  /* Mavi-grimsi tonlarda yumuşak geçiş */
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: linear-gradient(135deg, #e4f0f8, #d3e3f1);  /* Form arka planı, sayfa ile uyumlu mavi ton geçişi */
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

h1 {
    font-size: 32px;
    text-align: center;
    color: #333;
    margin-bottom: 25px;
}

.alici-name {
    text-align: center;
    font-size: 24px;
    margin-bottom: 25px;
    font-weight: bold;
    color: #4A90E2;
}

.messages {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 35px;
    border-bottom: 2px solid #ddd;
    padding-bottom: 20px;
}

.message {
    padding: 14px;
    border-radius: 15px;
    margin-bottom: 15px;
    font-size: 16px;
    line-height: 1.6;
    word-wrap: break-word;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sent {
    background-color: #d1ffd6;
    text-align: right;
    margin-left: auto;
    max-width: 70%;
}

.received {
    background-color: #f9f9f9;
    text-align: left;
    margin-right: auto;
    max-width: 70%;
}

.timestamp {
    font-size: 13px;
    color: #777;
}

.status-ok {
    color: green;
    font-size: 18px;
    margin-left: 10px;
}

.form-section {
    margin-top: 35px;
}

label {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
    display: block;
}

select, textarea, button {
    width: 100%;
    padding: 14px;
    margin: 10px 0;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 16px;
    transition: all 0.3s ease-in-out;
}

select:focus, textarea:focus, button:focus {
    outline: none;
    border-color: #4A90E2;  /* Mavi renkte odaklama */
    box-shadow: 0 0 10px rgba(74, 144, 226, 0.6);  /* Hafif mavi gölge */
}

select {
    background-color: #f7faff;
    background-image: linear-gradient(to top, #ffffff, #f7faff);
    border: 1px solid #d3d3d3;
}

textarea {
    background-color: #f7faff;
    background-image: linear-gradient(to top, #ffffff, #f7faff);
    border: 1px solid #d3d3d3;
    resize: none;
}

button {
    background-color: #4CAF50;
    color: white;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.2s ease-in-out;
}

button:hover {
    background-color: #45a049;
    transform: scale(1.05);  /* Hafif büyütme efekti */
}

button:active {
    transform: scale(1);  /* Tıklama sırasında normal boyutta tut */
}

.back-button {
    display: inline-block;
    background-color: #007BFF;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 8px;
    font-size: 16px;
    margin-top: 25px;
    text-align: center;
    transition: background-color 0.3s ease, transform 0.2s ease-in-out;
}

.back-button:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.back-button:active {
    transform: scale(1);
}




    </style>
</head>
<body>

<div class="container">
    <h1>Mesajlaşma Sistemi</h1>

    <!-- Alıcı adı burada görünür -->
    <div class="alici-name">
        <?= htmlspecialchars($aliciName) ?>
    </div>

    <?php if (isset($error)): ?>
        <div class="message received">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="message sent">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="messages" id="messagesContainer">
        <?php while ($message = $messagesResult->fetch_assoc()): ?>
            <div class="message <?= ($message['gonderen'] == $tc_no) ? 'sent' : 'received' ?>">
                <p><?= htmlspecialchars($message['msj']) ?></p>
                <div class="timestamp">
                    <?= date('d M Y H:i', strtotime($message['tarih'])) ?>
                    <?php if ($message['gonderen'] == $tc_no && $message['alici'] == $selected_alici): ?>
                        <span class="status-ok">✓</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Güncelleme Butonu -->
    <button onclick="updateAndSelectAlıcı()">Güncelle</button>

    <div class="form-section">
        <form method="GET" action="">
            <label for="alici">Alıcı Seç:</label>
            <select name="alici" id="alici" onchange="this.form.submit()" required>
                <option value="">-- Alıcıyı Seçin --</option>
                <?php while ($admin = $admins->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($admin['yonetici_id']) ?>" 
                            <?= ($selected_alici == $admin['yonetici_id']) ? 'selected' : '' ?> >
                        <?= htmlspecialchars($admin['username']) ?> (<?= htmlspecialchars($admin['yonetici_id']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <form method="POST" action="">
            <input type="hidden" name="alici" value="<?= htmlspecialchars($selected_alici) ?>">
            <label for="mesaj">Mesajınız:</label>
            <textarea name="mesaj" id="mesaj" rows="4" placeholder="Mesajınızı buraya yazın..." required></textarea>

            <button type="submit">Gönder</button>
        </form>
    </div>

    <a href="personel_ekrani.php" class="back-button">Geri Git</a>

</div>

<script>
// Güncelleme ve alıcı seçimini otomatik yapma fonksiyonu
function updateAndSelectAlıcı() {
    var currentAlıcı = "<?= htmlspecialchars($selected_alici) ?>"; // PHP'den gelen alıcı id'si
    var selectElement = document.getElementById("alici");
    
    // Alıcıyı sıfırlıyoruz
    selectElement.value = "";

    // Sayfa yenilendiğinde tekrar aynı alıcıyı seçiyoruz
    setTimeout(function() {
        selectElement.value = currentAlıcı;
        selectElement.form.submit();
    }, 100); // Küçük bir gecikme ile tekrar seçme işlemi
}
</script>

</body>
</html>
