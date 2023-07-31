<?php
// app/Http/Controllers/VilaController.php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Vila;

class VilaController extends Controller
{
    // Menampilkan daftar vila
    public function index()
    {
        $vilas = vila::all();
        return view('vilas.index', compact('vilas'));
        
    }

    // Menampilkan formulir create vila
    public function create()
    {
        return view('vilas.create');
    }

    // Menyimpan data vila ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_vila' => 'required',
            'alamat_lengkap' => 'required',
            'lokasi' => 'required',
            'deskripsi' => 'required',
            'jumlah_kasur' => 'required',
            'kapasitas' => 'required',
            'fasilitas' => 'required',
            'harga' => 'required',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'jumlah_kamar_mandi' => 'required', // Validasi jumlah kamar mandi
        ]);

        $vilaData = $request->except('foto');

        // Upload foto ke direktori storage dan ambil pathnya
        $vila = Vila::create($vilaData);

        // Proses foto-foto yang diunggah dan simpan ke dalam kolom "foto"
        if ($request->hasFile('foto')) {
            $photoPaths = [];
            foreach ($request->file('foto') as $photo) {
                $photoPath = $photo->storePublicly('vila_photos', 'public'); // Ganti menjadi storePublicly
                $photoPaths[] = $photoPath;
            }
            $vila->foto = $photoPaths;
            $vila->save();
        }

        return redirect()->route('vila.index')->with('success', 'Data vila telah ditambahkan.');
    }

    // Menampilkan detail vila
    public function show($id)
    {
        $vila = Vila::findOrFail($id);
        return view('vilas.show', compact('vila'));
        return view('customers.show', compact('vila'));

    }

    // Menampilkan formulir edit vila
    public function edit($id)
    {
        $vila = Vila::findOrFail($id);

        return view('vilas.edit', compact('vila'));
    }

    // Fungsi untuk mengupdate data vila
    public function update(Request $request, $id)
    {
        $vila = Vila::findOrFail($id);

        $request->validate([
            'nama_vila' => 'required',
            'alamat_lengkap' => 'required',
            'lokasi' => 'required',
            'deskripsi' => 'required',
            'jumlah_kasur' => 'required',
            'kapasitas' => 'required',
            'fasilitas' => 'required',
            'harga' => 'required',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'jumlah_kamar_mandi' => 'required',
        ]);

        $vilaData = $request->except('foto');

        // Upload foto ke direktori storage dan ambil pathnya
        if ($request->hasFile('foto')) {
            $photoPaths = [];
            foreach ($request->file('foto') as $photo) {
                $photoPath = $photo->storePublicly('vila_photos', 'public'); // Ganti menjadi storePublicly
                $photoPaths[] = str_replace('public/', '', $photoPath);
            }
            $vilaData['foto'] = $photoPaths;
        }

        // Update data vila
        $vila->update($vilaData);

        return redirect()->route('vila.index')->with('success', 'Data vila telah diperbarui.');
    }

    public function destroy($id)
    {
        Vila::destroy($id);

        return redirect()->route('vila.index')->with('success', 'Data vila telah dihapus.');
    }
    public function booking($id)
    {
        // Redirect pengguna ke halaman form pemesanan dengan data id villa
        return redirect()->route('bookingForm', ['id' => $id]);
        }
}