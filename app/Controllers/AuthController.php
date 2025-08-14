<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireGuest();
    }

    public function showLogin()
    {
        $errors = [];
        $oldInput = [];
        
        // Check for flash messages
        if ($this->hasFlash('errors')) {
            $errors = $this->getFlash('errors');
        }
        
        if ($this->hasFlash('old_input')) {
            $oldInput = $this->getFlash('old_input');
        }
        
        // Render login page with layout
        $this->renderLayout('layout', [
            'content' => $this->render('auth/login', [
                'errors' => $errors,
                'oldInput' => $oldInput
            ]),
            'currentPage' => 'login',
            'pageTitle' => 'Login - BC Attendance System',
            'user' => null
        ]);
    }

    public function login()
    {
        // Validate CSRF token
        CSRF::validateRequest();
        
        $username = $this->getInput('username');
        $password = $this->getInput('password');
        
        // Validation
        $errors = $this->validate([
            'username' => $username,
            'password' => $password
        ], [
            'username' => 'required',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->setFlash('errors', $errors);
            $this->setFlash('old_input', ['username' => $username]);
            $this->redirect('/login');
        }
        
        // Check for login attempts
        if ($this->isLoginBlocked($username)) {
            $this->setFlash('errors', ['username' => 'Too many login attempts. Please try again later.']);
            $this->setFlash('old_input', ['username' => $username]);
            $this->redirect('/login');
        }
        
        // Attempt authentication
        $user = $this->authenticateUser($username, $password);
        
        if ($user) {
            // Success - create session
            $this->createUserSession($user);
            
            // Log successful login
            $this->logAudit('login', 'user', $user['id'], [
                'username' => $username,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);
            
            // Clear failed login attempts
            $this->clearLoginAttempts($username);
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            $this->redirect('/dashboard');
        } else {
            // Failed login
            $this->recordLoginAttempt($username);
            
            $this->setFlash('errors', ['username' => 'Invalid username or password.']);
            $this->setFlash('old_input', ['username' => $username]);
            $this->redirect('/login');
        }
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            $this->logAudit('logout', 'user', $_SESSION['user_id']);
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect to login
        $this->redirect('/login');
    }

    private function authenticateUser($username, $password)
    {
        $sql = "SELECT id, username, password_hash, email, full_name FROM users WHERE username = ? AND status = 'active'";
        $user = $this->db->fetch($sql, [$username]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }

    private function isLoginBlocked($username)
    {
        $config = $this->config['security'];
        $maxAttempts = $config['login_max_attempts'];
        $lockoutTime = $config['login_lockout_time'];
        
        // Count recent failed attempts - using SQLite compatible syntax
        $sql = "SELECT COUNT(*) FROM login_attempts 
                WHERE username = ? AND attempted_at > datetime('now', '-' || ? || ' seconds')";
        $attempts = $this->db->fetchColumn($sql, [$username, $lockoutTime]);
        
        return $attempts >= $maxAttempts;
    }

    private function recordLoginAttempt($username)
    {
        $sql = "INSERT INTO login_attempts (username, ip_address) VALUES (?, ?)";
        $this->db->execute($sql, [
            $username,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }

    private function clearLoginAttempts($username)
    {
        $sql = "DELETE FROM login_attempts WHERE username = ?";
        $this->db->execute($sql, [$username]);
    }

    private function createUserSession($user)
    {
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_time'] = time();
        
        // Set session cookie parameters
        $config = $this->config['session'];
        session_set_cookie_params([
            'lifetime' => $config['lifetime'],
            'path' => $config['path'],
            'domain' => $config['domain'],
            'secure' => $config['secure'],
            'httponly' => $config['httponly'],
            'samesite' => $config['samesite']
        ]);
    }

    private function updateLastLogin($userId)
    {
        $sql = "UPDATE users SET last_login = datetime('now') WHERE id = ?";
        $this->db->execute($sql, [$userId]);
    }
}
