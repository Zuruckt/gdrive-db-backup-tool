<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Database Backup Tool</title>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Montserrat', sans-serif;
            }
            
            .content {
                height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            p {
                margin-top: 20px;
                border: 1px solid #000000;
                border-radius: 25px;
                padding: 10px;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <h1>Please copy this code on your terminal.</h1>
            <p>
                <?=$_GET['code']?>
            </p>
        </div>
    </body>
</html>
