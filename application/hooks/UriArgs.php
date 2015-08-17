<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UriArgs {

    public function validate_args(){
        
        $RTR =& load_class('Router');

        $class  = $RTR->fetch_class();
        $method = $RTR->fetch_method();

        if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($class))) ){
            show_404("{$class}/{$method}");
        }
        $class_reflection = new ReflectionClass($class);
        $method_reflection = $class_reflection->getMethod($method);
        $URI =& load_class('URI');
        $argnum = count(array_slice($URI->rsegments, 2));
        if ($method_reflection->getNumberOfRequiredParameters() > $argnum || $method_reflection->getNumberOfParameters() < $argnum){
            show_404("{$class}/{$method}");
        }
    }
}
