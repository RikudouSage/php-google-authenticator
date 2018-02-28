<?php

namespace rikudou\GoogleAuthenticator;

use Base32\Base32;

class Authenticator
{
    const BASE32_CHARS = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', '2', '3', '4', '5', '6', '7',
    ];
    /**
     * @var string
     */
    private $secret;
    /**
     * @var int|null
     */
    private $timeSlice;

    /**
     * @param int $length
     * @return string
     * @throws AuthenticatorException
     */
    public static function generateSecret($length = 16): string
    {
        $maxArrayIndex = count(static::BASE32_CHARS) - 1;
        if ($length < 16 || $length > 128) {
            throw new AuthenticatorException("Could not create secret, length invalid. Valid value is an integer between 16 and 128, $length given", AuthenticatorException::INVALID_SECRET_LENGTH);
        }
        $secret = "";
        for ($i = 0; $i < $length; $i++) {
            try {
                $secret .= static::BASE32_CHARS[random_int(0, $maxArrayIndex)];
            } catch (\Exception $e) {
                throw new AuthenticatorException("No randomness source for 'random_int()' found", AuthenticatorException::NO_RANDOMNESS_SOURCE);
            }
        }
        return $secret;
    }

    /**
     * Authenticator constructor.
     * @param string $secret
     * @param int|null $timeSlice
     */
    public function __construct(string $secret, int $timeSlice = null)
    {
        if (is_null($timeSlice)) {
            $timeSlice = floor(time() / 30);
        }
        $this->secret = $secret;
        $this->timeSlice = $timeSlice;
    }

    public function getCode(int $timeSlice = null): string
    {
        if (is_null($timeSlice)) {
            $timeSlice = $this->timeSlice;
        }
        $secret = Base32::decode($this->secret);
        $binaryTime = chr(0) . chr(0) . chr(0) . chr(0) . pack("N*", $timeSlice);

        $hash = hash_hmac('SHA1', $binaryTime, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);

        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
        $modulo = pow(10, 6);

        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    public function verify(string $code, int $timeSlice = null): bool
    {
        return $code === $this->getCode($timeSlice);
    }
}