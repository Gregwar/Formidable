<?php

namespace Gregwar\DSD\Fields;

/**
 * Classe parente des champs
 */
abstract class Field
{
    /**
     * Type du champ (à placer dans le type="")
     */
    protected $type = 'text';

    /**
     * Nom du champ
     */
    protected $name;

    /**
     * Code HTML supplémentaire
     */
    protected $attributes = array();

    /**
     * Une value a t-elle été fournie ?
     */
    protected $value = false;

    /**
     * Le champ est t-il optionnel ?
     */
    protected $optional = false;

    /**
     * Expression régulière à respecter
     */
    protected $regex;

    /**
     * Dimensions à respecter
     */
    protected $minlength;
    protected $maxlength;

    /**
     * Nom "joli" (pour les messages d'erreur)
     */
    protected $prettyname;

    /**
     * Lecture seule ?
     */
    protected $readonly = false;

    /**
     * La valeur a t-elle changé ?
     */
    protected $valuechanged = false;

    /**
     * Plusieurs valeurs ?
     */
    protected $multiple = false;
    protected $multipleChange = '';

    /**
     * Permet d'appliquer des contraintes sql
     */
    protected $in = '';
    protected $notin = '';

    /**
     * Donnée de mapping pour la base de données
     */
    protected $sqlname;

    /**
     * Définir un attribut
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Obtenir un attribut 
     */
    public function getAttribute($name)
    {
        return $this->attributes[$name];
    }

    /**
     * A t-il l'attribut $name ?
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Fonction apellée par le dispatcher
     */
    public function push($name, $value = null)
    {
        switch ($name) {
        case 'class':
            $this->attributes['class'] = $value;
            break;
        case 'name':
            $this->name = $value;
            break;
        case 'type':
            if (!$this->type) {
                $this->type = $value;
            }
            break;
        case 'value':
            $this->setValue($value);
            break;
        case 'optional':
            $this->optional = true;
            break;
        case 'regex':
            $this->regex = $value;
            break;
        case 'minlength':
            $this->minlength = $value;
            break;
        case 'maxlength':
            $this->maxlength = $value;
            $this->attributes['maxlength'] = $value;
            break;
        case 'multiple':
            $this->multiple = true;
            break;
        case 'multiplechange':
            $this->multipleChange = $value;
            break;
        case 'sqlname':
            $this->sqlname = $value;
            break;
        case 'in':
            $this->in = $value;
            break;
        case 'notin':
            $this->notin = $value;
            break;
        case 'prettyname':
            $this->prettyname=$value;
            break;
        case 'readonly':
            $this->readonly=true;
            $this->attributes['readonly'] = 'readonly';
            break;
        default:
            if (preg_match('#^([a-z0-9_-]+)$#mUsi', $name)) {
                if ($value !== null) {
                    $this->attributes[$name] = $value;
                } else {
                    $this->attributes[$name] = $name;
                }
            }
        }
    }

    public function printName()
    {
        if ($this->prettyname)
            return $this->prettyname;
        return $this->name;
    }

    /**
     * Test des contraintes
     */
    public function check()
    {
        if ($this->valuechanged && $this->readonly) {
            return 'Le champ '.$this->printName().' est en lecture seule';
        }

        if ($this->multiple && is_array($this->value)) {
            $tmp = $this->value;
            $nodata=true;
            foreach ($tmp as $val) {
                if ($val!="")
                    $nodata=false;
                $this->value = $val;
                $err = $this->check();
                if ($err) {
                    $this->value = $tmp;
                    return $err;
                }
            }
            if (!$this->optional && $nodata)
                return 'Vous devez saisir une valeur pour '.$this->printName();
            $this->value = $tmp;
            return;
        }
        if ($this->value===false || (is_string($this->value) && $this->value=="")) {
            if ($this->optional || $this->multiple)
                return;
            else {
                return 'Vous devez saisir une valeur pour '.$this->printName();
            }
        } else {
            if ($this->regex) {
                if (!eregi($this->regex, $this->value))
                    return 'Le format du champ '.$this->printName().' est incorrect';
            }
            if ($this->minlength && strlen($this->value)<$this->minlength)
                return 'Le champ '.$this->printName().' doit faire au moins '.$this->minlength.' caracteres.';
            if ($this->maxlength && strlen($this->value)>$this->maxlength)
                return 'Le champ '.$this->printName().' ne doit pas dépasser '.$this->maxlength.' caracteres.';

            $err = $this->inNotIn();
            if ($err)
                return $err;
        }
    }

