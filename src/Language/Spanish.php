<?php

namespace Gregwar\Formidable\Language;

class Spanish extends Language
{
    protected $messages = array(
        'read_only' => 'El campo %s es de sólo lectura y no debe ser cambiado',
        'value_required' => 'Se debe introducir un valor para el campo %s',
        'bad_format' => 'El formato del campo %s es incorrecto',
        'at_least' => 'El campo %s debe tener al menos %d caracteres de longitud',
        'not_more' => 'El campo %s debe tener como máximo %d caracteres de longitud',
        'bad_email' => 'El campo %s debe ser una direccin de e-mail válida',
        'bad_captcha' => 'El valor del captcha no es correcto',
        'bad_date' => 'La fecha en el campo %s no es válida',
        'add' => 'Añadir',
        'remove' => 'Borrar',
        'file_size_too_big' => 'El tamaño de archivo del campo %s no puede ser mayor de %s',
        'file_image' => 'El archivo del campo %s debe ser una imagen',
        'file_required' => 'Se necesita un archivo para el campo %s',
        'integer' => 'El campo %s debe ser un número entero',
        'should_check' => 'Debes marcar al menos una opción para el campo %s',
        'number' => 'El campo %s debe ser un número',
        'number_min' => 'El campo %s debe ser al menos igual a %s',
        'number_max' => 'El campo %s no debe ser mayor que %s',
        'number_step' => 'El campo %s debe ser un múltiplo de %f',
        'should_choose' => 'Se debe escoger una entrada para el campo %s',
        'multiple_min' => 'Se deben dar al menos %d entradas para %s',
        'multiple_mmax' => 'No se deben dar más de  %d entradas para %s',
        'bad_array_value' => 'El valor para los campos %s es incorrecto',
    );
}
