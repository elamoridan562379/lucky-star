<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lucky Star Coffee Shop</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <style>
        :root{
            --espresso:#2b160e;
            --roast:#3a1f14;
            --mahogany:#5a2f1d;
            --caramel:#c8813a;
            --gold:#d4a847;
            --cream:#f5ead8;
            --paper:#ffffff;
            --border: rgba(74,37,24,0.18);
        }
        body{
            font-family:'Lato',sans-serif;
            margin:0;
        }
        .bg{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:40px 16px;
            background:
                radial-gradient(circle at 15% 25%, rgba(255,255,255,.08) 0 22px, transparent 24px),
                radial-gradient(circle at 80% 35%, rgba(255,255,255,.06) 0 26px, transparent 28px),
                radial-gradient(circle at 30% 80%, rgba(255,255,255,.05) 0 28px, transparent 30px),
                linear-gradient(135deg, #2a150d 0%, #5a2f1d 50%, #2a150d 100%);
        }
        .card{
            width:100%;
            max-width:520px;
            background:var(--paper);
            border-radius:22px;
            box-shadow:0 30px 80px rgba(0,0,0,.35);
            overflow:hidden;
            padding:36px 40px 28px;
            animation: fadeInUp 0.8s ease-out;
            transition: transform 0.3s ease;
        }
        .logo{
            width:72px;height:72px;
            border-radius:999px;
            margin:0 auto 18px;
            display:flex;align-items:center;justify-content:center;
            background: radial-gradient(circle at 30% 30%, #e8b57b 0%, var(--caramel) 45%, #a55a1f 100%);
            box-shadow:0 14px 30px rgba(200,129,58,.35);
            color:#fff;font-size:30px;
            animation: bounceIn 1s ease-out 0.3s both;
            transition: transform 0.3s ease;
        }
        .logo:hover{
            transform: scale(1.05) rotate(5deg);
        }
        .title{
            text-align:center;
            font-family:'Playfair Display',serif;
            font-size:34px;
            font-weight:700;
            color:#7a3f1d;
            margin:0;
        }
        .subtitle{
            text-align:center;
            margin:8px 0 22px;
            color:rgba(74,37,24,.75);
            font-size:14px;
            letter-spacing:.04em;
        }
        .demo{
            margin-top:18px;
            background:#eaf6ff;
            border:1px solid rgba(0,120,255,.18);
            border-radius:12px;
            padding:12px 14px;
            font-size:13px;
            color:#1f3c5a;
        }
        .demo b{ color:#0d2d4a; }
        a.link{ color:#b86a2b; font-weight:700; text-decoration:none; transition: all 0.3s ease; }
        a.link:hover{ text-decoration:underline; color:#d4a847; }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .error-message {
            background: rgba(234, 56, 56, 0.1);
            border: 1px solid rgba(234, 56, 56, 0.3);
            border-radius: 8px;
            padding: 8px 12px;
            color: #d32f2f;
            font-size: 13px;
            margin-top: 4px;
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>

    <div class="bg">
        <div class="card">
            <div class="logo">☕</div>

            <h1 class="title">Lucky Star Coffee Shop</h1>
            <div class="subtitle">POS &amp; Inventory Management System</div>

            {{ $slot }}

            <div class="demo">
                <div style="font-weight:800; margin-bottom:6px;">Demo Credentials:</div>
                <div><b>Owner:</b> admin@luckystar.com / admin123</div>
                <div><b>Cashier:</b> cashier@luckystar.com / cashier123</div>
                <div><b>Manager:</b> manager@luckystar.com / manager123</div>
            </div>
        </div>
    </div>
</body>
</html>
