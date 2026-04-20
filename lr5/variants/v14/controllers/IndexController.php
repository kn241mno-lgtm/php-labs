<?php

class IndexController extends PageController
{
    public function action_main(): void
    {
        $db = Database::getInstance();

        // Ongoings (recent with status Ongoing)
        $stmt = $db->prepare("SELECT a.id, a.title, a.title_ua, a.cover_url, a.year, a.type, a.status, a.episodes, a.created_at, IFNULL((SELECT AVG(score) FROM rating r WHERE r.anime_id = a.id),0) AS rating, s.name AS studio_name FROM anime a LEFT JOIN studio s ON a.studio_id = s.id WHERE a.status = 'Ongoing' ORDER BY a.created_at DESC LIMIT 8");
        $stmt->execute();
        $ongoings = $stmt->fetchAll();

        // Latest news
        $nstmt = $db->prepare('SELECT n.id, n.title, n.summary, n.image_url, n.published_at, u.display_name AS author FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.is_published = 1 ORDER BY n.published_at DESC LIMIT 6');
        $nstmt->execute();
        $news = $nstmt->fetchAll();

        // Top anime by rating
        $tstmt = $db->prepare('SELECT a.id, a.title, a.title_ua, a.cover_url, IFNULL((SELECT AVG(score) FROM rating r WHERE r.anime_id = a.id),0) AS rating FROM anime a ORDER BY rating DESC LIMIT 10');
        $tstmt->execute();
        $top = $tstmt->fetchAll();

        $this->render('index/main', ['ongoings'=>$ongoings, 'news'=>$news, 'top'=>$top], 'Головна');
    }
}
