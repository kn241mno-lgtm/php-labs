<div class="page">
    <h1>Каталог манги</h1>
    <?php
        $canCreateManga = false;
        if (isset($_SESSION['user_id'])) {
            try {
                $db = Database::getInstance();
                $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                $rs->execute([':id' => $_SESSION['user_id']]);
                $r = $rs->fetch();
                $canCreateManga = $r && ($r['role'] === 'admin');
            } catch (Exception $e) { $canCreateManga = false; }
        }
    ?>
    <?php if ($canCreateManga): ?>
        <a href="index.php?route=manga/create" class="btn btn-primary">Додати мангу</a>
    <?php endif; ?>

    <div class="layout-row filters-right">
        <aside class="filters">
            <form method="get" action="index.php">
                <input type="hidden" name="route" value="manga/list">
                <div class="form-group">
                    <label>Пошук</label>
                    <input type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label>Рік випуску</label>
                    <div class="year-range range-wrap">
                        <div class="range-track"></div>
                        <input type="range" id="m_yearFromRange" min="1900" max="2100" value="<?= (int)($filters['yearFrom'] ?: 1900) ?>">
                        <input type="range" id="m_yearToRange" min="1900" max="2100" value="<?= (int)($filters['yearTo'] ?: 2100) ?>">
                        <div class="year-values">Від <span id="m_yearFromDisplay"></span> до <span id="m_yearToDisplay"></span></div>
                        <input type="hidden" name="yearFrom" id="m_yearFrom" value="<?= htmlspecialchars($filters['yearFrom'] ?? '') ?>">
                        <input type="hidden" name="yearTo" id="m_yearTo" value="<?= htmlspecialchars($filters['yearTo'] ?? '') ?>">
                    </div>
                </div>

                    <script>
                        (function(){
                            // year dual-range for manga
                            var yFromRange = document.getElementById('m_yearFromRange');
                            var yToRange = document.getElementById('m_yearToRange');
                            var yFromHidden = document.getElementById('m_yearFrom');
                            var yToHidden = document.getElementById('m_yearTo');
                            var yFromDisplay = document.getElementById('m_yearFromDisplay');
                            var yToDisplay = document.getElementById('m_yearToDisplay');
                            var track = document.querySelector('#mGenresOverlay .range-track') || document.querySelector('.range-track');
                            function syncYears(){
                                var min = parseInt(yFromRange.min,10);
                                var max = parseInt(yFromRange.max,10);
                                var from = parseInt(yFromRange.value,10);
                                var to = parseInt(yToRange.value,10);
                                if(from > to){ var tmp = from; from = to; to = tmp; }
                                yFromHidden.value = from;
                                yToHidden.value = to;
                                yFromDisplay.textContent = from;
                                yToDisplay.textContent = to;
                                if(track){
                                    var left = ((from - min) / (max - min)) * 100;
                                    var right = ((to - min) / (max - min)) * 100;
                                    track.style.left = left + '%';
                                    track.style.width = Math.max(0, right - left) + '%';
                                }
                            }
                            if(yFromRange && yToRange){
                                yFromRange.addEventListener('input', syncYears);
                                yToRange.addEventListener('input', syncYears);
                                syncYears();
                            }

                            // overlay for manga genres
                            function setupOverlay(btnId, overlayId, applyId, closeId, selectAllId, clearAllId, hiddenInputId){
                                var btn = document.getElementById(btnId);
                                var overlay = document.getElementById(overlayId);
                                var apply = document.getElementById(applyId);
                                var close = document.getElementById(closeId);
                                var selectAll = document.getElementById(selectAllId);
                                var clearAll = document.getElementById(clearAllId);
                                var hidden = document.getElementById(hiddenInputId);
                                if(!btn || !overlay) return;
                                btn.addEventListener('click', function(){ overlay.style.display = (overlay.style.display === 'none' || overlay.style.display === '') ? 'block' : 'none'; });
                                if(close) close.addEventListener('click', function(){ overlay.style.display = 'none'; });
                                if(selectAll) selectAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = true; }); });
                                if(clearAll) clearAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = false; }); });
                                if(apply) apply.addEventListener('click', function(){ var vals=[]; overlay.querySelectorAll('input[type=checkbox]:checked').forEach(function(c){ vals.push(c.value); }); if(hidden) hidden.value = vals.join(','); overlay.style.display='none'; });
                                document.addEventListener('click', function(e){ if(!overlay.contains(e.target) && !btn.contains(e.target)){ overlay.style.display = 'none'; } });
                            }
                            setupOverlay('openMGenresBtn','mGenresOverlay','mApplyGenres','mCloseGenres','mGenreSelectAll','mGenreClearAll','m_genreInput');
                            // overlay search for manga genres
                            function setupOverlaySearch(overlayId, searchId){
                                var overlay = document.getElementById(overlayId);
                                var input = document.getElementById(searchId);
                                if(!overlay || !input) return;
                                input.addEventListener('input', function(){
                                    var q = input.value.trim().toLowerCase();
                                    overlay.querySelectorAll('.overlay-checkbox').forEach(function(lb){
                                        var text = lb.textContent.trim().toLowerCase();
                                        lb.style.display = q === '' || text.indexOf(q) !== -1 ? 'block' : 'none';
                                    });
                                });
                            }
                            setupOverlaySearch('mGenresOverlay','m_genreSearch');
                        })();
                    </script>
                <div class="form-group">
                    <label>Статус</label>
                    <select name="status">
                        <option value="">Всі статуси</option>
                        <option value="Ongoing" <?= (isset($filters['status']) && $filters['status']=='Ongoing')?'selected':'' ?>>Ongoing</option>
                        <option value="Completed" <?= (isset($filters['status']) && $filters['status']=='Completed')?'selected':'' ?>>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Жанри</label>
                    <input type="hidden" name="genres" id="m_genreInput" value="<?= htmlspecialchars($filters['genre'] ?? '') ?>" />
                    <div class="chips-toggle">
                        <button type="button" class="filter-pill" id="openMGenresBtn">Жанри ▾</button>
                    </div>
                    <div class="overlay-panel" id="mGenresOverlay" style="display:none;">
                        <div class="overlay-header"><span>Жанри</span><input type="search" id="m_genreSearch" class="overlay-search" placeholder="Пошук..."></div>
                        <div class="overlay-body">
                            <div style="margin-bottom:8px"><button type="button" class="btn" id="mGenreSelectAll">Позначити всі</button> <button type="button" class="btn" id="mGenreClearAll">Зняти всі</button></div>
                            <?php if(!empty($genres)): foreach($genres as $g): ?>
                                <label class="overlay-checkbox"><input type="checkbox" value="<?= $g['id'] ?>" <?php if(isset($filters['genre']) && $filters['genre']!=='' && in_array($g['id'], array_filter(array_map('intval', explode(',', $filters['genre']))))): ?>checked<?php endif; ?>> <?= htmlspecialchars($g['name']) ?></label>
                            <?php endforeach; endif; ?>
                        </div>
                        <div class="overlay-actions">
                            <button type="button" class="btn" id="mApplyGenres">Застосувати</button>
                            <button type="button" class="btn btn--secondary" id="mCloseGenres">Закрити</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Тип</label>
                    <select name="type">
                        <option value="">Всі типи</option>
                        <option value="Manga" <?= (isset($filters['type']) && $filters['type']=='Manga')?'selected':'' ?>>Manga</option>
                        <option value="Manhwa" <?= (isset($filters['type']) && $filters['type']=='Manhwa')?'selected':'' ?>>Manhwa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Сортувати за</label>
                    <select name="sort">
                        <option value="title" <?= (isset($filters['sort']) && $filters['sort']=='title')?'selected':'' ?>>Заголовок</option>
                        <option value="year" <?= (isset($filters['sort']) && $filters['sort']=='year')?'selected':'' ?>>Рік</option>
                        <option value="views" <?= (isset($filters['sort']) && $filters['sort']=='views')?'selected':'' ?>>Перегляди</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button class="btn">Застосувати фільтри</button>
                    <a href="index.php?route=manga/list" class="btn btn--secondary">Скинути</a>
                </div>
            </form>
        </aside>

        <section class="content">
            <div class="card-grid">
        <?php foreach ($manga as $m): ?>
            <div class="card" style="position:relative">
                <?php $mcover = !empty($m['cover_url']) ? htmlspecialchars($m['cover_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'; ?>
                <a href="index.php?route=manga/view&id=<?= $m['id'] ?>"><img src="<?= $mcover ?>" alt="<?= htmlspecialchars($m['title']) ?>" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
                <div style="padding-top:6px">
                    <h3 class="card__title"><?= htmlspecialchars($m['title_ua'] ?: $m['title']) ?></h3>
                    <p class="card__text"><?= htmlspecialchars(mb_substr($m['description'] ?? '',0,120)) ?></p>
                    <!-- details by clicking cover/title -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
