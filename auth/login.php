
<?php
// Login page for admin authentication
// This file handles admin login, validates credentials against database,
// and manages session creation for authenticated users.

session_start();

// Database connection parameters
$server = "localhost";
$user = "root";
$pass = "";
$name = "animal_adoption";

// Variable to hold error messages for display
$error_message = "";

// Establish database connection
try {
    $conn = mysqli_connect($server, $user, $pass, $name);
} catch (mysqli_sql_exception $e) {
    die("Connection failed: " . $e->getMessage()); 
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["psw"] ?? '';

    // Validate input fields are not empty
    if (!empty($username) && !empty($password)) {
        
        // Prepare and execute query to fetch admin data
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result(); 

        if ($result->num_rows === 1) {
            $admins = $result->fetch_assoc();
            
            // Verify password using password_verify function
            if (password_verify($password, $admins["password"])) {
                // Store admin information in session variables
                $_SESSION["admin_id"] = $admins["id"];
                $_SESSION["username"] = $admins["username"];
                
                // Redirect to home page after successful login
                header("Location: ../home/home.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "Invalid username.";
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <form action="login.php" method="POST" id="login">
        <div class="form-login">
            <h1>Welcome Back!</h1>
            <p>Login to your account to access your dashboard</p>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required> 
            
            <label for="psw">Password</label>
            <input type="password" id="psw" name="psw" required>
            
            <input type="submit" name="login" value="Login">
            <div class="or"> <hr> or <hr> </div>    
            <p class="notadmin">Are you a guest? <a href="http://localhost:8080/miniprojer1/home/home.php" >continue without signup</a></p>
        </div>
    </form>
    
    <div class="paragraph">
        <h1 style="white-space: nowrap;">Find your New Best Friend,<br>and Adopt a Pet! <i class="fa-solid fa-paw" style="color: white; font-size: 20rem;"></i></h1>
        <p>long paragraph text goes here...</p>
    </div>
</body>
</html>