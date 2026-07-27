<?php
namespace App\Models;

class Citizen {
    private $db;
    
    public function __construct() {
        $this->db = \Database::getInstance();
    }
    
    public function find($id) {
        return $this->db->query("SELECT * FROM citizens WHERE id = ?", [$id])->fetch();
    }
    
    public function findByNationalId($nationalId) {
        return $this->db->query("SELECT * FROM citizens WHERE national_id = ?", [$nationalId])->fetch();
    }
    
    public function create($data) {
        $sql = "INSERT INTO citizens (national_id, first_name, last_name, first_name_ar, last_name_ar, date_of_birth, place_of_birth, gender, address, phone, email, blood_type, father_name, mother_name, marital_status, photo_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, array_values($data));
        return $this->db->getConnection()->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE citizens SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->query($sql, $values);
    }
    
    public function delete($id) {
        $this->db->query("DELETE FROM registers WHERE citizen_id = ?", [$id]);
        $this->db->query("DELETE FROM documents WHERE citizen_id = ?", [$id]);
        return $this->db->query("DELETE FROM citizens WHERE id = ?", [$id]);
    }
    
    public function paginate($page = 1, $perPage = 20, $search = '') {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM citizens";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE first_name LIKE ? OR last_name LIKE ? OR national_id LIKE ?";
            $params = array_fill(0, 3, "%$search%");
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
}