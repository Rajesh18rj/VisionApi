<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;700&display=swap" rel="stylesheet">

    <title>Welcome Email</title>
    <style>
        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px 0;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #F3F2FF;
            padding: 20px;
            text-align: center;
        }

        .header img {
            height: 40px;
        }

        .content {
            padding: 30px 20px;
            text-align: center;
        }

        .content h2 {
            color: #333333;
            margin-bottom: 10px;
        }

        .content p {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background-color: #ee495e;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://tapitbizcard.com/wp-content/uploads/2025/03/logo-1.png" alt="Company Logo">
        </div>
        <div class="content">
            <h2>Welcome to TapitBizCard!</h2>
            <p>We're excited to have you on board. Your profile has been successfully created.</p>
            <div class="text-center">
                <img height="150px" width="150px" src="https://tapitbizcard.codexskill.com/storage/profile_logo1.png">
            </div>
            <p style="font-size: larger;">Your Card Pin -  <span style="color: black;font-weight: bold;">{{$pin}}</span></p>
            <p>Start exploring your dashboard and get the most out of your new account.</p>
            <a href="https://tapitbizcard.app/Admin/" class="btn">Go to Dashboard</a>

        </div>
        <div class="footer">
            If you have any questions, feel free to <a href="https://tapitbizcard.app/" style="color: #111111;">contact
                us</a>.<br>
            &copy; 2025 TapitBizCard. All rights reserved.
        </div>
    </div>
</body>

</html>
