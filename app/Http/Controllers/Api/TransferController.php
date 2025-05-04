<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function __invoke(Request $request)
{
    // Validasi data
    $request->validate([
        'user_id' => 'required|integer',
        'jumlah' => 'required|numeric',
    ]);

    // Ambil user yang sedang login
    $user = Auth::user();

    // Ambil user yang akan ditransfer
    $userDiterima = User::find($request->user_id);

    // Cek jika user yang akan ditransfer tidak ada
    if (!$userDiterima) {
        return response()->json([
            'success' => false,
            'message' => 'User tidak ditemukan',
        ], 404);
    }

    // Cek jika saldo user yang sedang login tidak cukup
    $balance = $user->saldo;
    if ($balance < $request->jumlah) {
        return response()->json([
            'success' => false,
            'message' => 'Saldo tidak cukup',
        ], 422);
    }

    // Mulai transaksi database
    DB::beginTransaction();

    try {
         // Buat transaksi transfer
        $transfer = Transfer::create([
            'user_id' => $user->id,
            'user_diterima_id' => $userDiterima->id,
            'jumlah' => $request->jumlah,
        ]);

        // Update saldo user yang sedang login
        $newBalance = $balance - $request->jumlah;
        $user->saldo = $newBalance;
        $user->save();

        // Update saldo user yang akan ditransfer
        $newBalancePenerima = $userDiterima->saldo + $request->jumlah;
        $userDiterima->saldo = $newBalancePenerima;
        $userDiterima->save();

        // Commit transaksi database
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Transfer berhasil',
            'data' => [
                'saldo_sebelum' => $balance,
                'saldo_sesudah' => $newBalance,
                'jumlah_transfer' => $request->jumlah,
                'penerima' => $userDiterima->name,
            ],
        ]);
    } catch (\Exception $e) {
        // Rollback transaksi database jika terjadi kesalahan
        DB::rollback();

        return response()->json([
            'success' => false,
            'message' => 'Transfer gagal',
        ], 500);
    }
}
}
