<?php
/**
 * FarmContext — single source of truth for the active farm.
 *
 * Resolves and validates the farm context for the authenticated user:
 *   - currentFarmId()      : active farm id (session-backed, auto-selects
 *                            when the user owns exactly one farm)
 *   - currentFarmIdOrFail(): same, but redirects with an error if none
 *   - farmsForUser()       : all farms the user owns
 *   - setActiveFarm()      : switches farm AFTER validating ownership
 *   - hasFarmAccess()      : authorization check for any farm_id
 *
 * NOTE (Moo 2.0): ownership is currently `farms.user_id`. When real
 * multi-farm memberships are introduced, replace the ownership queries
 * here with `farm_memberships` lookups — callers will not change.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

class FarmContext
{
    /** @var array|null per-request cache of the user's farms */
    private static ?array $farmsCache = null;

    /**
     * All farms owned by the user (cached per request).
     */
    public static function farmsForUser(int $userId): array
    {
        if (self::$farmsCache !== null) {
            return self::$farmsCache;
        }
        $conn = getDatabase();
        $stmt = $conn->prepare("SELECT id, farm_name, location, registration_number, created_at FROM farms WHERE user_id = ? ORDER BY created_at ASC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $farms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return self::$farmsCache = $farms;
    }

    /**
     * Authorization check: does this user own this farm?
     * NEVER trust a farm_id from GET/POST without calling this.
     */
    public static function hasFarmAccess(int $userId, int $farmId): bool
    {
        foreach (self::farmsForUser($userId) as $farm) {
            if ((int)$farm['id'] === $farmId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Resolve the active farm id for the current user.
     *
     *  - Honors a farm-switch POST (farm_id + switch_farm action) after
     *    validating ownership.
     *  - Honors $_SESSION['active_farm_id'] if still valid.
     *  - Auto-selects when the user owns exactly one farm.
     *  - Returns null when the user has no farm, or owns several but
     *    has not chosen one (never silently picks the first).
     */
    public static function currentFarmId(): ?int
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            return null;
        }

        // Handle an explicit farm switch request
        if (($_SERVER['REQUEST_METHOD'] === 'POST')
            && isset($_POST['switch_farm'], $_POST['farm_id'])) {
            self::setActiveFarm((int)$_POST['farm_id'], $userId);
        }

        $farms = self::farmsForUser($userId);

        if (count($farms) === 1) {
            // Single farm: always the active one
            $_SESSION['active_farm_id'] = (int)$farms[0]['id'];
            return (int)$farms[0]['id'];
        }

        // Multiple farms (Moo 2.0 scenario): require an explicit, valid choice
        $active = (int)($_SESSION['active_farm_id'] ?? 0);
        if ($active > 0 && self::hasFarmAccess($userId, $active)) {
            return $active;
        }
        unset($_SESSION['active_farm_id']);
        return null;
    }

    /**
     * Like currentFarmId() but redirects with a flash error when no valid
     * farm context exists (no farm, or multi-farm user hasn't selected one).
     */
    public static function currentFarmIdOrFail(string $redirectTo = '/r2t6y9u3531ae7c877d967f298ee2d9278ceb68dd73a31'): int
    {
        $farmId = self::currentFarmId();
        if ($farmId === null) {
            $_SESSION['farm_context_error'] = 'Please select a farm to continue.';
            header('Location: ' . $redirectTo);
            exit();
        }
        return $farmId;
    }

/**
     * Full farm row (id, farm_name, location) for the active farm.
     * Returns null when there is no valid active farm. Does NOT die.
     */
    public static function currentFarm(): ?array
    {
        $farmId = self::currentFarmId();
        if ($farmId === null) {
            return null;
        }
        foreach (self::farmsForUser((int)$_SESSION['user_id']) as $farm) {
            if ((int)$farm['id'] === $farmId) {
                return $farm;
            }
        }
        $conn = getDatabase();
        $stmt = $conn->prepare("SELECT id, farm_name, location, registration_number, created_at FROM farms WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $farmId);
        $stmt->execute();
        $farm = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $farm ?: null;
    }
    /**
     * Switch the active farm. Returns false when the user does not own
     * the requested farm — the switch is refused, never applied.
     */
    public static function setActiveFarm(int $farmId, ?int $userId = null): bool
    {
        $userId = $userId ?? (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0 || $farmId <= 0) {
            return false;
        }
        if (!self::hasFarmAccess($userId, $farmId)) {
            return false; // unauthorized: do NOT switch
        }
        $_SESSION['active_farm_id'] = $farmId;
        return true;
    }
}