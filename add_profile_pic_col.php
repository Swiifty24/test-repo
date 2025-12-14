<?php
require_once __DIR__ . '/App/config/Database.php';

class Migration extends Database {
    public function up() {
        $sql = "ALTER TABLE members ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER email";
        try {
            $this->connect()->exec($sql);
            echo "Successfully added profile_picture column.";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

$migration = new Migration();
$migration->up();
?>
