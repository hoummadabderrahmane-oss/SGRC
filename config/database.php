<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sgrc_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $instance = null;
        private $pdo;
            
                private function __construct() {
                        try {
                                    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                                                $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
                                                            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                                        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                                                                                } catch (PDOException $e) {
                                                                                            die("Connection failed: " . $e->getMessage());
                                                                                                    }
                                                                                                        }
                                                                                                            
                                                                                                                public static function getInstance() {
                                                                                                                        if (self::$instance === null) {
                                                                                                                                    self::$instance = new self();
                                                                                                                                            }
                                                                                                                                                    return self::$instance;
                                                                                                                                                        }
                                                                                                                                                            
                                                                                                                                                                public function getConnection() {
                                                                                                                                                                        return $this->pdo;
                                                                                                                                                                            }
                                                                                                                                                                                
                                                                                                                                                                                    public function query($sql, $params = []) {
                                                                                                                                                                                            $stmt = $this->pdo->prepare($sql);
                                                                                                                                                                                                    $stmt->execute($params);
                                                                                                                                                                                                            return $stmt;
                                                                                                                                                                                                                }
                                                                                                                                                                                                                }
                                                                                                                                                                                                                