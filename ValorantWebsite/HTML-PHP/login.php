<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.html");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: index1.html");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html>
<head>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  	<link rel="shortcut icon" href="https://i.imgur.com/MHNZWUL.png">
  	<title>Login Page</title>
      <link rel="stylesheet" type="text/css" href="css/login_style.css">
	<style>
	  #content,.login,.login-card a,.login-card h1,.login-help{text-align:center}
	  body,html{margin:0;padding:0;width:100%;height:100%;display:table}#content{font-family:'Source Sans Pro',sans-serif;background:url(captiveportal-background.jpg) center center no-repeat fixed;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;display:table-cell;vertical-align:middle;}.login-card{padding:40px;width:274px;background-color:#F7F7F7;margin:0 auto 10px;border-radius:2px;box-shadow:0 2px 2px rgba(0,0,0,.3);overflow:hidden}.login-card h1{font-weight:400;font-size:2.3em;color:#1383c6}.login-card h1 span{color:#f26721}.login-card img{width:70%;height:70%}
	  .login-card input[type=submit]{width:100%;display:block;margin-bottom:10px;position:relative}
	  .login-card input[type=text],input[type=password]{height:44px;font-size:16px;width:100%;margin-bottom:10px;-webkit-appearance:none;background:#fff;border:1px solid #d9d9d9;border-top:1px solid silver;padding:0 8px;box-sizing:border-box;-moz-box-sizing:border-box}
	  .login-card input[type=text]:hover,input[type=password]:hover{border:1px solid #b9b9b9;border-top:1px solid #a0a0a0;-moz-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}
	  .login{font-size:14px;font-family:Arial,sans-serif;font-weight:700;height:36px;padding:0 8px}
	  .login-submit{-webkit-appearance:none;-moz-appearance:none;appearance:none;border:0;color:#fff;text-shadow:0 1px rgba(0,0,0,.1);background-color:#4d90fe}.login-submit:disabled{opacity:.6}.login-submit:hover{border:0;text-shadow:0 1px rgba(0,0,0,.3);background-color:#357ae8; opacity: 1;}.login-card a{text-decoration:none;color:#222;font-weight:400;display:inline-block;opacity:.8;transition:opacity ease .5s}.login-card a:hover{opacity:1;}.login-help{width:100%;font-size:12px}.list{list-style-type:none;padding:0}.list__item{margin:0 0 .7rem;padding:0}label{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;text-align:left;font-size:14px;}input[type=checkbox]{-webkit-box-flex:0;-webkit-flex:none;-ms-flex:none;flex:none;margin-right:10px;float:left}@media screen and (max-width:450px){.login-card{width:70%!important}.login-card img{width:30%;height:30%}}
  	</style>
</head>

<body>
    <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
    ?>
    <div id="content" style="background-image: url('https://cdna.artstation.com/p/assets/images/images/025/669/906/large/dino-rhinosaur-done-wp-pink-with-pic.jpg?1586544608');">
	    <div class="login-card">
		    <img src="https://cdn3.vectorstock.com/i/1000x1000/37/87/valorant-game-logo-icon-eps-10-gaming-streamer-vector-33193787.jpg" style="height: 60px; width: 60px;"/><br><br>
	        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="text" name="username" placeholder="Username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                <input type="password" name="password" placeholder="Password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
		        <div class="login-help">
			        <ul class="list">
				        <li class="list__item">
				        <label class="label--checkbox">
					    <input type="checkbox" class="checkbox" onchange="document.getElementById('login').disabled = !this.checked;">
					    <span>I agree with the <a target="_blank" rel="noopener" href="http://www.termslicences.com/example.pdf">terms & licences</a></span>
				        </label>
				        </li>
			        </ul>
		        </div>
		<input name="redirurl" type="hidden" value="$PORTAL_REDIRURL$">
		<input type="submit" name="accept" class="login login-submit" value="Login" id="login" disabled>
		<div>
		<h4 style="text-align: left;">Don't have an account?</h4>
		<a style="margin-right:-160px;" href="register.php">Sign up</a>
		</div>
		
        <a href="https://www.facebook.com/"><img src="data:image/png;base64,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" style="height: 50px; width: 50px;"></a>
        <a href="https://twitter.com/login?lang=en-gb"><img src="data:image/jpeg;base64,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" style="height: 50px; width: 50px;"></a>
    </form>
	</div>
</div>
</body>
</html>