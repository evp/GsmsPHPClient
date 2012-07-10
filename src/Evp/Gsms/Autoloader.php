<?php

/**
 * Evp_Gsms_Autoloader
 *
 * @author Šarūnas Dubinskas <s.dubinskas@evp.lt>
 */
class Evp_Gsms_Autoloader
{
    /**
     * Registers GatewayClient_Autoloader as an SPL autoloader.
     */
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array('Evp_Gsms_Autoloader', 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        if (0 !== strpos($class, 'Evp_Gsms')) {
            return;
        }

        if (file_exists($file = dirname(__FILE__) . '/../../' . str_replace('_', '/', $class) . '.php')) {
            require $file;
        }
    }
}