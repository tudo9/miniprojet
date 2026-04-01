<?php
session_start();
// if user is logged in, make login button disappear and show dashboard
if(isset($_SESSION["username"])) {
 $logedin = 1;    
}
else {
    $logedin = 0;
}

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

// Load animals for cards
$speciesFilter = '';
$where = '';
if (isset($_GET['species_filter']) && !empty($_GET['species_filter'])) {
    $speciesFilter = $_GET['species_filter'];
    $where = " WHERE species = '" . mysqli_real_escape_string($conn, $speciesFilter) . "'";
}
$animalsQuery = mysqli_query($conn, "SELECT * FROM animals" . $where . " ORDER BY id DESC");
$animals = [];
if ($animalsQuery) {
    while($row = mysqli_fetch_assoc($animalsQuery)) {
        $animals[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Adoption</title>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Outfit:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="home.css">
        <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 1rem;
        }
        .animal-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }
        .animal-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .animal-img-container {
            width: 100%;
            height: 200px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .animal-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .animal-details {
            padding: 1.5rem;
        }
        .animal-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .animal-meta {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .hero-section {
            text-align: center;
            padding: 2rem 1.5rem;
            background: linear-gradient(135deg, #f0fdf4 0%, #e0e7ff 100%);
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }
        .hero-section h1 {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
        }
    </style>

</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="fluent_animal-cat-28-regular.png" title="logo" class="photo">
                <span>Animals Adoption</span>
            </div>
    <ul class="nav-links">
        <?php if ( $logedin === 1 ): ?>
            <li><a href="http://localhost:8080/miniprojer1/dashboard/dashboard.php">Dashboard</a></li>
            <li><a href="http://localhost:8080/miniprojer1/auth/logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="http://localhost:8080/miniprojer1/auth/login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>
<!-- stats-->
<div class="stats-bar">
    <div>
        <i class="fa-solid fa-paw"></i>
        <?php echo $totalAnimals; ?> animals
    </div>
    <div>
        <i class="fa-solid fa-hand-holding-heart"></i>
        <?php echo $total_adopted; ?> adopted
    </div>
    <div>
        <i class="fa-solid fa-heart"></i>
        <?php echo $available; ?> available
    </div>
</div>
    </header>
    <div class="content">
        <div class="container">
        
        <div class="hero-section">
            <h1>Meet Our Furry Friends!</h1>
            <p>We have <?php echo $available; ?> lovely animal<?php echo $available != 1 ? 's' : ''; ?> ready for a forever home.</p>
        </div>

        <div class="list-header">
            <h2>Our Animals</h2>
            <!-- Filter Form -->
            <form action="home.php" method="GET" class="filter-form">
                <select name="species_filter">
                    <option value="">All Species</option>
                    <option value="Dog" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Dog') echo 'selected'; ?>>Dogs</option>
                    <option value="Cat" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Cat') echo 'selected'; ?>>Cats</option>
                    <option value="Bird" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Bird') echo 'selected'; ?>>Birds</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                <a href="home.php" class="btn btn-default btn-sm">Clear</a>
            </form>
        </div>

        <?php if(empty($animals)): ?>
            <div class="card glass-card empty-state">
                <p>No animals available at the moment! Please check back later.</p>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach($animals as $animal): ?>
                    <div class="animal-card">
                        <div class="animal-img-container">
                            <?php if($animal['image_path'] && file_exists($animal['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($animal['image_path']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>">
                            <?php else: ?>
                                🐾
                            <?php endif; ?>
                        </div>
                        <div class="animal-details">
                            <div class="animal-name">
                                <?php echo htmlspecialchars($animal['name']); ?>
                                <span class="badge species-<?php echo strtolower($animal['species']); ?>"><?php echo htmlspecialchars($animal['species']); ?></span>
                            </div>
                            <div class="animal-meta">
                                <?php echo htmlspecialchars($animal['color']); ?> • <?php echo $animal['age']; ?> yrs old • <?php echo $animal['gender']; ?>
                            </div>
                            <div class="status-badge <?php echo $animal['health_status'] == 'Healthy' ? 'status-green' : 'status-orange'; ?>">
                                <?php echo htmlspecialchars($animal['health_status']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>