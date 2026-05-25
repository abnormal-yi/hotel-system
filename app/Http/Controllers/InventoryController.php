<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('inventory_items');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
        }
        $items = $query->orderBy('name')->paginate(20);
        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);
        DB::table('inventory_items')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        return redirect()->route('inventory.index')->with('success', 'Item added.');
    }

    public function edit($id)
    {
        $item = DB::table('inventory_items')->where('id', $id)->first();
        abort_unless($item, 404);
        return view('inventory.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);
        DB::table('inventory_items')->where('id', $id)->update($data + ['updated_at' => now()]);
        return redirect()->route('inventory.index')->with('success', 'Item updated.');
    }

    public function destroy($id)
    {
        DB::table('inventory_items')->where('id', $id)->delete();
        return redirect()->route('inventory.index')->with('success', 'Item removed.');
    }
}
