<?php
/*
 * DSDForm's class
 *
 */

require_once("DSDConfig.php");
require_once("DSDDispatcher.class.php");
require_once("DSDTable.class.php");
require_once("DSDError.class.php");
 
class DSDForm implements Iterator {
	public static $currentLine = 0;
	private $content;
	private $datas;
	private $sourcers;
	private $hash;
	private $position;
	private $path;
	public static $postCleaned = false;

	public function __construct($path = "", $vars = array()) {
		if (isset($path)) {
			$this->path = $path;
			$this->getContent($vars);
		}
	}

	public function getContent($vars = array()) {
			foreach ($vars as $k=>$v)
				$$k = $v;
			ob_start();
			include($this->path);
			$this->content = ob_get_clean();
			$this->position = 0;
			$this->Parse();
	}

	public static function cleanPost() {
		if (DSDForm::$postCleaned == false) {
			if (get_magic_quotes_gpc() == true) {
				foreach ($_POST as $k => $v) {
					if (is_array($v)) {
						$v2=array();
						foreach ($v as $o=>$p) 
							$v2[$o] = stripslashes($p);
						$_POST[$k] = $v2;
					} else {
						$_POST[$k] = stripslashes($v);
					}
				}
			}
			DSDForm::$postCleaned = true;
		}
	}

	private function Parse() {
		$this->datas = array();
		$a = 0;
		$len = strlen($this->content);
		$balise = false;
		$textarea = false;
		$select = false;
		$option = false;
		DSDForm::$currentLine = 1;
		for ($i=0; $i<$len; $i++) {
			if ($this->content[$i] == "\n")
				DSDForm::$currentLine++;
			if (!isset($this->datas[$a])) 
				$this->datas[]="";
			if (!$balise) {
				if ($this->content[$i] == "<") {
					$balise = true;
					$tmp = "";
				} else {
					if ($textarea || $option) {
						$this->datas[$a-1]->addValue($this->content[$i]);
					} else if (!$select) {
						$this->datas[$a] .= $this->content[$i];
					}
				}
			} else {
				if ($this->content[$i] == ">") {
					$balise = false;
					$return = $this->ParseBalise($tmp);
					if (!is_object($return)) {
						switch ($return) {
							case "</textarea>":
								$textarea = false;
							break;
							case "</select>":
								$select = false;
							break;
							case "</option>":
								$option = false;
							break;
							case "</form>":
								if (isset($_SESSION["DSDSecret"]))
									$secret=$_SESSION["DSDSecret"];
								else {
									$secret=md5(mt_rand(0,10000).md5(time()).mt_rand(0,10000));
									$_SESSION["DSDSecret"]=$secret;
								}
								$this->hash = md5($secret);
								$return = "<input type=\"hidden\" name=\"DSDForm_".$this->hash."\" value=\"1\" />\n</form>";
							default:

							$this->datas[$a] .= $return;
						}

						if ($textarea) {
							$this->datas[$a-1]->addValue($return);
						} 
					} else {
						$return->setForm($this);
						if (get_class($return)=="DSDOptions") {
			                             if (get_class($this->datas[$a-1] != "DSDSelect")) {
                                                                DSDForm::error("Options out of select.");
                                                     }
						     $this->sourcers[] = $return;
						     $return->setParent($this->datas[$a-1]);
						} else
						if (get_class($return) == "DSDOption") {
							$option = true;
							if (get_class($this->datas[$a-1] != "DSDSelect")) {
								DSDForm::error("Option out of select.");
							} else {
								$this->datas[$a-1]->addOption($return);
						}
						}else {
							$this->datas[] = $return;
							$a+=2;
							if (get_class($return) == "DSDTextarea") {
								$textarea = true;
							}
							if (get_class($return) == "DSDSelect") {
								$select = true;
							}
							if (get_class($return) == "DSDCustom" || get_class($return) == "DSDField_multicheckbox" || get_class($return)=="DSDField_multiradio") {
								$this->sourcers[] = $return;
							}
						}
					}
				} else {
					$tmp .= $this->content[$i];
				}
			}
		}
	}

