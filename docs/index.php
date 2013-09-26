<?php
include('geshi/geshi.php');
$pages = include('pages.php');

function highlight($file, $lang='php') {
    $geshi = new GeSHi(rtrim(file_get_contents(__DIR__.'/files/'.$file)), $lang);
    $geshi->enable_classes();
    $geshi->enable_keyword_links(false);
    return '<div class="highlight">'.$geshi->parse_code().'</div>';
}

include('current.php');

?>
<!DOCTYPE html>
<html>
    <head>
	<meta charset="utf-8" />
	<title>Formidable - <?php echo $pages[$current]; ?></title>
	<link type="text/css" media="screen" rel="stylesheet" href="style.css" />
	<link href='http://fonts.googleapis.com/css?family=Questrial' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Julee' rel='stylesheet' type='text/css'>
    </head>
    <body>
	<h1>Formidable</h1>
	<?php include('menu.php'); ?>	
	<div class="content">
	    <?php include('pages/'.$current.'.php'); ?>
	    <div class="footer">
		<?php include('browse.php'); ?>
	    </div>
	</div>
    </body>
</html>
