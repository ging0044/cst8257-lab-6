<?php

include 'Picture.php';
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 2017-11-23
 * Time: 19:32
 */
session_start();

$success = false;

if (isset($_FILES['image'])) {
    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
        new Picture ($_FILES['image']['name'][$key], $tmp_name);
    }
    $success = true;
}

include './common/header.php';
?>
<div class="container">
    <h1>Upload Pictures</h1>
    <p>Accepted image types: JPG/JPEG, GIF, PNG</p>
    <?php if(isset($_GET['d']) && (int)$_GET['d'] == 1): ?>
        <div class="content-center" style="max-height: 400px; overflow-y: scroll">
            <?php var_dump(Picture::getPictures()); ?>
        </div>
    <?php endif; ?>
    <p>You can upload multiple pictures at once by holding ctrl or shift while selecting images.</p>

    <form action="UploadPicture.php" method="post" name="uploadForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="uploadFile">File to upload:</label>
            <input type="file" id="uploadFile" name="image[]" accept=".jpg,.jpeg,.gif,.png" multiple>
        </div>
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close"><span class="glyphicon glyphicon-remove-circle"></span></a>
            <p><span class="glyphicon glyphicon-thumbs-up"></span> Images uploaded successfully!</p>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <input type="submit" id="submit" value="Submit" class="btn btn-primary">
            <input type="reset" id="reset" value="Clear" class="btn btn-warning">
        </div>
    </form>
</div>
<?php
include './common/footer.php';
?>