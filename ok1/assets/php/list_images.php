<?php
    // list_images.php

    // Habilitar la visualización de errores (solo para desarrollo)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Define the base folder relative to this script
    $baseFolder = '../img/'; // Desde assets/php/list_images.php, '../img/' apunta a assets/img/
    $allowedExtensions = array('svg', 'jpg', 'jpeg', 'png', 'gif', 'webp');

    /**
     * Recursive function to list images in a folder and its subfolders.
     *
     * @param string $directory The path of the directory to scan.
     * @param array $extensions Allowed file extensions.
     * @return array List of image paths relative to the base folder.
     */
    function listImagesRecursively($directory, $extensions) {
        $images = array();

        // Abrir el directorio
        if (is_dir($directory)) {
            if ($dh = opendir($directory)) {
                // Iterar a través de todos los archivos y subdirectorios
                while (($file = readdir($dh)) !== false) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    $fullPath = $directory . DIRECTORY_SEPARATOR . $file;

                    if (is_dir($fullPath)) {
                        // Llamada recursiva para subdirectorios
                        $subfolderImages = listImagesRecursively($fullPath, $extensions);
                        $images = array_merge($images, $subfolderImages);
                    } else {
                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extension), $extensions)) {
                            // Obtener la ruta relativa (asegurarse de que usa barras normales)
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

    // Obtener la lista de imágenes
    $imageList = listImagesRecursively($baseFolder, $allowedExtensions);

    // Devolver la lista en formato JSON
    header('Content-Type: application/json');
    echo json_encode($imageList);
?>
