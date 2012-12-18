<?php if ($prev) { ?>
    <a class="prev" href="<?php echo $prev; ?>.html">&laquo; <?php echo $pages[$prev]; ?></a>
<?php } ?>
<?php if ($next) { ?>
    <a class="next" href="<?php echo $next; ?>.html"><?php echo $pages[$next]; ?> &raquo;</a>
<?php } ?>
