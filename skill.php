<?php include 'session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Project</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
<?php
     include 'header.php'; // Include the header file
?>
        <div id="layoutSidenav_content">
            <main>
                <!-- ====button for add ====== -->
                <div class="container my-5">
                    <h2>List of Skill</h2>
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                    ADD Skill
                </button>


                <?php
include 'contected.php'; // Include your database connection

// SQL query to fetch data from the 'skills' table
$sql = "SELECT id, name, image_path, short_description, detailed_description FROM skills";
$result = $conn->query($sql);

// Check if any data was fetched
$skillData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $skillData[] = $row;
    }
} else {
    $skillData = null;
}

// Close the database connection
$conn->close();
?>

<!-- Table displaying skill data -->
<div class="container mt-4">
    <?php if ($skillData): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Short Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="skillTableBody">
                <?php foreach ($skillData as $skill): ?>
                    <tr data-index="<?= htmlspecialchars($skill['id']) ?>">
                        <td><?= htmlspecialchars($skill['id']) ?></td>
                        <td><?= htmlspecialchars($skill['name']) ?></td>
                        <td>
    <img src="<?= htmlspecialchars($skill['image_path']) ?>" 
         class="img-thumbnail" 
         alt="<?= htmlspecialchars($skill['name']) ?>" 
         width="75">
