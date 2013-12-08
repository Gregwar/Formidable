<?php

namespace Gregwar\Formidable;

/**
 * Manage the form posting, this managed the posted token which is used to indicate
 * if the specific form has been posted.
 *
 * If the sessions are active, a CSRF token will be used.
 *
 * Else, a token depending on form name and installation directory will be used.
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
            $secret = array(
                'install' => __DIR__,
                'name' => $this->name,
            );

            if (isset($_SESSION)) {
                $key = sha1(__DIR__ . '/' . 'formidable_secret');

                if (isset($_SESSION[$key])) {
                    $secret['csrf'] = $_SESSION[$key];
                } else {
                    $csrf = sha1(uniqid(mt_rand(), true).'|'.gettimeofday(true));
                    $_SESSION[$key] = $csrf;
                    $secret['csrf'] = $csrf;
                }
            }

            $this->token = sha1(serialize($secret));
        }
    }

    /**
     * HTML render
     */
    public function getHtml()
    {        
        return '<input type="hidden" name="'.self::$fieldName.'" value="'.$this->getToken().'" />'."\n";
    }

    /**
     * Tell if the given form was posted
     */
    public function posted()
    {
        return (isset($_POST) && isset($_POST[self::$fieldName]) && $this->getToken() && $this->getToken() == $_POST[self::$fieldName]);
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
