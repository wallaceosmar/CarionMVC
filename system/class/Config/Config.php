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

namespace Carion\Config {
    
    /**
     * Description of Config
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     */
    class Config {
        
        /**
         *
         * @var \Carion\Config\Config 
         */
        protected static $instance;
        
        /**
         *
         * @var array 
         */
        protected static $config = [];
        
        /**
         * 
         * @return type
         */
        public static function &singleton() {
            if ( isset( self::$instance ) ) {
                $obj = __CLASS__;
                self::$instance = new $obj;
            }
            return self::$instance;
        }
        
        /**
         * Construct
         */
        public function __construct() {
            self::$config = include ( CORE_INC_PATH . 'default-settings.php' );
        }
        
        /**
         * Searches the $items array and returns the item
         * 
         * @param string $key
         * 
         * @return mixed Description
         */
        public function get( $key ) {
            $key = explode('.', $key );
            $_return = self::$config;
            foreach ( $key as $_key ) {
                
                if ( ! isset( $_return[ $_key ] ) ) {
                    return null;
                }
                
                $_return = $_return[ $_key ];
            }
            return $_return;
        }
        
        /**
         * 
         * @param string $name
         * 
         * @param mixed $value
         */
        public function set( $name, $value = '' ) {
            $key = explode('.', $key );
            $_next = &self::$config;
            foreach ( $key as $_key ) {
                if ( ! isset( $_next[ $_key ] ) ) {
                    $_next[ $_key ] = [];
                }
                $_next = &$_next[ $_key ];
            }
            $_next = $value;
        }
        
        /**
         * 
         * @param string $name
         */
        public function load( $name ) {
            if ( file_exists( $filename = APP_CFG_PATH . "cfg.{$name}.php" ) ) {
                self::$config = array_merge_recursive( self::$config, include( $filename ));
            }
        }
        
    }
}