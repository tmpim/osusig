<?php
require_once("p/.priv.php");

error_reporting(E_ERROR | E_PARSE);

$apiURL = "https://osu.ppy.sh/api/";

$templateDirectory = "templates/";
$flagsDirectory = "flags/";
$modesDirectory = "modes/";
$fontDirectory = "fonts/";

$fontRegular = $fontDirectory . "exo2regular.ttf";
$fontMedium = $fontDirectory . "exo2medium.ttf";
$fontBold = $fontDirectory . "exo2bold.ttf";

if (!isset($_GET['colour']) || !isset($_GET['uname'])) {
    die();
}

$colour = strtolower($_GET['colour']);
$pp = $_GET['pp'];
$uname = urldecode($_GET['uname']);
$mode = isset($_GET['mode']) ? $_GET['mode'] : 0;
$modeName = "osu";

switch ($mode) {
    case 0:
        $modeName = "osu";
        break;
    case 1:
        $modeName = "taiko";
        break;
    case 2:
        $modeName = "ctb";
        break;
    case 3:
        $modeName = "mania";
        break;
    default:
        $modeName = "osu";
        break;
}

$userInfo = json_decode(file_get_contents($apiURL . "get_user" . "?" .
                              "k"       . "=" . constant("AKEY") .
                              "&u"       . "=" . $uname .
                              "&m"       . "=" . $mode))[0];

if ($_GET['curl']=="test") return;

function getFontSize ($size) { 
    return ($size * 3) / 4; 
}

function imagecopymerge_alpha ($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) { 
    $cut = imagecreatetruecolor($src_w, $src_h); 
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h); 
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
} 

function imagecreatefrombmp( $filename )
{
    $file = fopen( $filename, "rb" );
    $read = fread( $file, 10 );
    while( !feof( $file ) && $read != "" )
    {
        $read .= fread( $file, 1024 );
    }
    $temp = unpack( "H*", $read );
    $hex = $temp[1];
    $header = substr( $hex, 0, 104 );
    $body = str_split( substr( $hex, 108 ), 6 );
    if( substr( $header, 0, 4 ) == "424d" )
    {
        $header = substr( $header, 4 );
        // Remove some stuff?
        $header = substr( $header, 32 );
        // Get the width
        $width = hexdec( substr( $header, 0, 2 ) );
        // Remove some stuff?
        $header = substr( $header, 8 );
        // Get the height
        $height = hexdec( substr( $header, 0, 2 ) );
        unset( $header );
    }
    $x = 0;
    $y = 1;
    $image = imagecreatetruecolor( $width, $height );
    foreach( $body as $rgb )
    {
        $r = hexdec( substr( $rgb, 4, 2 ) );
        $g = hexdec( substr( $rgb, 2, 2 ) );
        $b = hexdec( substr( $rgb, 0, 2 ) );
        $color = imagecolorallocate( $image, $r, $g, $b );
        imagesetpixel( $image, $x, $height-$y, $color );
        $x++;
        if( $x >= $width )
        {
            $x = 0;
            $y++;
        }
    }
    return $image;
}

header("Content-Type: image/png");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$template = imagecreatefrompng($templateDirectory . $colour . ".png");
imageAlphaBlending($template, true);
imageSaveAlpha($template, true);

$textWhite = imagecolorallocate($template, 255, 255, 255);
$textGrey = imagecolorallocate($template, 85, 85, 85);

// rank
$rankDimensions = imagettfbbox(getFontSize(14), 0, $fontRegular, "#" . number_format($userInfo->pp_rank));
$rankTextWidth = abs($rankDimensions[4] - $rankDimensions[0]);
imagettftext($template, getFontSize(14), 0, 289 - $rankTextWidth, 31, $textWhite, $fontRegular, "#" . number_format($userInfo->pp_rank));

// name
$nameFontSize = 24;

$nameDimensions = imagettfbbox(getFontSize($nameFontSize), 0, $fontMedium, $userInfo->username);
$nameTextWidth = abs($nameDimensions[4] - $nameDimensions[0]);

