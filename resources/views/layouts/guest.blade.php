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
        *{
            box-sizing:border-box;
        }
        body{
            font-family:'Lato',sans-serif;
            margin:0;
            overflow-x:hidden;
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
            position:relative;
        }
        .bg::before{
            content:'';
            position:absolute;
            top:0;
            left:0;
            right:0;
            bottom:0;
            background:
                radial-gradient(circle at 20% 20%, rgba(200,129,58,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(212,168,71,0.08) 0%, transparent 50%);
            animation:float 20s ease-in-out infinite;
        }
        @keyframes float{
            0%,100%{ transform:translateY(0) rotate(0deg); }
            50%{ transform:translateY(-20px) rotate(1deg); }
        }
        .card{
            width:100%;
            max-width:480px;
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(20px);
            border-radius:24px;
            box-shadow:
                0 30px 80px rgba(0,0,0,.35),
                0 0 0 1px rgba(255,255,255,0.1) inset;
            overflow:hidden;
            padding:32px 36px 24px;
            position:relative;
            z-index:1;
            animation:slideUp 0.6s ease-out;
        }
        @keyframes slideUp{
            from{ 
                opacity:0; 
                transform:translateY(30px); 
            }
            to{ 
                opacity:1; 
                transform:translateY(0); 
            }
        }
        .logo{
            width:80px;height:80px;
            border-radius:50%;
            margin:0 auto 20px;
            display:flex;align-items:center;justify-content:center;
            background: radial-gradient(circle at 30% 30%, #e8b57b 0%, var(--caramel) 45%, #a55a1f 100%);
            box-shadow:
                0 14px 30px rgba(200,129,58,.35),
                0 0 0 1px rgba(255,255,255,0.2) inset;
            color:#fff;
            font-size:32px;
            position:relative;
            animation:pulse 3s ease-in-out infinite;
        }
        @keyframes pulse{
            0%,100%{ transform:scale(1); }
            50%{ transform:scale(1.05); }
        }
        .logo::before{
            content:'';
            position:absolute;
            top:-2px;
            left:-2px;
            right:-2px;
            bottom:-2px;
            background:linear-gradient(45deg, var(--gold), var(--caramel), var(--gold));
            border-radius:50%;
            z-index:-1;
            animation:rotate 3s linear infinite;
        }
        @keyframes rotate{
            from{ transform:rotate(0deg); }
            to{ transform:rotate(360deg); }
        }
        .title{
            text-align:center;
            font-family:'Playfair Display',serif;
            font-size:32px;
            font-weight:700;
            background:linear-gradient(135deg, var(--mahogany), var(--caramel));
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            background-clip:text;
            margin:0;
            position:relative;
        }
        .subtitle{
            text-align:center;
            margin:8px 0 24px;
            color:rgba(74,37,24,.75);
            font-size:14px;
            letter-spacing:.04em;
            font-weight:400;
        }


        a.link{ 
            color:var(--caramel); 
            font-weight:700; 
            text-decoration:none;
            transition:all 0.3s ease;
            position:relative;
        }
        a.link:hover{ 
            text-decoration:none;
            color:var(--mahogany);
        }
        a.link::after{
            content:'';
            position:absolute;
            bottom:-2px;
            left:0;
            width:0;
            height:2px;
            background:var(--caramel);
            transition:width 0.3s ease;
        }
        a.link:hover::after{
            width:100%;
        }
        
        /* Responsive Design */
        @media (max-width: 480px){
            .card{
                padding:24px 28px 20px;
                margin:16px;
            }
            .title{
                font-size:28px;
            }
            .logo{
                width:70px;
                height:70px;
                font-size:28px;
            }
        }
        
        /* Enhanced Form Styles */
        .form-group{
            margin-bottom:20px;
        }
        .form-label{
            display:block;
            font-weight:700;
            font-size:14px;
            margin-bottom:8px;
            color:var(--roast);
        }
        .form-input{
            width:100%;
            padding:14px 16px;
            border-radius:12px;
            border:2px solid var(--border);
            outline:none;
            font-size:15px;
            transition:all 0.3s ease;
            background:rgba(255,255,255,0.8);
        }
        .form-input:focus{
            border-color:var(--caramel);
            box-shadow:0 0 0 3px rgba(200,129,58,0.1);
            transform:translateY(-1px);
        }
        .form-input:hover{
            border-color:rgba(200,129,58,0.3);
        }
        
        /* Password Toggle */
        .password-wrapper{
            position:relative;
        }
        .password-toggle{
            position:absolute;
            right:16px;
            top:50%;
            transform:translateY(-50%);
            background:none;
            border:none;
            color:var(--mahogany);
            cursor:pointer;
            padding:4px;
            border-radius:4px;
            transition:all 0.2s ease;
        }
        .password-toggle:hover{
            background:rgba(200,129,58,0.1);
        }
        
        /* Checkbox Enhancement */
        .checkbox-wrapper{
            display:flex;
            align-items:center;
            gap:10px;
            color:var(--roast);
            font-size:14px;
        }
        .checkbox-wrapper input[type="checkbox"]{
            width:18px;
            height:18px;
            accent-color:var(--caramel);
            cursor:pointer;
        }
        
        /* Button Enhancement */
        .btn-primary{
            width:100%;
            padding:14px 16px;
            border:none;
            border-radius:14px;
            background:linear-gradient(135deg, var(--mahogany) 0%, var(--caramel) 60%, var(--gold) 100%);
            color:#fff;
            font-weight:700;
            font-size:16px;
            cursor:pointer;
            box-shadow:0 14px 30px rgba(200,129,58,.35);
            transition:all 0.3s ease;
            position:relative;
            overflow:hidden;
        }
        .btn-primary:hover{
            transform:translateY(-2px);
            box-shadow:0 18px 40px rgba(200,129,58,.45);
        }
        .btn-primary:active{
            transform:translateY(0);
        }
        .btn-primary::before{
            content:'';
            position:absolute;
            top:0;
            left:-100%;
            width:100%;
            height:100%;
            background:linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition:left 0.5s ease;
        }
        .btn-primary:hover::before{
            left:100%;
        }
        

        
        /* Loading State */
        .loading{
            pointer-events:none;
            opacity:0.7;
        }
        .loading::after{
            content:'';
            position:absolute;
            top:50%;
            left:50%;
            width:20px;
            height:20px;
            margin:-10px 0 0 -10px;
            border:2px solid transparent;
            border-top:2px solid #fff;
            border-radius:50%;
            animation:spin 1s linear infinite;
        }
        @keyframes spin{
            to{ transform:rotate(360deg); }
        }
    </style>

    <div class="bg">
        <div class="card">
            <div class="logo">☕</div>

            <h1 class="title">Lucky Star Coffee Shop</h1>
            <div class="subtitle">POS &amp; Inventory Management System</div>

            {{ $slot }}


        </div>
    </div>
</body>
</html>
