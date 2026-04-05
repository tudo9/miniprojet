<?php
// Dashboard page for managing animal adoption system
// This file handles CRUD operations for animals, adoptions, and admin management
// It includes session management, database interactions, and HTML rendering

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../auth/login.php");
    exit();
}
// connect to database
require_once __DIR__ . '/../db_connect.php';

if (($_POST['action'] ?? '') === 'add') {
    // Handle adding a new animal to the database
    // Sanitize and validate all input data
    $namee   = mysqli_real_escape_string($conn, $_POST['namee'] ?? '');
    $species = mysqli_real_escape_string($conn, $_POST['species'] ?? '');
    $color   = mysqli_real_escape_string($conn, $_POST['color'] ?? '');
    $age     = (int)($_POST['age'] ?? 0);
    $gender  = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');
    $health  = mysqli_real_escape_string($conn, $_POST['health_status'] ?? '');
    $image   = '';
    // upload image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($_FILES["image"]["size"] <= 10*1024*1024 && in_array($fileType, ["jpg","jpeg","png"])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image = $fileName;
            }
        }
    }
    $sql = "INSERT INTO animals (name, species, color, age, gender, health_status, image) 
            VALUES ('$namee','$species','$color',$age,'$gender','$health','$image')";
    mysqli_query($conn, $sql);
    header("Location: dashboard.php");
    exit;
}

// delete animal
if (($_POST['action'] ?? '') === 'remove') {
    // Handle deleting an animal from the database
    $delId = (int)($_POST['id'] ?? 0);
    mysqli_query($conn, "DELETE FROM animals WHERE id = $delId");
    header("Location: dashboard.php");
    exit;
}

// adopt animal
if (($_POST['action'] ?? '') === 'adopt_confirm') {
    $animal_id = (int)($_POST['animal_id'] ?? 0);
    $animalRes = mysqli_query($conn, "SELECT * FROM animals WHERE id = $animal_id");
    $animal = mysqli_fetch_assoc($animalRes);

    $animal_name = mysqli_real_escape_string($conn, $animal['name'] ?? '');
    $species = mysqli_real_escape_string($conn, $animal['species'] ?? '');
    $color = mysqli_real_escape_string($conn, $animal['color'] ?? '');
    $age = (int)($animal['age'] ?? 0);
    $gender = mysqli_real_escape_string($conn, $animal['gender'] ?? '');
    $health_status = mysqli_real_escape_string($conn, $animal['health_status'] ?? '');
    $image = mysqli_real_escape_string($conn, $animal['image'] ?? '');

    $adopter_lname = mysqli_real_escape_string($conn, $_POST['adopter_lname'] ?? '');
    $adopter_fname = mysqli_real_escape_string($conn, $_POST['adopter_fname'] ?? '');
    $adopter_phone = mysqli_real_escape_string($conn, $_POST['adopter_phone'] ?? '');
    $adopter_address = mysqli_real_escape_string($conn, $_POST['adopter_address'] ?? '');

    $insertSql = "INSERT INTO adoptions (animal_name, species, color, age, gender, health_status, image, adopter_fname, adopter_lname, adopter_phone, adopter_address)
                  VALUES ('$animal_name', '$species', '$color', $age, '$gender', '$health_status', '$image', '$adopter_fname', '$adopter_lname', '$adopter_phone', '$adopter_address')";
    mysqli_query($conn, $insertSql);
    mysqli_query($conn, "DELETE FROM animals WHERE id=$animal_id");
    header("Location: dashboard.php");
    exit;
}

// edit animal
if (($_POST['action'] ?? '') === 'edit_confirm') {
    // Handle editing an existing animal's information
    $animal_id = (int)$_POST['animal_id'];
    // Sanitize new data from form
    $name = mysqli_real_escape_string($conn, $_POST['new_name'] ?? '');
    $species = mysqli_real_escape_string($conn, $_POST['new_species'] ?? '');
    $color = mysqli_real_escape_string($conn, $_POST['new_color'] ?? '');
    $age = (int)($_POST['new_age'] ?? 0);
    $gender = mysqli_real_escape_string($conn, $_POST['new_gender'] ?? '');
    $health_status = mysqli_real_escape_string($conn, $_POST['new_health_status'] ?? '');
    
    // Get current image to keep if no new image uploaded
    $currentRes = mysqli_query($conn, "SELECT image FROM animals WHERE id = $animal_id");
    $currentRow = mysqli_fetch_assoc($currentRes);
    $image = $currentRow['image'] ?? '';
    
    // Handle new image upload if provided
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
        $targetDir = __DIR__ . "/uploads/";
        $fileName = time() . "_" . basename($_FILES["new_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($_FILES["new_image"]["size"] <= 10*1024*1024 && in_array($fileType, ["jpg","jpeg","png"])) {
            if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $targetFile)) {
                $image = $fileName;
            }
        }
    }
    // Update animal record in database
    $editsql = "UPDATE animals SET name = '$name', species = '$species', color = '$color', age = $age, gender = '$gender', health_status = '$health_status', image = '$image' WHERE id = $animal_id";
    mysqli_query($conn, $editsql);
    header("Location: dashboard.php");
    exit;
}

