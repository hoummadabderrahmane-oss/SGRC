<?php
namespace App\Core;

class App {
    private static $instance = null;
        private $container = [];
            
                public static function getInstance() {
                        if (self::$instance === null) {
                                    self::$instance = new self();
                                            }
                                                    return self::$instance;
                                                        }
                                                            
                                                                public function bind($key, $value) {
                                                                        $this->container[$key] = $value;
                                                                            }
                                                                                
                                                                                    public function get($key) {
                                                                                            return $this->container[$key] ?? null;
                                                                                                }
                                                                                                    
                                                                                                        public function run() {
                                                                                                                $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                                                                                                                        $uri = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $uri);
                                                                                                                                $uri = trim($uri, '/');
                                                                                                                                        
                                                                                                                                                if (empty($uri)) {
                                                                                                                                                            $uri = 'dashboard';
                                                                                                                                                                    }
                                                                                                                                                                            
                                                                                                                                                                                    $parts = explode('/', $uri);
                                                                                                                                                                                            $module = $parts[0] ?? 'dashboard';
                                                                                                                                                                                                    $action = $parts[1] ?? 'index';
                                                                                                                                                                                                            
                                                                                                                                                                                                                    if (!isset($_SESSION['user_id']) && !in_array($module, ['auth', 'login'])) {
                                                                                                                                                                                                                                header('Location: /modules/auth/login.php');
                                                                                                                                                                                                                                            exit;
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                            
                                                                                                                                                                                                                                                                    $file = __DIR__ . "/../../modules/{$module}/{$action}.php";
                                                                                                                                                                                                                                                                            if (file_exists($file)) {
                                                                                                                                                                                                                                                                                        require_once $file;
                                                                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                                                                            http_response_code(404);
                                                                                                                                                                                                                                                                                                                        echo "Page not found";
                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                    