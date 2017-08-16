<?php

// Build the barauth function
if(!function_exists('barauth')) {
    function barauth() {
        return app('barauth');
    }
}
