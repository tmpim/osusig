<?php
require_once("../p/.priv.php");

error_reporting(E_ERROR | E_PARSE);

$apiURL = "https://osu.ppy.sh/api/";

$templateDirectory = "../templates/";
$flagsDirectory = "../flags/";
$modesDirectory = "../modes/";
$fontDirectory = "../fonts/";

$fontRegular = $fontDirectory . "exo2regular.ttf";
$fontMedium = $fontDirectory . "exo2medium.ttf";
$fontBold = $fontDirectory . "exo2bold.ttf";

if (!isset($_GET['colour']) || !isset($_GET['uname'])) {
    die();
}

$colour = strtolower($_GET['colour']);
$colour = strtolower($_GET['colour']);
$uname = urldecode($_GET['uname']);
$mode = isset($_GET['mode']) ? $_GET['mode'] : 0;
$modeName = "osu";

switch ($colour) {
    case "pink":  
    case "green":  
    case "blue":  
    case "yellow":  
    case "purple":
        break;
    default:
        die();
        break;
}

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


/***
* Cheers to Repflez
* https://github.com/Repflez/osu-API-lib/blob/master/lib/file.php
***/

curl_setopt($ch, CURLOPT_POST, false);

function get_url($url) {    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
            CURLOPT_USERAGENT => 'osu!next Signature Generator by Lemmmy',
        );
        
        curl_setopt_array( $ch, $options );
        
        $content = curl_exec($ch);
        
        if ($_GET['curl']=="test") {
            $info = curl_getinfo($ch);
            $info['url'] = "masked";
            $info['local_ip'] = "masked";
            $info['local_port'] = "masked";
            $info['primary_ip'] = "masked";
            
            echo("<pre>");
            print_r($info);
            echo("</pre>");
        }
        
        curl_close($ch);
    } else {        
        $content = file_get_contents($url);
    }

    return $content;
}

$userInfo = json_decode(get_url($apiURL . "get_user" . "?" .
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

$template = imagecreatefrompng($templateDirectory . $colour . ".png");
imageAlphaBlending($template, true);
imageSaveAlpha($template, true);

$textWhite = imagecolorallocate($template, 255, 255, 255);
$textGrey = imagecolorallocate($template, 85, 85, 85);

// rank
$rankDimensions = imagettfbbox(getFontSize(14), 0, $fontRegular, "#" . $userInfo->pp_rank);
$rankTextWidth = abs($rankDimensions[4] - $rankDimensions[0]);
imagettftext($template, getFontSize(14), 0, 289 - $rankTextWidth, 31, $textWhite, $fontRegular, "#" . $userInfo->pp_rank);

// name
$nameFontSize = 24;

$nameDimensions = imagettfbbox(getFontSize($nameFontSize), 0, $fontMedium, $userInfo->username);
$nameTextWidth = abs($nameDimensions[4] - $nameDimensions[0]);

while ($nameTextWidth > 210 - $rankTextWidth) {
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

// accuracy
imagettftext($template, getFontSize(14), 0, 90, 56, $textGrey, $fontRegular, "Accuracy");
imagettftext($template, getFontSize(14), 0, 90, 73, $textGrey, $fontRegular, "Play count");

$accuracyDimensions = imagettfbbox(getFontSize(14), 0, $fontBold, round($userInfo->accuracy, 2) . "%");
$accuracyTextWidth = abs($accuracyDimensions[4] - $accuracyDimensions[0]);
imagettftext($template, getFontSize(14), 0, 329 - $accuracyTextWidth, 56, $textGrey, $fontBold, round($userInfo->accuracy, 2) . "%");

$playCountDimensions = imagettfbbox(getFontSize(14), 0, $fontBold, $userInfo->playcount . " (lv" . floor($userInfo->level) . ")");
$playCountTextWidth = abs($playCountDimensions[4] - $playCountDimensions[0]);
imagettftext($template, getFontSize(14), 0, 329 - $playCountTextWidth, 73, $textGrey, $fontBold, $userInfo->playcount . " (lv" . floor($userInfo->level) . ")");

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

$avatar = imagescale($avatar, 76, 76, IMG_BICUBIC);   

imagecopymerge_alpha($template, $avatar, 9, 9, 0, 0, 76, 76, 100);

imagepng($template);
imagedestroy($template);