	public function getField($name) {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				if ($d->getName() == $name)
					return $d;
			}
		}
		return null;
	}

	private function ParseBalise($data) {
		$spaces = explode(" ", $data, 2);
		$name = $spaces[0];
		return DSDDispatcher::dispatch($name, $spaces[1]);
		return "<$data>";

	}

	public function setValues($values, $files=array()) {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				if (isset($values[$d->getName()])) {
					$d->setValue($values[$d->getName()]);
				} else
				if (get_class($d)=="DSDField_file" && isset($files[$d->getName()])) {
					$d->setValue($files[$d->getName()]);
				} else
				if (get_class($d) == "DSDField_checkbox" || get_class($d) == "DSDField_multicheckbox")
					$d->setValue("");
			}
		}
	}

	public function resetValues() {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				$d->setValue("");
			}
		}
	}

	public function setSQLValues($table) {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				$sql = $d->getSQLName();
				if ($sql) {
					if (isset($table->$sql)) {
						$d->setValue($table->$sql,1);
					}
				}
			}
		}
	}

	public function setValue($name, $value) {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				if ($d->getName() == $name)
					$d->setValue($value,1);
			}
		}
	}

	public function setClass($name, $value) {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				if ($d->getName() == $name)
					$d->setClass($value);
			}
		}
	}

	public function setOptionClass($select, $val, $class) {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				if ($d->getName() == $select)
					$d->setOptionClass($val,$class);
			}
		}
	}

	public function __toString() {
		return $this->getHTML();
	}

	public function getHTML() {
		global $DSDHttp;
		$s = "";
		$js = false;
		foreach ($this->datas as $d) {
			if (!is_object($d)) {
				$s .= $d;
			} else {
				$s .= $d->getHTML();
				if ($d->needJS())
					$js=true;
			}
		}
		if ($js)
		$s = "<script type=\"text/javascript\" src=\"$DSDHttp/js/dsd.js\"></script>".$s;
		return $s;
	}

	public function check() {
		$n = func_get_args();
		$e = array();
		$radios = array();
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				if (count($n) == 0 || array_search($d->getName(), $n)!==FALSE) {
					$r = $d->check();
					if (get_class($d) == "DSDField_radio") {
						if ($r == DSDField_radio::$OPTIONAL)
							$radios[$d->getName()] = true;
						else {
							if (!isset($radios[$d->getName()]) || $radios[$d->getName()] == false) {
								if ($r == DSDField_radio::$CHECKED)
									$radios[$d->getName()] = true;
								else
									$radios[$d->getName()] = false;
							}
						}
					} else {
						if ($r) {
							$e[] = new DSDError($d->getName(),$r);
						}
					}
				}
			}
		}
		foreach ($radios as $name => $val) {
			if ($val == false) {
				$e[] = new DSDError($name,DSDField_radio::error($name));
			}
		}
		
		return $e;
	}

	public function source($source, $data) {
		if (is_array($this->sourcers))
		foreach ($this->sourcers as $s) {
			if ($s->getSource() == $source) {
				$s->source($data);
			}
		}
	}

	public function SQL($table) {
		if (gettype($table) == "string")
			$table = new DSDTable($table);
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				$sql = $d->getSQLName();
				if ($sql) {
					if (get_class($d) == "DSDField_multicheckbox")
						continue;
					if (get_class($d) != "DSDField_radio" || $d->isChecked()==true) {
						$table->$sql = $d->getValue();
						if (get_class($d) == "DSDField_checkbox") {
							if ($d->isChecked()) {
								$table->$sql=$d->getValue();
							} else $table->$sql=0;
						}						
					}
				}
			}
		}
		return $table;
	}

	public function __get($var) {
		foreach ($this->datas as $d) {
			if (is_object($d)) {
				if (get_class($d) == "DSDField_radio" && $d->isChecked()==false) 
					continue;
				if ($d->getName() == $var)
					return $d->getValue();
			}
		}
		return false;
	}

	public function getValue($var) {
		return $this->__get($var);
	}

	public function __set($var, $val) {
		$this->setValue($var, $val);
	}
	
	public function posted() {
		global $_POST, $_SESSION;
		DSDForm::cleanPost();
		if (isset($_POST["DSDForm_".$this->hash])) {
			$this->setValues($_POST, $_FILES);
			return true;
		} else {
			return false;
		}
	}

	public static function error($s) {
		echo "<span style='font-family: Courier;'>";
		echo "<b>DSDForm Error (l.".DSDForm::$currentLine."):</b> $s";
		echo "</span>";
		exit(0);
	}

	public function rewind() {
		$this->position = 0;
		$this->next();
	}

	public function next() {
		$i = $this->position+1;
		while ($i<count($this->datas)) {
			if (is_object($this->datas[$i]))
				break;
			$i++;
		}
		$this->position = $i;
	}

	public function current() {
		return ($this->datas[$this->position]->getValue());
	}

	public function valid() {
		return isset($this->datas[$this->position]);
	}

	public function key() {
		return $this->datas[$this->position]->getName();
	}
}
?>