    function inNotIn()
    {
        if ($this->in) {
            if ($this->checkInQuery($this->in)==0)
                return "La valeur du champ ".$this->printName()." doit être présent dans la base";
        }
        if ($this->notin) {
            if ($this->checkInQuery($this->notin)!=0)
                return "La valeur du champ ".$this->printName()." doit pas déja être présent dans la base";
        }
    }

    //XXX: très sale
    function checkInQuery($v)
    {
        if (isset($this->sqlname)) $defaultField = $this->sqlname;
        else $defaultField = $this->name;
        $tmp = explode(".",$v);
        $table=$tmp[0];
        if (isset($tmp[1]))
            $field=$tmp[1];
        else $field=$defaultField;
        $q = mysql_query("SELECT COUNT(*) AS NB FROM `".$table."` WHERE `$field`=\"".mysql_real_escape_string($this->value)."\"");
        $r = mysql_fetch_assoc($q);
        return $r["NB"];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSQLName()
    {
        return $this->sqlname;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * Définition de la valeur
     */
    public function setValue($val, $default = 0)
    {
        if ($val!=$this->value && !$default)
            $this->valuechanged = true;
        if (!($this->valuechanged && $this->readonly))
            $this->value = $val;
        if ($this->multiple && !is_array($this->value)) {
            $this->value = explode(",",$this->value);
        }
        if ($this->multiple && is_array($this->value)) {
            $valuez = array();
            foreach ($this->value as $v) {
                if ($v!="")
                    $valuez[] = $v;
            }
            $this->value = $valuez;
        }
    }

    public function getHTMLForValue($extra, $value = '', $nameb = '')
    {
        $html = '<input ';
        foreach ($this->attributes as $name => $value) {
            $html.= $name.'="'.$value.'" ';
        }
        $html.= 'name="'.$this->name.$nameb.'" ';
        $html.= 'value="'.htmlspecialchars($value).'" ';
        $html.= $extra;
        $html.= "/>\n";

        return $html;
    }

    public function getHTML($extra = '')
    {
        if ($extra != '')
            $extra = ' '.$extra;

        // XXX: Utiliser un prototype
        $nameb = '';
        $rnd = md5(mt_rand(0,1000000).md5(time()).mt_rand(0,100000));
        $value = $this->value;
        if ($this->multiple) {
            $nameb="[0]";
            if (is_array($this->value) && isset($this->value[0]))
                $value=$this->value[0];
            else	$value="";
        }

        $code=$this->getHTMLForValue($extra, $value, $nameb);
        $novalcode=$this->getHTMLForValue($extra, "", $nameb);

        $others="";
        if ($this->multiple && is_array($this->value)) {
            for ($i=1; $i<count($this->value); $i++) {
                if (trim($this->value[$i])!="") {
                    $others.="DSD.addInput(\"$rnd\",\"";
                    $others.=str_replace(array("\r","\n"),array("",""),addslashes($this->getHTMLForValue($extra, $this->value[$i],$nameb)));
                    $others.="\");\n";
                }
            }
        }
        if ($this->multiple) {
            $html="<br /><span id=\"$rnd\"><span name=\"add\"></span></span><script type=\"text/javascript\">$others</script>";
            $html.="<a href=\"javascript:DSD.addInput('$rnd','".str_replace(array("\r","\n"),array("",""),htmlspecialchars($novalcode))."');".$this->multipleChange."\">Ajouter</a>";
            $html=$code.$html;
        } else $html=$code;
        return $html;
    }

    public function getSource()
    {
        return '';
    }

    public function isChecked()
    {
        return true;
    }	

    public function source() {
        return false;
    }
}
