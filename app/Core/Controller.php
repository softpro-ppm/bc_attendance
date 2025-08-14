<?php

namespace App\Core;

class Controller
{
    protected $db;
    protected $config;
    protected $user;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/config.php';
        $this->db = DB::getInstance($this->config);
        $this->user = $this->getCurrentUser();
    }

    protected function render($view, $data = [])
    {
        // Extract data to variables for use in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View not found: {$view}");
        }
        
        // Get the buffered content
        $content = ob_get_clean();
        
        return $content;
    }

    protected function renderLayout($view, $data = [])
    {
        // Extract data to variables for use in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View not found: {$view}");
        }
        
        // Get the buffered content and output it directly
        $content = ob_get_clean();
        echo $content;
    }

    protected function renderPartial($view, $data = [])
    {
        // Extract data to variables for use in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the partial view file
        $viewPath = __DIR__ . '/../../views/partials/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("Partial view not found: {$view}");
        }
        
        // Get the buffered content
        $content = ob_get_clean();
        
        return $content;
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    protected function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function requireAuth()
    {
        if (!$this->user) {
            $this->redirect('/login');
        }
    }

    protected function requireGuest()
    {
        if ($this->user) {
            $this->redirect('/dashboard');
        }
    }

    protected function getCurrentUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $sql = "SELECT id, username, email, full_name, last_login FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$_SESSION['user_id']]);
    }

    protected function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    protected function isPost()
    {
        return $this->getRequestMethod() === 'POST';
    }

    protected function isGet()
    {
        return $this->getRequestMethod() === 'GET';
    }

    protected function isPut()
    {
        return $this->getRequestMethod() === 'PUT';
    }

    protected function isDelete()
    {
        return $this->getRequestMethod() === 'DELETE';
    }

    protected function getInput($key = null, $default = null)
    {
        $input = array_merge($_GET, $_POST);
        
        if ($key === null) {
            return $input;
        }
        
        return $input[$key] ?? $default;
    }

    protected function getJsonInput()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = ucfirst($field) . ' is required';
                continue;
            }
            
            if (!empty($value)) {
                if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email format';
                }
                
                if (strpos($rule, 'min:') !== false) {
                    preg_match('/min:(\d+)/', $rule, $matches);
                    $min = (int) $matches[1];
                    if (strlen($value) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least {$min} characters";
                    }
                }
                
                if (strpos($rule, 'max:') !== false) {
                    preg_match('/max:(\d+)/', $rule, $matches);
                    $max = (int) $matches[1];
                    if (strlen($value) > $max) {
                        $errors[$field] = ucfirst($field) . " must not exceed {$max} characters";
                    }
                }
            }
        }
        
        return $errors;
    }

    protected function setFlash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash($key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    protected function hasFlash($key)
    {
        return isset($_SESSION['flash'][$key]);
    }

    protected function logAudit($action, $entity, $entityId, $details = null)
    {
        if (!$this->user) {
            return;
        }

        $sql = "INSERT INTO audit_log (user_id, action, entity, entity_id, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $this->user['id'],
            $action,
            $entity,
            $entityId,
            $details ? json_encode($details) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
