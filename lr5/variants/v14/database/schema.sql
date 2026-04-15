-- Main simplified schema adapted from the provided SQL Server script
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login TEXT NOT NULL UNIQUE,
    email TEXT,
    password TEXT,
    display_name TEXT,
    first_name TEXT,
    last_name TEXT,
    birth_date TEXT,
    gender TEXT,
    country TEXT,
    avatar_url TEXT,
    cover_url TEXT,
    bio TEXT,
    role TEXT DEFAULT 'user', -- user, moderator, admin
    status TEXT DEFAULT 'active',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS studio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    name_ua TEXT,
    country TEXT,
    founded INTEGER,
    employees INTEGER,
    description TEXT,
    website TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS genre (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    name_ua TEXT,
    name_en TEXT,
    description TEXT,
    color TEXT DEFAULT '#3b82f6',
    icon TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS anime (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    title_ua TEXT,
    title_en TEXT,
    year INTEGER,
    season TEXT,
    episodes INTEGER DEFAULT 0,
    episode_duration INTEGER DEFAULT 0,
    type TEXT,
    status TEXT,
    source TEXT,
    rating_mpaa TEXT,
    description TEXT,
    studio_id INTEGER,
    cover_url TEXT,
    poster_url TEXT,
    views INTEGER DEFAULT 0,
    favorites INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (studio_id) REFERENCES studio(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS manga (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    title_ua TEXT,
    year INTEGER,
    status TEXT,
    volumes INTEGER DEFAULT 0,
    chapters INTEGER DEFAULT 0,
    type TEXT,
    demographic TEXT,
    description TEXT,
    cover_url TEXT,
    views INTEGER DEFAULT 0,
    favorites INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS character (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    name_ua TEXT,
    full_name TEXT,
    gender TEXT,
    age INTEGER,
    birth_date TEXT,
    height INTEGER,
    weight REAL,
    blood_type TEXT,
    occupation TEXT,
    description TEXT,
    image_url TEXT,
    favorites INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS person (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    name_ua TEXT,
    birth_date TEXT,
    birth_place TEXT,
    gender TEXT,
    biography TEXT,
    image_url TEXT,
    website TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS news (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT,
    summary TEXT,
    category TEXT,
    image_url TEXT,
    author_id INTEGER,
    views INTEGER DEFAULT 0,
    likes INTEGER DEFAULT 0,
    is_published INTEGER DEFAULT 1,
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS release_dates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    anime_id INTEGER,
    episode_number INTEGER,
    release_date DATETIME,
    title TEXT,
    description TEXT,
    is_confirmed INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER,
    user_id INTEGER,
    anime_id INTEGER,
    manga_id INTEGER,
    character_id INTEGER,
    person_id INTEGER,
    news_id INTEGER,
    content TEXT NOT NULL,
    likes INTEGER DEFAULT 0,
    dislikes INTEGER DEFAULT 0,
    is_edited INTEGER DEFAULT 0,
    is_deleted INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS rating (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    anime_id INTEGER,
    manga_id INTEGER,
    user_id INTEGER,
    score REAL,
    is_favorite INTEGER DEFAULT 0,
    status TEXT,
    progress INTEGER DEFAULT 0,
    review TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Link tables
CREATE TABLE IF NOT EXISTS anime_genre (
    anime_id INTEGER,
    genre_id INTEGER,
    PRIMARY KEY (anime_id, genre_id),
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genre(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS manga_genre (
    manga_id INTEGER,
    genre_id INTEGER,
    PRIMARY KEY (manga_id, genre_id),
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genre(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS anime_character (
    anime_id INTEGER,
    character_id INTEGER,
    role TEXT,
    is_main INTEGER DEFAULT 0,
    "order" INTEGER DEFAULT 0,
    voice_actor_id INTEGER,
    description TEXT,
    PRIMARY KEY (anime_id, character_id)
);

CREATE TABLE IF NOT EXISTS manga_character (
    manga_id INTEGER,
    character_id INTEGER,
    role TEXT,
    is_main INTEGER DEFAULT 0,
    "order" INTEGER DEFAULT 0,
    description TEXT,
    PRIMARY KEY (manga_id, character_id)
);

CREATE TABLE IF NOT EXISTS anime_manga (
    anime_id INTEGER,
    manga_id INTEGER,
    relation_type TEXT,
    is_canon INTEGER DEFAULT 1,
    PRIMARY KEY (anime_id, manga_id)
);

CREATE TABLE IF NOT EXISTS anime_person (
    anime_id INTEGER,
    person_id INTEGER,
    role TEXT,
    "order" INTEGER DEFAULT 0,
    PRIMARY KEY (anime_id, person_id, role)
);

CREATE TABLE IF NOT EXISTS manga_author (
    manga_id INTEGER,
    author_id INTEGER,
    role TEXT,
    is_main INTEGER DEFAULT 1,
    "order" INTEGER DEFAULT 0,
    PRIMARY KEY (manga_id, author_id, role)
);

CREATE TABLE IF NOT EXISTS related_anime (
    anime_id1 INTEGER,
    anime_id2 INTEGER,
    relation_type TEXT,
    direction TEXT DEFAULT 'bidirectional',
    PRIMARY KEY (anime_id1, anime_id2, relation_type)
);

-- Seed a subset of reference data (genres, a few studios, some users)
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES
('Action', 'Бойовик', 'Action', 'Аніме з інтенсивними битвами та екшен-сценами', '#ef4444', 'sword'),
('Adventure', 'Пригоди', 'Adventure', 'Подорожі, дослідження нових світів та пошук пригод', '#f59e0b', 'compass'),
('Comedy', 'Комедія', 'Comedy', 'Смішні ситуації, жарти та гумористичні діалоги', '#10b981', 'laugh'),
('Drama', 'Драма', 'Drama', 'Емоційні історії з глибоким психологічним підтекстом', '#8b5cf6', 'drama'),
('Fantasy', 'Фентезі', 'Fantasy', 'Магія, фантастичні істоти та вигадані світи', '#ec4899', 'magic');

INSERT OR IGNORE INTO studio (name, name_ua, country, founded, employees, description, website) VALUES
('MAPPA', 'MAPPA', 'Japan', 2011, 400, 'Відома студія', 'https://mappa.co.jp'),
('Kyoto Animation', 'Kyoto Animation', 'Japan', 1981, 300, 'Студія з високою якістю анімації', 'https://kyotoanimation.co.jp'),
('Bones', 'Bones', 'Japan', 1998, 250, 'Відома за Fullmetal Alchemist', 'https://bones.co.jp');

INSERT OR IGNORE INTO users (login, email, display_name, role) VALUES
('admin', 'admin@example.com', 'Адміністратор', 'admin'),
('animefan', 'fan@example.com', 'Аніме Фан', 'user');

-- Imported full SQL Server schema (original) saved to: Encyclopedia_import.sql
-- The original T-SQL script from the course project is copied into
-- `Encyclopedia_import.sql` in this folder. Some parts (stored procedures,
-- SQL Server-specific functions and DDL) are not directly executable in
-- SQLite; use the imported file as reference or for porting.

-- If you want, I can now convert and merge specific tables / seed data
-- from `Encyclopedia_import.sql` into this `schema.sql` (SQLite-adapted).

-- ==================================================================
-- Additional converted schema + seed data (from Encyclopedia_import.sql)
-- Note: converted to SQLite (removed SQL Server-specific constructs)
-- ==================================================================

CREATE TABLE IF NOT EXISTS author (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    name_ua TEXT,
    name_en TEXT,
    birth_date TEXT,
    birth_place TEXT,
    death_date TEXT,
    gender TEXT,
    biography TEXT,
    image_url TEXT,
    website TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert more genres from import
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES
('Sci-Fi', 'Наукова фантастика', 'Sci-Fi', 'Футуристичні технології, космос та наукові відкриття', '#3b82f6', 'robot'),
('Romance', 'Романтика', 'Romance', 'Історії кохання та романтичні стосунки', '#ff6b6b', 'heart'),
('Slice of Life', 'Повсякденність', 'Slice of Life', 'Звичайне життя, щоденні турботи та побут', '#a3e635', 'home'),
('Mystery', 'Містика', 'Mystery', 'Таємничі події, розслідування та нерозгадані загадки', '#6366f1', 'question'),
('Horror', 'Жахи', 'Horror', 'Страшні історії, що викликають почуття страху та тривоги', '#18181b', 'ghost'),
('Psychological', 'Психологічний', 'Psychological', 'Глибокий аналіз психіки персонажів та їхніх мотивів', '#7c3aed', 'brain'),
('Supernatural', 'Надприродне', 'Supernatural', 'Надприродні сили, духи, демони та паранормальні явища', '#c084fc', 'sparkles'),
('Sports', 'Спорт', 'Sports', 'Спортивні змагання, тренування та командний дух', '#22c55e', 'sports'),
('Music', 'Музика', 'Music', 'Історії про музику, музикантів та музичні гурти', '#f43f5e', 'music'),
('School', 'Школа', 'School', 'Шкільне життя, навчання та стосунки між учнями', '#eab308', 'school'),
('Shounen', 'Шьонен', 'Shounen', 'Для хлопчиків-підлітків: битви, дружба, пригоди', '#f97316', 'target'),
('Seinen', 'Сейнен', 'Seinen', 'Для дорослих чоловіків: серйозні теми, складні сюжети', '#6b7280', 'mature'),
('Isekai', 'Ісекай', 'Isekai', 'Перенесення в інший світ, переродження', '#a855f7', 'portal'),
('Mecha', 'Меха', 'Mecha', 'Гігантські роботи та пілотування', '#64748b', 'robot'),
('Historical', 'Історичний', 'Historical', 'Історичні події та епохи', '#b45309', 'history'),
('Military', 'Військовий', 'Military', 'Військові конфлікти, армія, стратегія', '#475569', 'military'),
('Crime', 'Кримінал', 'Crime', 'Злочинний світ, розслідування, мафія', '#1e293b', 'crime'),
('Thriller', 'Трилер', 'Thriller', 'Напружені історії, що тримають у постійному напруженні', '#292524', 'thriller'),
('Gourmet', 'Кулінарія', 'Gourmet', 'Про їжу, приготування та кулінарне мистецтво', '#fbbf24', 'food'),
('Harem', 'Гарем', 'Harem', 'Один головний герой та багато героїнь', '#f87171', 'harem'),
('Martial Arts', 'Бойові мистецтва', 'Martial Arts', 'Різні види бойових мистецтв та поєдинків', '#dc2626', 'fist'),
('Philosophical', 'Філософський', 'Philosophical', 'Філософські роздуми про життя, смерть, сенс буття', '#4f46e5', 'philosophy'),
('Space', 'Космос', 'Space', 'Космічні подорожі, міжпланетні війни', '#0f172a', 'rocket');

-- Insert many studios from import
INSERT OR IGNORE INTO studio (name, name_ua, country, founded, employees, description, website) VALUES
('MAPPA', 'MAPPA', 'Japan', 2011, 400, 'Відома студія, що створила Jujutsu Kaisen, Chainsaw Man, Attack on Titan (фінальні сезони).', 'https://mappa.co.jp'),
('Kyoto Animation', 'Kyoto Animation', 'Japan', 1981, 300, 'Студія з високою якістю анімації', 'https://kyotoanimation.co.jp'),
('Bones', 'Bones', 'Japan', 1998, 250, 'Відома за Fullmetal Alchemist', 'https://bones.co.jp'),
('Madhouse', 'Madhouse', 'Japan', 1972, 350, 'Одна з найстаріших та найвпливовіших студій.', 'https://madhouse.co.jp'),
('A-1 Pictures', 'A-1 Pictures', 'Japan', 2005, 300, 'Студія, що створила Sword Art Online, Kaguya-sama.', 'https://a1pictures.jp'),
('Ufotable', 'Ufotable', 'Japan', 2000, 250, 'Відома неймовірною якістю анімації та використанням CGI.', 'https://ufotable.com'),
('WIT Studio', 'WIT Studio', 'Japan', 2012, 200, 'Студія, заснована колишніми співробітниками Production I.G.', 'https://witstudio.co.jp'),
('CloverWorks', 'CloverWorks', 'Japan', 2018, 180, 'Дочірня студія Aniplex', 'https://cloverworks.co.jp'),
('Trigger', 'Trigger', 'Japan', 2011, 150, 'Студія, відома екстравагантним стилем', 'https://www.trigger.co.jp'),
('Pierrot', 'Pierrot', 'Japan', 1979, 220, 'Студія, що створила Naruto, Bleach', 'https://pierrot.jp'),
('Toei Animation', 'Toei Animation', 'Japan', 1948, 500, 'Найстаріша та одна з найбільших аніме-студій', 'https://www.toei-animation.com'),
('Production I.G', 'Production I.G', 'Japan', 1987, 280, 'Студія, відома реалістичною анімацією', 'https://www.production-ig.co.jp'),
('Sunrise', 'Sunrise', 'Japan', 1972, 350, 'Студія, відома франшизою Gundam', 'https://www.sunrise-inc.co.jp'),
('J.C.Staff', 'J.C.Staff', 'Japan', 1986, 200, 'Студія, що створила Toradora!', 'https://www.jcstaff.co.jp'),
('White Fox', 'White Fox', 'Japan', 2007, 150, 'Студія, що створила Re:Zero', 'http://whitefox-studio.com'),
('Shaft', 'Shaft', 'Japan', 1975, 120, 'Студія, відома унікальним візуальним стилем', 'http://shaft.co.jp');

-- Insert authors
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, gender, biography, image_url) VALUES
('Kentaro Miura', 'Кентаро Міура', 'Kentaro Miura', '1966-07-11', 'Chiba, Japan', 'Male', 'Kentaro Miura was a Japanese manga artist, best known for Berserk.', 'https://cdn.myanimelist.net/images/voiceactors/3/61071.jpg'),
('Takehiko Inoue', 'Такехіко Іноуе', 'Takehiko Inoue', '1967-01-12', 'Okuchi, Japan', 'Male', 'Best known for Slam Dunk, Vagabond.', 'https://cdn.myanimelist.net/images/voiceactors/2/54829.jpg'),
('ONE', 'ONE', 'ONE', '1986-10-29', 'Niigata, Japan', 'Male', 'Author of One Punch Man and Mob Psycho 100.', 'https://cdn.myanimelist.net/images/voiceactors/2/55863.jpg'),
('Tatsuki Fujimoto', 'Тацукі Фудзімото', 'Tatsuki Fujimoto', '1992-10-10', 'Akita, Japan', 'Male', 'Creator of Chainsaw Man.', 'https://cdn.myanimelist.net/images/voiceactors/3/66441.jpg');

-- Insert additional users
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES
('admin', 'admin@miks.ua', 'Адміністратор', 'admin', 'Головний адміністратор сайту. Люблю аніме та мангу.', 'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png'),
('animefan', 'fan@example.com', 'Аніме Фан', 'user', 'Дивлюсь аніме щодня. Люблю бойовики та пригоди.', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png'),
('mangalover', 'manga@example.com', 'Манга Любитель', 'user', 'Колекціоную мангу вже 10 років.', 'https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png'),
('reviewer', 'review@example.com', 'Оглядач', 'user', 'Пишу огляди на новинки аніме.', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png'),
('moderator', 'mod@example.com', 'Модератор', 'moderator', 'Слідкую за порядком на сайті.', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295772_1280.png');

-- Insert selected anime (adapted column names)
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, studio_id, cover_url, views, favorites) VALUES
('Frieren: Beyond Journey''s End', 'Проводжальниця Фрірен', 2023, 'Fall', 28, 25, 'TV', 'Ongoing', 'Manga', 'PG-13', 'The story follows the elf mage Frieren...', 1, 'https://cdn.myanimelist.net/images/anime/1015/138006.jpg', 150000, 25000),
('Jujutsu Kaisen', 'Магічна битва', 2020, 'Fall', 24, 23, 'TV', 'Ongoing', 'Manga', 'R', 'Yuji Itadori, a high schooler with immense physical strength...', 1, 'https://cdn.myanimelist.net/images/anime/1171/109222.jpg', 200000, 30000),
('Attack on Titan', 'Напад титанів', 2013, 'Spring', 25, 24, 'TV', 'Completed', 'Manga', 'R', 'Humanity lives inside enormous walled cities...', 7, 'https://cdn.myanimelist.net/images/anime/10/47347.jpg', 250000, 40000),
('One Piece', 'Ван Піс', 1999, 'Fall', 1000, 24, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Monkey D. Luffy sets out to become the Pirate King...', 11, 'https://cdn.myanimelist.net/images/anime/6/73245.jpg', 300000, 50000);

-- Insert selected manga
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, cover_url, views, favorites) VALUES
('Berserk', 'Берсерк', 1989, 'Ongoing', 42, 376, 'Manga', 'Seinen', 'Guts, the Black Swordsman...', 'https://cdn.myanimelist.net/images/manga/1/157897.jpg', 200000, 35000),
('One Punch Man', 'Людина-один удар', 2012, 'Ongoing', 28, 200, 'Manga', 'Shounen', 'Saitama is a hero...', 'https://cdn.myanimelist.net/images/manga/3/155939.jpg', 180000, 30000),
('Chainsaw Man', 'Людина-бензопила', 2018, 'Ongoing', 17, 152, 'Manga', 'Shounen', 'Denji is a young man in crippling debt...', 'https://cdn.myanimelist.net/images/manga/3/216464.jpg', 170000, 29000);

-- Insert some characters (adapted)
INSERT OR IGNORE INTO character (name, name_ua, full_name, gender, age, occupation, description, image_url, favorites) VALUES
('Frieren', 'Фрірен', 'Frieren', 'Female', 1000, 'Mage', 'Frieren is an elven mage...', 'https://cdn.hikka.io/content/characters/frieren-7f706c/zX7V8YWM3zljrr80U3aPIw.jpg', 12500),
('Yuji Itadori', 'Юджі Ітадорі', 'Yuji Itadori', 'Male', 16, 'Jujutsu Sorcerer', 'Yuji Itadori is a high schooler...', 'https://cdn.myanimelist.net/images/characters/2/423325.jpg', 24500),
('Denji', 'Денджі', 'Denji', 'Male', 16, 'Devil Hunter', 'Denji is the main protagonist of Chainsaw Man.', 'https://cdn.myanimelist.net/images/characters/16/459893.jpg', 22300);

-- Release dates simplified: replace DATEADD(...) with CURRENT_TIMESTAMP for seed
INSERT OR IGNORE INTO release_dates (anime_id, episode_number, release_date, title, description, is_confirmed) VALUES
(9, 13, CURRENT_TIMESTAMP, 'Chainsaw Man 2 сезон, 1 епізод', 'Прем''єра довгоочікуваного другого сезону', 1),
(10, 13, CURRENT_TIMESTAMP, 'Solo Leveling 2 сезон, 1 епізод', 'Сон Джіну повертається в новому сезоні', 1);

-- News (simplified dates)
INSERT OR IGNORE INTO news (title, content, summary, category, image_url, author_id, views, likes, published_at) VALUES
('Анонсовано дату прем''єри 3 сезону Магічної битви', 'Студія MAPPA офіційно оголосила дату виходу третього сезону Jujutsu Kaisen.', 'Jujutsu Kaisen 3 сезон вийде в липні 2025', 'Анонси', 'https://cdn.myanimelist.net/images/anime/1171/109222.jpg', 1, 25400, 2100, CURRENT_TIMESTAMP),
('Chainsaw Man фільм: Reze Arc отримав віковий рейтинг', 'Майбутній фільм отримав віковий рейтинг R+', 'Chainsaw Man: Reze Arc отримав рейтинг R+', 'Новини', 'https://cdn.myanimelist.net/images/anime/1806/126216.jpg', 2, 31200, 2800, CURRENT_TIMESTAMP);

-- Links: anime_genre, manga_genre, anime_character etc. (partial)
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,2),(1,4),(1,5),(2,1),(2,5),(3,1),(3,2);
INSERT OR IGNORE INTO manga_genre (manga_id, genre_id) VALUES (1,1),(1,2),(3,1),(4,1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (1,1,'Main',1),(2,2,'Main',1),(4,3,'Main',1);
INSERT OR IGNORE INTO manga_character (manga_id, character_id, role, is_main) VALUES (4,3,'Main',1);

-- Ratings (partial)
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES
(1,1,9.5,1,'Completed',28),(1,2,9.0,1,'Completed',28),(2,1,9.0,1,'Completed',24);

-- Comments (partial)
INSERT OR IGNORE INTO comments (user_id, character_id, content, likes, created_at) VALUES
(2,9,'Акі Хаякава - один з найкращих персонажів Chainsaw Man.',15,CURRENT_TIMESTAMP),(3,9,'Його смерть - одна з найсумніших сцен.',22,CURRENT_TIMESTAMP);

-- End of converted seed
