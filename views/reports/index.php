<?php $this->render('layout', ['title' => 'Reports', 'content' => ob_start()]); ?>

<div class="page-header">
    <h1>Reports</h1>
    <p class="text-muted">Generate and view attendance reports</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Daily Attendance Report</h3>
            </div>
            <div class="card-body">
                <form action="/reports/daily" method="GET">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Batch Attendance Report</h3>
            </div>
            <div class="card-body">
                <form action="/reports/batch" method="GET">
                    <div class="mb-3">
                        <label for="batch_id" class="form-label">Select Batch</label>
                        <select class="form-select" id="batch_id" name="batch_id" required>
                            <option value="">Choose a batch...</option>
                            <!-- This will be populated via AJAX -->
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= date('Y-m-d', strtotime('-30 days')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="/export?type=candidates&format=csv" class="btn btn-outline-primary w-100 mb-2">
                            <i class="material-icons">download</i>
                            Export Candidates
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/export?type=attendance&format=csv" class="btn btn-outline-primary w-100 mb-2">
                            <i class="material-icons">download</i>
                            Export Attendance
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/import" class="btn btn-outline-success w-100 mb-2">
                            <i class="material-icons">upload</i>
                            Import Data
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/dashboard" class="btn btn-outline-info w-100 mb-2">
                            <i class="material-icons">dashboard</i>
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load batches for the batch report
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/batches')
        .then(response => response.json())
        .then(batches => {
            const select = document.getElementById('batch_id');
            batches.forEach(batch => {
                const option = document.createElement('option');
                option.value = batch.id;
                option.textContent = batch.name;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading batches:', error));
});
</script>

<?php $this->render('layout', ['content' => ob_get_clean()]); ?>
