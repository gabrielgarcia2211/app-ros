<?php

namespace App\Http\Controllers\Property;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Property\PropertyService;
use App\Http\Controllers\ResponseController as Response;

class PropertyController extends Controller
{
    protected $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->propertyService->getPropertiesQuery();
            return renderDataTable(
                $query,
                $request,
                [],
                [
                    'properties.id'
                ]
            );
        } catch (\Exception $ex) {
            return Response::sendError('Ocurrio un error inesperado al intentar procesar la solicitud', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $property = $this->propertyService->storeProperty($request->all());
            return Response::sendResponse($property, 'Registro creado con exito.');
        } catch (\Exception $ex) {
            return Response::sendError('Ocurrio un error inesperado al intentar procesar la solicitud', 500);
        }
    }

    public function show($id)
    {
        try {
            $property = $this->propertyService->showProperty($id);
            return Response::sendResponse($property, 'Registro obtenido con exito.');
        } catch (\Exception $ex) {
            return Response::sendError('Ocurrio un error inesperado al intentar procesar la solicitud', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $property = $this->propertyService->updateProperty($request->all(), $id);
            return Response::sendResponse($property, 'Registro actualizado con exito.');
        } catch (\Exception $ex) {
            return Response::sendError('Ocurrio un error inesperado al intentar procesar la solicitud', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->propertyService->deleteProperty($id);
            return Response::sendResponse(true, 'Registro eliminado con exito.');
        } catch (\Exception $ex) {
            return Response::sendError('Ocurrio un error inesperado al intentar procesar la solicitud', 500);
        }
    }
}
