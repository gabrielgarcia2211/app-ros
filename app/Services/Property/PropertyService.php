<?php

namespace App\Services\Property;

use App\Models\Property\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Interfaces\Property\PropertyRepositoryInterface;


class PropertyService
{
    protected $propertyRepository;
    protected $fileService;

    public function __construct(PropertyRepositoryInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function getPropertiesQuery()
    {
        $query = Property::query();
        return $query->distinct();
    }

    public function storeProperty(array $data)
    {
        DB::beginTransaction();
        try {
            $property = $this->propertyRepository->create($data);
            DB::commit();
            return $property;
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info($ex->getLine());
            Log::info($ex->getMessage());
            throw $ex;
        }
    }

    public function updateProperty(array $data, $id)
    {
        DB::beginTransaction();
        try {
            $property = $this->propertyRepository->update($id, $data);
            DB::commit();
            return $property;
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info($ex->getLine());
            Log::info($ex->getMessage());
            throw $ex;
        }
    }

    public function deleteProperty($id)
    {
        try {
            $this->propertyRepository->delete($id);
            return true;
        } catch (\Exception $ex) {
            Log::info($ex->getLine());
            Log::info($ex->getMessage());
            throw $ex;
        }
    }

    public function showProperty($id)
    {
        try {
            return $this->propertyRepository->findById($id);
        } catch (\Exception $ex) {
            Log::info($ex->getLine());
            Log::info($ex->getMessage());
            throw $ex;
        }
    }
}
