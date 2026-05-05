<div class="page">
    <h1>Каталог манги</h1>
    <form method="get" action="index.php">
        <input type="hidden" name="route" value="manga/list">
        <div class="list-toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div style="display:flex;gap:12px;align-items:center">
                <input type="text" name="q" placeholder="Пошук..." value="<?= htmlspecialchars($filters['q'] ?? '') ?>" class="form__input" style="min-width:360px;">
                <select name="sort" class="sort-select">
                    <option value="title" <?= (isset($filters['sort']) && $filters['sort']=='title')?'selected':'' ?>>Заголовок</option>
                    <option value="year" <?= (isset($filters['sort']) && $filters['sort']=='year')?'selected':'' ?>>Рік</option>
                    <option value="views" <?= (isset($filters['sort']) && $filters['sort']=='views')?'selected':'' ?>>Перегляди</option>
                </select>
            </div>
        </div>

        <div class="layout-row filters-right">
            <aside class="filters">
                <div class="form-group">
                    <label>Рік випуску</label>
                    <div class="year-range range-wrap">
                        <div class="range-track"></div>
                        <input type="range" id="m_yearFromRange" min="1965" max="2026" value="<?= (int)($filters['yearFrom'] !== '' ? $filters['yearFrom'] : 1965) ?>">
                        <input type="range" id="m_yearToRange" min="1965" max="2026" value="<?= (int)($filters['yearTo'] !== '' ? $filters['yearTo'] : 2026) ?>">
                        <div class="year-values">Від <span id="m_yearFromDisplay"></span> до <span id="m_yearToDisplay"></span></div>
                        <input type="hidden" name="yearFrom" id="m_yearFrom" value="<?= htmlspecialchars($filters['yearFrom'] ?? '') ?>">
                        <input type="hidden" name="yearTo" id="m_yearTo" value="<?= htmlspecialchars($filters['yearTo'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Оцінка</label>
                    <div class="rating-range range-wrap" style="padding-top:12px;padding-bottom:8px">
                        <div class="range-track"></div>
                        <input type="range" id="m_ratingFromRange" min="0" max="10" step="0.1" value="<?= htmlspecialchars($filters['ratingFrom'] !== '' ? $filters['ratingFrom'] : 0) ?>">
                        <input type="range" id="m_ratingToRange" min="0" max="10" step="0.1" value="<?= htmlspecialchars($filters['ratingTo'] !== '' ? $filters['ratingTo'] : 10) ?>">
                        <div class="year-values">Від <span id="m_ratingFromDisplay"></span> до <span id="m_ratingToDisplay"></span></div>
                        <input type="hidden" name="ratingFrom" id="m_ratingFrom" value="<?= htmlspecialchars($filters['ratingFrom'] ?? '') ?>">
                        <input type="hidden" name="ratingTo" id="m_ratingTo" value="<?= htmlspecialchars($filters['ratingTo'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Статус</label>
                    <select name="status">
                        <option value="">Всі статуси</option>
                        <option value="Ongoing" <?= (isset($filters['status']) && $filters['status']=='Ongoing')?'selected':'' ?>>Ongoing</option>
                        <option value="Completed" <?= (isset($filters['status']) && $filters['status']=='Completed')?'selected':'' ?>>Completed</option>
                        <option value="Hiatus" <?= (isset($filters['status']) && $filters['status']=='Hiatus')?'selected':'' ?>>Припинено</option>
                        <option value="Announced" <?= (isset($filters['status']) && $filters['status']=='Announced')?'selected':'' ?>>Анонс</option>
                        <option value="Stopped" <?= (isset($filters['status']) && $filters['status']=='Stopped')?'selected':'' ?>>Зупинено</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Автор</label>
                    <select name="author">
                        <option value="">Всі автори</option>
                        <?php if(!empty($authors)): foreach($authors as $au): ?>
                            <option value="<?= $au['id'] ?>" <?= (isset($filters['author']) && $filters['author']==$au['id'])?'selected':'' ?>><?= htmlspecialchars($au['name']) ?></option>
                        <?php endforeach; endif; ?>
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
                        <option value="Oneshot" <?= (isset($filters['type']) && $filters['type']=='Oneshot')?'selected':'' ?>>Ваншот</option>
                        <option value="Doujinshi" <?= (isset($filters['type']) && $filters['type']=='Doujinshi')?'selected':'' ?>>Доджінші</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button class="btn">Застосувати фільтри</button>
                    <a href="index.php?route=manga/list" class="btn">Скинути</a>
                </div>

                <script>
                    (function(){
                        // year sync
                        var yFromRange = document.getElementById('m_yearFromRange');
                        var yToRange = document.getElementById('m_yearToRange');
                        var yFromHidden = document.getElementById('m_yearFrom');
                        var yToHidden = document.getElementById('m_yearTo');
                        var yFromDisplay = document.getElementById('m_yearFromDisplay');
                        var yToDisplay = document.getElementById('m_yearToDisplay');
                        var yContainer = document.querySelector('.year-range');
                        var yTrack = yContainer ? yContainer.querySelector('.range-track') : null;
                        function syncYears(){
                            var min = parseFloat(yFromRange.min);
                            var max = parseFloat(yFromRange.max);
                            var from = parseFloat(yFromRange.value);
                            var to = parseFloat(yToRange.value);
                            if(from > to){ var tmp = from; from = to; to = tmp; }
                            yFromHidden.value = from;
                            yToHidden.value = to;
                            yFromDisplay.textContent = from;
                            yToDisplay.textContent = to;
                            if(yTrack){
                                var left = ((from - min) / (max - min)) * 100;
                                var right = ((to - min) / (max - min)) * 100;
                                yTrack.style.left = left + '%';
                                yTrack.style.width = Math.max(0, right - left) + '%';
                            }
                        }
                        if(yFromRange && yToRange){ yFromRange.addEventListener('input', syncYears); yToRange.addEventListener('input', syncYears); syncYears(); }

                        // rating sync
                        var rf = document.getElementById('m_ratingFromRange');
                        var rt = document.getElementById('m_ratingToRange');
                        var rfHidden = document.getElementById('m_ratingFrom');
                        var rtHidden = document.getElementById('m_ratingTo');
                        var rfDisplay = document.getElementById('m_ratingFromDisplay');
                        var rtDisplay = document.getElementById('m_ratingToDisplay');
                        var rContainer = document.querySelector('.rating-range');
                        var rTrack = rContainer ? rContainer.querySelector('.range-track') : null;
                        function syncRating(){
                            var min = parseFloat(rf.min);
                            var max = parseFloat(rf.max);
                            var from = parseFloat(rf.value);
                            var to = parseFloat(rt.value);
                            if(from > to){ var tmp = from; from = to; to = tmp; }
                            rfHidden.value = from;
                            rtHidden.value = to;
                            rfDisplay.textContent = from.toFixed(1);
                            rtDisplay.textContent = to.toFixed(1);
                            if(rTrack){
                                var left = ((from - min) / (max - min)) * 100;
                                var right = ((to - min) / (max - min)) * 100;
                                rTrack.style.left = left + '%';
                                rTrack.style.width = Math.max(0, right - left) + '%';
                            }
                        }
                        if(rf && rt){ rf.addEventListener('input', syncRating); rt.addEventListener('input', syncRating); syncRating(); }

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
            </aside>

        <section class="content">
            <div class="card-grid catalog-grid">
        <?php foreach ($manga as $m): ?>
            <div class="card" style="position:relative">
                <?php $mcover = !empty($m['cover_url']) ? htmlspecialchars($m['cover_url']) : (!empty($m['poster_url']) ? htmlspecialchars($m['poster_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'); ?>
                <a href="index.php?route=manga/view&id=<?= $m['id'] ?>"><img src="<?= $mcover ?>" alt="<?= htmlspecialchars($m['title']) ?>" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
                <div style="padding-top:6px">
                    <h3 class="card__title"><?= htmlspecialchars($m['title_ua'] ?: $m['title']) ?></h3>
                    <p class="card__text"><?= htmlspecialchars(mb_substr($m['description'] ?? '',0,120)) ?></p>
                    <!-- details by clicking cover/title -->
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
