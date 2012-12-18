<?php

if ($form->posted()) {
    // Sauvegarder une image
    $form->photo->save('uploads/photo.jpg');

    // Gérer un fichier manuellement
    $contents = file_get_contents($form->fichier->tmpName());
}
