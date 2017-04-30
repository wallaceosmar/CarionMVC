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
    
    use Carion\Config\Config;
    use Carion\Call\Instantiate;
    use Carion\Helper\Registry;
    use Carion\Routing\Dispatcher;
    use Carion\Routing\Route\Route;
    use Carion\Http\ServerResponse;
    use Carion\Http\ServerRequest;
    
    /**
     * Carion
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     * @package Carion
     */
    class Carion {
        
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
            
            $this->container = new Registry();
            $this->container['instantiate'] = new Instantiate();
            
            /**
             * 
             */
            $this->container->singleton('config', function( $c ){
                return $c->instantiate->newInstance( new Config(), $c);
            });
            
            /**
             * 
             */
            $this->container->singleton('response', function( $c ){
                return new ServerResponse();
            });
            
            /**
             * 
             */
            $this->container->singleton('request', function ($c) {
                return new ServerRequest();
            });
            
            /**
             * 
             */
            $this->container->singleton('router', function($c){
                $router = new Route();
                
                require_once ( APP_CFG_PATH . 'cfg.routing.php' );
                
                return $router;
            });
            
            /**
             * 
             */
            $this->container->singleton('dispatcher', function($c){
                return new Dispatcher($c['router']);
            });
            
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
         * Run
         */
        public function run() {
            ob_start();
            try {
                
                $dispatch = $this->dispatcher->dispatch( $this->request );
                $mountClass = "App\\Controller\\{$dispatch->getController()}Controller";
                if ( ! class_exists( $mountClass ) ) {
                    $mountClass = $this->config->get('controller.error');
                    if ( !class_exists( $mountClass ) ) {
                        throw new MissingControllerException('Controller não encontrado');
                    }
                }
                
                $controller = $this->instantiate->newInstance( $mountClass, $this->container );
                
                if ( ! method_exists( $controller, $dispatch->getAction() ) ) {
                    throw new MissingControllerException('Method não encontrado');
                }
                
                echo $this->instantiate->callMethod( $controller, $dispatch->getAction(), $dispatch->getParams() );
                
            } catch (Exception $ex) {
                ob_clean();
                echo sprintf('[%s]: %s on %s in line %s', $ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
            }
            
            $this->response->setContent( ob_get_contents() );
            ob_end_clean();
            $this->response->display();
        }
    
    }
}