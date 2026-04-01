<?php

class Request
{
    private array $get;
    private array $post;
    private string $method;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function allGet(): array
    {
        return $this->get;
    }

    public function allPost(): array
    {
        return $this->post;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function method(): string
    {
        return $this->method;
    }
}
