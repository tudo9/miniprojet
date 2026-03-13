<?php
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Adoption Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Outfit:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head>
<body>
<!-- delete confirmation modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3> Are you sure?</h3>
        <p> Are you sure you want to remove this animal?</p>
        <div class="confirmation">
            <button id="confirmDeleteBtn" class="confirm">remove</button>
            <button type="button" class="cancel-remove" id="cancelDeleteBtn">cancel</button>
        </div>
    </div>
</div>

<script>
// Delete modal handling
(function(){
    var formToDelete = null;
    function openDeleteModal(form){
        formToDelete = form;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    function closeDeleteModal(){
        formToDelete = null;
        document.getElementById('deleteModal').style.display = 'none';
    }
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.remove-btn').forEach(function(btn){
            btn.addEventListener('click', function(e){
                var form = btn.closest('form');
                if(form) openDeleteModal(form);
            });
        });
        var cancel = document.getElementById('cancelDeleteBtn');
        if(cancel) cancel.addEventListener('click', closeDeleteModal);
        var confirm = document.getElementById('confirmDeleteBtn');
        if(confirm) confirm.addEventListener('click', function(){
            if(formToDelete) formToDelete.submit();
        });
        var modal = document.getElementById('deleteModal');
        if(modal) modal.addEventListener('click', function(e){ if(e.target === modal) closeDeleteModal(); });
    });
})();
</script>



<header>
    <nav class="navbar">
        <div class="logo"> 
             <img src="fluent_animal-cat-28-regular.png" class="photo"> <span>Animals Adoption</span></div>
        <div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="#">Add new admin</a></li>            
                <li><a href="loginpage.html">Logout</a></li>
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
                <label>Animal Name</label>
                <input type="text" name="namee" required placeholder="e.g. Buddy">
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label>Species</label>
                    <select name="species" required>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group col">
                    <label>Color</label>
                    <input type="text" name="color" placeholder="e.g. Golden">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label>Age (Years)</label> 
                    <input type="number" name="age" min="0" required placeholder="e.g. 3">
                </div>
                <div class="form-group col">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Health Status</label>
                <select name="health_status" required>
                    <option value="Healthy">Healthy</option>
                    <option value="Under Treatment">Under Treatment</option>
                </select>
            </div>
            <div class="form-group">
                <label>Animal Photo</label>
                <input type="file" name="image" accept="image/*" class="file-input">
                <small class="text-muted">Max 10MB. JPG or PNG only.</small>
            </div>
            <button type="submit" class="submit">Add to Center</button>
        </form>
    </div>
</div>
<!-- Animal List -->
<div class="list-animal">
<?php
$result = mysqli_query($conn,"SELECT * FROM animals ORDER BY id DESC");
if ($result && mysqli_num_rows($result) > 0) {
?>
    <table class="animals-table">
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
            <?php while ($row = mysqli_fetch_assoc($result)) {
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
                    <a href="edit.php?id=<?php echo $id; ?>" ><button class="edit">Edit</button></a>
                </span>
            </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
</div>

</div>


<!--adoptions Modal -->
<script>
function openAdoptModal(id,name){
document.getElementById("adoptModal").style.display="flex";
document.getElementById("animal_id").value=id;
document.getElementById("animal_name").value=name;
}
function closeModal(){
document.getElementById("adoptModal").style.display="none";
}
    </script>


<div id="adoptModal" class="modal">
    <div class="modal-content">
        <h2>Adoption Form</h2>
        <form method="POST" action="dashboard.php">
            <input type="hidden" name="action" value="removecon">
            <input type="hidden" id="animal_id" name="animal_id">
            <input type="hidden" id="animal_name" name="animal_name">
            <label>Adopter First Name</label>
            <input type="text" name="adopter_fname" class="adopter_name" required>
            <label>Adopter Last Name</label>
            <input type="text" name="adopter_lname" class="adopter_name" required>
            <label>Phone</label>
            <input type="text" name="adopter_phone" class="adopter_phone" required>
            <label>Address</label>
            <input type="text" name="adopter_address" class="adopter_address">
            <button type="submit" class="submit">Confirm Adoption</button>
            <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- confirm remove-->





</body>
</html>