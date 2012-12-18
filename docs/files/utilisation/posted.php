<?php
// ...
if ($form->posted()) {
    echo 'Bonjour '.htmlspecialchars($form->prenom).' !';
}
