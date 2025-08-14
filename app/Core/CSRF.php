<?php

namespace App\Core;

class CSRF
{
    private static $tokenName = 'csrf_token';

    public static function generateToken()
    {
        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$token] = time();

        // Clean old tokens (older than 1 hour)
        self::cleanOldTokens();

        return $token;
    }

    public static function getToken()
    {
        return self::generateToken();
    }

    public static function verifyToken($token)
    {
        if (!isset($_SESSION['csrf_tokens'][$token])) {
            return false;
        }

        // Check if token is not expired (1 hour)
        if (time() - $_SESSION['csrf_tokens'][$token] > 3600) {
            unset($_SESSION['csrf_tokens'][$token]);
            return false;
        }

        // Remove used token
        unset($_SESSION['csrf_tokens'][$token]);
        return true;
    }

    public static function getTokenField()
    {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token) . '">';
    }

    public static function getTokenName()
    {
        return self::$tokenName;
    }

    public static function cleanOldTokens()
    {
        if (!isset($_SESSION['csrf_tokens'])) {
            return;
        }

        $currentTime = time();
        foreach ($_SESSION['csrf_tokens'] as $token => $timestamp) {
            if ($currentTime - $timestamp > 3600) {
                unset($_SESSION['csrf_tokens'][$token]);
            }
        }
    }

    public static function validateRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        $token = $_POST[self::$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!$token || !self::verifyToken($token)) {
            http_response_code(403);
            echo 'CSRF token validation failed';
            exit;
        }

        return true;
    }
}
