<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personel Yönetim Sistemi</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            color: #fff;
        }
        h1 {
            margin-bottom: 30px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        .button-container {
            display: flex;
            gap: 20px;
        }
        .button {
            padding: 15px 30px;
            font-size: 18px;
            color: #fff;
            background: rgba(0, 123, 255, 0.9);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background 0.3s, transform 0.2s;
        }
        .button:hover {
            background: rgba(0, 123, 255, 1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <h1>Personel Yönetim Sistemine Hoşgeldiniz</h1>
    <div class="button-container">
        <a href="yonetici_giris.php" class="button">Yönetici Girişi</a>
        <a href="personel_giris.php" class="button">Personel Girişi</a>
    </div>
</body>
</html>
