<?php

// app/Http/Controllers/Api/CekSaldoController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Savings;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekSaldoController extends Controller
{
    public function index(Request $request)
{
    // Ambil user yang sedang login
    $user = Auth::user();

    // Ambil riwayat transaksi
    $riwayatTransaksiSavings = Savings::where('user_id', $user->id)->latest()->get();
    $riwayatTransaksiTransferKeluar = Transfer::where('user_id', $user->id)->latest()->get();
    $riwayatTransaksiTransferMasuk = Transfer::where('user_diterima_id', $user->id)->latest()->get();

    // Gabungkan riwayat transaksi
    $riwayatTransaksi = collect();
    $riwayatTransaksi = $riwayatTransaksi->concat($riwayatTransaksiSavings);
    $riwayatTransaksi = $riwayatTransaksi->concat($riwayatTransaksiTransferKeluar);
    $riwayatTransaksi = $riwayatTransaksi->concat($riwayatTransaksiTransferMasuk);
    $riwayatTransaksi = $riwayatTransaksi->sortByDesc('created_at');

        // Hitung balance
    $balance = $riwayatTransaksi->first()->balance ?? $user->saldo;

    return response()->json([
        'success' => true,
        'balance' => $balance,
        'riwayat_transaksi' => $riwayatTransaksi,
    ]);
}
}