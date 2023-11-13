<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $videoForm = $_POST['video_hidden'];
    $galleryForm = $_POST['gallery_hidden'];
    $musicForm = $_POST['music_hidden'];

    if ($videoForm == 1) {
        // Define the target directory for video uploads
        $uploadDir = 'upload/videos/';

        // Create the current year and month directories if they don't exist
        $currentYear = date('Y');
        $currentMonth = date('m');
        $targetDir = $uploadDir . $currentYear . '/' . $currentMonth . '/';

        if (!is_dir($targetDir)) {
            // Create the year directory if it doesn't exist
            if (!is_dir($uploadDir . $currentYear)) {
                mkdir($uploadDir . $currentYear);
            }

            // Create the month directory
            mkdir($targetDir);
        }

        // Handle the video file upload
        $videoFile = $_FILES['video-input'];
        $thumbnailFile = $_FILES['video-thumb-input'];
        $videoTitle = $_POST['video-title-input'];
        $videoDescription = $_POST['video-desc-input'];
        $videoTags = $_POST['video-tag-input'];

        // Generate a unique folder name for this video upload
        $videoFolderName = uniqid();

        // Define the path for the dedicated folder
        $videoFolderPath = $targetDir . $videoFolderName . '/';

        // Create a dedicated folder for this video upload
        mkdir($videoFolderPath);

        // Generate a unique filename for the video and thumbnail
        $videoFileExtension = pathinfo($videoFile['name'], PATHINFO_EXTENSION);
        $thumbnailFileExtension = pathinfo($thumbnailFile['name'], PATHINFO_EXTENSION);

        $videoFileName = uniqid() . '.' . $videoFileExtension;
        $thumbnailFileName = uniqid() . '.' . $thumbnailFileExtension;

        // Define the paths for video and thumbnail within the dedicated folder
        $videoPath = $videoFolderPath . $videoFileName;
        $thumbnailPath = $videoFolderPath . $thumbnailFileName;

        // Move the uploaded files to the dedicated folder
        if (move_uploaded_file($videoFile['tmp_name'], $videoPath) && move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailPath)) {

            $getID3 = new getID3;
            $video_file = $getID3->analyze($videoPath);
            $duration_string = $video_file['playtime_string'];

            $fields = array(
                'user_id' => $pt->user->id,
                'video_id' => $videoFolderName, // Use the folder name as video_id
                'title' => $_POST['video-title-input'],
                'description' => $_POST['video-desc-input'],
                'thumbnail' => $thumbnailPath, // Store the thumbnail path
                'video_location' => $videoPath, // Store the video path
                'size' => filesize($videoPath), // Get the video file size in bytes
                'duration' => $duration_string,
                'file_type' => 'video',
                'time' => time()
            );

            // Insert the data into the 'videos' table
            $videoSave = $db->insert('videos', $fields);
            if ($videoSave) {
                echo "Video Save Successful";
            }

            // Redirect or display a success message to the user
            echo "Upload successful!";
        } else {
            // Handle upload errors
            echo "Upload failed!";
        }
    }

    if ($galleryForm == 2) {

        // Define the target directory for gallery image uploads
        $galleryUploadDir = 'upload/photos/';

        // Create the current year and month directories if they don't exist
        $galleryCurrentYear = date('Y');
        $galleryCurrentMonth = date('m');
        $galleryTargetDir = $galleryUploadDir . $galleryCurrentYear . '/' . $galleryCurrentMonth . '/';

        if (!is_dir($galleryTargetDir)) {
            // Create the year directory if it doesn't exist
            if (!is_dir($galleryUploadDir . $galleryCurrentYear)) {
                mkdir($galleryUploadDir . $galleryCurrentYear);
            }

            // Create the month directory
            mkdir($galleryTargetDir);
        }

        $galleryImages = $_FILES['gallery-input'];
        $thumbnail = $_FILES['img-thumb-input'];

        // Generate a unique folder name for this gallery
        $galleryFolderName = uniqid();
        $galleryFolderPath = $galleryTargetDir . $galleryFolderName . '/';

        // Create a dedicated folder for this gallery
        mkdir($galleryFolderPath);

        $uploadedImagePaths = []; // Initialize an array to store image paths

        foreach ($galleryImages['tmp_name'] as $key => $tmpName) {
            // Generate a unique filename for each image
            $galleryImageFileExtension = pathinfo($galleryImages['name'][$key], PATHINFO_EXTENSION);
            $galleryImageFileName = uniqid() . '.' . $galleryImageFileExtension;

            // Define the path for the image within the gallery folder
            $galleryImagePath = $galleryFolderPath . $galleryImageFileName;

            // Move the uploaded image to the target directory
            if (move_uploaded_file($tmpName, $galleryImagePath)) {
                // Handle successful image upload, e.g., store image path in a database if needed

                $uploadedImagePaths[] = $galleryImagePath;

                echo "Images Upload successful!";
            } else {
                // Handle image upload errors
            }
        }

        $imagePathsString = implode(',', $uploadedImagePaths);

        // Generate a unique filename for the thumbnail
        $thumbnailFileExtension = pathinfo($thumbnail['name'], PATHINFO_EXTENSION);
        $thumbnailFileName = uniqid() . '.' . $thumbnailFileExtension;

        // Define the path for the thumbnail within the gallery folder
        $thumbnailPath = $galleryFolderPath . $thumbnailFileName;

        // Move the uploaded thumbnail to the target directory
        if (move_uploaded_file($thumbnail['tmp_name'], $thumbnailPath)) {
            // Handle successful thumbnail upload, e.g., associate it with the gallery

            $fieldsGallery = array(
                'user_id' => $pt->user->id,
                'video_id' => $galleryFolderName, // Use the folder name as video_id
                'title' => $_POST['img-title-input'],
                'description' => $_POST['img-desc-input'],
                'thumbnail' => $thumbnailPath, // Store the thumbnail path
                'video_location' => $imagePathsString, // Store the video path
                'file_type' => 'gallery',
                'time' => time()
            );

            // Insert the data into the 'videos' table
            $gallerySave = $db->insert('videos', $fieldsGallery);
            if ($gallerySave) {
                echo "Images Save Successful";
            }

            echo "Thumbnail Upload successful!";
        } else {
            // Handle thumbnail upload errors
        }
    }

    if ($musicForm == 3) {

        // Define the target directory for music file uploads
        $musicUploadDir = 'upload/music/';

        // Create the current year and month directories if they don't exist
        $musicCurrentYear = date('Y');
        $musicCurrentMonth = date('m');
        $musicTargetDir = $musicUploadDir . $musicCurrentYear . '/' . $musicCurrentMonth . '/';

        if (!is_dir($musicTargetDir)) {
            // Create the year directory if it doesn't exist
            if (!is_dir($musicUploadDir . $musicCurrentYear)) {
                mkdir($musicUploadDir . $musicCurrentYear);
            }

            // Create the month directory
            mkdir($musicTargetDir);
        }

        // Handle the music file upload
        $musicFile = $_FILES['music-input'];
        $thumbnail = $_FILES['music-thumb-input'];

        // Generate a unique folder name for this music upload
        $musicFolderName = uniqid();
        $musicFolderPath = $musicTargetDir . $musicFolderName . '/';

        // Create a dedicated folder for this music upload
        mkdir($musicFolderPath);

        // Generate a unique filename for the music file
        $musicFileExtension = pathinfo($musicFile['name'], PATHINFO_EXTENSION);
        $musicFileName = uniqid() . '.' . $musicFileExtension;

        // Define the path for the music file within the folder
        $musicFilePath = $musicFolderPath . $musicFileName;

        // Move the uploaded music file to the target directory
        if (move_uploaded_file($musicFile['tmp_name'], $musicFilePath)) {
            // Handle successful music file upload, e.g., store file path in a database if needed
        } else {
            // Handle music file upload errors
        }

        // Generate a unique filename for the thumbnail
        $thumbnailFileExtension = pathinfo($thumbnail['name'], PATHINFO_EXTENSION);
        $thumbnailFileName = uniqid() . '.' . $thumbnailFileExtension;

        // Define the path for the thumbnail within the folder
        $thumbnailPath = $musicFolderPath . $thumbnailFileName;

        // Move the uploaded thumbnail to the target directory
        if (move_uploaded_file($thumbnail['tmp_name'], $thumbnailPath)) {
            // Handle successful thumbnail upload, e.g., associate it with the music

            $getID3Music = new getID3;
            $music_file = $getID3Music->analyze($musicFilePath);
            $duration_music = $music_file['playtime_string'];

            $fieldsMusic = array(
                'user_id' => $pt->user->id,
                'video_id' => $musicFolderName, // Use the folder name as video_id
                'title' => $_POST['music-title-input'],
                'description' => $_POST['music-desc-input'],
                'thumbnail' => $thumbnailPath, // Store the thumbnail path
                'video_location' => $musicFilePath, // Store the video path
                'size' => filesize($musicFilePath), // Get the video file size in bytes
                'duration' => $duration_music,
                'file_type' => 'music',
                'time' => time()
            );

            // Insert the data into the 'videos' table
            $musicSave = $db->insert('videos', $fieldsMusic);
            if ($musicSave) {
                echo "Music Save Successful";
            }

            echo "Upload successful!";
        } else {
            // Handle thumbnail upload errors
        }
    }
}
