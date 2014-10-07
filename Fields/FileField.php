<?php

namespace Gregwar\Formidable\Fields;

/**
 * File field type
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FileField extends Field
{
    /**
     * File data
     */
    protected $datas;

    /**
     * Maximum file size
     */
    protected $maxsize;

    /**
     * File type
     */
    protected $filetype;

    /**
     * Field type
     */
    protected $type = 'file';
    
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array(
            'datas', 'filetype', 'maxsize'
        ));
    }

    public function push($var, $value = null)
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

    public function setValue($value, $default = false)
    {
        if (!is_array($value)) {
            return;
        }

        $this->datas = $value;
    }

    public function check()
    {
        if ($this->hasData()) {
            if (null !== $this->maxsize && $this->datas['size'] > $this->maxsize) {
                return array('file_size_too_big', $this->printName(), $this->sizePrettyize($this->maxsize));
            }
            if (null !== $this->filetype) {
                switch ($this->filetype) {
                case 'image':
                    $size = @getimagesize($this->datas['tmp_name']);
                    if (!$size || !$size[0] || !$size[1]) {
                        return array('file_image', $this->printName());
                    }
                default:
                    break;
                }
            }
        } else {
            if ($this->required) {
                return array('file_required', $this->printName());
            }
        }

        return;
    }

    public function hasData()
    {
        return (null !== $this->datas && isset($this->datas['size']) && $this->datas['size'] != 0);
    }

    public function save($filename)
    {
        if (null === $this->datas) {
            return;
        }

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
        return $this->sizePrettyize($this->datas['size']);
    }

    public function sizePrettyize($size)
    {
        $unites = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po');

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
}
