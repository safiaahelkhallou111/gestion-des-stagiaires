<?php
$imgPath = __DIR__ . '/../public/img/loggo.png';
if (!file_exists($imgPath)) {
    $imgPath = __DIR__ . '/../public/img/logo.png';
}

if (!function_exists('imagecreatefrompng')) {
    echo "GD library not enabled\n";
    exit;
}

$im = imagecreatefrompng($imgPath);
$width = imagesx($im);
$height = imagesy($im);

$colors = [];
for ($x = 0; $x < $width; $x += 2) {
    for ($y = 0; $y < $height; $y += 2) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $alpha = ($rgb >> 24) & 0x7F;

        if ($alpha < 50 && $g > 100 && $g > $r && $g > $b) { // Green-ish pixel
            $hex = sprintf("#%02x%02x%02x", $r, $g, $b);
            $colors[$hex] = ($colors[$hex] ?? 0) + 1;
        }
    }
}

arsort($colors);
echo "Dominant green colors in logo:\n";
print_r(array_slice($colors, 0, 10));
