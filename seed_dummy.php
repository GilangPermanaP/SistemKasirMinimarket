<?php
// DB Connection & Seeder Bootstrap
require_once __DIR__ . '/config/database.php';

$is_cli = (php_sapi_name() === 'cli');

try {
    $dbClass = new Database();
    $db = $dbClass->getConnection();
} catch (Exception $e) {
    if ($is_cli) {
        die("Connection error: " . $e->getMessage() . "\n");
    } else {
        echo "<div style='color:red;'>Connection error: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit();
    }
}

// Disable foreign key constraints for truncation
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("TRUNCATE TABLE transaksi_penjualan;");
$db->exec("TRUNCATE TABLE barang;");
$db->exec("TRUNCATE TABLE member;");
$db->exec("TRUNCATE TABLE kategori;");
$db->exec("TRUNCATE TABLE user;");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

// Seed Users
$users = [
    [1, 'admin', password_hash('admin', PASSWORD_DEFAULT), 'Admin'],
    [2, 'operator', password_hash('operator', PASSWORD_DEFAULT), 'Operator']
];
$stmt = $db->prepare("INSERT INTO user (id_user, username, password, role) VALUES (?, ?, ?, ?)");
foreach ($users as $u) {
    $stmt->execute($u);
}

// Seed Categories
$categories = [
    [1, 'Makanan'],
    [2, 'Minuman'],
    [3, 'Peralatan Mandi'],
    [4, 'Kebutuhan Rumah Tangga'],
    [5, 'Obat-obatan'],
    [6, 'Alat Tulis Kantor (ATK)'],
    [7, 'Camilan']
];
$stmt = $db->prepare("INSERT INTO kategori (id_kategori, nama_kategori) VALUES (?, ?)");
foreach ($categories as $cat) {
    $stmt->execute($cat);
}

// Seed Goods
$goods = [
    // Makanan
    [1, 1, 'Mie Instan Goreng', 3500.00, 120, 'default.jpg'],
    [2, 1, 'Mie Instan Soto', 3400.00, 100, 'default.jpg'],
    [3, 1, 'Roti Tawar Kupas', 12500.00, 25, 'default.jpg'],
    [4, 1, 'Sarden Kaleng 155g', 10500.00, 45, 'default.jpg'],
    [5, 1, 'Margarin Serbaguna', 9500.00, 60, 'default.jpg'],
    // Minuman
    [6, 2, 'Air Mineral 600ml', 3000.00, 200, 'default.jpg'],
    [7, 2, 'Teh Botol 450ml', 5000.00, 150, 'default.jpg'],
    [8, 2, 'Kopi Susu Instan Cup', 7000.00, 80, 'default.jpg'],
    [9, 2, 'Suku UHT Cokelat 250ml', 6500.00, 110, 'default.jpg'],
    [10, 2, 'Minuman Isotonik 500ml', 7500.00, 90, 'default.jpg'],
    // Peralatan Mandi
    [11, 3, 'Sabun Cair Anti Bakteri', 24000.00, 35, 'default.jpg'],
    [12, 3, 'Shampoo Anti Dandruff', 26500.00, 40, 'default.jpg'],
    [13, 3, 'Pasta Gigi Herbal', 13500.00, 60, 'default.jpg'],
    [14, 3, 'Sikat Gigi Bulu Lembut', 9500.00, 75, 'default.jpg'],
    // Kebutuhan Rumah Tangga
    [15, 4, 'Deterjen Bubuk 800g', 21000.00, 30, 'default.jpg'],
    [16, 4, 'Pembersih Lantai Apel', 14500.00, 50, 'default.jpg'],
    [17, 4, 'Sabun Cuci Piring Cair', 12000.00, 40, 'default.jpg'],
    [18, 4, 'Tisu Wajah 250s', 16500.00, 70, 'default.jpg'],
    // Obat-obatan
    [19, 5, 'Obat Batuk & Flu 60ml', 17500.00, 25, 'default.jpg'],
    [20, 5, 'Tablet Paracetamol 500mg', 4500.00, 100, 'default.jpg'],
    [21, 5, 'Plester Luka Isi 10', 6000.00, 80, 'default.jpg'],
    // ATK
    [22, 6, 'Buku Tulis 38 Lembar', 4500.00, 90, 'default.jpg'],
    [23, 6, 'Pulpen Gel Hitam 0.5', 5000.00, 120, 'default.jpg'],
    // Camilan
    [24, 7, 'Keripik Kentang Keju', 11500.00, 55, 'default.jpg'],
    [25, 7, 'Biskuit Cokelat Sandwich', 8500.00, 65, 'default.jpg']
];
$stmt = $db->prepare("INSERT INTO barang (id_barang, id_kategori, nama_barang, harga, stok, foto_barang) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($goods as $item) {
    $stmt->execute($item);
}

// Seed Members
$members = [
    [1, 'Budi Santoso', 'MBR001', 5.00],
    [2, 'Siti Aminah', 'MBR002', 10.00],
    [3, 'Ahmad Fauzi', 'MBR003', 7.50],
    [4, 'Dewi Lestari', 'MBR004', 5.00],
    [5, 'Eko Prasetyo', 'MBR005', 8.00],
    [6, 'Rina Amalia', 'MBR006', 0.00]
];
$stmt = $db->prepare("INSERT INTO member (id_member, nama_member, kode_member, diskon_persen) VALUES (?, ?, ?, ?)");
foreach ($members as $mem) {
    $stmt->execute($mem);
}

// Generate Historical Transactions over the last 30 days
$transaction_count = 20;
$total_revenue_seeded = 0;

