<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMenuRequest;
use App\Http\Requests\EditMenuRequest;
use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuResource;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\MenuCollection
     */
    public function index(): MenuCollection
    {
        $model = Menu::query()
            ->paginate();

        return new MenuCollection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateMenuRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateMenuRequest $request): \Illuminate\Http\JsonResponse
    {
        $model = new Menu();
        $model->fill($request->only($model->getFillable()));
        $model->save();

        return response()->json([
            'message' => 'success',
            'data' => new MenuResource($model)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Menu $model
     *
     * @return \App\Http\Resources\MenuResource
     */
    public function show(Menu $model): MenuResource
    {
        return new MenuResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\EditMenuRequest $request
     * @param \App\Models\Menu $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditMenuRequest $request, Menu $model): \Illuminate\Http\JsonResponse
    {
        $model->fill($request->only($model->getFillable()));
        $model->save();

        return response()->json([
            'message' => 'success',
            'data' => new MenuResource($model)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Menu $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Menu $model): \Illuminate\Http\JsonResponse
    {
        $model->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }
}
