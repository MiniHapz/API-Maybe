<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetsRes;
use App\Models\Budgets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BudgetsController extends Controller
{
    public function index()
    {
        // Ambil semua data anggaran
        $budgets = Budgets::latest()->paginate(10);
        return new BudgetsRes(true, 'Daftar Anggaran Bulanan', $budgets);
    }

    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'kategori_id' => 'required|integer',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        // Buat data anggaran baru
        $budget = Budgets::create([
            'user_id' => $request->user_id,
            'kategori_id' => $request->kategori_id,
            'jumlah' => $request->jumlah,
        ]);

        return new BudgetsRes(true, 'Berhasil menambahkan anggaran', $budget);
    }

    public function show($id)
    {
        // Cari anggaran berdasarkan ID
        $budget = Budgets::find($id);

        if (!$budget) {
            return response()->json([
                "success" => false,
                "message" => 'Anggaran tidak ditemukan'
            ], 404);
        }

        return new BudgetsRes(true, 'Detail Anggaran', $budget);
    }

    public function update(Request $request, $id)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer',
            'jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        $budget = Budgets::find($id);

        if (!$budget) {
            return response()->json(['message' => 'Anggaran tidak ditemukan'], 404);
        }

        // Update data anggaran
        $budget->update([
            'kategori_id' => $request->kategori_id,
            'jumlah' => $request->jumlah,
        ]);

        return new BudgetsRes(true, 'Data anggaran berhasil di-update', $budget);
    }

    public function destroy($id)
    {
        $budget = Budgets::find($id);

        if (!$budget) {
            return response()->json(['message' => 'Anggaran tidak ditemukan'], 404);
        }

        // Hapus data anggaran
        $budget->delete();

        return new BudgetsRes(true, 'Data anggaran berhasil dihapus', null);
    }
}
