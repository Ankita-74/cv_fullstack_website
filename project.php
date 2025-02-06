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
                    <h2>List of Projects</h2>
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                        ADD Project
                    </button>

<?php
include 'contected.php';

// SQL query to fetch data from the 'projects' table
$sql = "SELECT id, name, description, technologies, github_link, image_path FROM projects";
$result = $conn->query($sql);

// Check if any data was fetched
$projectData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projectData[] = $row;
    }
} else {
    $projectData = null;
}

// Close the database connection
$conn->close();
?>

<!-- Table displaying project data -->
<div class="container mt-4">
    <?php if ($projectData): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>GitHub Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="projectTableBody">
                <?php foreach ($projectData as $project): ?>
                    <tr data-index="<?= htmlspecialchars($project['id']) ?>">
                        <td><?= htmlspecialchars($project['id']) ?></td>
                        <td><?= htmlspecialchars($project['name']) ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($project['image_path']) ?>" 
                                 class="img-thumbnail" 
                                 alt="<?= htmlspecialchars($project['name']) ?>" 
                                 width="75">
                        </td>
                        <td><?= htmlspecialchars($project['description']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($project['github_link']) ?>" 
                               target="_blank" rel="noopener noreferrer">View</a>
                        </td>
                        <td>
                                                <button 
                            type="button" 
                            class="btn btn-primary editButton" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editProjectModal" 
                            data-id="<?= htmlspecialchars($project['id']) ?>" 
                            data-name="<?= htmlspecialchars($project['name']) ?>" 
                            data-description="<?= htmlspecialchars($project['description']) ?>" 
                            data-link="<?= htmlspecialchars($project['github_link']) ?>" 
                            data-image="<?= htmlspecialchars($project['image_path']) ?>"
                        >
                            Edit
                        </button>
                        <button type="button" class="btn btn-danger deleteButton" 
        data-id="<?= htmlspecialchars($project['id']) ?>" 
        data-bs-toggle="modal" 
        data-bs-target="#deleteModal">
    Delete
</button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-danger">No projects found.</p>
    <?php endif; ?>
</div>

 <!-- Add Project Modal -->
 <?php
include 'contected.php'; // Ensure database connection file is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the AJAX request
    $name = trim($_POST['projectName'] ?? '');
    $description = trim($_POST['projectDescription'] ?? '');
    $technologies = trim($_POST['projectTechnologies'] ?? '');
    $github_link = trim($_POST['projectLink'] ?? '');
    $image_path = '';

    if (empty($name) || empty($description) || empty($technologies) || empty($github_link)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Handle image upload
    if (isset($_FILES['projectImage']) && $_FILES['projectImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        $fileName = uniqid() . '_' . basename($_FILES['projectImage']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['projectImage']['tmp_name'], $uploadFile)) {
            $image_path = $uploadFile;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed or no file uploaded.']);
        exit;
    }

    // Insert data into the database
    $sql = "INSERT INTO projects (name, description, technologies, github_link, image_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('sssss', $name, $description, $technologies, $github_link, $image_path);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Project added successfully.']);
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
?>
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProjectForm" enctype="multipart/form-data" novalidate>
                        <div class="mb-3">
                            <label for="projectName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="projectName" name="projectName" required minlength="3">
                            <div class="invalid-feedback">Please enter a project name (at least 3 characters).</div>
                        </div>
                        <div class="mb-3">
                            <label for="projectImage" class="form-label">Image</label>
                            <input type="file" class="form-control" id="projectImage" name="projectImage" accept="image/*" required>
                            <div class="invalid-feedback">Please upload an image for the project.</div>
                        </div>
                        <div class="mb-3">
                            <label for="projectDescription" class="form-label">Description</label>
                            <input type="text" class="form-control" id="projectDescription" name="projectDescription" required minlength="10">
                            <div class="invalid-feedback">Please provide a description (at least 10 characters).</div>
                        </div>
                        <div class="mb-3">
                            <label for="projectTechnologies" class="form-label">Technologies</label>
                            <input type="text" class="form-control" id="projectTechnologies" name="projectTechnologies" required>
                            <div class="invalid-feedback">Please enter the technologies used.</div>
                        </div>
                        <div class="mb-3">
                            <label for="projectLink" class="form-label">GitHub Link</label>
                            <input type="url" class="form-control" id="projectLink" name="projectLink" required pattern="https?://.+">
                            <div class="invalid-feedback">Please enter a valid URL, starting with http:// or https://.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addProjectButton">Add Project</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for handling form submission
        document.getElementById("addProjectButton").addEventListener("click", () => {
            const form = document.getElementById("addProjectForm");
            if (form.checkValidity()) {
                const formData = new FormData(form);

                fetch('', { // Same file
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        location.reload(); // Reload page on success
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("skill added successfully");
                location.reload();
                });
            } else {
                form.classList.add("was-validated");
            }
        });
    </script>







  <!-- Edit Project Modal -->
  <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProjectForm" enctype="multipart/form-data">
                    <input type="hidden" id="projectId" name="projectId">
                    <input type="hidden" id="currentImagePath" name="currentImagePath">

                    <div class="mb-3">
                        <label for="editProjectName" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editProjectName" name="projectName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProjectDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editProjectDescription" name="projectDescription" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="projectTechnologies" class="form-label">Technologies</label>
                        <input type="text" class="form-control" id="projectTechnologies" name="projectTechnologies" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProjectLink" class="form-label">GitHub Link</label>
                        <input type="url" class="form-control" id="editProjectLink" name="projectLink" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProjectImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="editProjectImage" name="projectImage" accept="image/*">
                        <img id="editProjectImagePreview" class="img-thumbnail mt-2" width="100">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editProjectButton">Save Changes</button>
            </div>
        </div>
    </div>
</div>

                <script>
  document.addEventListener("DOMContentLoaded", () => {
    // Open edit modal and populate fields
    document.querySelectorAll(".editButton").forEach(button => {
        button.addEventListener("click", function () {
            const projectId = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");
            const description = this.getAttribute("data-description");
            const technologies = this.getAttribute("data-technologies");
            const githubLink = this.getAttribute("data-link");
            const imagePath = this.getAttribute("data-image");

            document.getElementById("projectId").value = projectId;
            document.getElementById("editProjectName").value = name;
            document.getElementById("editProjectDescription").value = description;
            document.getElementById("projectTechnologies").value = technologies;
            document.getElementById("editProjectLink").value = githubLink;
            document.getElementById("currentImagePath").value = imagePath;
            document.getElementById("editProjectImagePreview").src = imagePath || "default_image.jpg";
        });
    });

    // Save changes
    document.getElementById("editProjectButton").addEventListener("click", () => {
        const form = document.getElementById("editProjectForm");
        const formData = new FormData(form);
        formData.append("action", "update");

        fetch('update_project.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload();
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

// Handle the deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $project_id = $_POST['projectId'];

    // Debugging: Output the received project ID
    error_log("Received project ID: $project_id");

    // Check if the project ID is empty
    if (empty($project_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Project ID is required.']);
        exit;
    }

    // SQL query to delete the project
    $sql = "DELETE FROM projects WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $project_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Project deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete project from the database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed.']);
    }

    $conn->close();
    exit;
}
?>

<!-- Modal HTML -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this project?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    let deleteProjectId = null;

    // Handle click on delete buttons
    document.querySelectorAll(".deleteButton").forEach(button => {
        button.addEventListener("click", function () {
            deleteProjectId = this.getAttribute("data-id"); // Get the project ID from the button
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show(); // Show the modal
        });
    });

    // Handle confirmation of deletion
    document.getElementById("confirmDeleteButton").addEventListener("click", () => {
        if (deleteProjectId) {
            const formData = new FormData();
            formData.append("action", "delete");
            formData.append("projectId", deleteProjectId);

            // Send the request to the current PHP file
            fetch(window.location.href, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
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
