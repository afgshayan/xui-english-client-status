<?php
$uuid = $_GET['uuid'] ?? '';
if ($uuid === '') {
    die("UUID وارد نشده است.");
}

$dbPath = "/etc/x-ui-english/x-ui-english.db";

function formatBytes($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' گیگابایت';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' مگابایت';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' کیلوبایت';
    return $bytes . ' بایت';
}

try {
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query("SELECT settings FROM inbounds LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $json = $row['settings'];
    $data = json_decode($json, true);

    $clientFound = null;
    foreach ($data['clients'] as $client) {
        if ($client['id'] === $uuid) {
            $clientFound = $client;
            break;
        }
    }

    if (!$clientFound) {
        die("کاربر یافت نشد.");
    }

    $email = htmlspecialchars($clientFound['email']);
    $ipLimit = (int)$clientFound['limitIp'];
    $totalBytes = $clientFound['totalGB'];
    $totalFormatted = formatBytes($totalBytes);

    // تاریخ انقضا
    $expiryTimestamp = $clientFound['expiryTime'];
    $expiryText = "نامحدود";
    $daysLeft = "";
    if (!empty($expiryTimestamp)) {
        $expiryDate = date('Y-m-d', $expiryTimestamp / 1000);
        $now = time();
        $secondsLeft = ($expiryTimestamp / 1000) - $now;
        $daysLeft = floor($secondsLeft / 86400);
        $expiryText = "$expiryDate (" . ($daysLeft >= 0 ? "$daysLeft روز مانده" : abs($daysLeft) . " روز گذشته") . ")";
    }

    // گرفتن ترافیک مصرف‌شده از جدول دیگر با استفاده از email
    $stmt2 = $db->prepare("SELECT up, down FROM client_traffics WHERE email = ? LIMIT 1");
    $stmt2->execute([$clientFound['email']]);
    $trafficRow = $stmt2->fetch(PDO::FETCH_ASSOC);
    $usedBytes = $trafficRow ? ($trafficRow['up'] + $trafficRow['down']) : 0;
    $usedFormatted = formatBytes($usedBytes);
    $remaining = $totalBytes > 0 ? formatBytes($totalBytes - $usedBytes) : "نامحدود";

} catch (Exception $e) {
    die("خطا: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>وضعیت اکانت VPN</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <!-- فونت فارسی -->
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #fc466b, #3f5efb);
      font-family: 'Vazirmatn', sans-serif;
      padding-top: 40px;
      direction: rtl;
    }
    .card {
      max-width: 500px;
      margin: auto;
      border-radius: 20px;
      background: white;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .icon {
      background: linear-gradient(45deg, #feda75, #fa7e1e, #d62976, #962fbf, #4f5bd5);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 22px;
      margin-left: 8px;
    }
    .list-group-item {
      font-size: 17px;
    }
    h3 {
      background: linear-gradient(45deg, #feda75, #fa7e1e, #d62976, #962fbf, #4f5bd5);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: bold;
    }
  </style>
</head>
<body>
<div class="card p-4">
  <h3 class="mb-4 text-center">وضعیت اکانت VPN</h3>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><i class="fas fa-user icon"></i> نام کاربر: <?= $email ?></li>
    <li class="list-group-item"><i class="fas fa-key icon"></i> شناسه UUID: <?= $uuid ?></li>
    <li class="list-group-item"><i class="fas fa-wifi icon"></i> تعداد آی‌پی مجاز: <?= $ipLimit ?></li>
    <li class="list-group-item"><i class="fas fa-calendar-alt icon"></i> تاریخ انقضا: <?= $expiryText ?></li>
    <li class="list-group-item"><i class="fas fa-download icon"></i> ترافیک مصرف‌شده: <?= $usedFormatted ?></li>
    <li class="list-group-item"><i class="fas fa-database icon"></i> حجم کل: <?= $totalFormatted ?></li>
    <li class="list-group-item"><i class="fas fa-battery-half icon"></i> حجم باقی‌مانده: <?= $remaining ?></li>
  </ul>
</div>
</body>
</html>
