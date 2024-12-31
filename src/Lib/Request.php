<?php

namespace Konnect\NayaFramework\Lib;

class Request
{
    // Store GET, POST, and raw JSON data
    private array $get;
    private array $post;
    private array $json;

    public function __construct()
    {
        // Initialize GET and POST data
        $this->get = $_GET;
        $this->post = $_POST;
        $this->json = [];

        // Parse incoming JSON data if the content-type is application/json
        if ($this->isJsonRequest()) {
            $this->json = json_decode(file_get_contents('php://input'), true) ?? [];
        }
    }

    // Check if the request has JSON data
    private function isJsonRequest(): bool
    {
        return isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json');
    }

    // Get a parameter from the GET request
    public function get($key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    // Get a parameter from the POST request
    public function post($key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    // Get a parameter from the JSON request
    public function json($key, $default = null)
    {
        return $this->json[$key] ?? $default;
    }

    // Get all GET parameters
    public function allGet(): array
    {
        return $this->get;
    }

    // Get all POST parameters
    public function allPost(): array
    {
        return $this->post;
    }

    // Get all parameters (GET, POST, and JSON merged)
    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->json);
    }

    // Get a parameter from either GET, POST, or JSON
    public function take($key)
    {
        return $this->json[$key] ?? $this->post[$key] ?? $this->get[$key] ?? null;
    }
}
