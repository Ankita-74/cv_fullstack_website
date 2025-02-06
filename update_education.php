<?php
include 'contected.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        // Validate and sanitize inputs
        $id = intval($_POST['educationId']);
        $degree = htmlspecialchars(trim($_POST['degree']), ENT_QUOTES, 'UTF-8');
        $year = intval($_POST['year']);
        $university = htmlspecialchars(trim($_POST['university']), ENT_QUOTES, 'UTF-8');
        $percentage = htmlspecialchars(trim($_POST['percentage_or_cgpa']), ENT_QUOTES, 'UTF-8');
        $currentFilePath = htmlspecialchars($_POST['currentFilePath'], ENT_QUOTES, 'UTF-8');
        $filePath = $currentFilePath; // Default to existing file

        // Handle file upload securely
        if (isset($_FILES['educationFile']) && $_FILES['educationFile']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['educationFile'];
            $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

            // Validate file extension
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                $filePath = 'cvAdmin/education_files/' . uniqid('file_', true) . '.' . $fileExtension;

                // Create directory if it doesn't exist
                if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 0777, true);
                }

                // Move the uploaded file
                if (!move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
                    throw new Exception('Failed to upload the file.');
                }
            } else {
                throw new Exception('Invalid file type. Only PDF, DOC, DOCX, JPG, and PNG are allowed.');
            }
        }

        // Update the database
        $query = "
            UPDATE qualifications 
            SET name = :degree, 
                passing_year = :year, 
                board = :university, 
                percentage_or_cgpa = :percentage, 
                file_link = :filePath 
            WHERE id = :id
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':degree' => $degree,
            ':year' => $year,
            ':university' => $university,
            ':percentage' => $percentage,
            ':filePath' => $filePath,
            ':id' => $id,
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Education updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
