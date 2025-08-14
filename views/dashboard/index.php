<div class="dashboard">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">location_city</span>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($stats['constituencies']) ?></h3>
                <p class="stat-label">Constituencies</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">business</span>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($stats['mandals']) ?></h3>
                <p class="stat-label">Mandals</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">groups</span>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($stats['batches']) ?></h3>
                <p class="stat-label">Active Batches</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">people</span>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($stats['candidates']) ?></h3>
                <p class="stat-label">Candidates</p>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Overview -->
    <div class="attendance-overview">
        <div class="overview-header">
            <h2>Today's Attendance</h2>
            <span class="date-display"><?= date('l, F j, Y') ?></span>
        </div>
        
        <div class="attendance-stats">
            <div class="attendance-stat present">
                <div class="stat-icon">
                    <span class="material-icons">check_circle</span>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['todayPresent']) ?></h3>
                    <p>Present</p>
                </div>
            </div>
            
            <div class="attendance-stat absent">
                <div class="stat-icon">
                    <span class="material-icons">cancel</span>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['todayAbsent']) ?></h3>
                    <p>Absent</p>
                </div>
            </div>
            
            <div class="attendance-stat late">
                <div class="stat-icon">
                    <span class="material-icons">schedule</span>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['todayLate']) ?></h3>
                    <p>Late</p>
                </div>
            </div>
            
            <div class="attendance-stat excused">
                <div class="stat-icon">
                    <span class="material-icons">event_busy</span>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['todayExcused']) ?></h3>
                    <p>Excused</p>
                </div>
            </div>
        </div>
        
        <div class="attendance-percentage">
            <div class="percentage-circle">
                <svg viewBox="0 0 36 36" class="percentage-chart">
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                          stroke-dasharray="<?= $stats['attendancePercentage'] ?>, 100"/>
                </svg>
                <div class="percentage-text">
                    <span class="percentage-number"><?= $stats['attendancePercentage'] ?>%</span>
                    <span class="percentage-label">Attendance</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
            <a href="/attendance/mark" class="action-card">
                <span class="material-icons">how_to_reg</span>
                <h3>Mark Attendance</h3>
                <p>Record today's attendance for candidates</p>
            </a>
            
            <a href="/reports" class="action-card">
                <span class="material-icons">assessment</span>
                <h3>View Reports</h3>
                <p>Generate attendance reports and analytics</p>
            </a>
            
            <a href="/constituencies" class="action-card">
                <span class="material-icons">storage</span>
                <h3>Master Data</h3>
                <p>Manage constituencies, mandals, and batches</p>
            </a>
            
            <a href="/import" class="action-card">
                <span class="material-icons">upload_file</span>
                <h3>Import/Export</h3>
                <p>Bulk import or export attendance data</p>
            </a>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Recent Attendance -->
        <div class="content-card">
            <div class="card-header">
                <h3>Recent Attendance</h3>
                <a href="/attendance" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <?php if (!empty($recentAttendance)): ?>
                <div class="attendance-list">
                    <?php foreach (array_slice($recentAttendance, 0, 10) as $attendance): ?>
                    <div class="attendance-item">
                        <div class="attendance-status status-<?= strtolower($attendance['status']) ?>">
                            <?php
                            $statusLabels = ['P' => 'Present', 'A' => 'Absent', 'L' => 'Late', 'E' => 'Excused'];
                            echo $statusLabels[$attendance['status']] ?? $attendance['status'];
                            ?>
                        </div>
                        <div class="attendance-details">
                            <div class="candidate-name"><?= htmlspecialchars($attendance['full_name']) ?></div>
                            <div class="candidate-info">
                                <?= htmlspecialchars($attendance['reg_no']) ?> • 
                                <?= htmlspecialchars($attendance['batch_name']) ?> • 
                                <?= htmlspecialchars($attendance['mandal_name']) ?>
                            </div>
                        </div>
                        <div class="attendance-date">
                            <?= date('M j', strtotime($attendance['attn_date'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span class="material-icons">event_note</span>
                    <p>No recent attendance records</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Batches -->
        <div class="content-card">
            <div class="card-header">
                <h3>Upcoming Batches</h3>
                <a href="/batches" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <?php if (!empty($upcomingBatches)): ?>
                <div class="batch-list">
                    <?php foreach ($upcomingBatches as $batch): ?>
                    <div class="batch-item">
                        <div class="batch-info">
                            <h4 class="batch-name"><?= htmlspecialchars($batch['name']) ?></h4>
                            <p class="batch-details">
                                <?= htmlspecialchars($batch['mandal_name']) ?> • 
                                <?= htmlspecialchars($batch['constituency_name']) ?>
                            </p>
                            <p class="batch-dates">
                                <?= date('M j', strtotime($batch['start_date'])) ?> - 
                                <?= $batch['end_date'] ? date('M j, Y', strtotime($batch['end_date'])) : 'Ongoing' ?>
                            </p>
                        </div>
                        <div class="batch-candidates">
                            <span class="candidate-count"><?= $batch['candidate_count'] ?> candidates</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span class="material-icons">schedule</span>
                    <p>No upcoming batches</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Attendance Trends Chart -->
    <div class="content-card full-width">
        <div class="card-header">
            <h3>Attendance Trends (Last 30 Days)</h3>
        </div>
        <div class="card-content">
            <canvas id="attendanceChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js for attendance trends
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($attendanceTrends['labels']) ?>,
            datasets: [
                {
                    label: 'Present',
                    data: <?= json_encode($attendanceTrends['present']) ?>,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Absent',
                    data: <?= json_encode($attendanceTrends['absent']) ?>,
                    borderColor: '#F44336',
                    backgroundColor: 'rgba(244, 67, 54, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Late',
                    data: <?= json_encode($attendanceTrends['late']) ?>,
                    borderColor: '#FF9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Excused',
                    data: <?= json_encode($attendanceTrends['excused']) ?>,
                    borderColor: '#9C27B0',
                    backgroundColor: 'rgba(156, 39, 176, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
