<?php
/*
 * DSDTextarea
 */

require_once("DSDField.class.php");
 
class DSDTextarea extends DSDField {

	public function __construct() {
		$this->type="";
	}

	public function addValue($c) {
		$this->value .= $c;
	}

	public function getHTML() {
		return "<textarea class=\"".$this->class."\" name=\"".$this->name."\"".$this->HTML.">".htmlspecialchars($this->value)."</textarea>\n";
	}
}
?>
