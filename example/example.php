<?php

// Register classes
require_once dirname(__FILE__) . '/../src/Evp/Gsms/Autoloader.php';
Evp_Gsms_Autoloader::register();

// Create client and send message
try {
    $client = Evp_Gsms_Client::newInstance('username', 'password');
    $response = $client->send('Your telephone number', 'Receiver telephone number', 'message');

    if ($response->isSuccessful()) {
        // Do something when successful
    } else {
        // Do something when unsuccessful
        $lastResponse = $client->getLastResponse();
    }
} catch (Evp_Gsms_Exception $e) {
    $e->getMessage();
    $client->getLastResponse();
}