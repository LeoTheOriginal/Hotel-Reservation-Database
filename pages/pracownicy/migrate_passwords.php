<?php
/**
 * Password Migration Script
 *
 * This script is designed to be run after login to check and update 
 * user passwords that are still stored using MD5 to the more secure
 * password_hash format.
 */

/**
 * Updates a password hash if it's in the old MD5 format
 * 
 * @param int $user_id The user ID
 * @param string $password The plain text password (only used if current hash is MD5)
 * @param string $current_hash The current hash from the database
 * @return boolean True if password was migrated, false if no migration needed
 */
function migratePasswordIfNeeded($user_id, $password, $current_hash) {
    include_once __DIR__ . '/../../config.php';
    
    // Check if the current hash is MD5 (MD5 hashes are always 32 characters in hexadecimal)
    if (strlen($current_hash) == 32 && ctype_xdigit($current_hash)) {
        try {
            // Connect to the database
            $pdo = new PDO($dsn, $db_user, $db_password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Create a new secure hash
            $new_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
            
            // Update the password in the database
            $stmt = $pdo->prepare("UPDATE rezerwacje_hotelowe.pracownik SET hasło = :new_hash WHERE id = :id");
            $stmt->bindParam(':new_hash', $new_hash, PDO::PARAM_STR);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Log the password upgrade
            $log_stmt = $pdo->prepare("
                INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
                VALUES ('Aktualizacja zabezpieczeń hasła', :details)
            ");
            $details = json_encode([
                'pracownik_id' => $user_id,
                'akcja' => 'Migracja hasła z MD5 do bezpiecznego formatu'
            ]);
            $log_stmt->bindParam(':details', $details, PDO::PARAM_STR);
            $log_stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            // Log error but don't expose it to the user
            error_log('Error migrating password: ' . $e->getMessage());
            return false;
        }
    }
    
    // No migration needed - password is already in secure format
    return false;
}