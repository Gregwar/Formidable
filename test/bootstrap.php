<?php

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

/**
 * Loading files to bootstrap testing
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
include(__DIR__.'/../autoload.php');

