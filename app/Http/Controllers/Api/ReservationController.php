<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\CreateReservationRequest;
use App\Http\Requests\EditOrderRequest;
use App\Http\Requests\EditReservationRequest;
use App\Http\Requests\PaymentOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ReservationCollection;
use App\Http\Resources\ReservationResource;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\ReservationCollection
     */
    public function index(): ReservationCollection
    {
        $model = Reservation::query()
            ->paginate();

        return new ReservationCollection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateReservationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateReservationRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        $this->checkTableUsed($request['table_id']);

        // Update table status
        $this->updateTableStatus($request['table_id'], 0);

        // Save to reservations
        $model = new Reservation();
        $model->fill($request->only($model->getFillable()));
        $model->save();

        // Save to orders
        $order = new Order();
        $order->fill([
            'table_id' => $model->table_id,
            'total' => 0
        ]);
        $order->save();
        // Save to order_details
        if ($request->filled('order_details')) {
            $details = $request->only(['order_details'])['order_details'];
            foreach ($details as $detail) {
                $menu = Menu::find($detail['menu_id']);
                $orderDetail = new OrderDetail();
                $orderDetail->fill([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'qty' => $detail['qty'],
                    'total' => $menu->price * $detail['qty']
                ]);
                $orderDetail->save();
            }
        }
        $details = $order->details;
        $total = $details->sum('total') ?? $order->total;
        $order->fill([
            'total' => $total,
            'payment' => 0
        ]);
        $order->save();
        // Update reservations
        $model->fill([
            'order_id' => $order->id ?? null
        ]);
        $model->save();
        DB::commit();

        return response()->json([
            'message' => 'success',
            'data' => new ReservationResource($model)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Reservation $model
     *
     * @return \App\Http\Resources\ReservationResource
     */
    public function show(Reservation $model): ReservationResource
    {
        return new ReservationResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\EditReservationRequest $request
     * @param \App\Models\Reservation $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditReservationRequest $request, Reservation $model): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        /** @var Reservation */
        $model = Reservation::query()
            ->where('id', $model->id)
            ->lockForUpdate()
            ->first();
        $model->fill($request->only($model->getFillable()));
        $model->save();

        // Save to orders
        /** @var Order */
        $order = $model->order_id
            ? $model->order
            : new Order();
        $order->fill([
            'table_id' => $model->table_id,
            'total' => 0
        ]);
        $order->save();
        // Save to order_details
        if ($request->filled('order_details')) {
            $details = $request->only(['order_details'])['order_details'];
            foreach ($details as $detail) {
                $menu = Menu::find($detail['menu_id']);
                $orderDetail = new OrderDetail();
                $orderDetail->fill([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'qty' => $detail['qty'],
                    'total' => $menu->price * $detail['qty']
                ]);
                $orderDetail->save();
            }
        }
        $details = $order->details;
        $total = $details->sum('total') ?? $order->total;
        $order->fill([
            'total' => $total,
            'payment' => 0
        ]);
        $order->save();
        // Update reservations
        $model->fill([
            'order_id' => $order->id ?? null
        ]);
        $model->save();
        DB::commit();

        return response()->json([
            'message' => 'success',
            'data' => new ReservationResource($model)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Reservation $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Reservation $model): \Illuminate\Http\JsonResponse
    {
        $this->updateTableStatus($model->table_id, 1);
        $model->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function checkTableUsed(int $tableId): void
    {
        $checkTable = Table::query()
            ->where('id', $tableId)
            ->where('is_open', 0)
            ->exists();
        if ($checkTable) {
            throw new HttpResponseException(response()->json([
                'message' => 'Error, table still used!'
            ], 422));
        }
    }

    public function updateTableStatus(int $tableId, int $isOpen): void
    {
        Table::query()
            ->where('id', $tableId)
            ->update([
                'is_open' => $isOpen
            ]);
    }
}
