<?php

namespace Gregwar\Formidable\Language;

class English extends Language
{
    protected $messages = array(
        'read_only' => 'The field %s is read-only and should not be changed',
        'value_required' => 'You should enter a value for the field %s',
        'bad_format' => 'Format of the field %s is not correct',
        'at_least' => 'The field %s should be at least %d characters long',
        'not_more' => 'The field %s should not be longer than %d characters',
        'bad_email' => 'The field %s should be a valid e-mail address',
        'bad_captcha' => 'The captcha value is not correct',
        'bad_date' => 'The date %s is not correct',
        'add' => 'Add',
        'remove' => 'Remove',
        'file_size_too_big' => 'File size for the field %s should not exceed %s',
        'file_image' => 'File for the field %s should be an image',
        'file_required' => 'You should send a file for the field %s',
        'integer' => 'Field %s should be an integer',
        'should_check' => 'You should check a box for %s',
        'number' => 'Field %s should be a number',
        'number_min' => 'Field %s should be at least equal to %s',
        'number_max' => 'Field %s should not be bigger than %s',
        'number_step' => 'Field %s should be a multiple of %f',
        'should_choose' => 'You should choose a field for %s',
        'multiple_min' => 'You should at least provide %d entries for %s',
        'multiple_mmax' => 'You should not provide more than %d entries for %s',
        'bad_array_value' => 'The value for fields %s is not correct',
    );
}
