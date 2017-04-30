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

namespace Carion\Helper {
    
    use ArrayAccess;
    use ArrayIterator;
    use Countable;
    use IteratorAggregate;
    
    /**
     * Description of Registry
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     */
    class Registry implements ArrayAccess, Countable, IteratorAggregate {
        
        /**
         * 
         * @var array 
         */
        protected $data = [];
        
        /**
         * Clear all values
         */
        public function clear() {
            $this->data = [];
        }
        
        /**
         * 
         * @param string $key
         * @param \Closure $value
         */
        public function singleton( $key, $value ) {
            $this->set ( $key, function ( $c ) use( $value ) {
                static $object;
                
                if ( null === $object ) {
                    $object = $value($c);
                }
                
                return $object;
            });
        }
        
        /**
         * Normalize the key string
         * 
         * @param string $key
         * 
         * @return string
         */
        protected function normalizeKey( $key ) {
            return $key;
        }
        
        /**
         * 
         * @param string $name
         * @param mixed $value
         */
        public function set( $name, $value ) {
            $this->data[ $this->normalizeKey($name) ] = $value;
        }
        
        /**
         * 
         * @param string $key
         * @param mixed $default
         * 
         * @return type
         */
        public function get( $key, $default = null ) {
            if ( $this->has($key) ) {
                $key = $this->normalizeKey($key);
                if ( is_object($this->data[ $key ]) && method_exists($this->data[ $key ], '__invoke') ) {
                    $default = $this->data[ $key ]($this);
                } else {
                    $default = $this->data[ $key ];
                }
            }
            return $default;
        }
        
        /**
         * 
         * @param type $key
         */
        public function remove( $key ) {
            unset( $this->data[ $this->normalizeKey($key) ] );
        }
        
        /**
         * 
         * @param string $key
         * 
         * @return bool
         */
        public function has( string $key ) : bool {
            return isset( $this->data[ $this->normalizeKey($key) ] );
        }
        
        /**
         * 
         * @return int
         */
        public function count(): int {
            return count( $this->data );
        }
        
        /**
         * 
         * @return \Traversable
         */
        public function getIterator(): \Traversable {
            return new ArrayIterator( $this->data );
        }
        
        /**
         * 
         * @param bool $offset
         */
        public function offsetExists($offset): bool {
            return $this->has($offset);
        }
        
        /**
         * 
         * @param type $offset
         */
        public function offsetGet($offset) {
            return $this->get($offset);
        }
        
        /**
         * 
         * @param type $offset
         * @param type $value
         */
        public function offsetSet($offset, $value): void {
            $this->set($offset, $value);
        }
        
        /**
         * 
         * @param type $offset
         */
        public function offsetUnset($offset): void {
            $this->remove( $offset );
        }

    }
}