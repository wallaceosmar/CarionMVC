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

namespace CarionMVC\Routing {
    
    use \CarionMVC\Http\ServerRequest;
    use \CarionMVC\Routing\Route\Route;
    use \CarionMVC\Routing\Exception\MissingControllerException;
    
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
        public function __construct( Route $router ) {
            $this->route = $router;
        }
        
        /**
         * 
         * @param \Core\Http\ServerRequest $request
         */
        public function dispatch( ServerRequest $request ) {
            $matches = $this->route->match($request);
            var_dump($matches);
            if ( false === $matches ) {
                throw new MissingControllerException('Falha ao encontar o controller.');
            }
            
            if ( is_string( $matches['target'] ) && false !== strpos( $matches['target'], '@' ) ) {
                list( $controllet, $method ) = explode( '@', $matches['target'] );
            }
            
            if ( '*' == $controllet ) {
                if ( ! isset( $matches['params']['controller'] ) ) {
                    throw new MissingControllerException('Falha ao encontar o controller.');
                }
                $controllet = $matches['params']['controller'];
            }
            
            if ( '*' == $method ) {
                if ( ! isset( $matches['params']['action'] ) ) {
                    throw new MissingControllerException('Falha ao encontar o controller.');
                }
                $controllet = $matches['params']['action'];
            }
            
            return (object) [
                'controller' => $controllet,
                'method' => $method,
                'params' => $matches['params'],
                'name' => $matches['name']
            ];
        }
        
    }
}