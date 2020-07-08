<?php
// Include config file
require_once "../db_config.php";
 
// Define variables and initialize with empty values
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = $global_err = "";
$points = 10;


// NOTE GENERATES A RANDOM STRING FOR THE ACTIVATION_TOKEN AND QR_TOKEN
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (!isset($_GET["company_token"])) {
    header("location: ./login");
}

// NOTE USER NEEDS COMPANY TOKEN TO SIGNUP
if(isset($_GET["company_token"])){
    $company_token = preg_replace("/([^a-zA-Z0-9]+)/","", mysqli_real_escape_string($link, $_GET['company_token']));
    $company_data = mysqli_query($link, "SELECT company_name, company_id FROM company WHERE company_token = '$company_token'");
    
    if (mysqli_num_rows($company_data) > 0) {
    while($row = mysqli_fetch_assoc($company_data)) { // NOTE TURNS QUERY RESULTS INTO VARIABLE
      $company_name = $row['company_name'];
      $company_id = $row['company_id'];
    }
  } 
else {
        $global_err = "Sorry, deze token is niet valid, ga naar je teamleider en/of de MediaINN om een code te krijgen. ";
    } 
}




// Processing form data when form is submitted
if (isset($_POST['signup'])) {

    // Validate username
    if(empty(trim($_POST["email"]))){
        $email_err = "Voer alstublieft een email in.";
    } else{
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);

            $param_email = filter_var($param_email, FILTER_SANITIZE_EMAIL);
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Deze email is al in gebruik.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oeps! iets ging fout, probeer het later opnieuw.";
            }
        }
         
        // Close statement
        if($stmt = mysqli_prepare($link, $sql)){
            // [...]
        
            mysqli_stmt_close($stmt);
        } else {
            echo "Something's wrong with the query: " . mysqli_error($link);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Voer alstublieft een wachtwoord in.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Het wachtwoord moet minimaal uit 6 karakters bestaan.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Bevestig uw wachtwoord";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Wachtwoord was geen match.";
        }
    }

    if(trim($_POST["firstname"])){
        $firstname = trim($_POST["firstname"]);
    }

    if(trim($_POST["lastname"])){
        $lastname = trim($_POST["lastname"]);
    }
    
    
    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($global_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (email, company_id, firstname, lastname, points, qr_token, password, activate_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $qr_token = str_shuffle(str_replace(".", "", uniqid(generateRandomString(50),true)));
        $activate_token = str_shuffle(str_replace(".", "", uniqid(generateRandomString(50),true)));
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssss", $email, $company_id, $firstname, $lastname, $points, $qr_token, $param_password, $activate_token);
            
            // Set parameters
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $subject = "Confirmation email";
                $headers = "From: noreply@GetINNtheGame.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $message = "<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><meta name='viewport' content='width=device-width'><style type='text/css'>@media (min-width: 500px){.avatar__media .media__fluid{margin-top:3px !important}}@media (min-width: 500px){.button,.button__shadow{font-size:16px !important;display:inline-block !important;width:auto !important}}@media (min-width: 500px){footer li{display:inline-block !important;margin-right:20px !important}}@media (min-width: 500px){.mt1--lg{margin-top:10px !important}}@media (min-width: 500px){.mt2--lg{margin-top:20px !important}}@media (min-width: 500px){.mt3--lg{margin-top:30px !important}}@media (min-width: 500px){.mt4--lg{margin-top:40px !important}}@media (min-width: 500px){.mb1--lg{margin-bottom:10px !important}}@media (min-width: 500px){.mb2--lg{margin-bottom:20px !important}}@media (min-width: 500px){.mb3--lg{margin-bottom:30px !important}}@media (min-width: 500px){.mb4--lg{margin-bottom:40px !important}}@media (min-width: 500px){.pt1--lg{padding-top:10px !important}}@media (min-width: 500px){.pt2--lg{padding-top:20px !important}}@media (min-width: 500px){.pt3--lg{padding-top:30px !important}}@media (min-width: 500px){.pt4--lg{padding-top:40px !important}}@media (min-width: 500px){.pb1--lg{padding-bottom:10px !important}}@media (min-width: 500px){.pb2--lg{padding-bottom:20px !important}}@media (min-width: 500px){.pb3--lg{padding-bottom:30px !important}}@media (min-width: 500px){.pb4--lg{padding-bottom:40px !important}}@media (min-width: 500px){pre{font-size:14px !important}.body{font-size:14px !important;line-height:24px !important}h1{font-size:22px !important}h2{font-size:16px !important}small{font-size:12px !important}}@media (min-width: 500px){.user-content pre, .user-content code{font-size:14px !important;line-height:24px !important}.user-content ul, .user-content ol, .user-content pre{margin-top:12px !important;margin-bottom:12px !important}.user-content hr{margin:12px 0 !important}.user-content h1{font-size:22px !important}.user-content h2{font-size:16px !important}.user-content h3{font-size:14px !important}}</style></head><body class='body' style='font-family: -apple-system, BlinkMacSystemFont, Roboto, Ubuntu, Helvetica, sans-serif; line-height: initial; max-width: 580px;'> <header class='mt2 mb2' style='margin-bottom: 20px; margin-top: 20px;'></header><h1 style='box-sizing: border-box; font-size: 1.25rem; margin: 0; margin-bottom: 0.5em; padding: 0;'>Thanks for joining GetINNtheGame!</h1><p style='box-sizing: border-box; margin: 0; margin-bottom: 0.5em; padding: 0;'>Please confirm that your email address is correct to continue. Click the link below to get started.</p><p class='mt2 mb2 mt3--lg mb3--lg' style='box-sizing: border-box; margin: 0; margin-bottom: 20px; margin-top: 20px; padding: 0;'> <span class='button__shadow' style='border-bottom: 2px solid rgba(0,0,0,0.1); border-radius: 4px; box-sizing: border-box; display: block; width: 100%;'> <a class='button' href='www.getinnthegame.com/registration/activate.php?token=$activate_token' style='background: #204dd5; border: 1px solid #000; border-radius: 3px; box-sizing: border-box; color: white; display: block; font-size: 1rem; font-weight: 600; padding: 12px 20px; text-align: center; text-decoration: none; width: 100%;' target='_blank'> Confirm Email Address </a> </span></p><p style='box-sizing: border-box; margin: 0; margin-bottom: 0.5em; padding: 0;'>OR copy/paste this link: www.getinnthegame.com/registration/activate.php?token=$activate_token</p><footer class='mt2 mt4--lg' style='border-top: 1px solid #D9D9D9; margin-top: 20px; padding: 20px 0;'></ul> </footer></body>";
               
                // NOTE SEND A VERIFICATION EMAIL TO USER
                mail($param_email,$subject,$message,$headers);

                header("location: ./login.php");
            } else{
                echo "Oeps! iets ging fout, probeer het later opnieuw.";
            }
        }
         
        // Close statement
        if($stmt = mysqli_prepare($link, $sql)){
            // [...]
        
            mysqli_stmt_close($stmt);
        } else {
            echo "Something's wrong with the query: " . mysqli_error($link);
        }
    }
    
    // Close connection
    mysqli_close($link);
}


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/stylesheet-main.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <!-- this is the orange  -->
    <div class="container-fluid">
        <div class="row color-oranje" style=" box-shadow: 0 0 40px black;">
            <div class="col-2"></div>
            <div class="col-8"><h1 class="text-center color-wit"><strong>WELCOME!</strong></h1></div>
            <div class="col-2"></div>
        </div>
        <div class="row color-grijs">
            <div class="col-12 mh-100" style="padding: 10px;"></div>
        </div><br>
        
        <?php 

