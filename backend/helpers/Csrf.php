<?php
/**
 * Csrf — centralized CSRF token generation/validation.
 *
 * Tokens are stored in the session. A single token is generated per session
 * and REUSED across requests (never cleared on validation) so that pages with
 * multiple forms (add + delete) all work. All comparisons use hash_equals().
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Csrf
{
    /** Session key used for the token. */
    private const SESSION_KEY = 'csrf_token';

    /**
     * The token to embed in forms. Generates one on first use.
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Validate a submitted token. Returns true when it matches the session
     * token using a constant-time comparison.
     */
    public static function validate(?string $submitted): bool
    {
        $expected = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_string($expected) || $expected === '' || $submitted === null || $submitted === '') {
            return false;
        }
        return hash_equals($expected, $submitted);
    }

    /**
     * The variable used in templates as $form_token (kept for consistency
     * with the existing feeds/milk_sales modules).
     */
    public static function formTokenVar(): string
    {
        return self::token();
    }
}