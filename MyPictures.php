<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 2017-12-04
 * Time: 09:27
 */

session_start();
include 'Picture.php';

$displayId = 0;
$displayPicture;
$pictures;

if (isset($_GET['delete'])) {
    foreach(Picture::getPictures() as $picture) {
        if ($picture->__get('id') == $_GET['delete']) {
            $picture->delete();
        }
    }
}

if (isset($_GET['id'])) {
    $displayId = (int)$_GET['id'];
}

$pictures = Picture::getPictures();
foreach ($pictures as $picture) {
    if ($picture->__get('id') == $displayId)
        $displayPicture = $picture;
}

if (isset($_GET['rotate'])) {
    $displayPicture->rotate($_GET['rotate']);
    $pictures = Picture::getPictures();
    foreach ($pictures as $picture) {
        if ($picture->__get('id') == $displayId)
            $displayPicture = $picture;
    }
}

if (isset($_GET['download'])) {
    $pictures = Picture::getPictures();
    foreach ($pictures as $picture) {
        if ($picture->__get('id') == $_GET['download'])
            $picture->download();
    }
}

include './common/header.php';
if (count($pictures) > 0):
?>
    <link rel="stylesheet" href="./MyPictures.css" />
<div class="container">
    <h1><?php echo $displayPicture->__get('name'); ?></h1>
    <div class="img-container" style="padding-bottom: 10px;position:relative">
        <img src="<?php echo $displayPicture->__get('albumPath'); ?>" alt="<?php echo $displayPicture->__get('name'); ?>" width="1024" height="800" />
        <div style="text-align: center;position:absolute;top:755px;right:50%">
            <a href="?id=<?php echo $displayId; ?>&rotate=left"><span class="glyphicon glyphicon-repeat gly-flip-horizontal"></span></a>
            <a href="?id=<?php echo $displayId; ?>&rotate=right"><span class="glyphicon glyphicon-repeat"></span></a>
            <a href="?download=<?php echo $displayId; ?>"><span class="glyphicon glyphicon-download"></span></a>
            <a href="?delete=<?php echo $displayId; ?>"><span class="glyphicon glyphicon-trash"></span></a>
        </div>
    </div>
    <div class="thumbnail-list" style="overflow-x: scroll;width: 1024px;height: 116px;padding: 5px;white-space: nowrap;border: 3px double black">
        <?php foreach($pictures as $picture): ?>
                <a href="?id=<?php echo $picture->__get('id'); ?>">
                    <img src="<?php echo $picture->__get('thumbnailPath'); ?>" alt="<?php echo $picture->__get('name'); ?>" width="100" height="100" />
                </a>
        <?php endforeach; ?>
    </div>
</div>
<?php
    endif;
    if (count($pictures) == 0):
?>
<div class="container">
    <h1>You have not uploaded any pictures!</h1>
    <p>Do so by clicking "Upload Pictures" above.</p>
</div>
<?php
    endif;
    include './common/footer.php';
?>