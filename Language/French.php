<?php

namespace Gregwar\Formidable\Language;

class French extends Language
{
    protected $messages = array(
        'read_only' => 'Le champ %s est en lecture seule et ne peut pas être changé',
        'value_required' => 'Vous devez saisir une valeur pour le champ %s',
        'bad_format' => 'Le format du champ %s n\'est pas correct',
        'at_least' => 'Le champ %s doit faire au moins %s caractères',
        'not_more' => 'Le champ %S ne doit pas être plus long que %s caractères',
        'bad_email' => 'Le champ %s doit être une adresse e-mail valide',
        'bad_captcha' => 'La valeur du code visuel n\'est pas bonne',
        'bad_date' => 'La date du champ %s n\'est pas correcte',
        'add' => 'Ajouter',
        'remove' => 'Enlever',
        'file_size_too_big' => 'La taille du fichier %s ne doit pas être plus gros que %s',
        'file_image' => 'Le fichier du champ %s doit être une image',
        'file_required' => 'Vous devez envoyer un fichier pour le champ %s',
        'integer' => 'Le champ %s doit être un entier',
        'should_check' => 'Vous devez cocher une des cases pour le champ %s',
        'number' => 'Le champ %s doit être un nombre',
        'number_min' => 'Le champ %s doit être au moins %s',
        'number_max' => 'Le champ %s ne doit pas dépasser %s',
        'number_step' => 'Le champ %s doit être un multiple de %f',
        'should_choose' => 'Vous devez choisir une valeur pour %s',
        'multiple_min' => 'Vous devez au moins fournir %d entrées pour %s',
        'multiple_mmax' => 'Vous ne pouvez pas fournir plus de %d entrées pour %s',
        'bad_array_value' => 'La valeur des champs %s n\'est pas correcte',
    );
}
