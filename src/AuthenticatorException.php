<?php

namespace rikudou\GoogleAuthenticator;

class AuthenticatorException extends \Exception
{
    const INVALID_SECRET_LENGTH = 1;
    const NO_RANDOMNESS_SOURCE = 2;
}