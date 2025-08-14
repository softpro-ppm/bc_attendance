<?php

namespace App\Controllers;

use App\Core\Controller;

class ReportsController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        
        $this->render('reports/index');
    }
    
    public function daily()
    {
        $this->requireAuth();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $constituencyId = $_GET['constituency_id'] ?? null;
        $mandalId = $_GET['mandal_id'] ?? null;
        $batchId = $_GET['batch_id'] ?? null;
        
        $whereConditions = ["a.attn_date = ?"];
        $params = [$date];
        
        if ($constituencyId) {
            $whereConditions[] = "co.id = ?";
            $params[] = $constituencyId;
        }
        
        if ($mandalId) {
            $whereConditions[] = "m.id = ?";
            $params[] = $mandalId;
        }
        
        if ($batchId) {
            $whereConditions[] = "b.id = ?";
            $params[] = $batchId;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $attendance = $this->db->fetchAll(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE $whereClause
             ORDER BY co.name, m.name, b.name, c.full_name",
            $params
        );
        
        // Get summary statistics
        $summary = $this->db->fetch(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE $whereClause",
            $params
        );
        
        $this->render('reports/daily', [
            'attendance' => $attendance,
            'summary' => $summary,
            'date' => $date,
            'filters' => [
                'constituency_id' => $constituencyId,
                'mandal_id' => $mandalId,
                'batch_id' => $batchId
            ]
        ]);
    }
    
    public function batch()
    {
        $this->requireAuth();
        
        $batchId = $_GET['batch_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        if (!$batchId) {
            $this->setFlash('error', 'Batch ID is required');
            $this->redirect('/reports');
        }
        
        // Get batch details
        $batch = $this->db->fetch(
            "SELECT b.*, m.name as mandal_name, co.name as constituency_name
             FROM batches b
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE b.id = ?",
            [$batchId]
        );
        
        if (!$batch) {
            $this->setFlash('error', 'Batch not found');
            $this->redirect('/reports');
        }
        
        // Get attendance data for the batch
        $attendance = $this->db->fetchAll(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             WHERE c.batch_id = ? AND a.attn_date BETWEEN ? AND ?
             ORDER BY c.full_name, a.attn_date",
            [$batchId, $startDate, $endDate]
        );
        
        // Calculate attendance percentage for each candidate
        $candidates = [];
        $dates = [];
        
        foreach ($attendance as $record) {
            $candidateId = $record['candidate_id'];
            $date = $record['attn_date'];
            
            if (!isset($candidates[$candidateId])) {
                $candidates[$candidateId] = [
                    'id' => $candidateId,
                    'name' => $record['candidate_name'],
                    'reg_no' => $record['reg_no'],
                    'total_days' => 0,
                    'present_days' => 0,
                    'attendance_percentage' => 0
                ];
            }
            
            if (!in_array($date, $dates)) {
                $dates[] = $date;
            }
            
            $candidates[$candidateId]['total_days']++;
            if ($record['status'] === 'present') {
                $candidates[$candidateId]['present_days']++;
            }
        }
        
        // Calculate percentages
        foreach ($candidates as &$candidate) {
            if ($candidate['total_days'] > 0) {
                $candidate['attendance_percentage'] = round(
                    ($candidate['present_days'] / $candidate['total_days']) * 100, 
                    2
                );
            }
        }
        
        $this->render('reports/batch', [
            'batch' => $batch,
            'candidates' => $candidates,
            'dates' => $dates,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
    
    public function export()
    {
        $this->requireAuth();
        
        $type = $_GET['type'] ?? 'daily';
        $format = $_GET['format'] ?? 'csv';
        
        if ($type === 'daily') {
            $this->exportDailyReport($format);
        } elseif ($type === 'batch') {
            $this->exportBatchReport($format);
        } else {
            $this->setFlash('error', 'Invalid report type');
            $this->redirect('/reports');
        }
    }
    
    private function exportDailyReport($format)
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $attendance = $this->db->fetchAll(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no, b.name as batch_name, m.name as mandal_name, co.name as constituency_name
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             JOIN mandals m ON b.mandal_id = m.id
             JOIN constituencies co ON m.constituency_id = co.id
             WHERE a.attn_date = ?
             ORDER BY co.name, m.name, b.name, c.full_name",
            [$date]
        );
        
        if ($format === 'csv') {
            $this->exportToCSV($attendance, "daily_attendance_$date.csv");
        } else {
            $this->exportToExcel($attendance, "daily_attendance_$date.xlsx");
        }
    }
    
    private function exportBatchReport($format)
    {
        $batchId = $_GET['batch_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        if (!$batchId) {
            $this->setFlash('error', 'Batch ID is required');
            $this->redirect('/reports');
        }
        
        $attendance = $this->db->fetchAll(
            "SELECT a.*, c.full_name as candidate_name, c.reg_no, b.name as batch_name
             FROM attendance a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN batches b ON c.batch_id = b.id
             WHERE c.batch_id = ? AND a.attn_date BETWEEN ? AND ?
             ORDER BY c.full_name, a.attn_date",
            [$batchId, $startDate, $endDate]
        );
        
        $filename = "batch_attendance_$batchId" . "_" . $startDate . "_to_" . $endDate;
        
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
        $this->redirect('/reports/export?format=csv');
    }
}
