<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTableRequest;
use App\Http\Requests\EditTableRequest;
use App\Http\Resources\TableCollection;
use App\Http\Resources\TableResource;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\TableCollection
     */
    public function index(): TableCollection
    {
        $model = Table::query()
            ->paginate();

        return new TableCollection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateTableRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateTableRequest $request): \Illuminate\Http\JsonResponse
    {
        $model = new Table();
        $model->fill($request->only($model->getFillable()));
        $model->save();

        return response()->json([
            'message' => 'success',
            'data' => new TableResource($model)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Table $model
     *
     * @return \App\Http\Resources\TableResource
     */
    public function show(Table $model): TableResource
    {
        return new TableResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\EditTableRequest $request
     * @param \App\Models\Table $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditTableRequest $request, Table $model): \Illuminate\Http\JsonResponse
    {
        $model->fill($request->only($model->getFillable()));
        $model->save();

        return response()->json([
            'message' => 'success',
            'data' => new TableResource($model)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Table $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Table $model): \Illuminate\Http\JsonResponse
    {
        $model->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function getOpenTables(): TableCollection
    {
        $tables = Table::query()
            ->where('is_open', 1)
            ->paginate();

        return new TableCollection($tables);
    }
}
