<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-file-lines"></i> Laporan Rekapitulasi Minimarket</span>
        <button class="btn btn-secondary" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak Seluruh Laporan</button>
    </div>
    <p>Sistem Kasir Minimarket - Proyek Tugas Kuliah Abdul Zabar</p>
</div>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-filter"></i> Filter Tanggal Transaksi</span>
    </div>
    <form action="index.php" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="page" value="laporan">
        <div style="flex-grow: 1;">
            <label for="start_date" style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.25rem;">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" class="form-input" value="<?php echo htmlspecialchars($start_date); ?>">
        </div>
        <div style="flex-grow: 1;">
            <label for="end_date" style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.25rem;">Tanggal Akhir</label>
            <input type="date" name="end_date" id="end_date" class="form-input" value="<?php echo htmlspecialchars($end_date); ?>">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
    </form>
</div>

<div class="report-section card">
    <div class="report-title">
        <i class="fa-solid fa-box"></i> 1. Laporan Data Master (Barang & Member)
    </div>
    
    <h4 style="margin-top: 1rem;">Data Master Barang</h4>
    <div class="table-responsive" style="margin-bottom: 1.5rem;">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laporan_barang as $row): ?>
                    <tr>
                        <td><img src="assets/images/<?php echo $row['foto_barang']; ?>" class="product-img" alt="Foto"></td>
                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                        <td>Rp <?php echo number_format($row['harga'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($row['stok']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h4>Data Master Member</h4>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Member</th>
                    <th>Kode Member</th>
                    <th>Diskon (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laporan_member as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_member']); ?></td>
                        <td><code><?php echo htmlspecialchars($row['kode_member']); ?></code></td>
                        <td><?php echo htmlspecialchars($row['diskon_persen']); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="report-section card">
    <div class="report-title">
        <i class="fa-solid fa-clock-rotate-left"></i> 2. Laporan Riwayat Transaksi Kasir (Seluruhnya)
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Member</th>
                    <th>Subtotal</th>
                    <th>Diskon</th>
                    <th>Pajak</th>
                    <th>Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laporan_transaksi as $row): ?>
                    <tr>
                        <td>#<?php echo $row['id_transaksi']; ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_member'] ?? '-'); ?></td>
                        <td>Rp <?php echo number_format($row['subtotal'], 2, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($row['diskon'], 2, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($row['pajak'], 2, ',', '.'); ?></td>
                        <td><strong>Rp <?php echo number_format($row['total_bayar'], 2, ',', '.'); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="report-section card">
    <div class="report-title">
        <i class="fa-solid fa-calendar-days"></i> 3. Laporan Transaksi Terfilter (Periode: <?php echo htmlspecialchars($start_date); ?> s/d <?php echo htmlspecialchars($end_date); ?>)
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Member</th>
                    <th>Subtotal</th>
                    <th>Diskon</th>
                    <th>Pajak</th>
                    <th>Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporan_transaksi_filter)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b;">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($laporan_transaksi_filter as $row): ?>
                        <tr>
                            <td>#<?php echo $row['id_transaksi']; ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_member'] ?? '-'); ?></td>
                            <td>Rp <?php echo number_format($row['subtotal'], 2, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['diskon'], 2, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['pajak'], 2, ',', '.'); ?></td>
                            <td><strong>Rp <?php echo number_format($row['total_bayar'], 2, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="report-section card" style="background-color: #0f172a; color: #ffffff;">
    <div class="report-title" style="color: #ffffff; border-left-color: #10b981;">
        <i class="fa-solid fa-money-bill-trend-up"></i> 4. Laporan Total Pendapatan Kumulatif
    </div>
    <div style="padding: 1.5rem 0; text-align: center;">
        <h2 style="font-size: 1.5rem; font-weight: 500; color: #94a3b8;">Total Akumulasi Pendapatan Minimarket</h2>
        <p style="font-size: 3rem; font-weight: 700; color: #10b981; margin-top: 0.5rem;">
            Rp <?php echo number_format($total_pendapatan, 2, ',', '.'); ?>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
