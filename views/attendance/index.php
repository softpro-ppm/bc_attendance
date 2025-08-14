<?php $this->render('layout', ['title' => 'Attendance Overview', 'content' => ob_start()]); ?>

<div class="page-header">
    <h1>Attendance Overview</h1>
    <p class="text-muted">Today's attendance summary</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Today's Attendance - <?= date('d M Y', strtotime($today)) ?></h3>
                <a href="/attendance/mark" class="btn btn-primary">Mark Attendance</a>
            </div>
            <div class="card-body">
                <?php if (empty($attendance)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No attendance records for today</p>
                        <a href="/attendance/mark" class="btn btn-primary">Start Marking Attendance</a>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance as $index => $record): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($record['reg_no']) ?></td>
                                        <td><?= htmlspecialchars($record['candidate_name']) ?></td>
                                        <td><?= htmlspecialchars($record['batch_name']) ?></td>
                                        <td><?= htmlspecialchars($record['mandal_name']) ?></td>
                                        <td><?= htmlspecialchars($record['constituency_name']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'late' ? 'warning' : ($record['status'] === 'excused' ? 'info' : 'danger')) ?>">
                                                <?= ucfirst($record['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($record['notes'] ?? '') ?></td>
                                        <td>
                                            <a href="/attendance/edit/<?= $record['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="/attendance/view/<?= $record['id'] ?>" class="btn btn-sm btn-outline-info">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->render('layout', ['content' => ob_get_clean()]); ?>
