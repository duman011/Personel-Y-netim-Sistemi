<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo1";

// Veritabanı bağlantısını oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verileri al
$sql = "SELECT * FROM kullanıcı_girisler";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yapanlar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #001f3d, #00bfff);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 70vw;  /* Tabloyu biraz daha küçülttüm */
            height: 40vw; /* 16:9 oranı */
            max-width: 1300px;  /* Maksimum genişlik */
            max-height: 750px;  /* Maksimum yükseklik */
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);  /* Kenarlara gölgelendirme ekledim */
            border-radius: 15px;  /* Kenarları yuvarlattım */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Butonu alt tarafa yerleştirmek için */
        }

        table {
            width: 100%;
            height: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);  /* Tabloya gölgeleme ekledim */
            transition: transform 0.3s ease-in-out;
        }

        table:hover {
            transform: scale(1.02);
        }

        th, td {
            padding: 20px;
            text-align: center;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
        }

        th {
            background-color: #006bb3;
            color: white;
            font-size: 1.3em;
            border-bottom: 3px solid #004080;
        }

        td {
            background-color: #004080;
            border-bottom: 2px solid #003366;
        }

        tr:nth-child(even) td {
            background-color: #003366;
        }

        tr:hover td {
            background-color: #005c99;
        }

        /* Kaydırma çubuğu eklemek için */
        .table-container {
            height: 100%;
            overflow-y: auto; /* Dikey kaydırma ekle */
        }

        .button {
            background-color: #ff4d4d;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            font-size: 1.2em;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            transition: background-color 0.3s, box-shadow 0.3s;
            margin-top: 10px;
            align-self: center;
        }

        .button:hover {
            background-color: #ff1a1a;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.6);
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Giriş Yapan ID</th>
                    <th>Ad</th>
                    <th>Tarih</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["id"] . "</td>
                                <td>" . $row["giris_yapan_id"] . "</td>
                                <td>" . $row["ad"] . "</td>
                                <td>" . $row["tarih"] . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Veri bulunamadı</td></tr>";
                }
                $conn->close();
                ?>
            </table>
        </div>
        <div class="button-container">
            <a href="yonetici_ekrani.php">
                <button class="button">Geri Git</button>
            </a>
        </div>
    </div>
</body>
</html>
