<?php
require_once __DIR__ . '/../config/database.php';

class Model {
    protected $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($table, $data) {
        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $placeholders = ":" . implode(", :", $keys);
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function read($table, $conditions = [], $order_by = null, $direction = 'ASC') {
        $sql = "SELECT * FROM $table";
        $params = [];
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $parts = [];
            foreach ($conditions as $key => $value) {
                $parts[] = "$key = :$key";
                $params[$key] = $value;
            }
            $sql .= implode(" AND ", $parts);
        }
        if ($order_by) {
            $sql .= " ORDER BY $order_by $direction";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function update($table, $data, $id_column, $id_val) {
        $parts = [];
        $params = [];
        foreach ($data as $key => $value) {
            $parts[] = "$key = :$key";
            $params[$key] = $value;
        }
        $params['id_val'] = $id_val;
        $sql = "UPDATE $table SET " . implode(", ", $parts) . " WHERE $id_column = :id_val";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($table, $id_column, $id_val) {
        $sql = "DELETE FROM $table WHERE $id_column = :id_val";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id_val' => $id_val]);
    }

    public function getById($table, $id_column, $id_val) {
        $sql = "SELECT * FROM $table WHERE $id_column = :id_val LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_val' => $id_val]);
        return $stmt->fetch();
    }

    public function searchAndSort($table, $search_cols = [], $query = '', $order_by = null, $direction = 'ASC') {
        $sql = "SELECT * FROM $table";
        $params = [];
        if ($table === 'barang') {
            $sql = "SELECT barang.*, kategori.nama_kategori FROM barang JOIN kategori ON barang.id_kategori = kategori.id_kategori";
        }
        
        $conditions = [];
        if (!empty($search_cols) && $query !== '') {
            foreach ($search_cols as $col) {
                $conditions[] = "$col LIKE :query";
            }
            $sql .= " WHERE (" . implode(" OR ", $conditions) . ")";
            $params['query'] = "%$query%";
        }
        
        if ($order_by) {
            $sql .= " ORDER BY $order_by $direction";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function authenticate($username, $password) {
        $sql = "SELECT * FROM user WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getReportMasterBarang() {
        $sql = "SELECT barang.*, kategori.nama_kategori FROM barang JOIN kategori ON barang.id_kategori = kategori.id_kategori ORDER BY barang.nama_barang ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getReportMasterMember() {
        $sql = "SELECT * FROM member ORDER BY nama_member ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getReportTransaksiAll() {
        $sql = "SELECT transaksi.*, user.username, member.nama_member 
                FROM transaksi 
                JOIN user ON transaksi.id_user = user.id_user 
                LEFT JOIN member ON transaksi.id_member = member.id_member 
                ORDER BY transaksi.tanggal DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getReportTransaksiFilter($start_date, $end_date) {
        $sql = "SELECT transaksi.*, user.username, member.nama_member 
                FROM transaksi 
                JOIN user ON transaksi.id_user = user.id_user 
                LEFT JOIN member ON transaksi.id_member = member.id_member 
                WHERE DATE(transaksi.tanggal) BETWEEN :start_date AND :end_date 
                ORDER BY transaksi.tanggal DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
        return $stmt->fetchAll();
    }

    public function getReportTotalRevenue() {
        $sql = "SELECT SUM(total_bayar) as total_pendapatan FROM transaksi";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch();
        return $res['total_pendapatan'] ?? 0;
    }

    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
