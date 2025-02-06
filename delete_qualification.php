<?php
// Assuming you are using a database connection already
include 'contected.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $qualificationId = $_POST['id'];

    // Prepare and execute SQL to delete qualification
    $sql = "DELETE FROM qualifications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $qualificationId);

    if ($stmt->execute()) {
        // Return success as JSON
        echo json_encode(["success" => true]);
    } else {
        // Return error as JSON
        echo json_encode(["success" => false]);
    }

    $stmt->close();
    $conn->close();
}
?>
