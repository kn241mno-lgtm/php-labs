<?php
/**
 * Завдання 1: Форматований текст
 *
 * Вірш про художника з форматуванням: <b>, <i>, margin-left
 */
require_once __DIR__ . '/layout.php';

ob_start();
?>
<div class="poem">
    <?php
    echo "<p class='poem-indent-1'>Дорога <b>кличе</b> за обрій далекий,";
    echo "<p class='poem-indent-1'>Вітер несе запах <i>свободи</i> й трав,";
    echo "<p class='poem-indent-1'>Карпатські вершини сяють у сонці,";
    echo "<p class='poem-indent-1'>І кожен мандрівник тут щастя знав.";
    ?>
</div>
<?php
$content = ob_get_clean();

renderVariantLayout($content, 'Завдання 1', 'task2-body');
