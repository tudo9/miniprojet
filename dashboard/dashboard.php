<?php
session_start();
if (!isset($_SESSION["username"])) {
    // إذا لم يكن مسجلاً، وجهه لصفحة تسجيل الدخول
    header("Location: http://localhost:8080/miniprojer1/auth/login.php");
    exit();
}
$server="localhost";
$user="root";
$pass="";
$name= "animal_adoption";
// connect to database===========================================
try {
    $conn = mysqli_connect($server,$user,$pass,$name);
} catch (mysqli_sql_exception) {
    echo "connection failed: ";
}
if (($_POST['action'] ?? '') === 'add') {
        //  add new animal=====================================
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
            mkdir($targetDir, 0777, true); // create directory for photos
        }
        $fileName = time() . "_" . basename($_FILES["image"]["name"]); // create unique file name
        $targetFile = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($_FILES["image"]["size"] <= 10*1024*1024 && in_array($fileType, ["jpg","jpeg","png"])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image = $fileName; // save file name in database
            }
        }
    }
    $sql = "INSERT INTO animals (name, species, color, age, gender, health_status, image) 
            VALUES ('$namee','$species','$color',$age,'$gender','$health','$image')";
    mysqli_query($conn, $sql);
    header("Location: dashboard.php");
    exit;
}
        // delete animal================================================
if (($_POST['action'] ?? '') === 'remove') {
    $delId = (int)($_POST['id'] ?? 0);
        $delStmt = mysqli_query($conn, "DELETE FROM animals WHERE id = $delId");
header("Location: dashboard.php");
exit;
        }
        // adopt animal================================================
        if (($_POST['action'] ?? '') === 'adopt_confirm') {

            $animal_id = (int)($_POST['animal_id'] ?? 0);

            // fetch the full animal record from animals table to ensure we save all fields
            $animalRes = mysqli_query($conn, "SELECT * FROM animals WHERE id = $animal_id");

            $animal = mysqli_fetch_assoc($animalRes);

            $animal_name = mysqli_real_escape_string($conn, $animal['name'] ?? '');
            $species = mysqli_real_escape_string($conn, $animal['species'] ?? '');
            $color = mysqli_real_escape_string($conn, $animal['color'] ?? '');
            $age = (int)($animal['age'] ?? 0);
            $gender = mysqli_real_escape_string($conn, $animal['gender'] ?? '');
            $health_status = mysqli_real_escape_string($conn, $animal['health_status'] ?? '');
            $image = mysqli_real_escape_string($conn, $animal['image'] ?? '');

            // adopter fields (from the confirmation form)
            $adopter_lname = mysqli_real_escape_string($conn, $_POST['adopter_lname'] ?? '');
            $adopter_fname = mysqli_real_escape_string($conn, $_POST['adopter_fname'] ?? '');
            $adopter_phone = mysqli_real_escape_string($conn, $_POST['adopter_phone'] ?? '');
            $adopter_address = mysqli_real_escape_string($conn, $_POST['adopter_address'] ?? '');

            $insertSql = "INSERT INTO adoptions ( animal_name, species, color, age, gender, health_status, image, adopter_fname, adopter_lname, adopter_phone, adopter_address)
                          VALUES ( '$animal_name', '$species', '$color', $age, '$gender', '$health_status', '$image', '$adopter_fname', '$adopter_lname', '$adopter_phone', '$adopter_address')";

            mysqli_query($conn, $insertSql);

            
            // remove from animals after successful adoption
            mysqli_query($conn, "DELETE FROM animals WHERE id=$animal_id");
            header("Location: dashboard.php");
            exit;

        }
        //edit animal===============================================
 //edit animal===============================================
if (($_POST['action'] ?? '') === 'edit_confirm') {
    $animal_id = (int)$_POST['animal_id'];
    $name = mysqli_real_escape_string($conn, $_POST['new_name'] ?? '');
    $species = mysqli_real_escape_string($conn, $_POST['new_species'] ?? '');
    $color = mysqli_real_escape_string($conn, $_POST['new_color'] ?? '');
    $age = (int)($_POST['new_age'] ?? 0);
    $gender = mysqli_real_escape_string($conn, $_POST['new_gender'] ?? '');
    $health_status = mysqli_real_escape_string($conn, $_POST['new_health_status'] ?? '');
    // جلب الصورة القديمة في حال لم يقم المستخدم برفع صورة جديدة
    $currentRes = mysqli_query($conn, "SELECT image FROM animals WHERE id = $animal_id");
    $currentRow = mysqli_fetch_assoc($currentRes);
    $image = $currentRow['image'] ?? '';
    // معالجة رفع الصورة الجديدة إذا وُجدت
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
        $targetDir = __DIR__ . "/uploads/";
        $fileName = time() . "_" . basename($_FILES["new_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($_FILES["new_image"]["size"] <= 10*1024*1024 && in_array($fileType, ["jpg","jpeg","png"])) {
            if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $targetFile)) {
                $image = $fileName; // تحديث اسم الصورة إذا تم الرفع بنجاح
            }
        }
    }
    // تحديث البيانات في قاعدة البيانات
    $editsql = "UPDATE animals SET name = '$name', species = '$species', color = '$color', age = $age, gender = '$gender', health_status = '$health_status', image = '$image' WHERE id = $animal_id";
    mysqli_query($conn, $editsql);
    header("Location: dashboard.php");
    exit;
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

