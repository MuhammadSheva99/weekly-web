<?php
// Jalankan dari root project: php fix-achievement.php
$cari = '/\$achievement\s*=\s*\$commitment->target\s*>\s*0\s*\?\s*\(\$actualValue\s*\/\s*\$commitment->target\)\s*\*\s*100\s*:\s*0;/';
$ganti = '$achievement = \App\Support\KpiAchievement::hitung($commitment, $actualValue);';

$total = 0;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/app/Http/Controllers'));
foreach ($it as $f) {
    if (! $f->isFile() || $f->getExtension() !== 'php') continue;
    $isi = file_get_contents($f->getPathname());
    $baru = preg_replace($cari, $ganti, $isi, -1, $n);
    if ($n > 0) {
        file_put_contents($f->getPathname(), $baru);
        echo "$n baris diganti: " . str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $f->getPathname()) . PHP_EOL;
        $total += $n;
    }
}
echo "Total: $total baris diganti." . PHP_EOL;