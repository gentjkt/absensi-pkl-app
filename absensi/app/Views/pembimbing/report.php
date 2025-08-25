<h2>📊 Laporan Absensi Bulanan</h2>

<div class="action-buttons" style="margin-bottom: 20px;">
    <a href="?r=pembimbing/dashboard" class="btn btn-info">
        🏠 Dashboard
    </a>
    <a href="?r=pembimbing/bimbingan" class="btn btn-primary">
        👥 Bimbingan
    </a>
    <button type="button" class="btn btn-success" onclick="cetakLaporan()">
        🖨️ Cetak Laporan
    </button>
</div>

<div class="card">
  <h3>📈 Statistik Absensi Siswa</h3>
  
  <!-- Filter Tanggal -->
  <div class="filter-section" style="margin-bottom: 20px; padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
    <h4>🔍 Filter Periode Laporan</h4>
    
    <form method="GET" action="?r=pembimbing/report" class="filter-form">
      <div class="filter-row" style="display: flex; gap: 20px; flex-wrap: wrap; align-items: end;">
        
        <!-- Filter Bulan dan Tahun (Default) -->
        <div class="form-group">
          <label for="month">Bulan:</label>
          <select name="month" id="month" class="form-control" onchange="this.form.submit()">
            <?php foreach($bulanList as $key => $namaBulan): ?>
              <option value="<?= $key ?>" <?= $key == $bulan ? 'selected' : '' ?>>
                <?= $namaBulan ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="year">Tahun:</label>
          <select name="year" id="year" class="form-control" onchange="this.form.submit()">
            <?php foreach($tahunList as $tahunOption): ?>
              <option value="<?= $tahunOption ?>" <?= $tahunOption == $year ? 'selected' : '' ?>>
                <?= $tahunOption ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <!-- Filter Range Tanggal -->
        <div class="form-group">
          <label for="start_date">Tanggal Mulai:</label>
          <input type="date" name="start_date" id="start_date" class="form-control" 
                 value="<?= htmlspecialchars($startDate ?? '') ?>" 
                 onchange="updateEndDateMin()">
        </div>
        
        <div class="form-group">
          <label for="end_date">Tanggal Akhir:</label>
          <input type="date" name="end_date" id="end_date" class="form-control" 
                 value="<?= htmlspecialchars($endDate ?? '') ?>"
                 min="<?= htmlspecialchars($startDate ?? '') ?>">
        </div>
        
        <!-- Tombol Filter -->
        <div class="form-group">
          <button type="submit" class="btn btn-primary">
            🔍 Terapkan Filter
          </button>
          <a href="?r=pembimbing/report" class="btn btn-secondary">
            🔄 Reset
          </a>
        </div>
        
      </div>
      
      <!-- Info Filter Aktif -->
      <div class="filter-info" style="margin-top: 15px; padding: 10px; background-color: #e9ecef; border-radius: 5px;">
        <small>
          <strong>Filter Aktif:</strong> 
          <?php if (!empty($startDate) && !empty($endDate)): ?>
            Range Tanggal: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>
            (<?= $totalHariFilter ?> hari kerja)
          <?php else: ?>
            Bulan: <?= $bulanList[$bulan] ?> <?= $year ?> (<?= $totalHariKerja ?> hari kerja)
          <?php endif; ?>
        </small>
      </div>
    </form>
  </div>
  
  <?php if(empty($statistikSiswa)): ?>
    <div class="alert alert-info">
      <strong>ℹ️ Info:</strong> Tidak ada siswa yang dibimbing untuk periode ini.
    </div>
  <?php else: ?>
    <div class="info" style="margin-bottom: 20px;">
      <p><strong>Periode:</strong> 
        <?php if (!empty($startDate) && !empty($endDate)): ?>
          <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>
        <?php else: ?>
          <?= $bulanList[$bulan] ?> <?= $year ?>
        <?php endif; ?>
      </p>
      <p><strong>Total Hari Kerja:</strong> 
        <?php if (!empty($startDate) && !empty($endDate)): ?>
          <?= $totalHariFilter ?> hari
        <?php else: ?>
          <?= $totalHariKerja ?> hari (Senin-Jumat)
        <?php endif; ?>
      </p>
      <p><strong>Keterangan:</strong> Hadir = Absen dengan GPS, Ijin/Sakit = Input manual, Alpa = Tidak hadir</p>
    </div>
    
    <div class="table-responsive">
      <table class="table" id="tabelLaporan">
        <thead>
          <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Tempat PKL</th>
            <th>Hadir</th>
            <th>Ijin</th>
            <th>Sakit</th>
            <th>Alpa</th>
            <th>Total Hari</th>
            <th>Persentase</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        $totalHadir = 0;
        $totalIjin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        
        foreach($statistikSiswa as $stat): 
          $hadir = $stat['hadir'];
          $ijin = $stat['ijin'];
          $sakit = $stat['sakit'];
          $alpa = $stat['alpa'];
          $totalHari = $stat['total_hari'];
          
          $totalHadir += $hadir;
          $totalIjin += $ijin;
          $totalSakit += $sakit;
          $totalAlpa += $alpa;
          
          $persentase = $totalHari > 0 ? round((($hadir + $ijin + $sakit) / $totalHari) * 100, 1) : 0;
          $statusClass = $persentase >= 80 ? 'baik' : ($persentase >= 60 ? 'cukup' : 'kurang');
        ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= htmlspecialchars($stat['siswa']['nis']) ?></strong></td>
            <td><strong><?= htmlspecialchars($stat['siswa']['nama']) ?></strong></td>
            <td><?= htmlspecialchars($stat['siswa']['kelas']) ?></td>
            <td><?= htmlspecialchars($stat['siswa']['tempat_pkl'] ?? '-') ?></td>
            <td>
              <span class="badge badge-success"><?= $hadir ?></span>
            </td>
            <td>
              <span class="badge badge-info"><?= $ijin ?></span>
            </td>
            <td>
              <span class="badge badge-warning"><?= $sakit ?></span>
            </td>
            <td>
              <span class="badge badge-danger"><?= $alpa ?></span>
            </td>
            <td><strong><?= $totalHari ?></strong></td>
            <td>
              <span class="persentase <?= $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger') ?>">
                <?= $persentase ?>%
              </span>
            </td>
            <td>
              <?php if($persentase >= 80): ?>
                <span class="badge badge-success">✅ Baik</span>
              <?php elseif($persentase >= 60): ?>
                <span class="badge badge-warning">⚠️ Cukup</span>
              <?php else: ?>
                <span class="badge badge-danger">❌ Kurang</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="table-footer">
            <td colspan="5"><strong>TOTAL</strong></td>
            <td><strong><?= $totalHadir ?></strong></td>
            <td><strong><?= $totalIjin ?></strong></td>
            <td><strong><?= $totalSakit ?></strong></td>
            <td><strong><?= $totalAlpa ?></strong></td>
            <td><strong><?= $totalHariKerja * count($statistikSiswa) ?></strong></td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- Summary Cards -->
    <div class="stats-grid" style="margin-top: 20px;">
      <div class="stat-card success">
        <div class="stat-number"><?= $totalHadir ?></div>
        <div class="stat-label">Total Hadir</div>
      </div>
      
      <div class="stat-card info">
        <div class="stat-number"><?= $totalIjin ?></div>
        <div class="stat-label">Total Ijin</div>
      </div>
      
      <div class="stat-card warning">
        <div class="stat-number"><?= $totalSakit ?></div>
        <div class="stat-label">Total Sakit</div>
      </div>
      
      <div class="stat-card danger">
        <div class="stat-number"><?= $totalAlpa ?></div>
        <div class="stat-label">Total Alpa</div>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
