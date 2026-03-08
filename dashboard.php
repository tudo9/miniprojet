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
    <style>
        body{
            font-family:'Outfit',sans-serif;
            margin:0;
            padding:0;
            background-color:#f8fafc;
            
        }
        /* header */
        header{
            background-color: #137657;
            padding:25px 50px 10px 50px;
        }
        .navbar{
            display:flex;
            justify-content:space-between;
            align-items:start;
            margin-bottom:25px;
            height: 40px;
        }
        .nav-links{
            margin-right:150px;
            margin-top: -1%;
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
        /* add animal area */
        .add-animal{
            border-radius:15px;
            width:340px;
            margin-left:150px;
            margin-top:40px;
            background:#ffffff;
            display:grid;
            grid-template-columns:repeat(1,1fr);
            padding:20px;
            gap:15px;
            box-shadow:0px 0px 50px rgba(0,0,0,0.1);
        }
        /* label in add animal area */
        label{
            display:block;
            font-size:14px;
            font-weight:600;
            margin-bottom:8px;
            color: #111827;
        }
        /* row in add animal area */
        .form-row{
            
            display: grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
        }
        /* columns in add animal area */
        .form-group{
            margin-bottom:25px;
        }
        /* input box's */
        input[type="text"],input[type="number"]{
            width:100%;
            padding:12px 14px;
            border-radius:10px;
            border:1px solid #c4c7cc;
            background:#ffffff;
            font-size:15px;
            color:#0f1724;
            box-sizing:border-box;
        }
        /* select box */
        select{
            width:100%;
            padding:12px 14px;
            border-radius:10px;
            border:1px solid #c4c7cc;
            background:#ffffff;
            font-size:15px;
            color:#0f1724;
            box-sizing:border-box;
        }
        /* image */
        .file-input{
            display:block;
            width:100%;
            padding:12px;
            border-radius:
            10px;border:
            1px dashed #e2e8f0;
            background:#f8fafc;
            color:#111827;
            cursor:pointer;
            box-sizing:border-box;
        }
        /* button */
        .submit{
            width:100%;
            height: 60px;
            background-color: #1a7a5fc7;
            padding:10px 20px;
            border:none;
            border-radius:8px;
            color: #ffffff;
            cursor:pointer;
        }
        .submit:hover{
            background-color: #137657;
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        .submit:active{
            background-color: #104f3d;
            transform: scale(0.95);
            transition: transform 0.5s ease;
        } 
        /* table */
        table{
            border-collapse:collapse;
            background: #ffffff;
            border-radius: 15px;
        }
        td{
            padding:12px 15px;
            text-align:center;
            border-bottom:1px solid #ddd;
        }
    
        th{
            padding:12px 15px;
            text-align:center;
            border-bottom:1px solid #ddd;
            background-color: #ffffff;
            color:black;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        } 
        /* animal list (status green and orange) */
        .status-healthy { color: green; 
        font-family: 'Outfit', sans-serif;
        }
        .status-other { color: orange; ; 
        font-family: 'Outfit', sans-serif;
        }
        /* animal list (array of animals)*/
        .list-animal{
            border-radius:15px;width:340px;
            margin-left:580px;
            margin-top: -668px;
            width: 50%;
            background: #ffffff;
            display:grid;
            grid-template-columns:repeat(1,1fr);
            padding:20px;
            gap:15px;
            box-shadow:0px 0px 50px rgba(0,0,0,0.1);
        }
        /* edit and delete and adopt*/
        .delete a{
            text-decoration:none;
            color: #ffffff;
        }
        .edit a{
            text-decoration:none;
            color: #000000;
        }
        .adopt a{
            text-decoration:none;
            color: #ffffff;
        }
        .edit{
            border: none;
            color: #000000;
            width: 100%;
            height: 30px;
            text-decoration:none;
            background-color: #cecece;
            border-radius: 8px;
        }
        .delete{
            border: none;
            color: #ffffff;
            height: 30px;
            width: 100%;
            text-decoration:none;
            background-color: #ef4444;
            border-radius: 8px;
        }
        .adopt{
            margin-bottom: 5px;
            cursor:pointar;
            border: none;
            color: #ffffff;
            width: 100%;
            height: 40px;
            text-decoration:none;
            background-color: #1a7a5e;
            border-radius: 8px;
        }
        .edit:hover{
            background-color: #a3a3a3;
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        .delete:hover{
            background-color: #ef4444;
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        .adopt:hover{
            background-color: #104f3d;
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        .edit-remove{
            width: 100%;
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        /* photo of animal*/
        img{
            width: 100px;
            height: 100px;
            border-radius: 25%;
        }
        /*for adoption form*/
        .modal{
            display:none;
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.5);
            justify-content:center;
            align-items:center;
            backdrop-filter:blur(3px);
            animation: popup 0.3s ease;
        }
        @keyframes popup {
            0%{
                transform: scale(1.3);
                opacity:0;
            }
            100%{
                transform: scale(1);
                opacity:1;
            }
        }
        .adopter_name{
            margin-bottom: 20px;
        }
        .adopter_phone{
            margin-bottom: 20px;
        }
        .adopter_address{
            margin-bottom: 15px;
        }
        .modal-content{
            background:white;
            padding:20px 40px 20px 40px;
            border-radius:10px;
            width:350px;
        }
        .modal button{
            margin-top: 20px;
        }
        .cancel{
            background-color: #666;
            border: none;
            color: #ffffff;
            width: 25%;
            height: 30px;
            text-decoration:none;
            border-radius: 8px;
            margin-left: 75%;
        }
        .cancel:hover{
            background-color: red;
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

    </style>
</head>
<body>
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
                <td><?php echo $species; ?><br><?php echo $color; ?>
                <br><?php echo $age; ?><br><?php echo $gender; ?></td>
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
                     <form action="dashboard.php" method="POST" >
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="delete">Remove</button>
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
            <input type="hidden" name="action" value="adopt_confirm">
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




</body>
</html>