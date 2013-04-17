<ol class="menu">
<?php foreach ($pages as $page => $description) { ?>
<li class="<?php if ($current == $page) { ?>current<?php } ?>">
	<a href="?p=<?php echo $page; ?>"><?php echo $description; ?></a>
    </li>
<?php } ?>
</ol>
