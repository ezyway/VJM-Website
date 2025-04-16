<?php
$galleryDir = 'assets/photos/gallery';
$albums = array_diff(scandir($galleryDir), ['.', '..']);

function getFirstImage($folderPath)
{
     $images = array_diff(scandir($folderPath), ['.', '..']);
     foreach ($images as $file) {
          $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
          if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
               return "$folderPath/$file";
          }
     }
     return null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

     <!-- ===================================================
         Metadata & Document Setup
         =================================================== -->
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
     <!-- Meta description for SEO -->
     <meta name="description" content="Shri V.J. Modha College - Empowering students for a better future.">

     <!-- Document Title -->
     <title>Gallery</title>

     <!-- Favicon -->
     <link rel="shortcut icon" type="image/x-icon" href="assets/logo.ico">

     <!-- ===================================================
         Fonts & Stylesheets
         =================================================== -->

     <!-- Fonts -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

     <!-- Custom Styles -->
     <link href="style/global.css" rel="stylesheet">
     <link href="style/gallery.css" rel="stylesheet">

     <!-- ===================================================
         Scripts
         =================================================== -->
     <script src="scripts/gallery.js" defer></script>
</head>

<body>
     <!-- ===================================================
         Navigation Section
         - Included via PHP to allow reusability across pages.
         =================================================== -->
     <?php include("nav.html"); ?>

     <div class="container">
          <div class="wrapper">
               <h2 class="page_title">Gallery</h2>
               <div class="album-grid" id="album-grid">
                    <?php foreach ($albums as $album): ?>
                         <?php
                         $thumb = getFirstImage("$galleryDir/$album");
                         $thumbStyle = $thumb ? "background-image: url('$thumb');" : "";
                         ?>
                         <div class="album" style="<?php echo $thumbStyle; ?>" onclick="openLightbox('<?php echo $album; ?>')">
                              <div class="album-title"><?php echo ucwords(str_replace('_', ' ', $album)); ?></div>
                         </div>
                    <?php endforeach; ?>
               </div>

               <div class="lightbox" id="lightbox">
                    <div class="close-btn" onclick="closeLightbox()">&times;</div>
                    <div class="lightbox-content" id="lightbox-content"></div>
               </div>

          </div>
     </div>

     <!-- ===================================================
         Footer Section
         - Included via PHP for consistency across pages.
         =================================================== -->
     <?php include("footer.html"); ?>

</body>

</html>

<?php
// Handle AJAX image loading
if (isset($_GET['album'])) {
     $album = basename($_GET['album']);
     $path = "$galleryDir/$album";
     if (is_dir($path)) {
          $images = array_diff(scandir($path), ['.', '..']);
          foreach ($images as $img) {
               $imgUrl = "$path/$img";
               $ext = strtolower(pathinfo($imgUrl, PATHINFO_EXTENSION));
               if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    echo "<img src='$imgUrl' alt=''>";
               }
          }
     }
     exit;
}
?>