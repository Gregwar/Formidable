<?php 

// ...

if ($form->posted()) {
    $errors = $form->check();
    if (count($errors) == 0) {
	// Traitement
    }
}

?>

<!-- Dans votre template -->
<?php if ($errors) { ?>
    Il y a eu des erreurs de validation :
    <ul>
    <?php foreach ($errors as $error) { ?>
	<li><?php echo $error; ?></li>
    <?php } ?>
    </ul>
<?php } ?>
