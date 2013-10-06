<?php
include(__DIR__.'/../autoload.php');

$iterations = 5000;

echo "Generating $iterations forms without cache...\n";
$start = microtime(true);
for ($i=0; $i<$iterations; $i++) {
    $form = new Gregwar\Formidable\Form('forms/test.html');
    $x = "$form";
}
$withoutCache = microtime(true)-$start;
echo "Time = ".$withoutCache."s\n\n";

echo "Generating $iterations forms with cache...\n";
$start = microtime(true);
for ($i=0; $i<$iterations; $i++) {
    $form = new Gregwar\Formidable\Form('forms/test.html', array(), null, true);
    $x = "$form";
}
$withCache = microtime(true)-$start;
echo "Time = ".$withCache."s\n";
