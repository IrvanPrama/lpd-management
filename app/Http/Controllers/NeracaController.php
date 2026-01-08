<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Models\Equities;
use App\Models\Liabilities;
use Illuminate\Support\Carbon;

class NeracaController extends Controller
{
    public function index()
    {
        $asetlancar = Assets::all()->where('sub_jenis', 'asset lancar');

        $tglasetlancar = Assets::all()->where('sub_jenis', 'asset lancar')->first()->tanggal;
        $datefirst = Assets::all()->where('sub_jenis', 'asset lancar')->first()->tanggal;
        $tglfirst = Carbon::parse($datefirst)->format('d M Y');

        $datelast = Assets::all()->where('sub_jenis', 'asset lancar')->last()->tanggal;
        $tgllast = Carbon::parse($datelast)->format('d M Y');

        $tglasettetap = Assets::all()->where('sub_jenis', 'asset tetap')->first()->tanggal;
        $asettetap = Assets::all()->where('sub_jenis', 'asset tetap');
        $equities = Equities::all();
        $tglequities = Equities::all()->first()->tanggal;

        $liabilities = Liabilities::all();
        $tglliabilities = Liabilities::all()->first()->tanggal;

        return view('neraca.index', compact('asetlancar', 'asettetap', 'equities', 'liabilities', 'tglasetlancar', 'tglasettetap', 'tglasetlancar', 'tglequities', 'tglliabilities', 'tglfirst', 'tgllast'));
    }

    public function print()
    {
        $asetlancar = Assets::all()->where('sub_jenis', 'asset lancar');

        $tglasetlancar = Assets::all()->where('sub_jenis', 'asset lancar')->first()->tanggal;
        $datefirst = Assets::all()->where('sub_jenis', 'asset lancar')->first()->tanggal;
        $tglfirst = Carbon::parse($datefirst)->format('d M Y');

        $datelast = Assets::all()->where('sub_jenis', 'asset lancar')->last()->tanggal;
        $tgllast = Carbon::parse($datelast)->format('d M Y');

        $tglasettetap = Assets::all()->where('sub_jenis', 'asset tetap')->first()->tanggal;
        $asettetap = Assets::all()->where('sub_jenis', 'asset tetap');
        $equities = Equities::all();
        $tglequities = Equities::all()->first()->tanggal;

        $liabilities = Liabilities::all();
        $tglliabilities = Liabilities::all()->first()->tanggal;

        $pdf = \PDF::loadView('pengajuansurat.surat-pdf', compact('asetlancar', 'asettetap', 'equities', 'liabilities', 'tglasetlancar', 'tglasettetap', 'tglasetlancar', 'tglequities', 'tglliabilities', 'tglfirst', 'tgllast'))
                    ->setPaper('A4', 'portrait');

        return $pdf->stream('surat-'.$data->name.'.pdf');
    }
}
