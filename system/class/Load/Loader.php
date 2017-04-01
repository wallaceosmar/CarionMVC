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

namespace CarionMVC\Load {
    
    use \ReflectionClass;
    use \CarionMVC\Error\Exception\CarionException;
    
    /**
     * Description of Loader
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     */
    class Loader {
        
        /**
         * List of loaded models
         * 
         * @var array 
         */
        private $load_models = [];
        
        /**
         * List of loaded library
         * 
         * @var array 
         */
        private $load_library = [];
        
        /**
         * List of loaded functions
         * 
         * @var array 
         */
        private $load_function = [];
        
        /**
         * Model Loader
         * 
         * Loads and instantiates models.
         * 
         * @param string $model Model name
         * @param string $name An optional object name to assign to
         * 
         * @return $this
         */
        public function model( $model, $name = '' ) {
            
            if ( empty( $name ) ) {
                $name = $model;
            }
            
            if ( in_array($name, $this->load_models, true) ) {
                return $this;
            }
            
            return $this;
        }
        
        /**
         * 
         * @param type $library
         * @param type $params
         * @param type $object_name
         * 
         * @return $this
         * 
         * @throws CarionException
         */
        public function library( $library, $params = NULL, $object_name = NULL ) {
            
            if ( empty( $library ) ) {
                return $this;
            } elseif ( is_array( $library ) ) {
                foreach ( $library as $key => $value ) {
                    if ( is_string( $key ) ) {
                        $this->library($library, $params, $params);
                    } else {
                        $this->library($library, $params);
                    }
                }
                return $this;
            }
            
            if ( null !== $params && is_array( $params ) ) {
                $params = null;
            }
            
            $library = ucfirst($library);
            $library = 'Lib\\' . $library;
            
            $CI = &get_instance();
            if ( isset( $CI->$object_name ) ) {
                if ( $CI->$object_name instanceof $library ) {
                    throw new CarionException();
                }
            }
            
            $this->load_library[ $object_name ] = $library;
            $CI->$object_name = $this->newinstance( $library, $params);
            
            return $this;
        }
        
        /**
         * Group of functions to load
         * 
         * @param string $group
         */
        public function functions ( $group ) {
            $group = ucfirst($group);
            
            if ( in_array( $group, $this->load_function ) ) {
                throw new CarionException('');
            }
            
            if ( ! file_exists( CORE_FNC_PATH . "{$group}.fn.php" ) ) {
                throw new CarionException('');
            }
            
            require_once ( CORE_FNC_PATH . $group . '.fn.php' );
            $this->load_function[] = $group;
        }
        
        /**
         * Instantiate the class with the params
         * 
         * @param mixed $argument <p>
	 * Either a string containing the name of the class to
	 * reflect, or an object.
         * 
         * @param array $args [optional]
         *  The parameters to be passed to the class constructor as an array.
         * 
         * @return object a new instance of the class.
         */
        private function &newinstance( $class_name, $args = [] ) {
            $reflection = new ReflectionClass( $class_name );
            
            return $reflection->newInstanceArgs($args);
        }
        
    }
    
}