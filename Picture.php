<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 2017-12-05
 * Time: 14:16
 */
session_start();

class Picture {

    // Static stuff

    private static $originalDir = './pictures/original/';
    private static $albumDir = './pictures/album/';
    private static $thumbnailDir = './pictures/thumbnail/';

    private static $pictures = array();

    private static function countPics() {
        return count(self::$pictures);
    }

    public static function getPictures() {
//        if (isset($_SESSION['pictures'])) {
//            self::$pictures = $_SESSION['pictures'];
//        }
//        return self::$pictures;
//        function () {
        self::$pictures = array();
        $files = scandir(self::__getStatic('originalDir'));
        foreach ($files as $file) {
            if ($file[0] != '.')
                new Picture(end(explode('/', $file)), $file);

        }
        return self::$pictures;
    }

    public static function __getStatic($name) {
        return Picture::$$name;
    }

    // Instanced stuff

    private $name;
    private $id;
    private $originalPath;
    private $albumPath;
    private $thumbnailPath;

    public function __construct($name, $tmp_name)
    {
        $this->name = $name;
        $this->id = self::countPics();

        $this->originalPath = self::$originalDir.$this->name;
        $this->albumPath = self::$albumDir.$this->name;
        $this->thumbnailPath = self::$thumbnailDir.$this->name;

        move_uploaded_file($tmp_name, $this->originalPath);

        $this->imageSize = getimagesize($this->originalPath);
        $this->imagePointer;
        $this->newPointerAlbum = imagecreatetruecolor(1024, 800);
        $this->newPointerThumbnail = imagecreatetruecolor(100, 100);

        $this->imageCreateFunction;

        switch($this->imageSize[2]) {
            case IMAGETYPE_JPEG:
                $this->imagePointer = imagecreatefromjpeg($this->originalPath);
                $this->imageCreateFunction = 'imagejpeg';
                break;
            case IMAGETYPE_GIF:
                $this->imagePointer = imagecreatefromgif($this->originalPath);
                $this->imageCreateFunction = 'imagegif';
                break;
            case IMAGETYPE_PNG:
                $this->imagePointer = imagecreatefrompng($this->originalPath);
                $this->imageCreateFunction = 'imagepng';
                break;
            default:
                break;
        }

        imagecopyresampled($this->newPointerAlbum, $this->imagePointer, 0, 0, 0, 0, 1024, 800, $this->imageSize[0], $this->imageSize[1]);
        imagecopyresampled($this->newPointerThumbnail, $this->imagePointer, 0, 0, 0, 0, 100, 100, $this->imageSize[0], $this->imageSize[1]);

        ($this->imageCreateFunction)($this->newPointerAlbum, $this->albumPath);
        ($this->imageCreateFunction)($this->newPointerThumbnail, $this->thumbnailPath);

        imagedestroy($this->newPointerThumbnail);
        imagedestroy($this->newPointerAlbum);
        imagedestroy($this->imagePointer);

//        if (isset($_SESSION['pictures'])) {
//            self::$pictures = $_SESSION['pictures'];
//        }
        array_push(self::$pictures, $this);
    }

    public function __get($name) {
        return $this->$name;
    }

    public function rotate($direction) {
        switch($this->imageSize[2]) {
            case IMAGETYPE_JPEG:
                $this->imagePointer = imagecreatefromjpeg($this->originalPath);
                $this->imageCreateFunction = 'imagejpeg';
                break;
            case IMAGETYPE_GIF:
                $this->imagePointer = imagecreatefromgif($this->originalPath);
                $this->imageCreateFunction = 'imagegif';
                break;
            case IMAGETYPE_PNG:
                $this->imagePointer = imagecreatefrompng($this->originalPath);
                $this->imageCreateFunction = 'imagepng';
                break;
            default:
                break;
        }
        $this->newPointerOriginal = imagecreatetruecolor($this->imageSize[0], $this->imageSize[1]);
        //$this->newPointerAlbum = imagecreatetruecolor(1024,800);
        //$this->newPointerThumbnail = imagecreatetruecolor(100,100);
        if ($direction == 'right') {
            $this->newPointerOriginal = imagerotate($this->imagePointer, 270, 0);
        }
        if ($direction == 'left') {
            $this->newPointerOriginal = imagerotate($this->imagePointer, 90, 0);
        }
//        imagecopyresampled($this->newPointerOriginal, $this->newPointerAlbum, 0,0,0,0, 1024, 800, $this->imageSize[0], $this->imageSize[1]);
//        imagecopyresampled($this->newPointerOriginal, $this->newPointerThumbnail,0,0,0,0,100, 100, $this->imageSize[0], $this->imageSize[1]);

        ($this->imageCreateFunction)($this->newPointerOriginal, $this->originalPath);
//        ($this->imageCreateFunction)($this->newPointerAlbum, $this->albumPath);
//        ($this->imageCreateFunction)($this->newPointerThumbnail, $this->thumbnailPath);

        imagedestroy($this->imagePointer);
        imagedestroy($this->newPointerOriginal);
//        imagedestroy($this->newPointerAlbum);
//        imagedestroy($this->newPointerThumbnail);
    }

    public function delete() {
        unlink($this->thumbnailPath);
        unlink($this->albumPath);
        unlink($this->originalPath);
        unset(self::$pictures[$this->id]);
    }

    public function download() {
        $filePath = $this->originalPath;
        $fileName = basename($filePath);
        $fileLength = filesize($filePath);

        $this->mime = (function() {
            switch($this->imageSize[2]) {
                case IMAGETYPE_JPEG:
                    return 'image/jpeg';
                    break;
                case IMAGETYPE_GIF:
                    return 'image/gif';
                    break;
                case IMAGETYPE_PNG:
                    return 'image/png';
                    break;
                default:
                    return 'application/octet-stream';
                    break;
            }
        })();

        header("Content-Type: $this->mime");
        header("Content-Disposition: attachment; filename = \"$fileName\" ");
        header("Content-Length: $fileLength" );
        header("Content-Description: File Transfer");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: private");

        ob_clean();
        flush();
        readfile($filePath);
        flush();
    }
}

//if (count(Picture::getPictures()) == 0) {
//    (function () {
//        $files = scandir(Picture::__getStatic('originalDir'));
//        foreach ($files as $file) {
//            if ($file[0] != '.')
//                new Picture(end(explode('/', $file)), $file);
//    }
//    })();
//}