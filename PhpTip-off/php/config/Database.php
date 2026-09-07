<?php
class Database {
    private static $host = "localhost";
    private static $db_name = "algoritmos benja avila";
    private static $username = "root";
    private static $password = "";
    public static $conn;

    public static function getConnection() {
        self::$conn = null;
        try {
            self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4", self::$username, self::$password);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return self::$conn;
    }
}
?>