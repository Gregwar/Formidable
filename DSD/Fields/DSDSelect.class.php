<?php
/*
 * DSDSelect class
 *
 */
 
require_once("DSDField.class.php");
 
class DSDSelect extends DSDField {
	private $options;
	
	public function __construct() {
	
	}

	public function countOptions() {
		return count($this->options);
	}

	public function addOption($opt,$pos=null) {
		$opt->setParent($this);
		if ($pos==null) {
			$this->options[] = $opt;
		} else {
			for ($i=$this->countOptions(); $i>$pos; $i--) {
				$this->options[$i] = $this->options[$i-1];
			}
			$this->options[$pos] = $opt;
		}
	}

	public function addValue($c) {
		$this->options[count($this->options)-1]->addValue($c);
	}

	public function check() {
		if ($this->value == "")
			return "Vous devez choisur une valeur pour le champ ".$this->printName();
		foreach ($this->options as $opt) {
			if ($this->value == $opt->getValue()) {
				$err = $this->inNotIn();
				if ($err)
					return $err;
				return;
			}
		}
		return "Vous devez choisir une option parmi les choix pour le champ ".$this->printName();
	}

	public function setOptionClass($val, $value) {
		foreach ($this->options as $opt) {
			if ($opt->getValue() == $val)
				$opt->setClass($value);
		}
	}

	public function getHTML() {
		$s = "<select class=\"".$this->class."\" name=\"".$this->name."\"".$this->HTML.">\n";
		if (count($this->options) !=0) {
			foreach ($this->options as $opt) {
				if ($opt->getValue() == $this->value)
					$s .= $opt->getHTML(true);
				else
					$s .= $opt->getHTML(false);
			}
		}
		$s .= "</select>\n";
		return $s;
	}
}
?>
