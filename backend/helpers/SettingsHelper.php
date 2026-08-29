<?php
require_once __DIR__ . '/../config/database.php';

class SettingsHelper {
    private $conn;
    private $user_id;

    public function __construct($user_id) {
        $this->conn = getDatabase();
        $this->user_id = $user_id;
    }

    /**
     * Get milk price per litre for the current user
     * @return float
     */
    public function getMilkPrice() {
        $stmt = $this->conn->prepare("SELECT setting_value FROM farm_settings WHERE user_id = ? AND setting_key = 'milk_price_per_litre'");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row) {
            return (float)$row['setting_value'];
        }
        return 70.00; // fallback default
    }

    /**
     * Update milk price per litre
     * @param float $price
     * @return bool
     */
    public function setMilkPrice($price) {
        $stmt = $this->conn->prepare("INSERT INTO farm_settings (user_id, setting_key, setting_value) VALUES (?, 'milk_price_per_litre', ?)
                                      ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("idd", $this->user_id, $price, $price);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>