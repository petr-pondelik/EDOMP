<?php

define('ENV_DEVELOPMENT', in_array($_SERVER['SERVER_ADDR'], ['127.0.0.1', '::1'], true));

if (ENV_DEVELOPMENT) {
    define('ENVIRONMENT', 'devel');
} else {
    define('ENVIRONMENT', 'prod');
}