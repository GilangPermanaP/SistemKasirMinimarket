<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card">
    <div class="card-title">
        <span><i class="fa-solid fa-cash-register"></i> Transaksi Kasir</span>
        <?php if ($action === 'receipt'): ?>
            <a href="index.php?page=transaksi" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Transaksi Baru
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action === 'receipt' && isset($_SESSION['receipt'])): 
        $receipt = $_SESSION['receipt'];
        $receipt_details = $this->model->query("SELECT detail_transaksi.*, barang.nama_barang 
                                                FROM detail_transaksi 
                                                JOIN barang ON detail_transaksi.id_barang = barang.id_barang 
                                                WHERE detail_transaksi.id_transaksi = :id", 
                                                ['id' => $receipt['id_transaksi']])->fetchAll();
    ?>
        <div class="receipt-box">
            <div class="receipt-header">
                <h3>MINIMARKET KASIR</h3>
                <p>Abdul Zabar - NIM: 0</p>
                <p>Tanggal: <?php echo htmlspecialchars($receipt['tanggal']); ?></p>
                <p>No. Transaksi: #<?php echo htmlspecialchars($receipt['id_transaksi']); ?></p>
            </div>
            
            <?php foreach ($receipt_details as $item): ?>
                <div class="receipt-row">
                    <span><?php echo htmlspecialchars($item['nama_barang']); ?> (<?php echo $item['jumlah_beli']; ?> x Rp <?php echo number_format($item['harga_satuan'], 0, ',', '.'); ?>)</span>
                    <span>Rp <?php echo number_format($item['jumlah_beli'] * $item['harga_satuan'], 2, ',', '.'); ?></span>
                </div>
            <?php endforeach; ?>
            
            <div class="receipt-divider"></div>
            
            <div class="receipt-row">
                <span>Subtotal:</span>
                <span>Rp <?php echo number_format($receipt['subtotal'], 2, ',', '.'); ?></span>
            </div>
            <div class="receipt-row">
                <span>Diskon:</span>
                <span>Rp <?php echo number_format($receipt['diskon'], 2, ',', '.'); ?></span>
            </div>
            <div class="receipt-row">
                <span>Pajak (PPN 11%):</span>
                <span>Rp <?php echo number_format($receipt['pajak'], 2, ',', '.'); ?></span>
            </div>
            
            <div class="receipt-divider"></div>
            
            <div class="receipt-row receipt-total">
                <span>Total Bayar:</span>
                <span>Rp <?php echo number_format($receipt['total_bayar'], 2, ',', '.'); ?></span>
            </div>
            <div class="receipt-row">
                <span>Bayar:</span>
                <span>Rp <?php echo number_format($receipt['uang_diterima'], 2, ',', '.'); ?></span>
            </div>
            <div class="receipt-row">
                <span>Kembalian:</span>
                <span>Rp <?php echo number_format($receipt['kembalian'], 2, ',', '.'); ?></span>
            </div>
            
            <div class="receipt-divider"></div>
            <p style="text-align: center; font-size: 0.8rem; color: #64748b;">Terima kasih atas kunjungan Anda!</p>
            
            <div style="margin-top: 1.5rem; text-align: center;">
                <button class="btn btn-secondary" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak</button>
            </div>
        </div>
        <?php unset($_SESSION['receipt']); ?>

    <?php else: ?>
        <div class="kasir-container">
            <div class="kasir-cart">
                <h3>Keranjang Belanja</h3>
                <div style="margin-top: 1rem; display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 1.5rem;">
                    <div style="flex-grow: 1;">
                        <label for="select_barang" style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.25rem;">Pilih Barang</label>
                        <select id="select_barang" class="form-input">
                            <option value="">-- Cari & Pilih Barang --</option>
                            <?php foreach ($barang_list as $b): ?>
                                <option value="<?php echo $b['id_barang']; ?>" data-harga="<?php echo $b['harga']; ?>" data-stok="<?php echo $b['stok']; ?>" data-nama="<?php echo htmlspecialchars($b['nama_barang']); ?>">
                                    <?php echo htmlspecialchars($b['nama_barang']); ?> (Harga: Rp <?php echo number_format($b['harga']); ?>, Stok: <?php echo $b['stok']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="width: 100px;">
                        <label for="input_jumlah" style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.25rem;">Jumlah</label>
                        <input type="number" id="input_jumlah" class="form-input" min="1" value="1">
                    </div>
                    <button type="button" id="btn_tambah" class="btn btn-primary"><i class="fa-solid fa-cart-plus"></i></button>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th style="width: 120px;">Harga</th>
                                <th style="width: 100px; text-align: center;">Qty</th>
                                <th style="width: 120px;">Total</th>
                                <th style="width: 80px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart_body">
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b;">Keranjang kosong.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="kasir-sidebar">
                <h3>Pembayaran</h3>
                <form action="index.php?page=transaksi&action=checkout" method="POST" id="checkout_form" style="margin-top: 1rem;">
                    <div id="hidden_items_container"></div>
                    
                    <div class="form-group">
                        <label for="kode_member">Kode Member (Opsional)</label>
                        <input type="text" name="kode_member" id="kode_member" class="form-input" autocomplete="off">
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="display_subtotal">Rp 0,00</span>
                    </div>
                    
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label for="uang_diterima">Uang Diterima (Rp)</label>
                        <input type="number" step="0.01" name="uang_diterima" id="uang_diterima" class="form-input" required min="0">
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-block" style="margin-top: 1rem;">
                        <i class="fa-solid fa-cash-register"></i> Selesaikan Transaksi
                    </button>
                </form>
            </div>
        </div>

        <script>
            let cart = [];
            const selectBarang = document.getElementById('select_barang');
            const inputJumlah = document.getElementById('input_jumlah');
            const btnTambah = document.getElementById('btn_tambah');
            const cartBody = document.getElementById('cart_body');
            const displaySubtotal = document.getElementById('display_subtotal');
            const hiddenContainer = document.getElementById('hidden_items_container');

            btnTambah.addEventListener('click', function() {
                const selected = selectBarang.options[selectBarang.selectedIndex];
                if (!selected.value) return;

                const id = selected.value;
                const nama = selected.getAttribute('data-nama');
                const harga = parseFloat(selected.getAttribute('data-harga'));
                const stok = parseInt(selected.getAttribute('data-stok'));
                const qty = parseInt(inputJumlah.value);

                if (qty <= 0 || isNaN(qty)) {
                    alert('Jumlah harus valid.');
                    return;
                }

                const existingIndex = cart.findIndex(item => item.id === id);
                let currentQtyInCart = 0;
                if (existingIndex !== -1) {
                    currentQtyInCart = cart[existingIndex].qty;
                }

                if (qty + currentQtyInCart > stok) {
                    alert('Stok tidak mencukupi. Sisa stok: ' + (stok - currentQtyInCart));
                    return;
                }

                if (existingIndex !== -1) {
                    cart[existingIndex].qty += qty;
                } else {
                    cart.push({ id, nama, harga, qty });
                }

                updateCart();
                selectBarang.value = '';
                inputJumlah.value = 1;
            });

            function updateCart() {
                if (cart.length === 0) {
                    cartBody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #64748b;">Keranjang kosong.</td></tr>';
                    displaySubtotal.innerText = 'Rp 0,00';
                    hiddenContainer.innerHTML = '';
                    return;
                }

                let html = '';
                let subtotal = 0;
                let hiddenHtml = '';

                cart.forEach((item, index) => {
                    const total = item.harga * item.qty;
                    subtotal += total;
                    
                    html += `<tr>
                        <td>${item.nama}</td>
                        <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                        <td style="text-align: center;">${item.qty}</td>
                        <td>Rp ${total.toLocaleString('id-ID')}</td>
                        <td style="text-align: center;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </td>
                    </tr>`;

                    hiddenHtml += `
                        <input type="hidden" name="items[${index}][id_barang]" value="${item.id}">
                        <input type="hidden" name="items[${index}][jumlah_beli]" value="${item.qty}">
                    `;
                });

                cartBody.innerHTML = html;
                displaySubtotal.innerText = 'Rp ' + subtotal.toLocaleString('id-ID', { minimumFractionDigits: 2 });
                hiddenContainer.innerHTML = hiddenHtml;
            }

            function removeItem(index) {
                cart.splice(index, 1);
                updateCart();
            }
        </script>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
