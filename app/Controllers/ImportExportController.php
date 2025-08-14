<?php

namespace App\Controllers;

use App\Core\Controller;

class ImportExportController extends Controller
{
    public function showImport()
    {
        $this->requireAuth();
        
        $this->render('import_export/import');
    }
    
    public function import()
    {
        $this->requireAuth();
        
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Please select a valid file to import');
            $this->redirect('/import');
        }
        
        $file = $_FILES['file'];
        $type = $_POST['type'] ?? 'candidates';
        
        try {
            if ($type === 'candidates') {
                $this->importCandidates($file);
            } elseif ($type === 'attendance') {
                $this->importAttendance($file);
            } else {
                throw new \Exception('Invalid import type');
            }
            
            $this->setFlash('success', 'Data imported successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error importing data: ' . $e->getMessage());
        }
        
        $this->redirect('/import');
    }
    
    private function importCandidates($file)
    {
        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            throw new \Exception('Could not open file');
        }
        
        $this->db->beginTransaction();
        
        try {
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;
                
                $name = trim($row[0]);
                $regNo = trim($row[1]);
                $batchName = trim($row[2]);
                $phone = trim($row[3] ?? '');
                $email = trim($row[4] ?? '');
                
                if (empty($name) || empty($regNo) || empty($batchName)) {
                    continue;
                }
                
                // Find batch by name
                $batch = $this->db->fetch(
                    "SELECT id FROM batches WHERE name = ?",
                    [$batchName]
                );
                
                if (!$batch) {
                    continue;
                }
                
                // Check if candidate already exists
                $existing = $this->db->fetch(
                    "SELECT id FROM candidates WHERE reg_no = ?",
                    [$regNo]
                );
                
                if ($existing) {
                    // Update existing
                    $sql = "UPDATE candidates SET name = ?, batch_id = ?, phone = ?, email = ?, updated_at = datetime('now') WHERE id = ?";
                    $this->db->execute($sql, [$name, $batch['id'], $phone, $email, $existing['id']]);
                } else {
                    // Insert new
                    $this->db->execute(
                        "INSERT INTO candidates (name, reg_no, batch_id, phone, email, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'active', datetime('now'), datetime('now'))",
                        [$name, $regNo, $batch['id'], $phone, $email]
                    );
                }
            }
            
            fclose($handle);
            $this->db->commit();
            
        } catch (\Exception $e) {
            fclose($handle);
            $this->db->rollback();
            throw $e;
        }
    }
    
    private function importAttendance($file)
    {
        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            throw new \Exception('Could not open file');
        }
        
        $this->db->beginTransaction();
        
        try {
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 4) continue;
                
                $regNo = trim($row[0]);
                $date = trim($row[1]);
                $status = trim($row[2]);
                $notes = trim($row[3] ?? '');
                
                if (empty($regNo) || empty($date) || empty($status)) {
                    continue;
                }
                
                // Find candidate by reg_no
                $candidate = $this->db->fetch(
                    "SELECT id FROM candidates WHERE reg_no = ?",
                    [$regNo]
                );
                
                if (!$candidate) {
                    continue;
                }
                
                // Check if attendance record exists
                $existing = $this->db->fetch(
                    "SELECT id FROM attendance WHERE candidate_id = ? AND date = ?",
                    [$candidate['id'], $date]
                );
                
                if ($existing) {
                    // Update existing
                    $this->db->execute(
                        "UPDATE attendance SET status = ?, notes = ?, updated_at = datetime('now') WHERE id = ?",
                        [$status, $notes, $existing['id']]
                    );
                } else {
                    // Insert new
                    $this->db->execute(
                        "INSERT INTO attendance (candidate_id, date, status, notes, created_at, updated_at) VALUES (?, ?, ?, ?, datetime('now'), datetime('now'))",
                        [$candidate['id'], $date, $status, $notes]
                    );
                }
            }
            
            fclose($handle);
            $this->db->commit();
            
        } catch (\Exception $e) {
            fclose($handle);
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function showExport()
    {
        $this->requireAuth();
        
        $this->render('import_export/export');
    }
    
    public function export()
    {
        $this->requireAuth();
        
        $type = $_POST['type'] ?? 'candidates';
        $format = $_POST['format'] ?? 'csv';
        
        try {
            if ($type === 'candidates') {
                $this->exportCandidates($format);
            } elseif ($type === 'attendance') {
                $this->exportAttendance($format);
            } else {
                throw new \Exception('Invalid export type');
            }
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error exporting data: ' . $e->getMessage());
            $this->redirect('/export');
        }
    }
    
    private function exportCandidates($format)
    {
        $candidates = $this->db->fetchAll(
            "SELECT c.*, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM candidates c
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE c.status = 'active'
             ORDER BY co.name, m.name, b.name, c.full_name"
        );
        
        if ($format === 'csv') {
            $this->exportToCSV($candidates, "candidates_export.csv");
        } else {
            $this->exportToExcel($candidates, "candidates_export.xlsx");
        }
    }
    
    private function exportAttendance($format)
    {
        $startDate = $_POST['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_POST['end_date'] ?? date('Y-m-d');
        
        $attendance = $this->db->fetchAll(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE a.attn_date BETWEEN ? AND ?
             ORDER BY a.attn_date, co.name, m.name, b.name, c.full_name",
            [$startDate, $endDate]
        );
        
        $filename = "attendance_export_$startDate" . "_to_" . "$endDate";
        
        if ($format === 'csv') {
            $this->exportToCSV($attendance, "$filename.csv");
        } else {
            $this->exportToExcel($attendance, "$filename.xlsx");
        }
    }
    
    private function exportToCSV($data, $filename)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit;
    }
    
    private function exportToExcel($data, $filename)
    {
        // This would require PhpSpreadsheet library
        // For now, redirect to CSV export
        $this->setFlash('info', 'Excel export requires PhpSpreadsheet library. Redirecting to CSV export.');
        $this->redirect('/export?format=csv');
    }
}
