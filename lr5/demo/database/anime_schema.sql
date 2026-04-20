-- Anime/Manga/News schema scaffold for SQLite
PRAGMA foreign_keys = ON;

-- Genres
CREATE TABLE IF NOT EXISTS genres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Studios
CREATE TABLE IF NOT EXISTS studios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Authors (manga authors, etc.)
CREATE TABLE IF NOT EXISTS authors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Anime
CREATE TABLE IF NOT EXISTS anime (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    original_title TEXT DEFAULT '',
    year INTEGER DEFAULT 0,
    type TEXT DEFAULT '',
    episodes INTEGER DEFAULT 0,
    status TEXT DEFAULT '',
    rating REAL DEFAULT 0,
    description TEXT DEFAULT '',
    poster TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Manga
CREATE TABLE IF NOT EXISTS manga (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    year INTEGER DEFAULT 0,
    volumes INTEGER DEFAULT 0,
    chapters INTEGER DEFAULT 0,
    rating REAL DEFAULT 0,
    description TEXT DEFAULT '',
    cover TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Anime-Genre many-to-many
CREATE TABLE IF NOT EXISTS anime_genre (
    anime_id INTEGER NOT NULL,
    genre_id INTEGER NOT NULL,
    PRIMARY KEY (anime_id, genre_id),
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

-- Anime-Studios
CREATE TABLE IF NOT EXISTS anime_studio (
    anime_id INTEGER NOT NULL,
    studio_id INTEGER NOT NULL,
    PRIMARY KEY (anime_id, studio_id),
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (studio_id) REFERENCES studios(id) ON DELETE CASCADE
);

-- Manga-Authors
CREATE TABLE IF NOT EXISTS manga_author (
    manga_id INTEGER NOT NULL,
    author_id INTEGER NOT NULL,
    PRIMARY KEY (manga_id, author_id),
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
);

-- Characters
CREATE TABLE IF NOT EXISTS characters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    anime_id INTEGER,
    role TEXT DEFAULT '',
    image TEXT DEFAULT '',
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE SET NULL
);

-- News
CREATE TABLE IF NOT EXISTS news (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT DEFAULT '',
    excerpt TEXT DEFAULT '',
    content TEXT DEFAULT '',
    category TEXT DEFAULT '',
    author TEXT DEFAULT '',
    image TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Comments (can be used for anime/news)
CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER DEFAULT NULL,
    user_id INTEGER DEFAULT NULL,
    target_type TEXT DEFAULT '', -- 'anime' or 'news' or 'manga'
    target_id INTEGER DEFAULT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Simple tags table
CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Ratings (user ratings for anime/manga)
CREATE TABLE IF NOT EXISTS ratings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER DEFAULT NULL,
    target_type TEXT DEFAULT '',
    target_id INTEGER DEFAULT NULL,
    rating INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
