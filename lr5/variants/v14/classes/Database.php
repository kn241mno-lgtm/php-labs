<?php

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require ROOT_DIR . '/config/database.php';

            try {
                self::$instance = new PDO(
                    $config['dsn'],
                    $config['username'],
                    $config['password']
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Enable foreign keys for SQLite
                if (strpos($config['dsn'], 'sqlite') === 0) {
                    self::$instance->exec('PRAGMA foreign_keys = ON');
                    // If database is empty / not initialized, apply schema.sql to create tables
                    try {
                        $required = ['anime','manga','genre'];
                        $inList = "'" . implode("','", $required) . "'";
                        $check = self::$instance->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ($inList)");
                        $existing = [];
                        if ($check) {
                            foreach ($check->fetchAll() as $r) $existing[] = $r['name'];
                        }
                        $missing = array_diff($required, $existing);
                        if (!empty($missing)) {
                            $schemaFile = ROOT_DIR . '/database/schema.sql';
                            if (file_exists($schemaFile)) {
                                $sql = file_get_contents($schemaFile);
                                try {
                                    self::$instance->exec($sql);
                                } catch (Exception $e) {
                                    // if large exec fails, try splitting by semicolons as fallback
                                    $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
                                    foreach ($stmts as $s) {
                                        if ($s === '') continue;
                                        try { self::$instance->exec($s); } catch (Exception $se) { error_log('DB init stmt failed: ' . $se->getMessage()); }
                                    }
                                }
                            }
                        }

                        // Ensure rating table has expected columns (manga_id, anime_id)
                        try {
                            $cols = [];
                            $res = self::$instance->query("PRAGMA table_info('rating')");
                            if ($res) {
                                foreach ($res->fetchAll() as $r) {
                                    $cols[] = $r['name'];
                                }
                            }
                            if (!in_array('manga_id', $cols)) {
                                try { self::$instance->exec("ALTER TABLE rating ADD COLUMN manga_id INTEGER"); } catch (Exception $e) { error_log('Could not add manga_id: '.$e->getMessage()); }
                            }
                            if (!in_array('anime_id', $cols)) {
                                try { self::$instance->exec("ALTER TABLE rating ADD COLUMN anime_id INTEGER"); } catch (Exception $e) { error_log('Could not add anime_id: '.$e->getMessage()); }
                            }
                        } catch (Exception $e) {
                            error_log('DB rating columns check failed: ' . $e->getMessage());
                        }
                        
                        // Ensure users table has expected profile columns used by controllers/views
                        try {
                            $ucols = [];
                            $ures = self::$instance->query("PRAGMA table_info('users')");
                            if ($ures) {
                                foreach ($ures->fetchAll() as $r) { $ucols[] = $r['name']; }
                            }
                            $needed = ['first_name','last_name','phone','city','gender','about','display_name','bio','password','avatar_url','ui_color','show_email','notify_comments'];
                            foreach ($needed as $col) {
                                if (!in_array($col, $ucols)) {
                                    try {
                                        // default to TEXT for profile fields, leave password TEXT
                                        self::$instance->exec("ALTER TABLE users ADD COLUMN $col TEXT");
                                    } catch (Exception $e) {
                                        error_log('Could not add users column '.$col.': '.$e->getMessage());
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            error_log('DB users columns check failed: ' . $e->getMessage());
                        }
                    
                    } catch (Exception $e) {
                        error_log('DB init check failed: ' . $e->getMessage());
                    }
                }
            } catch (PDOException $e) {
                error_log('DB connection error: ' . $e->getMessage());
                $appDebug = getenv('APP_DEBUG');
                if ($appDebug === '1' || strtolower($appDebug) === 'true') {
                    // show detailed error in development
                    die('DB connection error: ' . htmlspecialchars($e->getMessage()));
                }
                die('Помилка підключення до бази даних.');
            }
        }

        return self::$instance;
    }
}
