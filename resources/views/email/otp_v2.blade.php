<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>顯示 OTP</title>
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background-color: #d9d7d2;
        }

        .container {
            text-align: center;
            border-radius: 1em;
            background-color: #fffefa;
            padding: 1em;
        }

        .otp {
            padding: 0.5em;
            border-radius: 1em;
            background-color: #f6f1e7;
        }
    </style>
</head>

<body>
    <div class="container">
        <p>您的 OTP驗證碼為：</p>
        <h1 class="otp">{{ $otp }}</h1>
        <p>驗證碼時效為一分鐘，請在時效內輸入完畢。</p>
    </div>
</body>

</html>
