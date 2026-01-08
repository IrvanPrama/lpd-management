<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profit Loss PDF</title>

<style>
    body {
        font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
        color: #333;
        margin: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 25px;
    }

    .header img{
        width: 100%;
    }

    h1 {
        margin-top: 10px;
        font-size: 22px;
        letter-spacing: .5px;
    }

    /* table  */
    .table-wrapper {
        margin-top: 15px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #ddd;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    thead {
        background: #1F2937;
        color: white;
    }

    th, td {
        padding: 10px;
    }

    tr:nth-child(even) {
        background: #f4f6f9;
    }

    tr:nth-child(odd) {
        background: #ffffff;
    }

    th {
        color: black;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .3px;
    }

    /* status */
    .badge {
        padding: 4px 8px;
        border-radius: 6px;
        color: white;
        font-size: 10px;
    }

    .pending { background: #eab308; }
    .approved { background: #16a34a; }
    .cancelled { background: #dc2626; }

    /* CARD SUMMARY */
    .cards {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .card {
        margin: 10px 0;
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        color: white;
    }

    .card h3 {
        margin: 0;
        font-size: 13px;
        letter-spacing: .3px;
    }

    .card p {
        font-size: 16px;
        margin-top: 3px;
        font-weight: bold;
    }

    .blue { background: #0090FF; }
    .green { background: #98FB98 ; }
    .red { background: #FF6464 ; }

    /* FOOTER TANDA TANGAN */
    .ttd {
        text-align: right;
        margin-top: 40px;
        margin-right: 40px;
    }
</style>
</head>

<body>

<div class="header">
    <img src="./storage/assets-img/kop-surat.jpeg">
    <h1>LAPORAN RESERVASI</h1>
</div>

<div class="table-wrapper">
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Penyewa</th>
            <th>Tanggal</th>
            <th>Durasi</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach($filtered as $l)
        <tr>
            <td>{{ $l->lapangan_id }}</td>
            <td>{{ $l->nama_penyewa }}</td>
            <td>{{ $l->tanggal_reservasi }}</td>
            <td>{{ $l->durasi_jam }} Jam</td>
            <td>Rp {{ number_format($l->total_harga) }}</td>
            <td>
                <span class="badge 
                    {{ $l->status == 'pending' ? 'pending' : '' }}
                    {{ $l->status == 'approved' ? 'approved' : '' }}
                    {{ $l->status == 'cancelled' ? 'cancelled' : '' }}">
                    {{ ucfirst($l->status) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

<!-- CARD SUMMARY -->
<div class="cards">
    <div class="card blue">
        <h3>Total Pending</h3>
        <p>Rp {{ number_format($pending->sum('total_harga')) }}</p>
    </div>

    <div class="card green">
        <h3>Total Booked</h3>
        <p>Rp {{ number_format($approved->sum('total_harga')) }}</p>
    </div>

    <div class="card red">
        <h3>Total Cancelled</h3>
        <p>Rp {{ number_format($cancelled->sum('total_harga')) }}</p>
    </div>
</div>

<div class="ttd">
    Tegallalang, {{ now()->format('d F Y') }} <br><br><br>
    <b>Manager GS Futsal</b>
</div>

</body>
</html>