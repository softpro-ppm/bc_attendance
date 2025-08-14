<?php

namespace App\Controllers;

use App\Core\Controller;

class AttendanceController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        
        // Get attendance data for today
        $today = date('Y-m-d');
        $attendance = $this->db->fetchAll(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE a.attn_date = ?
             ORDER BY co.name, m.name, b.name, c.full_name",
            [$today]
        );
        
        $this->renderLayout('layout', [
            'content' => $this->render('attendance/index', [
                'attendance' => $attendance,
                'today' => $today
            ]),
            'currentPage' => 'attendance-list',
            'pageTitle' => 'Attendance List - BC Attendance System',
            'user' => $this->user
        ]);
    }
    
    public function markAttendance()
    {
        $this->requireAuth();
        
        // Get candidates for today's marking
        $candidates = $this->db->fetchAll(
            "SELECT c.*, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM candidates c
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE c.status = 'active'
             ORDER BY co.name, m.name, b.name, c.full_name"
        );
        
        $this->renderLayout('layout', [
            'content' => $this->render('attendance/mark', [
                'candidates' => $candidates,
                'today' => date('Y-m-d')
            ]),
            'currentPage' => 'attendance',
            'pageTitle' => 'Mark Attendance - BC Attendance System',
            'user' => $this->user
        ]);
    }
    
    public function saveAttendance()
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $date = $input['date'] ?? date('Y-m-d');
        $attendanceData = $input['attendance'] ?? [];
        
        $this->db->beginTransaction();
        
        try {
            foreach ($attendanceData as $candidateId => $data) {
                $status = $data['status'] ?? 'absent';
                $notes = $data['notes'] ?? '';
                
                // Check if attendance record exists
                $existing = $this->db->fetch(
                    "SELECT id FROM attendance WHERE candidate_id = ? AND date = ?",
                    [$candidateId, $date]
                );
                
                if ($existing) {
                    // Update existing record
                    $this->db->execute(
                        "UPDATE attendance SET status = ?, notes = ?, updated_at = datetime('now') WHERE id = ?",
                        [$status, $notes, $existing['id']]
                    );
                } else {
                    // Insert new record
                    $this->db->execute(
                        "INSERT INTO attendance (candidate_id, date, status, notes, created_at, updated_at) VALUES (?, ?, ?, ?, datetime('now'), datetime('now'))",
                        [$candidateId, $date, $status, $notes]
                    );
                }
            }
            
            $this->db->commit();
            $this->setFlash('success', 'Attendance saved successfully!');
            $this->redirect('/attendance');
            
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->setFlash('error', 'Error saving attendance: ' . $e->getMessage());
            $this->redirect('/attendance/mark');
        }
    }
    
    public function view($id)
    {
        $this->requireAuth();
        
        $attendance = $this->db->fetch(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE a.id = ?",
            [$id]
        );
        
        if (!$attendance) {
            $this->setFlash('error', 'Attendance record not found');
            $this->redirect('/attendance');
        }
        
        $this->render('attendance/view', ['attendance' => $attendance]);
    }
    
    public function edit($id)
    {
        $this->requireAuth();
        
        $attendance = $this->db->fetch(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = b.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE a.id = ?",
            [$id]
        );
        
        if (!$attendance) {
            $this->setFlash('error', 'Attendance record not found');
            $this->redirect('/attendance');
        }
        
        $this->render('attendance/edit', ['attendance' => $attendance]);
    }
    
    public function update($id)
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $status = $input['status'] ?? 'absent';
        $notes = $input['notes'] ?? '';
        
        try {
            $this->db->execute(
                "UPDATE attendance SET status = ?, notes = ?, updated_at = datetime('now') WHERE id = ?",
                [$status, $notes, $id]
            );
            
            $this->setFlash('success', 'Attendance updated successfully!');
            $this->redirect('/attendance');
            
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error updating attendance: ' . $e->getMessage());
            $this->redirect("/attendance/edit/$id");
        }
    }
    
    public function delete($id)
    {
        $this->requireAuth();
        
        try {
            $this->db->execute("DELETE FROM attendance WHERE id = ?", [$id]);
            $this->setFlash('success', 'Attendance record deleted successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error deleting attendance: ' . $e->getMessage());
        }
        
        $this->redirect('/attendance');
    }
}
