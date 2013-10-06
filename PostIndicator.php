<?php

namespace Gregwar\Formidable;

/**
 * Manage the form posting
 *
 * If the sessions are active, a CSRF token will be used
 * Else, a token depending on form name and installation directory will be used
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class PostIndicator
{
    public static $fieldName = 'posted_token';

    protected $name;

    /**
     * CSRF token
     */
    protected $token = null;

    public function __construct($name = '')
    {
        $this->name = $name;
    }

    /**
     * Get the token value
     */
    public function getToken()
    {
        $this->generateToken();

        return $this->token;
    }

    /**
     * Generate the token or get it from the session
     */
    protected function generateToken()
    {
        if ($this->token === null) {
            if (isset($_SESSION)) {
                $key = sha1(__DIR__ . '/' . 'formidable_secret');

                if (isset($_SESSION[$key])) {
                    $secret = $_SESSION[$key];
                } else {
                    $secret = sha1(uniqid(mt_rand(), true));
                    $_SESSION[$key] = $secret;
                }

                if ($this->name) {
                    $secret .= '/' . $this->name;
                }

                $this->token = sha1($secret);
            } else {
                $this->token = sha1(__DIR__ . '/' . $this->name);
            }
        }
    }

    /**
     * HTML render
     */
    public function getHtml()
    {        
        return '<input type="hidden" name="'.self::$fieldName.'" value="'.$this->getToken().'" />';
    }

    /**
     * Tell if the given form was posted
     */
    public function posted()
    {
        return (isset($_POST[self::$fieldName]) && $this->getTOken() && $this->getToken() == $_POST[self::$fieldName]);
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
