<?php
include 'contected.php';

// Ensure the POST data is properly retrieved, sanitized, and assigned to variables
$id = isset($_POST['skillId']) ? intval($_POST['skillId']) : 0;
$name = isset($_POST['skillName']) ? htmlspecialchars(trim($_POST['skillName']), ENT_QUOTES, 'UTF-8') : '';
$shortDescription = isset($_POST['shortDescription']) ? htmlspecialchars(trim($_POST['shortDescription']), ENT_QUOTES, 'UTF-8') : '';
$detailedDescription = isset($_POST['detailedDescription']) ? htmlspecialchars(trim($_POST['detailedDescription']), ENT_QUOTES, 'UTF-8') : '';
$currentImagePath = isset($_POST['currentSkillImagePath']) ? htmlspecialchars($_POST['currentSkillImagePath'], ENT_QUOTES, 'UTF-8') : '';

// Default to the current image path if no new image is uploaded
$imagePath = $currentImagePath; 

// Handle the image upload process (if any)
if (isset($_FILES['skillImage']) && $_FILES['skillImage']['error'] === UPLOAD_ERR_OK) {
    $uploadedImage = $_FILES['skillImage'];
    $imageExtension = pathinfo($uploadedImage['name'], PATHINFO_EXTENSION);
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    // Validate the file extension
    if (in_array(strtolower($imageExtension), $allowedExtensions)) {
        // Generate a unique image filename
        $imagePath = 'cvAdmin/skill_images/' . uniqid('skill_', true) . '.' . $imageExtension;

        // Create the directory if it doesn't exist
        if (!is_dir(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0777, true);
        }

        // Move the uploaded file to the desired directory
        if (!move_uploaded_file($uploadedImage['tmp_name'], $imagePath)) {
            throw new Exception('Failed to upload the image.');
        }
    } else {
        throw new Exception('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');
    }
}

// Perform the database update
try {
    // SQL query to update the skill in the database
    $query = "
        UPDATE skills 
        SET name = :name, 
            image_path = :imagePath, 
            short_description = :shortDescription, 
            detailed_description = :detailedDescription
        WHERE id = :id
    ";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':name' => $name,
        ':imagePath' => $imagePath,
        ':shortDescription' => $shortDescription,
        ':detailedDescription' => $detailedDescription,
        ':id' => $id,
    ]);

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Skill updated successfully.']);
} catch (PDOException $e) {
    // Return error if something goes wrong with the query
    echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Return general error for image upload or other issues
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    // Ensure the database connection is closed
    $pdo = null;  // Close the connection
}
?>
