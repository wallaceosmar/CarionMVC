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

namespace Carion {

    use Carion\Call\Instantiate;
    use Carion\Helper\Registry;

    /**
     * Carion
     * 
     * @todo Implement class hooks
     * 
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     * @package Carion
     */
    class Carion {
        
        /**
         *
         * @var Carion 
         */
        protected static $instance = null;
        
        /**
         * 
         * @var array[] 
         */
        protected $hooks = [
            'before' => [],
            'after' => []
        ];
        
        /**
         *
         * @var Registry 
         */
        protected $container;
        
        /**
         * 
         */
        public function __construct() {
            ob_start();
            
            $this->container = new Registry();
            $this->container['instantiate'] = new Instantiate();
            
            // Require a array with the default singletons
            $singletons = include ( CORE_INC_PATH . 'default-singletons.php' );
            foreach ( (array) $singletons as $name => $singletons ) {
                $this->container->singleton( $name, $singletons);
            }
            
            // Require a array with the custom singletons
            $singletons = include ( APP_CFG_PATH . 'cfg.singleton.php' );
            foreach ( (array) $singletons as $name => $singletons ) {
                $this->container->singleton( $name, $singletons);
            }
            
            // Register error Handler
            $this->errorHandler->register();
        }
        
        /**
         * 
         * @return Carion
         */
        public static function &singleton() {
            if ( ! isset( self::$instance ) ) {
                $obj = __CLASS__;
                self::$instance = new $obj;
            }
            return self::$instance;
        }
        
        /**
         * 
         * @param string $name
         * 
         * @return mixed
         */
        public function get( $name ) {
            return $this->container->get($name);
        }
        
        /**
         * 
         * @param string $name
         * 
         * @return mixed
         */
        public function __get($name) {
            return $this->get($name);
        }
        
        /**
         * 
         * @param string $name
         * @param mixed $value
         */
        public function set( $name, $value ) {
            $this->container->set($name, $value);
        }
        
        /**
         * 
         * @param string $name
         * @param type $value
         */
        public function __set($name, $value) {
            $this->set($name, $value);
        }
        
        /**
         * 
         */
        public function stop() {
            $this->response->setContent( ob_get_contents() );
            ob_end_clean();
            $this->response->display();
            exit;
        }
        
        /**
         * Run
         */
        public function run() {
            try {
                
                /* @var $dispatch Routing\RouteDispatcher */
                $dispatch = $this->dispatcher->dispatch( $this->request );
                if ( $dispatch->isExec() ) {
                    echo $this->instantiate->callMethod($dispatch->getController(), '__invoke', $dispatch->getParams());
                } else {
                    $mountClass = "App\\Controller\\{$dispatch->getController()}Controller";
                    if ( ! class_exists( $mountClass ) ) {
                        $mountClass = $this->config->get('controller.error');
                        if ( !class_exists( $mountClass ) ) {
                            throw new MissingControllerException('Controller não encontrado');
                        }
                    }

                    // Initialize The Controller Instance
                    $controller = $this->instantiate->newInstance( $mountClass, $this->container );
                    if ( ! method_exists( $controller, $dispatch->getAction() ) ) {
                        throw new MissingControllerException('Method não encontrado');
                    }

                    echo $this->instantiate->callMethod( $controller, $dispatch->getAction(), $dispatch->getParams() );
                }
            } catch (Exception $ex) {
                ob_clean();
                echo sprintf('[%s]: %s on %s in line %s', $ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
            }
            
            $this->response->withBody( ob_get_contents() );
            ob_end_clean();
            die( $this->response->getBody() );
        }
    
    }
}