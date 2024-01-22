<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\EditOrderRequest;
use App\Http\Requests\PaymentOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Table;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\OrderCollection
     */
    public function index(): OrderCollection
    {
        $model = Order::query()
            ->paginate();

        return new OrderCollection($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateOrderRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        $this->checkTableUsed($request['table_id']);

        // Update table status
        $this->updateTableStatus($request['table_id'], 0);

        $model = new Order();
        $model->fill($request->only($model->getFillable()));
        $model->save();
        // save to order_details
        if ($request->filled('details')) {
            $details = $request->only(['details'])['details'];
            foreach ($details as $detail) {
                $menu = Menu::find($detail['menu_id']);
                $orderDetail = new OrderDetail();
                $orderDetail->fill([
                    'order_id' => $model->id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'qty' => $detail['qty'],
                    'total' => $menu->price * $detail['qty']
                ]);
                $orderDetail->save();
            }
        }
        $details = $model->details;
        $total = $details->sum('total') ?? $model->total;
        $model->fill([
            'total' => $total,
            'payment' => 0
        ]);
        $model->save();
        DB::commit();

        return response()->json([
            'message' => 'success',
            'data' => new OrderResource($model)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $model
     *
     * @return \App\Http\Resources\OrderResource
     */
    public function show(Order $model): OrderResource
    {
        return new OrderResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\EditOrderRequest $request
     * @param \App\Models\Order $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditOrderRequest $request, Order $model): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        /** @var Order */
        $model = Order::query()
            ->where('id', $model->id)
            ->lockForUpdate()
            ->first();
        $model->fill($request->only($model->getFillable()));
        $model->save();
        // save to order_details
        if ($request->filled('details')) {
            $details = $request->only(['details'])['details'];
            foreach ($details as $detail) {
                $menu = Menu::find($detail['menu_id']);
                $orderDetail = new OrderDetail();
                $orderDetail->fill([
                    'order_id' => $model->id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'qty' => $detail['qty'],
                    'total' => $menu->price * $detail['qty']
                ]);
                $orderDetail->save();
            }
        }
        $details = $model->details;
        $total = $details->sum('total') ?? $model->total;
        $model->fill([
            'total' => $total,
            'payment' => 0
        ]);
        $model->save();
        DB::commit();

        return response()->json([
            'message' => 'success',
            'data' => new OrderResource($model)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $model): \Illuminate\Http\JsonResponse
    {
        $this->updateTableStatus($model->table_id, 1);

        OrderDetail::query()
            ->where('order_id', $model->id)
            ->delete();
        $model->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function payment(PaymentOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        /** @var Order */
        $order = Order::find($request['order_id']);
        $order->fill([
            'payment' => $request['payment']
        ]);
        $order->save();
        if ($order->reservation) {
            /** @var \App\Models\Reservation */
            $reservation = $order->reservation;
            $reservation->fill([
                'end_at' => now()->format('Y-m-d H:i:s')
            ]);
            $reservation->save();
        }

        $this->updateTableStatus($order->table_id, 1);

        return response()->json([
            'message' => 'success',
            'data' => new OrderResource($order)
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
