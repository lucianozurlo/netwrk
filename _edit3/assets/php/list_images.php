<?php
    // list_images.php

    // Define the base folder relative to this script
    $baseFolder = '../img/'; // Ajusta esto según la estructura de tu proyecto
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

    /**
     * Recursive function to list images in a folder and its subfolders.
     *
     * @param string $directory The path of the directory to scan.
     * @param array $extensions Allowed file extensions.
     * @return array List of image paths relative to the base folder.
     */
    function listImagesRecursively($directory, $extensions) {
        $images = array();

        // Open the directory
        if (is_dir($directory)) {
            if ($dh = opendir($directory)) {
                // Iterate through all files and subdirectories
                while (($file = readdir($dh)) !== false) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    $fullPath = $directory . DIRECTORY_SEPARATOR . $file;

                    if (is_dir($fullPath)) {
                        // Recursive call for subdirectories
                        $subfolderImages = listImagesRecursively($fullPath, $extensions);
                        $images = array_merge($images, $subfolderImages);
                    } else {
                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extension), $extensions)) {
                            // Get the relative path (ensure it uses forward slashes)
                            $relativePath = str_replace('\\', '/', $fullPath);
                            $images[] = $relativePath;
                        }
                    }
                }
                closedir($dh);
            }
        }

        return $images;
    }

    // Get the list of images
    $imageList = listImagesRecursively($baseFolder, $allowedExtensions);

    // Return the list in JSON format
    header('Content-Type: application/json');
    echo json_encode($imageList);
?>
