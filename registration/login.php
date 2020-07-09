<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

// NOTE CONNECT TO MASTER INCLUDE FILE
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/masterInclude.php";

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Detect device type
function get_operating_system()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $operating_system = 'mobile';

    //Get the operating_system
    if (preg_match('/iphone/i', $u_agent)) {
        $operating_system = 'mobile';
    } elseif (preg_match('/ipod/i', $u_agent)) {
        $operating_system = 'mobile';
    } elseif (preg_match('/android/i', $u_agent)) {
        $operating_system = 'mobile';
    } elseif (preg_match('/blackberry/i', $u_agent)) {
        $operating_system = 'mobile';
    } elseif (preg_match('/webos/i', $u_agent)) {
        $operating_system = 'mobile';
    } else {
        $operating_system = 'desktop';
    }

    return $operating_system;
}

if (!isset($device)) {
    $device = get_operating_system();
}


// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Voer alstublieft een email in. ";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Voer alstublieft een wachtwoord in. ";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, email, company_id, user_role, firstname, password, activate_token FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $company_id, $user_role, $firstname, $hashed_password, $activate_token);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            if (is_null($activate_token)) {
                                session_start();

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["device"] = $device;
                                $_SESSION["user_id"] = $id;
                                $_SESSION["email"] = $email;
                                $_SESSION["firstname"] = $firstname;
                                $_SESSION["user_role"] = $user_role;
                                $_SESSION["company_id"] = $company_id;

                                // Redirect user to welcome page
                                header("location: ../index.php");
                            } else {
                                $email_err = "Dit account is nog niet geactiveerd, ga naar je email om deze te activeren. ";
                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "Het wachtwoord dat u heeft ingetypt klopt niet. ";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $email_err = "Geen account gevonden met deze email. ";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later. ";
            }
        }

        // Close statement
        if ($stmt = mysqli_prepare($link, $sql)) {
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
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/stylesheet-main.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }
    </style>
</head>

<body>
    <!-- this is the orange  -->
    <div class="container-fluid">
        <div class="row color-oranje" style=" box-shadow: 0 0 40px black;">
            <div class="col-2"></div>
            <div class="col-8">
                <h1 class="text-center color-wit"><strong>WELCOME!</strong></h1>
            </div>
            <div class="col-2"></div>
        </div>
        <div class="row color-grijs">
            <div class="col-12 mh-100" style="padding: 10px;"></div>
        </div><br>

        <?php

        if (!empty($email_err) or !empty($password_err)) {
            echo "<div class='alert alert-danger' role='alert'>";
            if (!empty($email_err)) {
                echo $email_err;
            }

            if (!empty($password_err)) {
                echo $password_err;
            }
            echo "</div>";
        }

        ?>
        <br><br>

        <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                    </div>
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
            </div>
            <div class="col-3"></div>
        </div>
        <br><br>
        <div class="row text-center">
            <div class="col-3 col-md-5"></div>
            <div class="col-6 col-md-2">
                <div class="form-group">
                    <input type="submit" class="btn color-oranje btn-block" value="Login">
                </div>
            </div>
            <div class="col-3 col-md-5"></div>
        </div>
        </form>
        <div class="row">
            <div class="col-xs-1 col-md-4"></div>
            <div class="col text-center"><img src="/images/logo.png" alt="GET OUT THE GAME" class="img-fluid"></div>
            <div class="col-xs-1 col-md-4"></div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</html>