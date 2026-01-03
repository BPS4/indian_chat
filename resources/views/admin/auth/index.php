<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyIndiaBusiness.com â€“ Coming Soon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MyIndiaBusiness.com is launching soon. Stay tuned for something amazing.">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        padding: 20px;
    }

    .container {
        max-width: 600px;
        width: 100%;
        padding: 40px 30px;
    }

    h1 {
        font-size: 42px;
        margin-bottom: 15px;
        letter-spacing: 1px;
        word-break: break-word;
    }

    p {
        font-size: 18px;
        opacity: 0.9;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .badge {
        display: inline-block;
        padding: 10px 22px;
        border-radius: 30px;
        background: rgba(255,255,255,0.15);
        font-size: 14px;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    footer {
        margin-top: 40px;
        font-size: 14px;
        opacity: 0.7;
    }

    /* ðŸ“± Mobile Devices */
    @media (max-width: 768px) {
        h1 {
            font-size: 32px;
        }

        p {
            font-size: 16px;
        }

        .container {
            padding: 30px 20px;
        }
    }

    /* ðŸ“± Small Mobile Devices */
    @media (max-width: 480px) {
        h1 {
            font-size: 26px;
        }

        p {
            font-size: 15px;
        }

        .badge {
            font-size: 12px;
            padding: 8px 18px;
        }

        footer {
            font-size: 12px;
        }
    }
</style>

</head>
<body>

    <div class="container">
        <div class="badge">Coming Soon</div>

        <h1>MyIndiaBusiness.com</h1>

        <p>
            We are working to launch our new platform.<br>
            Stay tuned for something amazing!
        </p>

        <footer>
            Â© <?php echo date('Y'); ?> MyIndiaBusiness.com. All rights reserved.
        </footer>
    </div>

</body>
</html>
