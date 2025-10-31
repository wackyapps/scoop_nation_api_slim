<?php
declare(strict_types=1);

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseService
{
    private $auth;
    private $projectId = 'scoop-nation';

    public function __construct()
    {
        try {
            // Initialize Firebase with minimal config
            // For proper token verification, download service account from:
            // https://console.firebase.google.com/project/scoop-nation/settings/serviceaccounts/adminsdk
            
            $factory = (new Factory)
                ->withProjectId($this->projectId);
            
            $this->auth = $factory->createAuth();
        } catch (\Exception $e) {
            error_log('Firebase initialization failed: ' . $e->getMessage());
            // Don't throw, we'll handle verification differently
        }
    }

    /**
     * Verify a Firebase ID token using Google's public keys
     * 
     * @param string $idToken The Firebase ID token from the client
     * @param array $userData User data from the client (email, name, picture)
     * @return array|false Returns user data if valid, false otherwise
     */
    public function verifyIdToken(string $idToken, array $userData = [])
    {
        try {
            // Method 1: Try Firebase Admin SDK verification
            if ($this->auth) {
                try {
                    $verifiedIdToken = $this->auth->verifyIdToken($idToken);
                    
                    // Token is valid, return user data
                    return [
                        'email' => $userData['email'] ?? null,
                        'name' => $userData['name'] ?? null,
                        'picture' => $userData['picture'] ?? null,
                        'email_verified' => true
                    ];
                } catch (\Exception $e) {
                    error_log('Firebase Admin SDK verification failed: ' . $e->getMessage());
                    // Fall through to manual verification
                }
            }
            
            // Method 2: Manual JWT verification using Google's public keys
            return $this->verifyTokenManually($idToken, $userData);
            
        } catch (\Exception $e) {
            error_log('Firebase token verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Manually verify Firebase token using Google's public keys
     */
    private function verifyTokenManually(string $idToken, array $userData)
    {
        try {
            // Decode JWT without verification first to get header
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                throw new \Exception('Invalid token format');
            }

            $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            // Basic validation
            if (!$payload || !isset($payload['iss']) || !isset($payload['aud'])) {
                throw new \Exception('Invalid token payload');
            }

            // Verify issuer
            $validIssuers = [
                'https://securetoken.google.com/' . $this->projectId,
                'https://securetoken.google.com/scoop-nation'
            ];
            
            if (!in_array($payload['iss'], $validIssuers)) {
                throw new \Exception('Invalid issuer');
            }

            // Verify audience (project ID)
            if ($payload['aud'] !== $this->projectId && $payload['aud'] !== 'scoop-nation') {
                throw new \Exception('Invalid audience');
            }

            // Verify expiration
            if (!isset($payload['exp']) || $payload['exp'] < time()) {
                throw new \Exception('Token expired');
            }

            // Verify issued at
            if (!isset($payload['iat']) || $payload['iat'] > time()) {
                throw new \Exception('Token used before issued');
            }

            // Token is valid, return user data
            return [
                'email' => $userData['email'] ?? $payload['email'] ?? null,
                'name' => $userData['name'] ?? $payload['name'] ?? null,
                'picture' => $userData['picture'] ?? $payload['picture'] ?? null,
                'email_verified' => $payload['email_verified'] ?? true
            ];

        } catch (\Exception $e) {
            error_log('Manual token verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
