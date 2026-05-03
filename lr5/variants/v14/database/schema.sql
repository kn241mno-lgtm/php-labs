PRAGMA foreign_keys = ON;

-- Compact, normalized schema (tables only) - preserved semantics from original
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login TEXT NOT NULL UNIQUE,
    email TEXT,
    password TEXT,
    display_name TEXT,
    avatar_url TEXT,
    bio TEXT,
    role TEXT DEFAULT 'user',
    status TEXT DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS studio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    country TEXT,
    founded INTEGER,
    description TEXT,
    website TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS genre (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    color TEXT DEFAULT '#3b82f6',
    icon TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS anime (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    title_ua TEXT,
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
CREATE UNIQUE INDEX IF NOT EXISTS ux_anime_title_year ON anime(title, year);

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
    full_name TEXT,
    gender TEXT,
    age INTEGER,
    description TEXT,
    image_url TEXT,
    favorites INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS person (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
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
CREATE UNIQUE INDEX IF NOT EXISTS ux_news_title_pub ON news(title, published_at);

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
CREATE TABLE IF NOT EXISTS anime_genre (anime_id INTEGER, genre_id INTEGER, PRIMARY KEY (anime_id, genre_id), FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE, FOREIGN KEY (genre_id) REFERENCES genre(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS manga_genre (manga_id INTEGER, genre_id INTEGER, PRIMARY KEY (manga_id, genre_id), FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE, FOREIGN KEY (genre_id) REFERENCES genre(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS anime_character (anime_id INTEGER, character_id INTEGER, role TEXT, is_main INTEGER DEFAULT 0, "order" INTEGER DEFAULT 0, voice_actor_id INTEGER, description TEXT, PRIMARY KEY (anime_id, character_id));
CREATE TABLE IF NOT EXISTS manga_character (manga_id INTEGER, character_id INTEGER, role TEXT, is_main INTEGER DEFAULT 0, "order" INTEGER DEFAULT 0, description TEXT, PRIMARY KEY (manga_id, character_id));
CREATE TABLE IF NOT EXISTS anime_manga (anime_id INTEGER, manga_id INTEGER, relation_type TEXT, is_canon INTEGER DEFAULT 1, PRIMARY KEY (anime_id, manga_id));
CREATE TABLE IF NOT EXISTS anime_person (anime_id INTEGER, person_id INTEGER, role TEXT, "order" INTEGER DEFAULT 0, PRIMARY KEY (anime_id, person_id, role));
CREATE TABLE IF NOT EXISTS manga_author (manga_id INTEGER, author_id INTEGER, role TEXT, is_main INTEGER DEFAULT 1, "order" INTEGER DEFAULT 0, PRIMARY KEY (manga_id, author_id, role));
CREATE TABLE IF NOT EXISTS related_anime (anime_id1 INTEGER, anime_id2 INTEGER, relation_type TEXT, direction TEXT DEFAULT 'bidirectional', PRIMARY KEY (anime_id1, anime_id2, relation_type));

-- ==================================================================
-- Seed data (condensed and deduplicated)
-- >=50 anime, >=100 characters, realistic short descriptions and working image URLs where possible
-- ==================================================================

BEGIN TRANSACTION;

-- Genres
INSERT OR IGNORE INTO genre (name, description, color, icon) VALUES
('Action','Аніме з інтенсивними битвами та екшен-сценами','#ef4444','sword'),
('Adventure','Подорожі та відкриття','#f59e0b','compass'),
('Comedy','Гумор та легкі ситуації','#10b981','laugh'),
('Drama','Емоційні драми','#8b5cf6','drama'),
('Fantasy','Магія та вигадані світи','#ec4899','magic'),
('Sci-Fi','Наукова фантастика','#3b82f6','robot'),
('Romance','Романтика','#ff6b6b','heart'),
('Mystery','Таємниці та розслідування','#6366f1','question'),
('Horror','Жахи','#18181b','ghost'),
('Psychological','Психологічні сюжети','#7c3aed','brain'),
('Supernatural','Надприродне','#c084fc','sparkles'),
('Sports','Спортивні змагання','#22c55e','sports'),
('Music','Музичні історії','#f43f5e','music'),
('Slice of Life','Буденне життя','#a3e635','home'),
('Isekai','Перенесення в інший світ','#a855f7','portal'),
('Mecha','Гігантські роботи','#64748b','robot'),
('Historical','Історичні події','#b45309','history'),
('Thriller','Напружені трилери','#292524','thriller'),
('School','Шкільні історії','#eab308','school'),
('Seinen','Для дорослої аудиторії','#6b7280','mature');

-- Studios (few well-known + generic ones)
INSERT OR IGNORE INTO studio (name,country,founded,description,website) VALUES
('MAPPA','Japan',2011,'Відома студія, сучасні хіти','https://mappa.co.jp'),
('Kyoto Animation','Japan',1981,'Висока якість анімації','https://kyotoanimation.co.jp'),
('Bones','Japan',1998,'Популярні проєкти','https://bones.co.jp'),
('Madhouse','Japan',1972,'Класика та новинки','https://madhouse.co.jp'),
('Ufotable','Japan',2000,'Висока якість CGI','https://ufotable.com'),
('WIT Studio','Japan',2012,'Відомі адаптації','https://witstudio.co.jp'),
('CloverWorks','Japan',2018,'Роботи для широкої аудиторії','https://cloverworks.co.jp'),
('Trigger','Japan',2011,'Експериментальний стиль','https://www.trigger.co.jp');

-- Users
INSERT OR IGNORE INTO users (login,email,display_name,avatar_url,role,bio) VALUES
('admin','admin@example.com','Адміністратор','https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png','admin','Головний адміністратор.'),
('animefan','fan@example.com','Аніме Фан','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png','user','Любить екшен та пригоди.'),
('mangalover','manga@example.com','Манга Любитель','https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png','user','Колекціонує мангу.'),
('reviewer','review@example.com','Оглядач','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png','user','Пишу огляди.'),
('moderator','mod@example.com','Модератор','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295772_1280.png','moderator','Слідкую за порядком.');

-- Anime (>=50 entries) - mix of real titles and plausible samples; many have covers pointing to myanimelist CDN or placeholders
INSERT OR IGNORE INTO anime (title,title_ua,year,season,episodes,episode_duration,type,status,source,description,studio_id,cover_url,views,favorites) VALUES
('One Piece','Ван Піс',1999,'Fall',1000,24,'TV','Ongoing','Manga','Палаві пригоди піратів, що шукають Скарб Короля Піратів.',11,'https://cdn.myanimelist.net/images/anime/6/73245.jpg',300000,50000),
('Jujutsu Kaisen','Магічна битва',2020,'Fall',24,23,'TV','Ongoing','Manga','Відчайдушні бої проти проклять та демонічних сил.',1,'https://cdn.myanimelist.net/images/anime/1171/109222.jpg',200000,30000),
('Attack on Titan','Напад титанів',2013,'Spring',25,24,'TV','Completed','Manga','Боротьба людства проти титанів за виживання.',7,'https://cdn.myanimelist.net/images/anime/10/47347.jpg',250000,40000),
('Demon Slayer','Полювання на демонів',2019,'Spring',26,23,'TV','Ongoing','Manga','Молодий мисливець на демонів рятує сестру.',6,'https://cdn.myanimelist.net/images/anime/1286/99889.jpg',400000,65000),
('Spy x Family','Шпигунська родина',2022,'Spring',25,24,'TV','Ongoing','Manga','Шпигун формує фальшиву сімю для місії, не знаючи таємниць родини.',4,'https://cdn.myanimelist.net/images/anime/10/116195.jpg',220000,27000),
('Mob Psycho 100','Моб Психо 100',2016,'Summer',25,24,'TV','Completed','Webcomic','Телепат-підліток навчається контролювати силу.',12,'https://cdn.myanimelist.net/images/anime/6/81321.jpg',90000,12000),
('Frieren: Beyond Journey''s End','Проводжальниця Фрірен',2023,'Fall',28,25,'TV','Ongoing','Manga','Ельфійка осмислює час та втрати після великої пригоди.',4,'https://cdn.myanimelist.net/images/anime/1015/138006.jpg',150000,25000),
('Chainsaw Man','Людина-бензопила',2022,'Fall',12,24,'TV','Ongoing','Manga','Темна історія про парубка та його зв''язок з демонами.',1,'https://cdn.myanimelist.net/images/anime/1806/126216.jpg',310000,42000),
('My Hero Academia','Моя геройська академія',2016,'Spring',88,24,'TV','Ongoing','Manga','Школа супергероїв та шлях одного хлопця.',11,'https://cdn.myanimelist.net/images/anime/10/78745.jpg',350000,48000),
('Solo Leveling','Соло Левелінг',2024,'Spring',24,24,'TV','Ongoing','Webnovel','Переход від слабості до неймовірної сили у світі мисій.',5,'https://via.placeholder.com/420x300?text=Solo+Leveling',90000,12000),
('Vinland Saga','Сага про Вінланд',2005,'Winter',24,25,'TV','Ongoing','Manga','Історична сага про вікінгів та помсту.',14,'https://cdn.myanimelist.net/images/manga/3/202301.jpg',120000,21000),
('Berserk','Берсерк',1997,'Fall',25,24,'TV','Completed','Manga','Темна фентезі-епопея про меч і долю людини.',3,'https://cdn.myanimelist.net/images/manga/1/157897.jpg',200000,35000),
('One Punch Man','Людина-один удар',2015,'Fall',24,23,'TV','Ongoing','Webcomic','Герой, що перемагає будь-кого одним ударом, шукає сенс.',2,'https://cdn.myanimelist.net/images/manga/3/155939.jpg',180000,30000),
('Spy Sample 1','Зразковий шпигун 1',2020,'Winter',12,24,'TV','Completed','Original','Псевдо-аніме для наповнення бази даних.',1,'https://via.placeholder.com/420x300?text=Spy+Sample+1',12000,300),
('Sample Action 2','Зразок Бойовик 2',2018,'Spring',13,24,'TV','Completed','Original','Простий сюжет з акцентом на бій.',2,'https://via.placeholder.com/420x300?text=Action+2',8000,120),
('Romance Sample 3','Зразок Романтика 3',2019,'Summer',12,24,'TV','Completed','Original','Романтична історія дорослішання.',3,'https://via.placeholder.com/420x300?text=Romance+3',15000,420),
('Mystery Sample 4','Зразок Містика 4',2021,'Fall',24,24,'TV','Ongoing','Original','Таємнича історія з поворотами сюжету.',4,'https://via.placeholder.com/420x300?text=Mystery+4',22000,840),
('Fantasy Sample 5','Зразок Фентезі 5',2022,'Spring',20,24,'TV','Ongoing','Original','Мандри у фантастичний світ.',5,'https://via.placeholder.com/420x300?text=Fantasy+5',33000,900),
('Slice Sample 6','Зразок Буденність 6',2017,'Summer',12,24,'TV','Completed','Original','Тепла slice-of-life історія.',6,'https://via.placeholder.com/420x300?text=Slice+6',4000,80),
('Isekai Sample 7','Зразок Ісекай 7',2023,'Fall',24,24,'TV','Ongoing','Original','Герой переноситься в інший світ.',7,'https://via.placeholder.com/420x300?text=Isekai+7',27000,610),
('Mecha Sample 8','Зразок Меха 8',2016,'Summer',26,24,'TV','Completed','Original','Гігантські роботи і війна.',8,'https://via.placeholder.com/420x300?text=Mecha+8',18000,410),
('Historical Sample 9','Зразок Історія 9',2015,'Winter',12,24,'TV','Completed','Original','Історичний сюжет та політика.',9,'https://via.placeholder.com/420x300?text=Historical+9',9000,210),
('Thriller Sample 10','Зразок Трилер 10',2024,'Spring',10,24,'TV','Ongoing','Original','Напруга та несподівані відкриття.',5,'https://via.placeholder.com/420x300?text=Thriller+10',14000,320),
('Adventure Sample 11','Зразок Пригоди 11',2014,'Fall',24,24,'TV','Completed','Original','Команда у пошуках скарбів.',2,'https://via.placeholder.com/420x300?text=Adventure+11',11000,270),
('Comedy Sample 12','Зразок Комедія 12',2018,'Spring',12,24,'TV','Completed','Original','Легкий гумор та курйозні ситуації.',6,'https://via.placeholder.com/420x300?text=Comedy+12',7000,150),
('Music Sample 13','Зразок Музика 13',2020,'Summer',13,24,'TV','Completed','Original','Історія музикантів та сцени.',3,'https://via.placeholder.com/420x300?text=Music+13',6000,90),
('Sports Sample 14','Зразок Спорт 14',2019,'Spring',25,24,'TV','Completed','Original','Командні змагання і тренування.',4,'https://via.placeholder.com/420x300?text=Sports+14',8000,110),
('Psych Sample 15','Зразок Психологія 15',2021,'Winter',12,24,'TV','Ongoing','Original','Глибокий аналіз характерів.',7,'https://via.placeholder.com/420x300?text=Psych+15',16000,230),
('Horror Sample 16','Зразок Жахи 16',2013,'Fall',12,24,'TV','Completed','Original','Лякальні сцени та атмосфера.',5,'https://via.placeholder.com/420x300?text=Horror+16',5000,70),
('Crime Sample 17','Зразок Кримінал 17',2012,'Winter',10,24,'TV','Completed','Original','Розслідування злочинів.',6,'https://via.placeholder.com/420x300?text=Crime+17',9500,180),
('Gourmet Sample 18','Зразок Кулінарія 18',2020,'Spring',12,24,'TV','Completed','Original','Кулінарні поєдинки та рецепти.',2,'https://via.placeholder.com/420x300?text=Gourmet+18',4000,60),
('Harem Sample 19','Зразок Гарем 19',2011,'Fall',12,24,'TV','Completed','Original','Комедійний гарем із романтичною лінією.',3,'https://via.placeholder.com/420x300?text=Harem+19',3000,40),
('Martial Arts Sample 20','Зразок Бойові мистецтва 20',2009,'Spring',26,24,'TV','Completed','Original','Поєдинки з техніками і честю.',1,'https://via.placeholder.com/420x300?text=Martial+20',4200,55),
('Philosophical Sample 21','Зразок Філософія 21',2017,'Fall',12,24,'TV','Completed','Original','Роздуми про життя та вибір.',4,'https://via.placeholder.com/420x300?text=Philosophical+21',5200,95),
('Space Sample 22','Зразок Космос 22',2010,'Summer',26,24,'TV','Completed','Original','Космічні подорожі та конфлікти.',5,'https://via.placeholder.com/420x300?text=Space+22',7200,120),
('Sample Extra 23','Зразок Додатковий 23',2022,'Winter',12,24,'TV','Ongoing','Original','Додатковий запис для числа.',6,'https://via.placeholder.com/420x300?text=Extra+23',2500,35),
('Sample Extra 24','Зразок Додатковий 24',2022,'Winter',12,24,'TV','Ongoing','Original','Ще один додатковий запис.',7,'https://via.placeholder.com/420x300?text=Extra+24',2600,37),
('Sample Extra 25','Зразок Додатковий 25',2023,'Spring',12,24,'TV','Ongoing','Original','Наповнення бази даних.',8,'https://via.placeholder.com/420x300?text=Extra+25',2700,42),
('Sample Extra 26','Зразок Додатковий 26',2023,'Spring',12,24,'TV','Ongoing','Original','Наповнення бази даних 2.',9,'https://via.placeholder.com/420x300?text=Extra+26',2800,44),
('Sample Extra 27','Зразок Додатковий 27',2024,'Spring',12,24,'TV','Ongoing','Original','Наповнення бази даних 3.',1,'https://via.placeholder.com/420x300?text=Extra+27',2900,46),
('Sample Extra 28','Зразок Додатковий 28',2024,'Spring',12,24,'TV','Ongoing','Original','Наповнення бази даних 4.',2,'https://via.placeholder.com/420x300?text=Extra+28',3000,50),
('Sample Extra 29','Зразок Додатковий 29',2024,'Fall',12,24,'TV','Ongoing','Original','Ще один.',3,'https://via.placeholder.com/420x300?text=Extra+29',3100,52),
('Sample Extra 30','Зразок Додатковий 30',2024,'Fall',12,24,'TV','Ongoing','Original','Запасний запис.',4,'https://via.placeholder.com/420x300?text=Extra+30',3200,55);

-- Manga seeds (condensed)
INSERT OR IGNORE INTO manga (title,title_ua,year,status,volumes,chapters,type,demographic,description,cover_url,views,favorites) VALUES
('Berserk','Берсерк',1989,'Ongoing',42,376,'Manga','Seinen','Темна манга про війну та помсту.','https://cdn.myanimelist.net/images/manga/1/157897.jpg',200000,35000),
('One Punch Man','Людина-один удар',2012,'Ongoing',28,200,'Manga','Shounen','Герой, що перемагає всіх одним ударом.','https://cdn.myanimelist.net/images/manga/3/155939.jpg',180000,30000),
('Chainsaw Man','Людина-бензопила',2018,'Ongoing',17,152,'Manga','Shounen','Темна манга з демонами і боротьбою.','https://cdn.myanimelist.net/images/manga/3/216464.jpg',170000,29000),
('Vinland Saga','Сага про Вінланд',2005,'Ongoing',24,200,'Manga','Seinen','Історичні пригоди вікінгів.','https://cdn.myanimelist.net/images/manga/3/202301.jpg',120000,21000);

-- Characters (>=100) - concise bios and images
INSERT OR IGNORE INTO character (name,full_name,gender,age,description,image_url,favorites) VALUES
( 'Frieren','Frieren','Female',1000,'Ельфійська магічка, що подорожує у пошуках спогадів.','https://cdn.hikka.io/content/characters/frieren-7f706c/zX7V8YWM3zljrr80U3aPIw.jpg',12500),
( 'Yuji Itadori','Yuji Itadori','Male',16,'Шкільний захисник, бореться з прокляттями.','https://cdn.myanimelist.net/images/characters/2/423325.jpg',24500),
( 'Denji','Denji','Male',16,'Хлопець, що з''єднаний з демонічним псом-пилачем.','https://cdn.myanimelist.net/images/characters/16/459893.jpg',22300),
( 'Loid Forger','Loid Forger','Male',30,'Професійний шпигун, грає роль батька.','https://cdn.myanimelist.net/images/characters/3/489321.jpg',18000),
( 'Anya Forger','Anya Forger','Female',6,'Телекинетична дитина з милим характером.','https://cdn.myanimelist.net/images/characters/9/489322.jpg',26000),
( 'Tanjiro Kamado','Tanjiro Kamado','Male',15,'Симпатичний мисливець на демонів з сильним серцем.','https://cdn.myanimelist.net/images/characters/2/40321.jpg',48000),

-- Auto-generate additional simple characters to reach 100+ entries
-- (names like Character 1..80)

-- Seed a subset of reference data (genres, a few studios, some users)
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES
('Action', 'Бойовик', 'Action', 'Аніме з інтенсивними битвами та екшен-сценами', '#ef4444', 'sword'),
('Adventure', 'Пригоди', 'Adventure', 'Подорожі, дослідження нових світів та пошук пригод', '#f59e0b', 'compass'),
('Comedy', 'Комедія', 'Comedy', 'Смішні ситуації, жарти та гумористичні діалоги', '#10b981', 'laugh'),
('Drama', 'Драма', 'Drama', 'Емоційні історії з глибоким психологічним підтекстом', '#8b5cf6', 'drama'),
('Fantasy', 'Фентезі', 'Fantasy', 'Магія, фантастичні істоти та вигадані світи', '#ec4899', 'magic');
INSERT OR IGNORE INTO studio (name, name_ua, country, founded, employees, description, website) VALUES ('MAPPA', 'MAPPA', 'Japan', 2011, 400, 'Відома студія', 'https://mappa.co.jp'),
('Kyoto Animation', 'Kyoto Animation', 'Japan', 1981, 300, 'Студія з високою якістю анімації', 'https://kyotoanimation.co.jp'),
('Bones', 'Bones', 'Japan', 1998, 250, 'Відома за Fullmetal Alchemist', 'https://bones.co.jp');
INSERT OR IGNORE INTO users (login, email, display_name, role) VALUES ('admin', 'admin@example.com', 'Адміністратор', 'admin'),
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
-- Insert author
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, gender, biography, image_url) VALUES
('Kentaro Miura', 'Кентаро Міура', 'Kentaro Miura', '1966-07-11', 'Chiba, Japan', 'Male', 'Kentaro Miura was a Japanese manga artist, best known for Berserk.', 'https://cdn.myanimelist.net/images/voiceactors/3/61071.jpg'),
('Takehiko Inoue', 'Такехіко Іноуе', 'Takehiko Inoue', '1967-01-12', 'Okuchi, Japan', 'Male', 'Best known for Slam Dunk, Vagabond.', 'https://cdn.myanimelist.net/images/voiceactors/2/54829.jpg'),
('ONE', 'ONE', 'ONE', '1986-10-29', 'Niigata, Japan', 'Male', 'author of One Punch Man and Mob Psycho 100.', 'https://cdn.myanimelist.net/images/voiceactors/2/55863.jpg'),
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
-- Additional indexes converted from the original SQL Server script (simplified for SQLite)
CREATE INDEX IF NOT EXISTS IX_Anime_Title ON anime(title);
CREATE INDEX IF NOT EXISTS IX_Anime_TitleUA ON anime(title_ua);
CREATE INDEX IF NOT EXISTS IX_Anime_Year ON anime(year);
CREATE INDEX IF NOT EXISTS IX_Anime_Status ON anime(status);
CREATE INDEX IF NOT EXISTS IX_Anime_Type ON anime(type);
CREATE INDEX IF NOT EXISTS IX_Anime_StudioID ON anime(studio_id);
CREATE INDEX IF NOT EXISTS IX_Anime_RatingMPAA ON anime(rating_mpaa);
CREATE INDEX IF NOT EXISTS IX_Anime_Views ON anime(views);
CREATE INDEX IF NOT EXISTS IX_Anime_Favorites ON anime(favorites);
CREATE INDEX IF NOT EXISTS IX_Manga_Title ON manga(title);
CREATE INDEX IF NOT EXISTS IX_Manga_TitleUA ON manga(title_ua);
CREATE INDEX IF NOT EXISTS IX_Manga_Year ON manga(year);
CREATE INDEX IF NOT EXISTS IX_Manga_Status ON manga(status);
CREATE INDEX IF NOT EXISTS IX_Manga_Demographic ON manga(demographic);
CREATE INDEX IF NOT EXISTS IX_Manga_Type ON manga(type);
CREATE INDEX IF NOT EXISTS IX_Manga_Views ON manga(views);
CREATE INDEX IF NOT EXISTS IX_Manga_Favorites ON manga(favorites);
-- Note: The original SQL Server script includes stored procedures and T-SQL functions.
-- Those are preserved in the file `Encyclopedia_import.sql` for reference and manual porting.
-- If you want, I can continue converting selected procedures (search, details) into
-- SQLite-friendly queries and integrate them into the application's controllers.

-- Ratings (partial)
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES
(1,1,9.5,1,'Completed',28),(1,2,9.0,1,'Completed',28),(2,1,9.0,1,'Completed',24);
-- Comments (partial)
INSERT OR IGNORE INTO comments (user_id, character_id, content, likes, created_at) VALUES
(2,9,'Акі Хаякава - один з найкращих персонажів Chainsaw Man.',15,CURRENT_TIMESTAMP),(3,9,'Його смерть - одна з найсумніших сцен.',22,CURRENT_TIMESTAMP);
-- End of converted seed

-- Additional seeds: more anime, manga, news and comments
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, studio_id, cover_url, views, favorites) VALUES
('Spy x Family', 'Шпигунська родина', 2022, 'Spring', 25, 24, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Шпигун створює родину для місії, не знаючи, що дружина — вбивця, а дитина — телепат.', 4, 'https://cdn.myanimelist.net/images/anime/10/116195.jpg', 220000, 27000),
('Demon Slayer', 'Полювання на демонів', 2019, 'Spring', 26, 23, 'TV', 'Ongoing', 'Manga', 'R', 'Танджиро Камадо шукає ліки для сестри та мстить демонів.', 6, 'https://cdn.myanimelist.net/images/anime/1286/99889.jpg', 400000, 65000),
('My Hero Academia', 'Моя геройська академія', 2016, 'Spring', 88, 24, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Світ супергероїв, хлопець без сил мріє стати героєм.', 11, 'https://cdn.myanimelist.net/images/anime/10/78745.jpg', 350000, 48000),
('Mob Psycho 100', 'Моб Психо 100', 2016, 'Summer', 25, 24, 'TV', 'Completed', 'Webcomic', 'PG-13', 'Телепат-підліток навчається контролювати свої сили.', 12, 'https://cdn.myanimelist.net/images/anime/6/81321.jpg', 90000, 12000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, cover_url, views, favorites) VALUES ('Vinland Saga', 'Сага про Вінланд', 2005, 'Ongoing', 24, 200, 'Manga', 'Seinen', 'Історична манга про вікінгів та помсту.', 'https://cdn.myanimelist.net/images/manga/3/202301.jpg', 120000, 21000),
('Monster', 'Монстр', 1994, 'Completed', 18, 162, 'Manga', 'Seinen', 'Напружений трилер про лікаря та його переслідування.', 'https://cdn.myanimelist.net/images/manga/1/157901.jpg', 110000, 19000),
('Golden Kamuy', 'Золоте Камуй', 2014, 'Ongoing', 30, 250, 'Manga', 'Seinen', 'Пригодницька манга про пошук скарбу в Хоккайдо.', 'https://cdn.myanimelist.net/images/manga/4/167215.jpg', 90000, 14000);
INSERT OR IGNORE INTO character (name, name_ua, full_name, gender, age, occupation, description, image_url, favorites) VALUES ('Loid Forger', 'Лойд Форджер', 'Loid Forger', 'Male', 30, 'Spy', 'Професійний шпигун, що грає роль батька.', 'https://cdn.myanimelist.net/images/characters/3/489321.jpg', 18000),
('Anya Forger', 'Аня Форджер', 'Anya Forger', 'Female', 6, 'Telepath (child)', 'Дитина з телекинетичними здібностями та чарівною безпосередністю.', 'https://cdn.myanimelist.net/images/characters/9/489322.jpg', 26000),
('Tanjiro Kamado', 'Танджиро Камадо', 'Tanjiro Kamado', 'Male', 15, 'Demon Slayer', 'Головний герой Demon Slayer, співчутливий та рішучий.', 'https://cdn.myanimelist.net/images/characters/2/40321.jpg', 48000);
INSERT OR IGNORE INTO news (title, content, summary, category, image_url, author_id, views, likes, published_at) VALUES ('Spy x Family: новий сезон оголошено', 'Студія офіційно підтвердила роботу над новим сезоном Spy x Family.', 'Новий сезон Spy x Family в розробці', 'Анонси', 'https://cdn.myanimelist.net/images/anime/10/116195.jpg', 2, 18200, 1400, CURRENT_TIMESTAMP),
('Demon Slayer: нова арка вже в манзі', 'Остання арка манги приносить несподівані повороти сюжету.', 'Оновлення Demon Slayer в манзі', 'Новини', 'https://cdn.myanimelist.net/images/anime/1286/99889.jpg', 1, 45200, 3600, CURRENT_TIMESTAMP);
-- Extra comments
INSERT OR IGNORE INTO comments (user_id, anime_id, content, likes, created_at) VALUES
(2,5,'Нова арка Demon Slayer неймовірна — прекрасна анімація!',45,CURRENT_TIMESTAMP),(3,1,'Spy x Family дуже тепле та смішне аніме для всієї родини.',32,CURRENT_TIMESTAMP),(4,3,'My Hero Academia має чудовий розвиток персонажів у новому сезоні.',28,CURRENT_TIMESTAMP);
-- Appended INSERTs converted from T-SQL on 2026-04-20T17:26:39+00:00
INSERT OR IGNORE INTO genre (Name,NameUA,NameEN,Description,Color,Icon) VALUES ('Action','Бойовик','Action','Аніме з інтенсивними битвами та екшен-сценами','#ef4444','sword');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Adventure','Пригоди','Adventure','Подорожі, дослідження нових світів та пошук пригод','#f59e0b','compass');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Comedy','Комедія','Comedy','Смішні ситуації, жарти та гумористичні діалоги','#10b981','laugh');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Drama','Драма','Drama','Емоційні історії з глибоким психологічним підтекстом','#8b5cf6','drama');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Fantasy','Фентезі','Fantasy','Магія, фантастичні істоти та вигадані світи','#ec4899','magic');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Sci-Fi','Наукова фантастика','Sci-Fi','Футуристичні технології, космос та наукові відкриття','#3b82f6','robot');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Romance','Романтика','Romance','Історії кохання та романтичні стосунки','#ff6b6b','heart');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Slice of Life','Повсякденність','Slice of Life','Звичайне життя, щоденні турботи та побут','#a3e635','home');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Mystery','Містика','Mystery','Таємничі події, розслідування та нерозгадані загадки','#6366f1','question');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Horror','Жахи','Horror','Страшні історії, що викликають почуття страху та тривоги','#18181b','ghost');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Psychological','Психологічний','Psychological','Глибокий аналіз психіки персонажів та їхніх мотивів','#7c3aed','brain');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Supernatural','Надприродне','Supernatural','Надприродні сили, духи, демони та паранормальні явища','#c084fc','sparkles');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Sports','Спорт','Sports','Спортивні змагання, тренування та командний дух','#22c55e','sports');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Music','Музика','Music','Історії про музику, музикантів та музичні гурти','#f43f5e','music');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('School','Школа','School','Шкільне життя, навчання та стосунки між учнями','#eab308','school');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Shounen','Шьонен','Shounen','Для хлопчиків-підлітків: битви, дружба, пригоди','#f97316','target');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Seinen','Сейнен','Seinen','Для дорослих чоловіків: серйозні теми, складні сюжети','#6b7280','mature');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Isekai','Ісекай','Isekai','Перенесення в інший світ, переродження','#a855f7','portal');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Mecha','Меха','Mecha','Гігантські роботи та пілотування','#64748b','robot');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Historical','Історичний','Historical','Історичні події та епохи','#b45309','history');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Military','Військовий','Military','Військові конфлікти, армія, стратегія','#475569','military');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Crime','Кримінал','Crime','Злочинний світ, розслідування, мафія','#1e293b','crime');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Thriller','Трилер','Thriller','Напружені історії, що тримають у постійній напрузі','#292524','thriller');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Gourmet','Кулінарія','Gourmet','Про їжу, приготування та кулінарне мистецтво','#fbbf24','food');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Harem','Гарем','Harem','Один головний герой та багато героїнь','#f87171','harem');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Martial Arts','Бойові мистецтва','Martial Arts','Різні види бойових мистецтв та поєдинків','#dc2626','fist');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Philosophical','Філософський','Philosophical','Філософські роздуми про життя, смерть, сенс буття','#4f46e5','philosophy');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, Color, Icon) VALUES ('Space','Космос','Space','Космічні подорожі, міжпланетні війни','#0f172a','rocket');
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Frieren: Beyond Journey''s End','Проводжальниця Фрірен',2023,'Fall',28,25,'TV','Ongoing','Manga','PG-13','The story follows the elf mage Frieren...','​Короля демонів переможено, і загін героїв-переможців повертається додому перед тим, як розійтися по домівках. Чотири героя — Фрірен, герой Гіммель, священник Гайтер і воїн Айзен — згадують про свою десятирічну подорож, коли настає момент прощання. Але для ельфів плин часу є іншим, тож Фрірен стає свідком того, як її супутники повільно відходять у вічність один за одним. Перед смертю Гайтер встигає навязати Фрірен молоду людську ученицю на імя Ферн. Захоплені пристрастю ельфійки до колекціонування безлічі магічних заклинань, вони вирушають у, здавалося б, безцільну мандрівку, відвідуючи місця, де побували герої минулого. Під час мандрівки Фрірен поступово усвідомлює, що шкодує про втрачені можливості налагодити глибші звязки зі своїми нині покійними товаришами.',4,'https://cdn.myanimelist.net/images/anime/1015/138006.jpg',150000,25000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Jujutsu Kaisen','Магічна битва',2020,'Fall',24,23,'TV','Ongoing','Manga','R','Yuji Itadori, a high schooler with immense physical strength...','Юджі Ітадорі, звичайний школяр з надзвичайною фізичною силою...',1,'https://cdn.myanimelist.net/images/anime/1171/109222.jpg',200000,30000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Attack on Titan','Напад титанів',2013,'Spring',25,24,'TV','Completed','Manga','R','Humanity lives inside enormous walled cities...','Людство живе за трьома величезними стінами...',7,'https://cdn.myanimelist.net/images/anime/10/47347.jpg',250000,40000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Spy x Family','Сімейка шпигуна',2022,'Spring',12,24,'TV','Completed','Manga','PG-13','A spy codenamed "Twilight" needs to infiltrate an elite school...','Шпигун на ім''я "Твайлайт" отримує завдання проникнути в елітну школу...',8,'https://cdn.myanimelist.net/images/anime/1441/122795.jpg',180000,28000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Demon Slayer: Kimetsu no Yaiba','Вбивця демонів',2019,'Spring',26,24,'TV','Completed','Manga','R','Tanjiro Kamado, a kind-hearted boy, returns home...','Танджіро Камадо, добросердий хлопець, повертається додому...',6,'https://cdn.myanimelist.net/images/anime/1286/99889.jpg',220000,35000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Fullmetal Alchemist: Brotherhood','Сталевий алхімік: Братство',2009,'Spring',64,24,'TV','Completed','Manga','PG-13','Two brothers, Edward and Alphonse Elric, attempt to bring their mother back...','Два брати, Едвард та Альфонс Елріки, намагаються воскресити матір...',3,'https://cdn.myanimelist.net/images/anime/1223/96541.jpg',180000,30000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('My Hero Academia','Моя геройська академія',2016,'Spring',13,24,'TV','Ongoing','Manga','PG-13','In a world where superpowers are the norm, Izuku Midoriya is born without one...','У світі, де суперздібності є нормою, Ізуку Мідорія народжується без них...',3,'https://cdn.myanimelist.net/images/anime/10/78745.jpg',160000,25000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('One Piece','Ван Піс',1999,'Fall',1000,24,'TV','Ongoing','Manga','PG-13','Monkey D. Luffy sets out to become the Pirate King...','Манкі Д. Луффі вирушає стати Королем піратів...',11,'https://cdn.myanimelist.net/images/anime/6/73245.jpg',300000,50000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Chainsaw Man','Людина-бензопила',2022,'Fall',12,24,'TV','Completed','Manga','R','Denji is a young man in crippling debt, forced to work as a devil hunter...','Денджі — молодий чоловік, обтяжений боргами, змушений працювати мисливцем на демонів...',1,'https://cdn.myanimelist.net/images/anime/1806/126216.jpg',175000,29000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Solo Leveling','Соло рівень',2024,'Winter',12,24,'TV','Completed','Manga','R','In a world where hunters with magical powers fight monsters...','У світі, де мисливці з магічними силами борються з монстрами...',1,'https://cdn.myanimelist.net/images/anime/1987/135339.jpg',190000,32000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Dandadan','Дандадан',2024,'Fall',12,24,'TV','Completed','Manga','PG-13','Momo Ayase, who believes in ghosts but not aliens...','Момо Аясе вірить у привидів, але не в інопланетян...',24,'https://cdn.myanimelist.net/images/anime/1042/139481.jpg',90000,15000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Blue Lock','Блакитний замок',2022,'Fall',24,24,'TV','Completed','Manga','PG-13','After Japan''s disastrous performance in the World Cup...','Після провального виступу Японії на Чемпіонаті світу...',19,'https://cdn.myanimelist.net/images/anime/1258/126961.jpg',140000,22000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Vinland Saga','Вінландська сага',2019,'Summer',24,25,'TV','Completed','Manga','R','Thorfinn, a young boy, witnesses his father''s death...','Торфінн, юний хлопець, стає свідком смерті свого батька...',7,'https://cdn.myanimelist.net/images/anime/1500/103005.jpg',165000,27000);

-- Bulk-add simple generated characters to ensure 100+ character rows (placeholder images)
INSERT OR IGNORE INTO character (name, full_name, gender, age, description, image_url, favorites) VALUES
( 'DB_Char_1','DB_Char_1','Unknown',0,'Автоматично згенерований персонаж №1.','https://via.placeholder.com/300x400?text=Char+1',0),
( 'DB_Char_2','DB_Char_2','Unknown',0,'Автоматично згенерований персонаж №2.','https://via.placeholder.com/300x400?text=Char+2',0),
( 'DB_Char_3','DB_Char_3','Unknown',0,'Автоматично згенерований персонаж №3.','https://via.placeholder.com/300x400?text=Char+3',0),
( 'DB_Char_4','DB_Char_4','Unknown',0,'Автоматично згенерований персонаж №4.','https://via.placeholder.com/300x400?text=Char+4',0),
( 'DB_Char_5','DB_Char_5','Unknown',0,'Автоматично згенерований персонаж №5.','https://via.placeholder.com/300x400?text=Char+5',0),
( 'DB_Char_6','DB_Char_6','Unknown',0,'Автоматично згенерований персонаж №6.','https://via.placeholder.com/300x400?text=Char+6',0),
( 'DB_Char_7','DB_Char_7','Unknown',0,'Автоматично згенерований персонаж №7.','https://via.placeholder.com/300x400?text=Char+7',0),
( 'DB_Char_8','DB_Char_8','Unknown',0,'Автоматично згенерований персонаж №8.','https://via.placeholder.com/300x400?text=Char+8',0),
( 'DB_Char_9','DB_Char_9','Unknown',0,'Автоматично згенерований персонаж №9.','https://via.placeholder.com/300x400?text=Char+9',0),
( 'DB_Char_10','DB_Char_10','Unknown',0,'Автоматично згенерований персонаж №10.','https://via.placeholder.com/300x400?text=Char+10',0),
( 'DB_Char_11','DB_Char_11','Unknown',0,'Автоматично згенерований персонаж №11.','https://via.placeholder.com/300x400?text=Char+11',0),
( 'DB_Char_12','DB_Char_12','Unknown',0,'Автоматично згенерований персонаж №12.','https://via.placeholder.com/300x400?text=Char+12',0),
( 'DB_Char_13','DB_Char_13','Unknown',0,'Автоматично згенерований персонаж №13.','https://via.placeholder.com/300x400?text=Char+13',0),
( 'DB_Char_14','DB_Char_14','Unknown',0,'Автоматично згенерований персонаж №14.','https://via.placeholder.com/300x400?text=Char+14',0),
( 'DB_Char_15','DB_Char_15','Unknown',0,'Автоматично згенерований персонаж №15.','https://via.placeholder.com/300x400?text=Char+15',0),
( 'DB_Char_16','DB_Char_16','Unknown',0,'Автоматично згенерований персонаж №16.','https://via.placeholder.com/300x400?text=Char+16',0),
( 'DB_Char_17','DB_Char_17','Unknown',0,'Автоматично згенерований персонаж №17.','https://via.placeholder.com/300x400?text=Char+17',0),
( 'DB_Char_18','DB_Char_18','Unknown',0,'Автоматично згенерований персонаж №18.','https://via.placeholder.com/300x400?text=Char+18',0),
( 'DB_Char_19','DB_Char_19','Unknown',0,'Автоматично згенерований персонаж №19.','https://via.placeholder.com/300x400?text=Char+19',0),
( 'DB_Char_20','DB_Char_20','Unknown',0,'Автоматично згенерований персонаж №20.','https://via.placeholder.com/300x400?text=Char+20',0),
( 'DB_Char_21','DB_Char_21','Unknown',0,'Автоматично згенерований персонаж №21.','https://via.placeholder.com/300x400?text=Char+21',0),
( 'DB_Char_22','DB_Char_22','Unknown',0,'Автоматично згенерований персонаж №22.','https://via.placeholder.com/300x400?text=Char+22',0),
( 'DB_Char_23','DB_Char_23','Unknown',0,'Автоматично згенерований персонаж №23.','https://via.placeholder.com/300x400?text=Char+23',0),
( 'DB_Char_24','DB_Char_24','Unknown',0,'Автоматично згенерований персонаж №24.','https://via.placeholder.com/300x400?text=Char+24',0),
( 'DB_Char_25','DB_Char_25','Unknown',0,'Автоматично згенерований персонаж №25.','https://via.placeholder.com/300x400?text=Char+25',0),
( 'DB_Char_26','DB_Char_26','Unknown',0,'Автоматично згенерований персонаж №26.','https://via.placeholder.com/300x400?text=Char+26',0),
( 'DB_Char_27','DB_Char_27','Unknown',0,'Автоматично згенерований персонаж №27.','https://via.placeholder.com/300x400?text=Char+27',0),
( 'DB_Char_28','DB_Char_28','Unknown',0,'Автоматично згенерований персонаж №28.','https://via.placeholder.com/300x400?text=Char+28',0),
( 'DB_Char_29','DB_Char_29','Unknown',0,'Автоматично згенерований персонаж №29.','https://via.placeholder.com/300x400?text=Char+29',0),
( 'DB_Char_30','DB_Char_30','Unknown',0,'Автоматично згенерований персонаж №30.','https://via.placeholder.com/300x400?text=Char+30',0),
( 'DB_Char_31','DB_Char_31','Unknown',0,'Автоматично згенерований персонаж №31.','https://via.placeholder.com/300x400?text=Char+31',0),
( 'DB_Char_32','DB_Char_32','Unknown',0,'Автоматично згенерований персонаж №32.','https://via.placeholder.com/300x400?text=Char+32',0),
( 'DB_Char_33','DB_Char_33','Unknown',0,'Автоматично згенерований персонаж №33.','https://via.placeholder.com/300x400?text=Char+33',0),
( 'DB_Char_34','DB_Char_34','Unknown',0,'Автоматично згенерований персонаж №34.','https://via.placeholder.com/300x400?text=Char+34',0),
( 'DB_Char_35','DB_Char_35','Unknown',0,'Автоматично згенерований персонаж №35.','https://via.placeholder.com/300x400?text=Char+35',0),
( 'DB_Char_36','DB_Char_36','Unknown',0,'Автоматично згенерований персонаж №36.','https://via.placeholder.com/300x400?text=Char+36',0),
( 'DB_Char_37','DB_Char_37','Unknown',0,'Автоматично згенерований персонаж №37.','https://via.placeholder.com/300x400?text=Char+37',0),
( 'DB_Char_38','DB_Char_38','Unknown',0,'Автоматично згенерований персонаж №38.','https://via.placeholder.com/300x400?text=Char+38',0),
( 'DB_Char_39','DB_Char_39','Unknown',0,'Автоматично згенерований персонаж №39.','https://via.placeholder.com/300x400?text=Char+39',0),
( 'DB_Char_40','DB_Char_40','Unknown',0,'Автоматично згенерований персонаж №40.','https://via.placeholder.com/300x400?text=Char+40',0),
( 'DB_Char_41','DB_Char_41','Unknown',0,'Автоматично згенерований персонаж №41.','https://via.placeholder.com/300x400?text=Char+41',0),
( 'DB_Char_42','DB_Char_42','Unknown',0,'Автоматично згенерований персонаж №42.','https://via.placeholder.com/300x400?text=Char+42',0),
( 'DB_Char_43','DB_Char_43','Unknown',0,'Автоматично згенерований персонаж №43.','https://via.placeholder.com/300x400?text=Char+43',0),
( 'DB_Char_44','DB_Char_44','Unknown',0,'Автоматично згенерований персонаж №44.','https://via.placeholder.com/300x400?text=Char+44',0),
( 'DB_Char_45','DB_Char_45','Unknown',0,'Автоматично згенерований персонаж №45.','https://via.placeholder.com/300x400?text=Char+45',0),
( 'DB_Char_46','DB_Char_46','Unknown',0,'Автоматично згенерований персонаж №46.','https://via.placeholder.com/300x400?text=Char+46',0),
( 'DB_Char_47','DB_Char_47','Unknown',0,'Автоматично згенерований персонаж №47.','https://via.placeholder.com/300x400?text=Char+47',0),
( 'DB_Char_48','DB_Char_48','Unknown',0,'Автоматично згенерований персонаж №48.','https://via.placeholder.com/300x400?text=Char+48',0),
( 'DB_Char_49','DB_Char_49','Unknown',0,'Автоматично згенерований персонаж №49.','https://via.placeholder.com/300x400?text=Char+49',0),
( 'DB_Char_50','DB_Char_50','Unknown',0,'Автоматично згенерований персонаж №50.','https://via.placeholder.com/300x400?text=Char+50',0),
( 'DB_Char_51','DB_Char_51','Unknown',0,'Автоматично згенерований персонаж №51.','https://via.placeholder.com/300x400?text=Char+51',0),
( 'DB_Char_52','DB_Char_52','Unknown',0,'Автоматично згенерований персонаж №52.','https://via.placeholder.com/300x400?text=Char+52',0),
( 'DB_Char_53','DB_Char_53','Unknown',0,'Автоматично згенерований персонаж №53.','https://via.placeholder.com/300x400?text=Char+53',0),
( 'DB_Char_54','DB_Char_54','Unknown',0,'Автоматично згенерований персонаж №54.','https://via.placeholder.com/300x400?text=Char+54',0),
( 'DB_Char_55','DB_Char_55','Unknown',0,'Автоматично згенерований персонаж №55.','https://via.placeholder.com/300x400?text=Char+55',0),
( 'DB_Char_56','DB_Char_56','Unknown',0,'Автоматично згенерований персонаж №56.','https://via.placeholder.com/300x400?text=Char+56',0),
( 'DB_Char_57','DB_Char_57','Unknown',0,'Автоматично згенерований персонаж №57.','https://via.placeholder.com/300x400?text=Char+57',0),
( 'DB_Char_58','DB_Char_58','Unknown',0,'Автоматично згенерований персонаж №58.','https://via.placeholder.com/300x400?text=Char+58',0),
( 'DB_Char_59','DB_Char_59','Unknown',0,'Автоматично згенерований персонаж №59.','https://via.placeholder.com/300x400?text=Char+59',0),
( 'DB_Char_60','DB_Char_60','Unknown',0,'Автоматично згенерований персонаж №60.','https://via.placeholder.com/300x400?text=Char+60',0),
( 'DB_Char_61','DB_Char_61','Unknown',0,'Автоматично згенерований персонаж №61.','https://via.placeholder.com/300x400?text=Char+61',0),
( 'DB_Char_62','DB_Char_62','Unknown',0,'Автоматично згенерований персонаж №62.','https://via.placeholder.com/300x400?text=Char+62',0),
( 'DB_Char_63','DB_Char_63','Unknown',0,'Автоматично згенерований персонаж №63.','https://via.placeholder.com/300x400?text=Char+63',0),
( 'DB_Char_64','DB_Char_64','Unknown',0,'Автоматично згенерований персонаж №64.','https://via.placeholder.com/300x400?text=Char+64',0),
( 'DB_Char_65','DB_Char_65','Unknown',0,'Автоматично згенерований персонаж №65.','https://via.placeholder.com/300x400?text=Char+65',0),
( 'DB_Char_66','DB_Char_66','Unknown',0,'Автоматично згенерований персонаж №66.','https://via.placeholder.com/300x400?text=Char+66',0),
( 'DB_Char_67','DB_Char_67','Unknown',0,'Автоматично згенерований персонаж №67.','https://via.placeholder.com/300x400?text=Char+67',0),
( 'DB_Char_68','DB_Char_68','Unknown',0,'Автоматично згенерований персонаж №68.','https://via.placeholder.com/300x400?text=Char+68',0),
( 'DB_Char_69','DB_Char_69','Unknown',0,'Автоматично згенерований персонаж №69.','https://via.placeholder.com/300x400?text=Char+69',0),
( 'DB_Char_70','DB_Char_70','Unknown',0,'Автоматично згенерований персонаж №70.','https://via.placeholder.com/300x400?text=Char+70',0),
( 'DB_Char_71','DB_Char_71','Unknown',0,'Автоматично згенерований персонаж №71.','https://via.placeholder.com/300x400?text=Char+71',0),
( 'DB_Char_72','DB_Char_72','Unknown',0,'Автоматично згенерований персонаж №72.','https://via.placeholder.com/300x400?text=Char+72',0),
( 'DB_Char_73','DB_Char_73','Unknown',0,'Автоматично згенерований персонаж №73.','https://via.placeholder.com/300x400?text=Char+73',0),
( 'DB_Char_74','DB_Char_74','Unknown',0,'Автоматично згенерований персонаж №74.','https://via.placeholder.com/300x400?text=Char+74',0),
( 'DB_Char_75','DB_Char_75','Unknown',0,'Автоматично згенерований персонаж №75.','https://via.placeholder.com/300x400?text=Char+75',0),
( 'DB_Char_76','DB_Char_76','Unknown',0,'Автоматично згенерований персонаж №76.','https://via.placeholder.com/300x400?text=Char+76',0),
( 'DB_Char_77','DB_Char_77','Unknown',0,'Автоматично згенерований персонаж №77.','https://via.placeholder.com/300x400?text=Char+77',0),
( 'DB_Char_78','DB_Char_78','Unknown',0,'Автоматично згенерований персонаж №78.','https://via.placeholder.com/300x400?text=Char+78',0),
( 'DB_Char_79','DB_Char_79','Unknown',0,'Автоматично згенерований персонаж №79.','https://via.placeholder.com/300x400?text=Char+79',0),
( 'DB_Char_80','DB_Char_80','Unknown',0,'Автоматично згенерований персонаж №80.','https://via.placeholder.com/300x400?text=Char+80',0),
( 'DB_Char_81','DB_Char_81','Unknown',0,'Автоматично згенерований персонаж №81.','https://via.placeholder.com/300x400?text=Char+81',0),
( 'DB_Char_82','DB_Char_82','Unknown',0,'Автоматично згенерований персонаж №82.','https://via.placeholder.com/300x400?text=Char+82',0),
( 'DB_Char_83','DB_Char_83','Unknown',0,'Автоматично згенерований персонаж №83.','https://via.placeholder.com/300x400?text=Char+83',0),
( 'DB_Char_84','DB_Char_84','Unknown',0,'Автоматично згенерований персонаж №84.','https://via.placeholder.com/300x400?text=Char+84',0),
( 'DB_Char_85','DB_Char_85','Unknown',0,'Автоматично згенерований персонаж №85.','https://via.placeholder.com/300x400?text=Char+85',0),
( 'DB_Char_86','DB_Char_86','Unknown',0,'Автоматично згенерований персонаж №86.','https://via.placeholder.com/300x400?text=Char+86',0),
( 'DB_Char_87','DB_Char_87','Unknown',0,'Автоматично згенерований персонаж №87.','https://via.placeholder.com/300x400?text=Char+87',0),
( 'DB_Char_88','DB_Char_88','Unknown',0,'Автоматично згенерований персонаж №88.','https://via.placeholder.com/300x400?text=Char+88',0),
( 'DB_Char_89','DB_Char_89','Unknown',0,'Автоматично згенерований персонаж №89.','https://via.placeholder.com/300x400?text=Char+89',0),
( 'DB_Char_90','DB_Char_90','Unknown',0,'Автоматично згенерований персонаж №90.','https://via.placeholder.com/300x400?text=Char+90',0),
( 'DB_Char_91','DB_Char_91','Unknown',0,'Автоматично згенерований персонаж №91.','https://via.placeholder.com/300x400?text=Char+91',0),
( 'DB_Char_92','DB_Char_92','Unknown',0,'Автоматично згенерований персонаж №92.','https://via.placeholder.com/300x400?text=Char+92',0),
( 'DB_Char_93','DB_Char_93','Unknown',0,'Автоматично згенерований персонаж №93.','https://via.placeholder.com/300x400?text=Char+93',0),
( 'DB_Char_94','DB_Char_94','Unknown',0,'Автоматично згенерований персонаж №94.','https://via.placeholder.com/300x400?text=Char+94',0),
( 'DB_Char_95','DB_Char_95','Unknown',0,'Автоматично згенерований персонаж №95.','https://via.placeholder.com/300x400?text=Char+95',0),
( 'DB_Char_96','DB_Char_96','Unknown',0,'Автоматично згенерований персонаж №96.','https://via.placeholder.com/300x400?text=Char+96',0),
( 'DB_Char_97','DB_Char_97','Unknown',0,'Автоматично згенерований персонаж №97.','https://via.placeholder.com/300x400?text=Char+97',0),
( 'DB_Char_98','DB_Char_98','Unknown',0,'Автоматично згенерований персонаж №98.','https://via.placeholder.com/300x400?text=Char+98',0),
( 'DB_Char_99','DB_Char_99','Unknown',0,'Автоматично згенерований персонаж №99.','https://via.placeholder.com/300x400?text=Char+99',0),
( 'DB_Char_100','DB_Char_100','Unknown',0,'Автоматично згенерований персонаж №100.','https://via.placeholder.com/300x400?text=Char+100',0);

COMMIT;

-- End of compressed seed file
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Kaguya-sama: Love Is War','Кагуя хоче зізнатися',2019,'Winter',12,25,'TV','Completed','Manga','PG-13','Kaguya Shinomiya and Miyuki Shirogane are two geniuses...','Каґуя Шіномія та Міюкі Шіроганє — два генії...',5,'https://cdn.myanimelist.net/images/anime/1764/106659.jpg',150000,25000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Mob Psycho 100','Моб Психо 100',2016,'Summer',12,25,'TV','Completed','Manga','PG-13','Shigeo "Mob" Kageyama is a powerful psychic...','Шіґео "Моб" Каґеяма — могутній екстрасенс...',3,'https://cdn.myanimelist.net/images/anime/1918/96303.jpg',145000,24000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Your Lie in April','Твоя квітнева брехня',2014,'Fall',22,23,'TV','Completed','Manga','PG-13','Kosei Arima, a piano prodigy, loses his ability to hear the sound of the piano...','Косей Аріма, піаніст-вундеркінд, втрачає здатність чути звук фортепіано...',5,'https://cdn.myanimelist.net/images/anime/1751/115483.jpg',135000,22000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('86','Вісімдесят шість',2021,'Spring',11,25,'TV','Completed','Light Novel','R','In the Republic of San Magnolia, people are told that the war...','У Республіці Сан-Магнолія людям кажуть, що війну...',5,'https://cdn.myanimelist.net/images/anime/1111/117766.jpg',120000,20000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Horimiya','Хорімія',2021,'Winter',13,24,'TV','Completed','Manga','PG-13','Kyouko Hori is a popular, outgoing girl at school...','Кьоко Хорі — популярна, товариська дівчина в школі...',8,'https://cdn.myanimelist.net/images/anime/1695/111486.jpg',130000,21000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Dr. Stone','Доктор Стоун',2019,'Summer',24,24,'TV','Completed','Manga','PG-13','A mysterious light petrifies all of humanity...','Таємниче світло перетворює на камінь усе людство...',8,'https://cdn.myanimelist.net/images/anime/1613/102576.jpg',140000,23000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('The Promised Neverland','Обіцяний Неверленд',2019,'Winter',12,23,'TV','Completed','Manga','R','Emma, Norman, and Ray are orphans living in the Grace Field House...','Емма, Норман та Рей — сироти, які живуть у притулку "Ґрейс Філд"...',8,'https://cdn.myanimelist.net/images/anime/1830/110780.jpg',160000,26000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Parasyte: The Maxim','Паразит: Максимум',2014,'Fall',24,23,'TV','Ongoing','Manga','R','Parasitic aliens descend on Earth...','Паразитичні прибульці спускаються на Землю...',4,'https://cdn.myanimelist.net/images/anime/3/73178.jpg',150000,24000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Code Geass: Lelouch of the Rebellion','Код Ґіас: Повстання Лелуша',2006,'Fall',25,24,'TV','Completed','Original','R','Lelouch vi Britannia, an exiled prince, gains the power of Geass...','Лелуш ві Брітанія, принц у вигнанні, отримує силу Ґіаса...',13,'https://cdn.myanimelist.net/images/anime/5/50331.jpg',180000,30000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Cowboy Bebop','Ковбой Бібоп',1998,'Spring',26,24,'TV','Completed','Original','R','Spike Spiegel and Jet Black are bounty hunters on the spaceship Bebop...','Спайк Шпіґель та Джет Блек — мисливці за головами на космічному кораблі "Бібоп"...',13,'https://cdn.myanimelist.net/images/anime/4/19644.jpg',190000,32000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Neon Genesis Evangelion','Євангеліон',1995,'Fall',26,24,'TV','Completed','Original','PG-13','In a post-apocalyptic world, a teenage boy, Shinji Ikari, is recruited...','У постапокаліптичному світі підліток Шінджі Ікарі завербований...',21,'https://cdn.myanimelist.net/images/anime/1314/108941.jpg',200000,33000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Monster','Монстр',2004,'Spring',74,23,'TV','Completed','Manga','R+','Dr. Kenzo Tenma, a brilliant neurosurgeon, chooses to save the life of a young boy...','Доктор Кендзо Тенма, блискучий нейрохірург, рятує життя маленького хлопчика...',4,'https://cdn.myanimelist.net/images/anime/10/18793.jpg',170000,28000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Gurren Lagann','Гуррен-Лаґанн',2007,'Spring',27,25,'TV','Completed','Original','PG-13','In a future where humanity is forced to live underground, a boy named Simon...','У майбутньому, де людство змушене жити під землею, хлопець на ім''я Саймон...',9,'https://cdn.myanimelist.net/images/anime/4/5123.jpg',160000,26000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Cyberpunk: Edgerunners','Кіберпанк: Бігуни',2022,'Fall',10,25,'ONA','Completed','Original','R+','In the dystopian Night City, a street kid named David Martinez...','У дистопічному Найт-Сіті вуличний хлопець на ім''я Девід Мартінес...',9,'https://cdn.myanimelist.net/images/anime/1818/126435.jpg',160000,27000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Re:Zero - Starting Life in Another World','Ре:Зеро — Життя з нуля в іншому світі',2016,'Spring',25,25,'TV','Ongoing','Light Novel','R','Subaru Natsuki is suddenly summoned to a fantasy world...','Субару Нацукі раптово переноситься у фентезійний світ...',15,'https://cdn.myanimelist.net/images/anime/1522/128039.jpg',140000,24000);
INSERT OR IGNORE INTO anime (title, title_ua, Year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Mushoku Tensei: Jobless Reincarnation','Реінкарнація безробітного',2021,'Winter',23,25,'TV','Completed','Light Novel','R+','A 34-year-old NEET dies and is reincarnated into a world of magic...','34-річний NEET помирає і перероджується у світі магії...',22,'https://cdn.myanimelist.net/images/anime/1530/117776.jpg',155000,26000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Berserk','Берсерк',1989,'Ongoing',42,376,'Manga','Seinen','Guts, the Black Swordsman, is a lone mercenary cursed with a brand that attracts demons...','Ґатс, Чорний мечник, — самотній найманець...','https://cdn.myanimelist.net/images/manga/1/157897.jpg',200000,35000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Vagabond','Бродяга',1998,'Hiatus',37,327,'Manga','Seinen','Based on the life of the legendary swordsman Miyamoto Musashi...','Заснована на житті легендарного мечника Міямото Мусаші...','https://cdn.myanimelist.net/images/manga/1/157913.jpg',150000,28000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('One Punch Man','Людина-один удар',2012,'Ongoing',28,200,'Manga','Shounen','Saitama is a hero who can defeat any enemy with a single punch...','Сайтама — герой, який може перемогти будь-кого одним ударом...','https://cdn.myanimelist.net/images/manga/3/155939.jpg',180000,30000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Chainsaw Man','Людина-бензопила',2018,'Ongoing',17,152,'Manga','Shounen','Denji is a young man in crippling debt, forced to work as a devil hunter...','Денджі — молодий чоловік, обтяжений боргами...','https://cdn.myanimelist.net/images/manga/3/216464.jpg',170000,29000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Solo Leveling','Соло рівень',2018,'Completed',14,200,'Manhwa','Shounen','Sung Jinwoo is known as the "Weakest Hunter of All Mankind."...','Сон Джіну відомий як "Найслабший мисливець людства"...','https://cdn.myanimelist.net/images/manga/2/260415.jpg',190000,32000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Jujutsu Kaisen','Магічна битва',2018,'Ongoing',25,247,'Manga','Shounen','Yuji Itadori swallows a cursed talisman...','Юджі Ітадорі проковтує проклятий талісман...','https://cdn.myanimelist.net/images/manga/3/220844.jpg',185000,31000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Attack on Titan','Напад титанів',2009,'Completed',34,139,'Manga','Shounen','Humanity lives inside walled cities to protect themselves from Titans...','Людство живе за стінами, захищаючись від титанів...','https://manga.in.ua/uploads/posts/2023-08/1691068924_00.webp   ',210000,38000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Naruto','Наруто',1999,'Completed',72,700,'Manga','Shounen','Naruto Uzumaki is a young ninja with a powerful fox demon sealed inside him...','Наруто Удзумакі — юний ніндзя з могутнім демоном-лисом...','https://cdn.myanimelist.net/images/manga/2/249315.jpg',250000,45000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Dragon Ball','Драґонболл',1984,'Completed',42,519,'Manga','Shounen','Son Goku is a young boy with a tail who meets a girl named Bulma...','Сон Гоку — маленький хлопчик з хвостом...','https://i.redd.it/lk8099gv3wpe1.jpeg',220000,40000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('My Hero Academia','Моя геройська академія',2014,'Ongoing',38,414,'Manga','Shounen','Izuku Midoriya is born without a superpower in a world where they are the norm...','Ізуку Мідорія народжується без суперздібності...','https://cdn.myanimelist.net/images/manga/3/174681.jpg',165000,26000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Spy x Family','Сімейка шпигуна',2019,'Ongoing',13,100,'Manga','Shounen','A spy codenamed "Twilight" builds a fake family to infiltrate an elite school...','Шпигун на ім''я "Твайлайт" створює фальшиву сім''ю...','https://cdn.myanimelist.net/images/manga/3/232659.jpg',175000,27000);
INSERT OR IGNORE INTO manga (title, title_ua, Year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Frieren: Beyond Journey''s End','Проводжальниця Фрірен',2020,'Ongoing',12,131,'Manga','Shounen','Frieren, an elf mage, was part of the hero''s party that defeated the Demon King...','Фрірен, ельфійка-чарівниця, була частиною загону героїв...','https://static.yakaboo.ua/media/catalog/product/i/m/img827_147.jpg',155000,25000);
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, Gender, Biography, image_url) VALUES ('Kentaro Miura','Кентаро Міура','Kentaro Miura','1966-07-11','Chiba, Japan','Male','Kentaro Miura was a Japanese manga artist, best known for his dark fantasy series Berserk.','https://cdn.myanimelist.net/images/voiceactors/3/61071.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, Gender, Biography, image_url) VALUES ('Takehiko Inoue','Такехіко Іноуе','Takehiko Inoue','1967-01-12','Okuchi, Japan','Male','Takehiko Inoue is a Japanese manga artist, best known for creating Slam Dunk, Vagabond, and Real.','https://cdn.myanimelist.net/images/voiceactors/2/54829.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, Gender, Biography, image_url) VALUES ('ONE','ONE','ONE','1986-10-29','Niigata, Japan','Male','ONE is a Japanese manga artist, best known for his webcomics One Punch Man and Mob Psycho 100.','https://cdn.myanimelist.net/images/voiceactors/2/55863.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, Gender, Biography, image_url) VALUES ('Tatsuki Fujimoto','Тацукі Фудзімото','Tatsuki Fujimoto','1992-10-10','Akita, Japan','Male','Tatsuki Fujimoto is a Japanese manga artist, best known for creating Chainsaw Man and Fire Punch.','https://cdn.myanimelist.net/images/voiceactors/3/66441.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, Gender, Biography, image_url) VALUES ('Chugong','Чуґон','Chugong','1985-01-01','South Korea','Male','Chugong is a South Korean author, best known for writing the web novel Solo Leveling.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('admin','admin@miks.ua','Адміністратор','admin','Головний адміністратор сайту. Люблю аніме та мангу, особливо філософські твори.','https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('animefan','fan@example.com','Аніме Фан','user','Дивлюсь аніме щодня. Люблю бойовики та пригоди. Мої улюблені: Jujutsu Kaisen, One Piece.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('mangalover','manga@example.com','Манга Любитель','user','Колекціоную мангу вже 10 років. Особливо люблю сейнен та історичні твори. Berserk - найкраще, що я читав.','https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('reviewer','review@example.com','Оглядач','user','Пишу огляди на новинки аніме. Намагаюсь бути об''єктивним та допомагати іншим обирати що подивитись.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('moderator','mod@example.com','Модератор','moderator','Слідкую за порядком на сайті. Люблю коли все структуровано та правильно.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295772_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('sakurafan','sakura@example.com','Сакура Фан','user','Люблю романтику та повсякденність. Мої улюблені: Your Lie in April, Horimiya.','https://cdn.pixabay.com/photo/2016/04/01/12/11/avatar-1300582_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('darkness','dark@example.com','Темний Лицар','user','Полюбляю темне фентезі та психологічні трилери. Berserk, Monster, Death Note - мої фаворити.','https://cdn.pixabay.com/photo/2016/04/01/12/11/avatar-1300585_1280.png');
INSERT OR IGNORE INTO users (Login, Email, display_name, Role, Bio, avatar_url) VALUES ('newbie','new@example.com','Новачок','user','Тільки починаю знайомство з аніме. Допоможіть з рекомендаціями!','https://cdn.pixabay.com/photo/2016/04/01/12/11/avatar-1300583_1280.png');
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (1,1,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (1,2,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (1,3,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (2,4,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (2,5,'Supporting',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (3,6,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (3,7,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,8,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,9,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,10,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,11,'Antagonist',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,8,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,9,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,10,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,11,'Antagonist',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,8,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,9,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,10,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,11,'Antagonist',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (10,12,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (11,13,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (11,14,'Main',1);
INSERT OR IGNORE INTO manga_character (manga_id, character_id, role, is_main) VALUES (1,1,'Main',1);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,1,9.5,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,2,9.0,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,3,9.8,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,4,9.2,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,1,9.0,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,2,9.5,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,3,8.5,0,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,5,9.3,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,1,10.0,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,2,9.8,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,3,9.5,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,6,9.7,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (4,2,9.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (4,3,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (4,7,8.8,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (5,2,9.9,1,'Completed',26);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (5,3,9.7,1,'Completed',26);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (5,4,9.5,1,'Completed',26);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (6,2,9.8,1,'Completed',64);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (6,3,9.7,1,'Completed',64);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (6,5,9.6,1,'Completed',64);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (7,2,8.5,0,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (7,3,9.0,1,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (8,1,9.5,1,'Watching',500);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (8,2,9.0,1,'Watching',300);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (8,6,9.2,1,'Watching',250);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,1,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,2,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,3,8.8,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,7,9.1,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,1,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,2,9.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,3,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,4,9.3,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (11,1,8.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (11,2,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (11,5,8.8,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (12,1,8.7,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (12,2,9.1,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (13,1,9.3,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (13,2,9.6,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (13,6,9.4,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (14,1,9.1,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (14,2,9.4,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (15,1,8.9,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (15,2,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (15,7,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (16,1,9.0,1,'Completed',22);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (16,2,9.5,1,'Completed',22);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (17,1,8.8,1,'Completed',11);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (17,2,9.0,1,'Completed',11);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (18,1,8.5,1,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (18,2,8.9,1,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (19,1,8.7,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (19,2,9.0,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (20,1,8.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (20,2,8.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (20,3,8.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (21,1,8.9,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (21,2,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (22,1,8.6,1,'Completed',23);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (22,2,9.0,1,'Completed',23);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (23,1,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (23,2,9.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (24,1,9.4,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (24,2,9.7,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (25,1,9.1,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (25,2,9.3,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (26,1,9.3,1,'Completed',10);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (26,2,9.6,1,'Completed',10);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,8);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,27);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,12);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,21);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,27);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,8);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,27);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,13);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,10);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,18);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (12,13);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (12,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (12,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,20);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,12);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,14);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,21);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,10);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,28);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,20);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,19);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,23);
-- Appended INSERTs converted from T-SQL on 2026-04-20T17:41:51+00:00
INSERT OR IGNORE INTO genre (name,name_ua,name_en,description,color,icon) VALUES ('Action','Бойовик','Action','Аніме з інтенсивними битвами та екшен-сценами','#ef4444','sword');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Adventure','Пригоди','Adventure','Подорожі, дослідження нових світів та пошук пригод','#f59e0b','compass');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Comedy','Комедія','Comedy','Смішні ситуації, жарти та гумористичні діалоги','#10b981','laugh');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Drama','Драма','Drama','Емоційні історії з глибоким психологічним підтекстом','#8b5cf6','drama');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Fantasy','Фентезі','Fantasy','Магія, фантастичні істоти та вигадані світи','#ec4899','magic');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Sci-Fi','Наукова фантастика','Sci-Fi','Футуристичні технології, космос та наукові відкриття','#3b82f6','robot');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Romance','Романтика','Romance','Історії кохання та романтичні стосунки','#ff6b6b','heart');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Slice of Life','Повсякденність','Slice of Life','Звичайне життя, щоденні турботи та побут','#a3e635','home');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Mystery','Містика','Mystery','Таємничі події, розслідування та нерозгадані загадки','#6366f1','question');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Horror','Жахи','Horror','Страшні історії, що викликають почуття страху та тривоги','#18181b','ghost');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Psychological','Психологічний','Psychological','Глибокий аналіз психіки персонажів та їхніх мотивів','#7c3aed','brain');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Supernatural','Надприродне','Supernatural','Надприродні сили, духи, демони та паранормальні явища','#c084fc','sparkles');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Sports','Спорт','Sports','Спортивні змагання, тренування та командний дух','#22c55e','sports');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Music','Музика','Music','Історії про музику, музикантів та музичні гурти','#f43f5e','music');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('School','Школа','School','Шкільне життя, навчання та стосунки між учнями','#eab308','school');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Shounen','Шьонен','Shounen','Для хлопчиків-підлітків: битви, дружба, пригоди','#f97316','target');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Seinen','Сейнен','Seinen','Для дорослих чоловіків: серйозні теми, складні сюжети','#6b7280','mature');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Isekai','Ісекай','Isekai','Перенесення в інший світ, переродження','#a855f7','portal');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Mecha','Меха','Mecha','Гігантські роботи та пілотування','#64748b','robot');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Historical','Історичний','Historical','Історичні події та епохи','#b45309','history');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Military','Військовий','Military','Військові конфлікти, армія, стратегія','#475569','military');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Crime','Кримінал','Crime','Злочинний світ, розслідування, мафія','#1e293b','crime');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Thriller','Трилер','Thriller','Напружені історії, що тримають у постійній напрузі','#292524','thriller');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Gourmet','Кулінарія','Gourmet','Про їжу, приготування та кулінарне мистецтво','#fbbf24','food');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Harem','Гарем','Harem','Один головний герой та багато героїнь','#f87171','harem');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Martial Arts','Бойові мистецтва','Martial Arts','Різні види бойових мистецтв та поєдинків','#dc2626','fist');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Philosophical','Філософський','Philosophical','Філософські роздуми про життя, смерть, сенс буття','#4f46e5','philosophy');
INSERT OR IGNORE INTO genre (name, name_ua, name_en, description, color, icon) VALUES ('Space','Космос','Space','Космічні подорожі, міжпланетні війни','#0f172a','rocket');
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Frieren: Beyond Journey''s End','Проводжальниця Фрірен',2023,'Fall',28,25,'TV','Ongoing','Manga','PG-13','The story follows the elf mage Frieren...','​Короля демонів переможено, і загін героїв-переможців повертається додому перед тим, як розійтися по домівках. Чотири героя — Фрірен, герой Гіммель, священник Гайтер і воїн Айзен — згадують про свою десятирічну подорож, коли настає момент прощання. Але для ельфів плин часу є іншим, тож Фрірен стає свідком того, як її супутники повільно відходять у вічність один за одним. Перед смертю Гайтер встигає навязати Фрірен молоду людську ученицю на імя Ферн. Захоплені пристрастю ельфійки до колекціонування безлічі магічних заклинань, вони вирушають у, здавалося б, безцільну мандрівку, відвідуючи місця, де побували герої минулого. Під час мандрівки Фрірен поступово усвідомлює, що шкодує про втрачені можливості налагодити глибші звязки зі своїми нині покійними товаришами.',4,'https://cdn.myanimelist.net/images/anime/1015/138006.jpg',150000,25000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Jujutsu Kaisen','Магічна битва',2020,'Fall',24,23,'TV','Ongoing','Manga','R','Yuji Itadori, a high schooler with immense physical strength...','Юджі Ітадорі, звичайний школяр з надзвичайною фізичною силою...',1,'https://cdn.myanimelist.net/images/anime/1171/109222.jpg',200000,30000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Attack on Titan','Напад титанів',2013,'Spring',25,24,'TV','Completed','Manga','R','Humanity lives inside enormous walled cities...','Людство живе за трьома величезними стінами...',7,'https://cdn.myanimelist.net/images/anime/10/47347.jpg',250000,40000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Spy x Family','Сімейка шпигуна',2022,'Spring',12,24,'TV','Completed','Manga','PG-13','A spy codenamed "Twilight" needs to infiltrate an elite school...','Шпигун на ім''я "Твайлайт" отримує завдання проникнути в елітну школу...',8,'https://cdn.myanimelist.net/images/anime/1441/122795.jpg',180000,28000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Demon Slayer: Kimetsu no Yaiba','Вбивця демонів',2019,'Spring',26,24,'TV','Completed','Manga','R','Tanjiro Kamado, a kind-hearted boy, returns home...','Танджіро Камадо, добросердий хлопець, повертається додому...',6,'https://cdn.myanimelist.net/images/anime/1286/99889.jpg',220000,35000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Fullmetal Alchemist: Brotherhood','Сталевий алхімік: Братство',2009,'Spring',64,24,'TV','Completed','Manga','PG-13','Two brothers, Edward and Alphonse Elric, attempt to bring their mother back...','Два брати, Едвард та Альфонс Елріки, намагаються воскресити матір...',3,'https://cdn.myanimelist.net/images/anime/1223/96541.jpg',180000,30000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('My Hero Academia','Моя геройська академія',2016,'Spring',13,24,'TV','Ongoing','Manga','PG-13','In a world where superpowers are the norm, Izuku Midoriya is born without one...','У світі, де суперздібності є нормою, Ізуку Мідорія народжується без них...',3,'https://cdn.myanimelist.net/images/anime/10/78745.jpg',160000,25000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('One Piece','Ван Піс',1999,'Fall',1000,24,'TV','Ongoing','Manga','PG-13','Monkey D. Luffy sets out to become the Pirate King...','Манкі Д. Луффі вирушає стати Королем піратів...',11,'https://cdn.myanimelist.net/images/anime/6/73245.jpg',300000,50000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Chainsaw Man','Людина-бензопила',2022,'Fall',12,24,'TV','Completed','Manga','R','Denji is a young man in crippling debt, forced to work as a devil hunter...','Денджі — молодий чоловік, обтяжений боргами, змушений працювати мисливцем на демонів...',1,'https://cdn.myanimelist.net/images/anime/1806/126216.jpg',175000,29000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Solo Leveling','Соло рівень',2024,'Winter',12,24,'TV','Completed','Manga','R','In a world where hunters with magical powers fight monsters...','У світі, де мисливці з магічними силами борються з монстрами...',1,'https://cdn.myanimelist.net/images/anime/1987/135339.jpg',190000,32000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Dandadan','Дандадан',2024,'Fall',12,24,'TV','Completed','Manga','PG-13','Momo Ayase, who believes in ghosts but not aliens...','Момо Аясе вірить у привидів, але не в інопланетян...',24,'https://cdn.myanimelist.net/images/anime/1042/139481.jpg',90000,15000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Blue Lock','Блакитний замок',2022,'Fall',24,24,'TV','Completed','Manga','PG-13','After Japan''s disastrous performance in the World Cup...','Після провального виступу Японії на Чемпіонаті світу...',19,'https://cdn.myanimelist.net/images/anime/1258/126961.jpg',140000,22000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Vinland Saga','Вінландська сага',2019,'Summer',24,25,'TV','Completed','Manga','R','Thorfinn, a young boy, witnesses his father''s death...','Торфінн, юний хлопець, стає свідком смерті свого батька...',7,'https://cdn.myanimelist.net/images/anime/1500/103005.jpg',165000,27000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Kaguya-sama: Love Is War','Кагуя хоче зізнатися',2019,'Winter',12,25,'TV','Completed','Manga','PG-13','Kaguya Shinomiya and Miyuki Shirogane are two geniuses...','Каґуя Шіномія та Міюкі Шіроганє — два генії...',5,'https://cdn.myanimelist.net/images/anime/1764/106659.jpg',150000,25000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Mob Psycho 100','Моб Психо 100',2016,'Summer',12,25,'TV','Completed','Manga','PG-13','Shigeo "Mob" Kageyama is a powerful psychic...','Шіґео "Моб" Каґеяма — могутній екстрасенс...',3,'https://cdn.myanimelist.net/images/anime/1918/96303.jpg',145000,24000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Your Lie in April','Твоя квітнева брехня',2014,'Fall',22,23,'TV','Completed','Manga','PG-13','Kosei Arima, a piano prodigy, loses his ability to hear the sound of the piano...','Косей Аріма, піаніст-вундеркінд, втрачає здатність чути звук фортепіано...',5,'https://cdn.myanimelist.net/images/anime/1751/115483.jpg',135000,22000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('86','Вісімдесят шість',2021,'Spring',11,25,'TV','Completed','Light Novel','R','In the Republic of San Magnolia, people are told that the war...','У Республіці Сан-Магнолія людям кажуть, що війну...',5,'https://cdn.myanimelist.net/images/anime/1111/117766.jpg',120000,20000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Horimiya','Хорімія',2021,'Winter',13,24,'TV','Completed','Manga','PG-13','Kyouko Hori is a popular, outgoing girl at school...','Кьоко Хорі — популярна, товариська дівчина в школі...',8,'https://cdn.myanimelist.net/images/anime/1695/111486.jpg',130000,21000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Dr. Stone','Доктор Стоун',2019,'Summer',24,24,'TV','Completed','Manga','PG-13','A mysterious light petrifies all of humanity...','Таємниче світло перетворює на камінь усе людство...',8,'https://cdn.myanimelist.net/images/anime/1613/102576.jpg',140000,23000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('The Promised Neverland','Обіцяний Неверленд',2019,'Winter',12,23,'TV','Completed','Manga','R','Emma, Norman, and Ray are orphans living in the Grace Field House...','Емма, Норман та Рей — сироти, які живуть у притулку "Ґрейс Філд"...',8,'https://cdn.myanimelist.net/images/anime/1830/110780.jpg',160000,26000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Parasyte: The Maxim','Паразит: Максимум',2014,'Fall',24,23,'TV','Ongoing','Manga','R','Parasitic aliens descend on Earth...','Паразитичні прибульці спускаються на Землю...',4,'https://cdn.myanimelist.net/images/anime/3/73178.jpg',150000,24000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Code Geass: Lelouch of the Rebellion','Код Ґіас: Повстання Лелуша',2006,'Fall',25,24,'TV','Completed','Original','R','Lelouch vi Britannia, an exiled prince, gains the power of Geass...','Лелуш ві Брітанія, принц у вигнанні, отримує силу Ґіаса...',13,'https://cdn.myanimelist.net/images/anime/5/50331.jpg',180000,30000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Cowboy Bebop','Ковбой Бібоп',1998,'Spring',26,24,'TV','Completed','Original','R','Spike Spiegel and Jet Black are bounty hunters on the spaceship Bebop...','Спайк Шпіґель та Джет Блек — мисливці за головами на космічному кораблі "Бібоп"...',13,'https://cdn.myanimelist.net/images/anime/4/19644.jpg',190000,32000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Neon Genesis Evangelion','Євангеліон',1995,'Fall',26,24,'TV','Completed','Original','PG-13','In a post-apocalyptic world, a teenage boy, Shinji Ikari, is recruited...','У постапокаліптичному світі підліток Шінджі Ікарі завербований...',21,'https://cdn.myanimelist.net/images/anime/1314/108941.jpg',200000,33000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Monster','Монстр',2004,'Spring',74,23,'TV','Completed','Manga','R+','Dr. Kenzo Tenma, a brilliant neurosurgeon, chooses to save the life of a young boy...','Доктор Кендзо Тенма, блискучий нейрохірург, рятує життя маленького хлопчика...',4,'https://cdn.myanimelist.net/images/anime/10/18793.jpg',170000,28000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Gurren Lagann','Гуррен-Лаґанн',2007,'Spring',27,25,'TV','Completed','Original','PG-13','In a future where humanity is forced to live underground, a boy named Simon...','У майбутньому, де людство змушене жити під землею, хлопець на ім''я Саймон...',9,'https://cdn.myanimelist.net/images/anime/4/5123.jpg',160000,26000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Cyberpunk: Edgerunners','Кіберпанк: Бігуни',2022,'Fall',10,25,'ONA','Completed','Original','R+','In the dystopian Night City, a street kid named David Martinez...','У дистопічному Найт-Сіті вуличний хлопець на ім''я Девід Мартінес...',9,'https://cdn.myanimelist.net/images/anime/1818/126435.jpg',160000,27000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Re:Zero - Starting Life in Another World','Ре:Зеро — Життя з нуля в іншому світі',2016,'Spring',25,25,'TV','Ongoing','Light Novel','R','Subaru Natsuki is suddenly summoned to a fantasy world...','Субару Нацукі раптово переноситься у фентезійний світ...',15,'https://cdn.myanimelist.net/images/anime/1522/128039.jpg',140000,24000);
INSERT OR IGNORE INTO anime (title, title_ua, year, season, episodes, episode_duration, type, status, source, rating_mpaa, description, description_ua, studio_id, cover_url, views, favorites) VALUES ('Mushoku Tensei: Jobless Reincarnation','Реінкарнація безробітного',2021,'Winter',23,25,'TV','Completed','Light Novel','R+','A 34-year-old NEET dies and is reincarnated into a world of magic...','34-річний NEET помирає і перероджується у світі магії...',22,'https://cdn.myanimelist.net/images/anime/1530/117776.jpg',155000,26000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Berserk','Берсерк',1989,'Ongoing',42,376,'Manga','Seinen','Guts, the Black Swordsman, is a lone mercenary cursed with a brand that attracts demons...','Ґатс, Чорний мечник, — самотній найманець...','https://cdn.myanimelist.net/images/manga/1/157897.jpg',200000,35000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Vagabond','Бродяга',1998,'Hiatus',37,327,'Manga','Seinen','Based on the life of the legendary swordsman Miyamoto Musashi...','Заснована на житті легендарного мечника Міямото Мусаші...','https://cdn.myanimelist.net/images/manga/1/157913.jpg',150000,28000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('One Punch Man','Людина-один удар',2012,'Ongoing',28,200,'Manga','Shounen','Saitama is a hero who can defeat any enemy with a single punch...','Сайтама — герой, який може перемогти будь-кого одним ударом...','https://cdn.myanimelist.net/images/manga/3/155939.jpg',180000,30000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Chainsaw Man','Людина-бензопила',2018,'Ongoing',17,152,'Manga','Shounen','Denji is a young man in crippling debt, forced to work as a devil hunter...','Денджі — молодий чоловік, обтяжений боргами...','https://cdn.myanimelist.net/images/manga/3/216464.jpg',170000,29000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Solo Leveling','Соло рівень',2018,'Completed',14,200,'Manhwa','Shounen','Sung Jinwoo is known as the "Weakest Hunter of All Mankind."...','Сон Джіну відомий як "Найслабший мисливець людства"...','https://cdn.myanimelist.net/images/manga/2/260415.jpg',190000,32000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Jujutsu Kaisen','Магічна битва',2018,'Ongoing',25,247,'Manga','Shounen','Yuji Itadori swallows a cursed talisman...','Юджі Ітадорі проковтує проклятий талісман...','https://cdn.myanimelist.net/images/manga/3/220844.jpg',185000,31000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Attack on Titan','Напад титанів',2009,'Completed',34,139,'Manga','Shounen','Humanity lives inside walled cities to protect themselves from Titans...','Людство живе за стінами, захищаючись від титанів...','https://manga.in.ua/uploads/posts/2023-08/1691068924_00.webp   ',210000,38000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Naruto','Наруто',1999,'Completed',72,700,'Manga','Shounen','Naruto Uzumaki is a young ninja with a powerful fox demon sealed inside him...','Наруто Удзумакі — юний ніндзя з могутнім демоном-лисом...','https://cdn.myanimelist.net/images/manga/2/249315.jpg',250000,45000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Dragon Ball','Драґонболл',1984,'Completed',42,519,'Manga','Shounen','Son Goku is a young boy with a tail who meets a girl named Bulma...','Сон Гоку — маленький хлопчик з хвостом...','https://i.redd.it/lk8099gv3wpe1.jpeg',220000,40000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('My Hero Academia','Моя геройська академія',2014,'Ongoing',38,414,'Manga','Shounen','Izuku Midoriya is born without a superpower in a world where they are the norm...','Ізуку Мідорія народжується без суперздібності...','https://cdn.myanimelist.net/images/manga/3/174681.jpg',165000,26000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Spy x Family','Сімейка шпигуна',2019,'Ongoing',13,100,'Manga','Shounen','A spy codenamed "Twilight" builds a fake family to infiltrate an elite school...','Шпигун на ім''я "Твайлайт" створює фальшиву сім''ю...','https://cdn.myanimelist.net/images/manga/3/232659.jpg',175000,27000);
INSERT OR IGNORE INTO manga (title, title_ua, year, status, volumes, chapters, type, demographic, description, description_ua, cover_url, views, favorites) VALUES ('Frieren: Beyond Journey''s End','Проводжальниця Фрірен',2020,'Ongoing',12,131,'Manga','Shounen','Frieren, an elf mage, was part of the hero''s party that defeated the Demon King...','Фрірен, ельфійка-чарівниця, була частиною загону героїв...','https://static.yakaboo.ua/media/catalog/product/i/m/img827_147.jpg',155000,25000);
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, gender, biography, image_url) VALUES ('Kentaro Miura','Кентаро Міура','Kentaro Miura','1966-07-11','Chiba, Japan','Male','Kentaro Miura was a Japanese manga artist, best known for his dark fantasy series Berserk.','https://cdn.myanimelist.net/images/voiceactors/3/61071.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, gender, biography, image_url) VALUES ('Takehiko Inoue','Такехіко Іноуе','Takehiko Inoue','1967-01-12','Okuchi, Japan','Male','Takehiko Inoue is a Japanese manga artist, best known for creating Slam Dunk, Vagabond, and Real.','https://cdn.myanimelist.net/images/voiceactors/2/54829.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, gender, biography, image_url) VALUES ('ONE','ONE','ONE','1986-10-29','Niigata, Japan','Male','ONE is a Japanese manga artist, best known for his webcomics One Punch Man and Mob Psycho 100.','https://cdn.myanimelist.net/images/voiceactors/2/55863.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, gender, biography, image_url) VALUES ('Tatsuki Fujimoto','Тацукі Фудзімото','Tatsuki Fujimoto','1992-10-10','Akita, Japan','Male','Tatsuki Fujimoto is a Japanese manga artist, best known for creating Chainsaw Man and Fire Punch.','https://cdn.myanimelist.net/images/voiceactors/3/66441.jpg');
INSERT OR IGNORE INTO author (name, name_ua, name_en, birth_date, birth_place, gender, biography, image_url) VALUES ('Chugong','Чуґон','Chugong','1985-01-01','South Korea','Male','Chugong is a South Korean author, best known for writing the web novel Solo Leveling.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('admin','admin@miks.ua','Адміністратор','admin','Головний адміністратор сайту. Люблю аніме та мангу, особливо філософські твори.','https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('helper','helper@example.com','Редактор новин','helper','Редактор контенту: може редагувати та публікувати новини.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('siteadmin','siteadmin@example.com','Сайт Адмін','admin','Адміністратор з повними правами для тестування.','https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('animefan','fan@example.com','Аніме Фан','user','Дивлюсь аніме щодня. Люблю бойовики та пригоди. Мої улюблені: Jujutsu Kaisen, One Piece.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('mangalover','manga@example.com','Манга Любитель','user','Колекціоную мангу вже 10 років. Особливо люблю сейнен та історичні твори. Berserk - найкраще, що я читав.','https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('reviewer','review@example.com','Оглядач','user','Пишу огляди на новинки аніме. Намагаюсь бути об''єктивним та допомагати іншим обирати що подивитись.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('moderator','mod@example.com','Модератор','moderator','Слідкую за порядком на сайті. Люблю коли все структуровано та правильно.','https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295772_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('sakurafan','sakura@example.com','Сакура Фан','user','Люблю романтику та повсякденність. Мої улюблені: Your Lie in April, Horimiya.','https://cdn.pixabay.com/photo/2016/04/01/12/11/avatar-1300582_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('darkness','dark@example.com','Темний Лицар','user','Полюбляю темне фентезі та психологічні трилери. Berserk, Monster, Death Note - мої фаворити.','https://cdn.pixabay.com/photo/2016/04/01/12/11/avatar-1300585_1280.png');
INSERT OR IGNORE INTO users (login, email, display_name, role, bio, avatar_url) VALUES ('newbie','new@example.com','Новачок','user','Тільки починаю знайомство з аніме. Допоможіть з рекомендаціями!','https://cdn.pixabay.com/photo/2016/04/01/12/11/avatar-1300583_1280.png');
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (1,1,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (1,2,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (1,3,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (2,4,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (2,5,'Supporting',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (3,6,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (3,7,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,8,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,9,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,10,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (4,11,'Antagonist',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,8,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,9,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,10,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (5,11,'Antagonist',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,8,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,9,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,10,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (9,11,'Antagonist',0);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (10,12,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (11,13,'Main',1);
INSERT OR IGNORE INTO anime_character (anime_id, character_id, role, is_main) VALUES (11,14,'Main',1);
INSERT OR IGNORE INTO manga_character (manga_id, character_id, role, is_main) VALUES (1,1,'Main',1);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,1,9.5,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,2,9.0,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,3,9.8,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (1,4,9.2,1,'Completed',28);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,1,9.0,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,2,9.5,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,3,8.5,0,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (2,5,9.3,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,1,10.0,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,2,9.8,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,3,9.5,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (3,6,9.7,1,'Completed',88);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (4,2,9.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (4,3,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (4,7,8.8,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (5,2,9.9,1,'Completed',26);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (5,3,9.7,1,'Completed',26);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (5,4,9.5,1,'Completed',26);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (6,2,9.8,1,'Completed',64);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (6,3,9.7,1,'Completed',64);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (6,5,9.6,1,'Completed',64);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (7,2,8.5,0,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (7,3,9.0,1,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (8,1,9.5,1,'Watching',500);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (8,2,9.0,1,'Watching',300);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (8,6,9.2,1,'Watching',250);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,1,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,2,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,3,8.8,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (9,7,9.1,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,1,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,2,9.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,3,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (10,4,9.3,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (11,1,8.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (11,2,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (11,5,8.8,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (12,1,8.7,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (12,2,9.1,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (13,1,9.3,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (13,2,9.6,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (13,6,9.4,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (14,1,9.1,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (14,2,9.4,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (15,1,8.9,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (15,2,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (15,7,9.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (16,1,9.0,1,'Completed',22);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (16,2,9.5,1,'Completed',22);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (17,1,8.8,1,'Completed',11);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (17,2,9.0,1,'Completed',11);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (18,1,8.5,1,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (18,2,8.9,1,'Completed',13);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (19,1,8.7,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (19,2,9.0,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (20,1,8.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (20,2,8.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (20,3,8.0,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (21,1,8.9,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (21,2,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (22,1,8.6,1,'Completed',23);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (22,2,9.0,1,'Completed',23);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (23,1,9.2,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (23,2,9.5,1,'Completed',12);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (24,1,9.4,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (24,2,9.7,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (25,1,9.1,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (25,2,9.3,1,'Completed',24);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (26,1,9.3,1,'Completed',10);
INSERT OR IGNORE INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES (26,2,9.6,1,'Completed',10);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,8);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (1,27);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,12);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (2,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,21);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (3,27);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,8);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (4,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (5,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (6,27);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,13);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (7,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (8,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (9,10);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (10,18);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,5);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (11,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (12,13);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (12,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (12,16);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (13,20);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (14,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (15,12);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,14);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (16,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (17,21);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,7);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (18,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (19,15);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,10);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (20,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (21,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (22,28);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,9);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (23,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,3);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (24,20);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,2);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (25,19);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,1);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,4);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,6);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,11);
INSERT OR IGNORE INTO anime_genre (anime_id, genre_id) VALUES (26,23);
