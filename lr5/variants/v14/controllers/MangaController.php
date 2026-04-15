<?php

class MangaController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_list(): void
    {
        $stmt = $this->db->prepare('SELECT * FROM manga ORDER BY created_at DESC');
        $stmt->execute();
        $items = $stmt->fetchAll();

        $this->render('manga/list', ['manga' => $items], 'Каталог манги');
    }

    public function action_view(): void
    {
        $id = (int)($this->request->get('id', 0));
        if ($id <= 0) {
            $this->redirect('manga/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM manga WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) {
            $this->show404('Манга не знайдена');
            return;
        }

        $cstmt = $this->db->prepare('SELECT c.*, u.login FROM comments c JOIN users u ON u.id = c.user_id WHERE c.item_type = :type AND c.item_id = :id ORDER BY c.created_at DESC');
        $cstmt->execute([':type' => 'manga', ':id' => $id]);
        $comments = $cstmt->fetchAll();

        $this->render('manga/view', ['item' => $item, 'comments' => $comments], $item['title']);
    }
}
