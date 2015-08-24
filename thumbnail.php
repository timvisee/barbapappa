<?php

use app\picture\Picture;
use app\picture\PictureManager;
use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\file\File;
use carbon\core\util\StringUtils;

const IMG_TYPE_JPG = IMAGETYPE_JPEG;
const IMG_TYPE_PNG = IMAGETYPE_PNG;
const IMG_TYPE_GIF = IMAGETYPE_GIF;

// Initialize the app
require_once('app/init.php');



$maxWidth = 1024;
$maxHeight = 1024;

if(!isset($_GET['size']) || !isset($_GET['picture_id']))
    die('Missing arguments!');

$pictureId = $_GET['picture_id'];
$sizeStr = trim($_GET['size']);
$sizeParts = explode('x', $sizeStr, 2);

$shapeFixed = false;
if(isset($_GET['shape']) && ($shapeStr = $_GET['shape']) == 'fixed')
    $shapeFixed = true;
$shapeName = ($shapeFixed ? 'fixed' : 'dynamic');

$prefWidth = $sizeParts[0];
$prefHeight = $sizeParts[1];

if(!ctype_digit($prefWidth) || !ctype_digit($prefWidth))
    die('Invalid picture size!');

if(!PictureManager::isPictureWithId($pictureId))
    die('Invalid picture ID!');

if($prefWidth > $maxWidth)
    $prefWidth = $maxWidth;
if($prefHeight > $maxHeight)
    $prefHeight = $maxHeight;

$picture = new Picture($pictureId);

$file = $picture->getFile();

$imgExtension = $file->getExtension(false);

if(StringUtils::equals($imgExtension, Array('jpg', 'jpeg'), false, true))
    $imgType = IMG_TYPE_JPG;
elseif(StringUtils::equals($imgExtension, 'png', false, true))
    $imgType = IMG_TYPE_PNG;
elseif(StringUtils::equals($imgExtension, 'gif', false, true))
    $imgType = IMG_TYPE_GIF;
else
    die('Unsupported image type!');

$cacheDir = new Directory(new Directory(__DIR__, '/cache/data/pictures/'), $sizeStr);
$imgFileStrStuff = $file->getBasename('.' . $imgExtension) . '-' . $shapeName . '.' . $imgExtension;
$cacheFile = new File($cacheDir, $imgFileStrStuff);

$useCache = false;

if($cacheFile->exists())
    if($cacheFile->getModificationTime() > (time() - 1 * 60 * 60 * 12))
        $useCache = true;

if($useCache) {
    header("Content-type: " . image_type_to_mime_type($imgType));

    switch($imgType) {
    case IMG_TYPE_JPG:
        $img = @imagecreatefromjpeg($cacheFile->getPath());
        imagejpeg($img);
        break;

    case IMG_TYPE_PNG:
        $img = @imagecreatefrompng($cacheFile->getPath());
        imagepng($img);
        break;

    case IMG_TYPE_GIF:
        $img = @imagecreatefromgif($cacheFile->getPath());
        imagegif($img);
        break;

    default:
        die('An error occurred while generating the image thumbnail!');
    }

} else {
    $imgArray = getimagesize($file->getPath());
    $srcWidth = $imgWidth = $imgArray[0];
    $srcHeight = $imgHeight = $imgArray[1];
    $widthMax = $prefWidth;
    $heightMax = $prefHeight;
    $srcOriginX = $srcOriginY = $dstOriginX = $dstOriginY = 0;

    if(!$shapeFixed) {
        if($srcHeight > $widthMax || $srcHeight > $heightMax) {
            if(($srcWidth / $srcHeight) > ($widthMax / $heightMax)) {
                $dstWidth = $widthMax;
                $aspectRatio = $srcWidth / $widthMax;
                $dstHeight = round($srcHeight / $aspectRatio);
            } else {
                $dstHeight = $heightMax;
                $aspectRatio = $srcHeight / $heightMax;
                $dstWidth = round($srcWidth / $aspectRatio);
            }
        } else {
            $dstHeight = $heightMax;
            $dstWidth = $widthMax;
        }

    } else {
        // Set the destination width and height
        $dstWidth = min($prefWidth, $widthMax);
        $dstHeight = min($prefHeight, $heightMax);

        $aX = $srcWidth / $dstWidth;
        $aY = $srcHeight / $dstHeight;

        $srcWidth = min($aX, $aY) * $dstWidth;
        $srcHeight = min($aX, $aY) * $dstHeight;

        $srcOriginX = ($imgWidth - $srcWidth) / 2;
        $srcOriginY = ($imgHeight - $srcHeight) / 2;
    }

    switch($imgType) {
    case IMG_TYPE_JPG:
        $img = @imagecreatefromjpeg($file->getPath());
        break;

    case IMG_TYPE_PNG:
        $img = @imagecreatefrompng($file->getPath());
        break;

    case IMG_TYPE_GIF:
        $img = @imagecreatefromgif($file->getPath());
        break;

    default:
        die('An error occurred while generating the image thumbnail!');
    }

    $dstImg = imagecreatetruecolor($dstWidth, $dstHeight);
    imagecopyresized($dstImg, $img, $dstOriginX, $dstOriginY, $srcOriginX, $srcOriginY, $dstWidth, $dstHeight, $srcWidth, $srcHeight);
    imagedestroy($img);
    $img = $dstImg;

    header("Content-type: " . image_type_to_mime_type($imgType));
    switch($imgType) {
    case IMG_TYPE_JPG:
        imagejpeg($img);
        break;

    case IMG_TYPE_PNG:
        imagepng($img);
        break;

    case IMG_TYPE_GIF:
        imagegif($img);
        break;

    default:
        die('An error occurred while generating the image thumbnail!');
    }

    // Make sure the cache directory exists
    if(!$cacheDir->isDirectory())
        $cacheDir->createDirectory(0777, true);

    // Cache the image
    switch($imgType) {
    case IMG_TYPE_JPG:
        imagejpeg($img, $cacheFile->getPath());
        break;

    case IMG_TYPE_PNG:
        imagepng($img, $cacheFile->getPath());
        break;

    case IMG_TYPE_GIF:
        imagegif($img, $cacheFile->getPath());
        break;

    default:
        die('An error occurred while generating the image thumbnail!');
    }
}
