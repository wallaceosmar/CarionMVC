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

namespace Carion\Routing {
    
    use Carion\Http\Request;
    use Carion\Routing\Route\RouteCollection;
    use Carion\Routing\Exception\MissingControllerException;
    
    /**
     * Dispatcher converts Requests into controller actions. It uses the
     * dispatched Request to locate and load the correct controller. If found,
     * the requested action is called on the controller
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     */
    class Dispatcher {

        /**
         * @var \CarionMVC\Routing\Route\Route
         */
        private $route;

        /**
         * 
         * @param type $route
         */
        public function __construct( RouteCollection $router ) {
            $this->route = $router;
        }
        
        /**
         * 
         * @param \Carion\Http\Request $request
         * 
         * @return \Carion\Routing\RouteDispatcher
         */
        public function dispatch( Request $request ) {
            $matches = $this->route->match($request);
            if ( false === $matches ) {
                throw new MissingControllerException('Falha ao encontar o controller.');
            }
            
            $controller = $matches['target'];
            if ( is_string( $controller ) && false !== strpos( $controller, '@' ) ) {
                list( $controller, $method ) = explode( '@', $controller );
            }
            if ( '*' == $controller ) {
                if ( ! isset( $matches['params']['controller'] ) ) {
                    throw new MissingControllerException('Falha ao encontar o controller.');
                }
                $controller = $matches['params']['controller'];
            }
            
            if ( '*' == $method ) {
                if ( ! isset( $matches['params']['action'] ) ) {
                    throw new MissingControllerException('Falha ao encontar o controller.');
                }
                $controllet = $matches['params']['action'];
            }
            
            return new RouteDispatcher([
                'controller' => $controller,
                'method' => $method,
                'params' => $matches['params'],
                'name' => $matches['name']
            ]);
        }
        
    }
}