<?php
$current = $_GET['p'];
$prev = null;
$next = null;

if (!$current) {
    $current = 'presentation';
}

if (!isset($pages[$current])) {
    header('location: presentation.html');
    exit(0);
}

$find_next = false;
foreach ($pages as $name => $description) {
    if ($name == $current) {
	$find_next = true;
	continue;
    } 
    if (!$find_next) {
	$prev = $name;
    } else {
	$next = $name;
	break;
    }
}