if (!empty($email_err) OR !empty($password_err) OR !empty($confirm_password_err) OR !empty($global_err)) {
    echo "<div class='alert alert-danger' role='alert'>";
     if (!empty($email_err)) {
         echo $email_err;
     } 
     
     if (!empty($password_err)) {
         echo $password_err;
     }
   
     if (!empty($confirm_password_err)) {
       echo $confirm_password_err;
   }

   if (!empty($global_err)) {
    echo $global_err;
}
   echo "</div>";
   }
?>
        
        <br><br>

    <div class="row">
       <div class="col-3"></div>
         <div class="col-6">
             <form method="post">
              <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                  <label>Email</label>
                  <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
              </div>    
              <div class="form-group">
                  <label>Voornaam</label>
                  <input type="text" name="firstname" class="form-control" value="<?php echo $firstname; ?>">
              </div>   
              <div class="form-group">
                  <label>Achternaam</label>
                  <input type="text" name="lastname" class="form-control" value="<?php echo $lastname; ?>">
              </div>
              <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                  <label>Wachtwoord</label>
                  <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
              </div>
              <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                  <label>Bevestig wachtwoord</label>
                  <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
              </div>
              <div class="form-group">
                  <label>Bedrijf</label>
                  <input type="text" name="company" class="form-control" value="<?php echo $company_name; ?>" disabled>
              </div>  
           </div>
              <div class="col-3"></div>
         </div>

         <div class="row text-center">
            <div class="col-5 col-md-5"></div>
            <div class="col-4 col-md-2">
                <div class="form-group">
                <input type="submit" class="btn color-oranje btn-block" value="Sign Up" name="signup"></form>
              </div>
            </div>
            <div class="col-3 col-md-5"></div>
        </div>

            <div class="row text-center">
            <div class="col"><p>Do you have an account? <a href="login.php">Login here</a>.</p></div>
            </div>

        <div class="row">
            <div class="col-xs-1 col-md-4"></div>
            <div class="col text-center"><img src="/images/logo.png" alt="GET OUT THE GAME" class="img-fluid"></div>
            <div class="col-xs-1 col-md-4"></div>
        </div> 
    </div>  <!-- NOTE END OF CONTAINER -->
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</html>