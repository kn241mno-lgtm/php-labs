<?php
class Movie {
    public $title;
    public $director;
    public $year;

    public function __construct(string $title, string $director, int $year) {
        $this->title = $title;
        $this->director = $director;
        $this->year = $year;
    }

    public function getInfo() {
        return "Фільм: {$this->title}, Режисер: {$this->director}, Рік: {$this->year}";
    }
}