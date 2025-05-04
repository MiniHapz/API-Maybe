<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriRes;
use App\Models\Kategori;
use Illuminate\Support\Facades\Validator; // Namespace Validator yang benar
use Illuminate\Support\Facades\Storage;  // Tambahkan namespace Storage
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index() 
    {
        // Ambil 5 data terbaru
        $kategori = Kategori::latest()->paginate(5);
        return new KategoriRes(true, 'Daftar Data Kategori', $kategori);
    }

    // Untuk menambahkan kategori baru
    public function store(Request $request)
    {
        // Aturan validasi
        $validator = Validator::make($request->all(), [
            'pic' => 'required|image|mimes:png,jpg,svg,gif,jpeg|max:2048',
            'nama' => 'required',
            'keterangan' => 'required',
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }
        
        // Upload gambar
        $pic = $request->file('pic');
        $pic->storeAs('public/kategori', $pic->hashName());

        // Buat kategori baru
        $kategori = Kategori::create([
            'pic' => $pic->hashName(),
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
        ]);

        // Berikan respon
        return new KategoriRes(true, 'Berhasil menambahkan kategori', $kategori);
    }

    // Menampilkan data berdasarkan id
    public function show($id)
    {
        // Cari data berdasarkan id
        $kategori = Kategori::find($id);

        // Jika data tidak ditemukan
        if (!$kategori) {
            return response()->json([
                "success" => false,
                "message" => 'Kategori tidak ditemukan'
            ], 404);
        }

        // Jika data ditemukan
        return new KategoriRes(true, 'Detail Data Kategori', $kategori);
    }

    // Untuk mengupdate data berdasarkan id
    public function update(Request $request, $id)
    {
        // Aturan validasi
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'keterangan' => 'required',
            'pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Gambar opsional
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        // Cari kategori berdasarkan id
        $kategori = Kategori::find($id);

        // Jika kategori tidak ditemukan
        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        // Cek jika ada file gambar baru
        if ($request->hasFile('pic')) {
            $pic = $request->file('pic');
            $picPath = $pic->storeAs('public/kategori', $pic->hashName());

            // Hapus gambar lama jika ada
            if ($kategori->pic) {
                Storage::delete('public/kategori/' . basename($kategori->pic));
            }

            // Update dengan gambar baru
            $kategori->update([
                'pic' => $pic->hashName(),
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
            ]);
        } else {
            // Update tanpa gambar baru
            $kategori->update([
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
            ]);
        }

        // Berikan respons berhasil update
        return new KategoriRes(true, 'Data berhasil di-update', $kategori);
    }
    public function destroy($id)
    {
        // Mencari kategori berdasarkan ID
        $kategori = Kategori::find($id);

        // Cek kalau kategori tidak ditemukan
        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        // Hapus gambar jika ada
        if ($kategori->pic) {
            Storage::delete('public/kategori/' . basename($kategori->pic));
        }

        // Hapus dari tabel kategori
        $kategori->delete();

        // Berikan respon untuk membantu frontend
        return new KategoriRes(true, 'Data berhasil dihapus', null);
    }
}