// Fungsi untuk mengatur tanggal minimum pada end_date
function updateEndDateMin() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date');
    
    if (startDate) {
        endDate.min = startDate;
        
        // Jika end_date lebih kecil dari start_date, reset end_date
        if (endDate.value && endDate.value < startDate) {
            endDate.value = startDate;
        }
    }
}

// Fungsi untuk validasi filter tanggal
function validateDateFilter() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate && startDate > endDate) {
        alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
        return false;
    }
    
    return true;
}

// Event listener untuk form submission
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filter-form');
    
    filterForm.addEventListener('submit', function(e) {
        if (!validateDateFilter()) {
            e.preventDefault();
        }
    });
    
    // Set tanggal default jika tidak ada filter
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (!startDate.value && !endDate.value) {
        // Set default range untuk bulan ini
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        startDate.value = firstDay.toISOString().split('T')[0];
        endDate.value = lastDay.toISOString().split('T')[0];
    }
});

function cetakLaporan() {
    const printWindow = window.open('', '_blank');
    const namaPembimbing = '<?= htmlspecialchars($namaPembimbing ?? 'Pembimbing') ?>';
    const periode = '<?= $bulanList[$bulan] ?> <?= $year ?>';
    const totalHariKerja = '<?= $totalHariKerja ?>';
    
    // Data untuk tabel
    const dataSiswa = [
        <?php 
        $no = 1;
        $totalHadir = 0;
        $totalIjin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        
        foreach($statistikSiswa as $stat): 
            $hadir = $stat['hadir'];
            $ijin = $stat['ijin'];
            $sakit = $stat['sakit'];
            $alpa = $stat['alpa'];
            $totalHari = $stat['total_hari'];
            
            $totalHadir += $hadir;
            $totalIjin += $ijin;
            $totalSakit += $sakit;
            $totalAlpa += $alpa;
        ?>
        {
            no: <?= $no++ ?>,
            nis: '<?= htmlspecialchars($stat['siswa']['nis']) ?>',
            nama: '<?= htmlspecialchars($stat['siswa']['nama']) ?>',
            kelas: '<?= htmlspecialchars($stat['siswa']['kelas']) ?>',
            tempat_pkl: '<?= htmlspecialchars($stat['siswa']['tempat_pkl'] ?? '-') ?>',
            hadir: <?= $hadir ?>,
            ijin: <?= $ijin ?>,
            sakit: <?= $sakit ?>,
            alpa: <?= $alpa ?>,
            total_hari: <?= $totalHari ?>
        },
        <?php endforeach; ?>
    ];
    
    const totalSiswa = <?= count($statistikSiswa) ?>;
    const totalHadirFinal = <?= $totalHadir ?>;
    const totalIjinFinal = <?= $totalIjin ?>;
    const totalSakitFinal = <?= $totalSakit ?>;
    const totalAlpaFinal = <?= $totalAlpa ?>;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Absensi Bulanan - ${namaPembimbing}</title>
            <style>
                @page {
                    size: F4;
                    margin: 2cm;
                }
                
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    margin: 0;
                    padding: 0;
                }
                
                .kop {
                    text-align: center;
                    border-bottom: 3px solid #000;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                
                .kop h1 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                
                .kop h2 {
                    margin: 5px 0;
                    font-size: 16px;
                    font-weight: bold;
                }
                
                .kop h3 {
                    margin: 5px 0;
                    font-size: 14px;
                    font-weight: normal;
                }
                
                .info-laporan {
                    margin-bottom: 20px;
                    text-align: center;
                }
                
                .info-laporan h4 {
                    margin: 0 0 10px 0;
                    font-size: 16px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                
                .info-laporan p {
                    margin: 5px 0;
                    font-size: 12px;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: center;
                    font-size: 11px;
                }
                
                th {
                    background-color: #f0f0f0;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                
                .text-left {
                    text-align: left;
                }
                
                .text-center {
                    text-align: center;
                }
                
                .text-right {
                    text-align: right;
                }
                
                .footer {
                    margin-top: 30px;
                    text-align: right;
                }
                
                .footer p {
                    margin: 5px 0;
                    font-size: 12px;
                }
                
                .signature {
                    margin-top: 50px;
                }
                
                @media print {
                    body {
                        font-size: 11px;
                    }
                    
                    th, td {
                        padding: 6px;
                        font-size: 10px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="kop">
                <h1>Laporan Absensi Bulanan Siswa PKL</h1>
                <h2>Pembimbing: ${namaPembimbing}</h2>
                <h3>Periode: ${periode}</h3>
            </div>
            
            <div class="info-laporan">
                <h4>Informasi Laporan</h4>
                <p><strong>Total Hari Kerja:</strong> ${totalHariKerja} hari (Senin-Jumat)</p>
                <p><strong>Keterangan:</strong> Hadir = Absen dengan GPS, Ijin/Sakit = Input manual, Alpa = Tidak hadir</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 9%;">NIS</th>
                        <th style="width: 25%;">Nama Siswa</th>
                        <th style="width: 10%;">Kelas</th>
                        <th style="width: 20%;">Tempat PKL</th>
                        <th style="width: 7%;">Hadir</th>
                        <th style="width: 7%;">Ijin</th>
                        <th style="width: 7%;">Sakit</th>
                        <th style="width: 7%;">Alpa</th>
                        <th style="width: 10%;">Total Hari</th>
                    </tr>
                </thead>
                <tbody>
                    ${dataSiswa.map(siswa => `
                        <tr>
                            <td class="text-center">${siswa.no}</td>
                            <td class="text-center"><strong>${siswa.nis}</strong></td>
                            <td class="text-left"><strong>${siswa.nama}</strong></td>
                            <td class="text-center">${siswa.kelas}</td>
                            <td class="text-left">${siswa.tempat_pkl}</td>
                            <td class="text-center">${siswa.hadir}</td>
                            <td class="text-center">${siswa.ijin}</td>
                            <td class="text-center">${siswa.sakit}</td>
                            <td class="text-center">${siswa.alpa}</td>
                            <td class="text-center"><strong>${siswa.total_hari}</strong></td>
                        </tr>
                    `).join('')}
                </tbody>
                <tfoot>
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="5" class="text-center"><strong>TOTAL</strong></td>
                        <td class="text-center"><strong>${totalHadirFinal}</strong></td>
                        <td class="text-center"><strong>${totalIjinFinal}</strong></td>
                        <td class="text-center"><strong>${totalSakitFinal}</strong></td>
                        <td class="text-center"><strong>${totalAlpaFinal}</strong></td>
                        <td class="text-center"><strong>${totalHariKerja * totalSiswa}</strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="footer">
                <p><strong>Total Siswa:</strong> ${totalSiswa} orang</p>
                <p><strong>Total Hadir:</strong> ${totalHadirFinal} kali</p>
                <p><strong>Total Ijin:</strong> ${totalIjinFinal} kali</p>
                <p><strong>Total Sakit:</strong> ${totalSakitFinal} kali</p>
                <p><strong>Total Alpa:</strong> ${totalAlpaFinal} kali</p>
            </div>
            
            <div class="signature">
                <p style="text-align: right; margin-top: 50px;">
                    <span style="display: inline-block; width: 200px; border-top: 1px solid #000; margin-top: 40px;"></span><br>
                    <strong>${namaPembimbing}</strong><br>
                    Pembimbing PKL
                </p>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Tunggu sebentar agar konten ter-load, lalu cetak
    setTimeout(() => {
        printWindow.print();
    }, 500);
}
</script>

<style>
.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-info {
  background-color: #17a2b8;
  color: white;
}

.persentase {
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 4px;
}

.persentase.success {
  background-color: #d4edda;
  color: #155724;
}

.persentase.warning {
  background-color: #fff3cd;
  color: #856404;
}

.persentase.danger {
  background-color: #f8d7da;
  color: #721c24;
}

.table-footer {
  background-color: #f8f9fa;
  font-weight: bold;
}

.table-footer td {
  border-top: 2px solid #dee2e6;
}

.badge {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: bold;
}

.badge-success {
  background-color: #28a745;
  color: white;
}

.badge-info {
  background-color: #17a2b8;
  color: white;
}

.badge-warning {
  background-color: #ffc107;
  color: #212529;
}

.badge-danger {
  background-color: #dc3545;
  color: white;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-card.success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-card.info {
  background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.stat-card.warning {
  background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.stat-card.danger {
  background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

.stat-number {
  font-size: 2em;
  font-weight: bold;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 0.9em;
  opacity: 0.9;
}

@media print {
  .action-buttons {
    display: none;
  }
  
  .card {
    border: none;
    box-shadow: none;
  }
  
  table {
    font-size: 12px;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
