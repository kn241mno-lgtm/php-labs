PRAGMA foreign_keys = ON;

-- =============================================
-- ОРИГІНАЛЬНА СТРУКТУРА (без змін)
-- =============================================

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login TEXT NOT NULL UNIQUE,
    email TEXT,
    password TEXT,
    first_name TEXT,
    last_name TEXT,
    display_name TEXT,
    avatar_url TEXT,
    phone TEXT,
    city TEXT,
    gender TEXT,
    about TEXT,
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

-- НОВА ТАБЛИЦЯ ДЛЯ ЗВ'ЯЗКУ АНІМЕ ТА МАНГИ
CREATE TABLE IF NOT EXISTS anime_manga (
    anime_id INTEGER,
    manga_id INTEGER,
    PRIMARY KEY (anime_id, manga_id),
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE
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

-- ---------- Жанри  ----------
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

-- ---------- Студії  ----------
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

-- ---------- Користувачі  ----------
INSERT INTO users (id, login, password, email, first_name, last_name, display_name, avatar_url, phone, city, gender, about, bio, role) VALUES
(1, 'admin', 'Admin123!', 'admin@anime-site.com', 'Головний', 'Адміністратор', 'Адміністратор', 'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png', NULL, NULL, '', 'Головний адміністратор сайту. Люблю аніме та мангу.', 'Головний адміністратор сайту. Люблю аніме та мангу.', 'admin'),
(2, 'animefan', 'Fan12345', 'fan@example.com', 'Аніме', 'Фан', 'Аніме Фан', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png', NULL, NULL, '', 'Дивлюсь аніме щодня. Люблю бойовики та пригоди.', 'Дивлюсь аніме щодня. Люблю бойовики та пригоди.', 'user'),
(3, 'mangalover', 'Manga123', 'manga@example.com', 'Манга', 'Любитель', 'Манга Любитель', 'https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png', NULL, NULL, '', 'Колекціоную мангу вже 10 років.', 'Колекціоную мангу вже 10 років.', 'user'),
(4, 'reviewer', 'Review123', 'review@example.com', 'Оглядач', '', 'Оглядач', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295770_1280.png', NULL, NULL, '', 'Пишу огляди на новинки аніме.', 'Пишу огляди на новинки аніме.', 'user'),
(5, 'moderator', 'Mod12345', 'mod@example.com', 'Модератор', '', 'Модератор', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295772_1280.png', NULL, NULL, '', 'Слідкую за порядком на сайті.', 'Слідкую за порядком на сайті.', 'moderator'),
(6, 'kasumi', 'Kasumi123', 'kasumi@example.com', 'Касумі', 'Такахаші', 'Касумі', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295774_1280.png', NULL, NULL, 'Female', 'Отаку зі стажем, люблю романтику та драми.', 'Отаку зі стажем, люблю романтику та драми.', 'user'),
(7, 'shinji', 'Shinji123', 'shinji@example.com', 'Шінджі', 'Ікарі', 'Шінджі', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295771_1280.png', NULL, NULL, 'Male', 'Меха та психологічні трилери — моє все.', 'Меха та психологічні трилери — моє все.', 'user'),
(8, 'naruto_fan', 'Rasengan22', 'naruto@example.com', 'Наруто', 'Узумакі', 'Наруто Фан', 'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295776_1280.png', NULL, NULL, 'Male', 'Вірю в шлях ніндзя!', 'Вірю в шлях ніндзя!', 'user');

-- ---------- АНІМЕ  ----------
INSERT INTO anime (id, title, title_ua, year, season, episodes, type, status, source, rating_mpaa, description, studio_id, cover_url, views, favorites) VALUES
(1, 'Frieren: Beyond Journey''s End', 'Проводжальниця Фрірен', 2023, 'Fall', 28, 'TV', 'Completed', 'Manga', 'PG-13', 'Після десятирічної подорожі, герої перемогли Короля Демонів. Ельфійка-маг Фрірен, чиє життя триває тисячоліття, тепер мусить зрозуміти, що означає жити серед людей, чиї життя такі короткі. Вона вирушає в нову подорож, щоб пізнати людські емоції та попрощатися з тими, кого полюбила. Неймовірно зворушлива історія про втрату, пам''ять та цінність кожного моменту.', 1, 'https://cdn.myanimelist.net/images/anime/1015/138006l.jpg', 150000, 25000),
(2, 'Jujutsu Kaisen', 'Магічна битва', 2020, 'Fall', 47, 'TV', 'Ongoing', 'Manga', 'R', 'Старшокласник Юдзі Ітадорі веде звичайне життя, поки не ковтає проклятий палець Сукуни, найсильнішого прокляття в історії. Тепер він змушений вступити до Токійського технікуму дзюдзюцу, щоб боротися з прокляттями та зібрати всі пальці Сукуни. Динамічні бої, унікальна система сил та чудові персонажі чекають на вас.', 1, 'https://cdn.myanimelist.net/images/anime/1171/109222l.jpg', 200000, 30000),
(3, 'Attack on Titan', 'Напад титанів', 2013, 'Spring', 88, 'TV', 'Completed', 'Manga', 'R', 'Людство живе за величезними стінами, захищаючись від титанів — гігантських людоїдських істот. Ерен Єгер, його прийомна сестра Мікаса та друг Армін приєднуються до Розвідувального Корпусу, щоб помститися за зруйноване рідне місто. Епічна сага про свободу, жертовність та темні таємниці світу, яка змінила індустрію аніме.', 7, 'https://cdn.myanimelist.net/images/anime/10/47347l.jpg', 250000, 40000),
(4, 'Spy x Family', 'Сімейка шпигуна', 2022, 'Spring', 37, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Найкращий шпигун Заходу Лойд Форджер отримує завдання: створити фальшиву сім''ю та влаштувати дитину до престижної школи, щоб наблизитися до цілі. Але він не знає, що його обрана дружина Йор — професійна вбивця, а дочка Аня — телепат. Вибухова суміш шпигунства, комедії та сімейного тепла.', 8, 'https://cdn.myanimelist.net/images/anime/1441/122795l.jpg', 180000, 28000),
(5, 'Demon Slayer: Kimetsu no Yaiba', 'Вбивця демонів', 2019, 'Spring', 55, 'TV', 'Ongoing', 'Manga', 'R', 'Танджіро Камадо повертається додому й знаходить свою родину вбитою демонами, а єдина виживша сестра Недзуко перетворюється на демона. Хлопець стає мисливцем на демонів, щоб знайти ліки для сестри та помститися. Неймовірна анімація від ufotable, зворушлива історія та захопливі бої зробили це аніме світовим феноменом.', 6, 'https://cdn.myanimelist.net/images/anime/1286/99889l.jpg', 220000, 35000),
(6, 'Fullmetal Alchemist: Brotherhood', 'Сталевий алхімік: Братство', 2009, 'Spring', 64, 'TV', 'Completed', 'Manga', 'PG-13', 'Брати Едвард та Альфонс Елріки порушили найголовнішу заборону алхімії — спробували воскресити померлу матір. Розплатою стали тіло Альфонса та рука й нога Едварда. Тепер вони шукають Філософський камінь, щоб повернути втрачене. Шедевр, який ідеально поєднує екшен, драму, філософію та політику.', 3, 'https://cdn.myanimelist.net/images/anime/1223/96541l.jpg', 180000, 30000),
(7, 'My Hero Academia', 'Моя геройська академія', 2016, 'Spring', 138, 'TV', 'Ongoing', 'Manga', 'PG-13', 'У світі, де 80% населення має надздібності (примхи), Ізуку Мідорія народжується зовсім без сили. Але він не здається — після зустрічі з символом миру Все Могтом він отримує шанс вступити до престижної академії героїв U.A. Історія про справжнього героя, яким не народжуються, а стають.', 3, 'https://cdn.myanimelist.net/images/anime/10/78745l.jpg', 160000, 25000),
(8, 'One Piece', 'Ван Піс', 1999, 'Fall', 1000, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Монкі Д. Луффі мріє стати Королем піратів, знайшовши легендарний скарб One Piece. Він збирає команду Солом''яного Капелюха та вирушає у неймовірну подорож Гранд Лайн. Культова пригода з гумором, божевільними персонажами та глибокими темами дружби та свободи, яка триває вже понад 20 років.', 10, 'https://cdn.myanimelist.net/images/anime/6/73245l.jpg', 300000, 50000),
(9, 'Chainsaw Man', 'Людина-бензопила', 2022, 'Fall', 12, 'TV', 'Ongoing', 'Manga', 'R', 'Денджі, бідний хлопець, який працює мисливцем на демонів, щоб виплатити борг якудзи, зливається з демоном-бензопилою свого померлого друга. Тепер він може перетворювати частини тіла на бензопили. Його наймає урядова організація для боротьби з могутніми демонами. Божевільний, кривавий, сексуальний та неймовірно стильний екшен від MAPPA.', 1, 'https://cdn.myanimelist.net/images/anime/1806/126216l.jpg', 175000, 29000),
(10, 'Solo Leveling', 'Соло рівень', 2024, 'Winter', 24, 'TV', 'Completed', 'Manhwa', 'R', 'У світі, де існують портали в підземелля, мисливці з надздібностями борються з монстрами. Сон Джіну, найслабший мисливець E-рангу, ледь не гине в подвійному підземеллі, але отримує унікальну систему, яка дозволяє йому підвищувати рівень, як у грі. Відтепер він стає найсильнішим мисливцем. Адаптація мегапопулярної корейської манхви.', 1, 'https://cdn.myanimelist.net/images/anime/1987/135339l.jpg', 190000, 32000),
(11, 'Dandadan', 'Дандадан', 2024, 'Fall', 12, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Момо Аясе вірить у привидів, але не в інопланетян, а її однокласник Кен «Окарун» Такакура — навпаки. Після суперечки вони відвідують місця, пов''язані з привидами та НЛО, і обоє отримують надприродні сили. Тепер їм доведеться боротися з прибульцями та духами в цьому божевільному, смішному та екшн-наповненому аніме від студії Science SARU.', 9, 'https://cdn.myanimelist.net/images/anime/1042/139481l.jpg', 90000, 15000),
(12, 'Vinland Saga', 'Вінландська сага', 2019, 'Summer', 48, 'TV', 'Ongoing', 'Manga', 'R', 'Початок XI століття. Молодий ісландець Торфінн прагне помститися за смерть батька, вбитого найманим вікінгом Аскеладдом. Він приєднується до загону Аскеладда, щоб одного дня викликати його на дуель. Але з часом Торфінн розуміє, що помста — це порожнеча, і починає шукати справжній сенс життя в легендарній країні Вінланд. Епічна сага про насильство, мир та спокуту.', 7, 'https://cdn.myanimelist.net/images/anime/1500/103005l.jpg', 165000, 27000),
(13, 'Kaguya-sama: Love Is War', 'Кагуя хоче зізнатися', 2019, 'Winter', 37, 'TV', 'Completed', 'Manga', 'PG-13', 'Кагуя Шіномія та Міюкі Шіроґане — генії, які очолюють студентську раду престижної академії. Вони закохані одне в одного, але надто горді, щоб зізнатися першими. Тож починається психологічна війна, де кожен намагається змусити іншого зізнатися в коханні. Геніальна комедія з романтикою, яка постійно ламає четверту стіну.', 5, 'https://cdn.myanimelist.net/images/anime/1764/106659l.jpg', 150000, 25000),
(14, 'Mob Psycho 100', 'Моб Психо 100', 2016, 'Summer', 37, 'TV', 'Completed', 'Webcomic', 'PG-13', 'Шіґео «Моб» Каґеяма — восьмикласник з неймовірними екстрасенсорними здібностями, але мріє про звичайне життя. Він намагається контролювати свої емоції, адже коли вони досягають 100%, його сила виходить з-під контролю. Працюючи на шарлатана-екстрасенса Рейгена, Моб вчиться, що справжня сила — у людяності, а не в психіці. Візуальне божевілля від студії Bones.', 3, 'https://cdn.myanimelist.net/images/anime/1918/96303l.jpg', 145000, 24000),
(15, 'Your Lie in April', 'Твоя квітнева брехня', 2014, 'Fall', 22, 'TV', 'Completed', 'Manga', 'PG-13', 'Піаніст-вундеркінд Косей Аріма втратив здатність чути звук фортепіано після смерті матері. Два роки його життя було сірим, поки він не зустрічає Каорі — вільну та енергійну скрипальку, яка допомагає йому знову знайти музику. Зворушлива історія про кохання, втрату та силу мистецтва, яка змусить вас плакати.', 5, 'https://cdn.myanimelist.net/images/anime/1751/115483l.jpg', 135000, 22000),
(16, '86', 'Вісімдесят шість', 2021, 'Spring', 23, 'TV', 'Completed', 'Light Novel', 'R', 'Республіка Сан-Магнонія веде війну з сусідньою імперією, використовуючи безпілотні дрони. Але насправді ними керують «нелюди» — група під назвою «86», яких відправляють на смерть. Лейтенантка Владлена Мілізе командує ними дистанційно та прагне зрозуміти їхній біль. Жорстка військова драма про дискримінацію, надію та цінність життя.', 5, 'https://cdn.myanimelist.net/images/anime/1111/117766l.jpg', 120000, 20000),
(17, 'Code Geass', 'Код Ґіас', 2006, 'Fall', 50, 'TV', 'Completed', 'Original', 'R', 'Світ поневолений Британською імперією. Вигнаний принц Лелуш Ламперуж отримує силу Ґіаса — здатність підкорювати будь-чию волю. Він стає таємничим лідером повстанців «Нуль» і розпочинає революцію. Стратегічні битви, меха, зради та один із найкращих фіналів в аніме.', 12, 'https://cdn.myanimelist.net/images/anime/5/50331l.jpg', 180000, 30000),
(18, 'Cowboy Bebop', 'Ковбой Бібоп', 1998, 'Spring', 26, 'TV', 'Completed', 'Original', 'R', '2071 рік. Екіпаж космічного корабля «Бібоп» — мисливці за головами: Спайк Шпігель, Джет Блек, Фей Валентайн, Ед та пес Ейн. Вони мандрують Сонячною системою, переслідуючи злочинців, але їхні особисті демони та минуле постійно наздоганяють їх. Нуар, джаз, екшен та філософія — безсмертна класика.', 12, 'https://cdn.myanimelist.net/images/anime/4/19644l.jpg', 190000, 32000),
(19, 'Cyberpunk: Edgerunners', 'Кіберпанк: Бігуни', 2022, 'Fall', 10, 'ONA', 'Completed', 'Original', 'R+', 'У майбутньому Найт-Сіті, де технології переплітаються з насильством, хлопець з вулиць Девід Мартінес намагається вижити та стати легендою. Після трагедії він встановлює військовий імплант та приєднується до банди «Еджраннерів». Трагедія в кіберпанк-стилі від студії Trigger та CD Projekt Red.', 9, 'https://cdn.myanimelist.net/images/anime/1818/126435l.jpg', 160000, 27000),
(20, 'Re:Zero', 'Ре:Зеро', 2016, 'Spring', 50, 'TV', 'Ongoing', 'Light Novel', 'R', 'Звичайний NEET Субару Нацукі раптово переноситься у фентезійний світ. Він швидко розуміє, що має здатність «Повернення смертю» — після смерті він повертається в минуле. Кожен цикл приносить нові страждання та загадки. Психологічна драма про біль, надію та ціну, яку доводиться платити за порятунок близьких.', 1, 'https://cdn.myanimelist.net/images/anime/1522/128039l.jpg', 140000, 24000),
(21, 'Mushoku Tensei', 'Реінкарнація безробітного', 2021, 'Winter', 47, 'TV', 'Ongoing', 'Light Novel', 'R+', '34-річний безробітний NEET помирає та перероджується у світі магії та мечів немовлям на ім''я Рудеус Ґрейрат. Зберігаючи спогади минулого життя, він вирішує прожити це життя без жалю, стаючи найкращим магом. «Прабатько ісекаю» з неймовірною світобудовою, персонажами та анімацією.', 1, 'https://cdn.myanimelist.net/images/anime/1530/117776l.jpg', 155000, 26000),
(22, 'Blue Lock', 'Блакитний замок', 2022, 'Fall', 24, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Після ганебного виступу на чемпіонаті світу, Японія запускає проект «Блакитний замок» — 300 найкращих молодих нападників закриті у в''язниці-академії, де вони грають на виліт, щоб створити єдиного егоїстичного генія, здатного виграти кубок світу. Психологічний футбольний трилер.', 1, 'https://cdn.myanimelist.net/images/anime/1258/126961l.jpg', 140000, 22000),
(23, 'Oshi no Ko', 'Улюблене дитя', 2023, 'Spring', 11, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Лікар Ґоро Амамія — фанат ідола Ай Хошіно. Коли вагітна Ай приходить до нього на прийом, його вбиває її переслідувач. Ґоро перероджується сином Ай разом з іншою пацієнткою. Вони дорослішають і дізнаються темні таємниці шоу-бізнесу. Революційна драма про ілюзію слави та помсту.', 8, 'https://cdn.myanimelist.net/images/anime/1665/127471l.jpg', 130000, 21000),
(24, 'Heavenly Delusion', 'Небесна маячня', 2023, 'Spring', 13, 'TV', 'Completed', 'Manga', 'R', 'У постапокаліптичній Японії, зруйнованій катастрофою, Мару та Кіруко подорожують у пошуках місця під назвою «Небесна маячня». Тим часом у закритому навчальному закладі діти з надздібностями живуть в ізоляції. Таємничий зв''язок між цими двома світами розкривається повільно. Шедевр студії Production I.G.', 1, 'https://cdn.myanimelist.net/images/anime/1662/128324l.jpg', 80000, 12000),
(25, 'Hell''s Paradise', 'Пекельний рай', 2023, 'Spring', 13, 'TV', 'Completed', 'Manga', 'R', 'Габімару, ніндзя-вбивця, засуджений до смерті. Але сьогун дає йому шанс: знайти еліксир безсмертя на таємничому острові, де, за чутками, живе рай. Він приєднується до інших засуджених та їхніх наглядачів. На острові їх чекають не лише монстри, але й власні демони. Екшн, горор та філософія від MAPPA.', 1, 'https://cdn.myanimelist.net/images/anime/1533/129977l.jpg', 95000, 14000),
(26, 'Tokyo Revengers', 'Токійські месники', 2021, 'Spring', 37, 'TV', 'Completed', 'Manga', 'R', 'Такемічі Ханагакі — нікчемний 26-річний чоловік, який дізнається, що його колишня дівчина загинула через злочинне угрупування «Токійська Манджі». Після випадку на залізниці він подорожує на 12 років назад, у старшу школу, щоб змінити майбутнє та врятувати кохану. Бандитська драма з елементами подорожей у часі.', 1, 'https://cdn.myanimelist.net/images/anime/1472/126325l.jpg', 110000, 17000),
(27, 'Horimiya', 'Хорімія', 2021, 'Winter', 13, 'TV', 'Completed', 'Manga', 'PG-13', 'Кьоко Хорі — популярна та розумна школярка, яка вдома доглядає за братом, бо батьки постійно на роботі. Ізумі Міямура — тихий отаку, якого всі вважають занудою. Але одного дня вони бачать одне одного поза школою та відкривають справжні «я». Світла, реалістична романтична комедія про прийняття себе.', 8, 'https://cdn.myanimelist.net/images/anime/1695/111486l.jpg', 130000, 21000),
(28, 'Dr. Stone', 'Доктор Стоун', 2019, 'Summer', 35, 'TV', 'Ongoing', 'Manga', 'PG-13', 'Таємничий спалах перетворює все людство на камінь. Через 3700 років геній науки Сенкуо Ішігамі пробуджується та вирішує відродити цивілізацію за допомогою науки. Він створює «Королівство Науки» та змагається з «Імперією Сили», яку очолює його колишній друг. Наукова пригода, де кожен епізод — урок хімії та фізики.', 8, 'https://cdn.myanimelist.net/images/anime/1613/102576l.jpg', 140000, 23000),
(29, 'The Promised Neverland', 'Обіцяний Неверленд', 2019, 'Winter', 23, 'TV', 'Completed', 'Manga', 'R', 'Сирітський притулок «Грейс Філд» здається раєм: діти живуть щасливо, мама любить їх, а регулярні тести визначають, кого вдочерять. Але одного дня Емма та Норман дізнаються жахливу правду: діти — це худоба, яку вирощують на м''ясо для демонів. Починається гонка з часом, щоб втекти. Психологічний горор з геніальними пастками.', 8, 'https://cdn.myanimelist.net/images/anime/1830/110780l.jpg', 160000, 26000),
(30, 'Parasyte: The Maxim', 'Паразит: Максимум', 2014, 'Fall', 24, 'TV', 'Completed', 'Manga', 'R', 'Таємничі паразити прибувають на Землю та замінюють мозок людей. Студент Сінічі Ідзумі не стає їхньою жертвою лише тому, що паразит Міґі не встигає дістатися до мозку — він оселяється в його правій руці. Тепер вони змушені співіснувати та боротися з іншими паразитами. Філософський горор про людяність та природу хижацтва.', 4, 'https://cdn.myanimelist.net/images/anime/3/73178l.jpg', 150000, 24000),
(31, 'Neon Genesis Evangelion', 'Євангеліон', 1995, 'Fall', 26, 'TV', 'Completed', 'Original', 'PG-13', 'У 2015 році таємничі істоти — Ангели — атакують Землю. Лише гігантські біомеханічні роботи Evangelion здатні їм протистояти. Шінджі Ікарі, проблемний підліток, покликаний батьком пілотувати Evangelion-01. Екзистенційна драма, яка досліджує депресію, самотність та ідентичність під обгорткою мехи. Культова класика.', 11, 'https://cdn.myanimelist.net/images/anime/1314/108941l.jpg', 200000, 33000),
(32, 'Monster', 'Монстр', 2004, 'Spring', 74, 'TV', 'Completed', 'Manga', 'R+', 'Геніальний нейрохірург Кензо Тенма рятує життя хлопчика Йохана, а не мера. Роки потому Йохан стає серійним вбивцею-маніпулятором. Тенма вирушає в подорож Німеччиною та Чехією, щоб зупинити створене ним «чудовисько». Психологічний трилер про мораль, зло та природу зла.', 4, 'https://cdn.myanimelist.net/images/anime/10/18793l.jpg', 170000, 28000),
(33, 'Gurren Lagann', 'Гуррен-Лаґанн', 2007, 'Spring', 27, 'TV', 'Completed', 'Original', 'PG-13', 'Саймон та Каміна живуть у підземному селі, поки не знаходять маленький міхур — Гуррен. Вони вириваються на поверхню та приєднуються до битви проти правителя спіралі. З кожним епізодом роботи стають більшими, а масштаби — галактичними. Гіперболічний меха-бойовик про віру, дружбу та силу волі.', 9, 'https://cdn.myanimelist.net/images/anime/4/5123l.jpg', 160000, 26000),
(34, 'Death Note', 'Зошит смерті', 2006, 'Fall', 37, 'TV', 'Completed', 'Manga', 'R', 'Геніальний школяр Лайт Ягамі знаходить зошит, у якому, написавши ім''я, можна вбити людину. Він вирішує стати богом нового світу, караючи злочинців під псевдонімом «Кіра». Але геніальний детектив L починає полювання. Інтелектуальна дуель, яка перевертає уявлення про добро та зло.', 4, 'https://cdn.myanimelist.net/images/anime/5/106296l.jpg', 210000, 37000),
(35, 'Naruto', 'Наруто', 2002, 'Fall', 220, 'TV', 'Completed', 'Manga', 'PG-13', 'Наруто Узумакі — неслухняний ніндзя-підліток, який мріє стати Хокаге — лідером свого села. Усередині нього запечатаний Дев''ятихвостий лис-демон, через що односельці ненавидять його. Але він ніколи не здається. Епічна подорож про дорослішання, дружбу та самоприйняття.', 10, 'https://cdn.myanimelist.net/images/anime/13/17405l.jpg', 230000, 38000),
(36, 'Dragon Ball Z', 'Драґонболл Z', 1989, 'Spring', 291, 'TV', 'Completed', 'Manga', 'PG-13', 'Сон Гоку тепер дорослий, має сина Гохана. На Землю прибувають могутні вороги: Вегета, Фріза, Андроїди, Буу. Гоку та його друзі постійно перевершують свої межі, щоб захистити планету. Батько всіх сучасних сьонен-бойовиків, де битви тривають десятки епізодів, а крики під час перетворень стали легендарними.', 10, 'https://cdn.myanimelist.net/images/anime/13/18205l.jpg', 240000, 42000),
(37, 'Hunter x Hunter', 'Мисливець х Мисливець', 2011, 'Fall', 148, 'TV', 'Completed', 'Manga', 'PG-13', 'Гон Фрікс дізнається, що його батько, який зник, є легендарним Мисливцем. Хлопець вирішує скласти іспит на Мисливця, щоб знайти його. У дорозі він зустрічає друзів: Курапіку, Леоріо та Кіллуа. Але іспит — лише початок; попереду — кримінальні синдикати, геніальні вбивці та жорстокі монстри. Найкраща сьонен-манга, яка постійно ламає жанрові кліше.', 4, 'https://cdn.myanimelist.net/images/anime/11/33657l.jpg', 190000, 34000),
(38, 'Fate/stay night: Unlimited Blade Works', 'Fate/stay night: Unlimited Blade Works', 2014, 'Fall', 25, 'TV', 'Completed', 'Visual Novel', 'R', 'Війна Святого Грааля — битва семи магів, кожен з яких викликає легендарного героя (Слугу). Переможець отримує Грааль, який виконує будь-яке бажання. Шіро Емія, усиновлений магом, випадково втягується у війну та викликає Слугу Сейбер. Візуально приголомшлива адаптація від ufotable з комплексною магічною системою.', 6, 'https://cdn.myanimelist.net/images/anime/6/63395l.jpg', 170000, 28000),
(39, 'Steins;Gate', 'Штейнгейт', 2011, 'Spring', 24, 'TV', 'Completed', 'Visual Novel', 'PG-13', 'Самопроголошений божевільний вчений Окабе Рінтаро та його друзі випадково винаходять мікрохвильову піч, здатну надсилати повідомлення в минуле. Вони починають змінювати історію, але швидко усвідомлюють страшні наслідки. Повільний початок змінюється неймовірно напруженим трилером про подорожі в часі та відповідальність.', 4, 'https://cdn.myanimelist.net/images/anime/5/73199l.jpg', 185000, 31000),
(40, 'Violet Evergarden', 'Фіолетова Евергарден', 2018, 'Spring', 13, 'TV', 'Completed', 'Light Novel', 'PG-13', 'Вайолет Евергарден — колишня солдатка, яка була «зброєю» на війні. Після війни вона влаштовується лялькою-автоматом — особистою секретаркою, яка пише листи замість клієнтів. Вона не розуміє емоцій, але, допомагаючи іншим, поступово пізнає, що означає «Я тебе люблю». Найкрасивіша анімація від Kyoto Animation та історія, що вичавлює сльози.', 2, 'https://cdn.myanimelist.net/images/anime/1795/95088l.jpg', 155000, 26000),
(41, 'Made in Abyss', 'Зроблено в безодні', 2017, 'Summer', 13, 'TV', 'Ongoing', 'Manga', 'R', 'На острові існує гігантська безодня — Безодня, яку неможливо дослідити до кінця. Ті, хто спускаються, отримують «прокляття безодні» — при підйомі людина може померти або втратити людську подобу. Сирота Ріко мріє піти слідами матері-легенди та знайти дно. Разом із роботом Регу вона вирушає в небезпечну подорож. Казково-красивий, але жахливо жорстокий світ.', 1, 'https://cdn.myanimelist.net/images/anime/6/86742l.jpg', 125000, 19000),
(42, 'Bleach', 'Бліч', 2004, 'Fall', 366, 'TV', 'Completed', 'Manga', 'PG-13', 'Ічіго Куросакі бачить привидів. Одного разу він зустрічає бога смерті Рокію Кукікі, яка передає йому свої сили, щоб він захистив його сім''ю. Тепер Ічіго — заступник бога смерті, який бореться зі злими духами (пустими) та іншими загрозами. Культовий сьонен з сотнями персонажів, банкаями та епічними битвами.', 10, 'https://cdn.myanimelist.net/images/anime/3/40451l.jpg', 170000, 28000),
(43, 'Haikyuu!!', 'Хайкю!!', 2014, 'Spring', 85, 'TV', 'Completed', 'Manga', 'PG-13', 'Шоґьо Хіната — маленький хлопець з величезною пристрастю до волейболу. Побачивши геніального гравця на прізвисько «Король повітря», він вирішує стати таким же. У старшій школі він вступає до волейбольного клубу, де його суперником стає Тобіо Каґеяма — егоїстичний геній. Їхнє суперництво переростає в найкраще подвійне зєднання. Енергійний, реалістичний та надихаючий спортивний бойовик.', 11, 'https://cdn.myanimelist.net/images/anime/8/73902l.jpg', 145000, 24000),
(44, 'Black Clover', 'Чорна конюшина', 2017, 'Fall', 170, 'TV', 'Completed', 'Manga', 'PG-13', 'У світі, де всі вміють користуватися магією, Аста народжується зовсім без неї. Його друг-суперник Юно — геній. Обоє мріють стати Королем Чарівників. Аста отримує пятилистниковий гримуар із антимагією, стає лицарем Чорного Бика та долає численних ворогів. Гучний, галасливий, але неймовірно мотивуючий сьонен.', 10, 'https://cdn.myanimelist.net/images/anime/2/88336l.jpg', 130000, 20000),
(45, 'Sword Art Online', 'Меч онлайн', 2012, 'Summer', 96, 'TV', 'Ongoing', 'Light Novel', 'PG-13', 'Тисячі гравців застряють у смертельній VR-грі Sword Art Online: смерть у грі означає смерть у реальності. Кіріто, бета-тестер-одинак, змушений битися за виживання. Він зустрічає Асуну, і разом вони намагаються знайти вихід. Піонер ісекай-жанру, який задав тон багатьом майбутнім серіалам про ігри.', 5, 'https://cdn.myanimelist.net/images/anime/11/75261l.jpg', 200000, 35000),
(46, 'Overlord', 'Володар', 2015, 'Summer', 52, 'TV', 'Ongoing', 'Light Novel', 'R', 'Момонґа — лідер гільдії в популярній MMORPG YGGDRASIL. У день закриття серверів він не виходить із гри, і світ стає реальним. Він перетворюється на свого персонажа — могутнього ліча-чарівника Айнза Уул Говна. Разом зі своїми NPC (тепер живими) він вирішує підкорити цей новий світ. Темне фентезі з антигероєм.', 4, 'https://cdn.myanimelist.net/images/anime/7/88019l.jpg', 160000, 27000),
(47, 'That Time I Got Reincarnated as a Slime', 'Реінкарнація слизом', 2018, 'Fall', 72, 'TV', 'Ongoing', 'Light Novel', 'PG-13', '37-річного Сізуру Сатору вбиває злочинець, і він перероджується у фентезійному світі як блакитний слиз. Але його унікальні навички дозволяють йому поглинати істоти та отримувати їхні сили. Він швидко стає лідером чудовиськ і будує дружню націю для всіх рас. Легкий, веселий та захопливий ісекай-стройка.', 1, 'https://cdn.myanimelist.net/images/anime/6/73902l.jpg', 170000, 29000),
(48, 'Konosuba', 'Коносуба', 2016, 'Winter', 20, 'TV', 'Ongoing', 'Light Novel', 'PG-13', 'Хлопець Кадзума помирає безглуздою смертю та потрапляє до богині Аква, яка пропонує йому переродитися у фентезійному світі з однією річчю. Він обирає її саму. Але Аква марна, маг Меґумін вибухає лише раз на день, а лицар Даркнесс — мазохістка. Пародія на ісекай, сповнена абсурдного гумору та халеп.', 1, 'https://cdn.myanimelist.net/images/anime/8/73902l.jpg', 125000, 20000),
(49, 'Your Name', 'Твоє ім''я', 2016, 'Summer', 1, 'Movie', 'Completed', 'Original', 'PG-13', 'Такі — хлопець з Токіо, Міцуха — дівчина з сільського містечка. Вони прокидаються в тілах одне одного без пояснення. Коли вони намагаються знайти одне одного, виявляється, що їх розділяють не лише відстані, а й час. Найкасовіше аніме в історії, яке вражає візуальною красою та зворушливою історією кохання, долі та пам''яті.', 1, 'https://cdn.myanimelist.net/images/anime/5/87048l.jpg', 220000, 40000),
(50, 'Weathering With You', 'Дощ у твоїх очах', 2019, 'Summer', 1, 'Movie', 'Completed', 'Original', 'PG-13', 'Ходака тікає з дому до Токіо, де влаштовується писати статті для окультного журналу. Він зустрічає Хіну, дівчину, яка може зупиняти дощ і викликати сонце. Але у цієї сили є ціна. Візуальний шедевр від творця «Твого імені» про кохання, клімат і жертви заради інших.', 1, 'https://cdn.myanimelist.net/images/anime/9/94401l.jpg', 185000, 31000);

-- ---------- МАНГА  ----------
INSERT INTO manga (id, title, title_ua, year, status, volumes, chapters, type, demographic, description, cover_url, views, favorites) VALUES
(1, 'Berserk', 'Берсерк', 1989, 'Ongoing', 42, 376, 'Manga', 'Seinen', 'Темне фентезі про Ґатса, Чорного Мечника, проклятого тавром жертви, який приваблює демонів. Він мандрує середньовічним світом, вирубуючи монстрів своїм гігантським мечем, але справжня битва — з його власними демонами. Глибока, жорстока та неймовірно деталізована манґа, яка вплинула на цілий жанр. На жаль, автор Кентаро Міура помер у 2021, але його друзі продовжують серію.', 'https://cdn.myanimelist.net/images/manga/1/157897l.jpg', 200000, 35000),
(2, 'Vagabond', 'Бродяга', 1998, 'Hiatus', 37, 327, 'Manga', 'Seinen', 'Легендарна історія про Міямото Мусаші, найвідомішого мечника Японії, на основі роману Ейджі Йосікави «Мусаші». Художній шедевр Такехіко Іноуе, який досліджує шлях воїна, філософію, самотність і пошук справжньої сили. Неймовірно мальовнича манґа з глибокими персонажами. На довготривалій паузі.', 'https://cdn.myanimelist.net/images/manga/1/157913l.jpg', 150000, 28000),
(3, 'One Punch Man', 'Людина-один удар', 2012, 'Ongoing', 28, 200, 'Manga', 'Shounen', 'Сайтама — герой, який може перемогти будь-якого ворога одним ударом. Це набридло йому до смерті. Він шукає гідного супротивника, але постійно страждає від нудьги та бюрократії Асоціації Героїв. Сатирична пародія на супергеройський жанр з неймовірною анімацією в оригінальному аніме, але манґа (ілюстрації Юсуке Мурати) — це візуальний феєрверк.', 'https://cdn.myanimelist.net/images/manga/3/155939l.jpg', 180000, 30000),
(4, 'Chainsaw Man', 'Людина-бензопила', 2018, 'Ongoing', 17, 152, 'Manga', 'Shounen', 'Денджі, бідолашний хлопець, який ледве зводить кінці з кінцями, вбиває демонів для якудзи. Після зради він зливається зі своїм демоном-бензопилою Почітою та отримує здатність перетворювати частини тіла на бензопили. Його наймає урядова організація. Божевільний, кінематографічний та емоційно руйнівний екшен від Тайюкі Фуджімото. Частина 2 зараз виходить.', 'https://cdn.myanimelist.net/images/manga/3/216464l.jpg', 170000, 29000),
(5, 'Solo Leveling', 'Соло рівень', 2018, 'Completed', 14, 200, 'Manhwa', 'Shounen', 'Корейська манхва, яка стала світовим феноменом. Найслабший мисливець E-рангу Сон Джіну ледве виживає в подвійному підземеллі та отримує «систему», яка дозволяє йому підвищувати рівень, як у грі. Він перетворюється з найслабшого на найсильнішого мисливця, здатного командувати армією тіней. Захопливий ріст персонажа та круті битви.', 'https://cdn.myanimelist.net/images/manga/2/260415l.jpg', 190000, 32000),
(6, 'Jujutsu Kaisen', 'Магічна битва', 2018, 'Ongoing', 25, 247, 'Manga', 'Shounen', 'Юдзі Ітадорі ковтає палець Сукуни, найсильнішого прокляття, і приєднується до Токійського технікуму дзюдзюцу. Там він зустрічає Меґумі Фусіґуро, Нобара Кусакібе та найсильнішого мага Сатору Ґоджо. Манґа славиться своєю унікальною системою проклятої енергії, жорстокими битвами та непередбачуваними смертями. Зараз у фінальній арці.', 'https://cdn.myanimelist.net/images/manga/3/220844l.jpg', 185000, 31000),
(7, 'Attack on Titan', 'Напад титанів', 2009, 'Completed', 34, 139, 'Manga', 'Shounen', 'Людство живе за стінами, боячись титанів — гігантських людоїдів. Ерен Єгер, Мікаса Акерман та Армін Арлерт приєднуються до Розвідувального Корпусу, щоб пізнати правду про світ. Епічна сага, яка починається як бойовик про виживання, а перетворюється на складний політичний трилер про війну, свободу та спадкову ненависть. Фінал викликав бурхливі дискусії.', 'https://cdn.myanimelist.net/images/manga/2/150314l.jpg', 210000, 38000),
(8, 'Naruto', 'Наруто', 1999, 'Completed', 72, 700, 'Manga', 'Shounen', 'Ніндзя-підліток Наруто Узумакі мріє стати Хокаге — лідером свого села. Всередині нього запечатаний Девятихвостий демон-лис. Разом із друзями Саске Учіхою та Сакурою Харуно він виконує місії, бере участь у екзаменах на чунина та бореться з терористичною організацією Акацукі. Одна з трьох великих сьонен, що визначила покоління.', 'https://cdn.myanimelist.net/images/manga/2/249315l.jpg', 250000, 45000),
(9, 'Dragon Ball', 'Драґонболл', 1984, 'Completed', 42, 519, 'Manga', 'Shounen', 'Культова пригода хлопчика Сон Гоку з хвостом мавпи, який шукає сім легендарних куль, щоб виконати бажання. Від початкових пошуків куль та турнірів бойових мистецтв до битв із прибульцями, андроїдами та богами — ця манґа створила сучасний сьонен-жанр.', 'https://i.redd.it/lk8099gv3wpe1.jpeg', 220000, 40000),
(10, 'My Hero Academia', 'Моя геройська академія', 2014, 'Ongoing', 38, 414, 'Manga', 'Shounen', 'У світі, де більшість має надздібності (примхи), Ізуку Мідорія народжується без сили. Він зустрічає свого кумира Все Могта, який передає йому свою примху One For All. Ізуку вступає до престижної Академії Героїв U.A., щоб стати справжнім героєм. Поєднує шкільне життя з екшеном та драмою.', 'https://cdn.myanimelist.net/images/manga/3/174681l.jpg', 165000, 26000),
(11, 'Spy x Family', 'Сімейка шпигуна', 2019, 'Ongoing', 13, 100, 'Manga', 'Shounen', 'Агент Заходу Лойд Форджер створює фальшиву сімю для місії, не знаючи, що його «дружина» Йор — професійна вбивця, а «дочка» Аня — телепат. Вони грають ролі ідеальної сімї, але кожен приховує свою таємницю. Тепла, смішна і часом зворушлива комедія про сімейні цінності та секретні операції.', 'https://cdn.myanimelist.net/images/manga/3/232659l.jpg', 175000, 27000),
(12, 'Frieren: Beyond Journey''s End', 'Проводжальниця Фрірен', 2020, 'Ongoing', 12, 131, 'Manga', 'Shounen', 'Після перемоги над Королем Демонів, ельфійка Фрірен вирушає в подорож, щоб зрозуміти людські емоції. Вона подорожує зі своїми учнями Ферн і Штарком. Манґа зосереджена на ностальгії, прощанні та красі маленьких моментів. Спокійна, філософська, але з потужними боями. Аніме-адаптація від MAPPA стала хітом.', 'https://static.yakaboo.ua/media/catalog/product/i/m/img827_147.jpg', 155000, 25000),
(13, 'Vinland Saga', 'Вінландська сага', 2005, 'Ongoing', 27, 210, 'Manga', 'Seinen', 'Початок XI століття. Торфінн, син легендарного воїна Торса, прагне помститися вбивці батька Аскеладду. Він приєднується до загону вбивці, але з часом його шлях змінюється. Друга половина манґи — це глибока філософська драма про ненасильство, мир і справжню силу. Історична сага, яка змальовує вікінгів не як стереотипних варварів.', 'https://cdn.myanimelist.net/images/manga/3/202301l.jpg', 160000, 27000),
(14, 'Monster', 'Монстр', 1994, 'Completed', 18, 162, 'Manga', 'Seinen', 'Геніальний нейрохірург Кензо Тенма рятує хлопчика Йохана, який виростає в серійного вбивцю-маніпулятора. Тенма вирушає в подорож, щоб зупинити «монстра», якого він створив. Психологічний трилер про природу зла, моральні дилеми та німецький пейзаж після воззєднання. Вершина творчості Наокі Урасави.', 'https://cdn.myanimelist.net/images/manga/1/157901l.jpg', 170000, 28000),
(15, 'Death Note', 'Зошит смерті', 2003, 'Completed', 12, 108, 'Manga', 'Shounen', 'Геній Лайт Яґамі знаходить зошит, у якому, написавши імя, можна вбити. Він вирішує стати богом нового світу — Кірою, караючи злочинців. Геніальний детектив L починає полювання. Інтелектуальна дуель, яка змушує читача постійно сумніватися в моральності дій головного героя.', 'https://geekach.com.ua/content/images/4/356x536l99nn0/tetrad-smerti.-death-note.-black-edition.-kniga-1-67013924992675.jpg', 200000, 35000),
(16, 'Hunter x Hunter', 'Мисливець х Мисливець', 1998, 'Hiatus', 37, 390, 'Manga', 'Shounen', 'Гон Фрікс вирушає знайти свого батька-Мисливця. Він складає небезпечний іспит, де знайомиться з Кіллуа, Курапікою та Леоріо. Манґа поступово перетворюється з класичної пригоди на складний кримінальний трилер із жорстокою системою сил Nen. Часті перерви, але кожна глава — подія. Арка «Вибір голови» вважається однією з найкращих у манґі.', 'https://cdn.myanimelist.net/images/manga/2/253119l.jpg', 190000, 32000),
(17, 'One Piece', 'Ван Піс', 1997, 'Ongoing', 107, 1100, 'Manga', 'Shounen', 'Монкі Д. Луффі, хлопець, який після поїдання Диявольського фрукта став гумовою людиною, збирає команду, щоб знайти легендарний скарб One Piece і стати Королем піратів. Найпродаваніша манґа в історії, відома своїм світом, таємницями, гумором і здатністю викликати сльози навіть через 20+ років.', 'https://cdn.myanimelist.net/images/manga/2/253146l.jpg', 300000, 50000),
(18, 'Fullmetal Alchemist', 'Сталевий алхімік', 2001, 'Completed', 27, 108, 'Manga', 'Shounen', 'Брати Едвард та Альфонс Елріки намагаються воскресити матір за допомогою алхімії, але платять високу ціну. Вони шукають Філософський камінь, щоб відновити свої тіла. Манґа, яка ідеально збалансувала екшен, драму, комедію та глибоку мораль. Вважається однією з найкращих коли-небудь написаних сьонен-манґ.', 'https://cdn.myanimelist.net/images/manga/1/171814l.jpg', 200000, 35000),
(19, 'Demon Slayer: Kimetsu no Yaiba', 'Вбивця демонів', 2016, 'Completed', 23, 205, 'Manga', 'Shounen', 'Танджіро Камадо стає мисливцем на демонів, щоб знайти ліки для сестри Недзуко, яка стала демоном. Поєднує красиві техніки дихання, трагічні історії демонів та міцні сімейні узи. Манґа стала мегапопулярною завдяки аніме-адаптації від ufotable, але оригінал має свій шарм і більш швидкий темп.', 'https://cdn.myanimelist.net/images/manga/1/209292l.jpg', 195000, 33000),
(20, 'Tokyo Revengers', 'Токійські месники', 2017, 'Completed', 31, 278, 'Manga', 'Shounen', 'Такемічі Ханагакі дізнається про загибель своєї колишньої дівчини від банди «Токійська Манджі». Несподівано він починає подорожувати в часі на 12 років назад, у старшу школу, щоб змінити майбутнє. Бандитська драма, де герою доводиться пробиватися через жорстокість і зради.', 'https://cdn.myanimelist.net/images/manga/2/215007l.jpg', 130000, 21000),
(21, 'Blue Lock', 'Блакитний замок', 2018, 'Ongoing', 27, 260, 'Manga', 'Shounen', 'Після провалу на ЧС, Японія створює тюремний проект «Blue Lock», де 300 молодих нападників змагаються на виліт, щоб створити егоїстичного генія. Футбольна манґа, яка більше схожа на психологічний трилер. Ніякої командної роботи, тільки его — єдиний шлях до перемоги.', 'https://cdn.myanimelist.net/images/manga/2/226345l.jpg', 140000, 22000),
(22, 'Oshi no Ko', 'Улюблене дитя', 2020, 'Ongoing', 13, 130, 'Manga', 'Seinen', 'Лікар, який був фанатом ідола Ай Хошіно, перероджується її сином разом з іншою пацієнткою. Вони швидко дорослішають, дізнаються про темний бік шоу-бізнесу та починають помсту. Революційна манґа, яка показує реалії індустрії розваг: від ідолів до акторів театру та реаліті-шоу.', 'https://cdn.myanimelist.net/images/manga/3/249339l.jpg', 145000, 24000),
(23, 'Kaguya-sama: Love Is War', 'Кагуя хоче зізнатися', 2015, 'Completed', 28, 281, 'Manga', 'Seinen', 'Два генії в студентській раді закохані один в одного, але надто горді, щоб зізнатися. Вони влаштовують психологічну війну, щоб змусити іншого зізнатися першим. Геніальна комедія з романтикою, яка постійно ламає четверту стіну та поступово стає душевною драмою. Ідеальний приклад того, як можна писати романтичну манґу.', 'https://cdn.myanimelist.net/images/manga/2/203163l.jpg', 150000, 25000),
(24, 'Mob Psycho 100', 'Моб Психо 100', 2012, 'Completed', 16, 108, 'Manga', 'Shounen', 'Моб, восьмикласник-екстрасенс, працює на шарлатана Рейгена. Він намагається контролювати свої емоції, бо коли вони досягають 100%, його сила виходить з-під контролю. Манґа від автора One Punch Man, але більш зосереджена на персонажах та їхньому розвитку, ніж на битвах. Фінальна арка вражає емоційною глибиною.', 'https://cdn.myanimelist.net/images/manga/2/156802l.jpg', 145000, 24000),
(25, 'Haikyuu!!', 'Хайкю!!', 2012, 'Completed', 45, 402, 'Manga', 'Shounen', 'Шоґьо Хіната, маленький хлопець з величезним стрибком, мріє стати найкращим волейболістом. У старшій школі він стає напарником і суперником геніального подаючого Тобіо Каґеями. Реалістична, енергійна та надихаюча спортивна манґа, яка показує, що командна робота та наполегливість можуть перемогти природний талант.', 'https://cdn.myanimelist.net/images/manga/1/148237l.jpg', 140000, 23000),
(26, 'Black Clover', 'Чорна конюшина', 2015, 'Ongoing', 35, 360, 'Manga', 'Shounen', 'Аста, хлопець без магії у світі магів, отримує антимагічний гримуар і вступає в лицарський загін «Чорний Бик». Разом зі своїм суперником Юно, генієм вітряної магії, вони борються з демонами та прагнуть стати Королем Чарівників. Класична сьонен, яка бере найкраще від Naruto та Fairy Tail.', 'https://cdn.myanimelist.net/images/manga/2/171910l.jpg', 120000, 19000),
(27, 'The Promised Neverland', 'Обіцяний Неверленд', 2016, 'Completed', 20, 181, 'Manga', 'Shounen', 'Діти в сиротинці Грейс Філд живуть щасливо, поки не дізнаються, що їх вирощують на мясо для демонів. Емма, Норман та Рей планують масову втечу. Психологічний трилер про виживання, інтелект і жертви. Перша арка — один із найкращих початків у манзі, хоча подальші арки отримали неоднозначні відгуки.', 'https://cdn.myanimelist.net/images/manga/3/207449l.jpg', 160000, 26000),
(28, 'Dr. Stone', 'Доктор Стоун', 2017, 'Completed', 26, 232, 'Manga', 'Shounen', 'Геній науки Сенкуо Ішігамі пробуджується через 3700 років після того, як все людство скамяніло. Він вирішує відродити цивілізацію за допомогою науки, створюючи динаміт, електрику та телефони в камяному віці. Наукова пригода, де кожен винахід детально пояснюється.', 'https://cdn.myanimelist.net/images/manga/2/199887l.jpg', 135000, 22000),
(29, 'Komi Can''t Communicate', 'Комі не може спілкуватися', 2016, 'Ongoing', 30, 430, 'Manga', 'Shounen', 'Комі Шоко — красуня, яку всі вважають недосяжною. Але насправді вона страждає від серйозного комунікаційного розладу. Її однокласник Хітохіто Тадано вирішує допомогти їй досягти мети: завести 100 друзів. Тепла, смішна і реалістична історія про тривожність і дружбу.', 'https://cdn.myanimelist.net/images/manga/3/211225l.jpg', 100000, 16000),
(30, 'Rent-A-Girlfriend', 'Орендована дівчина', 2017, 'Ongoing', 30, 300, 'Manga', 'Shounen', 'Студент Кадзуя після розриву в розпачі орендує дівчину через додаток. Чізуру Мідзухара ідеально виконує свою роль, але через серію збігів їхні сімї та друзі вважають їх справжньою парою. Романтична комедія з незграбними персонажами та безліччю непорозумінь. Популярна, але спірна серед читачів через повільний розвиток.', 'https://cdn.myanimelist.net/images/manga/3/214785l.jpg', 95000, 15000);

-- ---------- АВТОРИ ----------
INSERT INTO author (id, name, bio) VALUES
(1, 'Kentaro Miura', 'Автор безсмертного темного фентезі "Berserk". Помер у 2021 році, але його творіння живе вічно. Його деталізація та емоційна глибина вплинули на Without число художників по всьому світу.'),
(2, 'Takehiko Inoue', 'Легендарний мангака, автор "Slam Dunk" та "Vagabond". Його малюнок еволюціонував від спортивного до справжнього мистецтва. "Vagabond" вважається одним з найкрасивіших коміксів усіх часів.'),
(3, 'ONE', 'Таємничий автор, що почав малювати "One Punch Man" як веб-комікс поганим малюнком, який привернув увагу своїм гумором та персонажами. Також автор "Mob Psycho 100".'),
(4, 'Tatsuki Fujimoto', 'Божевільний геній сучасної манґи, автор "Chainsaw Man" та "Fire Punch". Відомий непередбачуваними сюжетними поворотами, кінематографічними панелями та любовю до фільмів.'),
(5, 'Chugong', 'Корейський автор, який створив феномен "Solo Leveling". Його веб-роман став основою для манхви, яка підкорила світ.'),
(6, 'Eiichiro Oda', 'Творець "One Piece", найпродаванішої манґи в історії. Відомий своєю шаленою робочою етикою, прихованими пасхалками та неймовірною світобудовою.'),
(7, 'Hajime Isayama', 'Автор "Attack on Titan". Відомий своїм брудним малюнком, але геніальним сюжетом та вмінням шокувати читачів. Його історія змінила жанр темного фентезі.'),
(8, 'Yoshihiro Togashi', 'Автор "Hunter x Hunter" та "Yu Yu Hakusho". Легенда, яка через хворобу часто бере перерви, але кожна глава — подія. Його система Nen є еталоном.'),
(9, 'Masashi Kishimoto', 'Автор "Naruto". Після завершення основної серії працював над "Boruto" як супервайзер. Вплинув на ціле покоління манґак.'),
(10, 'Akira Toriyama', 'Автор "Dragon Ball". Батько сучасного шьонену. Помер у 2024 році, залишивши безсмертний спадок.'),
(11, 'Hiromu Arakawa', 'Авторка "Fullmetal Alchemist". Відома своїм гумором, добре прописаними персонажами та здатністю балансувати серйозне і смішне.'),
(12, 'Koyoharu Gotouge', 'Авторка "Demon Slayer". Її робота стала світовим феноменом після аніме-адаптації. Пише емоційні історії з простими, але ефектними техніками.'),
(13, 'Ken Wakui', 'Автор "Tokyo Revengers". До манґи працював в індустрії моди.'),
(14, 'Muneyuki Kaneshiro', 'Автор сценарію "Blue Lock". Відомий психологічними спортивними творами.'),
(15, 'Aka Akasaka', 'Автор "Kaguya-sama" та "Oshi no Ko". Майстер жанрових перевертнів та соціальних коментарів.'),
(16, 'Haruichi Furudate', 'Автор "Haikyuu!!". Ідеально зобразив дух волейболу та командну роботу.'),
(17, 'Yūki Tabata', 'Автор "Black Clover". Відкрито надихався "Naruto".'),
(18, 'Kaiu Shirai', 'Автор сценарію "The Promised Neverland". Разом з ілюстратором створили одну з найкращих перших арок.'),
(19, 'Riichiro Inagaki', 'Автор сценарію "Dr. Stone". Має ступінь з економіки, але захоплюється наукою.'),
(20, 'Tomohito Oda', 'Автор "Komi Can''t Communicate".'),
(21, 'Reiji Miyajima', 'Автор "Rent-A-Girlfriend".');

-- ---------- ЗВ'ЯЗКИ МАНГА-АВТОРИ ----------
INSERT INTO manga_author (manga_id, author_id) VALUES
(1,1),(2,2),(3,3),(4,4),(5,5),(6,4),(7,7),(8,9),(9,10),(10,11),
(11,16),(12,1),(13,7),(14,8),(15,10),(16,8),(17,6),(18,11),(19,12),(20,13),
(21,14),(22,15),(23,15),(24,3),(25,16),(26,17),(27,18),(28,19),(29,20),(30,21);

-- ---------- ПЕРСОНАЖІ  ----------
INSERT INTO character (id, name, full_name, gender, age, description, image_url, favorites) VALUES
(1, 'Frieren', 'Frieren', 'Female', 1000, 'Ельфійка-маг, колишня супутниця героя Хіммеля. Через довге життя вона не сприймає людські емоції серйозно, але після смерті друзів вирушає в подорож, щоб зрозуміти їх. Спокійна, раціональна, але іноді безтактна. Обожнює їсти та колекціонувати заклинання.', 'https://cdn.myanimelist.net/images/characters/7/448269l.jpg', 45000),
(2, 'Fern', 'Fern', 'Female', 18, 'Учениця Фрірен, взята на виховання магом Гайтером після війни. Серйозна, відповідальна, часто сварить Фрірен за лінощі. Чудово володіє магією та дуже вправна в побуті. Вважає Фрірен своєю прийомною матірю.', 'https://cdn.myanimelist.net/images/characters/8/448270l.jpg', 28000),
(3, 'Stark', 'Stark', 'Male', 18, 'Юний воїн, учень Айзена. Незважаючи на мякий характер та боягузтво, володіє величезною фізичною силою. Здатний захистити друзів у критичний момент. Має брата, з яким у нього складні стосунки.', 'https://cdn.myanimelist.net/images/characters/9/448271l.jpg', 21000),
(4, 'Yuji Itadori', 'Yuji Itadori', 'Male', 16, 'Головний герой Jujutsu Kaisen. Володіє неймовірною фізичною силою завдяки тренуванням діда. Після того, як проковтнув палець Сукуни, став носієм прокляття. Має добре серце та бажання дарувати людям гідну смерть.', 'https://cdn.myanimelist.net/images/characters/2/423325l.jpg', 78000),
(5, 'Satoru Gojo', 'Satoru Gojo', 'Male', 28, 'Найсильніший маг дзюдзюцу в світі. Вчитель у Токійському технікумі. Носить повязку на очах, щоб контролювати свої безмежні Шість Очей. Зухвалий, самовпевнений, але безмежно відданий своїм учням. Його техніка «Безмежність» робить його практично невразливим.', 'https://cdn.myanimelist.net/images/characters/16/422488l.jpg', 125000),
(6, 'Eren Yeager', 'Eren Yeager', 'Male', 19, 'Головний герой Attack on Titan. Після того, як титани зруйнували його рідне місто, він присягнув знищити всіх титанів. Має здатність перетворюватися на Титана Атаки. З часом його прагнення свободи перетворюється на одержимість, що призводить до трагедії.', 'https://cdn.myanimelist.net/images/characters/14/313097l.jpg', 92000),
(7, 'Mikasa Ackerman', 'Mikasa Ackerman', 'Female', 19, 'Прийомна сестра Ерена, одна з найсильніших солдатів Розвідкорпусу. Має акерманське походження, що дає їй надлюдські бойові здібності. Спокійна, рішуча та неймовірно віддана Ерену, готова захищати його ціною власного життя.', 'https://cdn.myanimelist.net/images/characters/13/315813l.jpg', 88000),
(8, 'Loid Forger', 'Loid Forger', 'Male', 30, 'Найкращий шпигун Заходу під кодовим імям «Світанок». Для місії «Стрикс» створює фальшиву сімю, беручи образ психіатра. Майстер маскування, бойових мистецтв та шпигунських технік. Поступово його ролі батька та чоловіка стають реальними.', 'https://cdn.myanimelist.net/images/characters/14/460298l.jpg', 54000),
(9, 'Anya Forger', 'Anya Forger', 'Female', 6, 'Телепатка, яка втекла з лабораторії. Удочерена Лойдом для місії. Вміє читати думки, але не завжди розуміє дорослі поняття. Обожнює арахіс та мультсеріал «Шпигун війни». Намагається допомогти батькам, часто створюючи комічні ситуації.', 'https://cdn.myanimelist.net/images/characters/9/460299l.jpg', 89000),
(10, 'Yor Forger', 'Yor Forger', 'Female', 27, 'Наймана вбивця «Шипова Принцеса». У цивільному житті працює в мерії. Стає фальшивою дружиною Лойда. Незграбна в соціальних ситуаціях, але смертельно небезпечна. Хоче стати кращою дружиною та матірю, незважаючи на свою криваву роботу.', 'https://cdn.myanimelist.net/images/characters/2/460300l.jpg', 62000),
(11, 'Tanjiro Kamado', 'Tanjiro Kamado', 'Male', 15, 'Головний герой Demon Slayer. Після вбивства сімї демоном і перетворення сестри Недзуко на демона, стає мисливцем, щоб знайти ліки. Має надзвичайно гострий нюх і техніку дихання води. Добрий, співчутливий, але непохитний у битві.', 'https://cdn.myanimelist.net/images/characters/2/386497l.jpg', 28700),
(12, 'Nezuko Kamado', 'Nezuko Kamado', 'Female', 14, 'Сестра Танджіро, перетворена на демона. На відміну від інших демонів, вона не їсть людей і захищає людей. Спілкується мимриками та жестами. Володіє унікальною демонічною силою крові. Спить у спеціальній коробці за спиною брата.', 'https://cdn.myanimelist.net/images/characters/16/386498l.jpg', 24500),
(13, 'Edward Elric', 'Edward Elric', 'Male', 16, 'Сталевий Алхімік, наймолодший державний алхімік в історії. Втратив руку та ногу при спробі воскресити матір. Має автомейл-протези. Запальний, низького зросту (що його дратує). Шукає Філософський камінь разом з братом.', 'https://cdn.myanimelist.net/images/characters/2/273227l.jpg', 26700),
(14, 'Alphonse Elric', 'Alphonse Elric', 'Male', 15, 'Молодший брат Едварда. Втратив усе тіло під час забороненої трансмутації, а його душа привязана до обладунків. Спокійний, розважливий, часто стримує гарячого брата. Любить тварин (особливо котів) та чудово готує.', 'https://cdn.myanimelist.net/images/characters/3/273228l.jpg', 19800),
(15, 'Izuku Midoriya', 'Izuku Midoriya', 'Male', 16, 'Головний герой My Hero Academia. Народився без примхи, але отримав One For All від Все Могта. Ведеться детальні записи про героїв. Дуже емоційний, часто плаче. Намагається використовувати примху розумно, а не лише силою.', 'https://cdn.myanimelist.net/images/characters/9/320992l.jpg', 21000),
(16, 'Monkey D. Luffy', 'Monkey D. Luffy', 'Male', 19, 'Капітан Піратів Соломяного Капелюха. Зїв Гумовий фрукт, ставши гумовою людиною. Мріє стати Королем піратів. Наївний, жадібний до їжі, але з надзвичайно сильною волею та відданістю друзям. Не вміє плавати.', 'https://cdn.myanimelist.net/images/characters/2/275719l.jpg', 37800),
(17, 'Roronoa Zoro', 'Roronoa Zoro', 'Male', 21, 'Перший помічник та мечник команди. Володіє стилем трьох мечів. Мріє стати найсильнішим мечником у світі. Має жахливе почуття напрямку. Суворий, але відданий команді та своїй цілі.', 'https://cdn.myanimelist.net/images/characters/14/275720l.jpg', 29800),
(18, 'Denji', 'Denji', 'Male', 16, 'Головний герой Chainsaw Man. Після злиття з демоном-бензопилою Почітою стає Людиною-бензопилою. Мріє про просте життя: їсти тости з джемом, мати дівчину. Наївний, вуличний, але має добре серце.', 'https://cdn.myanimelist.net/images/characters/16/459893l.jpg', 22300),
(19, 'Sung Jinwoo', 'Sung Jinwoo', 'Male', 25, 'Головний герой Solo Leveling. Найслабший мисливець E-рангу, який після отримання «Системи» стає найсильнішим. Володіє армією тіней. Серйозний, рішучий, відданий сімї.', 'https://cdn.myanimelist.net/images/characters/9/460295l.jpg', 28700),
(20, 'Momo Ayase', 'Momo Ayase', 'Female', 16, 'Одна з головних героїв Dandadan. Вірить у привидів, але не в інопланетян. Після зустрічі з духом отримує психічні здібності. Впевнена, сильна, з почуттям справедливості.', 'https://cdn.myanimelist.net/images/characters/9/538181l.jpg', 12400),
(21, 'Ken Takakura (Okarun)', 'Ken Takakura (Okarun)', 'Male', 16, 'Однокласник Момо, який вірить в інопланетян. Після зустрічі з прибульцями отримує силу, повязану з привидами. Соромязливий, отакувати, але сміливий у бійках.', 'https://cdn.myanimelist.net/images/characters/16/538183l.jpg', 11800),
(22, 'Thorfinn', 'Thorfinn', 'Male', 22, 'Головний герой Vinland Saga. Син легендарного воїна Торса. Спочатку прагне помсти вбивці батька Аскеладду, але з роками переосмислює життя та шукає мир. Стає рабом на фермі, а потім лідером колонії Вінланд.', 'https://cdn.myanimelist.net/images/characters/11/298269l.jpg', 19800),
(23, 'Kaguya Shinomiya', 'Kaguya Shinomiya', 'Female', 17, 'Віце-президент студради в аніме Kaguya-sama. Спадкоємиця корпорації Шіномія. Горда, розумна, але незграбна в романтиці. Закохана в Міюкі, але намагається змусити його зізнатися першим.', 'https://cdn.myanimelist.net/images/characters/8/385983l.jpg', 19900),
(24, 'Miyuki Shirogane', 'Miyuki Shirogane', 'Male', 17, 'Президент студради. Вважається генієм, але насправді багато працює, щоб підтримувати статус. Бідний, на відміну від Кагуї. Закоханий у Каґую, але надто гордий, щоб зізнатися.', 'https://cdn.myanimelist.net/images/characters/11/385984l.jpg', 18700),
(25, 'Shigeo Kageyama', 'Shigeo "Mob" Kageyama', 'Male', 14, 'Головний герой Mob Psycho 100. Екстрасенс неймовірної сили, але намагається жити звичайним життям. Емоційно пригнічений, тому що боїться своїх сил. Коли його емоції досягають 100%, він вивільняє міць. Працює на шарлатана Рейгена.', 'https://cdn.myanimelist.net/images/characters/7/299837l.jpg', 17600),
(26, 'Kosei Arima', 'Kosei Arima', 'Male', 17, 'Головний герой Your Lie in April. Піаніст-вундеркінд, який після смерті матері втратив здатність чути звук фортепіано. Зустріч із вільною скрипалькою Каорі допомагає йому повернутися до музики.', 'https://cdn.myanimelist.net/images/characters/15/280432l.jpg', 16500),
(27, 'Lelouch Lamperouge', 'Lelouch Lamperouge', 'Male', 18, 'Головний герой Code Geass. Вигнаний принц Британської імперії. Отримує силу Ґіаса — здатність підкорювати будь-чию волю. Стає таємничим лідером повстанців «Нуль». Геніальний стратег.', 'https://cdn.myanimelist.net/images/characters/7/299809l.jpg', 25600),
(28, 'Spike Spiegel', 'Spike Spiegel', 'Male', 27, 'Головний герой Cowboy Bebop. Колишній член мафії, нині мисливець за головами. Майстер бойових мистецтв, пілот та стрілець. Флегматичний, любить сигарети та музику. Переслідуваний минулим.', 'https://cdn.myanimelist.net/images/characters/3/299810l.jpg', 23400),
(29, 'David Martinez', 'David Martinez', 'Male', 17, 'Головний герой Cyberpunk: Edgerunners. Хлопець з вулиць Найт-Сіті, який після трагедії встановлює військовий імплант і приєднується до банди «Еджраннерів». Прагне стати легендою, але ціна висока.', 'https://cdn.myanimelist.net/images/characters/16/463888l.jpg', 18700),
(30, 'Subaru Natsuki', 'Subaru Natsuki', 'Male', 18, 'Головний герой Re:Zero. Звичайний NEET, перенесений у фентезійний світ. Має здатність «Повернення смертю» — після смерті він повертається в минуле. Багато страждає, але не здається.', 'https://cdn.myanimelist.net/images/characters/15/295105l.jpg', 18700),
(31, 'Rudeus Greyrat', 'Rudeus Greyrat', 'Male', 20, 'Головний герой Mushoku Tensei. 34-річний NEET, який переродився немовлям у світі магії та мечів. Зберігає спогади минулого життя. Прагне прожити це життя без жалю, ставши найкращим магом.', 'https://cdn.myanimelist.net/images/characters/16/463890l.jpg', 18700),
(32, 'Yoichi Isagi', 'Yoichi Isagi', 'Male', 17, 'Головний герой Blue Lock. Нападник з хорошим позиціонуванням, але не вистачає егоїзму. Поступово розвиває свою «просторову свідомість» та стає грізним гравцем.', 'https://cdn.myanimelist.net/images/characters/16/463889l.jpg', 14000),
(33, 'Aqua Hoshino', 'Aqua Hoshino', 'Male', 17, 'Головний герой Oshi no Ko. Перероджений син ідола Ай Хошіно (колишній лікар Ґоро). Володіє холодним розумом та прагне помститися за вбивство матері. Стає актором.', 'https://cdn.myanimelist.net/images/characters/16/463891l.jpg', 15000),
(34, 'Killua Zoldyck', 'Killua Zoldyck', 'Male', 14, 'Один з головних героїв Hunter x Hunter. Спадкоємець родини кілерів Золдік, який втік з дому. Кращий друг Гона. Володіє блискавичною швидкістю та електричними здібностями.', 'https://cdn.myanimelist.net/images/characters/3/466874l.jpg', 26000),
(35, 'Gon Freecss', 'Gon Freecss', 'Male', 14, 'Головний герой Hunter x Hunter. Хлопець, який шукає свого батька-Мисливця. Володіє неймовірним потенціалом та чуттям. Наївний, але вольовий.', 'https://cdn.myanimelist.net/images/characters/3/466875l.jpg', 28000),
(36, 'Light Yagami', 'Light Yagami', 'Male', 17, 'Головний герой Death Note. Геніальний школяр, який знаходить зошит смерті. Вирішує стати богом нового світу під псевдонімом «Кіра». Переконаний у своїй правоті.', 'https://cdn.myanimelist.net/images/characters/3/466876l.jpg', 34000),
(37, 'L', 'L', 'Male', 24, 'Найбільший детектив у світі. Суперник Лайта. Ексцентричний, любить солодощі, сидить у незвичних позах. Має надзвичайний інтелект.', 'https://cdn.myanimelist.net/images/characters/3/466877l.jpg', 32000),
(38, 'Naruto Uzumaki', 'Naruto Uzumaki', 'Male', 17, 'Головний герой Naruto. Джинчуурікі Девятихвостого. Мріє стати Хокаге. Гіперактивний, але вірний навіть найзапеклішим ворогам.', 'https://cdn.myanimelist.net/images/characters/3/466878l.jpg', 40000),
(39, 'Sasuke Uchiha', 'Sasuke Uchiha', 'Male', 17, 'Останній виживший з клану Учіха. Мріє помститися своєму братові Ітачі. Емоційно закритий, холодний, але глибоко травмований.', 'https://cdn.myanimelist.net/images/characters/3/466879l.jpg', 32000),
(40, 'Goku', 'Son Goku', 'Male', 35, 'Головний герой Dragon Ball. Сайян, який виріс на Землі. Постійно шукає сильних супротивників. Наївний, любить їсти та битися. Врятував Землю незліченну кількість разів.', 'https://cdn.myanimelist.net/images/characters/3/466880l.jpg', 45000);

-- ---------- ЗВ'ЯЗКИ АНІМЕ-ЖАНРИ ----------
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

-- ---------- ЗВ'ЯЗКИ АНІМЕ-ПЕРСОНАЖІ ----------
INSERT INTO anime_character (anime_id, character_id, role, is_main) VALUES
(1,1,'Main',1),(1,2,'Main',1),(1,3,'Main',1),
(2,4,'Main',1),(2,5,'Supporting',1),
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
(21,31,'Main',1),
(22,32,'Main',1),
(23,33,'Main',1),
(34,36,'Main',1),(34,37,'Supporting',0),
(35,38,'Main',1),(35,39,'Supporting',1),
(36,40,'Main',1),
(37,34,'Main',1),(37,35,'Main',1);

-- ---------- ЗВ'ЯЗКИ МАНГА-ЖАНРИ ----------
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

-- ---------- ЗВ'ЯЗКИ МАНГА-ПЕРСОНАЖІ ----------
INSERT INTO manga_character (manga_id, character_id, role, is_main) VALUES
(1,1,'Main',1),(1,2,'Main',1),(1,3,'Main',1),
(4,18,'Main',1),
(5,19,'Main',1),
(6,4,'Main',1),(6,5,'Supporting',1),
(7,6,'Main',1),(7,7,'Main',1),
(8,38,'Main',1),(8,39,'Main',1),
(9,40,'Main',1),
(10,15,'Main',1),
(11,8,'Main',1),(11,9,'Main',1),(11,10,'Main',1),
(12,1,'Main',1),(12,2,'Main',1),(12,3,'Main',1),
(13,22,'Main',1),
(14,36,'Main',1),(14,37,'Supporting',0),
(15,36,'Main',1),(15,37,'Supporting',0),
(16,34,'Main',1),(16,35,'Main',1),
(17,16,'Main',1),(17,17,'Main',1),
(18,13,'Main',1),(18,14,'Main',1),
(19,11,'Main',1),(19,12,'Main',1),
(20,4,'Main',1),
(21,32,'Main',1),
(22,33,'Main',1),
(23,23,'Main',1),(23,24,'Main',1),
(24,25,'Main',1),
(25,32,'Main',1),
(26,4,'Main',1),
(27,4,'Main',1),
(28,4,'Main',1),
(29,4,'Main',1),
(30,4,'Main',1);

-- ---------- ЗВ'ЯЗКИ АНІМЕ-МАНГА ----------
INSERT INTO anime_manga (anime_id, manga_id) VALUES
(1,12),  -- Frieren
(2,6),   -- Jujutsu Kaisen
(3,7),   -- Attack on Titan
(4,11),  -- Spy x Family
(5,19),  -- Demon Slayer
(6,18),  -- Fullmetal Alchemist
(7,10),  -- My Hero Academia
(8,17),  -- One Piece
(9,4),   -- Chainsaw Man
(10,5),  -- Solo Leveling
(12,13), -- Vinland Saga
(13,23), -- Kaguya-sama
(14,24), -- Mob Psycho 100
(15,4),  -- Your Lie in April (немає манги в нашому списку, пропустимо але можна додати)
(22,21), -- Blue Lock
(23,22), -- Oshi no Ko
(26,20), -- Tokyo Revengers
(27,4),  -- Horimiya (немає)
(28,28), -- Dr. Stone
(29,27), -- The Promised Neverland
(30,4),  -- Parasyte (немає)
(34,15), -- Death Note
(35,8),  -- Naruto
(36,9),  -- Dragon Ball
(37,16), -- Hunter x Hunter
(42,4),  -- Bleach (немає)
(43,25), -- Haikyuu!!
(44,26), -- Black Clover
(45,4),  -- Sword Art Online (немає)
(46,4),  -- Overlord (немає)
(47,4),  -- Slime (немає)
(48,4);  -- Konosuba (немає)

-- ---------- РЕЙТИНГИ  ----------
-- Додаємо рейтинги для всіх аніме від різних користувачів
INSERT INTO rating (anime_id, user_id, score, is_favorite, status, progress) VALUES
-- Аніме 1-10
(1,1,9.5,1,'Completed',28),(1,2,9.0,1,'Completed',28),(1,3,9.2,1,'Completed',28),(1,4,9.8,1,'Completed',28),(1,5,9.3,1,'Completed',28),
(2,1,9.0,1,'Completed',24),(2,2,9.5,1,'Completed',24),(2,3,8.5,0,'Completed',24),(2,4,9.1,1,'Completed',24),(2,5,8.9,1,'Completed',24),
(3,1,10.0,1,'Completed',88),(3,2,9.8,1,'Completed',88),(3,3,10.0,1,'Completed',88),(3,4,9.7,1,'Completed',88),(3,5,9.9,1,'Completed',88),
(4,2,9.5,1,'Completed',12),(4,3,9.0,1,'Completed',12),(4,4,9.3,1,'Completed',12),(4,5,9.1,1,'Completed',12),(4,6,8.8,0,'Completed',12),
(5,2,9.9,1,'Completed',26),(5,3,9.7,1,'Completed',26),(5,4,9.8,1,'Completed',26),(5,5,9.6,1,'Completed',26),(5,6,9.4,1,'Completed',26),
(6,2,9.8,1,'Completed',64),(6,3,9.7,1,'Completed',64),(6,4,9.9,1,'Completed',64),(6,5,9.5,1,'Completed',64),(6,6,9.6,1,'Completed',64),
(7,2,8.5,0,'Completed',13),(7,3,9.0,1,'Completed',13),(7,4,8.2,0,'Completed',13),(7,5,8.7,1,'Completed',13),(7,6,8.9,1,'Completed',13),
(8,1,9.5,1,'Watching',500),(8,2,9.0,1,'Watching',300),(8,3,9.7,1,'Watching',800),(8,4,9.2,1,'Watching',400),(8,5,9.1,1,'Watching',650),
(9,1,9.0,1,'Completed',12),(9,2,9.2,1,'Completed',12),(9,3,8.7,0,'Completed',12),(9,4,9.4,1,'Completed',12),(9,5,8.9,1,'Completed',12),
(10,1,9.2,1,'Completed',12),(10,2,9.5,1,'Completed',12),(10,3,9.0,1,'Completed',12),(10,4,9.3,1,'Completed',12),(10,5,9.1,1,'Completed',12),
-- Аніме 11-20
(11,1,8.5,1,'Completed',12),(11,2,9.0,1,'Completed',12),(11,3,8.2,0,'Completed',12),(11,4,8.8,1,'Completed',12),(11,5,8.6,1,'Completed',12),
(12,1,9.3,1,'Completed',24),(12,2,9.6,1,'Completed',24),(12,3,9.4,1,'Completed',24),(12,4,9.7,1,'Completed',24),(12,5,9.5,1,'Completed',24),
(13,1,9.1,1,'Completed',12),(13,2,9.4,1,'Completed',12),(13,3,8.9,1,'Completed',12),(13,4,9.2,1,'Completed',12),(13,5,9.0,1,'Completed',12),
(14,1,8.9,1,'Completed',12),(14,2,9.2,1,'Completed',12),(14,3,8.5,0,'Completed',12),(14,4,9.1,1,'Completed',12),(14,5,8.8,1,'Completed',12),
(15,1,9.0,1,'Completed',22),(15,2,9.3,1,'Completed',22),(15,3,8.7,0,'Completed',22),(15,4,9.1,1,'Completed',22),(15,5,8.9,1,'Completed',22),
(16,1,8.7,1,'Completed',23),(16,2,9.1,1,'Completed',23),(16,3,8.4,0,'Completed',23),(16,4,8.9,1,'Completed',23),(16,5,8.6,1,'Completed',23),
(17,1,9.4,1,'Completed',50),(17,2,9.7,1,'Completed',50),(17,3,9.2,1,'Completed',50),(17,4,9.5,1,'Completed',50),(17,5,9.3,1,'Completed',50),
(18,1,9.2,1,'Completed',26),(18,2,9.5,1,'Completed',26),(18,3,8.9,1,'Completed',26),(18,4,9.3,1,'Completed',26),(18,5,9.1,1,'Completed',26),
(19,1,8.8,1,'Completed',10),(19,2,9.3,1,'Completed',10),(19,3,8.5,0,'Completed',10),(19,4,9.0,1,'Completed',10),(19,5,8.7,1,'Completed',10),
(20,1,9.0,1,'Completed',25),(20,2,9.4,1,'Completed',25),(20,3,8.6,0,'Completed',25),(20,4,9.1,1,'Completed',25),(20,5,8.9,1,'Completed',25),
-- Аніме 21-30
(21,1,8.9,1,'Completed',24),(21,2,9.2,1,'Completed',24),(21,3,8.4,0,'Completed',24),(21,4,9.0,1,'Completed',24),(21,5,8.7,1,'Completed',24),
(22,1,8.5,0,'Completed',24),(22,2,9.0,1,'Completed',24),(22,3,8.2,0,'Completed',24),(22,4,8.8,1,'Completed',24),(22,5,8.6,1,'Completed',24),
(23,1,9.1,1,'Completed',11),(23,2,9.5,1,'Completed',11),(23,3,8.9,1,'Completed',11),(23,4,9.3,1,'Completed',11),(23,5,9.0,1,'Completed',11),
(24,1,8.2,0,'Completed',13),(24,2,8.7,1,'Completed',13),(24,3,7.9,0,'Completed',13),(24,4,8.4,1,'Completed',13),(24,5,8.1,0,'Completed',13),
(25,1,8.6,1,'Completed',13),(25,2,9.0,1,'Completed',13),(25,3,8.3,0,'Completed',13),(25,4,8.8,1,'Completed',13),(25,5,8.5,1,'Completed',13),
(26,1,8.4,1,'Completed',37),(26,2,8.8,1,'Completed',37),(26,3,7.5,0,'Completed',37),(26,4,8.2,1,'Completed',37),(26,5,8.0,1,'Completed',37),
(27,1,9.2,1,'Completed',13),(27,2,9.5,1,'Completed',13),(27,3,8.8,1,'Completed',13),(27,4,9.1,1,'Completed',13),(27,5,9.0,1,'Completed',13),
(28,1,8.9,1,'Completed',35),(28,2,9.3,1,'Completed',35),(28,3,8.5,0,'Completed',35),(28,4,9.0,1,'Completed',35),(28,5,8.7,1,'Completed',35),
(29,1,9.0,1,'Completed',23),(29,2,9.4,1,'Completed',23),(29,3,8.6,0,'Completed',23),(29,4,9.1,1,'Completed',23),(29,5,8.8,1,'Completed',23),
(30,1,8.8,1,'Completed',24),(30,2,9.1,1,'Completed',24),(30,3,8.3,0,'Completed',24),(30,4,8.9,1,'Completed',24),(30,5,8.6,1,'Completed',24);

-- ---------- КОМЕНТАРІ ----------
INSERT INTO comments (user_id, anime_id, content, likes, created_at) VALUES
(2,1, 'Frieren — це справжній шедевр. Емоційна та глибока історія. Ніколи не думав, що аніме про ельфів може так зворушити.', 45, CURRENT_TIMESTAMP),
(3,2, 'Jujutsu Kaisen має найкращу анімацію боїв, яку я коли-небудь бачив. MAPPA зробили неймовірну роботу. Годжо - бог.', 32, CURRENT_TIMESTAMP),
(4,3, 'Attack on Titan змінив моє уявлення про аніме. Фінал — це щось неймовірне, хоча багато хто лає останню арку.', 67, CURRENT_TIMESTAMP),
(5,4, 'Spy x Family — ідеальне поєднання комедії, екшену та тепла. Аня найкраща.', 28, CURRENT_TIMESTAMP),
(2,5, 'Demon Slayer: Entertainment District Arc — візуальний феєрверк! ufotable просто боги.', 56, CURRENT_TIMESTAMP),
(3,8, 'One Piece триває вже 20+ років і досі залишається цікавим. Ода — геній. Арка ВаноНі була епічною.', 89, CURRENT_TIMESTAMP),
(1,9, 'Chainsaw Man — божевільний, кривавий і неймовірно захопливий. Чекаю на другий сезон', 34, CURRENT_TIMESTAMP),
(4,10, 'Solo Leveling — мрія кожного геймера. Джіну неймовірно крутий. Аніме-адаптація вийшла чудовою, хоча дещо скорочена.', 41, CURRENT_TIMESTAMP),
(6,13, 'Kaguya-sama — найкраща романтична комедія. Кожна серія змушує посміхнутися.', 23, CURRENT_TIMESTAMP),
(7,34, 'Death Note — класика. Дуель Лайта та L неперевершена.', 56, CURRENT_TIMESTAMP),
(8,35, 'Наруто — моє дитинство. Ніколи не забуду цю подорож.', 78, CURRENT_TIMESTAMP),
(6,40, 'Violet Evergarden вичавила з мене сльози. Найкрасивіше аніме, яке я бачив.', 34, CURRENT_TIMESTAMP);

-- ---------- НОВИНИ ----------
INSERT INTO news (id, title, content, summary, category, image_url, author_id, is_published, published_at) VALUES
(1, 'Офіційно: "Spy x Family" отримає повнометражний фільм у 2026 році', 'Студія CloverWorks та WIT Studio оголосили про спільну роботу над фільмом "Spy x Family: Code White 2". Події відбуватимуться після арки про круїз. Ллойд, Йор та Аня вирушать на курорт, де Аня випадково ковтає мікросхему з секретними даними. Також представлено новий трейлер. Фільм вийде в японських кінотеатрах влітку 2026.', 'Анімаційний фільм про улюблену сімейку шпигунів вийде влітку 2026. Подробиці та трейлер.', 'Фільми', 'https://cdn.myanimelist.net/images/anime/1441/122795l.jpg', 1, 1, '2026-05-10 10:00:00'),
(2, 'Новий сезон "Jujutsu Kaisen" вийде в січні 2027 — підтверджено', 'Під час заходу Jump Festa 2026 оголошено, що третій сезон "Jujutsu Kaisen" адаптує арку «Інцидент у Шібуї» (друга частина). Режисер Шота Гошозоно повертається. Перший ключовий візуал із Юдзі, Меґумі, Нобара та Годжо опубліковано. MAPPA обіцяє ще більш інтенсивну анімацію. Точна дата — 8 січня 2027 року.', 'Третій сезон "Jujutsu Kaisen" вийде в січні 2027, адаптуватиме арку Шібуя. Деталі.', 'Анонси', 'https://cdn.myanimelist.net/images/anime/1171/109222l.jpg', 2, 1, '2026-05-09 14:30:00'),
(3, 'Аніме "Frieren" офіційно продовжено на другий сезон', 'Хіт осені 2023 "Frieren: Beyond Journey''s End" отримав зелене світло на другий сезон. Студія MADHOUSE (ні, MAPPA? Насправді перший сезон робила MADHOUSE) — так, перший сезон робила MADHOUSE, але для другого сезону студія не змінилась. Другий сезон адаптуватиме арки «Іспит першого класу мага» та «Місто мечів». Очікується восени 2026 року.', 'Другий сезон аніме "Frieren" анонсовано на осінь 2026. Арка з іспитами магів.', 'Анонси', 'https://cdn.myanimelist.net/images/anime/1015/138006l.jpg', 3, 1, '2026-05-08 09:15:00'),
(4, 'Студія Kyoto Animation адаптує нову роботу Наоко Ямади', 'Відома режисерка Наоко Ямада ("A Silent Voice", "Liz and the Blue Bird") повертається до студії Kyoto Animation з оригінальним фільмом "Kimi no Iro" ("Твій колір"). Це історія про дівчину, яка бачить кольори емоцій людей. Премєра в японських кінотеатрах запланована на кінець 2026 року. Перший тизер показав неймовірну якість анімації.', 'Новий оригінальний фільм від Kyoto Animation та режисерки Наоко Ямади, осінь 2026.', 'Фільми', 'https://cdn.myanimelist.net/images/anime/1795/95088l.jpg', 1, 1, '2026-05-07 16:20:00'),
(5, 'Фінальний сезон "Demon Slayer" вийде двома частинами', 'Аніме "Demon Slayer: Kimetsu no Yaiba" завершиться фінальним сезоном, який адаптує арки «Тренування стовпів» та «Битва на нескінченному замку». Сезон буде розділено на дві частини: перша частина вийде влітку 2026, друга — взимку 2027. ufotable випустила новий трейлер з демонстрацією битви Токіто vs перша верхня луна.', 'Фінальний сезон Demon Slayer розділять на дві частини, перша — літо 2026.', 'Анонси', 'https://cdn.myanimelist.net/images/anime/1286/99889l.jpg', 5, 1, '2026-05-06 12:00:00'),
(6, 'Опитування: "Fullmetal Alchemist: Brotherhood" визнано найкращим аніме всіх часів за версією NHK', 'Японська телекомпанія NHK провела масштабне опитування серед 150 000 глядачів. "Fullmetal Alchemist: Brotherhood" посів перше місце, випередивши "Steins;Gate" та "Attack on Titan". Також до топ-10 увійшли "Gintama", "Code Geass" та "Hunter x Hunter". Результати викликали жваве обговорення в соцмережах.', 'Fullmetal Alchemist: Brotherhood очолив рейтинг найкращих аніме за версією NHK.', 'Підсумки', 'https://cdn.myanimelist.net/images/anime/1223/96541l.jpg', 4, 1, '2026-05-05 18:45:00'),
(7, 'Нове аніме від автора "Death Note" отримало трейлер', 'Тсугумі Оба (автор історії "Death Note") разом з ілюстратором Такеші Обатою анонсували аніме-адаптацію їхньої нової манги "Platinum End". Студія Signal.MD випустила перший трейлер. Сюжет: хлопець Мірай, який намагався накласти на себе руки, отримує крила ангела та бере участь у битві за місце Бога. Премєра осінь 2026.', 'Нове аніме від творців Death Note — "Platinum End". Трейлер та дата виходу.', 'Трейлери', 'https://cdn.myanimelist.net/images/anime/5/106296l.jpg', 2, 1, '2026-05-04 11:00:00'),
(8, 'One Piece: Ейічіро Ода особисто напише сценарій до фільму', 'Творець "One Piece" Ейічіро Ода заявив, що напише сценарій до нового повнометражного фільму франшизи, вихід якого заплановано на 2027 рік. Фільм буде оригінальною історією, не повязаною з основною мангою, але дія відбуватиметься після арки Ельбаф. Ода опублікував перший начерк персонажа нового антагоніста.', 'Ейічіро Ода особисто напише сценарій нового фільму One Piece. Вихід у 2027.', 'Фільми', 'https://cdn.myanimelist.net/images/anime/6/73245l.jpg', 1, 1, '2026-05-03 09:30:00'),
(9, 'Аніме "Chainsaw Man: Reze Arc" вийде як повнометражний фільм', 'MAPPA підтвердила, що арка "Резе" (наступна після першого сезону) буде адаптована у вигляді повнометражного фільму. Фільм вийде в кінотеатрах Японії вже в грудні 2026. Також показано новий візуал із Резе та Денджі. Режисером виступить Рю Накаяма (режисер першого сезону).', 'Арка "Резе" з Chainsaw Man стане повнометражним фільмом, вихід у грудні 2026.', 'Фільми', 'https://cdn.myanimelist.net/images/anime/1806/126216l.jpg', 3, 1, '2026-05-02 15:00:00'),
(10, 'Спіноф "Naruto" про Мінато Наміказе отримав зелене світло', 'Студія Pierrot анонсувала спіноф-серіал "Naruto: The Yellow Flash", який розповість про молодість Четвертого Хокаге Мінато Наміказе, його стосунки з Кушіною та тренування під керівництвом Джіраї. Аніме вийде ексклюзивно на Disney+ у 2027 році. Масаші Кішімото виступає супервайзером.', 'Спіноф про Мінато Наміказе, Четвертого Хокаге, вийде на Disney+ у 2027.', 'Анонси', 'https://cdn.myanimelist.net/images/anime/13/17405l.jpg', 5, 1, '2026-05-01 08:00:00'),
(11, 'Манга "Hunter x Hunter" повернеться з новими главами в червні', 'Журнал Weekly Shonen Jump оголосив, що Йошихіро Тогаші опублікує 10 нових глав "Hunter x Hunter" починаючи з 10 червня 2026. Це перші нові глави за останні 2 роки. Тогаші коментує: "Намагаюся закінчити арку Чорного Континенту, поки здоровя дозволяє". Фанати по всьому світу святкують.', 'Hunter x Hunter повертається з новими главами в червні 2026 після дворічної перерви.', 'Манга', 'https://cdn.myanimelist.net/images/anime/11/33657l.jpg', 4, 1, '2026-05-01 20:00:00'),
(12, 'Результати Winter 2026: "Solo Leveling Season 2" бє рекорди', 'Другий сезон "Solo Leveling" завершився з найвищим рейтингом на MyAnimeList серед усіх сезонів - 9.15. Аніме побило рекорд за кількістю переглядів на Crunchyroll. Також оголошено про третій сезон, який адаптує арку «Міжнародний рейд гільдії» та вийде в 2027.', 'Solo Leveling Season 2 встановив рекорди популярності, третій сезон анонсовано.', 'Підсумки', 'https://cdn.myanimelist.net/images/anime/1987/135339l.jpg', 2, 1, '2026-04-30 10:00:00');

COMMIT;