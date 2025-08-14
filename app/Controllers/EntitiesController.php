<?php

namespace App\Controllers;

use App\Core\Controller;

class EntitiesController extends Controller
{
    // Constituencies
    public function constituencies()
    {
        $this->requireAuth();
        
        $constituencies = $this->db->fetchAll("SELECT * FROM constituencies ORDER BY name");
        
        $this->render('entities/constituencies', ['constituencies' => $constituencies]);
    }
    
    public function createConstituency()
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        
        if (empty($name)) {
            $this->setFlash('error', 'Constituency name is required');
            $this->redirect('/constituencies');
        }
        
        try {
            $sql = "INSERT INTO constituencies (name, code, created_at, updated_at) VALUES (?, ?, datetime('now'), datetime('now'))";
            $this->db->execute(
                $sql,
                [$name]
            );
            
            $this->setFlash('success', 'Constituency created successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error creating constituency: ' . $e->getMessage());
        }
        
        $this->redirect('/constituencies');
    }
    
    public function updateConstituency($id)
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        
        if (empty($name)) {
            $this->setFlash('error', 'Constituency name is required');
            $this->redirect('/constituencies');
        }
        
        try {
            $sql = "UPDATE constituencies SET name = ?, updated_at = datetime('now') WHERE id = ?";
            $this->db->execute(
                $sql,
                [$name, $id]
            );
            
            $this->setFlash('success', 'Constituency updated successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error updating constituency: ' . $e->getMessage());
        }
        
        $this->redirect('/constituencies');
    }
    
    public function deleteConstituency($id)
    {
        $this->requireAuth();
        
        try {
            $this->db->execute("DELETE FROM constituencies WHERE id = ?", [$id]);
            $this->setFlash('success', 'Constituency deleted successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error deleting constituency: ' . $e->getMessage());
        }
        
        $this->redirect('/constituencies');
    }
    
    // Mandals
    public function mandals()
    {
        $this->requireAuth();
        
        $mandals = $this->db->fetchAll(
            "SELECT m.*, c.name as constituency_name 
             FROM mandals m 
             JOIN constituencies c ON m.constituency_id = c.id 
             ORDER BY c.name, m.name"
        );
        
        $constituencies = $this->db->fetchAll("SELECT * FROM constituencies ORDER BY name");
        
        $this->render('entities/mandals', [
            'mandals' => $mandals,
            'constituencies' => $constituencies
        ]);
    }
    
    public function createMandal()
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        $constituencyId = $input['constituency_id'] ?? '';
        
        if (empty($name) || empty($constituencyId)) {
            $this->setFlash('error', 'Mandal name and constituency are required');
            $this->redirect('/mandals');
        }
        
        try {
            $sql = "INSERT INTO mandals (name, code, constituency_id, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))";
            $this->db->execute(
                $sql,
                [$name, $constituencyId]
            );
            
            $this->setFlash('success', 'Mandal created successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error creating mandal: ' . $e->getMessage());
        }
        
        $this->redirect('/mandals');
    }
    
    public function updateMandal($id)
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        $constituencyId = $input['constituency_id'] ?? '';
        
        if (empty($name) || empty($constituencyId)) {
            $this->setFlash('error', 'Mandal name and constituency are required');
            $this->redirect('/mandals');
        }
        
        try {
            $sql = "UPDATE mandals SET name = ?, code = ?, constituency_id = ?, updated_at = datetime('now') WHERE id = ?";
            $this->db->execute(
                $sql,
                [$name, $constituencyId, $id]
            );
            
            $this->setFlash('success', 'Mandal updated successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error updating mandal: ' . $e->getMessage());
        }
        
        $this->redirect('/mandals');
    }
    
    public function deleteMandal($id)
    {
        $this->requireAuth();
        
        try {
            $this->db->execute("DELETE FROM mandals WHERE id = ?", [$id]);
            $this->setFlash('success', 'Mandal deleted successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error deleting mandal: ' . $e->getMessage());
        }
        
        $this->redirect('/mandals');
    }
    
    // Batches
    public function batches()
    {
        $this->requireAuth();
        
        $batches = $this->db->fetchAll(
            "SELECT b.*, m.name as mandal_name, c.name as constituency_name 
             FROM batches b 
             JOIN mandals m ON b.mandal_id = m.id 
             JOIN constituencies c ON m.constituency_id = c.id 
             ORDER BY c.name, m.name, b.name"
        );
        
        $mandals = $this->db->fetchAll(
            "SELECT m.*, c.name as constituency_name 
             FROM mandals m 
             JOIN constituencies c ON m.constituency_id = c.id 
             ORDER BY c.name, m.name"
        );
        
        $this->render('entities/batches', [
            'batches' => $batches,
            'mandals' => $mandals
        ]);
    }
    
    public function createBatch()
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        $mandalId = $input['mandal_id'] ?? '';
        $startDate = $input['start_date'] ?? '';
        $endDate = $input['end_date'] ?? '';
        
        if (empty($name) || empty($mandalId)) {
            $this->setFlash('error', 'Batch name and mandal are required');
            $this->redirect('/batches');
        }
        
        try {
            $sql = "INSERT INTO batches (name, code, mandal_id, start_date, end_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))";
            $this->db->execute(
                $sql,
                [$name, $mandalId, $startDate, $endDate]
            );
            
            $this->setFlash('success', 'Batch created successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error creating batch: ' . $e->getMessage());
        }
        
        $this->redirect('/batches');
    }
    
    public function updateBatch($id)
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        $mandalId = $input['mandal_id'] ?? '';
        $startDate = $input['start_date'] ?? '';
        $endDate = $input['end_date'] ?? '';
        
        if (empty($name) || empty($mandalId)) {
            $this->setFlash('error', 'Batch name and mandal are required');
            $this->redirect('/batches');
        }
        
        try {
            $sql = "UPDATE batches SET name = ?, code = ?, mandal_id = ?, start_date = ?, end_date = ?, updated_at = datetime('now') WHERE id = ?";
            $this->db->execute(
                $sql,
                [$name, $mandalId, $startDate, $endDate, $id]
            );
            
            $this->setFlash('success', 'Batch updated successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error updating batch: ' . $e->getMessage());
        }
        
        $this->redirect('/batches');
    }
    
    public function deleteBatch($id)
    {
        $this->requireAuth();
        
        try {
            $this->db->execute("DELETE FROM batches WHERE id = ?", [$id]);
            $this->setFlash('success', 'Batch deleted successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error deleting batch: ' . $e->getMessage());
        }
        
        $this->redirect('/batches');
    }
    
    // Candidates
    public function candidates()
    {
        $this->requireAuth();
        
        $candidates = $this->db->fetchAll(
            "SELECT c.*, b.name as batch_name, m.name as mandal_name, co.name as constituency_name 
             FROM candidates c 
             JOIN batches b ON c.batch_id = b.id 
             JOIN mandals m ON b.mandal_id = m.id 
             JOIN constituencies co ON m.constituency_id = co.id 
             ORDER BY co.name, m.name, b.name, c.full_name"
        );
        
        $batches = $this->db->fetchAll(
            "SELECT b.*, m.name as mandal_name, c.name as constituency_name 
             FROM batches b 
             JOIN mandals m ON b.mandal_id = m.id 
             JOIN constituencies c ON m.constituency_id = c.id 
             ORDER BY c.name, m.name, b.name"
        );
        
        $this->render('entities/candidates', [
            'candidates' => $candidates,
            'batches' => $batches
        ]);
    }
    
    public function createCandidate()
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        $regNo = trim($input['reg_no'] ?? '');
        $batchId = $input['batch_id'] ?? '';
        $phone = trim($input['phone'] ?? '');
        $email = trim($input['email'] ?? '');
        
        if (empty($name) || empty($regNo) || empty($batchId)) {
            $this->setFlash('error', 'Name, registration number, and batch are required');
            $this->redirect('/candidates');
        }
        
        try {
            $sql = "INSERT INTO candidates (name, reg_no, batch_id, phone, email, gender, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'active', datetime('now'), datetime('now'))";
            $this->db->execute(
                $sql,
                [$name, $regNo, $batchId, $phone, $email]
            );
            
            $this->setFlash('success', 'Candidate created successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error creating candidate: ' . $e->getMessage());
        }
        
        $this->redirect('/candidates');
    }
    
    public function updateCandidate($id)
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $name = trim($input['name'] ?? '');
        $regNo = trim($input['reg_no'] ?? '');
        $batchId = $input['batch_id'] ?? '';
        $phone = trim($input['phone'] ?? '');
        $email = trim($input['email'] ?? '');
        $status = isset($input['is_active']) ? 'active' : 'inactive';
        
        if (empty($name) || empty($regNo) || empty($batchId)) {
            $this->setFlash('error', 'Name, registration number, and batch are required');
            $this->redirect('/candidates');
        }
        
        try {
            $sql = "UPDATE candidates SET name = ?, reg_no = ?, batch_id = ?, phone = ?, email = ?, gender = ?, status = ?, updated_at = datetime('now') WHERE id = ?";
            $this->db->execute(
                $sql,
                [$name, $regNo, $batchId, $phone, $email, $status, $id]
            );
            
            $this->setFlash('success', 'Candidate updated successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error updating candidate: ' . $e->getMessage());
        }
        
        $this->redirect('/candidates');
    }
    
    public function deleteCandidate($id)
    {
        $this->requireAuth();
        
        try {
            $this->db->execute("DELETE FROM candidates WHERE id = ?", [$id]);
            $this->setFlash('success', 'Candidate deleted successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error deleting candidate: ' . $e->getMessage());
        }
        
        $this->redirect('/candidates');
    }
}
