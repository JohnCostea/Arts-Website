<?php
/**
 * Server-Side Validation Helper Class
 * Provides comprehensive input validation and sanitization
 */

class Validator {
    private $errors = [];
    private $data = [];
    
    /**
     * Constructor
     * @param array $data - Data to validate (typically $_POST)
     */
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Validate a field with multiple rules
     * @param string $field - Field name
     * @param string $label - Human-readable label
     * @param string|array $rules - Validation rules (pipe-separated or array)
     * @return self
     */
    public function validate($field, $label, $rules) {
        // Convert string rules to array
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        
        $value = $this->data[$field] ?? '';
        
        foreach ($rules as $rule) {
            // Parse rule and parameters
            $params = [];
            if (strpos($rule, ':') !== false) {
                list($rule, $paramString) = explode(':', $rule, 2);
                $params = explode(',', $paramString);
            }
            
            // Check if validation method exists
            $method = 'validate' . ucfirst($rule);
            if (method_exists($this, $method)) {
                $result = $this->$method($value, $params, $label);
                
                // If validation fails, add error and stop checking this field
                if ($result !== true) {
                    $this->errors[$field] = $result;
                    break;
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Check if validation passed
     * @return bool
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * Check if validation failed
     * @return bool
     */
    public function fails() {
        return !$this->passes();
    }
    
    /**
     * Get all validation errors
     * @return array
     */
    public function errors() {
        return $this->errors;
    }
    
    /**
     * Get first error message
     * @return string|null
     */
    public function firstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Get validated and sanitized data
     * @return array
     */
    public function validated() {
        $validated = [];
        foreach ($this->data as $key => $value) {
            if (!isset($this->errors[$key])) {
                $validated[$key] = $this->sanitize($value);
            }
        }
        return $validated;
    }
    
    // ==================== VALIDATION RULES ====================
    
    /**
     * Required field validation
     */
    protected function validateRequired($value, $params, $label) {
        if (empty(trim($value))) {
            return "$label is required";
        }
        return true;
    }
    
    /**
     * Email validation
     */
    protected function validateEmail($value, $params, $label) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "$label must be a valid email address";
        }
        return true;
    }
    
    /**
     * Minimum length validation
     */
    protected function validateMin($value, $params, $label) {
        $min = $params[0] ?? 0;
        if (!empty($value) && strlen($value) < $min) {
            return "$label must be at least $min characters";
        }
        return true;
    }
    
    /**
     * Maximum length validation
     */
    protected function validateMax($value, $params, $label) {
        $max = $params[0] ?? 255;
        if (!empty($value) && strlen($value) > $max) {
            return "$label must not exceed $max characters";
        }
        return true;
    }
    
    /**
     * Password strength validation
     */
    protected function validatePassword($value, $params, $label) {
        if (empty($value)) {
            return true; // Let required rule handle empty values
        }
        
        if (strlen($value) < 8) {
            return "$label must be at least 8 characters";
        }
        
        if (!preg_match('/[A-Z]/', $value)) {
            return "$label must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[a-z]/', $value)) {
            return "$label must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[0-9]/', $value)) {
            return "$label must contain at least one number";
        }
        
        return true;
    }
    
    /**
     * Alphabetic characters only
     */
    protected function validateAlpha($value, $params, $label) {
        if (!empty($value) && !preg_match('/^[a-zA-Z\s]+$/', $value)) {
            return "$label must contain only letters";
        }
        return true;
    }
    
    /**
     * Alphanumeric characters only
     */
    protected function validateAlphanumeric($value, $params, $label) {
        if (!empty($value) && !preg_match('/^[a-zA-Z0-9\s]+$/', $value)) {
            return "$label must contain only letters and numbers";
        }
        return true;
    }
    
    /**
     * Numeric characters only
     */
    protected function validateNumeric($value, $params, $label) {
        if (!empty($value) && !is_numeric($value)) {
            return "$label must be a number";
        }
        return true;
    }
    
    /**
     * Integer validation
     */
    protected function validateInteger($value, $params, $label) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
            return "$label must be an integer";
        }
        return true;
    }
    
    /**
     * Postal code validation (supports multiple formats)
     */
    protected function validatePostalcode($value, $params, $label) {
        if (!empty($value) && !preg_match('/^[A-Z0-9\s-]{3,10}$/i', $value)) {
            return "$label must be a valid postal code";
        }
        return true;
    }
    
    /**
     * URL validation
     */
    protected function validateUrl($value, $params, $label) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            return "$label must be a valid URL";
        }
        return true;
    }
    
    /**
     * Match another field (for password confirmation)
     */
    protected function validateMatch($value, $params, $label) {
        $matchField = $params[0] ?? '';
        $matchValue = $this->data[$matchField] ?? '';
        
        if ($value !== $matchValue) {
            return "$label must match " . ucfirst(str_replace('_', ' ', $matchField));
        }
        return true;
    }
    
    /**
     * In array validation (for select dropdowns)
     */
    protected function validateIn($value, $params, $label) {
        if (!empty($value) && !in_array($value, $params)) {
            return "$label must be one of: " . implode(', ', $params);
        }
        return true;
    }
    
    /**
     * Unique value in database
     * @param string $value - Value to check
     * @param array $params - [table, column, excludeId (optional)]
     * @param string $label - Field label
     */
    protected function validateUnique($value, $params, $label) {
        if (empty($value)) {
            return true;
        }
        
        global $conn; // Requires database connection
        
        if (!isset($conn)) {
            return "$label validation failed";
        }
        
        $table = $params[0] ?? '';
        $column = $params[1] ?? '';
        $excludeId = $params[2] ?? null;
        
        if (empty($table) || empty($column)) {
            return "$label validation failed";
        }
        
        // Build query
        if ($excludeId) {
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table WHERE $column = ? AND id != ?");
            $stmt->bind_param("si", $value, $excludeId);
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table WHERE $column = ?");
            $stmt->bind_param("s", $value);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row['count'] > 0) {
            return "$label is already taken";
        }
        
        return true;
    }
    
    // ==================== SANITIZATION ====================
    
    /**
     * Sanitize a single value
     * @param mixed $value
     * @return mixed
     */
    public function sanitize($value) {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        
        // Trim whitespace
        $value = trim($value);
        
        // Remove null bytes
        $value = str_replace(chr(0), '', $value);
        
        // Convert special characters to HTML entities
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        return $value;
    }
    
    /**
     * Sanitize email
     */
    public function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitize integer
     */
    public function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitize float
     */
    public function sanitizeFloat($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Sanitize URL
     */
    public function sanitizeUrl($url) {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }
    
    /**
     * Strip HTML tags
     */
    public function stripTags($value, $allowedTags = '') {
        return strip_tags($value, $allowedTags);
    }
}

/**
 * Helper function to create validator instance
 */
function validator($data = []) {
    return new Validator($data);
}
?>