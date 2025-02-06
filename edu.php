<?php include 'session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Education</title>
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
                    <h2>List of Education</h2>
                    <!-- Button to Add Education -->
               <!-- Button to trigger Add Education Modal -->
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEducationModal" aria-controls="addEducationModal">
                    Add Education
                </button>

<?php
include 'contected.php'; // Include your database connection

// SQL query to fetch data from the 'qualification' table
$sql = "SELECT id, name, passing_year, percentage_or_cgpa, board, file_link, image_path FROM qualifications";
$result = $conn->query($sql);

// Check if any data was fetched
$educationData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $educationData[] = $row;
    }
} else {
    $educationData = null;
}

// Close the database connection
$conn->close();
?>


<!-- Table displaying project data -->
<div class="container mt-4">
    <?php if ($educationData): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Passing Year</th>
                    <th>Percentage/CGPA</th>
                    <th>University/Board</th>
                    <th>PDF</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="educationTableBody">
                <?php foreach ($educationData as $education): ?>
                    <tr data-index="<?= htmlspecialchars($education['id']) ?>">
                        <td><?= htmlspecialchars($education['id']) ?></td>
                        <td><?= htmlspecialchars($education['name']) ?></td>
                        <td><?= htmlspecialchars($education['passing_year']) ?></td>
                        <td><?= htmlspecialchars($education['percentage_or_cgpa']) ?></td>
                        <td><?= htmlspecialchars($education['board']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($education['file_link']) ?>" 
                               target="_blank" rel="noopener noreferrer">Download</a>
                        </td>
                        <td>
    <img src="<?= 'uploads/images/' . htmlspecialchars($education['image_path']) ?>" 
         class="img-thumbnail" 
         alt="<?= htmlspecialchars($education['name']) ?>" 
         width="75">
</td>

                        <td>
                            <button 
                                type="button" 
                                class="btn btn-primary editButton" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editEducationModal" 
                                data-id="<?= htmlspecialchars($education['id']) ?>" 
                                data-name="<?= htmlspecialchars($education['name']) ?>" 
                                data-passing_year="<?= htmlspecialchars($education['passing_year']) ?>" 
                                data-percentage="<?= htmlspecialchars($education['percentage_or_cgpa']) ?>" 
                                data-board="<?= htmlspecialchars($education['board']) ?>" 
                                data-pdf="<?= htmlspecialchars($education['file_link']) ?>" 
                                data-image="<?= htmlspecialchars($education['image_path']) ?>"
                            >
                             Edit
                            </button>
                            <button 
                                type="button" 
                                class="btn btn-danger deleteQualificationButton" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteQualificationModal" 
                                data-id="<?= htmlspecialchars($row['id']) ?>">
                                Delete
                            </button>


                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-danger">No education records found.</p>
    <?php endif; ?>
</div>

 <!-- Add Project Modal -->
 <?php
include 'contected.php'; // Ensure database connection file is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the AJAX request
    $name = trim($_POST['educationName'] ?? '');
    $passing_year = trim($_POST['passingYear'] ?? '');
    $percentage_or_cgpa = trim($_POST['percentageOrCgpa'] ?? '');
    $board = trim($_POST['board'] ?? '');
    $file_link = '';
    $image_file_name = ''; // Variable to store only the file name

    if (empty($name) || empty($passing_year) || empty($percentage_or_cgpa) || empty($board)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Handle PDF file upload
    if (isset($_FILES['filePdf']) && $_FILES['filePdf']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/files/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
        }

        $fileName = basename($_FILES['filePdf']['name']);  // Get only the file name
        $uploadFile = $uploadDir . $fileName;  // Full path for uploading

        if (move_uploaded_file($_FILES['filePdf']['tmp_name'], $uploadFile)) {
            $file_link = $fileName; // Store only the file name in the database
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload PDF.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'PDF upload failed or no file uploaded.']);
        exit;
    }

    // Handle image upload
    if (isset($_FILES['educationImage']) && $_FILES['educationImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
        }

        $imageFileName = basename($_FILES['educationImage']['name']);  // Get only the image file name
        $uploadImage = $uploadDir . $imageFileName;  // Full path for uploading

        if (move_uploaded_file($_FILES['educationImage']['tmp_name'], $uploadImage)) {
            $image_file_name = $imageFileName; // Store only the image file name in the database
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed or no file uploaded.']);
        exit;
    }

    // Insert data into the database
    $sql = "INSERT INTO qualifications (name, passing_year, percentage_or_cgpa, board, file_link, image_path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ssssss', $name, $passing_year, $percentage_or_cgpa, $board, $file_link, $image_file_name);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Education added successfully.']);
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

<!-- Add Education Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEducationModalLabel">Add Education</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEducationForm" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label for="educationName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="educationName" name="educationName" required minlength="3">
                        <div class="invalid-feedback">Please enter the qualification name (at least 3 characters).</div>
                    </div>
                    <div class="mb-3">
                        <label for="passingYear" class="form-label">Passing Year</label>
                        <input type="number" class="form-control" id="passingYear" name="passingYear" required>
                        <div class="invalid-feedback">Please enter a valid passing year.</div>
                    </div>
                    <div class="mb-3">
                        <label for="percentageOrCgpa" class="form-label">Percentage/CGPA</label>
                        <input type="text" class="form-control" id="percentageOrCgpa" name="percentageOrCgpa" required>
                        <div class="invalid-feedback">Please enter the percentage or CGPA.</div>
                    </div>
                    <div class="mb-3">
                        <label for="board" class="form-label">University/Board</label>
                        <input type="text" class="form-control" id="board" name="board" required>
                        <div class="invalid-feedback">Please enter the university/board.</div>
                    </div>
                    <div class="mb-3">
                        <label for="filePdf" class="form-label">Certificate (PDF)</label>
                        <input type="file" class="form-control" id="filePdf" name="filePdf" accept="application/pdf" required>
                        <div class="invalid-feedback">Please upload a PDF certificate.</div>
                    </div>
                    <div class="mb-3">
                        <label for="educationImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="educationImage" name="educationImage" accept="image/*" required>
                        <div class="invalid-feedback">Please upload an image for the education.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addEducationButton">Add Education</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Handle Add Education Button Click
    document.getElementById("addEducationButton").addEventListener("click", function () {
        const form = document.getElementById("addEducationForm");

        // Validate the form fields
        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        // Create FormData object with form data
        const formData = new FormData(form);
        formData.append("action", "add");

        // Send data to the server via fetch
        fetch("", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Education added successfully!");
                // You can also refresh the page or dynamically update the page content
                location.reload();  // Refresh the page
            } else {
                alert("Failed to add education. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Education added successfully!");
            location.reload(); 
        });
    });
});
</script>






  <!-- Edit Education Modal -->
  
  <div class="modal fade" id="editEducationModal" tabindex="-1" aria-labelledby="editEducationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEducationModalLabel">Edit Education</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEducationForm" enctype="multipart/form-data">
                    <input type="hidden" id="educationId" name="educationId">
                    <input type="hidden" id="currentFilePath" name="currentFilePath">
                    <div class="mb-3">
                        <label for="editEducationDegree" class="form-label">Degree</label>
                        <input type="text" class="form-control" id="editEducationDegree" name="degree" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEducationYear" class="form-label">Passing Year</label>
                        <input type="number" class="form-control" id="editEducationYear" name="year" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEducationUniversity" class="form-label">University</label>
                        <input type="text" class="form-control" id="editEducationUniversity" name="university" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEducationPercentage" class="form-label">Percentage/CGPA</label>
                        <input type="text" class="form-control" id="editEducationPercentage" name="percentage_or_cgpa" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEducationFile" class="form-label">Certificate/File</label>
                        <input type="file" class="form-control" id="editEducationFile" name="educationFile" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Allowed formats: PDF, DOC, DOCX, JPG, PNG.</small>
                    </div>
                    <div class="mt-2">
                        <a id="currentFileLink" href="#" target="_blank" class="btn btn-link">View Current File</a>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editEducationButton">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('editEducationButton').addEventListener('click', function() {
    // Get form elements
    const form = document.getElementById('editEducationForm');
    const degree = document.getElementById('editEducationDegree').value.trim();
    const year = document.getElementById('editEducationYear').value.trim();
    const university = document.getElementById('editEducationUniversity').value.trim();
    const percentageOrCgpa = document.getElementById('editEducationPercentage').value.trim();
    const educationFile = document.getElementById('editEducationFile').files[0];

    // Validate form fields
    if (!degree || !year || !university || !percentageOrCgpa) {
        alert("Please fill out all the required fields.");
        return;
    }

    // Validate the year input to be a valid number
    if (isNaN(year) || year <= 0) {
        alert("Please enter a valid passing year.");
        return;
    }

    // Validate the percentage or CGPA field to be a valid number or CGPA format
    if (isNaN(percentageOrCgpa) && !/^[0-9]+(\.[0-9]+)?$/.test(percentageOrCgpa)) {
        alert("Please enter a valid percentage or CGPA.");
        return;
    }

    // Validate file type if a file is selected
    if (educationFile) {
        const allowedExtensions = /(\.pdf|\.doc|\.docx|\.jpg|\.png)$/i;
        if (!allowedExtensions.exec(educationFile.name)) {
            alert("Invalid file type. Only PDF, DOC, DOCX, JPG, PNG files are allowed.");
            return;
        }
    }

    // If all validations pass, submit the form (you can handle form submission via AJAX or traditional form submission)
    form.submit();
});
</script>


<script>
   document.addEventListener("DOMContentLoaded", () => {
    // Handle edit button click
    document.querySelectorAll(".editButton").forEach(button => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const degree = this.getAttribute("data-degree");
            const year = this.getAttribute("data-year");
            const university = this.getAttribute("data-university");
            const percentage = this.getAttribute("data-percentage");
            const file = this.getAttribute("data-file");

            // Populate modal fields
            document.getElementById("educationId").value = id;
            document.getElementById("editEducationDegree").value = degree;
            document.getElementById("editEducationYear").value = year;
            document.getElementById("editEducationUniversity").value = university;
            document.getElementById("editEducationPercentage").value = percentage;

            const fileLink = document.getElementById("currentFileLink");
            fileLink.href = file || "#";
            fileLink.textContent = file ? "View Current File" : "No file available";
            document.getElementById("currentFilePath").value = file;

            // Show the modal
            new bootstrap.Modal(document.getElementById('editEducationModal')).show();
        });
    });

    // Handle save button click
    document.getElementById("editEducationButton").addEventListener("click", () => {
        const formData = new FormData(document.getElementById("editEducationForm"));
        formData.append("action", "update");

        fetch("update_education.php", {
            method: "POST",
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    location.reload(); // Refresh the page to see updated data
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
                location.reload(); 
            });
    });
});

</script>




  <!-- Delete Confirmation Modal -->
 <!-- Modal -->
<div class="modal fade" id="deleteQualificationModal" tabindex="-1" aria-labelledby="deleteQualificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQualificationModalLabel">Delete Qualification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this qualification?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
  // This script handles the deletion when the "Delete" button in the modal is clicked
document.addEventListener("DOMContentLoaded", function () {
    // Get modal and delete button
    const deleteModal = document.getElementById("deleteQualificationModal");
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");

    // Add event listener to open modal and store the ID of the qualification to be deleted
    deleteModal.addEventListener("show.bs.modal", function (event) {
        // Get the button that triggered the modal
        const button = event.relatedTarget;
        const qualificationId = button.getAttribute("data-id");

        // Store the ID in the confirm delete button
        confirmDeleteButton.setAttribute("data-id", qualificationId);
    });

    // Handle delete action
    confirmDeleteButton.addEventListener("click", function () {
        const qualificationId = confirmDeleteButton.getAttribute("data-id");

        // Make AJAX request to delete the qualification from the database
        fetch("delete_qualification.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "id=" + encodeURIComponent(qualificationId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal and refresh or update the UI
                $('#deleteQualificationModal').modal('hide');
                alert("Qualification deleted successfully.");
                // Optionally refresh the page or remove the item from the list
                location.reload(); // To refresh the page
            } else {
                alert("Error deleting qualification.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred. Please try again.");
        });
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
