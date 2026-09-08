<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        p { color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Performance</h1>
    <p>Periode: {{ $periode }}</p>

    <table>
        <thead>
            <tr>
                <th>Divisi</th>
                <th>Total PIC</th>
                <th>Target Bulanan</th>
                <th>Actual Bulanan</th>
                <th>Achievement</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row->divisi }}</td>
                    <td>{{ $row->total_pic }}</td>
                    <td>{{ $row->target ? number_format($row->target, 0, ',', '.') : '-' }}</td>
                    <td>{{ $row->actual ? number_format($row->actual, 0, ',', '.') : '-' }}</td>
                    <td>{{ $row->achievement !== null ? $row->achievement.'%' : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada data untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>