<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    // Menampilkan semua transaksi
    public function index()
    {
        $transaksis = Transaksi::all();
        $totalNominal = $transaksis->sum('jumlah_transaksi');
        return view('transaksi.pencatatan_transaksi', compact('transaksis', 'totalNominal'));
    }

    public function filterTransaksi(Request $request)
    {
        // Validasi input tanggal
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        // Ambil transaksi berdasarkan rentang tanggal
        $transaksis = Transaksi::whereBetween('tanggal', [$request->start_date, $request->end_date])->get();

        // Hitung total nominal dari transaksi yang terfilter
        $totalNominal = $transaksis->sum('jumlah_transaksi');

        return view('transaksi.pencatatan_transaksi', compact('transaksis', 'totalNominal'));
    }

    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|string',
            'deskripsi_transaksi' => 'required|string',
            'jumlah_transaksi' => 'required|numeric',
            'metode_pembayaran' => 'required|string',
            'kategori_transaksi' => 'required|string',
            'sub_kategori' => 'nullable|string',
            'sub_sub_kategori' => 'nullable|string',
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
            'bukti_transaksi' => $buktiPath,
        ]);

        return redirect()->route('pencatatan_transaksi')->with('success', 'Data transaksi berhasil disimpan!');
    }

    // Menampilkan form input transaksi
    public function create()
    {
        return view('transaksi.form_input_transaksi');
    }

    // Menampilkan form edit transaksi
    public function edit($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return view('transaksi.form_input_transaksi', compact('transaksi'));
    }

    // Mengupdate data transaksi
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|string|max:255',
            'deskripsi_transaksi' => 'required|string',
            'jumlah_transaksi' => 'required|numeric',
            'metode_pembayaran' => 'required|string|max:255',
            'kategori_transaksi' => 'required|string|max:255',
            'sub_kategori' => 'nullable|string',
            'sub_sub_kategori' => 'nullable|string',
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

        $dataUpdate = [
            'tanggal' => $request->tanggal,
            'jenis_transaksi' => $request->jenis_transaksi,
            'deskripsi_transaksi' => $request->deskripsi_transaksi,
            'jumlah_transaksi' => $jumlah,
            'metode_pembayaran' => $request->metode_pembayaran,
            'kategori_transaksi' => $request->kategori_transaksi,
            'sub_kategori' => $request->sub_kategori,
            'sub_sub_kategori' => $request->sub_sub_kategori,
        ];
        if ($buktiPath) {
            $dataUpdate['bukti_transaksi'] = $buktiPath;
        }

        $transaksi->update($dataUpdate);

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

    // Method untuk laporan keuangan dengan filter berdasarkan Tim Penelitian dan Kategori Pendanaan
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

    // Method untuk export laporan keuangan ke Excel
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

        return Excel::download(new LaporanExport($transaksis), 'laporan_keuangan.xlsx');
    }
}
