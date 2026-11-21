<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Nunito Sans Font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;400&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <title>Frogot Password mail Template</title>
    <style>
        body {
            font-family: 'Nunito Sans', sans-serif;
            padding: 25px 0;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="max-width: 600px; min-width: 250px;margin: auto; background-color: #eceef0;border-radius: 5px;">
        <div class="text-center" style="background-color: #F3F2FF;border-top-right-radius: 5px;border-top-left-radius: 5px;padding: 16px">
            <img height="40" width="auto" src="https://tapitbizcard.com/wp-content/uploads/2025/03/logo-1.png" </div>
            <!-- <div class="text-center" style="margin-bottom: 16px;"> -->
            <!-- <img height="200" src="http://185.151.31.3:7860/assets/images/mail_authentication.png"> -->
            <!-- </div> -->
            <div style="padding: 16px;">
                <h4 class="text-center">Forgot Your Password?</h4>
                <p class="text-center" style="color: #6c757d;">Resetting your password is easy. Just use the password below and do change password.</p>
                </p>
                <div class="text-center">
                    <a
            style="background-color: #ee495e;color: #ffffff; border: none;border-radius: 5px;padding: 7px 16px;"
            href="{{$domain_name}}"
          >Reset Password</a>
                    <!-- <button style="background-color: #ee495e;color: #ffffff; border: none;border-radius: 5px;padding: 7px 16px;" href={{$domain_name}}>Reset Password</button> -->
                </div>
                <div class="text-center" style="font-size: 20px; font-weight: bold;margin: 20px 0;">Password : <span style="color: #ee495e;">{{$new_password}}</span></div>
                <div class="text-center" style="margin-bottom: 16px;">
                    <img height="75" width="auto" src="https://shivatemplemanchester.co.uk/Admin/assets/images/mail_confidential_letter.png">
                </div>
                <h5 class="text-center">Problem or questions?</h5>
                <p style="margin: 0;">You received this email because you requested a password reset. If you did not, <a href="https://tapitbizcard.app/" target=" _blank" style="color: #111111; font-weight: 700;">please contact us.</a>.</p>
                <!-- <p class="text-center" style="color: #6c757d;"><small>&copy; 134 Hertford Rd, Enfield, EN3 5AX, London.</small></p> -->
            </div>
        </div>
</body>

</html>
