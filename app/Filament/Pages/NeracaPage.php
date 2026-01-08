<?php

namespace App\Filament\Pages;

use App\Models\Assets;
use App\Models\Equities;
use App\Models\Liabilities;
use App\Models\Pemasukan;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class NeracaPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static string $view = 'filament.pages.neraca-page';
    protected static ?string $navigationLabel = 'Neraca';
    protected static ?string $navigationGroup = 'Laporan';

    public $asetlancar;
    public $tglasetlancar;
    public $datefirst;
    public $tglfirst;
    public $datelast;
    public $tgllast;
    public $tglasettetap;
    public $asettetap;
    public $equities;
    public $tglequities;
    public $liabilities;
    public $tglliabilities;
    public $bulan;
    public $tahun;

    public $totalAssets;
    public $totalLiabilities;
    public $totalEquities;
    public $labaDitahan;

    public function mount(): void
    {
        $this->bulan = date('m');
        $this->tahun = date('Y');

        $this->hitung();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('bulan')
                ->label('Bulan')
                ->options([
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ])
                ->reactive()
                ->afterStateUpdated(fn () => $this->hitung()),

            Forms\Components\Select::make('tahun')
                ->label('Tahun')
                ->options(
                    collect(range(date('Y') - 5, date('Y') + 1))
                        ->mapWithKeys(fn ($y) => [$y => $y])
                )
                ->reactive()
                ->afterStateUpdated(fn () => $this->hitung()),
        ];
    }

    public function hitung()
    {
        $dateColumn = 'tanggal';

        $this->totalAssets = Assets::whereMonth($dateColumn, $this->bulan)
            ->whereYear($dateColumn, $this->tahun)
            ->selectRaw('SUM(jumlah * jumlah) AS total')
            ->value('total');

        $this->totalLiabilities = Liabilities::whereMonth($dateColumn, $this->bulan)
            ->whereYear($dateColumn, $this->tahun)
            ->sum('jumlah');

        $modal = Equities::whereMonth($dateColumn, $this->bulan)
            ->whereYear($dateColumn, $this->tahun)
            ->sum('jumlah');

        $pemasukan = Pemasukan::whereMonth('tanggal_pemasukan', $this->bulan)
            ->whereYear('tanggal_pemasukan', $this->tahun)
            ->sum('jumlah_pemasukan' ?? 0);

        $pengeluaran = Liabilities::whereMonth($dateColumn, $this->bulan)
            ->whereYear($dateColumn, $this->tahun)
            ->sum('jumlah' ?? 0);

        $this->labaDitahan = $pemasukan - $pengeluaran;

        $this->totalEquities = $modal + $this->labaDitahan;

        $this->asetlancar = Assets::all()->where('sub_jenis', 'asset lancar');

        $this->tglasetlancar = Assets::all()->where('sub_jenis', 'asset lancar')->first()->tanggal;
        $datefirst = Assets::all()->where('sub_jenis', 'asset lancar')->first()->tanggal;
        $this->tglfirst = Carbon::parse($datefirst)->format('d M Y');

        $datelast = Assets::all()->where('sub_jenis', 'asset lancar')->last()->tanggal;
        $this->tgllast = Carbon::parse($datelast)->format('d M Y');

        $this->tglasettetap = Assets::all()->where('sub_jenis', 'asset tetap')->first()->tanggal;
        $this->asettetap = Assets::all()->where('sub_jenis', 'asset tetap');
        $this->equities = Equities::all();
        $this->tglequities = Equities::all()->first()->tanggal;

        $this->liabilities = Liabilities::all();
        $this->tglliabilities = Liabilities::all()->first()->tanggal;
    }
}
