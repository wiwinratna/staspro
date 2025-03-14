<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    // Menampilkan semua transaksi
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            $transaksis = Transaksi::all(); // Ambil semua transaksi untuk admin
            return view('transaksi.pencatatan_transaksi', compact('transaksis'));
        } else {
            $transaksis = Transaksi::where('tim_peneliti', auth()->user()->name)->get(); // Ambil transaksi sesuai user
            return view('pencatatan_transaksi_user', compact('transaksis'));
        }
    }

    // Filter transaksi berdasarkan rentang tanggal
    public function filterTransaksi(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $transaksis = Transaksi::whereBetween('tanggal', [$request->start_date, $request->end_date])->get();
        $totalNominal = $transaksis->sum('jumlah_transaksi');

        return view('transaksi.pencatatan_transaksi', compact('transaksis', 'totalNominal'));
    }

    // Menampilkan form input transaksi
    public function create()
    {
        $tim_projects = Project::all(); // Mengambil semua tim penelitian dari model Project
        return view('transaksi.form_input_transaksi', compact('tim_projects'));
    }

    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|string|max:255',
            'deskripsi_transaksi' => 'required|string',
            'jumlah_transaksi' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:255',
            'kategori_transaksi' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'sub_sub_kategori' => 'nullable|string|max:255',
            'tim_penelitian' => 'nullable|exists:projects,id', // Validasi tim penelitian
            'bukti_transaksi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $jumlah = (float) str_replace(['Rp.', ',', ' '], '', $request->jumlah_transaksi);
        $buktiPath = null;

        if ($request->hasFile('bukti_transaksi')) {
            $file = $request->file('bukti_transaksi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $buktiPath = $file->storeAs('bukti_transaksi', $filename, 'public');
        }

        Transaksi::create([
            'tanggal' => $request->tanggal,
            'jenis_transaksi' => $request->jenis_transaksi,
            'deskripsi_transaksi' => $request->deskripsi_transaksi,
            'jumlah_transaksi' => $jumlah,
            'metode_pembayaran' => $request->metode_pembayaran,
            'kategori_transaksi' => $request->kategori_transaksi,
            'sub_kategori' => $request->sub_kategori,
            'sub_sub_kategori' => $request->sub_sub_kategori,
            'tim_penelitian' => $request->tim_penelitian,
            'bukti_transaksi' => $buktiPath,
        ]);

        return redirect()->route('pencatatan_transaksi')->with('success', 'Data transaksi berhasil disimpan!');
    }

    // Menampilkan form edit transaksi
    public function edit($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $tim_projects = Project::all();

        return view('transaksi.form_input_transaksi', compact('transaksi', 'tim_projects'));
    }

    // Mengupdate data transaksi
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|string|max:255',
            'deskripsi_transaksi' => 'required|string',
            'jumlah_transaksi' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:255',
            'kategori_transaksi' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string|max:255',
            'sub_sub_kategori' => 'nullable|string|max:255',
            'tim_penelitian' => 'nullable|exists:projects,id',
            'bukti_transaksi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $jumlah = (float) str_replace(['Rp.', ',', ' '], '', $request->jumlah_transaksi);
        $buktiPath = $transaksi->bukti_transaksi;

        if ($request->hasFile('bukti_transaksi')) {
            if ($transaksi->bukti_transaksi) {
                Storage::disk('public')->delete($transaksi->bukti_transaksi);
            }
            $file = $request->file('bukti_transaksi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $buktiPath = $file->storeAs('bukti_transaksi', $filename, 'public');
        }

        $transaksi->update([
            'tanggal' => $request->tanggal,
            'jenis_transaksi' => $request->jenis_transaksi,
            'deskripsi_transaksi' => $request->deskripsi_transaksi,
            'jumlah_transaksi' => $jumlah,
            'metode_pembayaran' => $request->metode_pembayaran,
            'kategori_transaksi' => $request->kategori_transaksi,
            'sub_kategori' => $request->sub_kategori,
            'sub_sub_kategori' => $request->sub_sub_kategori,
            'tim_penelitian' => $request->tim_penelitian,
            'bukti_transaksi' => $buktiPath,
        ]);

        return redirect()->route('pencatatan_transaksi')->with('success', 'Data transaksi berhasil diperbarui!');
    }

    // Menghapus transaksi
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->bukti_transaksi) {
            Storage::disk('public')->delete($transaksi->bukti_transaksi);
        }

        $transaksi->delete();

        return redirect()->route('pencatatan_transaksi')->with('success', 'Transaksi berhasil dihapus!');
    }

    // Laporan Keuangan dengan filter berdasarkan Tim Penelitian dan Kategori Pendanaan
    public function laporanKeuangan(Request $request)
    {
        $kategori = $request->input('kategoriPendanaan');
        $tim = $request->input('timPenelitian');

        $query = Transaksi::query();

        if ($kategori) {
            $query->where('kategori_transaksi', $kategori);
        }

        if ($tim) {
            $query->where('tim_penelitian', $tim);
        }

        $transaksis = $query->get();
        $totalNominal = $transaksis->sum('jumlah_transaksi');

        return view('laporan_keuangan', compact('transaksis', 'totalNominal'));
    }

    // Export laporan keuangan ke Excel
    public function exportExcel(Request $request)
    {
        $kategori = $request->input('kategoriPendanaan');
        $tim = $request->input('timPenelitian');

        $query = Transaksi::query();

        if ($kategori) {
            $query->where('kategori_transaksi', $kategori);
        }
        if ($tim) {
            $query->where('tim_penelitian', $tim);
        }

        $transaksis = $query->get();

        return Excel::download(new LaporanExport($transaksis), 'Laporan_Keuangan.xlsx');
    }
}
