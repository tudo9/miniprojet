<?php
// changepsw.php
session_start();

// Ensure ONLY an existing admin can change his own a new password
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: changpsw.php");
    exit;
}

require_once __DIR__ . '/../db_connect.php';

// changing password
    $sql = "update admins set ";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
    @import url(https://fonts.googleapis.com/css2?family=Marcellus&family=Outfit:wght@300;400&display=swap);
    @import url(https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap);
        body{
            margin: 0;
            height: fit-content;
            font-family:'Outfit',sans-serif;
            font-weight: 600;
            width: fit-content;
        }
        .form-login{
            display:grid;
            grid-template-columns:repeat(1,1fr);
            padding:35px;
            box-shadow:0px 0px 100px rgba(0,0,0,0.2);
            width: 27%;
            height: 70%;
            position: absolute;
            top: 10%;
            left: 36.5%;
            background-color: rgb(255, 255, 255); 
            border-radius: 15px;
        }
        input[type="text"], input[type="password"]{
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 4px;
            height: 1rem;
        }
        .or{
            font-weight: bold;
            height: fit-content;
            text-align: center;
            display: flex;
            align-items: center;
            margin-top: 30px
        }
        hr{
            width: 47%; 
            height: 1px; 
            background-color: black; 
            border: none;
        }
        .notadmin{
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="login.php" method="POST" id="login">
            <div class="form-login">
                <h1>Change your information!</h1>

                <?php if (!empty($error_message)): ?>
                    <div class="error-msg"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <label for="username">New Username</label>
                <input type="text" id="username" name="username" placeholder="<?php echo(htmlspecialchars($_SESSION['username'])) ?>" required> 
                
                <label for="psw"> New Password</label>
                <input type="password" id="psw" name="psw" placeholder="*******" required>
                
                <input type="submit" name="change" value="change">
                
            </div>
    </form>
</body>
</html>