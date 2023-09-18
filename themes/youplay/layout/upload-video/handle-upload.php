<?php
// Include your database connection config
require_once('../config.php');
echo $sql_db_user;

// Function to move uploaded files to the correct folders
function moveUploadedFile($file, $targetFolder) {
    if (move_uploaded_file($file['tmp_name'], $targetFolder . '/' . $file['name'])) {
        return true;
    } else {
        return false;
    }
}

// Get the form ID to determine the type of upload
$formId = isset($_POST['form_id']) ? $_POST['form_id'] : '';


// Define folder paths for different types of uploads
$uploadPaths = array(
    'video-upload' => 'upload/videos',
    'gallery-upload' => 'upload/photos',
    'music-upload' => 'upload/music'
);

// Check if the form ID corresponds to a valid upload type
if (isset($uploadPaths[$formId])) {
    $targetFolder = $uploadPaths[$formId];

    // Check if the target folder exists, create it if not
    if (!is_dir($targetFolder)) {
        mkdir($targetFolder, 0755, true);
    }

    // Process and move uploaded files
    if (isset($_FILES['video-input'])) {
        if (moveUploadedFile($_FILES['video-input'], $targetFolder)) {
            // File uploaded successfully
            // You can perform database operations here if needed
            $response = array("success" => true);
        } else {
            // File upload failed
            $response = array("success" => false);
        }
    } else {
        // No files uploaded
        $response = array("success" => false);
    }
} else {
    // Invalid form ID
    $response = array("success" => false);
}

// Set the content type to JSON
header('Content-Type: application/json');

// Output the JSON response
echo json_encode($response);

// Close the database connection (you can adapt this if needed)
$pdo = null;
?>
