<?php

namespace App\Services\Authentication;

use JsonException;

require_once("constants.php");
require_once("data_access/UsersDataAccess.php");

class JWT
{
    /**
     * Headers for JWT.
     *
     * @var array
     */
    private $headers;

    /**
     * Secret for JWT.
     *
     * @var string
     */
    private $secret;

    public function __construct()
    {
        $this->headers = [
            'alg' => 'HS256', // we are using a SHA256 algorithm
            'typ' => 'JWT', // JWT type
            'iss' => JWT_ISS, // token issuer
            'aud' => JWT_AUD // token audience
        ];
        $this->secret = JWT_SECRET; // change this to your secret code
    }

    /**
     * Generate JWT using a payload.
     *
     * @param array $payload
     * @return string
     */
    public function generate(array $payload): string
    {
        $headers = $this->encode(json_encode($this->headers)); // encode headers
        $payload["exp"] = time() + JWT_EXP; // add expiration to payload
        $payload = $this->encode(json_encode($payload)); // encode payload
        $signature = hash_hmac('SHA256', "$headers.$payload", $this->secret, true); // create SHA256 signature
        $signature = $this->encode($signature); // encode signature

        return "$headers.$payload.$signature";
    }

    /**
     * Encode JWT using base64.
     *
     * @param string $str
     * @return string
     */
    private function encode(string $str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '='); // base64 encode string
    }

    /**
     * Check if JWT is valid, return true | false.
     *
     * @param string $jwt
     * @return boolean
     */
    public function validate(string $jwt): bool
    {
        $token = explode('.', $jwt); // explode token based on JWT breaks
        if (!isset($token[1]) && !isset($token[2])) {
            return false; // fails if the header and payload is not set
        }
        $headers = base64_decode($token[0]); // decode header, create variable
        $payload = base64_decode($token[1]); // decode payload, create variable
        $clientSignature = $token[2]; // create variable for signature

        if (!json_decode($payload)) {
            return false; // fails if payload does not decode
        }

        // if the current time is greater than the expiration time, return false for the token validation
        if (time() > json_decode($payload)->exp) {
            return false; // fails if the token is expired
        }

        // create new signature to check against the received signature
        $signature = hash_hmac('SHA256', "$token[0].$token[1]", $this->secret, true); // create SHA256 signature
        $signature = $this->encode($signature); // encode signature

        // if the signatures do not match, return false for the token validation
        if ($signature != $clientSignature) {
            return false; // fails if the signatures do not match
        }

        return true; // if all checks pass, return true for the token validation
    }

    /**
     * Decode JWT and return payload as array.
     *
     * @param string $jwt
     * @return array|string
     */
    public function decodeJWT(string $jwt): array|string
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return 'Invalid JWT format: Must contain header, payload, and signature';
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        // Decode header and payload
        try {
            $headerJson = $this->base64UrlDecode($headerB64);
            $payloadJson = $this->base64UrlDecode($payloadB64);

            $header = json_decode($headerJson, true, 512, JSON_THROW_ON_ERROR);
            $payload = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return 'Failed to decode JWT: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'Invalid base64 encoding: ' . $e->getMessage();
        }

        return $payload;
    }

    // Helper function to decode base64url (JWT uses URL-safe base64)
    function base64UrlDecode(string $data): string
    {
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        $padding = strlen($data) % 4;
        if ($padding) {
            $data .= str_repeat('=', 4 - $padding);
        }
        return base64_decode($data);
    }
}