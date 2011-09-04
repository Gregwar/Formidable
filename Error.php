<?php

namespace Gregwar\DSD;

/**
 * Erreur sur un champ
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Error
{
    /**
     * Nom du champ concerné
     */
    public $name;

    /**
     * Message de l'erreur
     */
	public $message;

    public function __construct($name, $message)
    {
		$this->name = $name;
		$this->message = $message;
	}

    public function __toString()
    {
		return $this->message;
	}
}
