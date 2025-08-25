<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi PKL</title>
    <style>
        @page {
            size: 215.9mm 330mm; /* Ukuran kertas Folio */
            margin: 15mm;
            /* Hilangkan header dan footer browser */
            @top-center { content: ""; }
            @top-left { content: ""; }
            @top-right { content: ""; }
            @bottom-center { content: ""; }
            @bottom-left { content: ""; }
            @bottom-right { content: ""; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
            /* Hilangkan semua margin dan padding default */
            -webkit-margin-before: 0;
            -webkit-margin-after: 0;
            -webkit-margin-start: 0;
            -webkit-margin-end: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 11pt;
        }
        
        .filter-info {
            margin-bottom: 15px;
            font-size: 10pt;
        }
        
        .filter-info strong {
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .no {
            text-align: center;
            width: 30px;
        }
        
        .nama {
            width: 120px;
        }
        
        .nis {
            width: 80px;
            text-align: center;
        }
        
        .kelas {
            width: 60px;
            text-align: center;
        }
        
        .tempat {
            width: 100px;
        }
        
        .tanggal {
            width: 60px;
            text-align: center;
        }
        
        .waktu {
            width: 50px;
            text-align: center;
        }
        
        .jenis {
            width: 60px;
            text-align: center;
        }
        
        .toolbar {
            margin-bottom: 15px;
            text-align: center;
        }
        
        .btn-print {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11pt;
        }
        
        .btn-print:hover {
            background: #0056b3;
        }
        
        @media print {
            .toolbar {
                display: none;
            }
            
            body {
                font-size: 9pt;
                /* Hilangkan margin dan padding default browser */
                margin: 0 !important;
                padding: 0 !important;
            }
            
            table {
                font-size: 8pt;
            }
            
            /* Hilangkan header dan footer browser */
            @page {
                margin: 15mm;
                /* Pastikan tidak ada header/footer */
                @top-center { content: ""; }
                @top-left { content: ""; }
                @top-right { content: ""; }
                @bottom-center { content: ""; }
                @bottom-left { content: ""; }
                @bottom-right { content: ""; }
            }
        }
        
        .summary {
            margin-top: 15px;
            font-size: 10pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    </div>
    
    <div class="header">
        <h1>LAPORAN ABSENSI PRAKTIK KERJA LAPANGAN</h1>
        <p>Sistem Absensi PKL</p>
        <p>Periode: <?= date('d/m/Y', strtotime($filters['start'])) ?> - <?= date('d/m/Y', strtotime($filters['end'])) ?></p>
    </div>
    
    <div class="filter-info">
        <strong>Filter:</strong> 
        Tanggal: <?= date('d/m/Y', strtotime($filters['start'])) ?> s/d <?= date('d/m/Y', strtotime($filters['end'])) ?>
        <?php if (!empty($filters['kelas'])): ?>
            | Kelas: <?= htmlspecialchars($filters['kelas']) ?>
        <?php endif; ?>
        <?php if ($filters['tempat'] > 0): ?>
            | Tempat PKL ID: <?= $filters['tempat'] ?>
        <?php endif; ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="no">No</th>
                <th class="nama">Nama Siswa</th>
                <th class="nis">NIS</th>
                <th class="kelas">Kelas</th>
                <th class="tempat">Tempat PKL</th>
                <th class="tanggal">Tanggal</th>
                <th class="waktu">Waktu</th>
                <th class="jenis">Jenis Absen</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($rows as $r): 
            ?>
                <tr>
                    <td class="no"><?= $no++ ?></td>
                    <td class="nama"><?= htmlspecialchars($r['siswa']) ?></td>
                    <td class="nis"><?= htmlspecialchars($r['nis'] ?? 'N/A') ?></td>
                    <td class="kelas"><?= htmlspecialchars($r['kelas']) ?></td>
                    <td class="tempat"><?= htmlspecialchars($r['tempat']) ?></td>
                    <td class="tanggal"><?= date('d/m/Y', strtotime($r['waktu'])) ?></td>
                    <td class="waktu"><?= date('H:i', strtotime($r['waktu'])) ?></td>
                    <td class="jenis">
                        <?php if($r['jenis_absen'] === 'datang'): ?>
                            🚀 Datang
                        <?php elseif($r['jenis_absen'] === 'pulang'): ?>
                            🏠 Pulang
                        <?php else: ?>
                            ❓ <?= htmlspecialchars($r['jenis_absen']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="summary">
        <p>Total Data: <?= count($rows) ?> absensi</p>
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB</p>
    </div>
</body>
</html>