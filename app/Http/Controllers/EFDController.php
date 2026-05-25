<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EFDController extends Controller
{
    public function index()
    {
        $transactions = DB::table('efd_transactions')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('efd.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = DB::table('efd_transactions')->where('id', $id)->first();
        abort_unless($transaction, 404);
        return view('efd.show', compact('transaction'));
    }

    public function receipt($id)
    {
        $transaction = DB::table('efd_transactions')->where('id', $id)->first();
        abort_unless($transaction, 404);
        return view('efd.receipt', compact('transaction'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payable_type' => 'required|string',
            'payable_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'tin' => 'nullable|string',
        ]);

        $vatRate = 0.18;
        $data['vat'] = $data['amount'] * $vatRate;
        $data['receipt_number'] = 'EFD-' . strtoupper(uniqid());
        $data['status'] = 'completed';

        DB::table('efd_transactions')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        return redirect()->route('efd.index')->with('success', 'EFD receipt generated.');
    }

    public function fromPayment($paymentId)
    {
        $payment = DB::table('payments')->where('id', $paymentId)->first();
        abort_unless($payment, 404);

        $tin = DB::table('system_settings')->where('key', 'tin_number')->value('value') ?? '000-000-000';

        $existing = DB::table('efd_transactions')
            ->where('payable_type', 'payment')
            ->where('payable_id', $paymentId)
            ->first();

        if ($existing) {
            return redirect()->route('efd.receipt', $existing->id);
        }

        $vatRate = 0.18;
        $vat = $payment->amount * $vatRate;

        $id = DB::table('efd_transactions')->insertGetId([
            'receipt_number' => 'EFD-' . strtoupper(uniqid()),
            'payable_type' => 'payment',
            'payable_id' => $paymentId,
            'amount' => $payment->amount,
            'vat' => $vat,
            'tin' => $tin,
            'status' => 'completed',
            'response' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('efd.receipt', $id);
    }
}
