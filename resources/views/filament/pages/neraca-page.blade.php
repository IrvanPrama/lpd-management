<x-filament::page>
<style>
    .btn{
        width: 200px;
        background-color: blue;
    }
    table{
        margin: 0 auto;

        width: 80%;
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<button class="btn rounded-lg px-2 py-2 text-white">Cetak PDF</button>
<table>
        <tr>
        <th colspan="4"> KOPERASI SIMPAN PINJAM MANDIRI BERKAH</th> <br>
</tr>

        <tr><th colspan="4"> NERACA</th><br></tr>
    <tr>    <th colspan="4"> PERIODE {{ $tglfirst }} - {{ $tgllast }}</th></tr>
        <tr>
            <th>Nama Aku</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th></th>
        </tr>

        <tr>
            <td style="font-weight:bold">Asset </td> 
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <!-- Asset Lancar -->
        <tr>
            <td style="font-weight:bold">Asset Lancar</td> 
            <td></td>
            <td></td>
            <td>{{ $tglasetlancar }}</td>
        </tr>
        @foreach ($asetlancar as $n) <!-- Untuk pengulangan data -->
            <tr>
                <td>{{ $n->nama }}</td>
                <td>{{ $n->debit }}</td>
                <td>{{ $n->kredit }}</td>
                <td>Rp {{ number_format(($n->jumlah)) }}</td>
            </tr>
        @endforeach

<!-- ASSET TETAP -->
         <tr>
            <td style="font-weight:bold">Asset Tetap</td>
                       <td></td>

                        <td></td>

            <td>{{ $tglasettetap }}</td>
        </tr>
    
        @foreach ($asettetap as $n)
            <tr>
                <td>{{ $n->nama }}</td>
                <td>{{ $n->debit }}</td>
                <td>{{ $n->kredit }}</td>
                <td>Rp {{ number_format(($n->jumlah)) }}</td>
            </tr>
        @endforeach
        <tr>
            <td>Total Asset</td>
            <td></td>
            <td></td>
            <td>
                Rp {{ number_format(($asettetap->sum('jumlah')+($asetlancar->sum('jumlah')))) }}
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold">Kewajiban dan Ekuitas</td>
                                   <td></td>

                        <td></td>

            <td>{{ $tglequities }}</td>
        </tr>
        @foreach ($liabilities as $n)
            <tr>
                <td>{{ $n->name }}</td>
                <td>{{ $n->debit }}</td>
                <td>{{ $n->kredit }}</td>
                <td>Rp {{ number_format(($n->jumlah)) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="font-weight:bold">Total kewajiban</td>
            <td></td>
            <td></td>
            <td>
                Rp {{ number_format($liabilities->sum('jumlah')) }}
            </td>
        </tr>
         @foreach ($equities as $n)
            <tr>
                <td>{{ $n->nama }}</td>
                <td>{{ $n->debit }}</td>
                <td>{{ $n->kredit }}</td>
                <td>Rp {{ number_format(($n->jumlah)) }}</td>
            </tr>
        @endforeach
        <tr><td style="font-weight:bold">Total Ekuitas</td>
    <td></td>
<td></td>
<td>
    Rp {{ number_format(($equities->sum('jumlah'))) }}
</td>
</tr>
</table>

</x-filament::page>
