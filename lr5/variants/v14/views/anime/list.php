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
                    <div class="year-range">
                        <div class="range-pair">
                            <input type="number" id="yearFromInput" name="yearFrom" min="1965" max="2026" value="<?= (int)($filters['yearFrom'] ?: 1965) ?>">
                            <input type="number" id="yearToInput" name="yearTo" min="1965" max="2026" value="<?= (int)($filters['yearTo'] ?: 2026) ?>">
                        </div>
                        <div class="year-values">Від <span id="yearFromDisplay"></span> до <span id="yearToDisplay"></span></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Оцінка</label>
                    <div class="rating-range" style="padding-top:8px;padding-bottom:8px">
                        <div class="range-pair">
                            <input type="number" id="ratingFromInput" name="ratingFrom" min="0" max="10" step="0.1" value="<?= ($filters['ratingFrom'] !== '' ? htmlspecialchars($filters['ratingFrom']) : '0') ?>">
                            <input type="number" id="ratingToInput" name="ratingTo" min="0" max="10" step="0.1" value="<?= ($filters['ratingTo'] !== '' ? htmlspecialchars($filters['ratingTo']) : '10') ?>">
                        </div>
                        <div class="year-values">Від <span id="ratingFromDisplay"></span> до <span id="ratingToDisplay"></span></div>
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
        var filterForm = document.getElementById('filterForm');

        // chips (type/status) single-select behavior
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
                if(filterForm) filterForm.submit();
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
                e.stopPropagation();
                overlay.style.display = (overlay.style.display === 'none' || overlay.style.display === '') ? 'block' : 'none';
            });
            if(close) close.addEventListener('click', function(){ overlay.style.display = 'none'; });
            if(selectAll) selectAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = true; }); });
            if(clearAll) clearAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = false; }); });
            if(apply) apply.addEventListener('click', function(){ var vals=[]; overlay.querySelectorAll('input[type=checkbox]:checked').forEach(function(c){ vals.push(c.value); }); if(hidden) hidden.value = vals.join(','); overlay.style.display = 'none'; });
            document.addEventListener('click', function(e){ if(!overlay.contains(e.target) && !btn.contains(e.target)){ overlay.style.display = 'none'; } });
        }
        setupOverlay('openGenresBtn','genresOverlay','applyGenres','closeGenres','genreSelectAll','genreClearAll','genreInput');
        setupOverlay('openStudiosBtn','studiosOverlay','applyStudios','closeStudios','studioSelectAll','studioClearAll','studioInput');

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

        // Year inputs (number fields)
        var yFromInput = document.getElementById('yearFromInput');
        var yToInput = document.getElementById('yearToInput');
        var yFromDisplay = document.getElementById('yearFromDisplay');
        var yToDisplay = document.getElementById('yearToDisplay');
        function syncYears(){
            if(!yFromInput || !yToInput) return;
            var min = parseInt(yFromInput.min,10) || 1965;
            var max = parseInt(yFromInput.max,10) || 2026;
            var rawFrom = parseInt(yFromInput.value,10) || min;
            var rawTo = parseInt(yToInput.value,10) || max;
            var displayFrom = Math.min(rawFrom, rawTo);
            var displayTo = Math.max(rawFrom, rawTo);
            if(yFromDisplay) yFromDisplay.textContent = displayFrom;
            if(yToDisplay) yToDisplay.textContent = displayTo;
        }
        if(yFromInput && yToInput){ yFromInput.addEventListener('input', function(){ syncYears(); debounceSubmit(); }); yToInput.addEventListener('input', function(){ syncYears(); debounceSubmit(); }); syncYears(); }

        // Rating inputs
        var rFromInput = document.getElementById('ratingFromInput');
        var rToInput = document.getElementById('ratingToInput');
        var rFromDisplay = document.getElementById('ratingFromDisplay');
        var rToDisplay = document.getElementById('ratingToDisplay');
        function syncRating(){
            if(!rFromInput || !rToInput) return;
            var rawFrom = parseFloat(rFromInput.value) || 0;
            var rawTo = parseFloat(rToInput.value) || 10;
            var displayFrom = Math.min(rawFrom, rawTo);
            var displayTo = Math.max(rawFrom, rawTo);
            if(rFromDisplay) rFromDisplay.textContent = displayFrom.toFixed(1);
            if(rToDisplay) rToDisplay.textContent = displayTo.toFixed(1);
        }
        if(rFromInput && rToInput){ rFromInput.addEventListener('input', function(){ syncRating(); debounceSubmit(); }); rToInput.addEventListener('input', function(){ syncRating(); debounceSubmit(); }); syncRating(); }

        // selected chips rendering
        function renderSelectedChips(hiddenInputId, overlayId, btnId){
            var hidden = document.getElementById(hiddenInputId);
            var overlay = document.getElementById(overlayId);
            var btn = document.getElementById(btnId);
            if(!hidden || !overlay || !btn) return;
            var parent = btn.parentElement;
            var container = parent.querySelector('.selected-chips');
            if(!container){ container = document.createElement('div'); container.className = 'selected-chips'; parent.appendChild(container); }
            container.innerHTML = '';
            var ids = (hidden.value||'').split(',').map(function(s){ return s.trim(); }).filter(Boolean);
            ids.forEach(function(id){
                var checkbox = overlay.querySelector('input[type=checkbox][value="'+id+'"]');
                var labelText = checkbox ? checkbox.parentElement.textContent.trim() : id;
                var chip = document.createElement('span'); chip.className='selected-chip'; chip.textContent = labelText;
                var rem = document.createElement('button'); rem.type='button'; rem.textContent='×';
                rem.addEventListener('click', function(){ if(checkbox) checkbox.checked=false; var newIds = ids.filter(function(x){ return x!==id; }); hidden.value = newIds.join(','); if(filterForm) filterForm.submit(); });
                chip.appendChild(rem);
                container.appendChild(chip);
            });
        }

        var applyGenresBtn = document.getElementById('applyGenres');
        var applyStudiosBtn = document.getElementById('applyStudios');
        if(applyGenresBtn) applyGenresBtn.addEventListener('click', function(){ document.getElementById('genresOverlay').style.display='none'; renderSelectedChips('genreInput','genresOverlay','openGenresBtn'); if(filterForm) filterForm.submit(); });
        if(applyStudiosBtn) applyStudiosBtn.addEventListener('click', function(){ document.getElementById('studiosOverlay').style.display='none'; renderSelectedChips('studioInput','studiosOverlay','openStudiosBtn'); if(filterForm) filterForm.submit(); });

        // initial render
        renderSelectedChips('genreInput','genresOverlay','openGenresBtn');
        renderSelectedChips('studioInput','studiosOverlay','openStudiosBtn');

        // debounce submit helper
        var _deb; function debounceSubmit(){ if(_deb) clearTimeout(_deb); _deb = setTimeout(function(){ if(filterForm) filterForm.submit(); }, 450); }

        // order toggle (asc/desc)
        var orderInput = document.getElementById('orderInput');
        var orderToggle = document.getElementById('orderToggle');
        var sortSelect = document.querySelector('select[name="sort"]');
        if(orderToggle && orderInput){ orderToggle.addEventListener('click', function(){ var v = (orderInput.value || 'DESC').toUpperCase(); v = v === 'ASC' ? 'DESC' : 'ASC'; orderInput.value = v; orderToggle.textContent = v === 'ASC' ? '↑' : '↓'; if(filterForm) filterForm.submit(); }); }
        if(sortSelect){ sortSelect.addEventListener('change', function(){ if(filterForm) filterForm.submit(); }); }

    })();
</script>
