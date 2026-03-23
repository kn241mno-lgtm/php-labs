<?php
class Movie {
    public $title;
    public $director;
    public $year;

    public function __construct($title, $director, $year) {
        $this->title = $title;
        $this->director = $director;
        $this->year = $year;
    }

    public function getInfo() {
        return "Фільм: {$this->title}, Режисер: {$this->director}, Рік: {$this->year}";
    }

    public function __clone(): void {
        $this->title = "Без назви";
        $this->director = "";
        $this->year = 0;
    }
}