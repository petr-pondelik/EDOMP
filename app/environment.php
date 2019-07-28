<?php

define('ENV_DEVELOPMENT', isset($_SERVER['SERVER_ADDR']) && in_array($_SERVER['SERVER_ADDR'], ['127.0.0.1', '::1'], true));

if (ENV_DEVELOPMENT) {
    define('ENVIRONMENT', 'devel');
} else {
    define('ENVIRONMENT', 'prod');
}