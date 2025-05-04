<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostRes;
use Illuminate\Http\Request;
use App\Models\Post; // Pastikan model diimpor
use Illuminate\Support\Facades\Validator; // Untuk validasi
use Illuminate\Support\Facades\Storage; // Untuk akses storage

class PostController extends Controller
{
    // Untuk menampilkan daftar post
    public function index() 
    {
        // Ambil 5 data terbaru
        $posts = Post::latest()->paginate(5);
        return new PostRes(true, 'Daftar Data Post', $posts);
    }

    // Untuk menambahkan post baru
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
        $pic->storeAs('public/posts', $pic->hashName());

        // Buat post baru
        $post = Post::create([
            'pic' => $pic->hashName(),
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
        ]);

        // Berikan respon
        return new PostRes(true, 'Berhasil menambahkan post', $post);
    }

    // Menampilkan data berdasarkan id
    public function show($id)
    {
        // Cari data berdasarkan id
        $post = Post::find($id);

        // Jika data tidak ditemukan
        if (!$post) {
            return response()->json([
                "success" => false,
                "message" => 'Post tidak ditemukan'
            ], 404);
        }

        // Jika data ditemukan
        return new PostRes(true, 'Detail Data Post', $post);
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

        // Cari post berdasarkan id
        $post = Post::find($id);

        // Jika post tidak ditemukan
        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        // Cek jika ada file gambar baru
        if ($request->hasFile('pic')) {
            $pic = $request->file('pic');
            $picPath = $pic->storeAs('public/posts', $pic->hashName());

            // Hapus gambar lama jika ada
            if ($post->pic) {
                Storage::delete('public/posts/' . basename($post->pic));
            }

            // Update dengan gambar baru
            $post->update([
                'pic' => $pic->hashName(),
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
            ]);
        } else {
            // Update tanpa gambar baru
            $post->update([
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
            ]);
        }

        // Berikan respons berhasil update
        return new PostRes(true, 'Data berhasil di-update', $post);
    }
    public function destroy($id)
    {
        // Mencari post berdasarkan ID
        $post = Post::find($id);

        // Cek kalau post tidak ditemukan
        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        // Hapus gambar jika ada
        if ($post->foto) {
            Storage::delete('public/posts/' . basename($post->foto));
        }

        // Hapus dari tabel post
        $post->delete();

        // Berikan respon untuk membantu frontend
        return new PostRes(true, 'Data berhasil dihapus', null);
    }

}
