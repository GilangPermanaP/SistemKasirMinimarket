<?php
require_once __DIR__ . '/../models/Model.php';

class Controller {
    private $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new Model();
    }

    public function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function checkRole($roles = []) {
        $this->checkAuth();
        if (!in_array($_SESSION['user']['role'], $roles)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Unauthorized access.'];
            header('Location: index.php?page=dashboard');
            exit();
        }
    }

    public function login() {
        if (isset($_SESSION['user'])) {
            header('Location: index.php?page=dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            if (empty($username) || empty($password)) {
                $error = 'Username and password are required.';
                require __DIR__ . '/../views/login.php';
                return;
            }
            $user = $this->model->authenticate($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                header('Location: index.php?page=dashboard');
                exit();
            } else {
                $error = 'Invalid username or password.';
                require __DIR__ . '/../views/login.php';
                return;
            }
        }
        require __DIR__ . '/../views/login.php';
    }

    public function logout() {
        unset($_SESSION['user']);
        session_destroy();
        if (isset($_GET['exit'])) {
            echo "<script>window.close(); window.location.href = 'index.php?page=login';</script>";
            exit();
        }
        header('Location: index.php?page=login');
        exit();
    }

    public function dashboard() {
        $this->checkAuth();
        $total_barang = $this->model->query("SELECT COUNT(*) as cnt FROM barang")->fetch()['cnt'] ?? 0;
        $total_member = $this->model->query("SELECT COUNT(*) as cnt FROM member")->fetch()['cnt'] ?? 0;
        $total_transaksi = $this->model->query("SELECT COUNT(*) as cnt FROM transaksi")->fetch()['cnt'] ?? 0;
        $total_revenue = $this->model->getReportTotalRevenue() ?? 0;
        require __DIR__ . '/../views/dashboard.php';
    }

    public function barang() {
        $this->checkRole(['Admin']);
        $action = $_GET['action'] ?? 'list';
        $error = '';
        $success = '';

        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_barang = trim($_POST['nama_barang'] ?? '');
            $id_kategori = trim($_POST['id_kategori'] ?? '');
            $harga = trim($_POST['harga'] ?? '');
            $stok = trim($_POST['stok'] ?? '');

            if (empty($nama_barang) || empty($id_kategori) || $harga === '' || $stok === '') {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
                header('Location: index.php?page=barang&action=add');
                exit();
            }

            if (!is_numeric($harga) || $harga < 0 || !is_numeric($stok) || $stok < 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Price and stock must be positive numbers.'];
                header('Location: index.php?page=barang&action=add');
                exit();
            }

            $duplicate = $this->model->read('barang', ['nama_barang' => $nama_barang]);
            if (!empty($duplicate)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Product name already exists.'];
                header('Location: index.php?page=barang&action=add');
                exit();
            }

            $foto_barang = 'default.jpg';
            if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['foto_barang']['tmp_name'];
                $filename = time() . '_' . basename($_FILES['foto_barang']['name']);
                $target_dir = __DIR__ . '/../assets/images/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                if (move_uploaded_file($tmp_name, $target_dir . $filename)) {
                    $foto_barang = $filename;
                }
            }

            $this->model->create('barang', [
                'nama_barang' => $nama_barang,
                'id_kategori' => $id_kategori,
                'harga' => $harga,
                'stok' => $stok,
                'foto_barang' => $foto_barang
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product created successfully.'];
            header('Location: index.php?page=barang');
            exit();
        }

        if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_barang = trim($_POST['id_barang'] ?? '');
            $nama_barang = trim($_POST['nama_barang'] ?? '');
            $id_kategori = trim($_POST['id_kategori'] ?? '');
            $harga = trim($_POST['harga'] ?? '');
            $stok = trim($_POST['stok'] ?? '');

            if (empty($id_barang) || empty($nama_barang) || empty($id_kategori) || $harga === '' || $stok === '') {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
                header('Location: index.php?page=barang&action=edit_view&id=' . $id_barang);
                exit();
            }

            if (!is_numeric($harga) || $harga < 0 || !is_numeric($stok) || $stok < 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Price and stock must be positive numbers.'];
                header('Location: index.php?page=barang&action=edit_view&id=' . $id_barang);
                exit();
            }

            $stmt = $this->model->query("SELECT * FROM barang WHERE nama_barang = :name AND id_barang != :id", ['name' => $nama_barang, 'id' => $id_barang]);
            if ($stmt->fetch()) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Product name already exists.'];
                header('Location: index.php?page=barang&action=edit_view&id=' . $id_barang);
                exit();
            }

            $current_barang = $this->model->getById('barang', 'id_barang', $id_barang);
            $foto_barang = 'default.jpg';
            if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['foto_barang']['tmp_name'];
                $filename = time() . '_' . basename($_FILES['foto_barang']['name']);
                $target_dir = __DIR__ . '/../assets/images/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                if (move_uploaded_file($tmp_name, $target_dir . $filename)) {
                    $foto_barang = $filename;
                    if ($current_barang['foto_barang'] !== 'default.jpg') {
                        @unlink($target_dir . $current_barang['foto_barang']);
                    }
                }
            }

            $this->model->update('barang', [
                'nama_barang' => $nama_barang,
                'id_kategori' => $id_kategori,
                'harga' => $harga,
                'stok' => $stok,
                'foto_barang' => $foto_barang
            ], 'id_barang', $id_barang);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product updated successfully.'];
            header('Location: index.php?page=barang');
            exit();
        }

        if ($action === 'delete') {
            $id = $_GET['id'] ?? '';
            $current_barang = $this->model->getById('barang', 'id_barang', $id);
            if ($current_barang) {
                if ($current_barang['foto_barang'] !== 'default.jpg') {
                    @unlink(__DIR__ . '/../assets/images/' . $current_barang['foto_barang']);
                }
                $this->model->delete('barang', 'id_barang', $id);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product deleted successfully.'];
            }
            header('Location: index.php?page=barang');
            exit();
        }

        $query = trim($_GET['search'] ?? '');
        $order_by = $_GET['sort'] ?? 'nama_barang';
        $direction = $_GET['dir'] ?? 'ASC';
        $kategori_list = $this->model->read('kategori', [], 'nama_kategori', 'ASC');
        
        if ($action === 'add') {
            require __DIR__ . '/../views/master_barang.php';
            return;
        }

        if ($action === 'edit_view') {
            $id = $_GET['id'] ?? '';
            $barang = $this->model->getById('barang', 'id_barang', $id);
            require __DIR__ . '/../views/master_barang.php';
            return;
        }

        $barang_list = $this->model->searchAndSort('barang', ['nama_barang', 'kategori.nama_kategori'], $query, $order_by, $direction);
        require __DIR__ . '/../views/master_barang.php';
    }

    public function kategori() {
        $this->checkRole(['Admin']);
        $action = $_GET['action'] ?? 'list';
        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            if (empty($nama_kategori)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Category name is required.'];
                header('Location: index.php?page=kategori');
                exit();
            }
            $duplicate = $this->model->read('kategori', ['nama_kategori' => $nama_kategori]);
            if (!empty($duplicate)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Category already exists.'];
                header('Location: index.php?page=kategori');
                exit();
            }
            $this->model->create('kategori', ['nama_kategori' => $nama_kategori]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Category created.'];
            header('Location: index.php?page=kategori');
            exit();
        }
        if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_kategori = trim($_POST['id_kategori'] ?? '');
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            if (empty($id_kategori) || empty($nama_kategori)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
                header('Location: index.php?page=kategori');
                exit();
            }
            $this->model->update('kategori', ['nama_kategori' => $nama_kategori], 'id_kategori', $id_kategori);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Category updated.'];
            header('Location: index.php?page=kategori');
            exit();
        }
        if ($action === 'delete') {
            $id = $_GET['id'] ?? '';
            $this->model->delete('kategori', 'id_kategori', $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Category deleted.'];
            header('Location: index.php?page=kategori');
            exit();
        }
        $query = trim($_GET['search'] ?? '');
        $order_by = $_GET['sort'] ?? 'nama_kategori';
        $direction = $_GET['dir'] ?? 'ASC';
        $kategori_list = $this->model->searchAndSort('kategori', ['nama_kategori'], $query, $order_by, $direction);
        require __DIR__ . '/../views/kategori.php';
    }

    public function member() {
        $this->checkRole(['Admin']);
        $action = $_GET['action'] ?? 'list';
        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_member = trim($_POST['nama_member'] ?? '');
            $kode_member = trim($_POST['kode_member'] ?? '');
            $diskon_persen = trim($_POST['diskon_persen'] ?? '');

            if (empty($nama_member) || empty($kode_member) || $diskon_persen === '') {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
                header('Location: index.php?page=member');
                exit();
            }
            if (!is_numeric($diskon_persen) || $diskon_persen < 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Discount must be positive.'];
                header('Location: index.php?page=member');
                exit();
            }
            $duplicate = $this->model->read('member', ['kode_member' => $kode_member]);
            if (!empty($duplicate)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Member code already exists.'];
                header('Location: index.php?page=member');
                exit();
            }
            $this->model->create('member', [
                'nama_member' => $nama_member,
                'kode_member' => $kode_member,
                'diskon_persen' => $diskon_persen
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member created.'];
            header('Location: index.php?page=member');
            exit();
        }
        if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_member = trim($_POST['id_member'] ?? '');
            $nama_member = trim($_POST['nama_member'] ?? '');
            $kode_member = trim($_POST['kode_member'] ?? '');
            $diskon_persen = trim($_POST['diskon_persen'] ?? '');

            if (empty($id_member) || empty($nama_member) || empty($kode_member) || $diskon_persen === '') {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
                header('Location: index.php?page=member');
                exit();
            }
            $this->model->update('member', [
                'nama_member' => $nama_member,
                'kode_member' => $kode_member,
                'diskon_persen' => $diskon_persen
            ], 'id_member', $id_member);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member updated.'];
            header('Location: index.php?page=member');
            exit();
        }
        if ($action === 'delete') {
            $id = $_GET['id'] ?? '';
            $this->model->delete('member', 'id_member', $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member deleted.'];
            header('Location: index.php?page=member');
            exit();
        }
        $query = trim($_GET['search'] ?? '');
        $order_by = $_GET['sort'] ?? 'nama_member';
        $direction = $_GET['dir'] ?? 'ASC';
        $member_list = $this->model->searchAndSort('member', ['nama_member', 'kode_member'], $query, $order_by, $direction);
        require __DIR__ . '/../views/member.php';
    }

    public function user() {
        $this->checkRole(['Admin']);
        $action = $_GET['action'] ?? 'list';
        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = trim($_POST['role'] ?? '');

            if (empty($username) || empty($password) || empty($role)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
                header('Location: index.php?page=user');
                exit();
            }
            $duplicate = $this->model->read('user', ['username' => $username]);
            if (!empty($duplicate)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Username already exists.'];
                header('Location: index.php?page=user');
                exit();
            }
            $this->model->create('user', [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User created.'];
            header('Location: index.php?page=user');
            exit();
        }
        if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_user = trim($_POST['id_user'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $role = trim($_POST['role'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($id_user) || empty($username) || empty($role)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
                header('Location: index.php?page=user');
                exit();
            }
            $data = ['username' => $username, 'role' => $role];
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $this->model->update('user', $data, 'id_user', $id_user);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User updated.'];
            header('Location: index.php?page=user');
            exit();
        }
        if ($action === 'delete') {
            $id = $_GET['id'] ?? '';
            $this->model->delete('user', 'id_user', $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User deleted.'];
            header('Location: index.php?page=user');
            exit();
        }
        $query = trim($_GET['search'] ?? '');
        $order_by = $_GET['sort'] ?? 'username';
        $direction = $_GET['dir'] ?? 'ASC';
        $user_list = $this->model->searchAndSort('user', ['username', 'role'], $query, $order_by, $direction);
        require __DIR__ . '/../views/user.php';
    }

    public function transaksi() {
        $this->checkRole(['Admin', 'Operator']);
        $action = $_GET['action'] ?? 'form';

        $goods_count = $this->model->query("SELECT COUNT(*) as cnt FROM barang")->fetch();
        if ($goods_count['cnt'] == 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Transaksi diblokir. Data master barang kosong di database.'];
            if ($action === 'checkout') {
                header('Location: index.php?page=transaksi');
                exit();
            }
        }

        if ($action === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $kode_member = trim($_POST['kode_member'] ?? '');
            $uang_diterima = trim($_POST['uang_diterima'] ?? 0);
            $items = $_POST['items'] ?? [];

            if (empty($items)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'No items added.'];
                header('Location: index.php?page=transaksi');
                exit();
            }

            if (!is_numeric($uang_diterima) || $uang_diterima < 0) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Amount received must be a positive number.'];
                header('Location: index.php?page=transaksi');
                exit();
            }

            $subtotal = 0;
            $processed_items = [];
            foreach ($items as $item) {
                $id_barang = $item['id_barang'];
                $jumlah_beli = $item['jumlah_beli'];

                if (!is_numeric($jumlah_beli) || $jumlah_beli <= 0) {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Quantity must be greater than zero.'];
                    header('Location: index.php?page=transaksi');
                    exit();
                }

                $barang = $this->model->getById('barang', 'id_barang', $id_barang);
                if (!$barang) {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Product not found.'];
                    header('Location: index.php?page=transaksi');
                    exit();
                }

                if ($barang['stok'] < $jumlah_beli) {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Stock insufficient for ' . $barang['nama_barang']];
                    header('Location: index.php?page=transaksi');
                    exit();
                }

                $item_subtotal = $barang['harga'] * $jumlah_beli;
                $subtotal += $item_subtotal;
                $processed_items[] = [
                    'id_barang' => $id_barang,
                    'harga_satuan' => $barang['harga'],
                    'jumlah_beli' => $jumlah_beli,
                    'stok_sisa' => $barang['stok'] - $jumlah_beli
                ];
            }

            $id_member = null;
            $member_discount = 0;
            $extra_discount = 0;
            $is_member = false;

            if (!empty($kode_member)) {
                $member = $this->model->read('member', ['kode_member' => $kode_member]);
                if (!empty($member)) {
                    $is_member = true;
                    $member = $member[0];
                    $id_member = $member['id_member'];
                    $member_discount = $subtotal * ($member['diskon_persen'] / 100);

                    if ($subtotal >= 500000) {
                        $extra_discount = 50000;
                    } elseif ($subtotal >= 200000) {
                        $extra_discount = 20000;
                    } elseif ($subtotal >= 100000) {
                        $extra_discount = 5000;
                    } else {
                        $extra_discount = 0;
                    }
                }
            }

            if (!$is_member) {
                if ($subtotal >= 300000) {
                    $extra_discount = 15000;
                } elseif ($subtotal >= 150000) {
                    $extra_discount = 5000;
                } else {
                    $extra_discount = 0;
                }
            }

            $diskon = $member_discount + $extra_discount;
            $pajak = ($subtotal - $diskon) * 0.11;
            $total_bayar = $subtotal - $diskon + $pajak;

            if ($uang_diterima < $total_bayar) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Insufficient money paid. Total is Rp ' . number_format($total_bayar, 2)];
                header('Location: index.php?page=transaksi');
                exit();
            }

            $kembalian = $uang_diterima - $total_bayar;

            $transaksi_id = $this->model->create('transaksi', [
                'id_user' => $_SESSION['user']['id_user'],
                'id_member' => $id_member,
                'tanggal' => date('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'pajak' => $pajak,
                'total_bayar' => $total_bayar,
                'kembalian' => $kembalian,
                'uang_diterima' => $uang_diterima
            ]);

            foreach ($processed_items as $p_item) {
                $this->model->create('detail_transaksi', [
                    'id_transaksi' => $transaksi_id,
                    'id_barang' => $p_item['id_barang'],
                    'jumlah_beli' => $p_item['jumlah_beli'],
                    'harga_satuan' => $p_item['harga_satuan']
                ]);
                $this->model->update('barang', ['stok' => $p_item['stok_sisa']], 'id_barang', $p_item['id_barang']);
            }

            $_SESSION['receipt'] = [
                'id_transaksi' => $transaksi_id,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'pajak' => $pajak,
                'total_bayar' => $total_bayar,
                'uang_diterima' => $uang_diterima,
                'kembalian' => $kembalian,
                'tanggal' => date('Y-m-d H:i:s')
            ];

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaction success!'];
            header('Location: index.php?page=transaksi&action=receipt');
            exit();
        }

        if ($action === 'receipt') {
            require __DIR__ . '/../views/form_transaksi.php';
            return;
        }

        $barang_list = $this->model->read('barang', [], 'nama_barang', 'ASC');
        require __DIR__ . '/../views/form_transaksi.php';
    }

    public function laporan() {
        $this->checkRole(['Admin']);
        $start_date = $_GET['start_date'] ?? date('Y-m-d');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        $laporan_barang = $this->model->getReportMasterBarang();
        $laporan_member = $this->model->getReportMasterMember();
        $laporan_transaksi = $this->model->getReportTransaksiAll();
        $laporan_transaksi_filter = $this->model->getReportTransaksiFilter($start_date, $end_date);
        $total_pendapatan = $this->model->getReportTotalRevenue();

        require __DIR__ . '/../views/cetak_laporan.php';
    }
}
