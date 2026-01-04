<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - BaladiPick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .maintenance-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
        }
        .maintenance-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: rotate 2s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        h1 {
            color: #333;
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .brand {
            color: #667eea;
            font-weight: bold;
        }
        .estimated-time {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 30px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">🔧</div>
        <h1>We'll be right back!</h1>
        <p><span class="brand">BaladiPick</span> is currently undergoing scheduled maintenance.</p>
        <p>We're making some improvements to serve you better.</p>
        <div class="estimated-time">
            <strong>⏱️ Estimated Time:</strong> A few minutes
        </div>
        <p style="margin-top: 30px; font-size: 14px; color: #999;">
            Thank you for your patience!
        </p>
    </div>
</body>
</html>
