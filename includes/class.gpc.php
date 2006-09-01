<?php
//  {{{ class  Req
/**
 * Flyspray
 *
 * GPC classes
 *
 * This script contains classes for $_GET, $_REQUEST, $_POST and $_COOKIE
 * to safely retrieve values. Example: Get::val('foo', 'bar') to get $_GET['foo'] or 'bar' if 
 * the key does not exist. 
 *
 * @license http://opensource.org/licenses/lgpl-license.php Lesser GNU Public License
 * @package flyspray
 * @author Pierre Habouzit
 */
 
class Req
{
    function has($key)
    {
        return isset($_REQUEST[$key]);
    }

    function val($key, $default = null)
    {
        return Req::has($key) ? $_REQUEST[$key] : $default;
    }

    //it will always return a number no matter what(null is 0)
    function num($key, $default = null)
    {
        return Filters::num(Req::val($key, $default));
    }
    
    function enum($key, $options, $default = null)
    {
        return Filters::enum(Req::val($key, $default), $options);
    }

    //always a string (null is typed to an empty string)
    function safe($key)
    {
        return Filters::noXSS(Req::val($key));
    }

    function isAlnum($key)
    {
        return Filters::isAlnum(Req::val($key));
    }
}

 // }}}
// {{{ class Post

class Post
{
    function has($key)
    {
        // XXX semantics is different for POST, as POST of '' values is never
        //     unintentionnal, whereas GET/COOKIE may have '' values for empty
        //     ones.
        return isset($_POST[$key]);
    }

    function val($key, $default = null)
    {
        return Post::has($key) ? $_POST[$key] : $default;
    }

    //it will always return a number no matter what(null is 0)
    function num($key, $default = null)
    {
        return Filters::num(Post::val($key, $default));
    }

    //always a string (null is typed to an empty string)
    function safe($key)
    {
        return Filters::noXSS(Post::val($key));
    }

    function isAlnum($key)
    {
        return Filters::isAlnum(Post::val($key));
    }
}

// }}}
// {{{ class Get

class Get
{
    function has($key)
    {
        return isset($_GET[$key]) && $_GET[$key] !== '';
    }

    function val($key, $default = null)
    {
        return Get::has($key) ? $_GET[$key] : $default;
    }

    //it will always return a number no matter what(null is 0)
    function num($key, $default = null)
    {
        return Filters::num(Get::val($key, $default));
    }

    //always a string (null is typed to an empty string)
    function safe($key)
    {
        return Filters::noXSS(Get::val($key));
    }

    function enum($key, $options, $default = null)
    {
        return Filters::enum(Get::val($key, $default), $options);
    }

}

// }}}
//{{{ class  Cookie

class Cookie
{
    function has($key)
    {
        return isset($_COOKIE[$key]) && $_COOKIE[$key] !== '';
    }

    function val($key, $default = null)
    {
        return Cookie::has($key) ? $_COOKIE[$key] : $default;
    }
}
//}}}
/** 
 * Class Filters
 *
 * This is a simple class for safe input validation
 * no mixed stuff here, functions returns always the same type.
 * @author Cristian Rodriguez R <soporte@onfocus.cl>
 * @license BSD
 * @notes this intented to be used by Flyspray internals functions/methods
 * please DO NOT use this in templates , if the code processing the input there 
 * is not safe, please fix the underlying problem.
 */
class Filters {
    /**
     * give me a number only please?
     * @param mixed $data
     * @return int
     * @access public static
     */
    function num($data)
    {
         return (int) $data;
    }
    
    /**
     * Give user input free from potentially mailicious html
     * @param mixed $data
     * @return string htmlspecialchar'ed
     * @access public static
     */
    function noXSS($data)
    {
        return (is_string($data) && strlen($data)) 
                ? htmlspecialchars($data, ENT_QUOTES , 'utf-8')
                : '';
    }
    
    /**
     * is $data alphanumeric eh ?
     * @param string $data string value to check
     * @return bool
     * @access public static
     */
    function isAlnum($data)
    {
        return ctype_alnum($data);
    }

    /**
     * Checks if $data is a value of $options and returns the first element of
     * $options if it is not (for input validation if all possible values are known)
     * @param mixed $data
     * @param array $options
     * @return mixed
     * @access public static
     */
    function enum($data, $options)
    {
        if (!in_array($data, $options) && isset($options[0])) {
            return $options[0];
        }
        
        return $data;
    }
}

/**
 * A basic function which works like the GPC classes above for any array
 * @param array $array
 * @param mixed $key
 * @param mixed $default
 * @return mixed
 * @version 1.0
 * @since 0.9.9
 * @see Backend::get_task_list()
 */
function array_get(&$array, $key, $default = null)
{
    return (isset($array[$key])) ? $array[$key] : $default;
}
?>
