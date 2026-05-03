<?php

class CharacterController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_view(): void
    {
        $id = (int)($this->request->get('id', 0));
        if ($id <= 0) {
            $this->redirect('index/main');
            return;
        }

        // select name as name_ua so views that use name_ua work even without a dedicated column
        $stmt = $this->db->prepare('SELECT *, name AS name_ua FROM character WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) {
            $this->show404('Персонаж не знайдений');
            return;
        }

        // related anime
        $ast = $this->db->prepare('SELECT a.* FROM anime a JOIN anime_character ac ON a.id = ac.anime_id WHERE ac.character_id = :id');
        $ast->execute([':id' => $id]);
        $anime = $ast->fetchAll();

        // related manga
        $mst = $this->db->prepare('SELECT m.* FROM manga m JOIN manga_character mc ON m.id = mc.manga_id WHERE mc.character_id = :id');
        $mst->execute([':id' => $id]);
        $manga = $mst->fetchAll();

        $this->render('character/view', ['item' => $item, 'anime' => $anime, 'manga' => $manga], $item['name_ua'] ?: $item['name']);
    }
}
