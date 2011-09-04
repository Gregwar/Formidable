<?php
/*
 * DSDField_text class
 *
 */

require_once("DSDField.class.php");

function size_pretty($s) {
	$unites = array("o","Ko","Mo","Go","Po");
	$n = floor(log($s)/log(1024));
	$t = round($s/pow(1024,$n),1);
	return $t." ".$unites[$n];
}

class DSDField_file extends DSDField {
	private $datas;
	private $maxsize;
	private $filetype;
	private $image;
	private $imageWidth;
	private $imageHeight;

	public function __construct() {
		$this->type = "file";
		$this->datas =null;
		$this->maxsize=null;
		$this->filetype=null;
		$this->image=null;
	}

	public function push($var, $val) {
		switch ($var) {
			case "maxsize":
				$this->maxsize=$val;
			break;
			case "filetype":
				$this->filetype=$val;
			break;
			default:
				parent::push($var, $val);
			break;
		}
	}

	public function setValue($arr) {
		if (!is_array($arr))
			return;
		$this->datas = $arr;
		if (isset($this->datas["size"]))
		if ($this->datas["size"]!=0)
		switch ($this->filetype) {
			case "image":
					$i = @imagecreatefromjpeg($this->datas["tmp_name"]);
					if (!$i) $i = @imagecreatefromgif($this->datas["tmp_name"]);
					if (!$i) $i = @imagecreatefrompng($this->datas["tmp_name"]);
					if ($i && imagesx($i)!=0 && imagesy($i)!=0) {
						$this->image=$i;
						$this->imageWidth=imagesx($i);
						$this->imageHeight=imagesy($i);
					} else {
						return "Le fichier fourni dans le champ ".$this->printName()." doit être une image (JPEG, GIF, PNG...)";
					}
				default:
				break;
		}
	}
	
	public function check() {
		if ((!is_array($this->datas) || $this->datas["size"]==0) && !$this->optional) {
			return "Vous devez fournir un fichier pour le champ ".$this->printName();
		}
		if (is_array($this->datas) && $this->datas["size"]!=0) {
			if (!is_null($this->maxsize))
			if ($this->datas["size"]>$this->maxsize)
				return "La taille du fichier envoyé pour le champ ".$this->printName()." ne doit pas exceder ".size_pretty($this->maxsize);
			if (!is_null($this->filetype)) {
				switch ($this->filetype) {
					case "image":
						if (is_null($this->image))
							return "Le fichier fourni dans le champ ".$this->printName()." doit être une image (JPEG, GIF, PNG...)";
					default:
					break;
				}
			}
		}
		return;
	}

	public function valid() {
		return (is_array($this->datas) && $this->datas["size"]!=0);
	}

	public function save($filename) {
		if (!is_array($this->datas))
			return;
		
		@move_uploaded_file($this->datas["tmp_name"], $filename);
	}

	public function tmpName() {
		return $this->datas["tmp_name"];
	}

	public function saveJpeg($filename,$q=90) {
		if (!is_array($this->datas) || $this->filetype!="image")
			return;
		imagejpeg($this->image,$filename,$q);
	}
	
	public function savePng($filename) {
		if (!is_array($this->datas) || $this->filetype!="image")
			return;
		imagepng($this->image,$filename);
	}
	
	public function saveGif($filename) {
		if (!is_array($this->datas) || $this->filetype!="image")
			return;
		imagegif($this->image,$filename);
	}

	public function resize($width=null, $height=null) {
		if (!is_null($width)) {
			$w = $width;
			$h = round(($width*$this->imageHeight)/$this->imageWidth);
		} 
		if (!is_null($height)) {
			if (is_null($width) || $h>$height) {
				$h = $height;
				$w = round(($height*$this->imageWidth)/$this->imageHeight);
			}
		} 
		$i = imagecreatetruecolor($w,$h);
		imagecopyresampled($i, $this->image, 0, 0, 0, 0, $w, $h, $this->imageWidth, $this->imageHeight);
		$this->image = $i;

	}

	public function size() {
		return $this->datas["size"];
	}

	public function prettySize() {
		return size_pretty($this->datas["size"]);
	}

	public function fileName() {
		return $this->datas["name"];
	}


	public function getValue() {
		if (is_null($this->datas) || !is_array($this->datas) || !isset($this->datas["size"]) || $this->datas["size"]==0) {
			return null;
		} else {
			return $this;
		}
	}

	public function __toString() {
		return "(File ".$this->datas["name"].")";
	}
}
?>
