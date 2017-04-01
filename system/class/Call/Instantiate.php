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

namespace CarionMVC\Call {
    
    use ReflectionClass;
    use ReflectionMethod;
    use CarionMVC\Call\ParseParams;
    
    /**
     * Description of Call
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     * @license http://www.opensource.org/licenses/mit-license.php MIT License
     */
    class Instantiate {
        
        use ParseParams;
        
        /**
         * 
         * @param type $class
         * @param type $param_arr
         * 
         * @return type
         */
        public function newInstance( $class, $param_arr, $settings_arr = [] ) {
            $reflection = new ReflectionClass( $class );
            if ( is_a( $constructMethod = $reflection->getConstructor(), '\ReflectionMethod' ) ) {
                return $reflection->newInstanceArgs( $this->_parseParams( $reflection->getConstructor() , $param_arr, $settings_arr) );
            }
            return $reflection->newInstance();
        }
        
        /**
         * 
         * @param type $class
         * 
         * @return type
         */
        public function needInstance( $class ) {
            $reflection = new ReflectionClass( $class );
            if ( is_a( $constructMethod = $reflection->getConstructor(), '\ReflectionMethod' ) ) {
                return $constructMethod->getParameters();
            }
            return null;
        }
        
        /**
         * 
         * @param array[] $object
         */
        public function instantiateDependence( $className, $listDependence, &$object = [] ) {
            if ( isset( $listDependence[ $className ] ) ) {
                if ( ! is_null( $this->needInstance( $listDependence[ $className ] ) ) ) {
                    foreach ( $this->needInstance( $listDependence[ $className ] ) as $_class ) {
                        $this->instantiateDependence( $_class->getName(), $listDependence, $object );
                    }
                }
                $object[ $className ] = $this->newInstance( $listDependence[ $className ], $object);
            }
        }
        
        /**
         * 
         * @param type $object
         * @param type $method
         * @param type $param_arr
         * 
         * @return type
         */
        public function callMethod( $object, $method, $param_arr ) {
            $param_arr = $this->_parseParams( new ReflectionMethod( $object, $method), $param_arr);
            return call_user_func_array([ $object, $method ], $param_arr);
        }
        
    }
}
