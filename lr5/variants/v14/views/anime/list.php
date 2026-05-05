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

    <form id="filterForm" method="get" action="index.php">
        <input type="hidden" name="route" value="anime/list">
        <div class="list-toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="display:flex;gap:12px;align-items:center;width:100%">
                <input type="text" name="q" placeholder="Пошук..." value="<?= htmlspecialchars($filters['q'] ?? '') ?>" class="form__input" style="flex:1;min-width:220px;">
                <select name="sort" class="sort-select">
                    <option value="rating" <?= (isset($filters['sort']) && $filters['sort']=='rating')?'selected':'' ?>>Рейтинг</option>
                    <option value="year" <?= (isset($filters['sort']) && $filters['sort']=='year')?'selected':'' ?>>Рік</option>
                    <option value="title" <?= (isset($filters['sort']) && $filters['sort']=='title')?'selected':'' ?>>Заголовок</option>
                    <option value="views" <?= (isset($filters['sort']) && $filters['sort']=='views')?'selected':'' ?>>Перегляди</option>
                </select>
                <input type="hidden" name="order" id="orderInput" value="<?= htmlspecialchars(strtoupper($filters['order'] ?? 'DESC')) ?>">
                <button type="button" id="orderToggle" class="btn btn--small" title="Порядок сортування" style="margin-left:8px"><?= (isset($filters['order']) && strtoupper($filters['order'])==='ASC')? '↑':'↓' ?></button>
            </div>
        </div>

        <div class="layout-row filters-right">
            <aside class="filters">
                <div class="form-group">
                    <label>Рік випуску</label>
                    <div class="year-range range-wrap">
                        <div class="range-track"></div>
                        <input type="range" id="yearFromRange" min="1965" max="2026" value="<?= (int)($filters['yearFrom'] ?: 1965) ?>">
                        <input type="range" id="yearToRange" min="1965" max="2026" value="<?= (int)($filters['yearTo'] ?: 2026) ?>">
                        <div class="year-values">Від <span id="yearFromDisplay"></span> до <span id="yearToDisplay"></span></div>
                        <input type="hidden" name="yearFrom" id="yearFrom" value="<?= htmlspecialchars($filters['yearFrom'] ?? '') ?>">
                        <input type="hidden" name="yearTo" id="yearTo" value="<?= htmlspecialchars($filters['yearTo'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Оцінка</label>
                    <div class="rating-range range-wrap" style="padding-top:12px;padding-bottom:8px">
                        <div class="range-track"></div>
                            <input type="range" id="ratingFromRange" min="0" max="10" step="0.1" value="<?= ($filters['ratingFrom'] !== '' ? htmlspecialchars($filters['ratingFrom']) : '0') ?>">
                            <input type="range" id="ratingToRange" min="0" max="10" step="0.1" value="<?= ($filters['ratingTo'] !== '' ? htmlspecialchars($filters['ratingTo']) : '10') ?>">
                        <div class="year-values">Від <span id="ratingFromDisplay"></span> до <span id="ratingToDisplay"></span></div>
                        <input type="hidden" name="ratingFrom" id="ratingFrom" value="<?= htmlspecialchars($filters['ratingFrom'] ?? '') ?>">
                        <input type="hidden" name="ratingTo" id="ratingTo" value="<?= htmlspecialchars($filters['ratingTo'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Студія</label>
                    <input type="hidden" name="studios" id="studioInput" value="<?= htmlspecialchars($filters['studios'] ?? ($filters['studioId'] ?? '')) ?>" />
                    <div class="chips-toggle">
                        <button type="button" class="filter-pill" id="openStudiosBtn">Студії ▾</button>
                    </div>
                    <div class="overlay-panel" id="studiosOverlay" style="display:none;">
                        <div class="overlay-header"><span>Студії</span><input type="search" id="studioSearch" class="overlay-search" placeholder="Пошук..."></div>
                        <div class="overlay-body">
                            <div style="margin-bottom:8px"><button type="button" class="btn" id="studioSelectAll">Позначити всі</button> <button type="button" class="btn" id="studioClearAll">Зняти всі</button></div>
                            <?php foreach ($studios as $s): ?>
                                <?php $checked = false; if(!empty($filters['studios'])){ $arr = array_filter(array_map('intval', explode(',', $filters['studios']))); if(in_array($s['id'],$arr)) $checked = true; } elseif(isset($filters['studioId']) && $filters['studioId']==$s['id']){ $checked = true; } ?>
                                <label class="overlay-checkbox"><input type="checkbox" value="<?= $s['id'] ?>" <?php if($checked): ?>checked<?php endif; ?>> <?= htmlspecialchars($s['name']) ?></label>
                            <?php endforeach; ?>
                        </div>
                        <div class="overlay-actions">
                            <button type="button" class="btn" id="applyStudios">Застосувати</button>
                            <button type="button" class="btn btn--secondary" id="closeStudios">Закрити</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Жанр</label>
                    <input type="hidden" name="genres" id="genreInput" value="<?= htmlspecialchars($filters['genre'] ?? '') ?>" />
                    <div class="chips-toggle">
                        <button type="button" class="filter-pill" id="openGenresBtn">Жанри ▾</button>
                    </div>
                    <div class="overlay-panel" id="genresOverlay" style="display:none;">
                        <div class="overlay-header"><span>Жанри</span><input type="search" id="genreSearch" class="overlay-search" placeholder="Пошук..."></div>
                        <div class="overlay-body">
                            <div style="margin-bottom:8px"><button type="button" class="btn" id="genreSelectAll">Позначити всі</button> <button type="button" class="btn" id="genreClearAll">Зняти всі</button></div>
                            <?php foreach ($genres as $g): ?>
                                <label class="overlay-checkbox"><input type="checkbox" value="<?= $g['id'] ?>" data-name="<?= htmlspecialchars($g['name']) ?>" <?php if(isset($filters['genre']) && $filters['genre']!=='' && in_array($g['id'], array_filter(array_map('intval', explode(',', $filters['genre']))))): ?>checked<?php endif; ?>> <?= htmlspecialchars($g['name']) ?></label>
                            <?php endforeach; ?>
                        </div>
                        <div class="overlay-actions">
                            <button type="button" class="btn" id="applyGenres">Застосувати</button>
                            <button type="button" class="btn btn--secondary" id="closeGenres">Закрити</button>
                        </div>
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
                        <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='ONA') ? 'active' : '' ?>" data-val="ONA">ONA</div>
                        <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='Special') ? 'active' : '' ?>" data-val="Special">Спешл</div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <div class="filter-chips" id="statusChips">
                        <input type="hidden" name="status" id="statusInput" value="<?= htmlspecialchars($filters['status'] ?? '') ?>" />
                        <div class="filter-chip <?= empty($filters['status']) ? 'active' : '' ?>" data-val="">Всі</div>
                        <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Ongoing') ? 'active' : '' ?>" data-val="Ongoing">Виходить</div>
                        <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Completed') ? 'active' : '' ?>" data-val="Completed">Завершено</div>
                        <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Hiatus') ? 'active' : '' ?>" data-val="Hiatus">Припинено</div>
                        <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Announced') ? 'active' : '' ?>" data-val="Announced">Анонс</div>
                        <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Stopped') ? 'active' : '' ?>" data-val="Stopped">Зупинено</div>
                    </div>
                </div>
                <!-- removed duplicated sort control (kept only top toolbar sort) -->
                <div class="form-actions" style="display:flex;gap:12px">
                    <button class="btn" id="applyFiltersBtn" type="submit">Застосувати фільтри</button>
                    <a href="index.php?route=anime/list" class="btn btn--secondary btn--large" id="resetFiltersBtn">Скинути фільтри</a>
                </div>
            </form>
        </aside>

        <section class="content">
            <div class="card-grid catalog-grid">
                <?php foreach ($anime as $a): ?>
            <div class="card" style="position:relative">
                <?php if (!empty($a['status']) && strtolower($a['status']) === 'ongoing'): ?>
                    <div class="badge">Виходить</div>
                <?php endif; ?>
                <?php $cover = !empty($a['cover_url']) ? htmlspecialchars($a['cover_url']) : (!empty($a['poster_url']) ? htmlspecialchars($a['poster_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'); ?>
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

            <?php if (!empty($pagination) && $pagination['totalPages'] > 1): ?>
                <?php
                    $curr = (int)$pagination['page'];
                    $totalPages = (int)$pagination['totalPages'];
                    $qsBase = $_GET;
                    $qsBase['route'] = 'anime/list';
                    $window = 2;
                    $start = max(1, $curr - $window);
                    $end = min($totalPages, $curr + $window);
                ?>
                <div class="pagination">
                    <?php if ($curr > 1): $qsBase['page'] = $curr - 1; ?>
                        <a class="page-link" href="index.php?<?= http_build_query($qsBase) ?>">←</a>
                    <?php endif; ?>

                    <?php if ($start > 1): $qsBase['page'] = 1; ?>
                        <a class="page-link" href="index.php?<?= http_build_query($qsBase) ?>">1</a>
                        <?php if ($start > 2): ?><span class="page-ellipsis">…</span><?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): $qsBase['page'] = $i; ?>
                        <a class="page-link <?= $i === $curr ? 'active' : '' ?>" href="index.php?<?= http_build_query($qsBase) ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($end < $totalPages): $qsBase['page'] = $totalPages; ?>
                        <?php if ($end < $totalPages - 1): ?><span class="page-ellipsis">…</span><?php endif; ?>
                        <a class="page-link" href="index.php?<?= http_build_query($qsBase) ?>"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <?php if ($curr < $totalPages): $qsBase['page'] = $curr + 1; ?>
                        <a class="page-link" href="index.php?<?= http_build_query($qsBase) ?>">→</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
    (function(){
        // chips (type/status) single-select behavior remains
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
        wire('typeChips','typeInput','data-val');
        wire('statusChips','statusInput','data-val');

        // overlay helper
        function setupOverlay(btnId, overlayId, applyId, closeId, selectAllId, clearAllId, hiddenInputId){
            var btn = document.getElementById(btnId);
            var overlay = document.getElementById(overlayId);
            var apply = document.getElementById(applyId);
            var close = document.getElementById(closeId);
            var selectAll = document.getElementById(selectAllId);
            var clearAll = document.getElementById(clearAllId);
            var hidden = document.getElementById(hiddenInputId);
            if(!btn || !overlay) return;

            btn.addEventListener('click', function(e){
                overlay.style.display = (overlay.style.display === 'none' || overlay.style.display === '') ? 'block' : 'none';
            });
            if(close) close.addEventListener('click', function(){ overlay.style.display = 'none'; });
            if(selectAll) selectAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = true; }); });
            if(clearAll) clearAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = false; }); });
            if(apply){
                apply.addEventListener('click', function(){
                    var vals = [];
                    overlay.querySelectorAll('input[type=checkbox]:checked').forEach(function(c){ vals.push(c.value); });
                    if(hidden) hidden.value = vals.join(',');
                    overlay.style.display = 'none';
                });
            }
            // close overlay if clicked outside
            document.addEventListener('click', function(e){
                if(!overlay.contains(e.target) && !btn.contains(e.target)){
                    overlay.style.display = 'none';
                }
            });
        }

        setupOverlay('openGenresBtn','genresOverlay','applyGenres','closeGenres','genreSelectAll','genreClearAll','genreInput');
        setupOverlay('openStudiosBtn','studiosOverlay','applyStudios','closeStudios','studioSelectAll','studioClearAll','studioInput');

        // overlay search: filters visible checkbox labels by text
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
        setupOverlaySearch('genresOverlay','genreSearch');
        setupOverlaySearch('studiosOverlay','studioSearch');

        // year dual-range
        var yFromRange = document.getElementById('yearFromRange');
        var yToRange = document.getElementById('yearToRange');
        var yFromHidden = document.getElementById('yearFrom');
        var yToHidden = document.getElementById('yearTo');
        var yFromDisplay = document.getElementById('yearFromDisplay');
        var yToDisplay = document.getElementById('yearToDisplay');
        var track = document.querySelector('.year-range .range-track');

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
            // update track
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

        // rating dual-range sync
        var rFromRange = document.getElementById('ratingFromRange');
        var rToRange = document.getElementById('ratingToRange');
        var rFromHidden = document.getElementById('ratingFrom');
        var rToHidden = document.getElementById('ratingTo');
        var rFromDisplay = document.getElementById('ratingFromDisplay');
        var rToDisplay = document.getElementById('ratingToDisplay');
        var rtrack = document.querySelector('.rating-range .range-track');

        function syncRatings(){
            if(!rFromRange || !rToRange) return;
            var min = parseFloat(rFromRange.min);
            var max = parseFloat(rFromRange.max);
            var from = parseFloat(rFromRange.value);
            var to = parseFloat(rToRange.value);
            if(from > to){ var tmp = from; from = to; to = tmp; }
            rFromHidden.value = from;
            rToHidden.value = to;
            if(rFromDisplay) rFromDisplay.textContent = from.toFixed(1);
            if(rToDisplay) rToDisplay.textContent = to.toFixed(1);
            if(rtrack){
                var left = ((from - min) / (max - min)) * 100;
                var right = ((to - min) / (max - min)) * 100;
                rtrack.style.left = left + '%';
                rtrack.style.width = Math.max(0, right - left) + '%';
            }
        }
        if(rFromRange && rToRange){
            rFromRange.addEventListener('input', syncRatings);
            rToRange.addEventListener('input', syncRatings);
            syncRatings();
        }

        // order toggle (asc/desc)
        var orderInput = document.getElementById('orderInput');
        var orderToggle = document.getElementById('orderToggle');
        // submit helper (debounce)
        var filterForm = document.getElementById('filterForm');
        function submitFiltersDebounced(){
            if(!filterForm) return;
            if(window._filterTimeout) clearTimeout(window._filterTimeout);
            window._filterTimeout = setTimeout(function(){ filterForm.submit(); }, 450);
        }

        if(orderToggle && orderInput){
            orderToggle.addEventListener('click', function(){
                var v = (orderInput.value || 'DESC').toUpperCase();
                v = v === 'ASC' ? 'DESC' : 'ASC';
                orderInput.value = v;
                orderToggle.textContent = v === 'ASC' ? '↑' : '↓';
                // submit immediately when toggling order
                if(filterForm) filterForm.submit();
            });
        }

        // auto-submit when sort selection changes
        var sortSelect = document.querySelector('select[name="sort"]');
        if(sortSelect){
            sortSelect.addEventListener('change', function(){ if(filterForm) filterForm.submit(); });
        }

        // auto-submit when sliders change (debounced)
        if(yFromRange && yToRange){
            yFromRange.addEventListener('input', function(){ syncYears(); submitFiltersDebounced(); });
            yToRange.addEventListener('input', function(){ syncYears(); submitFiltersDebounced(); });
        }
        if(rFromRange && rToRange){
            rFromRange.addEventListener('input', function(){ syncRatings(); submitFiltersDebounced(); });
            rToRange.addEventListener('input', function(){ syncRatings(); submitFiltersDebounced(); });
        }

        // when apply buttons in overlays are used, submit the form as well
        var applyGenresBtn = document.getElementById('applyGenres');
        var applyStudiosBtn = document.getElementById('applyStudios');
        if(applyGenresBtn) applyGenresBtn.addEventListener('click', function(){ document.getElementById('genresOverlay').style.display='none'; if(filterForm) filterForm.submit(); });
        if(applyStudiosBtn) applyStudiosBtn.addEventListener('click', function(){ document.getElementById('studiosOverlay').style.display='none'; if(filterForm) filterForm.submit(); });

        // legacy minRating controls removed; rating dual-range above handles rating filters

    })();
</script>
