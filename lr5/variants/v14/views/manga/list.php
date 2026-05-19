<div class="page">
    <h1>Каталог манги</h1>
    <form id="filterForm" method="get" action="index.php">
        <input type="hidden" name="route" value="manga/list">
        <div class="list-toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="display:flex;gap:12px;align-items:center;width:100%">
                <input type="text" name="q" placeholder="Пошук..." value="<?= htmlspecialchars($filters['q'] ?? '') ?>" class="form__input" style="flex:1;min-width:220px;">
                <select name="sort" class="sort-select">
                    <option value="title" <?= (isset($filters['sort']) && $filters['sort']=='title')?'selected':'' ?>>Заголовок</option>
                    <option value="year" <?= (isset($filters['sort']) && $filters['sort']=='year')?'selected':'' ?>>Рік</option>
                    <option value="views" <?= (isset($filters['sort']) && $filters['sort']=='views')?'selected':'' ?>>Перегляди</option>
                </select>
                <input type="hidden" name="order" id="m_orderInput" value="<?= htmlspecialchars(strtoupper($filters['order'] ?? 'DESC')) ?>">
                <button type="button" id="m_orderToggle" class="btn btn--small" title="Порядок сортування" style="margin-left:8px"><?= (isset($filters['order']) && strtoupper($filters['order'])==='ASC')? '↑':'↓' ?></button>
            </div>
        </div>

                <div class="layout-row filters-right">
            <aside class="filters">
                <div class="form-group">
                    <label>Рік випуску</label>
                    <div class="year-range">
                        <div class="range-pair">
                            <input type="number" id="m_yearFromInput" name="yearFrom" min="1965" max="2026" value="<?= htmlspecialchars((isset($filters['yearFrom']) && $filters['yearFrom'] > 0) ? $filters['yearFrom'] : 1965) ?>">
                            <input type="number" id="m_yearToInput" name="yearTo" min="1965" max="2026" value="<?= htmlspecialchars((isset($filters['yearTo']) && $filters['yearTo'] > 0) ? $filters['yearTo'] : 2026) ?>">
                        </div>
                        <div class="year-values">Від <span id="m_yearFromDisplay"></span> до <span id="m_yearToDisplay"></span></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Оцінка</label>
                    <div class="rating-range" style="padding-top:8px;padding-bottom:8px">
                        <div class="range-pair">
                            <input type="number" id="m_ratingFromInput" name="ratingFrom" min="1" max="10" step="0.1" value="<?= htmlspecialchars(($filters['ratingFrom'] !== '' && is_numeric($filters['ratingFrom'])) ? $filters['ratingFrom'] : 1) ?>">
                            <input type="number" id="m_ratingToInput" name="ratingTo" min="1" max="10" step="0.1" value="<?= htmlspecialchars(($filters['ratingTo'] !== '' && is_numeric($filters['ratingTo'])) ? $filters['ratingTo'] : 10) ?>">
                        </div>
                        <div class="year-values">Від <span id="m_ratingFromDisplay"></span> до <span id="m_ratingToDisplay"></span></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Автори</label>
                        <input type="hidden" name="authors" id="m_authorsInput" value="<?= htmlspecialchars($filters['authors'] ?? '') ?>" />
                        <div class="chips-toggle">
                            <button type="button" class="filter-pill" id="openAuthorsBtn">Автори ▾</button>
                        </div>
                        <div class="overlay-panel overlay-panel--right" id="mAuthorsOverlay" style="display:none;">
                            <div class="overlay-header"><span>Автори</span><input type="search" id="m_authorSearch" class="overlay-search" placeholder="Пошук..."></div>
                            <div class="overlay-body">
                                <div style="margin-bottom:8px"><button type="button" class="btn" id="mAuthorSelectAll">Позначити всі</button> <button type="button" class="btn" id="mAuthorClearAll">Зняти всі</button></div>
                                <?php if(!empty($authors)): foreach($authors as $au): ?>
                                    <label class="overlay-checkbox"><input type="checkbox" value="<?= $au['id'] ?>" <?php if(isset($filters['authors']) && $filters['authors']!=='' && in_array($au['id'], array_filter(array_map('intval', explode(',', $filters['authors']))))): ?>checked<?php endif; ?>> <?= htmlspecialchars($au['name']) ?></label>
                                <?php endforeach; endif; ?>
                            </div>
                            <div class="overlay-actions">
                                <button type="button" class="btn" id="mApplyAuthors">Застосувати</button>
                                <button type="button" class="btn btn--secondary" id="mCloseAuthors">Закрити</button>
                            </div>
                        </div>
                    </div>

                <div class="form-group">
                    <label>Жанри</label>
                    <input type="hidden" name="genres" id="m_genreInput" value="<?= htmlspecialchars($filters['genre'] ?? '') ?>" />
                    <div class="chips-toggle">
                        <button type="button" class="filter-pill" id="openMGenresBtn">Жанри ▾</button>
                    </div>
                    <div class="overlay-panel overlay-panel--right" id="mGenresOverlay" style="display:none;">
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

                

                <!-- Type and Status chips (placed at bottom) -->
                <div style="margin-top:12px">
                    <div class="form-group">
                        <label>Тип</label>
                        <div class="filter-chips" id="m_typeChips">
                            <input type="hidden" name="type" id="m_typeInput" value="<?= htmlspecialchars($filters['type'] ?? '') ?>" />
                            <div class="filter-chip <?= empty($filters['type']) ? 'active' : '' ?>" data-val="">Всі</div>
                            <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='Manga') ? 'active' : '' ?>" data-val="Manga">Manga</div>
                            <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='Manhwa') ? 'active' : '' ?>" data-val="Manhwa">Manhwa</div>
                            <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='Oneshot') ? 'active' : '' ?>" data-val="Oneshot">Ваншот</div>
                            <div class="filter-chip <?= (isset($filters['type']) && $filters['type']=='Doujinshi') ? 'active' : '' ?>" data-val="Doujinshi">Доджinshi</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Статус</label>
                        <div class="filter-chips" id="m_statusChips">
                            <input type="hidden" name="status" id="m_statusInput" value="<?= htmlspecialchars($filters['status'] ?? '') ?>" />
                            <div class="filter-chip <?= empty($filters['status']) ? 'active' : '' ?>" data-val="">Всі</div>
                            <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Ongoing') ? 'active' : '' ?>" data-val="Ongoing">Виходить</div>
                            <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Completed') ? 'active' : '' ?>" data-val="Completed">Завершено</div>
                            <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Hiatus') ? 'active' : '' ?>" data-val="Hiatus">Припинено</div>
                            <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Announced') ? 'active' : '' ?>" data-val="Announced">Анонс</div>
                            <div class="filter-chip <?= (isset($filters['status']) && $filters['status']=='Stopped') ? 'active' : '' ?>" data-val="Stopped">Зупинено</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn">Застосувати фільтри</button>
                    <a href="index.php?route=manga/list" class="btn btn--secondary btn--large">Скинути фільтр</a>
                </div>

                <script>
                    (function(){
                        var filterForm = document.getElementById('filterForm');

                        // Year inputs (number fields)
                        var yFromInput = document.getElementById('m_yearFromInput');
                        var yToInput = document.getElementById('m_yearToInput');
                        var yFromDisplay = document.getElementById('m_yearFromDisplay');
                        var yToDisplay = document.getElementById('m_yearToDisplay');
                        function syncYears(){
                            if(!yFromInput || !yToInput) return;
                            var min = parseInt(yFromInput.min,10) || 1965;
                            var max = parseInt(yToInput.max,10) || 2026;
                            var rawFrom = yFromInput.value ? parseInt(yFromInput.value,10) : null;
                            var rawTo = yToInput.value ? parseInt(yToInput.value,10) : null;
                            if(rawFrom === null && rawTo === null){
                                if(yFromDisplay) yFromDisplay.textContent = min;
                                if(yToDisplay) yToDisplay.textContent = max;
                                return;
                            }
                            if(rawFrom === null) rawFrom = min;
                            if(rawTo === null) rawTo = max;
                            var displayFrom = Math.min(rawFrom, rawTo);
                            var displayTo = Math.max(rawFrom, rawTo);
                            if(yFromDisplay) yFromDisplay.textContent = displayFrom;
                            if(yToDisplay) yToDisplay.textContent = displayTo;
                        }
                        if(yFromInput && yToInput){
                            yFromInput.addEventListener('input', function(){ syncYears(); });
                            yToInput.addEventListener('input', function(){ syncYears(); });
                            syncYears();
                        }

                        // Rating inputs (number fields)
                        var rFromInput = document.getElementById('m_ratingFromInput');
                        var rToInput = document.getElementById('m_ratingToInput');
                        var rFromDisplay = document.getElementById('m_ratingFromDisplay');
                        var rToDisplay = document.getElementById('m_ratingToDisplay');
                        function syncRating(){
                            if(!rFromInput || !rToInput) return;
                            var min = parseFloat(rFromInput.min) || 0;
                            var max = parseFloat(rToInput.max) || 10;
                            var rawFrom = rFromInput.value ? parseFloat(rFromInput.value) : null;
                            var rawTo = rToInput.value ? parseFloat(rToInput.value) : null;
                            if(rawFrom === null && rawTo === null){
                                if(rFromDisplay) rFromDisplay.textContent = min.toFixed(1);
                                if(rToDisplay) rToDisplay.textContent = max.toFixed(1);
                                return;
                            }
                            if(rawFrom === null) rawFrom = min;
                            if(rawTo === null) rawTo = max;
                            var displayFrom = Math.min(rawFrom, rawTo);
                            var displayTo = Math.max(rawFrom, rawTo);
                            if(rFromDisplay) rFromDisplay.textContent = displayFrom.toFixed(1);
                            if(rToDisplay) rToDisplay.textContent = displayTo.toFixed(1);
                        }
                        if(rFromInput && rToInput){
                            rFromInput.addEventListener('input', function(){ syncRating(); });
                            rToInput.addEventListener('input', function(){ syncRating(); });
                            syncRating();
                        }

                        // overlay helpers
                        function setupOverlay(btnId, overlayId, applyId, closeId, selectAllId, clearAllId, hiddenInputId){
                            var btn = document.getElementById(btnId);
                            var overlay = document.getElementById(overlayId);
                            var apply = document.getElementById(applyId);
                            var close = document.getElementById(closeId);
                            var selectAll = document.getElementById(selectAllId);
                            var clearAll = document.getElementById(clearAllId);
                            var hidden = document.getElementById(hiddenInputId);
                            if(!btn || !overlay) return;
                            btn.addEventListener('click', function(e){ e.stopPropagation(); overlay.style.display = (overlay.style.display === 'none' || overlay.style.display === '') ? 'block' : 'none'; });
                            if(close) close.addEventListener('click', function(){ overlay.style.display = 'none'; });
                            if(selectAll) selectAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = true; }); });
                            if(clearAll) clearAll.addEventListener('click', function(){ overlay.querySelectorAll('input[type=checkbox]').forEach(function(c){ c.checked = false; }); });
                            if(apply) apply.addEventListener('click', function(){ var vals=[]; overlay.querySelectorAll('input[type=checkbox]:checked').forEach(function(c){ vals.push(c.value); }); if(hidden) hidden.value = vals.join(','); overlay.style.display='none'; });
                            document.addEventListener('click', function(e){ if(!overlay.contains(e.target) && !btn.contains(e.target)){ overlay.style.display = 'none'; } });
                        }
                        setupOverlay('openMGenresBtn','mGenresOverlay','mApplyGenres','mCloseGenres','mGenreSelectAll','mGenreClearAll','m_genreInput');
                        setupOverlay('openAuthorsBtn','mAuthorsOverlay','mApplyAuthors','mCloseAuthors','mAuthorSelectAll','mAuthorClearAll','m_authorsInput');

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
                        setupOverlaySearch('mAuthorsOverlay','m_authorSearch');

                        // render selected chips for overlays
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
                                var chip = document.createElement('span'); chip.className='selected-chip';
                                chip.textContent = labelText;
                                var rem = document.createElement('button'); rem.type='button'; rem.textContent='×';
                                rem.addEventListener('click', function(){ if(checkbox) checkbox.checked=false; var newIds = ids.filter(function(x){ return x!==id; }); hidden.value = newIds.join(','); });
                                chip.appendChild(rem);
                                container.appendChild(chip);
                            });
                        }

                        var mApplyGenresBtn = document.getElementById('mApplyGenres');
                        var mApplyAuthorsBtn = document.getElementById('mApplyAuthors');
                        if(mApplyGenresBtn) mApplyGenresBtn.addEventListener('click', function(){ document.getElementById('mGenresOverlay').style.display='none'; renderSelectedChips('m_genreInput','mGenresOverlay','openMGenresBtn'); });
                        if(mApplyAuthorsBtn) mApplyAuthorsBtn.addEventListener('click', function(){ document.getElementById('mAuthorsOverlay').style.display='none'; renderSelectedChips('m_authorsInput','mAuthorsOverlay','openAuthorsBtn'); });

                        // initial render of selected chips
                        renderSelectedChips('m_genreInput','mGenresOverlay','openMGenresBtn');
                        renderSelectedChips('m_authorsInput','mAuthorsOverlay','openAuthorsBtn');

                        // debounce submit helper removed — use Apply button to submit

                        // wire type/status chips (single-select behavior)
                        function wireChips(containerId, inputId){
                            var container = document.getElementById(containerId);
                            if(!container) return;
                            var input = document.getElementById(inputId);
                            container.addEventListener('click', function(e){
                                var chip = e.target.closest('.filter-chip');
                                if(!chip) return;
                                container.querySelectorAll('.filter-chip').forEach(function(c){ c.classList.remove('active'); });
                                chip.classList.add('active');
                                var val = chip.getAttribute('data-val') || '';
                                if(input) input.value = val;
                            });
                        }
                        wireChips('m_typeChips','m_typeInput');
                        wireChips('m_statusChips','m_statusInput');

                        // order toggle for manga
                        var mOrderInput = document.getElementById('m_orderInput');
                        var mOrderToggle = document.getElementById('m_orderToggle');
                        if(mOrderToggle && mOrderInput){
                            mOrderToggle.addEventListener('click', function(){
                                var v = (mOrderInput.value || 'DESC').toUpperCase();
                                v = v === 'ASC' ? 'DESC' : 'ASC';
                                mOrderInput.value = v;
                                mOrderToggle.textContent = v === 'ASC' ? '↑' : '↓';
                            });
                        }

                    })();
                </script>
            </aside>

        <section class="content">
            <div class="card-grid catalog-grid">
        <?php foreach ($manga as $m): ?>
            <div class="card" style="position:relative">
                <?php $mcover = !empty($m['cover_url']) ? htmlspecialchars($m['cover_url']) : (!empty($m['poster_url']) ? htmlspecialchars($m['poster_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'); ?>
                <a href="index.php?route=manga/view&id=<?= $m['id'] ?>"><img src="<?= $mcover ?>" alt="<?= htmlspecialchars($m['title']) ?>" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
                <div style="padding-top:6px">
                    <h3 class="card__title"><?= htmlspecialchars($m['title_ua'] ?: $m['title']) ?></h3>
                    <p class="card__text" style="font-size:0.85rem;color:var(--muted)"><?= htmlspecialchars($m['year'] ?? '') ?> • <?= htmlspecialchars($m['type'] ?? '') ?></p>
                </div>
            </div>
        <?php endforeach; ?>
            </div> <!-- .card-grid -->

            <?php if (!empty($pagination) && $pagination['totalPages'] > 1): ?>
                <?php
                    $curr = (int)$pagination['page'];
                    $totalPages = (int)$pagination['totalPages'];
                    $qsBase = $_GET;
                    $qsBase['route'] = 'manga/list';
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
    </div> <!-- .layout-row -->
    </form>
</div>
