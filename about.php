<?php include 'session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <script src="application.js"></script>
    <?php
     include 'header.php'; // Include the header file 
?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            <!-- ====simle navigation========= -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Edit Details</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">
    <li class="nav-item">
        <a class="nav-link active" href="index.php">Home</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="about.php">About</a>
    </li>
</ul>

    </div>
  </div>
</nav>
<div class="container mt-5">
<div class="mt-4">

<!-- ==============About Section============== -->
<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "cv";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch data from the database
$query = "SELECT * FROM about WHERE id = 1"; // Assuming you're editing the record with ID 1
$stmt = $pdo->prepare($query);
$stmt->execute();
$aboutData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aboutData) {
    die("No record found for the given ID.");
}

$title = $aboutData['title'];
$professionalLife = $aboutData['professional_life'];
$personalLife = $aboutData['personal_life'];
$resumeLink = $aboutData['resume_link']; // Default resume link
$imagePath = $aboutData['image_path'];   // Default image path
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $title = isset($_POST['editAboutTitle']) ? htmlspecialchars(trim($_POST['editAboutTitle'])) : $title;
    $professionalLife = isset($_POST['editProfessionalLife']) ? htmlspecialchars(trim($_POST['editProfessionalLife'])) : $professionalLife;
    $personalLife = isset($_POST['editPersonalLife']) ? htmlspecialchars(trim($_POST['editPersonalLife'])) : $personalLife;

    // Define the base directory where images will be stored
$imageBaseDir = 'cvAdmin/image/';

// Check if the folder exists, if not, create it
if (!is_dir($imageBaseDir)) {
    mkdir($imageBaseDir, 0777, true);
}

// Handle image upload
if (isset($_FILES['editAboutImage']) && $_FILES['editAboutImage']['error'] === UPLOAD_ERR_OK) {
    $uploadedImage = $_FILES['editAboutImage'];
    $imageName = basename($uploadedImage['name']);
    $imagePath = $imageBaseDir . $imageName;

    // Move the uploaded image to the correct directory
    move_uploaded_file($uploadedImage['tmp_name'], $imagePath);

    // Save the relative path for the database
    $imagePath = 'image/' . $imageName; // Save the relative path (e.g., 'image/bc.jpg')
}

// Handle resume upload
if (isset($_FILES['editResumeLink']) && $_FILES['editResumeLink']['error'] === UPLOAD_ERR_OK) {
    $uploadedResume = $_FILES['editResumeLink'];
    $resumeLink = 'cvAdmin/cv/' . basename($uploadedResume['name']);

    // Check if the folder exists, if not, create it
    if (!is_dir(dirname($resumeLink))) {
        mkdir(dirname($resumeLink), 0777, true);
    }

    move_uploaded_file($uploadedResume['tmp_name'], $resumeLink);
}

    try {
        // Update query with relative path for image
        $updateQuery = "
            UPDATE about 
            SET title = :title, 
                professional_life = :professional_life, 
                personal_life = :personal_life, 
                resume_link = :resume_link, 
                image_path = :image_path
            WHERE id = :id
        ";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([
            ':title' => $title,
            ':professional_life' => $professionalLife,
            ':personal_life' => $personalLife,
            ':resume_link' => $resumeLink,
            ':image_path' => $imagePath, // Store the relative path
            ':id' => $aboutData['id']
        ]);
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}

?>

<!-- HTML Form for Editing -->
<section id="about" class="section">
    <h5>Edit About Section</h5>
    <form action="" method="POST" enctype="multipart/form-data" id="editAboutForm" class="needs-validation" novalidate>
        <div class="row">
            <!-- Left Column: Form Fields -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editAboutTitle" class="form-label">Title</label>
                    <input type="text" id="editAboutTitle" name="editAboutTitle" class="form-control" 
                           value="<?= htmlspecialchars($title) ?>" required>
                    <div class="invalid-feedback">Please enter a title.</div>
                </div>
                <div class="mb-3">
                    <label for="editProfessionalLife" class="form-label">Professional Life</label>
                    <textarea id="editProfessionalLife" name="editProfessionalLife" class="form-control" rows="5" required><?= htmlspecialchars($professionalLife) ?></textarea>
                    <div class="invalid-feedback">Please enter your professional life description.</div>
                </div>
                <div class="mb-3">
                    <label for="editPersonalLife" class="form-label">Personal Life</label>
                    <textarea id="editPersonalLife" name="editPersonalLife" class="form-control" rows="5" required><?= htmlspecialchars($personalLife) ?></textarea>
                    <div class="invalid-feedback">Please enter your personal life description.</div>
                </div>
            </div>
            <!-- Right Column: Image and Resume Upload -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="editAboutImage" class="form-label">Profile Image</label>
                    <input type="file" id="editAboutImage" name="editAboutImage" class="form-control" accept="image/*">
                    <div class="mt-2">
                        <!-- If no image is uploaded, show the default image -->
                        <img id="aboutImagePreview" src="<?= htmlspecialchars($imagePath) ?>" 
                            alt="Profile Image" class="img-fluid" style="max-width: 100%; max-height: 200px;">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="editResumeLink" class="form-label">Resume File</label>
                    <input type="file" id="editResumeLink" name="editResumeLink" class="form-control" accept=".pdf">
                    <div class="mt-2">
                        <!-- If no resume is uploaded, provide a link to the default resume -->
                        <a href="<?= htmlspecialchars($resumeLink) ?>" target="_blank">View Current Resume</a>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
</section>


<br>
<br>

<script>
  // Client-side validation
(function () {
    'use strict';
    window.addEventListener('load', function () {
        const forms = document.getElementsByClassName('needs-validation');
        Array.prototype.forEach.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Image Preview for Home Section
function handleImagePreview(inputId, previewId) {
    document.getElementById(inputId).addEventListener('change', function (event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            document.getElementById(previewId).src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    });
}

// Trigger Image Upload for Home Section
function triggerFileInput(buttonId, inputId) {
    document.getElementById(buttonId).addEventListener('click', function () {
        document.getElementById(inputId).click();
    });
}

// Initialize Image Preview and Button for Home and About Sections
handleImagePreview('editImage', 'imagePreview');
handleImagePreview('editAboutImage', 'aboutImagePreview');
triggerFileInput('changeImageBtn', 'editImage');
triggerFileInput('changeAboutImageBtn', 'editAboutImage');

// Form Validation for Home Section
function handleFormValidation(formId, successMessage) {
    document.getElementById(formId).addEventListener('submit', function (event) {
        event.preventDefault();
        if (this.checkValidity() === false) {
            event.stopPropagation();
        } else {
            alert(successMessage);
        }
        this.classList.add('was-validated');
    });
}

// Initialize Form Validation for Home, About, and PDF Sections
handleFormValidation('editHomeForm', 'Home section updated successfully!');
handleFormValidation('editAboutForm', 'About Me section updated successfully!');
handleFormValidation('editPdfForm', 'PDF uploaded successfully!');

</script>
<?php
   include 'footer.php'; // Include the header file
 ?>

</body>
</html>
