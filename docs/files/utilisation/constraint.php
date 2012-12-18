<?php
// ...
$form->addConstraint('prenom', function($value) {
    if ($value[0] == 'P') {
	return 'Le prénom ne doit pas commencer par un P !';
    }
});
