<?php

namespace App\Services\Property;

use App\Models\Property\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Interfaces\Property\PropertyRepositoryInterface;
use App\Interfaces\UserProperty\UserPropertyRepositoryInterface;


class PropertyService
{
    protected $propertyRepository;
    protected $userPropertyRepository;
    protected $fileService;

    public function __construct(PropertyRepositoryInterface $propertyRepository, UserPropertyRepositoryInterface $userPropertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
        $this->userPropertyRepository = $userPropertyRepository;
    }

    public function getPropertiesQuery()
    {
        $query = Property::query()
            ->leftJoin('enum_options as eo_property_type', 'eo_property_type.id', '=', 'properties.property_type_id')
            ->leftJoin('user_properties', 'user_properties.property_id', '=', 'properties.id')
            ->leftJoin('users as user_owner', function ($join) {
                $join->on('user_owner.id', '=', 'user_properties.user_id')
                    ->join('model_has_roles as mhr_owner', 'mhr_owner.model_id', '=', 'user_owner.id')
                    ->join('roles as r_owner', 'r_owner.id', '=', 'mhr_owner.role_id')
                    ->where('r_owner.name', '=', 'owners')
                    ->where('mhr_owner.model_type', '=', 'App\\Models\\User');
            })
            ->leftJoin('users as user_tenant', function ($join) {
                $join->on('user_tenant.id', '=', 'user_properties.user_id')
                    ->join('model_has_roles as mhr_tenant', 'mhr_tenant.model_id', '=', 'user_tenant.id')
                    ->join('roles as r_tenant', 'r_tenant.id', '=', 'mhr_tenant.role_id')
                    ->where('r_tenant.name', '=', 'tenants')
                    ->where('mhr_tenant.model_type', '=', 'App\\Models\\User');
            })
            ->groupBy([
                'properties.id',
                'properties.name',
                'properties.address',
                'properties.status',
                'eo_property_type.id',
                'eo_property_type.name',
            ]);

        return $query->distinct();
    }

    public function storeProperty(array $data)
    {
        DB::beginTransaction();
        try {
            $owners = $data['owners'];
            $tenants = $data['tenants'];
            $property = $this->propertyRepository->create($data);
            foreach ($owners as $key => $value) {
                $this->userPropertyRepository->create([
                    'property_id' => $property->id,
                    'user_id' => $value,
                ]);
            }
            foreach ($tenants as $key => $value) {
                $this->userPropertyRepository->create([
                    'property_id' => $property->id,
                    'user_id' => $value,
                ]);
            }
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
            $owners = $data['owners'];
            $tenants = $data['tenants'];
            $property = $this->propertyRepository->update($id, $data);
            $this->userPropertyRepository->deleteByProperty($id);
            foreach ($owners as $key => $value) {
                $this->userPropertyRepository->create([
                    'property_id' => $property->id,
                    'user_id' => $value,
                ]);
            }
            foreach ($tenants as $key => $value) {
                $this->userPropertyRepository->create([
                    'property_id' => $property->id,
                    'user_id' => $value,
                ]);
            }
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
        DB::beginTransaction();
        try {
            $this->userPropertyRepository->deleteByProperty($id);
            $this->propertyRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
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
