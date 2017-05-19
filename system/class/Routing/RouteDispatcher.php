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
    
    /**
     * Description of RouteDispatcher
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     */
    class RouteDispatcher {
        
        /**
         *
         * @var string 
         */
        private $controller;
        
        /**
         *
         * @var string 
         */
        private $action;
        
        /**
         *
         * @var array 
         */
        private $params = [];
        
        /**
         *
         * @var string 
         */
        private $name;
        
        /**
         * 
         * @param array $args
         */
        public function __construct( $args ) {
            $this->controller = $args['controller'];
            $this->action = $args['method'];
            $this->params = $args['params'];
            $this->name = $args['name'];
        }
        
        /**
         * 
         * @return string
         */
        public function getController() {
            return $this->controller;
        }
        
        /**
         * 
         * @return bool
         */
        public function isExec() {
            return is_callable( $this->controller );
        }
        
        /**
         * 
         * @return string
         */
        public function getAction() {
            return $this->action;
        }
        
        /**
         * 
         * @return string
         */
        public function getName() {
            return $this->name;
        }
        
        /**
         * 
         * @return array
         */
        public function getParams() {
            return $this->params;
        }
        
    }
}