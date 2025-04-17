<?php
$galleryDir = 'assets/photos/gallery';
$albums = array_diff(scandir($galleryDir), ['.', '..']);
function getImageURLs($folderPath)
{
    $result = [];
    $images = array_diff(scandir($folderPath), ['.', '..']);
    foreach ($images as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $result[] = "$folderPath/$file";
        }
    }
    return $result;
}
// AJAX endpoint: Return a JSON list of image URLs for the selected album
if (isset($_GET['album']) && isset($_GET['action']) && $_GET['action'] === 'json') {
    $album = basename($_GET['album']); // security: ignore any path traversal
    $path = "$galleryDir/$album";
    if (is_dir($path)) {
        $images = getImageURLs($path);
        header('Content-Type: application/json');
        echo json_encode($images);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metadata & Document Setup -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="Shri V.J. Modha College - Empowering students for a better future.">
    <title>Gallery</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/logo.ico">
   
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="style/global.css" rel="stylesheet">
    <link href="style/gallery.css" rel="stylesheet">
    <!-- Scripts -->
    <script src="scripts/gallery.js" defer></script>
</head>
<body>
    <!-- Navigation Section (assuming nav.html exists) -->
    <?php include("nav.html"); ?>
    <div class="container">
        <div class="wrapper">
            <h2 class="page_title">Gallery</h2>
            <div class="gallery_albums" id="album-grid">
                <?php foreach ($albums as $album): ?>
                    <?php
                    // Get the first image to use as a thumbnail
                    $folderPath = "$galleryDir/$album";
                    $images = array_diff(scandir($folderPath), ['.', '..']);
                    $thumb = null;
                    foreach ($images as $file) {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $thumb = "$folderPath/$file";
                            break;
                        }
                    }
                    $thumbStyle = $thumb ? "background-image: url('$thumb');" : "";
                    ?>
                    <div class="gallery_album" style="<?php echo $thumbStyle; ?>" onclick="openModal('<?php echo $album; ?>')">
                        <div class="gallery_album__title"><?php echo ucwords(str_replace('_', ' ', $album)); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Fullscreen Modal for slideshow -->
    <div class="gallery_modal" id="modal">
        <button class="gallery_modal__close" onclick="closeModal()">&times;</button>
        <div class="gallery_modal__content" id="modal-content">
            <!-- Slides will be injected here by JavaScript -->
        </div>
        <button class="gallery_modal__nav gallery_modal__nav--prev" onclick="prevSlide()">&#10094;</button>
        <button class="gallery_modal__nav gallery_modal__nav--next" onclick="nextSlide()">&#10095;</button>
    </div>
    <!-- Footer Section (assuming footer.html exists) -->
    <?php include("footer.html"); ?>
</body>
</html>