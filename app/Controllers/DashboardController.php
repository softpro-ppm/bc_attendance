<?php

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent attendance
        $recentAttendance = $this->getRecentAttendance();
        
        // Get upcoming batches
        $upcomingBatches = $this->getUpcomingBatches();
        
        // Get attendance trends
        $attendanceTrends = $this->getAttendanceTrends();
        
        $this->renderLayout('layout', [
            'content' => $this->render('dashboard/index', [
                'stats' => $stats,
                'recentAttendance' => $recentAttendance,
                'upcomingBatches' => $upcomingBatches,
                'attendanceTrends' => $attendanceTrends,
                'user' => $this->user
            ]),
            'currentPage' => 'dashboard',
            'pageTitle' => 'Dashboard - BC Attendance System',
            'user' => $this->user
        ]);
    }

    private function getDashboardStats()
    {
        $stats = [];
        
        // Total constituencies
        $stats['constituencies'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM constituencies WHERE status = 'active'"
        );
        
        // Total mandals
        $stats['mandals'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM mandals WHERE status = 'active'"
        );
        
        // Total batches
        $stats['batches'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM batches WHERE status = 'active'"
        );
        
        // Total candidates
        $stats['candidates'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM candidates WHERE status = 'active'"
        );
        
        // Today's attendance
        $stats['todayAttendance'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM attendance WHERE attn_date = date('now')"
        );
        
        // Today's present count
        $stats['todayPresent'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM attendance WHERE attn_date = date('now') AND status = 'P'"
        );
        
        // Today's absent count
        $stats['todayAbsent'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM attendance WHERE attn_date = date('now') AND status = 'A'"
        );
        
        // Today's late count
        $stats['todayLate'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM attendance WHERE attn_date = date('now') AND status = 'L'"
        );
        
        // Today's excused count
        $stats['todayExcused'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM attendance WHERE attn_date = date('now') AND status = 'E'"
        );
        
        // Calculate attendance percentage
        if ($stats['todayAttendance'] > 0) {
            $stats['attendancePercentage'] = round(
                ($stats['todayPresent'] / $stats['todayAttendance']) * 100, 2
            );
        } else {
            $stats['attendancePercentage'] = 0;
        }
        
        return $stats;
    }

    private function getRecentAttendance()
    {
        $sql = "SELECT 
                    a.id,
                    a.attn_date,
                    a.status,
                    a.notes,
                    c.reg_no,
                    c.full_name,
                    b.name as batch_name,
                    m.name as mandal_name,
                    const.name as constituency_name
                FROM attendance a
                JOIN candidates c ON a.candidate_id = c.id
                JOIN batches b ON c.batch_id = b.id
                JOIN mandals m ON b.mandal_id = m.id
                JOIN constituencies const ON m.constituency_id = const.id
                WHERE a.attn_date >= date('now', '-7 days')
                ORDER BY a.attn_date DESC, c.full_name ASC
                LIMIT 20";
        
        return $this->db->fetchAll($sql);
    }

    private function getUpcomingBatches()
    {
        $sql = "SELECT 
                    b.id,
                    b.name,
                    b.code,
                    b.start_date,
                    b.end_date,
                    m.name as mandal_name,
                    const.name as constituency_name,
                    COUNT(c.id) as candidate_count
                FROM batches b
                JOIN mandals m ON b.mandal_id = m.id
                JOIN constituencies const ON m.constituency_id = const.id
                LEFT JOIN candidates c ON b.id = c.batch_id AND c.status = 'active'
                WHERE b.status = 'active' 
                AND b.start_date >= date('now')
                GROUP BY b.id
                ORDER BY b.start_date ASC
                LIMIT 5";
        
        return $this->db->fetchAll($sql);
    }

    private function getAttendanceTrends()
    {
        // Get attendance for last 30 days
        $sql = "SELECT 
                    attn_date,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'P' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN status = 'L' THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = 'E' THEN 1 ELSE 0 END) as excused
                FROM attendance 
                WHERE attn_date >= date('now', '-30 days')
                GROUP BY attn_date
                ORDER BY attn_date DESC
                LIMIT 30";
        
        $data = $this->db->fetchAll($sql);
        
        // Format for chart
        $trends = [
            'labels' => [],
            'present' => [],
            'absent' => [],
            'late' => [],
            'excused' => []
        ];
        
        foreach (array_reverse($data) as $row) {
            $trends['labels'][] = date('M j', strtotime($row['attn_date']));
            $trends['present'][] = (int) $row['present'];
            $trends['absent'][] = (int) $row['absent'];
            $trends['late'][] = (int) $row['late'];
            $trends['excused'][] = (int) $row['excused'];
        }
        
        return $trends;
    }
}
