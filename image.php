<?php

require_once 'connection.php';

$imagePath = $_GET['url'];

// Get the image content
$imageContent = file_get_contents($imagePath);

// Check if the image content was successfully read
if ($imageContent !== false) {
    // Get the MIME type of the image
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($imageContent);

    // Set the Content-Type header based on the MIME type
    header("Content-Type: $mime");

    // Output the image content
    echo $imageContent;
} else {
    // Handle the case where the image content could not be read
    echo 'Failed to read the image file';
}
?>
