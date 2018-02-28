# Google Authenticator compatible OTP generator 

Really simple, can be integrated in pretty much anything.

## Installation

Include `rikudou/google-authenticator` in your composer dependencies, e.g.: 
`composer require rikudou/google-authenticator`.

## Usage

```php
<?php

use rikudou\GoogleAuthenticator\Authenticator;

// create a secret key, you can than store it in db or whatever
$secretKey = Authenticator::generateSecret(); // holds something like 4O7LDGME6HHINEP7

// get otp code
$authenticator = new Authenticator($secretKey);
$otpCode = $authenticator->getCode(); // holds a string with six digits number, e.g. 408532

// verify submitted code
$userSubmittedCode = $_POST['2facode']; // just an example, get the code however you want
$isCorrect = $authenticator->verify($userSubmittedCode); // holds true or false

```

And that's it, simple as that.

### Handling exceptions

There are two cases where exception can be thrown.

```php
<?php

use rikudou\GoogleAuthenticator\Authenticator;
use rikudou\GoogleAuthenticator\AuthenticatorException;

try {
    $secret = Authenticator::generateSecret(200);
} catch (AuthenticatorException $exception) {
    switch ($exception->getCode()) {
        case AuthenticatorException::INVALID_SECRET_LENGTH:
            // Exception message: "Could not create secret, length invalid. Valid value is an integer between 16 and 128, 200 given"
            break;
        case AuthenticatorException::NO_RANDOMNESS_SOURCE:
            // Exception message: "No randomness source for 'random_int()' found"
            // This should not happen on any modern system
            break;
    }
}

```

And that's all, folks!