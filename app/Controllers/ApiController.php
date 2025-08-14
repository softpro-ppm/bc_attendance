<?php

namespace App\Controllers;

use App\Core\Controller;

class ApiController extends Controller
{
    public function getMandals()
    {
        $this->requireAuth();
        
        $constituencyId = $_GET['constituency_id'] ?? null;
        
        if (!$constituencyId) {
            $this->json(['error' => 'Constituency ID is required'], 400);
            return;
        }
        
        $mandals = $this->db->fetchAll(
            "SELECT id, name FROM mandals WHERE constituency_id = ? ORDER BY name",
            [$constituencyId]
        );
        
        $this->json($mandals);
    }
    
    public function getBatches()
    {
        $this->requireAuth();
        
        $mandalId = $_GET['mandal_id'] ?? null;
        
        if (!$mandalId) {
            $this->json(['error' => 'Mandal ID is required'], 400);
            return;
        }
        
        $batches = $this->db->fetchAll(
            "SELECT id, name FROM batches WHERE mandal_id = ? ORDER BY name",
            [$mandalId]
        );
        
        $this->json($batches);
    }
    
    public function getCandidates()
    {
        $this->requireAuth();
        
        $batchId = $_GET['batch_id'] ?? null;
        
        if (!$batchId) {
            $this->json(['error' => 'Batch ID is required'], 400);
            return;
        }
        
        $candidates = $this->db->fetchAll(
            "SELECT id, full_name, reg_no FROM candidates WHERE batch_id = ? AND status = 'active' ORDER BY full_name",
            [$batchId]
        );
        
        $this->json($candidates);
    }
    
    public function getAttendance()
    {
        $this->requireAuth();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $batchId = $_GET['batch_id'] ?? null;
        
        if (!$batchId) {
            $this->json(['error' => 'Batch ID is required'], 400);
            return;
        }
        
        $attendance = $this->db->fetchAll(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             WHERE a.attn_date = ?
             ORDER BY c.full_name",
            [$date]
        );
        
        $this->json($attendance);
    }
}
