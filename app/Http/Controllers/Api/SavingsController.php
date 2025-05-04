<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SavingsRes;
use App\Models\Savings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavingsController extends Controller
{
    public function index()
    {
        // Ambil semua data tabungan
        $savings = Savings::latest()->paginate(10);
        return new SavingsRes(true, 'Daftar Transaksi Tabungan', $savings);
    }

    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'debit' => 'required_without:credit|numeric',
            'credit' => 'required_without:debit|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        // Hitung saldo
        // $lastBalance = Savings::latest()->first()?->balance ?? 0;
        // $newBalance = $lastBalance + $request->debit - $request->credit;

        // Simpan transaksi
        $saving = Savings::create([
            'debit' => $request->debit ?? 0,
            'credit' => $request->credit ?? 0,
            // 'balance' => $newBalance,
        ]);

        return new SavingsRes(true, 'Transaksi berhasil disimpan', $saving);
    }

    public function show($id)
    {
        $saving = Savings::find($id);

        if (!$saving) {
            return response()->json([
                "success" => false,
                "message" => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return new SavingsRes(true, 'Detail Transaksi', $saving);
    }

    public function destroy($id)
    {
        $saving = Savings::find($id);

        if (!$saving) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $saving->delete();

        return new SavingsRes(true, 'Transaksi berhasil dihapus', null);
    }
}
