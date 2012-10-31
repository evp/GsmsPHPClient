#GsmsPhpClient

##What is GsmsPhpClient?
GsmsPhpClient is a helper-library, that will allow you to easily integrate sending SMS messages functionality into your
website if you are in possession of an active Gsms.lt account with a positive balance.

In short: this is a wrapper for the existing Gsms.lt API - [Download DOC format specification](https://www.gsms.lt/f/gsms_specifikacija.doc)

##Sections
* [Requirements](##Requirements)
* [Installation](##Installation)
* [Code samples](##Code samples)
* [Additional notes](##Additional notes)
* [Code samples](##Contacts)

##Requirements
* An active gsms.lt account
* PHP 5.1.2+

##Installation
* Use `git clone https://github.com/evp/GsmsPHPClient.git` to copy the GsmsPHPClient directory to your project directory.
* Add the following code to your PHP file where you intend to use the SMS send functionality:

```php
   require_once '/path/to/GsmsPHPClient/src/Evp/Gsms/Autoloader.php';
   Evp_Gsms_Autoloader::register();
```

This will ensure that all of the library's classes are properly loaded.
Make sure you change the 'path/to' to the actual path of the GsmsPHPClient.

Congratulations, you have successfully installed GsmsPHPClient!


##Code samples
Once you have installed the library, you are now ready to create a Gsms_Client

```
  $client = Evp_Gsms_Client::newInstance('username', 'password')
```

Where "username" and "password" are your gsms.lt account login and password accordingly.

Now use the client to send your first SMS message:

```php
$response = $client->send('Your telephone number', 'Receiver telephone number', 'message');
```

You are only allowed to send SMS from the numbers that have been added to your account via gsms.lt web interface,
which means that you are not to specify a random telephone number as the first parameter of the send method.

Now check if the SMS you sent has completed without a hitch with the $response object that Gsms_Client::send
method returns.

```php
    if ($response->isSuccessful()) {
           // Do something when the sms has been sent
       } else {
           // Do something when the sms has not been sent
           $lastResponse = $client->getLastResponse();
       }
```

The $lastResponse string will provide feedback from the API and will help you debug any issues you might encounter.

##Additional notes
Gsms_Client::send method will throw an exception if the response from the API is invalid.
It is always a good idea to wrap a try-catch clause around your code in to anticipate such behaviour

```php
  try {
    # This is where you send your message
  } catch (Evp_Gsms_Exception $e) {
      $e->getMessage();
      $client->getLastResponse();
  }
```


##Contacts
If you have any further questions feel free to contact us:

"EVP International", UAB

Mėnulio g. 7

LT-04326 Vilnius

Email: pagalba@gsms.lt

Tel. +370 (5) 2 03 27 19

Faksas +370 (5) 2 63 91 79
