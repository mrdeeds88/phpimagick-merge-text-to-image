<?php
$asset = new Imagick("A.png");
$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.';

$image = new Imagick();
$draw  = new ImagickDraw();
$pixel = new ImagickPixel( '#f5f5f5' );

$image->newImage(838, 516, $pixel);
$draw->setFillColor('#caa4ff');

$draw->setFont('SairaSemiCondensed-Regular.ttf');
$draw->setFontSize(23);
$draw->setTextAntialias(true);

$draw->setGravity(Imagick::GRAVITY_NORTHWEST);
$image->compositeImage($asset, Imagick::COMPOSITE_DEFAULT, 0, 0);
//$image->annotateImage($draw, 10, 10, 0, $text);
list($lines, $lineHeight) = wordWrapAnnotation($image, $draw, $text, 460);

for($i = 0; $i < count($lines); $i++)
    $image->annotateImage($draw, 10, 10 + $i*$lineHeight, 0, $lines[$i]);

$combined = $image->appendImages(true);
$combined->setResolution(72,72);
$combined->setImageFormat("jpg");
file_put_contents('file.jpg', $combined);
header("Content-Type: image/jpg");
echo $combined;

function wordWrapAnnotation(&$image, &$draw, $text, $maxWidth) //@Author: BMiner stackoverflow.com
{
    $words = explode(" ", $text);
    $lines = array();
    $i = 0;
    $lineHeight = 0;
    while($i < count($words) )
    {
        $currentLine = $words[$i];
        if($i+1 >= count($words))
        {
            $lines[] = $currentLine;
            break;
        }
        //Check to see if we can add another word to this line
        $metrics = $image->queryFontMetrics($draw, $currentLine . ' ' . $words[$i+1]);
        while($metrics['textWidth'] <= $maxWidth)
        {
            //If so, do it and keep doing it!
            $currentLine .= ' ' . $words[++$i];
            if($i+1 >= count($words))
                break;
            $metrics = $image->queryFontMetrics($draw, $currentLine . ' ' . $words[$i+1]);
        }
        //We can't add the next word to this line, so loop to the next line
        $lines[] = $currentLine;
        $i++;
        //Finally, update line height
        if($metrics['textHeight'] > $lineHeight)
            $lineHeight = $metrics['textHeight'];
    }
    return array($lines, $lineHeight);
}
