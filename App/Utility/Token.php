<?php

declare(strict_types=1);

namespace Kikopolis\App\Utility;

use Kikopolis\App\Helpers\Str;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Token utility class. Generate and validate basic tokens and csrf tokens.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Token
{
    /**
     * Regular token. Used for activation and misc low level auth functions
     * @var string
     */
    private $token = '';

    /**
     * CSRF token, hashed and more secure token.
     * @var string
     */
    private $csrf_token = '';

    private $x_csrf_token = '';

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Token constructor.
     * @param null $token_value Default $token_value is null, then we create a new token. If an actual value is passed in
     * then that is set as value and can be used for verifying said token.
     * @throws \Exception
     */
    public function __construct($token_value = null)
    {
        if ($token_value !== null) {
            $this->token = $token_value;
        } else {
            $this->token = Str::random(16);
        }
    }

    /**
     * Create a csrf token from a random string and hash it.
     * @throws \Exception
     * @return string
     */
    public function getCsrfToken(): string
    {
        $this->createCsrfToken();
        $this->csrf_token = $this->getCsrfHash();

        return $this->csrf_token;
    }

    /**
     * Return a hashed token.
     * @return string
     */
    public function getTokenHash(): string
    {
        return Hash::getHash($this->token);
    }

    /**
     * Compare token to its verification, saved in where ever, DB, session etc.
     * @param string $token
     * @param string $verification
     * @return bool
     */
    public function tokenIsValid(string $token, string $verification): bool
    {
        return Hash::compare($token, $verification);
    }

    /**
     * Get csrf token hash.
     * @return string
     */
    private function getCsrfHash(): string
    {
        return Hash::getHash($this->csrf_token);
    }

    /**
     * Create and set the CSRF token into session.
     * @throws \Exception
     * @return string
     */
    private function createCsrfToken(): string
    {
        $this->csrf_token = Str::random(16);
        $_SESSION['csrf_token'] = $this->csrf_token;
        $_SESSION['token_confirmation'] = Str::random(16);
        $_SESSION['csrf_token_time'] = time();

        return $this->csrf_token;
    }

    /**
     * Destroy the csrf token
     * @return boolean
     */
    private function destroyCSRFToken()
    {
        if (isset($_SESSION['csrf_token']) || isset($_SESSION['token_confirmation']) || isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = null;
            $_SESSION['token_confirmation'] = null;
            $_SESSION['csrf_token_time'] = null;

            return true;
        }

        return false;
    }

    /**
     * Verify that the csrf token is valid.
     * @throws \Exception
     * @return bool
     */
    public function csrfTokenIsValid(): bool
    {
        if (!Validate::hasValue($_POST['csrf_token'])) {
            throw new \Exception('Form Token not present. Stop the press and call the office!');
        } else {
            if (!$this->csrfTokenIsRecent()) {
                throw new \Exception('Form token has expired. Please try again.');
            }
            if (!Hash::compare($_SESSION['csrf_token'], $this->token)) {
                throw new \Exception('CSRF Tokens from form are mismatched. Stopping everything and running away scared!!!');
            } else {
                return true;
            }
        }
    }

    /**
     * Verify the csrf token is recent.
     * @param float|int $max_elapsed
     * @return bool
     */
    private function csrfTokenIsRecent(int $max_elapsed = 3600): bool
    {
        if (!Validate::hasValue($_SESSION['csrf_token'])) {
            $this->destroyCSRFToken();

            return false;
        } else {

            return ($_SESSION['csrf_token_time'] + $max_elapsed) >= time();
        }
    }

    /**
     * Get an XCSRF token for the html tag. Saved in an array of tokens in session.
     * @return string
     * @throws \Exception
     */
    public function xCsrfToken()
    {
        // Make sure there are no more than 50 current active tokens
        if ($_SESSION['x_csrf_token_count'] > 49) {
            for ($i = $_SESSION['x_csrf_token_count']; $i > 49; $i--) {
                array_shift($_SESSION['x_csrf_token']);
            }
        }
        $this->x_csrf_token = Str::random(16);
        $_SESSION['x_csrf_token_count'] = count($_SESSION['x_csrf_token']);
        $_SESSION['x_csrf_token'][$this->x_csrf_token]['token'] = $this->x_csrf_token;
        $_SESSION['x_csrf_token'][$this->x_csrf_token]['token_time'] = time();

        $this->x_csrf_token = Hash::getHash($this->x_csrf_token);

        return $this->x_csrf_token;
    }

    /**
     * Get an X-CSRF token html tag.
     * @return string
     * @throws \Exception
     */
    public static function xCsrfTokenTag()
    {
        $token = new Token();
        $token_value = $token->xCsrfToken();
        return "<meta name=\"x-csrf-token\" content=\"{$token_value}\">";
    }

    /**
     * Verify that the X-csrf token is valid.
     * @param string $token
     * @return bool
     * @throws \Exception
     */
    public function xCsrfTokenIsValid(string $token): bool
    {
        foreach ($_SESSION['x_csrf_token'] as $token) {
            if (\is_string($token)) {
                return true;
            }
        }

        if (!Validate::hasValue($_POST['x_csrf_token'][$token]['token'])) {
            throw new \Exception('Form Token not present. Stop the press and call the office!');
        } else {
            // Hash the token to get a valid name to choose from the array.
            $token = Hash::getHash($token);
            if (!$this->xCsrfTokenIsRecent($token)) {
                throw new \Exception('Form token has expired. Please try again.');
            }
            if (!Hash::compare($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                throw new \Exception('CSRF Tokens from form are mismatched. Stopping everything and running away scared!!!');
            } else {
                echo "<h1>CSRF TOKEN IS VALID</h1>";
                return true;
            }
        }
    }

    /**
     * Verify the x-csrf token is recent.
     * @param string $token
     * @param float|int $max_elapsed
     * @return bool
     */
    private function xCsrfTokenIsRecent(string $token, int $max_elapsed = 3600): bool
    {
        if (!Validate::hasValue($_SESSION['x_csrf_token'][$token]['token_time'])) {
            $this->destroyXCSRFToken($token);

            return false;
        } else {

            return ($_SESSION['x_csrf_token'][$token]['token_time'] + $max_elapsed) >= time();
        }
    }

    /**
     * Destroy an X-csrf token.
     * @param string $token
     * @return bool
     */
    private function destroyXCSRFToken(string $token): bool
    {
        if (isset($_SESSION['x_csrf_token'][$token]['token']) || isset($_SESSION['x_csrf_token'][$token]['token_time'])) {
            $_SESSION['x_csrf_token'][$token]['token'] = null;
            $_SESSION['x_csrf_token'][$token]['token_time'] = null;

            return true;
        }

        return false;
    }
}
