<?php
include 'contected.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $project_id = $_POST['projectId'];
    $name = $_POST['projectName'];
    $description = $_POST['projectDescription'];
    $technologies = $_POST['projectTechnologies'];
    $github_link = $_POST['projectLink'];
    $current_image_path = $_POST['currentImagePath'];

    // Validate input
    if (empty($project_id) || empty($name) || empty($description) || empty($github_link) || empty($technologies)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Handle image upload
    $image_path = $current_image_path;
    if (isset($_FILES['projectImage']) && $_FILES['projectImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['projectImage']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['projectImage']['tmp_name'], $uploadFile)) {
            $image_path = $uploadFile;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
            exit;
        }
    }

    // Update the database
    $sql = "UPDATE projects 
            SET name = ?, 
                description = ?, 
                technologies = ?, 
                github_link = ?, 
                image_path = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('sssssi', $name, $description, $technologies, $github_link, $image_path, $project_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Project updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
