<?php

namespace Konnect\NayaFramework\Services;

use Konnect\NayaFramework\Lib\Request;
use Konnect\NayaFramework\Models\Model;
use PDOException;

abstract class Service
{

    protected Request $request;
    protected Model $model;
    protected array $rules;
    protected string $uploadDir = 'uploads/'; // Default upload directory

    public function __construct(Model $model, array $rules = [])
    {
        $this->model = $model;
        $this->rules = $rules;
        $this->request = new Request();
    }

    // Validation rules
    private function validateData(): array
    {
        $errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                $fieldValue = $this->request->take($field);

                // Ensure the field value is not null before validation
                if ($fieldValue === null) {
                    $fieldValue = ''; // Set to an empty string if null
                }

                // Required rule
                if ($rule === 'required' && empty($fieldValue)) {
                    $errors[] = "$field is required.";
                }

                // Email rule
                if ($rule === 'email' && !filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "$field must be a valid email.";
                }

                // Min length rule (e.g., min:6)
                if (str_starts_with($rule, 'min:')) {
                    $minLength = (int)substr($rule, 4);
                    if (strlen($fieldValue) < $minLength) {
                        $errors[] = "$field must be at least $minLength characters long.";
                    }
                }

                // Max length rule (e.g., max:255)
                if (str_starts_with($rule, 'max:')) {
                    $maxLength = (int)substr($rule, 4);
                    if (strlen($fieldValue) > $maxLength) {
                        $errors[] = "$field must not exceed $maxLength characters.";
                    }
                }

                // Numeric rule
                if ($rule === 'numeric' && !is_numeric($fieldValue)) {
                    $errors[] = "$field must be a number.";
                }

                // Integer rule
                if ($rule === 'integer' && !filter_var($fieldValue, FILTER_VALIDATE_INT)) {
                    $errors[] = "$field must be an integer.";
                }

                // Alpha rule
                if ($rule === 'alpha' && !ctype_alpha($fieldValue)) {
                    $errors[] = "$field must only contain alphabetic characters.";
                }

                // Alpha numeric rule
                if ($rule === 'alpha_num' && !ctype_alnum($fieldValue)) {
                    $errors[] = "$field must only contain alphanumeric characters.";
                }

                // Min value rule
                if (str_starts_with($rule, 'min_value:')) {
                    $minValue = (int)substr($rule, 10);
                    if ((int)$fieldValue < $minValue) {
                        $errors[] = "$field must be greater than or equal to $minValue.";
                    }
                }

                // Max value rule
                if (str_starts_with($rule, 'max_value:')) {
                    $maxValue = (int)substr($rule, 10);
                    if ((int)$fieldValue > $maxValue) {
                        $errors[] = "$field must be less than or equal to $maxValue.";
                    }
                }

                // Confirmed rule
                if ($rule === 'confirmed' && $fieldValue !== $this->request->take("confirm_" . $field)) {
                    $errors[] = "$field confirmation does not match.";
                }

                // Date rule
                if ($rule === 'date' && !strtotime($fieldValue)) {
                    $errors[] = "$field must be a valid date.";
                }

                // In rule
                if (str_starts_with($rule, 'in:')) {
                    $values = explode(',', substr($rule, 3));
                    if (!in_array($fieldValue, $values)) {
                        $errors[] = "$field must be one of the following values: " . implode(', ', $values);
                    }
                }

                // Unique rule (assuming you have this method)
                if ($rule === 'unique') {
                    if (!$this->checkUniqueField($field, $fieldValue)) {
                        $errors[] = "$field must be unique.";
                    }
                }

                // URL rule
                if ($rule === 'url' && !filter_var($fieldValue, FILTER_VALIDATE_URL)) {
                    $errors[] = "$field must be a valid URL.";
                }

                // Regex rule
                if (str_starts_with($rule, 'regex:')) {
                    $pattern = substr($rule, 6);
                    if (!preg_match($pattern, $fieldValue)) {
                        $errors[] = "$field does not match the required format.";
                    }
                }
            }
        }

        return $errors;
    }


    // File validation method
    private function validateFile($file, array $rules): array
    {
        $errors = [];

        // Check if the file is provided
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error.';
        }

        // Validate file size based on the 'max_size' rule
        if (isset($rules['max_size']) && $file['size'] > $rules['max_size']) {
            $errors[] = 'File size exceeds the maximum limit.';
        }

        // Validate file type based on the 'mimes' rule
        if (isset($rules['mimes'])) {
            $allowedTypes = $rules['mimes'];
            if (!in_array($file['type'], $allowedTypes)) {
                $errors[] = 'Invalid file type. Allowed types are: ' . implode(', ', $allowedTypes);
            }
        }

        return $errors;
    }


    // Handle file upload and save
    private function saveFile($file): string
    {
        $fileName = uniqid('', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $this->uploadDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $filePath;
        }
        return '';
    }

    // Create operation (insert a new record)
    public function create(): array
    {
        try {
            $data = $this->request->all();
            $validationErrors = $this->validateData();
            if (!empty($validationErrors)) {
                return $validationErrors;
            }

            if (!empty($files['file'])) {
                $fileRules = $this->rules['file'] ?? [];
                $fileErrors = $this->validateFile($files['file'], $fileRules);
                if (!empty($fileErrors)) {
                    return $fileErrors;
                }
                $data['file_path'] = $this->saveFile($files['file']);
            }

            return $this->model->create($data);

        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ];
        }
    }

    // Read operation (fetch all records or a specific one)
    public function read(int $id = null, array $withRelations = []): array
    {
        try {
            // If an ID is passed, fetch that specific record
            if ($id) {
                $record = $this->model->find($id, $withRelations);
                return $record ? $record : ['status' => 'error', 'message' => 'Record not found.'];
            }

            return $this->model->all($withRelations);
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Update operation (modify an existing record)
    public function update(int $id): array
    {
        try {
            $data = $this->request->all();
            $validationErrors = $this->validateData();
            if (!empty($validationErrors)) {
                return $validationErrors;
            }

            if (!empty($files['file'])) {
                $fileRules = $this->rules['file'] ?? [];
                $fileErrors = $this->validateFile($files['file'], $fileRules);
                if (!empty($fileErrors)) {
                    return $fileErrors;
                }
                $data['file_path'] = $this->saveFile($files['file']);
            }

            $updatedRecord = $this->model->update($id, $data);
            if ($updatedRecord) {
                return $this->model->find($id);
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Record not found or update failed.',
                ];
            }
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ];
        }
    }

    // Delete operation (remove a record)
    public function delete(int $id): array
    {
        try {
            $deleted = $this->model->delete($id);
            if ($deleted) {
                return [
                    'status' => 'success',
                    'message' => 'Record deleted successfully!',
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Record not found or deletion failed.',
                ];
            }
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ];
        }
    }

    private function checkUniqueField(int|string $field, mixed $field1): bool
    {
        try {
            $record = $this->model->find($field1);
            return (bool)$record;
        } catch (PDOException $e) {
            return false;
        }
    }
}
