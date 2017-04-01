<?php

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

namespace CarionMVC\Core {
    
    use CarionMVC\Call\Instantiate;
    use CarionMVC\Config\Config;
    use CarionMVC\Controller\Controller;
    use CarionMVC\Error\Exception\CarionException;
    use CarionMVC\Routing\Exception\MissingControllerException;
    
    /**
     * Description of App
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     * @license http://www.opensource.org/licenses/mit-license.php MIT License
     */
    class App {
        
        /**
         *
         * @var \CarionMVC\App 
         */
        protected static $instance;
        
        /**
         * Array of instances
         * 
         * @var array[] 
         */
        protected static $objects = [];
        
        /**
         * Return the instance of the class
         */
        public static function &singleton() {
            if ( ! isset( self::$instance ) ) {
                $obj = __CLASS__;
                self::$instance = new $obj;
            }
            return self::$instance;
        }
        
        /**
         * Contructor
         */
        public function __construct() {
            // Class to help to instantiate class
            self::$objects[ 'instantiate' ] = new Instantiate();
            $GLOBALS[ 'instantiate' ] = &self::$objects[ 'instantiate' ];
            
            // Class to access the config
            self::$objects[ 'config' ] = new Config();
            
            // Load Config
            self::$objects[ 'config' ]->load('config');
            
            // Request
            self::$objects[ 'request' ] = self::$objects[ 'instantiate' ]->newInstance( self::$objects[ 'config' ]->get('registry.request'), self::$objects);
            
            // Response
            self::$objects[ 'response' ] = self::$objects[ 'instantiate' ]->newInstance( self::$objects[ 'config' ]->get('registry.response'), self::$objects);
            
            // Load Dependence for dispatch
            self::$objects[ 'instantiate' ]->instantiateDependence('dispatcher', self::$objects[ 'config' ]->get('registry'), self::$objects );
            // 
            $router = &self::$objects[ 'router' ];
            require_once ( APP_CFG_PATH . 'cfg.routing.php' );
        }
        
        /**
         * Get the instance of objects
         * 
         * @param string $name
         * 
         * @return mixed
         */
        public function &__get( $name ) {
            if ( isset( self::$objects[ $name ] ) ) {
                return self::$objects[ $name ];
            }
        }
        
        /**
         * 
         * @param string $name
         * @param string|object $value
         */
        public function __set( $name, $value ) {
            if ( ! isset( self::$objects[ $name ] ) ) {
                if ( is_string( $value ) && class_exists( $value ) ) {
                    self::$objects[ $name ] = self::$objects[ 'instantiate' ]->newInstance( $value, self::$objects );
                } elseif ( is_object( $value ) &&  !$value instanceof \Closure ) {
                    self::$objects[ $name ] = $value;
                }
            }
        }
        
        /**
         * 
         * @param string $name
         * 
         * @return bool
         */
        public function __isset($name) {
            return isset( self::$objects[ $name ] );
        }
        
        /**
         * 
         * @param string $name
         */
        public function __unset($name) {
            unset( self::$objects[ $name ] );
        }
        
        /**
         * 
         * @return string
         */
        public function __toString() {
            return implode(', ', array_keys( self::$objects ));
        }
        
        /**
         * 
         */
        public function run() {
            ob_start();
            $default_error = self::$objects['config']->get('error_controller');
            try {
                $dispatch = self::$objects['dispatcher']->dispatch( self::$objects['request'] );
                
                $mountClass = "App\\Controller\\{$dispatch->controller}Controller";
                if ( ! class_exists( $mountClass ) ) {
                    $mountClass = self::$objects['config']->get('controller.error');
                    if ( !class_exists( $mountClass ) ) {
                        throw new MissingControllerException('Controller não encontrado');
                    }
                }
                
                $controller = self::$objects['instantiate']->newInstance( $mountClass, self::$objects );
                if ( !$controller instanceof Controller ) {
                    throw new CarionException( 'A classe Controller precisa ser uma extensao de Controller' );
                }
                
                if ( ! method_exists( $controller, $dispatch->method ) ) {
                    throw new MissingControllerException('Method não encontrado');
                }
                
                echo self::$objects['instantiate']->callMethod( $controller, $dispatch->method, $dispatch->params );
                
            } catch ( \Exception $ex) {
                //ob_clean();
                
                echo sprintf('[%s]: %s on %s in line %s', $ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
            }
            $contents = ob_get_contents();
            ob_end_clean();
            // Die
            die( $contents );
        }
        
    }
}