while ($nameTextWidth > 200 - $rankTextWidth) {
    $nameDimensions = imagettfbbox(getFontSize($nameFontSize), 0, $fontMedium, $userInfo->username);
    $nameTextWidth = abs($nameDimensions[4] - $nameDimensions[0]);
    
    $nameFontSize -= 2;
}

imagettftext($template, getFontSize($nameFontSize), 0, 89, 32, $textWhite, $fontMedium, $userInfo->username);

// mode
$mode = imagecreatefrompng($modesDirectory . $modeName . ".png");
imagecopymerge_alpha($template, $mode, 290, 20, 0, 0, 12, 12, 100);

// flag
$flag = imagecreatefrompng($flagsDirectory . $userInfo->country . ".png");
imagecopymerge_alpha($template, $flag, 307, 20, 0, 0, 18, 12, 100);

// pp

if (isset($_GET['pp']) && $pp == 2) {
    $ppText = number_format(floor($userInfo->pp_raw)) . "pp";
    $ppDimensions = imagettfbbox(getFontSize(14), 0, $fontBold, $ppText);
    $ppTextWidth = abs($ppDimensions[4] - $ppDimensions[0]);
    imagettftext($template, getFontSize(10), 0, 342 - $ppTextWidth, 17, $textWhite, $fontRegular, $ppText);   
}

// accuracy
imagettftext($template, getFontSize(14), 0, 90, 56, $textGrey, $fontRegular, "Accuracy");
imagettftext($template, getFontSize(14), 0, 90, 73, $textGrey, $fontRegular, "Play count");

$accuracyText = isset($_GET['pp']) && $pp == 1 ? " (" . number_format(floor($userInfo->pp_raw)) . "pp)" : "";
$accuracyDimensions = imagettfbbox(getFontSize(14), 0, $fontBold, round($userInfo->accuracy, 2) . "%" . $accuracyText);
$accuracyTextWidth = abs($accuracyDimensions[4] - $accuracyDimensions[0]);
imagettftext($template, getFontSize(14), 0, 329 - $accuracyTextWidth, 56, $textGrey, $fontBold, round($userInfo->accuracy, 2) . "%" . $accuracyText);

$levelText = isset($_GET['pp']) && $pp == 0 ? " (" . number_format(floor($userInfo->pp_raw)) . "pp)" : " (lv" . floor($userInfo->level) . ")";
$playCountDimensions = imagettfbbox(getFontSize(14), 0, $fontBold, number_format($userInfo->playcount) . $levelText);
$playCountTextWidth = abs($playCountDimensions[4] - $playCountDimensions[0]);
imagettftext($template, getFontSize(14), 0, 329 - $playCountTextWidth, 73, $textGrey, $fontBold, number_format($userInfo->playcount) . $levelText);

// avatar
$avatarURL = "https://a.ppy.sh/" . $userInfo->user_id . "?" . time() . ".png";
$avatarType = exif_imagetype($avatarURL);

switch ($avatarType) {
    case IMAGETYPE_PNG:
        $avatar = imagecreatefrompng($avatarURL);        
        break;
    case IMAGETYPE_JPEG:
        $avatar = imagecreatefromjpeg($avatarURL);        
        break;
    case IMAGETYPE_BMP:
        $avatar = imagecreatefrombmp($avatarURL);        
        break;
    case IMAGETYPE_GIF:
        $avatar = imagecreatefromgif($avatarURL);        
        break;
    default:
        $avatar = imagecreatefrompng($avatarURL);  
        break;
}

$avatarSize = isset($_GET['removeavmargin']) ? 81 : 76;
$avatarPos = isset($_GET['removeavmargin']) ? 6 : 9;
$avatar = imagescale($avatar, $avatarSize, $avatarSize, IMG_BILINEAR_FIXED);   

imagecopymerge_alpha($template, $avatar, $avatarPos, $avatarPos, 0, 0, $avatarSize, $avatarSize, 100);

imagepng($template);
imagedestroy($avatar);
imagedestroy($template);