<?php

namespace Konnect\NayaFramework\Lib;

class Request
{
    private array $get;
    private array $post;
    private array $json;
    private array $files;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->json = [];
        $this->files = $_FILES;

        if ($this->isJsonRequest()) {
            $this->json = json_decode(file_get_contents('php://input'), true) ?? [];
        }
    }

    private function isJsonRequest(): bool
    {
        return isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json');
    }

    public function get($key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function post($key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function json($key, $default = null)
    {
        return $this->json[$key] ?? $default;
    }

    public function allGet(): array
    {
        return $this->get;
    }

    public function allPost(): array
    {
        return $this->post;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->json);
    }

    public function take($key)
    {
        return $this->json[$key] ?? $this->post[$key] ?? $this->get[$key] ?? null;
    }

    // Check if a file is present in the request
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    // Get details of a specific uploaded file
    public function file(string $key): ?array
    {
        return $this->hasFile($key) ? $this->files[$key] : null;
    }

    // Get all uploaded files
    public function allFiles(): array
    {
        return $this->files;
    }
}
