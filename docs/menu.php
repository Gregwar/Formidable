<ol class="menu">
<?php foreach ($pages as $page => $description) { ?>
<li class="<?php if ($current == $page) { ?>current<?php } ?>">
	<a href="<?php echo $page; ?>.html"><?php echo $description; ?></a>
    </li>
<?php } ?>
</ol>