</td>



                        <td><?= htmlspecialchars($skill['short_description']) ?></td>
                        <td>
                            <!-- Edit Button with dynamic data attributes -->
                            <button 
                                class="btn btn-primary editSkillButton"
                                data-id="<?= htmlspecialchars($skill['id']) ?>"
                                data-name="<?= htmlspecialchars($skill['name']) ?>"
                                data-short-description="<?= htmlspecialchars($skill['short_description']) ?>"
                                data-detailed-description="<?= htmlspecialchars($skill['detailed_description']) ?>"
                                data-image="<?= htmlspecialchars($skill['image_path']) ?>">
                                Edit
                            </button>

                            <!-- Delete Button (data-id needs to be dynamic as well) -->
                            <button class="btn btn-danger deleteSkillButton" data-id="<?= htmlspecialchars($skill['id']) ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-danger">No skills found.</p>
    <?php endif; ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
    // Handle Edit Button Click for each row
    document.querySelectorAll('.editSkillButton').forEach(button => {
        button.addEventListener('click', function() {
            // Get skill data from data attributes of the clicked button
            const skillId = this.getAttribute('data-id');
            const skillName = this.getAttribute('data-name');
            const shortDescription = this.getAttribute('data-short-description');
            const detailedDescription = this.getAttribute('data-detailed-description');
            const imagePath = this.getAttribute('data-image');

            // Populate the modal with the skill data
            document.getElementById('skillId').value = skillId;
            document.getElementById('editSkillName').value = skillName;
            document.getElementById('editSkillShortDescription').value = shortDescription;
            document.getElementById('editSkillDetailedDescription').value = detailedDescription;
            document.getElementById('currentSkillImagePath').value = imagePath;
            document.getElementById('editSkillImagePreview').src = imagePath || "default_image.jpg"; // Default image if no path provided

            // Show the modal
            new bootstrap.Modal(document.getElementById('editSkillModal')).show();
        });
    });

    // Save the changes when the 'Save Changes' button is clicked
    document.getElementById('saveSkillChanges').addEventListener('click', () => {
        const form = document.getElementById('editSkillForm');
        const formData = new FormData(form);

        // Send the data to the server
        fetch('update_skill.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Log server response for debugging
            alert(data.message);
            if (data.status === 'success') {
                location.reload(); // Reload the page to reflect changes
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>

 <!-- Add Project Modal -->
 <?php
include 'contected.php'; // Ensure database connection file is included

// Handle the AJAX request to add a skill
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['skillName'] ?? '');
    $short_description = trim($_POST['shortDescription'] ?? '');
    $detailed_description = trim($_POST['detailedDescription'] ?? '');
    $image_path = ''; // Default value for the image path

    // Check if the required fields are provided
    if (empty($name) || empty($short_description) || empty($detailed_description)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Handle image upload
    if (isset($_FILES['skillImage']) && $_FILES['skillImage']['error'] === UPLOAD_ERR_OK) {
        // Define the directory where images will be saved
        $uploadDir = 'image/'; // Directory where images will be stored, relative to cvAdmin folder

        // Check if the directory exists, if not, create it
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        // Generate a unique file name (based on the original file name)
        $fileExtension = pathinfo($_FILES['skillImage']['name'], PATHINFO_EXTENSION); // Get file extension
        $fileName = uniqid('skill_') . '.' . $fileExtension; // Unique file name with extension
        $uploadFile = $uploadDir . $fileName; // Full path for storing the image

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['skillImage']['tmp_name'], $uploadFile)) {
            // Save only the relative file path (image/filename.ext) in the database
            $image_path = $uploadDir . $fileName; // Store as relative path
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed or no file uploaded.']);
        exit;
    }

    // Insert data into the database
    $sql = "INSERT INTO skills (name, short_description, detailed_description, image_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ssss', $name, $short_description, $detailed_description, $image_path);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Skill added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database insertion failed.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query error.']);
    }

    $conn->close();
    exit;
}

// Fetch skills data from the database
$sql = "SELECT id, name, short_description, detailed_description, image_path FROM skills";
$result = $conn->query($sql);
$skillData = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>




<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSkillModalLabel">Add Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSkillForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="skillName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="skillName" name="skillName" required minlength="3">
                        <div class="invalid-feedback">Please enter a skill name (at least 3 characters).</div>
                    </div>
                    <div class="mb-3">
                        <label for="skillImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="skillImage" name="skillImage" accept="image/*" required>
                        <div class="invalid-feedback">Please upload an image for the skill.</div>
                    </div>
                    <div class="mb-3">
                        <label for="shortDescription" class="form-label">Short Description</label>
                        <input type="text" class="form-control" id="shortDescription" name="shortDescription" required minlength="10">
                        <div class="invalid-feedback">Please provide a short description (at least 10 characters).</div>
                    </div>
                    <div class="mb-3">
                        <label for="detailedDescription" class="form-label">Detailed Description</label>
                        <textarea class="form-control" id="detailedDescription" name="detailedDescription" required minlength="15"></textarea>
                        <div class="invalid-feedback">Please provide a detailed description (at least 15 characters).</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addSkillButton">Add Skill</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("addSkillButton").addEventListener("click", () => {
        const form = document.getElementById("addSkillForm");
        if (form.checkValidity()) {
            const formData = new FormData(form);
            formData.append("action", "add");

            fetch("", { // Same file
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    location.reload();
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("skill added successfully");
                location.reload();
            });
        } else {
            form.classList.add("was-validated");
        }
    });
</script>


  <!-- Edit Project Modal -->
  <div class="modal fade" id="editSkillModal" tabindex="-1" aria-labelledby="editSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSkillModalLabel">Edit Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSkillForm" enctype="multipart/form-data">
                    <input type="hidden" id="skillId" name="skillId">
                    <input type="hidden" id="currentSkillImagePath" name="currentSkillImagePath">

                    <div class="mb-3">
                        <label for="editSkillName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editSkillName" name="skillName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editSkillShortDescription" class="form-label">Short Description</label>
                        <input type="text" class="form-control" id="editSkillShortDescription" name="shortDescription" required>
                    </div>
                    <div class="mb-3">
                        <label for="editSkillDetailedDescription" class="form-label">Detailed Description</label>
                        <textarea class="form-control" id="editSkillDetailedDescription" name="detailedDescription" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editSkillImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="editSkillImage" name="skillImage" accept="image/*">
                        <img id="editSkillImagePreview" class="img-thumbnail mt-2" width="100">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSkillChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
    // Handle Edit Button Click
    document.querySelectorAll('.editSkillButton').forEach(button => {
        button.addEventListener('click', function() {
            // Get skill data from data attributes
            const skillId = this.getAttribute('data-id');
            const skillName = this.getAttribute('data-name');
            const shortDescription = this.getAttribute('data-short-description');
            const detailedDescription = this.getAttribute('data-detailed-description');
            const imagePath = this.getAttribute('data-image');

            // Populate modal with skill data
            document.getElementById('skillId').value = skillId;
            document.getElementById('editSkillName').value = skillName;
            document.getElementById('editSkillShortDescription').value = shortDescription;
            document.getElementById('editSkillDetailedDescription').value = detailedDescription;
            document.getElementById('currentSkillImagePath').value = imagePath;
            document.getElementById('editSkillImagePreview').src = imagePath || "default_image.jpg"; // Default if no image

            // Show modal
            new bootstrap.Modal(document.getElementById('editSkillModal')).show();
        });
    });

    // Save the changes
    document.getElementById('saveSkillChanges').addEventListener('click', () => {
        const form = document.getElementById('editSkillForm');
        const formData = new FormData(form);

        // Send the data to the server
        fetch('update_skill.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Log server response for debugging
            alert(data.message);
            if (data.status === 'success') {
                location.reload(); // Reload the page to reflect changes
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});

</script>

  <!-- Delete Confirmation Modal -->
  <?php
include 'contected.php'; // Include your database connection

// Handle the deletion request (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Log POST data for debugging
    error_log("POST data: " . print_r($_POST, true));  // Debugging line
    
    $skill_id = $_POST['skillId'];

    // Debugging: Output the received skill ID
    error_log("Received skill ID: $skill_id");

    // Check if the skill ID is empty
    if (empty($skill_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Skill ID is required.']);
        exit;
    }

    // SQL query to delete the skill
    $sql = "DELETE FROM skills WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $skill_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Skill deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete skill from the database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed.']);
    }

    $conn->close();
    exit;
}
?>

<!-- Modal HTML for Deletion Confirmation -->
<div class="modal fade" id="deleteSkillModal" tabindex="-1" aria-labelledby="deleteSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSkillModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this skill?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSkillButton">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Your other HTML content goes here -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    let deleteSkillId = null;

    // Handle click on delete buttons for skills
    document.querySelectorAll(".deleteSkillButton").forEach(button => {
        button.addEventListener("click", function () {
            deleteSkillId = this.getAttribute("data-id"); // Get the skill ID from the button
            const deleteSkillModal = new bootstrap.Modal(document.getElementById('deleteSkillModal'));
            deleteSkillModal.show(); // Show the modal
        });
    });

    // Handle confirmation of skill deletion
    document.getElementById("confirmDeleteSkillButton").addEventListener("click", () => {
        if (deleteSkillId) {
            const formData = new FormData();
            formData.append("action", "delete");
            formData.append("skillId", deleteSkillId);

            // Send the request to the current PHP file (this will handle the deletion on the same page)
            fetch(window.location.href, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response data:", data);  // Debugging line to log the response
                alert(data.message); // Show success or failure message
                if (data.status === "success") {
                    location.reload(); // Reload the page after successful deletion
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
            });
        }
    });
});

</script>

</main>
 </div>
</div>
<?php
   include 'footer.php'; // Include the header file
 ?>
<script>
    // Add click event listener to all buttons with the class "editButton"
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".editButton").forEach(button => {
            button.addEventListener("click", function () {
                // Retrieve data attributes from the clicked button
                const projectId = this.getAttribute("data-id");
                const projectName = this.getAttribute("data-name");
                const projectDescription = this.getAttribute("data-description");
                const projectLink = this.getAttribute("data-link");
                const projectImage = this.getAttribute("data-image");

                // Populate modal fields with the retrieved data
                document.getElementById("editProjectForm").setAttribute("data-project-id", projectId);
                document.getElementById("editProjectName").value = projectName;
                document.getElementById("editProjectDescription").value = projectDescription;
                document.getElementById("editProjectLink").value = projectLink;
                document.getElementById("editProjectImagePreview").src = projectImage || "default_image.jpg"; // Fallback to default image if none
            });
        });
    });
</script>

</body>
</html>
