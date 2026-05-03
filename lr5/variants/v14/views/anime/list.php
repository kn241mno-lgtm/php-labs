<div class="page">
    <h1>Каталог аніме</h1>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <?php
        $canCreateAnime = false;
        if (isset($_SESSION['user_id'])) {
            try {
                $db = Database::getInstance();
                $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                $rs->execute([':id' => $_SESSION['user_id']]);
                $r = $rs->fetch();
                $canCreateAnime = $r && ($r['role'] === 'admin');
            } catch (Exception $e) { $canCreateAnime = false; }
        }
    ?>
    <?php if ($canCreateAnime): ?>
        <a href="index.php?route=anime/create" class="btn btn-primary">Додати аніме</a>
    <?php endif; ?>

    <div class="layout-row filters-right">
            <aside class="filters">
            <form method="get" action="index.php">
                <input type="hidden" name="route" value="anime/list">
                <div class="form-group">
                    <label>Пошук</label>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($filters['q'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label>Жанр</label>
                    <input type="hidden" name="genres" id="genreInput" value="<?= htmlspecialchars($filters['genre'] ?? '') ?>" />
                    <div class="chips-toggle">
                        <button type="button" class="btn" id="openGenresBtn">Вибрати жанри</button>
                    </div>
                    <div class="filter-chips" id="genreChips" style="display:none;margin-top:8px;">
                        <div class="filter-chip <?= empty($filters['genre']) ? 'active' : '' ?>" data-id="" data-color="#444">Всі жанри</div>
                        <?php foreach ($genres as $g): ?>
                            <?php $color = !empty($g['color']) ? $g['color'] : '#2563eb'; ?>
                            <div class="filter-chip <?= (isset($filters['genre']) && $filters['genre'] == $g['id']) ? 'active' : '' ?>" data-id="<?= $g['id'] ?>" data-color="<?= htmlspecialchars($color) ?>" style="border:1px solid rgba(255,255,255,0.03);"><?= htmlspecialchars($g['name']) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Рік випуску</label>
                    <div class="year-range">
                        <input type="range" id="yearFromRange" min="1965" max="2026" value="<?= (int)($filters['yearFrom'] ?: 1965) ?>">
                        <input type="range" id="yearToRange" min="1965" max="2026" value="<?= (int)($filters['yearTo'] ?: 2026) ?>">
                        <div class="year-values">Від <span id="yearFromDisplay"></span> до <span id="yearToDisplay"></span></div>
                        <input type="hidden" name="yearFrom" id="yearFrom" value="<?= htmlspecialchars($filters['yearFrom'] ?? '') ?>">
                        <input type="hidden" name="yearTo" id="yearTo" value="<?= htmlspecialchars($filters['yearTo'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Студія</label>
                    <input type="hidden" name="studioId" id="studioInput" value="<?= htmlspecialchars($filters['studioId'] ?? '') ?>" />
                    <div class="chips-toggle">
                        <button type="button" class="btn" id="openStudiosBtn">Вибрати студію</button>
                    </div>
                    <div class="filter-chips" id="studioChips" style="display:none;margin-top:8px;">
                        <div class="filter-chip <?= empty($filters['studioId']) ? 'active' : '' ?>" data-id="">Всі студії</div>
                        <?php foreach ($studios as $s): ?>
                            <div class="filter-chip <?= (isset($filters['studioId']) && $filters['studioId'] == $s['id']) ? 'active' : '' ?>" data-id="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Тип</label>
                    <div class="filter-chips" id="typeChips">
                        <input type="hidden" name="type" id="typeInput" value="<?= htmlspecialchars($filters['type'] ?? '') ?>" />
                        <div class="filter-chip <?= empty($filters['type']) ? 'active' : '' ?>" data-val="">Всі</div>
                        <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='TV') ? 'active' : '' ?>" data-val="TV">TV Серіал</div>
                        <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='Movie') ? 'active' : '' ?>" data-val="Movie">Фільм</div>
                        <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='OVA') ? 'active' : '' ?>" data-val="OVA">OVA</div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <div class="filter-chips" id="statusChips">
                        <input type="hidden" name="status" id="statusInput" value="<?= htmlspecialchars($filters['status'] ?? '') ?>" />
                        <div class="filter-chip <?= empty($filters['status']) ? 'active' : '' ?>" data-val="">Всі</div>
                        <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Ongoing') ? 'active' : '' ?>" data-val="Ongoing">Виходить</div>
                        <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Completed') ? 'active' : '' ?>" data-val="Completed">Завершено</div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Сортувати за</label>
                    <select name="sort">
                        <option value="rating" <?= (isset($filters['sort']) && $filters['sort']=='rating')?'selected':'' ?>>Рейтинг</option>
                        <option value="year" <?= (isset($filters['sort']) && $filters['sort']=='year')?'selected':'' ?>>Рік</option>
                        <option value="title" <?= (isset($filters['sort']) && $filters['sort']=='title')?'selected':'' ?>>Заголовок</option>
                        <option value="views" <?= (isset($filters['sort']) && $filters['sort']=='views')?'selected':'' ?>>Перегляди</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button class="btn">Застосувати фільтри</button>
                    <a href="index.php?route=anime/list" class="btn btn--secondary">Скинути</a>
                </div>
            </form>
        </aside>

        <section class="content">
            <div class="card-grid">
                <?php foreach ($anime as $a): ?>
            <div class="card" style="position:relative">
                <?php if (!empty($a['status']) && strtolower($a['status']) === 'ongoing'): ?>
                    <div class="badge">Виходить</div>
                <?php endif; ?>
                <?php $cover = !empty($a['cover_url']) ? htmlspecialchars($a['cover_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'; ?>
                    <a href="index.php?route=anime/view&id=<?= $a['id'] ?>"><img src="<?= $cover ?>" alt="<?= htmlspecialchars($a['title']) ?>" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
                <div style="padding-top:6px">
                    <h3 class="card__title"><?= htmlspecialchars($a['title_ua'] ?: $a['title']) ?></h3>
                    <p class="card__text"><?= htmlspecialchars(mb_substr($a['description'] ?? '',0,120)) ?></p>
                    <!-- details available by clicking the cover/title -->
                </div>
                <div class="rating-pill"><?= round($a['rating'] ?? 0,1) ?></div>
                <!-- admin controls are available on the anime detail page only -->
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
        // UI wiring: chips + toggles + year dual-range
    (function(){
        function wire(chipsId, inputId, attr){
            var container = document.getElementById(chipsId);
            if(!container) return;
            var input = document.getElementById(inputId);
            container.addEventListener('click', function(e){
                var chip = e.target.closest('.filter-chip');
                if(!chip) return;
                container.querySelectorAll('.filter-chip').forEach(function(c){ c.classList.remove('active'); });
                chip.classList.add('active');
                var val = chip.getAttribute(attr) || chip.getAttribute('data-val') || chip.getAttribute('data-id') || '';
                if(input) input.value = val;
            });
        }
        // toggles
        document.getElementById('openGenresBtn').addEventListener('click', function(){
            var el = document.getElementById('genreChips');
            el.style.display = (el.style.display === 'none') ? 'flex' : 'none';
        });
        document.getElementById('openStudiosBtn').addEventListener('click', function(){
            var el = document.getElementById('studioChips');
            el.style.display = (el.style.display === 'none') ? 'flex' : 'none';
        });

        wire('genreChips','genreInput','data-id');
        wire('studioChips','studioInput','data-id');
        wire('typeChips','typeInput','data-val');
        wire('statusChips','statusInput','data-val');

        // year dual-range
        var yFromRange = document.getElementById('yearFromRange');
        var yToRange = document.getElementById('yearToRange');
        var yFromHidden = document.getElementById('yearFrom');
        var yToHidden = document.getElementById('yearTo');
        var yFromDisplay = document.getElementById('yearFromDisplay');
        var yToDisplay = document.getElementById('yearToDisplay');

        function syncYears(){
            var from = parseInt(yFromRange.value,10);
            var to = parseInt(yToRange.value,10);
            if(from > to){
                // keep difference minimal: swap
                var tmp = from; from = to; to = tmp;
            }
            yFromHidden.value = from;
            yToHidden.value = to;
            yFromDisplay.textContent = from;
            yToDisplay.textContent = to;
            yFromRange.value = from;
            yToRange.value = to;
        }
        yFromRange.addEventListener('input', syncYears);
        yToRange.addEventListener('input', syncYears);
        // initialize
        syncYears();
    })();
</script>
