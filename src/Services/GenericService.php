<?php
namespace Konnect\NayaFramework\Services;

use Konnect\NayaFramework\Models\Model;
use PDOException;

class GenericService extends Service{

    public function __construct(Model $model, array $rules=[]) {
        parent::__construct($model, $rules);
    }
}
