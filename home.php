<?php
$server="localhost";
$user="root";
$pass="";
$name= "animal_adoption";
try {
$conn = mysqli_connect($server,$user,$pass,$name);
} catch (mysqli_sql_exception) {
    echo "connection failed: ";
}
// total animals
$totalAnimalsQuery = mysqli_query($conn,"SELECT COUNT(*) as total FROM animals");
$totalAnimals = mysqli_fetch_assoc($totalAnimalsQuery)['total'];
// total adopted
$total_adoptedquery = mysqli_query($conn,"SELECT COUNT(*) as total FROM adoptions");
$total_adopted = mysqli_fetch_assoc($total_adoptedquery)["total"];
// total available
$availableAnimalsQuery = mysqli_query($conn,"SELECT COUNT(*) as total FROM animals WHERE health_status = 'healthy'");
$available = mysqli_fetch_assoc($availableAnimalsQuery)["total"];
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Adoption</title>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Outfit:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background-color: #ffffff;
            overflow-x: hidden;
        }

        /*header*/
        header {
            background-color: #137657; 
            padding: 25px 50px 10px 50px;
        }
        .navbar{
            display:flex;
            justify-content:space-between;
            align-items:start;
            margin-bottom:25px;
            height: 25px;
        }
        .nav-links{
            margin-right:150px;
            margin-top: -0.4%;
            list-style:none;
            display:flex;
            gap: 8px;
        }
        .nav-links li a{
            border-radius: 4px;
            padding: 10px 5px 10px 5px;
            text-decoration:none;
            color: #b5d5ca;
            font-size:17px;
            font-weight:400;
            transition:color 0.3s ease;
        }
        .nav-links li a:hover{
            color:#ffffff;
            background-color: #ffffff4f;
        }
        .logo{
            margin-top: -1%;
            width: auto;
            color: #ffffff;
            font-family: 'Marcellus', serif;
            font-size: 26px;
            letter-spacing: 1px;
            margin-left: 5%;
        }
        .photo{
            margin-bottom: -2.5px;
            margin-right: -8px;
            width: 28px;
            height: 28px;
        }

        /* احصائيات */
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 50px;
            color: #b5d5ca;
            font-size: 14px;
        }

        .stats-bar div {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stats-bar i {
            font-size: 12px;
        }

        /*  */
        .main {
            position: relative;
            height: 65vh;
            min-height: 500px;
            width: 100%;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* leg logo */
        .paw-graphic {
            position: absolute;
            top: 28%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }
        .paw-graphic:hover {
            transform: translateX(-50%) scale(1.1);
            transition: transform 0.3s ease;

        }
        /*the main title*/
        .main-title {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Marcellus', serif;
            font-size: 55px;
            color: #153b2d;
            z-index: 3;
            width: 100%;
            text-align: center;
            letter-spacing: 3px;
        }

        /*  waves */
        .waves-container {
            position: absolute;
            top: 30%;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 90%;
          
        }
        .gwave{
            z-index: 4;
        }
        .waves-container svg {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
<form action="dashboard.php" method="post">
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="fluent_animal-cat-28-regular.png" title="logo" class="photo">
                <span>Animals Adoption</span>
            </div>
            <ul class="nav-links">
            <li><a href="http://localhost:8080/miniprojer1/dashboard.php">Dashboard</a></li>
                <li><a href="#">Missing pet</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="loginpage.html">Logout</a></li>
            </ul>
</nav>
<div class="stats-bar">
    <div>
        <i class="fa-solid fa-paw"></i>
        <?php echo $totalAnimals; ?> animals
    </div>
    <div>
        <i class="fa-solid fa-house"></i>
        <?php echo $total_adopted; ?> adopted
    </div>
    <div>
        <i class="fa-solid fa-heart"></i>
        <?php echo $available; ?> available
    </div>
</div>
    </header>
    <main class="mani">
    <a href="http://localhost:8080/miniprojer1/dashboard.php" title="dashboard">
            <div class="paw-graphic">
             <img src="fluent_animal-paw-print-20-filled.png" alt="f" width="450">
             <h1 class="main-title" width="800" justify-content="center">Animal Adoption Management</h1>
        </div>
    </a>
        <div class="waves-container">
            <svg viewBox="0 0 1440 400" preserveAspectRatio="none">
                <path fill="#125c45" d="M0,200 C300,250 800,50 1440,250 L1440,400 L0,400 Z"></path>
                <path fill="#15634d" d="M0,170 C1440,300 600,100 2440,200 L500,400 L0,400 Z"></path>
                <path fill="#137657" d="M0,190 C400,400 600,100 2440,200 L1440,400 L0,400 Z" class="gwave"></path>

            </svg>
        </div>
    </main>
</form>
</body>
</html>