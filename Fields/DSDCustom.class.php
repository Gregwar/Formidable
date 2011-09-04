<?php
/*
 * DSDCustom class
 *
 */

require_once("DSDField.class.php");
require_once("DSDOption.class.php");

class DSDCustom extends DSDField {
	private $src;
	private $source;
	
	public function push($name, $value) {
		if ($name == "source") {
			$this->source = $value;
		} else {
			$pushSave[] = array($name, $value);
			DSDField::push($name, $value);
		}
	}
	
	public function getSource() {
		return $this->source;
	}

	public function source($src) {
		$this->src = $src;
	}

	public function getHTML() {
		return $this->src;
	}

	public function check() {
		return;
	}
}