// Query statistics for dashboard display
// total animals
$totalAnimalsQuery = mysqli_query($conn,"SELECT COUNT(*) as total FROM animals");
$totalAnimals = mysqli_fetch_assoc($totalAnimalsQuery)['total'];
// total adopted
$total_adoptedquery = mysqli_query($conn,"SELECT COUNT(*) as total FROM adoptions");
$total_adopted = mysqli_fetch_assoc($total_adoptedquery)["total"];
// total available
$availableAnimalsQuery = mysqli_query($conn,"SELECT COUNT(*) as total FROM animals WHERE health_status = 'healthy'");
$available = mysqli_fetch_assoc($availableAnimalsQuery)["total"];

// add new admin
if (($_POST["action"] ?? '') == "addadmin") {
    // Handle adding a new admin user
    $newusername = mysqli_real_escape_string($conn, $_POST["admin_username"] ?? '');
    $rawPassword = $_POST["admin_password"] ?? '';
    // Hash password for security
    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
    $sql = "INSERT INTO admins (username , password)
    VALUES ('$newusername' , '$hashedPassword')";
    mysqli_query($conn, $sql);
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Adoption Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="script.js"></script>
</head>
<body>

<!-- Sidebar for navigation and stats -->
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<div class="sidebar" id="mySidebar" onmouseenter="expandSidebar()" onmouseleave="collapseSidebar()">
    <div class="toggle-btn">☰</div>
    <h2>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <ul>
        <li>
            <a href="../home/home.php">
                <i class="fa-solid fa-house"></i> <span>Home</span>
            </a>
        </li>

        <!-- Stats -->
        <div class="stats-bar">
            <li><i class="fa-solid fa-paw"></i> <span><?php echo $totalAnimals; ?> animals</span></li>
            <li><i class="fa-solid fa-hand-holding-heart"></i> <span><?php echo $total_adopted; ?> adopted</span></li>
            <li><i class="fa-solid fa-heart"></i> <span><?php echo $available; ?> available</span></li>
        </div>

        <!-- Profile -->
        <li>
            <a href="#">
                <i class="fa-solid fa-user"></i> <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            </a>
        </li>

        <hr>

        <li>
            <a onclick="openAddAdminModal()" style="cursor:pointer;">
                <i class="fa-solid fa-circle-plus"></i> <span>Add new Admin</span>
            </a>
        </li>
        <li>
            <a href="../auth/login.php">
                <i class="fa-solid fa-right-left"></i> <span>Switch account</span>
            </a>
        </li>
    </ul>

    <!-- Logout -->
    <a class="logout" href="../auth/logout.php">
        <i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span>
    </a>
</div>

<div class="without-sidebar">
<header>
    <nav class="navbar">
        <div class="logo">
            <img src="fluent_animal-cat-28-regular.png" class="photo">
            <span>Animals Adoption</span>
        </div>
        <div>
            <ul class="nav-links">
                <li><a href="../home/home.php">Home</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
</header>

<!-- Main dashboard content -->
<div class="dashboard-full">
    <!-- Form to add new animal -->
    <div class="add-animal">
        <h2>Add New Animal</h2>
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" id="addAnimalForm">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="namee">Animal Name</label>
                <input type="text" name="namee" id="namee" required placeholder="e.g. Buddy">
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label for="species">Species</label>
                    <select name="species" id="species" required>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group col">
                    <label for="color">Color</label>
                    <input type="text" name="color" placeholder="e.g. Golden" id="color">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label for="age">Age (Years)</label>
                    <input type="number" id="age" name="age" min="0" required placeholder="e.g. 3">
                </div>
                <div class="form-group col">
                    <label style="margin-bottom: 6px; display: block; font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Gender</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="radio" name="gender" id="Male" value="Male" class="input-gender" required>
                        <label for="Male" class="gender">Male</label>
                        <input type="radio" name="gender" id="Female" value="Female" class="input-gender" required>
                        <label for="Female" class="gender">Female</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="status">Health Status</label>
                <select name="health_status" id="status" required>
                    <option value="Healthy">Healthy</option>
                    <option value="Under Treatment">Under Treatment</option>
                </select>
            </div>
            <div class="form-group">
                <label for="photo">Animal Photo</label>
                <input type="file" name="image" id="photo" accept="image/*" class="file-input">
                <small class="text-muted">Max 10MB. JPG or PNG only.</small>
            </div>
            <button type="submit" class="submit">Add to Center</button>
        </form>
    </div>

    <!-- Animal List Section -->
    <div class="list-animal">
        <div class="searchbar">
            <h1>All animals</h1>
            <i class="fa-solid fa-search"></i>
            <!-- Search input for filtering animals by name or color -->
            <input type="text" id="searchInput" name="search" onkeyup="instantsearch()" placeholder="Name or color..." class="search-input">
            <!-- Dropdown to filter animals by species -->
            <select name="species_filter" id="speciesfilter" onchange="filterbyspecies()">
                <option value="">All Species</option>
                <option value="Dog" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Dog') echo 'selected'; ?>>Dogs</option>
                <option value="Cat" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Cat') echo 'selected'; ?>>Cats</option>
                <option value="Bird" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Bird') echo 'selected'; ?>>Birds</option>
                <option value="Other" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

<?php
// Query animals from database with optional search filter
$searchTerm = mysqli_real_escape_string($conn, $_POST['search'] ?? '');
$result = mysqli_query($conn,"SELECT * FROM animals WHERE name LIKE '%$searchTerm%' OR color LIKE '%$searchTerm%' order by id desc");
if ($result && mysqli_num_rows($result) > 0) {
?>
        <!-- Table to display animals -->
        <table class="animals-table" id="animalsTable">
            <thead>
                <tr>
                    <th style="text-align: left;">Photo</th>
                    <th>Name</th>
                    <th>Info</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
<?php 
    // Loop through each animal record and display in table rows
    while ($row = mysqli_fetch_assoc($result)) {
        // Sanitize and extract animal data
        $id = (int)$row["id"];
        $name = htmlspecialchars($row["name"] ?? '');
        $species = htmlspecialchars($row["species"] ?? '');
        $color = htmlspecialchars($row["color"] ?? '');
        $age = htmlspecialchars($row["age"] ?? '');
        $gender = htmlspecialchars($row["gender"] ?? '');
        $health_status = htmlspecialchars($row["health_status"] ?? '');
        $image = '';
        if (!empty($row['image'])) {
            $image = 'uploads/' . htmlspecialchars($row['image']);
        }
?>
                <tr>
                    <td style="text-align:left;"><img src="<?php echo $image; ?>" alt="Animal Photo"></td>
                    <td><?php echo $name; ?></td>
                    <td>
                        <b class="species"><?php echo $species; ?></b><br>
                        <span style="color:#94a3b8;">●</span> <?php echo $color; ?><br>
                        <span style="color:#94a3b8;">●</span> <?php echo $age; ?> years<br>
                        <span style="color:#94a3b8;">●</span> <?php echo $gender; ?>
                    </td>
                    <td>
<?php 
    // Display health status with color coding
    if ($health_status === "Healthy") {
        echo '<span style="color:#16a34a; font-weight:600;">● '.$health_status.'</span>';
    } else {
        echo '<span style="color:#f59e0b; font-weight:600;">● '.$health_status.'</span>';
    }
?>
                    </td>
                    <td class="actions">
                        <!-- Button to open adoption modal -->
                        <button class="adopt" onclick="openAdoptModal(<?php echo $id; ?>,'<?php echo $name; ?>')">Adopt</button>
                        <span class="edit-remove">
                            <!-- Form for deleting animal -->
                            <form action="dashboard.php" method="POST" class="remove-form">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="button" class="delete remove-btn">Remove</button>
                            </form>
                            <!-- Button to open edit modal -->
                            <button type="button" class="edit" onclick="openEditModal(<?php echo $id; ?>, '<?php echo addslashes($name); ?>', '<?php echo addslashes($species); ?>', '<?php echo addslashes($color); ?>', <?php echo $age; ?>, '<?php echo addslashes($gender); ?>', '<?php echo addslashes($health_status); ?>')">Edit</button>
                        </span>
                    </td>
                </tr>
<?php } ?>
            </tbody>
        </table>
<?php } else { ?>
        <!-- Display message when no animals are found -->
        <div style="text-align:center; padding:40px; color:#94a3b8;">
            <i class="fa-solid fa-paw" style="font-size:3rem; margin-bottom:16px; display:block;"></i>
            <p>No animals found. Add your first animal using the form!</p>
        </div>
<?php } ?>
    </div>
</div>

<!-- Modal for animal adoption form -->
<div id="adoptModal" class="modal">
    <div class="modal-content">
        <h2>Adoption Form</h2>
        <!-- Form collects adopter information and processes adoption -->
        <form id="adoptform" method="POST" action="dashboard.php">
            <input type="hidden" name="action" value="adopt_confirm">
            <input type="hidden" id="animal_id" name="animal_id">
            <label>Adopter First Name</label>
            <input type="text" name="adopter_fname" class="adopter_name" required>
            <label>Adopter Last Name</label>
            <input type="text" name="adopter_lname" class="adopter_name" required>
            <label for="adopter_phone">Phone</label>
            <input type="text" name="adopter_phone" id="adopter_phone" class="adopter_phone" required>
            <small id="error" style="display:none; color:red;">Enter a valid phone number (0XX XXX XX XX or +213XX XXX XX XX)</small>
            <br>
            <label>Address</label>
            <input type="text" name="adopter_address" class="adopter_address">
            <div class="btn-row">
                <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="submit" id="submit">Confirm Adoption</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal for delete confirmation -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Are you sure?</h3>
        <p>Are you sure you want to remove this animal?</p>
        <div class="confirmation btn-row">
            <button type="button" class="cancel" id="cancelDeleteBtn">Cancel</button>
            <button id="confirmDeleteBtn" class="confirm">Remove</button>
        </div>
    </div>
</div>

<!-- Modal for editing animal information -->
<div id="editmodal" class="modal">
    <div class="edit-modal-content">
        <h2>Edit Animal</h2>
        <!-- Form allows updating animal details and uploading new photo -->
        <form action="dashboard.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_confirm">
            <input type="hidden" id="edit_animal_id" name="animal_id">
            
            <label for="edit_name">Animal Name</label>
            <input type="text" name="new_name" id="edit_name" required placeholder="e.g. Buddy">
            
            <label for="edit_species">Species</label>
            <select name="new_species" id="edit_species" required>
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
                <option value="Bird">Bird</option>
                <option value="Other">Other</option>
            </select>
            
            <label for="edit_color">Color</label>
            <input type="text" name="new_color" id="edit_color" placeholder="e.g. Golden">
            
            <label for="edit_age">Age (Years)</label>
            <input type="number" name="new_age" id="edit_age" min="0" required placeholder="e.g. 3">
            
            <label for="edit_gender">Gender</label>
            <select name="new_gender" id="edit_gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            
            <label for="edit_health_status">Health Status</label>
            <select name="new_health_status" id="edit_health_status" required>
                <option value="Healthy">Healthy</option>
                <option value="Under Treatment">Under Treatment</option>
            </select>
            
            <label for="edit_image">Animal Photo (Leave empty to keep current)</label>
            <input type="file" name="new_image" id="edit_image" accept="image/*" class="file-input">
            <small class="text-muted">Max 10MB. JPG or PNG only.</small>
            
            <div class="btn-row">
                <button type="button" class="cancel" onclick="document.getElementById('editmodal').style.display='none'">Cancel</button>
                <button type="submit" class="submit">Update</button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- Modal for adding new admin -->
<div id="addAdminModal" class="modal">
    <div class="addadmin-content">
        <h2>Add New Admin</h2>
        <!-- Form creates new admin account with hashed password -->
        <form id="addAdminForm" method="POST" action="dashboard.php">
            <label for="admin_username">Username</label>
            <input type="text" name="admin_username" id="admin_username" required>
            <label for="admin_password">Password</label>
            <input type="password" name="admin_password" id="admin_password" required>
            <input type="hidden" name="action" value="addadmin">
            <div class="btn-row">
                <button type="button" class="cancel" onclick="document.getElementById('addAdminModal').style.display='none'">Cancel</button>
                <button type="submit" class="submit">Add Admin</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>