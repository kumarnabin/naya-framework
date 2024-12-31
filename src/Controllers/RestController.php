<?php

namespace Konnect\NayaFramework\Controllers;

use Konnect\NayaFramework\Lib\JsonResponse;
use Konnect\NayaFramework\Services\Service;

abstract class RestController
{
    protected Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    // Display a listing of the resource
    public function index(): void
    {
        // Fetch all models (replace with actual model call)
        $models = $this->service->read(); // Example method

        $response = [
            'status' => 'success',
            'message' => 'List of all models',
            'data' => $models,
        ];

        JsonResponse::send($response);
    }

    // Store a newly created resource in storage
    public function store(): void
    {
        $data = $this->service->create();

        $response = [
            'status' => 'success',
            'message' => 'Model created successfully!',
            'data' => $data,
        ];

        JsonResponse::send($response);
    }

    // Display the specified resource
    public function show(int $id): void
    {
        // Fetch model by ID (replace with actual model call)
        $model = $this->service->read($id); // Example method

        $response = [
            'status' => 'success',
            'data' => $model,
        ];

        JsonResponse::send($response);
    }

    // Update the specified resource in storage
    public function update(int $id): void
    {
        // Fetch model by ID
        $model = $this->service->read($id); // Example method

        if (!$model) {
            $response = [
                'status' => 'error',
                'message' => 'Model not found.',
            ];
            JsonResponse::send($response, 404);
        }

        // Update model in the database
        $data = $this->service->update($id); // Example method

        $response = [
            'status' => 'success',
            'message' => 'Model updated successfully!',
            'data' => $data,
        ];

        JsonResponse::send($response);
    }

    // Remove the specified resource from storage
    public function destroy(int $id): void
    {
        // Delete model from the database
        $deleted = $this->service->delete($id); // Example method

        if ($deleted) {
            $response = [
                'status' => 'success',
                'message' => 'Model deleted successfully!',
            ];
            JsonResponse::send($response);
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Model not found.',
            ];
            JsonResponse::send($response, 404);
        }
    }

    // Helper function to return JSON response

}
