<?php ! defined('BASEPATH') && exit( 'No direct script access allowed' );

/* 
 * The MIT License
 *
 * Copyright 2017 Wallace Osmar.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use CarionMVC\Controller\Controller;

/**
 * Tests if an input is valid PHP serialized string.
 *
 * Checks if a string is serialized using quick string manipulation
 * to throw out obviously incorrect strings. Unserialize is then run
 * on the string to perform the final verification.
 *
 * Valid serialized forms are the following:
 * <ul>
 * <li>boolean: <code>b:1;</code></li>
 * <li>integer: <code>i:1;</code></li>
 * <li>double: <code>d:0.2;</code></li>
 * <li>string: <code>s:4:"test";</code></li>
 * <li>array: <code>a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}</code></li>
 * <li>object: <code>O:8:"stdClass":0:{}</code></li>
 * <li>null: <code>N;</code></li>
 * </ul>
 *
 * @author		Chris Smith <code+php@chris.cs278.org>
 * @copyright	        Copyright (c) 2009 Chris Smith (http://www.cs278.org/)
 * @license		http://sam.zoy.org/wtfpl/ WTFPL
 * @param		string	$value	Value to test for serialized form
 * @param		mixed	$result	Result of unserialize() of the $value
 * @return		boolean			True if $value is serialized data, otherwise false
 * 
 * @package             Core
 * @subpackage          Functions\Util
 * 
 * @access              public
 */
function is_serialized($value, &$result = null) {
    // Bit of a give away this one
    if (!is_string($value)) {
        return false;
    }
    // Serialized false, return true. unserialize() returns false on an
    // invalid string or it could return false if the string is serialized
    // false, eliminate that possibility.
    if ($value === 'b:0;') {
        $result = false;
        return true;
    }
    $length = strlen($value);
    $end = '';
    switch ($value[0]) {
        case 's':
            if ($value[$length - 2] !== '"') {
                return false;
            }
        case 'b':
        case 'i':
        case 'd':
            // This looks odd but it is quicker than isset()ing
            $end .= ';';
        case 'a':
        case 'O':
            $end .= '}';
            if ($value[1] !== ':') {
                return false;
            }
            switch ( $value[2] ) {
                case 0:
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                    break;
                default:
                    return false;
            }
        case 'N':
            $end .= ';';
            if ($value[$length - 1] !== $end[0]) {
                return false;
            }
            break;
        default:
            return false;
    }
    if (($result = @unserialize($value)) === false){
        $result = null;
        return false;
    }
    return true;
}

/**
 * Serialize data, if needed.
 * 
 * @package Core
 * @subpackage Functions\Util
 * 
 * @param string|array|object $data Data that might be serialized.
 * 
 * @return mixed A scalar data
 * @access public
 */
function maybe_serialize( $data ) {
    if ( is_array( $data ) || is_object( $data ) ) {
        return serialize($data);
    }
    return $data;
}

/**
 * Unserialize value only if it was serialized.
 * 
 * @package Core
 * @subpackage Functions\Util
 * 
 * @param string $original Maybe unserialized original, if is needed.
 * 
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize( $original ) {
    if ( is_serialized( $original ) ) {
        return @unserialize( $original );
    }
    return $original;
}

/**
 * Gets an environment variable from available sources.
 * 
 * @param string $key Environment variable name.
 * 
 * @return mixed Environment variable setting.
 */
function env( $key ) {
    switch ( $key ) {
        case 'HTTPS':
            if ( isset( $_SERVER['HTTPS'] ) ) {
                return ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' );
            }
            return (strpos(env('SCRIPT_URI'), 'https://') === 0);
        case 'CGI_MODE':
            return (PHP_SAPI === 'cgi');
        default :
            $val = NULL;
            if ( isset( $_SERVER[$key] ) ) {
                $val = $_SERVER[$key];
            } elseif ( isset( $_ENV[$key] ) ) {
                $val = $_ENV[$key];
            } elseif ( getenv($key) !== false ) {
                $val = getenv($key);
            }
            return $val;
    }
}

/**
 * Get the singleton of the Controller class
 * 
 * @return object
 */
function &get_instance() {
    return Controller::singleton();
}