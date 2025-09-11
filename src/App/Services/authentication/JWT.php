<?php
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
            // 'iss' => 'jwt.local', // token issuer
            'iss' => JWT_ISS, // token issuer
            // 'aud' => 'example.com' // token audience
            'aud' => JWT_AUD // token audience
        ];
        // $this->secret = 'thisIsASecret'; // change this to your secret code
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
        // $payload["exp"] = time() + 60; // add expiration to payload
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
        if (json_decode($payload)->exp <= time()) {
            return false; // fails if expiration is greater than 0, setup for 120 minute
        }

        // if ((json_decode($payload)->exp - time()) < 0) {
        //     return false; // fails if expiration is greater than 0, setup for 120 minute
        // }

        if (isset(json_decode($payload)->iss)) {
            if (json_decode($headers)->iss != json_decode($payload)->iss) {
                return false; // fails if issuers are not the same
            }
        } else {
            return false; // fails if issuer is not set 
        }

        if (isset(json_decode($payload)->aud)) {
            if (json_decode($headers)->aud != json_decode($payload)->aud) {
                return false; // fails if audiences are not the same
            }
        } else {
            return false; // fails if audience is not set
        }

        // if user is found then create object of UserDataAccess class
        $userDataAccess = new UsersDataAccess();
        // check if user exists in database
        if (isset(json_decode($payload)->id)) {
            $user = $userDataAccess->getUserById(json_decode($payload)->id);
            if (!$user) {
                return false; // fails if user is not found
            } else {
                // update user last login time
                $update =    $userDataAccess->updateUser(
                    json_decode($payload)->id,
                    [
                        'last_login' => date('Y-m-d H:i:s')
                    ]
                );
            }
        }


        $base64_header = $this->encode($headers);
        $base64_payload = $this->encode($payload);

        $signature = hash_hmac('SHA256', $base64_header . "." . $base64_payload, $this->secret, true);
        $base64_signature = $this->encode($signature);

        return ($base64_signature === $clientSignature);
    }

    /**
     * Decode JWT.
     *
     * @param string $jwt
     * @return array
     */

    public function decode($jwt): array
    {
        var_dump($jwt);
        return json_decode(
            base64_decode(
                str_replace('_', '/', str_replace('-', '+', explode('.', $jwt)[1]))
            ),
            true
        );
    }

    // function decodeJWT($jwt) {
    //     // Split the JWT into its three parts: header, payload, signature
    //     $parts = explode('.', $jwt);

    //     if (count($parts) !== 3) {
    //         return ['error' => 'Invalid JWT format'];
    //     }

    //     list($header, $payload, $signature) = $parts;

    //     // Function to decode Base64 URL-safe strings
    //     $decodeBase64Url = function($input) {
    //         $remainder = strlen($input) % 4;
    //         if ($remainder) {
    //             $padlen = 4 - $remainder;
    //             $input .= str_repeat('=', $padlen);
    //         }
    //         return base64_decode(strtr($input, '-_', '+/'));
    //     };

    //     // Decode header and payload
    //     $decodedHeader = $decodeBase64Url($header);
    //     $decodedPayload = $decodeBase64Url($payload);

    //     // Parse JSON into associative arrays
    //     $headerArray = json_decode($decodedHeader, true);
    //     $payloadArray = json_decode($decodedPayload, true);

    //     // Check for JSON decoding errors
    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return ['error' => 'JSON decoding error: ' . json_last_error_msg()];
    //     }

    //     // Return decoded parts (signature is returned as-is since it’s not typically decoded)
    //     return [
    //         'header' => $headerArray,
    //         'payload' => $payloadArray,
    //         'signature' => $signature
    //     ];
    // }

    function decodeJwt(string $jwt, string $secret = null): array|string
    {
        // Split the JWT into its three parts
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return 'Invalid JWT format: Must contain header, payload, and signature';
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        // Decode header and payload (base64url to binary, then JSON to array)
        try {
            $headerJson = $this->base64UrlDecode($headerB64);
            $payloadJson = $this->base64UrlDecode($payloadB64);

            $header = json_decode($headerJson, true, 512, JSON_THROW_ON_ERROR);
            $payload = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return 'Failed to decode JWT: ' . $e->getMessage();
        } catch (Exception $e) {
            return 'Invalid base64 encoding: ' . $e->getMessage();
        }

        // If no secret provided, return payload without signature verification
        if ($secret === null) {
            return $payload;
        }

        // Verify the signature
        $expectedSignature = $this->base64UrlDecode($signatureB64);
        $dataToSign = "$headerB64.$payloadB64";
        $algorithm = $header['alg'] ?? 'HS256'; // Default to HS256 if not specified

        if ($algorithm !== 'HS256') {
            return 'Unsupported algorithm: Only HS256 is supported in this example';
        }

        $computedSignature = hash_hmac('sha256', $dataToSign, $secret, true);

        // Compare signatures securely
        if (!hash_equals($expectedSignature, $computedSignature)) {
            return 'Invalid JWT: Signature verification failed';
        }

        // Check expiration (optional)
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return 'JWT has expired';
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
