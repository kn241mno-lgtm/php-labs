PRAGMA foreign_keys = ON;

-- =============================================
-- ОРИГІНАЛЬНА СТРУКТУРА (як було до переробки)
-- =============================================

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

CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER,
    user_id INTEGER,
    anime_id INTEGER,
    manga_id INTEGER,
    character_id INTEGER,
    news_id INTEGER,
    content TEXT NOT NULL,
    likes INTEGER DEFAULT 0,
    dislikes INTEGER DEFAULT 0,
    is_edited INTEGER DEFAULT 0,
    is_deleted INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES character(id) ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE
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
    PRIMARY KEY (anime_id, character_id),
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES character(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS manga_character (
    manga_id INTEGER,
    character_id INTEGER,
    role TEXT,
    is_main INTEGER DEFAULT 0,
    "order" INTEGER DEFAULT 0,
    description TEXT,
    PRIMARY KEY (manga_id, character_id),
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE,
    FOREIGN KEY (character_id) REFERENCES character(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS author (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    bio TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS manga_author (
    manga_id INTEGER,
    author_id INTEGER,
    PRIMARY KEY (manga_id, author_id),
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE
);

-- =============================================
-- ІНДЕКСИ
-- =============================================

CREATE INDEX IF NOT EXISTS idx_anime_title ON anime(title);
CREATE INDEX IF NOT EXISTS idx_anime_title_ua ON anime(title_ua);
CREATE INDEX IF NOT EXISTS idx_anime_year ON anime(year);
CREATE INDEX IF NOT EXISTS idx_anime_status ON anime(status);
CREATE INDEX IF NOT EXISTS idx_anime_type ON anime(type);
CREATE INDEX IF NOT EXISTS idx_anime_studio ON anime(studio_id);
CREATE INDEX IF NOT EXISTS idx_manga_title ON manga(title);
CREATE INDEX IF NOT EXISTS idx_manga_title_ua ON manga(title_ua);
CREATE INDEX IF NOT EXISTS idx_manga_year ON manga(year);
CREATE INDEX IF NOT EXISTS idx_manga_status ON manga(status);
CREATE INDEX IF NOT EXISTS idx_character_name ON character(name);

-- =============================================
-- ПОЧАТОК НАПОВНЕННЯ ДАНИМИ
-- =============================================

BEGIN TRANSACTION;

-- ---------- Жанри ----------
INSERT INTO genre (id, name, description, color, icon) VALUES
(1, 'Action', 'Аніме з інтенсивними битвами та екшен-сценами', '#ef4444', 'sword'),
(2, 'Adventure', 'Подорожі, дослідження нових світів та пошук пригод', '#f59e0b', 'compass'),
(3, 'Comedy', 'Смішні ситуації, жарти та гумористичні діалоги', '#10b981', 'laugh'),
(4, 'Drama', 'Емоційні історії з глибоким психологічним підтекстом', '#8b5cf6', 'drama'),
(5, 'Fantasy', 'Магія, фантастичні істоти та вигадані світи', '#ec4899', 'magic'),
(6, 'Sci-Fi', 'Футуристичні технології, космос та наукові відкриття', '#3b82f6', 'robot'),
(7, 'Romance', 'Історії кохання та романтичні стосунки', '#ff6b6b', 'heart'),
(8, 'Slice of Life', 'Звичайне життя, щоденні турботи та побут', '#a3e635', 'home'),
(9, 'Mystery', 'Таємничі події, розслідування та нерозгадані загадки', '#6366f1', 'question'),
(10, 'Horror', 'Страшні історії, що викликають почуття страху та тривоги', '#18181b', 'ghost'),
(11, 'Psychological', 'Глибокий аналіз психіки персонажів та їхніх мотивів', '#7c3aed', 'brain'),
(12, 'Supernatural', 'Надприродні сили, духи, демони та паранормальні явища', '#c084fc', 'sparkles'),
(13, 'Sports', 'Спортивні змагання, тренування та командний дух', '#22c55e', 'sports'),
(14, 'Music', 'Історії про музику, музикантів та музичні гурти', '#f43f5e', 'music'),
(15, 'School', 'Шкільне життя, навчання та стосунки між учнями', '#eab308', 'school'),
(16, 'Shounen', 'Для хлопчиків-підлітків: битви, дружба, пригоди', '#f97316', 'target'),
(17, 'Seinen', 'Для дорослих чоловіків: серйозні теми, складні сюжети', '#6b7280', 'mature'),
(18, 'Isekai', 'Перенесення в інший світ, переродження', '#a855f7', 'portal'),
(19, 'Mecha', 'Гігантські роботи та пілотування', '#64748b', 'robot'),
(20, 'Historical', 'Історичні події та епохи', '#b45309', 'history'),
(21, 'Military', 'Військові конфлікти, армія, стратегія', '#475569', 'military'),
(22, 'Crime', 'Злочинний світ, розслідування, мафія', '#1e293b', 'crime'),
(23, 'Thriller', 'Напружені історії, що тримають у постійній напрузі', '#292524', 'thriller'),
(24, 'Gourmet', 'Про їжу, приготування та кулінарне мистецтво', '#fbbf24', 'food'),
(25, 'Harem', 'Один головний герой та багато героїнь', '#f87171', 'harem'),
(26, 'Martial Arts', 'Різні види бойових мистецтв та поєдинків', '#dc2626', 'fist'),
(27, 'Philosophical', 'Філософські роздуми про життя, смерть, сенс буття', '#4f46e5', 'philosophy'),
(28, 'Space', 'Космічні подорожі, міжпланетні війни', '#0f172a', 'rocket');

-- ---------- Студії ----------
INSERT INTO studio (id, name, country, founded, description, website) VALUES
(1, 'MAPPA', 'Japan', 2011, 'Відома студія, що створила Jujutsu Kaisen, Chainsaw Man, Attack on Titan (фінальні сезони)', 'https://mappa.co.jp'),
(2, 'Kyoto Animation', 'Japan', 1981, 'Студія, відома неймовірною якістю анімації', 'https://kyotoanimation.co.jp'),
(3, 'Bones', 'Japan', 1998, 'Студія, що створила Fullmetal Alchemist, My Hero Academia', 'https://bones.co.jp'),
(4, 'Madhouse', 'Japan', 1972, 'Одна з найстаріших та найвпливовіших студій', 'https://madhouse.co.jp'),
(5, 'A-1 Pictures', 'Japan', 2005, 'Студія, що створила Sword Art Online, Kaguya-sama', 'https://a1pictures.jp'),
(6, 'Ufotable', 'Japan', 2000, 'Відома неймовірною якістю анімації та використанням CGI', 'https://ufotable.com'),
(7, 'WIT Studio', 'Japan', 2012, 'Студія, що створила Attack on Titan (1-3 сезони), Vinland Saga', 'https://witstudio.co.jp'),
(8, 'CloverWorks', 'Japan', 2018, 'Дочірня студія Aniplex, створила Spy×Family', 'https://cloverworks.co.jp'),
(9, 'Trigger', 'Japan', 2011, 'Студія, відома екстравагантним стилем', 'https://trigger.co.jp'),
(10, 'Toei Animation', 'Japan', 1948, 'Найстаріша та одна з найбільших аніме-студій', 'https://toei-animation.com'),
(11, 'Production I.G', 'Japan', 1987, 'Студія, відома реалістичною анімацією', 'https://production-ig.co.jp'),
(12, 'Sunrise', 'Japan', 1972, 'Студія, відома франшизою Gundam', 'https://sunrise-inc.co.jp');

-- ---------- Користувачі ----------
INSERT INTO users (id, login, email, display_name, avatar_url, role, bio) VALUES
(1, 'admin', 'admin@anime-site.com', 'Адміністратор', 'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png', 'admin', 'Головний адміністратор сайту. Люблю аніме та мангу.'),
(2, 'animefan', 'fan@example.com', 'Аніме Фан', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png', 'user', 'Дивлюсь аніме щодня. Люблю бойовики та пригоди.'),
(3, 'mangalover', 'manga@example.com', 'Манга Любитель', 'https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png', 'user', 'Колекціоную мангу вже 10 років.'),
(4, 'reviewer', 'review@example.com', 'Оглядач', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png', 'user', 'Пишу огляди на новинки аніме.'),
(5, 'moderator', 'mod@example.com', 'Модератор', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295772_1280.png', 'moderator', 'Слідкую за порядком на сайті.');

-- ---------- Аніме (50+ тайтлів) ----------
INSERT INTO anime (id, title, title_ua, year, season, episodes, type, status, source, rating_mpaa, description, studio_id, cover_url, views, favorites) VALUES
(1, 'Frieren: Beyond Journey''s End', 'Проводжальниця Фрірен', 2023, 'Fall', 28, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Ельфійка-маг Фрірен після перемоги над Королем Демонів вирушає в нову подорож, щоб зрозуміти людські емоції.', 1, 'https://cdn.myanimelist.net/images/anime/1015/138006.jpg', 150000, 25000),
(2, 'Jujutsu Kaisen', 'Магічна битва', 2020, 'Fall', 47, 'TV', 'Ongoing', 'Manga', 'R', 'Юдзі Ітадорі ковтає проклятий палець Сукуни та вступає до Токійського технікуму дзюдзюцу.', 1, 'https://cdn.myanimelist.net/images/anime/1171/109222.jpg', 200000, 30000),
(3, 'Attack on Titan', 'Напад титанів', 2013, 'Spring', 88, 'TV', 'Completed', 'Manga', 'R', 'Людство живе за величезними стінами, захищаючись від титанів.', 7, 'https://cdn.myanimelist.net/images/anime/10/47347.jpg', 250000, 40000),
(4, 'Spy x Family', 'Сімейка шпигуна', 2022, 'Spring', 37, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Шпигун створює фальшиву сім''ю для місії, не знаючи, що дружина — вбивця, а дочка — телепат.', 8, 'https://cdn.myanimelist.net/images/anime/1441/122795.jpg', 180000, 28000),
(5, 'Demon Slayer: Kimetsu no Yaiba', 'Вбивця демонів', 2019, 'Spring', 55, 'TV', 'Ongoing', 'Manga', 'R', 'Танджіро Камадо стає мисливцем на демонів, щоб знайти ліки для сестри Недзуко.', 6, 'https://cdn.myanimelist.net/images/anime/1286/99889.jpg', 220000, 35000),
(6, 'Fullmetal Alchemist: Brotherhood', 'Сталевий алхімік: Братство', 2009, 'Spring', 64, 'TV', 'Completed', 'Manga', 'PG-13', 'Брати Елріки шукають Філософський камінь, щоб повернути свої тіла.', 3, 'https://cdn.myanimelist.net/images/anime/1223/96541.jpg', 180000, 30000),
(7, 'My Hero Academia', 'Моя геройська академія', 2016, 'Spring', 138, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Ізуку Мідорія народжується без суперздібності у світі, де вони є нормою.', 3, 'https://cdn.myanimelist.net/images/anime/10/78745.jpg', 160000, 25000),
(8, 'One Piece', 'Ван Піс', 1999, 'Fall', 1000, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Монкі Д. Луффі мріє стати Королем піратів.', 10, 'https://cdn.myanimelist.net/images/anime/6/73245.jpg', 300000, 50000),
(9, 'Chainsaw Man', 'Людина-бензопила', 2022, 'Fall', 12, 'TV', 'Ongoing', 'Manga', 'R', 'Денджі зливається з демоном-бензопилою та стає мисливцем на демонів.', 1, 'https://cdn.myanimelist.net/images/anime/1806/126216.jpg', 175000, 29000),
(10, 'Solo Leveling', 'Соло рівень', 2024, 'Winter', 24, 'TV', 'Completed', 'Manhwa', 'R', 'Найслабший мисливець отримує здатність підвищувати рівень.', 1, 'https://cdn.myanimelist.net/images/anime/1987/135339.jpg', 190000, 32000),
(11, 'Dandadan', 'Дандадан', 2024, 'Fall', 12, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Момо та Окарун стикаються з привидами та прибульцями.', 9, 'https://cdn.myanimelist.net/images/anime/1042/139481.jpg', 90000, 15000),
(12, 'Vinland Saga', 'Вінландська сага', 2019, 'Summer', 48, 'TV', 'Ongoing', 'Manga', 'R', 'Історична сага про вікінга Торфінна.', 7, 'https://cdn.myanimelist.net/images/anime/1500/103005.jpg', 165000, 27000),
(13, 'Kaguya-sama: Love Is War', 'Кагуя хоче зізнатися', 2019, 'Winter', 37, 'TV', 'Completed', 'Manga', 'PG-13', 'Два генії в студентській раді закохані, але надто горді для зізнання.', 5, 'https://cdn.myanimelist.net/images/anime/1764/106659.jpg', 150000, 25000),
(14, 'Mob Psycho 100', 'Моб Психо 100', 2016, 'Summer', 37, 'TV', 'Completed', 'Webcomic', 'PG-13', 'Могутній екстрасенс намагається жити звичайним життям.', 3, 'https://cdn.myanimelist.net/images/anime/1918/96303.jpg', 145000, 24000),
(15, 'Your Lie in April', 'Твоя квітнева брехня', 2014, 'Fall', 22, 'TV', 'Completed', 'Manga', 'PG-13', 'Піаніст втрачає здатність чути звук піаніно після смерті матері.', 5, 'https://cdn.myanimelist.net/images/anime/1751/115483.jpg', 135000, 22000),
(16, '86', 'Вісімдесят шість', 2021, 'Spring', 23, 'TV', 'Completed', 'Light Novel', 'R', 'Безпілотні дрони ведуть війну, але ними керують люди.', 5, 'https://cdn.myanimelist.net/images/anime/1111/117766.jpg', 120000, 20000),
(17, 'Code Geass', 'Код Ґіас', 2006, 'Fall', 50, 'TV', 'Completed', 'Original', 'R', 'Принц отримує силу Ґіаса та починає революцію.', 12, 'https://cdn.myanimelist.net/images/anime/5/50331.jpg', 180000, 30000),
(18, 'Cowboy Bebop', 'Ковбой Бібоп', 1998, 'Spring', 26, 'TV', 'Completed', 'Original', 'R', 'Мисливці за головами подорожують галактикою.', 12, 'https://cdn.myanimelist.net/images/anime/4/19644.jpg', 190000, 32000),
(19, 'Cyberpunk: Edgerunners', 'Кіберпанк: Бігуни', 2022, 'Fall', 10, 'ONA', 'Completed', 'Original', 'R+', 'Хлопець з вулиць стає кіберпанком у Найт-Сіті.', 9, 'https://cdn.myanimelist.net/images/anime/1818/126435.jpg', 160000, 27000),
(20, 'Re:Zero', 'Ре:Зеро', 2016, 'Spring', 50, 'TV', 'Ongoing', 'Light Novel', 'R', 'Субару переноситься у фентезійний світ і отримує здатність повертатися після смерті.', 1, 'https://cdn.myanimelist.net/images/anime/1522/128039.jpg', 140000, 24000),
(21, 'Mushoku Tensei', 'Реінкарнація безробітного', 2021, 'Winter', 47, 'TV', 'Ongoing', 'Light Novel', 'R+', '34-річний NEET перероджується у світі магії.', 1, 'https://cdn.myanimelist.net/images/anime/1530/117776.jpg', 155000, 26000),
(22, 'Blue Lock', 'Блакитний замок', 2022, 'Fall', 24, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Футбольний проект для створення найкращого нападника.', 1, 'https://cdn.myanimelist.net/images/anime/1258/126961.jpg', 140000, 22000),
(23, 'Oshi no Ko', 'Улюблене дитя', 2023, 'Spring', 11, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Лікар та пацієнтка перероджуються дітьми кумира.', 8, 'https://cdn.myanimelist.net/images/anime/1665/127471.jpg', 130000, 21000),
(24, 'Heavenly Delusion', 'Небесна маячня', 2023, 'Spring', 13, 'TV', 'Completed', 'Manga', 'R', 'Подорож у постапокаліптичній Японії.', 1, 'https://cdn.myanimelist.net/images/anime/1662/128324.jpg', 80000, 12000),
(25, 'Hell''s Paradise', 'Пекельний рай', 2023, 'Spring', 13, 'TV', 'Completed', 'Manga', 'R', 'Злочинці шукають еліксир безсмертя на таємничому острові.', 1, 'https://cdn.myanimelist.net/images/anime/1533/129977.jpg', 95000, 14000),
(26, 'Tokyo Revengers', 'Токійські месники', 2021, 'Spring', 37, 'TV', 'Completed', 'Manga', 'R', 'Чоловік подорожує в часі, щоб врятувати кохану та змінити майбутнє.', 1, 'https://cdn.myanimelist.net/images/anime/1472/126325.jpg', 110000, 17000),
(27, 'Horimiya', 'Хорімія', 2021, 'Winter', 13, 'TV', 'Completed', 'Manga', 'PG-13', 'Популярна дівчина та скромний хлопець приховують свою справжню особистість.', 8, 'https://cdn.myanimelist.net/images/anime/1695/111486.jpg', 130000, 21000),
(28, 'Dr. Stone', 'Доктор Стоун', 2019, 'Summer', 35, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Геній відроджує цивілізацію в кам''яному віці.', 8, 'https://cdn.myanimelist.net/images/anime/1613/102576.jpg', 140000, 23000),
(29, 'The Promised Neverland', 'Обіцяний Неверленд', 2019, 'Winter', 23, 'TV', 'Completed', 'Manga', 'R', 'Сироти дізнаються страшну правду про свій притулок.', 8, 'https://cdn.myanimelist.net/images/anime/1830/110780.jpg', 160000, 26000),
(30, 'Parasyte: The Maxim', 'Паразит: Максимум', 2014, 'Fall', 24, 'TV', 'Completed', 'Manga', 'R', 'Прибульці паразитують на людях.', 4, 'https://cdn.myanimelist.net/images/anime/3/73178.jpg', 150000, 24000),
(31, 'Neon Genesis Evangelion', 'Євангеліон', 1995, 'Fall', 26, 'TV', 'Completed', 'Original', 'PG-13', 'Підлітки пілотують гігантських роботів для боротьби з Ангелами.', 11, 'https://cdn.myanimelist.net/images/anime/1314/108941.jpg', 200000, 33000),
(32, 'Monster', 'Монстр', 2004, 'Spring', 74, 'TV', 'Completed', 'Manga', 'R+', 'Лікар рятує хлопчика, який виростає в серійного вбивцю.', 4, 'https://cdn.myanimelist.net/images/anime/10/18793.jpg', 170000, 28000),
(33, 'Gurren Lagann', 'Гуррен-Лаґанн', 2007, 'Spring', 27, 'TV', 'Completed', 'Original', 'PG-13', 'Хлопці з підземелля піднімаються на поверхню та борються зі злом.', 9, 'https://cdn.myanimelist.net/images/anime/4/5123.jpg', 160000, 26000),
(34, 'Death Note', 'Зошит смерті', 2006, 'Fall', 37, 'TV', 'Completed', 'Manga', 'R', 'Геніальний школяр отримує зошит, який вбиває.', 4, 'https://cdn.myanimelist.net/images/anime/5/106296.jpg', 210000, 37000),
(35, 'Naruto', 'Наруто', 2002, 'Fall', 220, 'TV', 'Completed', 'Manga', 'PG-13', 'Юний ніндзя мріє стати Хокаге.', 10, 'https://cdn.myanimelist.net/images/anime/6/73245.jpg', 230000, 38000),
(36, 'Dragon Ball Z', 'Драґонболл Z', 1989, 'Spring', 291, 'TV', 'Completed', 'Manga', 'PG-13', 'Сон Гоку захищає Землю від могутніх ворогів.', 10, 'https://cdn.myanimelist.net/images/anime/6/73245.jpg', 240000, 42000),
(37, 'Hunter x Hunter', 'Мисливець х Мисливець', 2011, 'Fall', 148, 'TV', 'Completed', 'Manga', 'PG-13', 'Хлопець стає мисливцем, щоб знайти свого батька.', 4, 'https://cdn.myanimelist.net/images/anime/11/33657.jpg', 190000, 34000),
(38, 'Fate/stay night', 'Fate/stay night', 2014, 'Fall', 25, 'TV', 'Completed', 'Visual Novel', 'R', 'Битва магів за Святий Грааль.', 6, 'https://cdn.myanimelist.net/images/anime/6/63395.jpg', 170000, 28000),
(39, 'Steins;Gate', 'Штейнгейт', 2011, 'Spring', 24, 'TV', 'Completed', 'Visual Novel', 'PG-13', 'Науковці випадково винаходять машину часу.', 4, 'https://cdn.myanimelist.net/images/anime/5/73199.jpg', 185000, 31000),
(40, 'Violet Evergarden', 'Фіолетова Евергарден', 2018, 'Spring', 13, 'TV', 'Completed', 'Light Novel', 'PG-13', 'Колишній солдат стає лялькою-автоматом, допомагаючи людям писати листи.', 2, 'https://cdn.myanimelist.net/images/anime/1795/95088.jpg', 155000, 26000),
(41, 'Made in Abyss', 'Зроблено в безодні', 2017, 'Summer', 13, 'TV', 'Ongoing', 'Manga', 'R', 'Дівчинка досліджує гігантську безодню.', 1, 'https://cdn.myanimelist.net/images/anime/6/86742.jpg', 125000, 19000),
(42, 'Bleach', 'Бліч', 2004, 'Fall', 366, 'TV', 'Completed', 'Manga', 'PG-13', 'Хлопець отримує сили Бога Смерті та захищає людей.', 10, 'https://cdn.myanimelist.net/images/anime/3/40451.jpg', 170000, 28000),
(43, 'Haikyuu!!', 'Хайкю!!', 2014, 'Spring', 85, 'TV', 'Completed', 'Manga', 'PG-13', 'Хлопці грають у волейбол.', 11, 'https://cdn.myanimelist.net/images/anime/8/73902.jpg', 145000, 24000),
(44, 'Black Clover', 'Чорна конюшина', 2017, 'Fall', 170, 'TV', 'Completed', 'Manga', 'PG-13', 'Хлопець без магії в світі магів мріє стати Королем чарівників.', 10, 'https://cdn.myanimelist.net/images/anime/2/88336.jpg', 130000, 20000),
(45, 'Sword Art Online', 'Меч онлайн', 2012, 'Summer', 96, 'TV', 'Ongoing', 'Light Novel', 'PG-13', 'Гравці застряють у смертельній VR-грі.', 5, 'https://cdn.myanimelist.net/images/anime/11/73902.jpg', 200000, 35000),
(46, 'Overlord', 'Володар', 2015, 'Summer', 52, 'TV', 'Ongoing', 'Light Novel', 'R', 'Геймер застряє в MMORPG як персонаж-ліч.', 4, 'https://cdn.myanimelist.net/images/anime/7/73902.jpg', 160000, 27000),
(47, 'That Time I Got Reincarnated as a Slime', 'Реінкарнація слизом', 2018, 'Fall', 72, 'TV', 'Ongoing', 'Light Novel', 'PG-13', 'Чоловік перероджується у вигляді слизу у фентезійному світі.', 1, 'https://cdn.myanimelist.net/images/anime/6/73902.jpg', 170000, 29000),
(48, 'Konosuba', 'Коносуба', 2016, 'Winter', 20, 'TV', 'Ongoing', 'Light Novel', 'PG-13', 'Пародія на ісекай з нездарами.', 1, 'https://cdn.myanimelist.net/images/anime/8/73902.jpg', 125000, 20000),
(49, 'Your Name', 'Твоє ім''я', 2016, 'Summer', 1, 'Movie', 'Completed', 'Original', 'PG-13', 'Хлопець і дівчина міняються тілами.', 1, 'https://cdn.myanimelist.net/images/anime/5/87048.jpg', 220000, 40000),
(50, 'Weathering With You', 'Дощ у твоїх очах', 2019, 'Summer', 1, 'Movie', 'Completed', 'Original', 'PG-13', 'Хлопець зустрічає дівчину, яка може зупиняти дощ.', 1, 'https://cdn.myanimelist.net/images/anime/11/73902.jpg', 185000, 31000);

-- ---------- Манга (30+ тайтлів) ----------
INSERT INTO manga (id, title, title_ua, year, status, volumes, chapters, type, demographic, description, cover_url, views, favorites) VALUES
(1, 'Berserk', 'Берсерк', 1989, 'Ongoing', 42, 376, 'Manga', 'Seinen', 'Ґатс, Чорний Мечник, проклятий тавром і приречений вічно боротися з демонами.', 'https://cdn.myanimelist.net/images/manga/1/157897.jpg', 200000, 35000),
(2, 'Vagabond', 'Бродяга', 1998, 'Hiatus', 37, 327, 'Manga', 'Seinen', 'Історія про легендарного мечника Міямото Мусаші.', 'https://cdn.myanimelist.net/images/manga/1/157913.jpg', 150000, 28000),
(3, 'One Punch Man', 'Людина-один удар', 2012, 'Ongoing', 28, 200, 'Manga', 'Shounen', 'Сайтама може перемогти будь-якого ворога одним ударом.', 'https://cdn.myanimelist.net/images/manga/3/155939.jpg', 180000, 30000),
(4, 'Chainsaw Man', 'Людина-бензопила', 2018, 'Ongoing', 17, 152, 'Manga', 'Shounen', 'Денджі зливається з демоном-бензопилою.', 'https://cdn.myanimelist.net/images/manga/3/216464.jpg', 170000, 29000),
(5, 'Solo Leveling', 'Соло рівень', 2018, 'Completed', 14, 200, 'Manhwa', 'Shounen', 'Найслабший мисливець стає найсильнішим.', 'https://cdn.myanimelist.net/images/manga/2/260415.jpg', 190000, 32000),
(6, 'Jujutsu Kaisen', 'Магічна битва', 2018, 'Ongoing', 25, 247, 'Manga', 'Shounen', 'Боротьба з прокляттями в сучасній Японії.', 'https://cdn.myanimelist.net/images/manga/3/220844.jpg', 185000, 31000),
(7, 'Attack on Titan', 'Напад титанів', 2009, 'Completed', 34, 139, 'Manga', 'Shounen', 'Людство бореться з титанами.', 'https://manga.in.ua/uploads/posts/2023-08/1691068924_00.webp', 210000, 38000),
(8, 'Naruto', 'Наруто', 1999, 'Completed', 72, 700, 'Manga', 'Shounen', 'Ніндзя мріє стати Хокаге.', 'https://cdn.myanimelist.net/images/manga/2/249315.jpg', 250000, 45000),
(9, 'Dragon Ball', 'Драґонболл', 1984, 'Completed', 42, 519, 'Manga', 'Shounen', 'Сон Гоку шукає сім куль.', 'https://i.redd.it/lk8099gv3wpe1.jpeg', 220000, 40000),
(10, 'My Hero Academia', 'Моя геройська академія', 2014, 'Ongoing', 38, 414, 'Manga', 'Shounen', 'Школа супергероїв.', 'https://cdn.myanimelist.net/images/manga/3/174681.jpg', 165000, 26000),
(11, 'Spy x Family', 'Сімейка шпигуна', 2019, 'Ongoing', 13, 100, 'Manga', 'Shounen', 'Шпигун створює сім''ю для місії.', 'https://cdn.myanimelist.net/images/manga/3/232659.jpg', 175000, 27000),
(12, 'Frieren: Beyond Journey''s End', 'Проводжальниця Фрірен', 2020, 'Ongoing', 12, 131, 'Manga', 'Shounen', 'Ельфійка розуміє цінність людського життя.', 'https://static.yakaboo.ua/media/catalog/product/i/m/img827_147.jpg', 155000, 25000),
(13, 'Vinland Saga', 'Вінландська сага', 2005, 'Ongoing', 27, 210, 'Manga', 'Seinen', 'Вікінг шукає помсту, а потім мир.', 'https://cdn.myanimelist.net/images/manga/3/202301.jpg', 160000, 27000),
(14, 'Monster', 'Монстр', 1994, 'Completed', 18, 162, 'Manga', 'Seinen', 'Лікар переслідує серійного вбивцю.', 'https://cdn.myanimelist.net/images/manga/1/157901.jpg', 170000, 28000),
(15, 'Death Note', 'Зошит смерті', 2003, 'Completed', 12, 108, 'Manga', 'Shounen', 'Школяр отримує зошит, який вбиває.', 'https://geekach.com.ua/content/images/4/356x536l99nn0/tetrad-smerti.-death-note.-black-edition.-kniga-1-67013924992675.jpg', 200000, 35000),
(16, 'Hunter x Hunter', 'Мисливець х Мисливець', 1998, 'Hiatus', 37, 390, 'Manga', 'Shounen', 'Хлопець стає мисливцем, щоб знайти батька.', 'https://cdn.myanimelist.net/images/manga/2/253119.jpg', 190000, 32000),
(17, 'One Piece', 'Ван Піс', 1997, 'Ongoing', 107, 1100, 'Manga', 'Shounen', 'Пірати шукають скарб One Piece.', 'https://cdn.myanimelist.net/images/manga/2/253146.jpg', 300000, 50000),
(18, 'Fullmetal Alchemist', 'Сталевий алхімік', 2001, 'Completed', 27, 108, 'Manga', 'Shounen', 'Брати шукають Філософський камінь.', 'https://cdn.myanimelist.net/images/manga/1/171814.jpg', 200000, 35000),
(19, 'Demon Slayer', 'Вбивця демонів', 2016, 'Completed', 23, 205, 'Manga', 'Shounen', 'Хлопець рятує сестру-демона.', 'https://cdn.myanimelist.net/images/manga/1/209292.jpg', 195000, 33000),
(20, 'Tokyo Revengers', 'Токійські месники', 2017, 'Completed', 31, 278, 'Manga', 'Shounen', 'Подорожі в часі для порятунку коханої.', 'https://cdn.myanimelist.net/images/manga/2/215007.jpg', 130000, 21000),
(21, 'Blue Lock', 'Блакитний замок', 2018, 'Ongoing', 27, 260, 'Manga', 'Shounen', 'Футбольний проект для створення найкращого нападника.', 'https://cdn.myanimelist.net/images/manga/2/226345.jpg', 140000, 22000),
(22, 'Oshi no Ko', 'Улюблене дитя', 2020, 'Ongoing', 13, 130, 'Manga', 'Seinen', 'Переродження дітьми кумира.', 'https://cdn.myanimelist.net/images/manga/3/249339.jpg', 145000, 24000),
(23, 'Kaguya-sama', 'Кагуя хоче зізнатися', 2015, 'Completed', 28, 281, 'Manga', 'Seinen', 'Романтична комедія в студраді.', 'https://cdn.myanimelist.net/images/manga/2/203163.jpg', 150000, 25000),
(24, 'Mob Psycho 100', 'Моб Психо 100', 2012, 'Completed', 16, 108, 'Manga', 'Shounen', 'Екстрасенс хоче жити нормальним життям.', 'https://cdn.myanimelist.net/images/manga/2/156802.jpg', 145000, 24000),
(25, 'Haikyuu!!', 'Хайкю!!', 2012, 'Completed', 45, 402, 'Manga', 'Shounen', 'Волейбольна команда.', 'https://cdn.myanimelist.net/images/manga/1/148237.jpg', 140000, 23000),
(26, 'Black Clover', 'Чорна конюшина', 2015, 'Ongoing', 35, 360, 'Manga', 'Shounen', 'Маг без магії хоче стати Королем.', 'https://cdn.myanimelist.net/images/manga/2/171910.jpg', 120000, 19000),
(27, 'The Promised Neverland', 'Обіцяний Неверленд', 2016, 'Completed', 20, 181, 'Manga', 'Shounen', 'Сироти тікають з притулку.', 'https://cdn.myanimelist.net/images/manga/3/207449.jpg', 160000, 26000),
(28, 'Dr. Stone', 'Доктор Стоун', 2017, 'Completed', 26, 232, 'Manga', 'Shounen', 'Відродження цивілізації в кам''яному віці.', 'https://cdn.myanimelist.net/images/manga/2/199887.jpg', 135000, 22000),
(29, 'Komi Can''t Communicate', 'Комі не може спілкуватися', 2016, 'Ongoing', 30, 430, 'Manga', 'Shounen', 'Дівчина з комунікаційним розладом.', 'https://cdn.myanimelist.net/images/manga/3/211225.jpg', 100000, 16000),
(30, 'Rent-A-Girlfriend', 'Орендована дівчина', 2017, 'Ongoing', 30, 300, 'Manga', 'Shounen', 'Студент орендує дівчину.', 'https://cdn.myanimelist.net/images/manga/3/214785.jpg', 95000, 15000);

-- ---------- Автори (seed) ----------
INSERT INTO author (id, name, bio) VALUES
(1, 'Kentaro Miura', 'Автор Berserk'),
(2, 'Takehiko Inoue', 'Автор Vagabond'),
(3, 'ONE', 'Автор One Punch Man'),
(4, 'Tatsuki Fujimoto', 'Автор Chainsaw Man'),
(5, 'Chugong', 'Автор Solo Leveling'),
(6, 'Eiichiro Oda', 'Автор One Piece'),
(7, 'Hajime Isayama', 'Автор Attack on Titan');

-- ---------- Зв'язки: Манга-Автори (seed) ----------
INSERT INTO manga_author (manga_id, author_id) VALUES
(1,1),
(2,2),
(3,3),
(4,4),
(5,5),
(6,6),
(7,7);

-- ---------- Персонажі (60+ персонажів) ----------
INSERT INTO character (id, name, full_name, gender, age, description, image_url, favorites) VALUES
(1, 'Frieren', 'Frieren', 'Female', 1000, 'Ельфійська магічка, яка подорожує у пошуках спогадів.', 'https://cdn.hikka.io/content/characters/frieren-7f706c/zX7V8YWM3zljrr80U3aPIw.jpg', 12500),
(2, 'Fern', 'Fern', 'Female', 16, 'Учениця Фрірен, спокійна та вправна в магії.', 'https://shikimori.one/system/characters/original/188176.jpg', 9800),
(3, 'Stark', 'Stark', 'Male', 17, 'Юний воїн, учень Айзена, боязкий, але сильний.', 'https://shikimori.one/system/characters/original/188177.jpg', 8700),
(4, 'Yuji Itadori', 'Yuji Itadori', 'Male', 16, 'Носій Короля Проклять Сукуни.', 'https://cdn.myanimelist.net/images/characters/2/423325.jpg', 24500),
(5, 'Satoru Gojo', 'Satoru Gojo', 'Male', 28, 'Найсильніший маг дзюдзюцу.', 'https://cdn.myanimelist.net/images/characters/16/422488.jpg', 35800),
(6, 'Eren Yeager', 'Eren Yeager', 'Male', 19, 'Колишній солдат Розвідкорпусу.', 'https://cdn.myanimelist.net/images/characters/14/313097.jpg', 28700),
(7, 'Mikasa Ackerman', 'Mikasa Ackerman', 'Female', 19, 'Одна з найсильніших солдатів.', 'https://cdn.myanimelist.net/images/characters/13/315813.jpg', 25300),
(8, 'Loid Forger', 'Loid Forger', 'Male', 30, 'Шпигун для місії ''Стрикс''.', 'https://cdn.myanimelist.net/images/characters/14/460298.jpg', 18000),
(9, 'Anya Forger', 'Anya Forger', 'Female', 6, 'Дитина-телепат.', 'https://cdn.myanimelist.net/images/characters/9/460299.jpg', 26000),
(10, 'Yor Forger', 'Yor Forger', 'Female', 27, 'Наймана вбивця ''Шипова Принцеса''.', 'https://cdn.myanimelist.net/images/characters/2/460300.jpg', 17600),
(11, 'Tanjiro Kamado', 'Tanjiro Kamado', 'Male', 15, 'Мисливець на демонів.', 'https://cdn.myanimelist.net/images/characters/2/386497.jpg', 28700),
(12, 'Nezuko Kamado', 'Nezuko Kamado', 'Female', 14, 'Сестра Танджіро, перетворена на демона.', 'https://cdn.myanimelist.net/images/characters/16/386498.jpg', 24500),
(13, 'Edward Elric', 'Edward Elric', 'Male', 16, 'Сталевий Алхімік.', 'https://cdn.myanimelist.net/images/characters/2/273227.jpg', 26700),
(14, 'Alphonse Elric', 'Alphonse Elric', 'Male', 15, 'Душа в обладунках.', 'https://cdn.myanimelist.net/images/characters/3/273228.jpg', 19800),
(15, 'Izuku Midoriya', 'Izuku Midoriya', 'Male', 16, 'Успадкував силу ''One For All''.', 'https://cdn.myanimelist.net/images/characters/9/320992.jpg', 21000),
(16, 'Monkey D. Luffy', 'Monkey D. Luffy', 'Male', 19, 'Капітан Піратів Солом''яного Капелюха.', 'https://cdn.myanimelist.net/images/characters/2/275719.jpg', 37800),
(17, 'Roronoa Zoro', 'Roronoa Zoro', 'Male', 21, 'Тримечник, перший помічник.', 'https://cdn.myanimelist.net/images/characters/14/275720.jpg', 29800),
(18, 'Denji', 'Denji', 'Male', 16, 'Людина-бензопила.', 'https://cdn.myanimelist.net/images/characters/16/459893.jpg', 22300),
(19, 'Sung Jinwoo', 'Sung Jinwoo', 'Male', 25, 'Король Тіней.', 'https://cdn.myanimelist.net/images/characters/9/460295.jpg', 28700),
(20, 'Momo Ayase', 'Momo Ayase', 'Female', 16, 'Вірить у привидів.', 'https://cdn.myanimelist.net/images/characters/9/538181.jpg', 12400),
(21, 'Ken Takakura', 'Ken Takakura (Okarun)', 'Male', 16, 'Вірить в інопланетян.', 'https://cdn.myanimelist.net/images/characters/16/538183.jpg', 11800),
(22, 'Thorfinn', 'Thorfinn', 'Male', 22, 'Вікінг у пошуках помсти.', 'https://cdn.myanimelist.net/images/characters/11/298269.jpg', 19800),
(23, 'Kaguya Shinomiya', 'Kaguya Shinomiya', 'Female', 17, 'Спадкоємиця величезної корпорації.', 'https://cdn.myanimelist.net/images/characters/8/385983.jpg', 19900),
(24, 'Miyuki Shirogane', 'Miyuki Shirogane', 'Male', 17, 'Президент студради.', 'https://cdn.myanimelist.net/images/characters/11/385984.jpg', 18700),
(25, 'Shigeo Kageyama', 'Shigeo "Mob" Kageyama', 'Male', 14, 'Могутній екстрасенс.', 'https://cdn.myanimelist.net/images/characters/7/299837.jpg', 17600),
(26, 'Kosei Arima', 'Kosei Arima', 'Male', 17, 'Піаніст-вундеркінд.', 'https://cdn.myanimelist.net/images/characters/15/280432.jpg', 16500),
(27, 'Lelouch Lamperouge', 'Lelouch Lamperouge', 'Male', 18, 'Таємничий лідер повстанців.', 'https://cdn.myanimelist.net/images/characters/7/299809.jpg', 25600),
(28, 'Spike Spiegel', 'Spike Spiegel', 'Male', 27, 'Мисливець за головами.', 'https://cdn.myanimelist.net/images/characters/3/299810.jpg', 23400),
(29, 'David Martinez', 'David Martinez', 'Male', 17, 'Кіберпанк у Найт-Сіті.', 'https://cdn.myanimelist.net/images/characters/16/463888.jpg', 18700),
(30, 'Subaru Natsuki', 'Subaru Natsuki', 'Male', 18, 'Володіє ''Return by Death''.', 'https://cdn.myanimelist.net/images/characters/15/295105.jpg', 18700),
(31, 'Rudeus Greyrat', 'Rudeus Greyrat', 'Male', 20, 'Перероджений NEET.', 'https://cdn.myanimelist.net/images/characters/16/463890.jpg', 18700);

-- ---------- Зв'язки: Аніме-Жанри ----------
INSERT INTO anime_genre (anime_id, genre_id) VALUES
(1,2),(1,4),(1,5),(1,8),(2,1),(2,5),(2,11),(2,12),(3,1),(3,2),(3,4),(3,21),
(4,1),(4,3),(4,4),(4,8),(5,1),(5,2),(5,4),(5,5),(6,1),(6,2),(6,4),(6,27),
(7,1),(7,2),(7,3),(7,15),(8,1),(8,2),(8,3),(8,5),(9,1),(9,2),(9,4),(9,10),
(10,1),(10,2),(10,4),(10,18),(11,1),(11,3),(11,5),(11,7),(12,1),(12,2),(12,4),(12,20),
(13,3),(13,4),(13,7),(13,15),(14,1),(14,3),(14,11),(14,12),(15,4),(15,7),(15,14),(15,15),
(16,1),(16,4),(16,6),(16,21),(17,1),(17,4),(17,6),(17,22),(18,1),(18,4),(18,6),(18,28),
(19,1),(19,4),(19,6),(19,23),(20,4),(20,5),(20,14),(20,18),(21,1),(21,4),(21,5),(21,18),
(22,1),(22,4),(22,13),(22,15),(23,1),(23,4),(23,14),(23,15),(24,1),(24,2),(24,4),(24,5),
(25,1),(25,2),(25,4),(25,5),(25,10),(26,1),(26,4),(26,9),(26,11),(27,3),(27,4),(27,7),(27,15),
(28,1),(28,4),(28,6),(28,15),(29,1),(29,4),(29,9),(29,10),(30,1),(30,4),(30,9),(30,11),
(31,1),(31,4),(31,6),(31,19),(32,1),(32,4),(32,6),(32,28),(33,1),(33,2),(33,4),(33,20),
(34,1),(34,4),(34,6),(34,28),(35,1),(35,2),(35,4),(35,5),(35,16),(36,1),(36,2),(36,4),(36,6),(36,19),
(37,1),(37,2),(37,4),(37,5),(37,16),(38,1),(38,4),(38,6),(38,28),(39,1),(39,4),(39,6),(39,11),
(40,4),(40,7),(40,14),(40,15),(41,1),(41,2),(41,4),(41,5),(42,1),(42,4),(42,6),(42,12),
(43,1),(43,4),(43,13),(43,15),(44,1),(44,4),(44,16),(44,15),(45,1),(45,4),(45,6),(45,18),
(46,1),(46,4),(46,5),(46,18),(47,1),(47,4),(47,5),(47,18),(48,1),(48,3),(48,4),(48,18),
(49,4),(49,7),(49,8),(49,14),(50,4),(50,7),(50,8),(50,27);

-- ---------- Зв'язки: Аніме-Персонажі ----------
INSERT INTO anime_character (anime_id, character_id, role, is_main) VALUES
(1,1,'Main',1),(1,2,'Main',1),(1,3,'Main',1),
(2,4,'Main',1),(2,5,'Supporting',0),
(3,6,'Main',1),(3,7,'Main',1),
(4,8,'Main',1),(4,9,'Main',1),(4,10,'Main',1),
(5,11,'Main',1),(5,12,'Main',1),
(6,13,'Main',1),(6,14,'Main',1),
(7,15,'Main',1),
(8,16,'Main',1),(8,17,'Main',1),
(9,18,'Main',1),
(10,19,'Main',1),
(11,20,'Main',1),(11,21,'Main',1),
(12,22,'Main',1),
(13,23,'Main',1),(13,24,'Main',1),
(14,25,'Main',1),
(15,26,'Main',1),
(17,27,'Main',1),
(18,28,'Main',1),
(19,29,'Main',1),
(20,30,'Main',1),
(21,31,'Main',1);

-- ---------- Зв'язки: Манга-Жанри ----------
INSERT INTO manga_genre (manga_id, genre_id) VALUES
(1,1),(1,2),(1,4),(1,5),(1,10),(1,17),
(2,2),(2,4),(2,20),(2,17),
(3,1),(3,3),(3,5),(3,12),(3,16),
(4,1),(4,2),(4,4),(4,5),(4,10),
(5,1),(5,2),(5,4),(5,5),(5,12),
(6,1),(6,4),(6,5),(6,11),(6,12),
(7,1),(7,2),(7,4),(7,5),(7,21),
(8,1),(8,2),(8,4),(8,5),(8,16),
(9,1),(9,2),(9,3),(9,4),(9,5),(9,16),
(10,1),(10,2),(10,3),(10,4),(10,13),(10,15),
(11,1),(11,2),(11,3),(11,4),(11,7),(11,8),
(12,2),(12,3),(12,4),(12,5),(12,8),
(13,1),(13,4),(13,20),(13,17),
(14,4),(14,9),(14,11),(14,22),(14,23),
(15,4),(15,9),(15,11),(15,22),(15,23),
(16,1),(16,2),(16,4),(16,5),(16,16),
(17,1),(17,2),(17,4),(17,5),(17,16),
(18,1),(18,2),(18,4),(18,5),(18,16),
(19,1),(19,2),(19,4),(19,5),(19,16),
(20,1),(20,4),(20,9),(20,11),
(21,1),(21,4),(21,13),(21,15),
(22,1),(22,4),(22,14),(22,15),
(23,3),(23,4),(23,7),(23,15),
(24,1),(24,3),(24,11),(24,12),
(25,1),(25,4),(25,13),(25,15),
(26,1),(26,4),(26,11),(26,16),
(27,1),(27,4),(27,9),(27,10),
(28,1),(28,4),(28,6),(28,15),
(29,3),(29,4),(29,7),(29,15),
(30,3),(30,4),(30,7),(30,15);

-- ---------- Рейтинги ----------
INSERT INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES
(1,1,9.5,1,'Completed',28),(1,2,9.0,1,'Completed',28),(2,1,9.0,1,'Completed',24),(2,2,9.5,1,'Completed',24),
(3,1,10.0,1,'Completed',88),(3,2,9.8,1,'Completed',88),(4,2,9.5,1,'Completed',12),(4,3,9.0,1,'Completed',12),
(5,2,9.9,1,'Completed',26),(5,3,9.7,1,'Completed',26),(6,2,9.8,1,'Completed',64),(6,3,9.7,1,'Completed',64),
(7,2,8.5,0,'Completed',13),(7,3,9.0,1,'Completed',13),(8,1,9.5,1,'Watching',500),(8,2,9.0,1,'Watching',300),
(9,1,9.0,1,'Completed',12),(9,2,9.2,1,'Completed',12),(10,1,9.2,1,'Completed',12),(10,2,9.5,1,'Completed',12),
(11,1,8.5,1,'Completed',12),(11,2,9.0,1,'Completed',12),(12,1,9.3,1,'Completed',24),(12,2,9.6,1,'Completed',24),
(13,1,9.1,1,'Completed',12),(13,2,9.4,1,'Completed',12),(14,1,8.9,1,'Completed',12),(14,2,9.2,1,'Completed',12),
(15,1,9.0,1,'Completed',22);

-- ---------- Коментарі ----------
INSERT INTO comments (user_id, anime_id, content, likes, created_at) VALUES
(2,1, 'Frieren — це справжній шедевр. Емоційна та глибока історія.', 45, CURRENT_TIMESTAMP),
(3,2, 'Jujutsu Kaisen має найкращу анімацію боїв, яку я коли-небудь бачив.', 32, CURRENT_TIMESTAMP),
(4,3, 'Attack on Titan змінив моє уявлення про аніме. Фінал — це щось неймовірне.', 67, CURRENT_TIMESTAMP),
(5,4, 'Spy x Family — ідеальне поєднання комедії, екшену та тепла.', 28, CURRENT_TIMESTAMP),
(2,5, 'Demon Slayer: Entertainment District Arc — візуальний феєрверк!', 56, CURRENT_TIMESTAMP),
(3,8, 'One Piece триває вже 20+ років і досі залишається цікавим. Ода — геній.', 89, CURRENT_TIMESTAMP),
(1,9, 'Chainsaw Man — божевільний, кривавий і неймовірно захопливий.', 34, CURRENT_TIMESTAMP),
(4,10, 'Solo Leveling — мрія кожного геймера. Джіну неймовірно крутий.', 41, CURRENT_TIMESTAMP);

-- ---------- Новини (seed) ----------
INSERT INTO news (id, title, content, summary, category, image_url, author_id, is_published, published_at) VALUES
(1, 'Запуск нового каталогу', 'Ми додали новий каталог з покращеними фільтрами та рейтингами.', 'Новий каталог з фільтрами та рейтингами', 'Оголошення', 'https://picsum.photos/seed/news1/800/450', 1, 1, '2026-04-25 09:00:00'),
(2, 'Виправлено помилки відображення обкладинок', 'Виправлено логіку використання cover/poster для коректного показу обкладинок.', 'Фікс обкладинок', 'Технічне', 'https://picsum.photos/seed/news2/800/450', 4, 1, '2026-04-26 11:30:00'),
(3, 'Додано фільтр за оцінкою', 'Тепер можна фільтрувати тайтли за мінімальним рейтингом.', 'Фільтр за рейтингом', 'Оновлення', 'https://picsum.photos/seed/news3/800/450', 2, 1, '2026-04-27 14:00:00'),
(4, 'Нові персонажі додані', 'У базу додано більше персонажів з зображеннями та звязками.', 'Багато нових персонажів', 'Контент', 'https://picsum.photos/seed/news4/800/450', 3, 1, '2026-04-28 10:00:00'),
(5, 'Покращено пошук', 'Пошук тепер враховує повні імена персонажів та альтернативні назви.', 'Покращений пошук', 'Технічне', 'https://picsum.photos/seed/news5/800/450', 1, 1, '2026-04-29 12:00:00'),
(6, 'Робота над мобільним відображенням', 'Інтерфейс фільтрів адаптовано для мобільних пристроїв.', 'Мобільні покращення', 'Оновлення', 'https://picsum.photos/seed/news6/800/450', 4, 1, '2026-04-30 09:30:00'),
(7, 'Планове технічне обслуговування', 'Сайт буде недоступний на 30 хвилин для оновлення.', 'Техобслуговування', 'Оголошення', 'https://picsum.photos/seed/news7/800/450', 1, 1, '2026-05-01 02:00:00'),
(8, 'Додано рейтинг персонажів (пілот)', 'Пілотна функція рейтингу персонажів доступна обмеженому колу користувачів.', 'Пілотний рейтинг', 'Експеримент', 'https://picsum.photos/seed/news8/800/450', 5, 1, '2026-05-01 15:00:00'),
(9, 'Оновлення API', 'Випущено нову версію внутрішнього API для швидшої роботи запитів.', 'API 2.1', 'Технічне', 'https://picsum.photos/seed/news9/800/450', 1, 1, '2026-05-02 08:00:00'),
(10, 'Керівництво для модераторів', 'Опубліковано оновлені інструкції для модерації контенту.', 'Інструкції модератору', 'Документація', 'https://picsum.photos/seed/news10/800/450', 4, 1, '2026-05-02 16:00:00'),
(11, 'Нові теги і категорії', 'Додано кілька категорій для новин та контенту.', 'Нові категорії', 'Оновлення', 'https://picsum.photos/seed/news11/800/450', 2, 1, '2026-05-03 09:00:00'),
(12, 'Конкурс оглядів', 'Запускаємо конкурс оглядів — переможці отримають підписки.', 'Конкурс оглядів', 'Події', 'https://picsum.photos/seed/news12/800/450', 3, 1, '2026-05-03 18:00:00');

COMMIT;

