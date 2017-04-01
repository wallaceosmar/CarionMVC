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

/** Begin Application Constants */
define( 'APP_PATH', BASEPATH . 'application' . DS );
define( 'APP_CTL_PATH', APP_PATH . 'Controllers' . DS );
define( 'APP_MDL_PATH', APP_PATH . 'Models' . DS );
define( 'APP_VIEW_PATH', APP_PATH . 'Views' . DS );
define( 'APP_CFG_PATH', APP_PATH . 'config' . DS );
/** End Application Constants */

/** Begin Core Constants */
define( 'CORE_PATH', BASEPATH . 'system' . DS );
define( 'CORE_CLS_PATH', CORE_PATH . 'class' . DS );
define( 'CORE_FNC_PATH', CORE_PATH . 'function' . DS );
define( 'CORE_INC_PATH', CORE_PATH . 'include' . DS );
define( 'CORE_LIB_PATH', CORE_PATH . 'libs' . DS );
/** End Core Constants */

/** Begin Data Constants */
define( 'DATA_PATH', BASEPATH . 'data' . DS );
define( 'DATA_CACHE_PATH', DATA_PATH . 'cache' . DS );
define( 'DATA_TMP_PATH', DATA_PATH . 'tmp' . DS );
define( 'DATA_UPLOAD_PATH', DATA_PATH . 'upload' . DS );
/** End Data Constants */