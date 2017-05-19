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

use Carion\Config\Config;
use Carion\Http\Request;
use Carion\Http\Response;
use Carion\Routing\Dispatcher;
use Carion\Routing\Route\RouteCollection;
use Carion\Error\CarionErrorHandler;

return [
    
    /**
     * 
     */
    'config' => function ($c) {
        return $c['instantiate']->newInstance( Config::class , $c);
    },
    
    /**
     * 
     */
    'response' => function ( $c ) {
        return $c->instantiate->newInstance( Response::class, $c );
    },
    
    /**
     * 
     */
    'request' => function ( $c ) {
        return $c['instantiate']->newInstance( Request::class, $c);
    },
    
    /**
     * 
     */
    'router' => function ($c) {
        $router = $c['instantiate']->newInstance( RouteCollection::class, $c );
        
        require_once ( APP_CFG_PATH . 'cfg.routing.php' );
        
        return $router;
    },
    
    /**
     * 
     */
    'dispatcher' => function ($c) {
        return $c['instantiate']->newInstance( Dispatcher::class , $c);
    },
    
    /**
     * 
     */
    'errorHandler' => function( $c ) {
        return $c['instantiate']->newInstance( CarionErrorHandler::class, $c );
    }
            
];