// add new admin
if (($_POST["action"] ?? '') == "addadmin") {
    $newusername = mysqli_real_escape_string($conn, $_POST["admin_username"] ?? '');
    $newpassword = mysqli_real_escape_string($conn, $_POST["admin_password"] ?? '');
    $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
    $sql = "INSERT INTO admins (username , password)
    VALUES ('$newusername' , '$hashedPassword')";
    mysqli_query($conn, $sql);
    header("Location: dashboard.php");
    exit;
}
// Handle filters
$where_clause = "WHERE 1=1";
$params = [];

if(isset($_GET['species_filter']) && !empty($_GET['species_filter'])) {
    $where_clause .= " AND species = :species";
    $params[':species'] = $_GET['species_filter'];
}

if(isset($_GET['health_filter']) && !empty($_GET['health_filter'])) {
    $where_clause .= " AND health_status = :health";
    $params[':health'] = $_GET['health_filter'];
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
    
    <!-- Sidebar -->
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<div class="sidebar" id="mySidebar" onmouseenter="expandSidebar()" onmouseleave="collapseSidebar()">
    <div class="toggle-btn">☰</div>
    <h2 style="margin-left: 5px;">hello <?php echo $_SESSION['username']; ?> !</h2>
    <ul>
        <li style="margin-left: -32px; display: flex;align-items: center; ">
            <a href="http://localhost:8080/miniprojer1/home/home.php">
                <i class="fa-solid fa-house" style="margin-right: 5px;"></i> <span>Home</span> </a>
        </li>        
        <br><br>
        <!-- stats -->
        <div class="stats-bar" style="margin-left: -28px; padding-left: 15px;">
            <li><i class="fa-solid fa-paw" style="margin-right: 5px;"></i>
            <span style=""><?php echo $totalAnimals; ?> animals</span></li>
            <br><br>
            <li>    <i class="fa-solid fa-hand-holding-heart" style="margin-right: 5px;"></i><span style=""><?php echo $total_adopted; ?> adopted</span></li>
            <br><br>
            <li>    <i class="fa-solid fa-heart" style="margin-right: 5px;"></i><span style=""><?php echo $available; ?> available </span></li> 
        </div>
        <li><span><hr style="position: absolute; left: -25px; width: 100vh; border: none;height: 1px; background-color: #ccc;"></span></li>
        <br><br>
        <!-- profiles-->
         
        <li style="margin-left: -20px ; display: flex;align-items: center; ">
            <i class="fa-solid fa-user" style="margin-right: 5px;"></i> <span>Profiles</span>
        </li>
        
        <li style="display: flex; align-items: center; justify-content: center;margin-top: 5px; margin-left: -120px;">
            <span style="background-color: #ffffff4f; border-radius: 5px;padding: 5px;"><?php
                echo ("User" . ": " . $_SESSION["username"]);?>
            </span>
            <br>
        </li>
        <li><span><hr style="position: absolute; left: 17%; width: 100vh; border: none;height: 1px; background-color: #ccc;"></span></li>
        <br>
        <li>
            <span>
                <a onclick="openAddAdminModal()" style="background-color: none; height: 2px;"> 
                    <i class="fa-solid fa-circle-plus" style="margin-right: 5px;"></i>Add a new Admin</a>
            </span>
        </li>
        <br>
        <li>
            <span>
                <a href="http://localhost:8080/miniprojer1/auth/login.php" style="background-color: none; height: 2px;">
                    <i class="fa-solid fa-right-left" style="margin-right: 5px;"></i>Use another account
                </a>
            </span>
        </li>

        <!-- logout -->

        <li style="margin-left: -32px; position: absolute; top: 85%; width: 85%;">
            <a class="logout" href="http://localhost:8080/miniprojer1/auth/logout.php">
                <i class="fa-solid fa-sign-out-alt" style="margin-right: 5px;"></i> <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

 <script>
function expandSidebar() {
    const sidebar = document.getElementById("mySidebar");
    const body = document.body;
    
    sidebar.classList.add("expanded");
    body.classList.add("sidebar-open");
}

function collapseSidebar() {
    const sidebar = document.getElementById("mySidebar");
    const body = document.body;
    
    sidebar.classList.remove("expanded");
    body.classList.remove("sidebar-open");
}

// Toggle sidebar when clicking the toggle button or overlay

function toggleSidebar() {
    collapseSidebar();
} </script>


<div class="without-sidebar">
<header>
    <nav class="navbar">
        <div class="logo"> 
             <img src="fluent_animal-cat-28-regular.png" class="photo"> <span>Animals Adoption</span></div>
        <div>
            <ul class="nav-links">
                <li><a href="http://localhost:8080/miniprojer1/home/home.php">Home</a></li>
                <li><a href="http://localhost:8080/miniprojer1/auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
</header>
<div class="dashboard-full">
    <!-- Add Animal -->
<div class="add-animal">
    <div class="card glass-card form-section">
        <h2 >Add New Animal</h2>
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" id="addAnimalForm">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="namee">Animal Name</label>
                <input type="text" name="namee" id="namee"required placeholder="e.g. Buddy">
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
                <div class="form-group col" style=" display: flex; flex-direction: row; align-items: flex-end; justify-content: space-between; ">                   
                    <input type="radio" name="gender" id="Male" value="Male" class="input-gender" required>
                    <label for="Male" class="gender">Male</label>
                    <input type="radio" name="gender" id="Female" value="Female"  class="input-gender" required>
                    <label for="Female" class="gender">Female</label>
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
</div>
<!-- Animal List -->
    <!-- search bar -->
<div class="list-animal">
    <div class="searchbar" >
        <h1 >All animals</h1>
        <i class="fa-solid fa-search" style="margin: 5px 0 0 31%; width: fit-content; height: 20px;"></i>

        <input type="text" id="searchInput" name="search" onkeyup="instantsearch()" placeholder="Name or color..."  class="search-input">

        <select name="species_filter" id="speciesfilter" onchange="filterbyspecies()" >
            <option value="">All Species</option>
            <option value="Dog" id="dog" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Dog') echo 'selected'; ?>>Dogs</option>
            <option value="Cat" id="cat" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Cat') echo 'selected'; ?>>Cats</option>
            <option value="Bird" id="bird" <?php if(isset($_GET['species_filter']) && $_GET['species_filter'] == 'Bird') echo 'selected'; ?>>Birds</option>
        </select>
    </div>
    <br>                 
<?php
$searchTerm = mysqli_real_escape_string($conn, $_POST['search'] ?? '');
$result = mysqli_query($conn,"SELECT * FROM animals WHERE name LIKE '%$searchTerm%' OR color LIKE '%$searchTerm%' order by id desc");
if ($result && mysqli_num_rows($result) > 0) {
?>
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
            
                while ($row = mysqli_fetch_assoc($result)) {
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
                <td style="text-align:left;"><img src="<?php echo $image; ?>" alt="Animal Photo" width="100"></td>
                <td><?php echo $name; ?></td>
                <td><b class="species"><?php echo $species; ?></b>
                <br>
                <span style="color:grey; font-weight:bold;">&#9679</span> 
                <?php echo $color; ?>
                <br>
                <span style="color:grey; font-weight:bold;">&#9679</span>
                <?php echo $age; ?>years
                <br>
                <span style="color:grey; font-weight:bold;">&#9679</span>
                <?php echo $gender; ?></td>
                <td>
                <?php 
                if ($health_status === "Healthy") {
                echo '<span style="color:green; font-weight:bold;">&#9679; '.$health_status.'</span>';
                } else {
                echo '<span style="color:orange; font-weight:bold;">&#9679; '.$health_status.'</span>';
                }
                ?>
                </td>
                
                <td class="actions">
                <button class="adopt" 
                onclick="openAdoptModal(<?php echo $id; ?>,'<?php echo $name; ?>')">
                Adopt
                </button>
                <span class="edit-remove">
                    <form action="dashboard.php" method="POST" class="remove-form">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="button" class="delete remove-btn">Remove</button>
                    </form>
                    <button type="button" class="edit" onclick="openEditModal(<?php echo $id; ?>, '<?php echo addslashes($name); ?>', '<?php echo addslashes($species); ?>', '<?php echo addslashes($color); ?>', <?php echo $age; ?>, '<?php echo addslashes($gender); ?>', '<?php echo addslashes($health_status); ?>')">Edit</button>                
                </span>
            </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
</div>
</div>
<script>
    function instantsearch() {
    // 1. الحصول على النص المكتوب وتحويله لحروف صغيرة
    let input = document.getElementById('searchInput');
    let filter = input.value.toLowerCase();
    
    // 2. الوصول إلى جدول الحيوانات وصفوفه
    let table = document.getElementById('animalsTable');
    let tr = table.getElementsByTagName('tr');

    // 3. المرور على كل الصفوف (تجاهل رأس الجدول index 0)
    for (let i = 1; i < tr.length; i++) {
        // نأخذ النص الموجود في خلية الاسم وخلية المعلومات (النوع واللون)
        let nameCell = tr[i].getElementsByTagName('td')[1]; // عمود الاسم
        let infoCell = tr[i].getElementsByTagName('td')[2]; // عمود المعلومات
        
        if (nameCell || infoCell) {
            let nameText = nameCell.textContent || nameCell.innerText;
            let infoText = infoCell.textContent || infoCell.innerText;
            
            // 4. إذا كان النص المكتوب موجوداً في الاسم أو المعلومات، اظهر الصف، وإلا أخفه
            if (nameText.toLowerCase().indexOf(filter) > -1 || 
                infoText.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
// filter by species
 function filterbyspecies() {
        let species = document.getElementById('speciesfilter');
        let selectedSpecies = species.value;
        let table = document.getElementById('animalsTable');
        let tr = table.getElementsByTagName('tr');
        for (let i = 1; i < tr.length; i++) {
            let speciesCell = tr[i].getElementsByTagName('td')[2]; // عمود المعلومات
            if (speciesCell) {
                let speciesText = speciesCell.textContent || speciesCell.innerText;
                if (selectedSpecies === "" || speciesText.toLowerCase().indexOf(selectedSpecies.toLowerCase()) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
 }
</script>
<!-- Adopt Modal -->

<div id="adoptModal" class="modal">
    <div class="modal-content">
        <h2>Adoption Form</h2>
        <form id="adoptform" method="POST" action="dashboard.php" >
            <input type="hidden" name="action" value="adopt_confirm">
            <input type="hidden" id="animal_id" name="animal_id">
            <label>Adopter First Name</label>
            <input type="text" name="adopter_fname" class="adopter_name" required>
            <label>Adopter Last Name</label>
            <input type="text" name="adopter_lname" class="adopter_name" required>
            <label for="adopter_phone">Phone</label>
            <input type="text"  name="adopter_phone" id="adopter_phone" class="adopter_phone" required>
            <small id="error" style="display: none ;color: red; ">Enter a valid phone number (0XX XXX XX XX or +213XX XXX XX XX)</small>
            <br>
            <label>Address</label>
            <input type="text" name="adopter_address" class="adopter_address">
            <button type="submit" class="submit" id="submit">Confirm Adoption</button>
            <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>
    <!-- validate phone number-->
<script>
        const phone = document.getElementById('adopter_phone');
    const error = document.getElementById('error');
    phone.addEventListener('input', () => {
        const value = phone.value;
        const onlyNumbersRegex = /^(0|\+213)[567]\d{8}$/;
        const submit = document.getElementById("submit");
        if (value === ""){
            error.style.display = "none";
            adopter_phone.style.border = "1px solid #ccc";
            submit.disabled = true;
        }
        else if (!onlyNumbersRegex.test(value)) {
            error.style.display = "block";
            adopter_phone.style.border = "1px solid red";
            submit.disabled = true;
        }
        else {
            error.style.display = "none";
            adopter_phone.style.border = "1px solid #ccc";
            submit.disabled = false;
        }   
    });
</script>

<!-- delete confirmation modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="width: 15rem">
        <h3> Are you sure?</h3>
        <p> Are you sure you want to remove this animal?</p>
        <div class="confirmation">
            <button id="confirmDeleteBtn" class="confirm">remove</button>
            <button type="button" class="cancel-remove" id="cancelDeleteBtn">cancel</button>
        </div>
    </div>
</div>

<!-- edit animal modal-->

<div id="editmodal" class="modal">
    <div class="edit-modal-content">
        <h2>Edit Animal</h2>
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" >
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
            
            <label for="edit_image">Animal Photo (Leave empty to keep current photo)</label>
            <input type="file" name="new_image" id="edit_image" accept="image/*" class="file-input">
            <small class="text-muted">Max 10MB. JPG or PNG only.</small>
            
            <button type="submit" class="submit">Update</button>
            <button type="button" class="cancel" onclick="document.getElementById('editmodal').style.display='none'">Cancel</button>
        </form>
    </div>
</div>
</div>

<!-- add admin modal -->

<div id="addAdminModal" class="modal" >
    <div class="addadmin-content">
        <h2>Add New Admin</h2>
        <form id="addAdminForm" method="POST" action="dashboard.php">
            <label for="admin_username">Username</label>
            <input type="text" name="admin_username" id="admin_username" required>
            <label for="admin_password">Password</label>
            <input type="password" name="admin_password" id="admin_password" required>
            <input type="hidden" name="action" value="addadmin">
            <button type="submit" class="submit" >Add Admin</button>
            <button type="button" class="cancel" onclick="document.getElementById('addAdminModal').style.display='none'">Cancel</button>
        </form>
    </div>    
</div>

</body>
</html>