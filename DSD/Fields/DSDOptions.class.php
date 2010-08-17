<?php
/*
 * DSDOptions class
 *
 */

require_once("DSDField.class.php");
require_once("DSDOption.class.php");

class DSDOptions extends DSDField {

	private $source;
	private $parent;
	private $pushSave;
	private $pos;

	public function __construct() {
		$this->pushSave = array();
	}

	public function push($name, $value) {
		if ($name == "source") {
			$this->source = $value;
		} else {
			$pushSave[] = array($name, $value);
			DSDField::push($name, $value);
		}
	}

	public function setParent($p) {
		$this->parent = $p;
		$this->pos = $this->parent->countOptions();
	}

	public function check() {
		return;
	}

	public function getSource() {
		return $this->source;
	}

	public function source($data) {
		foreach ($data as $k => $v) {
			if (is_object($v)) {
				$k = $v->getKey();
				$v = $v->getValue();
			}
			$opt = new DSDOption();
			foreach ($this->pushSave as $p) {
				$opt->push($p[0], $p[1]);
			}
			$opt->push("value", $k);
			$opt->setLabel($v);
			$this->parent->addOption($opt, $this->pos);
		}
	}
}

?>
