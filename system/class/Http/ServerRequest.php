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

namespace Carion\Http {
    
    use ArrayAccess;
    
    /**
     * A class that helps wrap Request information and particulars about a single request.
     * 
     * Has both an Array and Object interface. You can access framework parameters using indexes:
     * 
     * `$request['controller']` or `$request->controller`.
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     * @license http://www.opensource.org/licenses/mit-license.php MIT License
     * 
     * @since 0.0.1
     * 
     * @package Core
     * @subpackage Http
     */
    class ServerRequest implements ArrayAccess {
        
        /**
         * Array of paramters parsed by the url
         * 
         * @var array 
         */
        private $params = [];
        
        /**
         * Array of data passed by the method POST
         * 
         * @var array
         */
        private $data = [];
        
        /**
         * Array of querystring arguments
         * 
         * @var array 
         */
        private $query = [];
        
        /**
         * The URL string used for the request.
         * 
         * @var string 
         */
        private $url = '';
        
        /**
         *
         * @var type 
         */
        protected $_input;
        
        /**
         * magi
         * 
         * @param string $url
         */
        public function __construct( $url = null ) {
            if ( empty( $url ) ) {
                $url = $this->_url();
            }
            $this->url = $url;
            
            $this->data = filter_var_array($_POST);
            $this->query = filter_var_array($_GET);
        }
        
        /**
         * Magic get method allows access to parsed routing parameters directly on the object.
         * 
         * @param string $name The property being accessed.
         * 
         * @return mixed Either the value of the parameter or null.
         */
        public function __get($name) {
            if ( isset ( $this->params[ $name ] ) ) {
                return $this->params[ $name ];
            }
            return null;
        }
        
        /**
         * Missing method handler, handles wrapping older style isAjax() type methods
         * 
         * @param string $name The method called
         * @param array $arguments Array of parameters for the method call
         * 
         * @return mixed
         */
        public function __call($name, $arguments) {
            if ( strpos( $name, 'is' ) === 0 ) {
                $type = strtolower( substr( $name, 2) );
                return $this->is($type);
            }
            throw new CarionException( sprintf('Method %s does not exist', $name) );
        }
        
        /* Public functions name */
        
        /**
         * 
         * @param string $name
         * 
         * @return mixed
         */
        public function query( $name ) {
            return $this->query[ $name ];
        }
        
        /**
         * 
         * @param type $name
         * @return type
         */
        public function data( $name ) {
            return $this->data[ $name ];
        }
        
        /**
         * 
         * @param type $name
         * @return type
         */
        public function param( $name ) {
            return $this->params[ $name ];
        }
        
        /**
         * 
         * @param bool $ssl
         * @return type
         */
        public function host( bool $ssl = false ) {
            if ( $ssl ) {
                return $_SERVER['HTTP_X_FORWARDED_HOST'];
            }
            return $_SERVER['HTTP_HOST'];
        }
        
        /**
         * Get the domain name and include $tld segments of the tld.
         * 
         * @param int $tld Number of segments your tld contains. For example: `example.com` contains 1 tld.
         *   While `example.co.uk` contains 2.
         * 
         * @return string Domain name without subdomains.
         */
        public function domain( int $tld = 1 ) {
            $seg = explode( '.', $this->host() );
            $domain = array_splice($seg, -1 * ( $tld + 1));
            return implode('.', $domain);
        }
        
        /**
         * Get the subdomains for a host.
         * 
         * @param int $tldLength
         * @return string
         */
        public function subdomains( int $tld = 1) {
            $segments = explode( '.', $this->host());
            return array_slice( $segments, 0, -1 * ( $tld + 1));
	}
        
        /**
         * 
         * @return string
         */
        public function requesturi () {
            return $this->_url();
        }
        
        /**
         * Get the HTTP method used for this request.
         * There are a few ways to specify a method.
         * 
         * @return string The name of the HTTP method used.
         */
        public function mehtod() {
            return $_SERVER['REQUEST_METHOD'];
        }
        
        /**
         * Check whether or not a Request is a certain type.
         * 
         * @param type $type The type of request you want to check. If an array
         *   this method will return true if the request matches any type.
         * 
         * @return boolean Whether or not the request is the type you are checking.
         */
        public function is( $type ) {
            
            if ( is_array($type) ) {
                foreach ( $type as $value ) {
                    if ( ! $this->is($value) ) {
                        return false;
                    }
                }
                return true;
            }
            
            switch ( $type ) {
                default :
                    return false;
            }
        }
        
        /**
         * Array access isset() implementation
         * 
         * @param string $offset
         * 
         * @return bool
         */
        public function offsetExists( $offset ) {
            if ( 'url' === $offset || 'data' === $offset ) {
                return true;
            }
            return isset( $this->params[ $offset ] );
        }
        
        /**
         * Array access read implementation
         * 
         * @param string $offset Name of the key being accessed.
         * 
         * @return mixed
         */
        public function offsetGet( $offset ) {
            switch ( $offset ) {
                case 'url':
                    return $this->query;
                    break;
                case 'data':
                    return $this->data;
                    break;
                default :
                    return isset ($this->params[ $offset ]) ? $this->params[ $offset ] : null;
            }
        }
        
        /**
         * Array access write implementation
         * 
         * @param string $offset Name of the key being written
         * @param mixed $value The value being written.
         */
        public function offsetSet( $offset, $value) {
            $this->params[ $offset ] = $value;
        }
        
        /**
         * Array access unset() implementation
         * 
         * @param string $offset Name to unset.
         */
        public function offsetUnset( $offset) {
            unset( $this->params[ $offset ] );
        }
        
        /**
         * Get the request uri. Looks in PATH_INFO first, as this is the exact value we need prepared 
         * by PHP.
         * 
         * @return string URI The request path that is being accessed.
         */
        protected function _url() {
            if ( isset ( $_SERVER['PATH_INFO'] ) ) {
                $uri = $_SERVER['PATH_INFO'];
            } elseif ( isset( $_SERVER['REQUEST_URI'] ) ) {
                $uri = $_SERVER['REQUEST_URI'];
                
                if( strpos($uri, $_SERVER['SCRIPT_NAME']) === 0 ) {
                    $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
                } elseif( strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0 ) {
                    $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
                }
                // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
                // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
                if( strncmp($uri, '?/', 2) === 0 ) {
                    $uri = substr($uri, 2);
                }

                $parts = preg_split('#\?#i', $uri, 2);
                $uri = $parts[0];

                if( isset( $parts[1] ) ) {
                    $_SERVER['QUERY_STRING'] = $parts[1];
                    parse_str($_SERVER['QUERY_STRING'], $_GET);
                } else {
                    $_SERVER['QUERY_STRING'] = '';
                    $_GET = array();
                }
                $uri = parse_url($uri, PHP_URL_PATH);
            } else {
                return null;
            }
            
            return str_replace([ '//', '../' ], '/', $uri);
        }
    }
}