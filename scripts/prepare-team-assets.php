<?php

// php scripts/prepare-team-assets.php atif-avatar.png waseem-avatar.png waseem-banner.png nazim-banner.png
$targets = [
    ['atif/avatar-team.webp', 512, 512],
    ['waseem/avatar-team.webp', 512, 512],
    ['waseem/banner-team.webp', 1200, 400],
    ['nazim/banner-team.webp', 1200, 400],
];
if (count($argv) !== 5) {
    fwrite(STDERR, "Provide the four generated source images in the order shown above.\n");
    exit(1);
}
foreach ($targets as $index => [$file, $width, $height]) {
    $source = imagecreatefrompng($argv[$index + 1]);
    if (! $source) throw new RuntimeException('Cannot read source image.');
    $output = imagecreatetruecolor($width, $height);
    imagealphablending($output, false);
    imagesavealpha($output, true);
    imagefill($output, 0, 0, imagecolorallocatealpha($output, 0, 0, 0, 127));
    imagecopyresampled($output, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));
    $path = dirname(__DIR__).'/public/images/profiles/'.$file;
    if (! imagewebp($output, $path, 84)) throw new RuntimeException('Cannot save '.$file);
    imagedestroy($source);
    imagedestroy($output);
    echo $file.' '.$width.'x'.$height.' '.round(filesize($path) / 1024).' KB'.PHP_EOL;
}
