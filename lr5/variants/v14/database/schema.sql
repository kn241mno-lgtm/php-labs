CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) DEFAULT '',
    city VARCHAR(50) DEFAULT '',
    gender VARCHAR(10) DEFAULT '',
    about TEXT DEFAULT '',
    role VARCHAR(20) DEFAULT 'user', -- user or admin
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Anime table
CREATE TABLE IF NOT EXISTS anime (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    title_ua VARCHAR(255) DEFAULT '',
    year INTEGER DEFAULT NULL,
    type VARCHAR(50) DEFAULT '',
    status VARCHAR(50) DEFAULT '',
    episodes INTEGER DEFAULT 0,
    description TEXT DEFAULT '',
    cover_url VARCHAR(255) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Manga table
CREATE TABLE IF NOT EXISTS manga (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    title_ua VARCHAR(255) DEFAULT '',
    year INTEGER DEFAULT NULL,
    type VARCHAR(50) DEFAULT '',
    status VARCHAR(50) DEFAULT '',
    chapters INTEGER DEFAULT 0,
    description TEXT DEFAULT '',
    cover_url VARCHAR(255) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Comments table (for anime/manga)
CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    item_type VARCHAR(10) NOT NULL, -- 'anime' or 'manga'
    item_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
