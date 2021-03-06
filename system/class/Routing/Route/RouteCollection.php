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

namespace Carion\Routing\Route {
    
    use \Traversable;
    use \Carion\Http\Request;
    use \Carion\Error\Exception\CarionException;
    use \Carion\Routing\Exception\DuplicateNamedRouteException;
    
    /**
     * Description of RouteCollection
     * 
     * Based in AltoRouter
     * 
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     * @link https://github.com/dannyvankooten/AltoRouter AltoRouter
     * @since 0.0.1
     */
    final class RouteCollection {
        /**
         * 
         */
        const PARSE_REGEX = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';
        
        /**
         * Array of all routes (incl. named routes).
         * 
         * @var array
         */
        protected $routes = [];
        
        /**
         * Array of all named routes.
         * 
         * @var type 
         */
        protected $namedRoutes = [];
        
        /**
         * Uri
         * 
         * @var string
         */
        protected $basePath = '/';
        
        /**
         * Array of default match types (regex helpers)
         * 
         * @var array
         */
        protected $matchTypes = [
            'i'  => '[0-9]++',
            'a'  => '[0-9A-Za-z]++',
            'h'  => '[0-9A-Fa-f]++',
            '*'  => '.+?',
            '**' => '.++',
            ''   => '[^/\.]++'
	];
        
        /**
         * Retrieves all routes.
         * 
         * @return array
         */
        public function getRoutes() {
            return $this->routes;
        }
        
        /**
         * 
         * @param array|Traversable $routes
         * 
         * @throws CarionException
         */
        public function addRoutes( $routes ) {
            if( ! is_array($routes) && ! $routes instanceof Traversable ) {
                throw new CarionException('Routes should be an array or an instance of Traversable');
            }
            foreach ( $routes as $route ) {
                call_user_func_array([ $this, 'map' ], $route);
            }
        }
        
        /**
         * 
         * @param string $basepath
         */
        public function setBasepath( $basepath = '/' ) {
            $this->basePath = $basepath;
        }
        
        /**
         * 
         * @return string
         */
        public function getBasepath() {
            return $this->basePath;
        }
        
        /**
         * 
         */
        public function clearBasepath() {
            $this->basePath = '/';
        }
        
        /**
         * 
         * @param array $matchTypes
         */
        public function addMatchTypes($matchTypes) {
            $this->matchTypes = array_merge($this->matchTypes, $matchTypes);
        }
        
        /**
         * Map a route to a target
         * 
         * @param string|array $method One of 5 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PATCH|PUT|DELETE)
         * @param string $route The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
         * @param string $target The target where this route should point to. Can be anything.
         * @param string $name Optional name of this route. Supply if you want to reverse route this url in your application.
         * 
         * @throws \CarionMVC\Routing\Exception\DuplicateNamedRouteException
         */
        public function map( $method, $route, $target, $name = null ) {
            $method = implode('|', (array) $method);
            
            // Mount my Route
            $route = $this->mountRoute($route);
            $this->routes[] = [ $method, $route, $target, $name ];
            if ( $name ) {
                if ( isset ( $this->namedRoutes[ $name ] ) ) {
                    throw new DuplicateNamedRouteException("Can not redeclare route '{$name}'");
                } else {
                    $this->namedRoutes[ $name ] = $route;
                }
            }
            return $this;
        }
        
        /**
         * 
         * @param string|array $group
         * @param \Closure $callback
         */
        public function group ( $group, $callback ) {
            if ( is_string( $group ) ) {
                $group = [ 'path' => $group ];
            }
            
            if ( isset( $group['domain'] ) && $_SERVER['SERVER_NAME'] !== $group['domain'] ) {
                return;
            }
            
            if ( isset( $group['path'] ) ) {
                $aux = $this->getBasepath();
                $this->setBasepath( $this->mountRoute( $group['path'] ) );
            }
            // Call Closure
            $callback( $this );
            if ( isset( $group['path'] ) ) {
                $this->setBasepath($aux);
            }
        }
        
