<?php $this->render('layout', ['title' => 'Mark Attendance', 'content' => ob_start()]); ?>

<div class="page-header">
    <h1>Mark Attendance</h1>
    <p class="text-muted">Mark attendance for today's candidates</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Mark Attendance - <?= date('d M Y', strtotime($today)) ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/attendance/save" id="attendanceForm">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?= $today ?>" required>
                        </div>
                    </div>
                    
                    <?php if (empty($candidates)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No active candidates found</p>
                            <a href="/candidates" class="btn btn-primary">Add Candidates</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Reg No</th>
                                        <th>Name</th>
                                        <th>Batch</th>
                                        <th>Mandal</th>
                                        <th>Constituency</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($candidates as $index => $candidate): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($candidate['reg_no']) ?></td>
                                            <td><?= htmlspecialchars($candidate['name']) ?></td>
                                            <td><?= htmlspecialchars($candidate['batch_name']) ?></td>
                                            <td><?= htmlspecialchars($candidate['mandal_name']) ?></td>
                                            <td><?= htmlspecialchars($candidate['constituency_name']) ?></td>
                                            <td>
                                                <select class="form-select form-select-sm" name="attendance[<?= $candidate['id'] ?>][status]" required>
                                                    <option value="present">Present</option>
                                                    <option value="absent" selected>Absent</option>
                                                    <option value="late">Late</option>
                                                    <option value="excused">Excused</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="attendance[<?= $candidate['id'] ?>][notes]" placeholder="Optional notes">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">Save Attendance</button>
                            <a href="/attendance" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('attendanceForm').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to save the attendance? This will overwrite any existing records for today.')) {
        e.preventDefault();
    }
});
</script>

<?php $this->render('layout', ['content' => ob_get_clean()]); ?>
