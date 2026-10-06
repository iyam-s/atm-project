<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
   public function saldo(Request $request, $nomor_rekening)
{
    $rekening = Rekening::where('nomor_rekening', $nomor_rekening)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$rekening) {
        return response()->json([
            'message' => 'Rekening tidak ditemukan atau bukan milik Anda'
        ], 404);
    }

    return response()->json([
        'nomor_rekening' => $rekening->nomor_rekening,
        'saldo' => $rekening->saldo
    ]);
}

    public function setor(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nomor_rekening' => 'required|string',
        'nominal' => 'required|numeric|min:10000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Data tidak valid',
            'errors' => $validator->errors()
        ], 422);
    }

    $rekening = Rekening::where('nomor_rekening', $request->nomor_rekening)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$rekening) {
        return response()->json([
            'message' => 'Rekening tidak ditemukan atau bukan milik Anda'
        ], 404);
    }

    DB::beginTransaction();

    try {
        $rekening = Rekening::where('id', $rekening->id)
            ->lockForUpdate()
            ->first();

        $saldoSebelum = $rekening->saldo;
        $rekening->saldo += $request->nominal;
        $rekening->save();

        Transaksi::create([
            'rekening_id' => $rekening->id,
            'jenis' => 'setor',
            'nominal' => $request->nominal,
            'keterangan' => 'Setor tunai',
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $rekening->saldo,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Setor tunai berhasil',
            'nominal' => $request->nominal,
            'saldo_sekarang' => $rekening->saldo
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Setor tunai gagal',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function tarik(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nomor_rekening' => 'required|string',
        'nominal' => 'required|numeric|min:10000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Data tidak valid',
            'errors' => $validator->errors()
        ], 422);
    }

    $rekening = Rekening::where('nomor_rekening', $request->nomor_rekening)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$rekening) {
        return response()->json([
            'message' => 'Rekening tidak ditemukan atau bukan milik Anda'
        ], 404);
    }

    DB::beginTransaction();

    try {
        $rekening = Rekening::where('id', $rekening->id)
            ->lockForUpdate()
            ->first();

        if ($rekening->saldo < $request->nominal) {
            DB::rollBack();

            return response()->json([
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        $saldoSebelum = $rekening->saldo;

        $rekening->saldo -= $request->nominal;
        $rekening->save();

        Transaksi::create([
            'rekening_id' => $rekening->id,
            'jenis' => 'tarik',
            'nominal' => $request->nominal,
            'keterangan' => 'Tarik tunai',
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $rekening->saldo,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Tarik tunai berhasil',
            'nominal' => $request->nominal,
            'saldo_sekarang' => $rekening->saldo
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Tarik tunai gagal',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function transfer(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nomor_rekening_pengirim' => 'required|string',
        'nomor_rekening_tujuan' => 'required|string',
        'nominal' => 'required|numeric|min:10000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Data tidak valid',
            'errors' => $validator->errors()
        ], 422);
    }

    // Rekening pengirim harus milik user yang sedang login
    $pengirim = Rekening::where(
        'nomor_rekening',
        $request->nomor_rekening_pengirim
    )
    ->where('user_id', $request->user()->id)
    ->first();

    if (!$pengirim) {
        return response()->json([
            'message' => 'Rekening pengirim tidak ditemukan atau bukan milik Anda'
        ], 404);
    }

    // Rekening tujuan harus ada
    $tujuan = Rekening::where(
        'nomor_rekening',
        $request->nomor_rekening_tujuan
    )->first();

    if (!$tujuan) {
        return response()->json([
            'message' => 'Rekening tujuan tidak ditemukan'
        ], 404);
    }

    // Tidak boleh transfer ke rekening sendiri
    if ($pengirim->id === $tujuan->id) {
        return response()->json([
            'message' => 'Tidak dapat transfer ke rekening sendiri'
        ], 400);
    }

    DB::beginTransaction();

    try {
        // Lock kedua rekening agar transaksi aman
        $rekeningIds = [$pengirim->id, $tujuan->id];
        sort($rekeningIds);

        $rekeningTerkunci = Rekening::whereIn('id', $rekeningIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $pengirim = $rekeningTerkunci[$pengirim->id];
        $tujuan = $rekeningTerkunci[$tujuan->id];

        // Cek saldo
        if ($pengirim->saldo < $request->nominal) {
            DB::rollBack();

            return response()->json([
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        $saldoPengirimSebelum = $pengirim->saldo;
        $saldoTujuanSebelum = $tujuan->saldo;

        // Kurangi saldo pengirim
        $pengirim->saldo -= $request->nominal;
        $pengirim->save();

        // Tambah saldo tujuan
        $tujuan->saldo += $request->nominal;
        $tujuan->save();

        // Catat transaksi pengirim
        Transaksi::create([
            'rekening_id' => $pengirim->id,
            'jenis' => 'transfer',
            'nominal' => $request->nominal,
            'keterangan' => 'Transfer ke ' . $tujuan->nomor_rekening,
            'saldo_sebelum' => $saldoPengirimSebelum,
            'saldo_sesudah' => $pengirim->saldo,
        ]);

        // Catat transaksi penerima
        Transaksi::create([
            'rekening_id' => $tujuan->id,
            'jenis' => 'transfer',
            'nominal' => $request->nominal,
            'keterangan' => 'Transfer dari ' . $pengirim->nomor_rekening,
            'saldo_sebelum' => $saldoTujuanSebelum,
            'saldo_sesudah' => $tujuan->saldo,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Transfer berhasil',
            'dari' => $pengirim->nomor_rekening,
            'ke' => $tujuan->nomor_rekening,
            'nominal' => $request->nominal,
            'saldo_sekarang' => $pengirim->saldo
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Transfer gagal',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function riwayat(Request $request, $nomor_rekening)
{
    $rekening = Rekening::where('nomor_rekening', $nomor_rekening)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$rekening) {
        return response()->json([
            'message' => 'Rekening tidak ditemukan atau bukan milik Anda'
        ], 404);
    }

    $transaksi = Transaksi::where('rekening_id', $rekening->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'nomor_rekening' => $rekening->nomor_rekening,
        'saldo_sekarang' => $rekening->saldo,
        'jumlah_transaksi' => $transaksi->count(),
        'transaksi' => $transaksi
    ]);
}

}