        /**
         * Match a given Request Url against stored routes
         * 
         * @param \CarionMVC\Http\ServerRequest $request
         * 
         * @return object Array with route information on success, false on failure (no match).
         */
        public function match ( Request $request ) {
            $params = [];
            $match = false;
            
            // set Request Url if it isn't passed as parameter
            $requestUrl = $request->requesturi();
            
            // set Request Method if it isn't passed as a parameter
            $requestMethod = $request->method();
            foreach($this->routes as $handler) {
                list($methods, $route, $target, $name) = $handler;
                $method_match = (stripos($methods, $requestMethod) !== false);
                // Method did not match, continue to next route.
                if (!$method_match) continue;
                
                if ( $route === '*' ) {
                    // * wildcard (matches all)
                    $match = true;
                } elseif (isset($route[0]) && $route[0] === '@') {
                    // @ regex delimiter
                    $pattern = '`' . substr($route, 1) . '`u';
                    $match = preg_match($pattern, $requestUrl, $params) === 1;
                } elseif (($position = strpos($route, '[')) === false) {
                    // No params in url, do string comparison
                    $match = strcmp($requestUrl, $route) === 0;
                } else {
                    // Compare longest non-param string with url
                    if (strncmp($requestUrl, $route, $position) !== 0) {
                        continue;
                    }
                    $regex = $this->compileRoute($route);
                    $match = preg_match($regex, $requestUrl, $params) === 1;
                }
                if ($match) {
                    if ($params) {
                        foreach($params as $key => $value) {
                            if(is_numeric($key)) unset($params[$key]);
                        }
                    }
                    return array(
                        'target' => $target,
                        'params' => $params,
                        'name' => $name
                    );
                }
            }
            return false;
        }
        
        /**
         * Reversed routing
         *
         * Generate the URL for a named route. Replace regexes with supplied parameters
         *
         * @param string $routeName The name of the route.
         * @param array @params Associative array of parameters to replace placeholders with.
         * 
         * @return string The URL of the route with named parameters in place.
         * @throws Exception
         */
        public function generate ( $routeName, $params = [] ) {
            if ( !isset( $this->namedRoutes[ $routeName ] ) ) {
                throw new \Exception("Route '{$routeName}' does not exist.");
            }
            // Replace named parameters
            $route = $this->namedRoutes[$routeName];
            // prepend base path to route url again
            $url = $route;
            if ( preg_match_all( self::PARSE_REGEX, $route, $matches, PREG_SET_ORDER)) {
                foreach($matches as $match) {
                    list($block, $pre, $type, $param, $optional) = $match;
                    if ( $pre ) {
                        $block = substr($block, 1);
                    }
                    if( isset( $params[$param] ) ) {
                        $url = str_replace($block, $params[$param], $url);
                    } elseif ($optional) {
                        $url = str_replace($pre . $block, '', $url);
                    }
                }
            }
            return $url;
        }
        
        /**
         * 
         * @param type $route
         * 
         * @return type
         */
        private function compileRoute ( $route ) {
            if ( preg_match_all( self::PARSE_REGEX, $route, $matches, PREG_SET_ORDER )) {
		$matchTypes = $this->matchTypes;
                foreach($matches as $match) {
                    list($block, $pre, $type, $param, $optional) = $match;
                    
                    if (isset($matchTypes[$type])) {
                        $type = $matchTypes[$type];
                    }
                    
                    if ($pre === '.') {
                        $pre = '\.';
                    }
                    
                    //Older versions of PCRE require the 'P' in (?P<named>)
                    $pattern = '(?:'
                            . ($pre !== '' ? $pre : null)
                            . '('
                            . ($param !== '' ? "?P<$param>" : null)
                            . $type
                            . '))'
                            . ($optional !== '' ? '?' : null);
                    $route = str_replace($block, $pattern, $route);
                }
            }
            return "`^$route$`u";
        }
        
        /**
         * 
         * @param string $route
         * 
         * @return string
         */
        private function mountRoute( $route ) {
            return rtrim( $this->basePath, '/' ) . '/' . ltrim ( $route, '/' );
        }
    }
}