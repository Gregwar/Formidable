<?php

namespace Gregwar\DSD\Fields;

/**
 * Champ de type custom
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FileField extends Field
{
    /**
     * Données du fichier
     */
    private $datas = null;

    /**
     * Taille maximum
     */
    private $maxsize = null;

    /**
     * Type de fichier
     */
    private $filetype = null;

    /**
     * Utilisé pour les limitations de dimensions
     */
    private $image = null;

    public function __construct()
    {
        $this->type = 'file';
    }

    public function push($var, $value)
    {
        switch ($var) {
        case 'maxsize':
            $this->maxsize = $value;
            break;
        case 'filetype':
            $this->filetype = $value;
            break;
        default:
            parent::push($var, $value);
            break;
        }
    }

    public function setValue($value)
    {
        if (!is_array($value)) {
            return;
        }

        $this->datas = $value;
    }

    public function check()
    {
        if ($this->hasData()) {
            if (null !== $this->maxsize && $this->datas["size"] > $this->maxsize) {
                return 'La taille du fichier envoyé pour le champ '.$this->printName().
                       ' ne doit pas excéder '.$this->sizePrettyize($this->maxsize);
            }
            if (null !== $this->filetype) {
                switch ($this->filetype) {
                case 'image':
                    $size = @getimagesize($this->datas['tmp_name']);
                    if (!$size || !$size[0] || !$size[1]) {
                        return 'Le fichier fourni dans le champ '.$this->printName().' doit être une image (JPEG, GIF, PNG...)';
                    }
                default:
                    break;
                }
            }
        } else {
            if (!$this->optional) {
                return 'Vous devez fournir un fichier pour le champ '.$this->printName();
            }
        }

        return;
    }

    public function hasData()
    {
        return (null !== $this->datas && isset($this->datas['size']) && $this->datas['size']!=0);
    }

    public function save($filename)
    {
        if (null === $this->datas)

            return;

        @move_uploaded_file($this->datas['tmp_name'], $filename);
    }

    public function tmpName() {
        return $this->datas['tmp_name'];
    }

    public function size()
    {
        return $this->datas['size'];
    }

    public function prettySize()
    {
        return $this->sizePrettyize($this->datas["size"]);
    }

    public function sizePrettyize($size)
    {
        $unites = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po');

        $size = $this->datas['size'];
        $n = floor(log($size)/log(1024));
        $t = round($size/pow(1024,$n),1);

        return $t.' '.$unites[$n];
    }

    public function fileName()
    {
        return $this->datas['name'];
    }

    public function getValue()
    {
        return $this->hasData() ? $this : null;
    }

    public function __toString()
    {
        return '(File '.$this->datas['name'].')';
    }
}
