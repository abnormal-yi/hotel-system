<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $orders = DB::table('pos_orders')
            ->leftJoin('guests', 'pos_orders.guest_id', '=', 'guests.id')
            ->leftJoin('users', 'pos_orders.user_id', '=', 'users.id')
            ->leftJoin('rooms', 'pos_orders.room_id', '=', 'rooms.id')
            ->select('pos_orders.*', 'guests.name as guest_name', 'users.name as user_name', 'rooms.room_number')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('pos.index', compact('orders'));
    }

    public function create()
    {
        $guests = Guest::orderBy('name')->get();
        $items = DB::table('inventory_items')->where('quantity', '>', 0)->orderBy('name')->get();
        return view('pos.form', compact('guests', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_id' => 'nullable|exists:guests,id',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $orderNumber = 'POS-' . now()->format('Ymd') . '-' . str_pad(DB::table('pos_orders')->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $subtotal = 0;
        $orderItems = [];
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['unit_price'];
            $subtotal += $total;
            $orderItems[] = [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
            ];
        }
        $tax = $subtotal * 0.18;
        $total = $subtotal + $tax;

        $orderId = DB::table('pos_orders')->insertGetId([
            'order_number' => $orderNumber,
            'guest_id' => $validated['guest_id'] ?? null,
            'user_id' => auth()->id(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($orderItems as $oi) {
            $oi['pos_order_id'] = $orderId;
            $oi['created_at'] = now();
            $oi['updated_at'] = now();
            DB::table('pos_order_items')->insert($oi);
        }

        return redirect()->route('pos.show', $orderId)->with('success', 'Order created.');
    }

    public function show($id)
    {
        $order = DB::table('pos_orders')->where('id', $id)->first();
        abort_unless($order, 404);
        $items = DB::table('pos_order_items')->where('pos_order_id', $id)->get();
        $guest = $order->guest_id ? DB::table('guests')->where('id', $order->guest_id)->first() : null;
        $user = DB::table('users')->where('id', $order->user_id)->first();
        $room = $order->room_id ? DB::table('rooms')->where('id', $order->room_id)->first() : null;
        return view('pos.show', compact('order', 'items', 'guest', 'user', 'room'));
    }

    public function complete($id)
    {
        DB::table('pos_orders')->where('id', $id)->update([
            'status' => 'completed',
            'updated_at' => now(),
        ]);
        return redirect()->route('pos.show', $id)->with('success', 'Order completed.');
    }

    public function roomOrderCreate()
    {
        $rooms = Room::whereIn('status', ['occupied', 'available'])->where('is_active', true)->orderBy('room_number')->get();
        $guests = Guest::orderBy('name')->get();
        $items = DB::table('inventory_items')->where('quantity', '>', 0)->orderBy('name')->get();
        return view('pos.room-order', compact('rooms', 'guests', 'items'));
    }

    public function roomOrderStore(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'nullable|exists:guests,id',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $orderNumber = 'RM-' . now()->format('Ymd') . '-' . str_pad(DB::table('pos_orders')->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $subtotal = 0;
        $orderItems = [];
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['unit_price'];
            $subtotal += $total;
            $orderItems[] = [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
            ];
        }
        $tax = $subtotal * 0.18;
        $total = $subtotal + $tax;

        $orderId = DB::table('pos_orders')->insertGetId([
            'order_number' => $orderNumber,
            'guest_id' => $validated['guest_id'] ?? null,
            'user_id' => auth()->id(),
            'room_id' => $validated['room_id'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'order_type' => 'room_order',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($orderItems as $oi) {
            $oi['pos_order_id'] = $orderId;
            $oi['created_at'] = now();
            $oi['updated_at'] = now();
            DB::table('pos_order_items')->insert($oi);
        }

        return redirect()->route('pos.show', $orderId)->with('success', 'Room order created.');
    }
}