for ($i = 1; $i <= $transaction_count; $i++) {
    // Generate date distributed over 30 days
    $days_ago = $transaction_count - $i;
    $hour_offset = rand(8, 21); // operating hours
    $minute_offset = rand(0, 59);
    $second_offset = rand(0, 59);
    $tanggal = date('Y-m-d H:i:s', strtotime("-$days_ago days")) ;
    $tanggal = substr($tanggal, 0, 11) . sprintf('%02d:%02d:%02d', $hour_offset, $minute_offset, $second_offset);

    // Random user (Admin or Operator)
    $id_user = rand(1, 2);

    // Random member (60% chance of having member)
    $id_member = null;
    $member_discount_percent = 0;
    if (rand(1, 10) <= 6) {
        $rand_member = $members[array_rand($members)];
        $id_member = $rand_member[0];
        $member_discount_percent = $rand_member[3];
    }

    // Choose 1 to 4 random unique items
    $shuffled_goods = $goods;
    shuffle($shuffled_goods);
    $items_in_trans = array_slice($shuffled_goods, 0, rand(1, 4));

    $subtotal = 0;
    $details = [];

    foreach ($items_in_trans as $goods_item) {
        $id_barang = $goods_item[0];
        $price = $goods_item[3];
        $qty = rand(1, 3);

        $item_subtotal = $price * $qty;
        $subtotal += $item_subtotal;

        $details[] = [
            'id_barang' => $id_barang,
            'qty' => $qty,
            'price' => $price
        ];
    }

    // Calculate discounts
    $member_discount = $subtotal * ($member_discount_percent / 100);
    $extra_discount = 0;

    if ($id_member !== null) {
        if ($subtotal >= 500000) {
            $extra_discount = 50000;
        } elseif ($subtotal >= 200000) {
            $extra_discount = 20000;
        } elseif ($subtotal >= 100000) {
            $extra_discount = 5000;
        }
    } else {
        if ($subtotal >= 300000) {
            $extra_discount = 15000;
        } elseif ($subtotal >= 150000) {
            $extra_discount = 5000;
        }
    }

    $diskon = $member_discount + $extra_discount;
    $pajak = ($subtotal - $diskon) * 0.11;
    $total_bayar = $subtotal - $diskon + $pajak;

    // Simulate payment
    $uang_diterima = ceil($total_bayar / 10000) * 10000;
    if ($uang_diterima == $total_bayar) {
        $uang_diterima += (rand(0, 1) ? 10000 : 0);
    }
    $kembalian = $uang_diterima - $total_bayar;

    // Insert transaction
    $invoice = 'TRX-' . date('Ymd', strtotime($tanggal)) . '-' . sprintf('%04d', $i);
    $stmt_trans = $db->prepare("INSERT INTO transaksi_penjualan (invoice, id_user, id_member, id_barang, jumlah_beli, harga_satuan, tanggal, subtotal, diskon, pajak, total_bayar, uang_diterima, kembalian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_upd = $db->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");

    foreach ($details as $det) {
        $stmt_trans->execute([
            $invoice,
            $id_user,
            $id_member,
            $det['id_barang'],
            $det['qty'],
            $det['price'],
            $tanggal,
            $subtotal,
            $diskon,
            $pajak,
            $total_bayar,
            $uang_diterima,
            $kembalian
        ]);
        $stmt_upd->execute([$det['qty'], $det['id_barang']]);
    }

    $total_revenue_seeded += $total_bayar;
}

// Check output format (CLI or HTML Browser)
if ($is_cli) {
    echo "========================================\n";
    echo " DATABASE SEEDED SUCCESSFULLY!\n";
    echo "========================================\n";
    echo "Users Seeded       : " . count($users) . "\n";
    echo "Categories Seeded  : " . count($categories) . "\n";
    echo "Goods Seeded       : " . count($goods) . "\n";
    echo "Members Seeded     : " . count($members) . "\n";
    echo "Transactions Seeded: " . $transaction_count . "\n";
    echo "Total Sales Seeded : Rp " . number_format($total_revenue_seeded, 2, ',', '.') . "\n";
    echo "========================================\n";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Seeder - Sistem Kasir Minimarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid #10b981;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease-out;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        p.subtitle {
            color: #94a3b8;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: left;
        }

        .stat-item.full-width {
            grid-column: span 2;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%);
            border-color: rgba(59, 130, 246, 0.2);
        }

        .stat-label {
            font-size: 0.8rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #ffffff;
        }

        .stat-item.full-width .stat-value {
            font-size: 1.5rem;
            color: #60a5fa;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background-color: #2563eb;
            color: #ffffff;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1>Database Seeded!</h1>
        <p class="subtitle">Dummy data has been successfully generated for Sistem Kasir Minimarket.</p>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-label">Categories Seeded</div>
                <div class="stat-value"><?php echo count($categories); ?> Items</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Products Seeded</div>
                <div class="stat-value"><?php echo count($goods); ?> Items</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Members Seeded</div>
                <div class="stat-value"><?php echo count($members); ?> Accounts</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Transactions</div>
                <div class="stat-value"><?php echo $transaction_count; ?> Records</div>
            </div>
            <div class="stat-item full-width">
                <div class="stat-label">Simulated Total Sales Revenue</div>
                <div class="stat-value">Rp <?php echo number_format($total_revenue_seeded, 2, ',', '.'); ?></div>
            </div>
        </div>

        <a href="index.php?page=login" class="btn">
            <i class="fa-solid fa-right-to-bracket"></i> Go to Login Screen
        </a>
    </div>
</body>
</html>
