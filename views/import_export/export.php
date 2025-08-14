<?php $this->render('layout', ['title' => 'Export Data', 'content' => ob_start()]); ?>

<div class="page-header">
    <h1>Export Data</h1>
    <p class="text-muted">Export candidates and attendance data to CSV format</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Export Candidates</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/export">
                    <input type="hidden" name="type" value="candidates">
                    
                    <div class="mb-3">
                        <label for="candidates_format" class="form-label">Format</label>
                        <select class="form-select" id="candidates_format" name="format">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel (XLSX)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Export Candidates</button>
                </form>
                
                <div class="mt-3">
                    <h5>Exported Data Includes:</h5>
                    <ul>
                        <li>Candidate ID</li>
                        <li>Name</li>
                        <li>Registration Number</li>
                        <li>Batch Name</li>
                        <li>Mandal Name</li>
                        <li>Constituency Name</li>
                        <li>Phone</li>
                        <li>Email</li>
                        <li>Active Status</li>
                        <li>Created Date</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Export Attendance</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/export">
                    <input type="hidden" name="type" value="attendance">
                    
                    <div class="mb-3">
                        <label for="attendance_format" class="form-label">Format</label>
                        <select class="form-select" id="attendance_format" name="format">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel (XLSX)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= date('Y-m-d', strtotime('-30 days')) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Export Attendance</button>
                </form>
                
                <div class="mt-3">
                    <h5>Exported Data Includes:</h5>
                    <ul>
                        <li>Attendance ID</li>
                        <li>Candidate Name</li>
                        <li>Registration Number</li>
                        <li>Date</li>
                        <li>Status</li>
                        <li>Notes</li>
                        <li>Batch Name</li>
                        <li>Mandal Name</li>
                        <li>Constituency Name</li>
                        <li>Created Date</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Quick Export Links</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="/export?type=candidates&format=csv" class="btn btn-outline-primary w-100 mb-2">
                            <i class="material-icons">download</i>
                            All Candidates (CSV)
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/export?type=attendance&format=csv&start_date=<?= date('Y-m-d', strtotime('-7 days')) ?>&end_date=<?= date('Y-m-d') ?>" class="btn btn-outline-primary w-100 mb-2">
                            <i class="material-icons">download</i>
                            Last 7 Days (CSV)
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/export?type=attendance&format=csv&start_date=<?= date('Y-m-d', strtotime('-30 days')) ?>&end_date=<?= date('Y-m-d') ?>" class="btn btn-outline-primary w-100 mb-2">
                            <i class="material-icons">download</i>
                            Last 30 Days (CSV)
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/export?type=attendance&format=csv&start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-t') ?>" class="btn btn-outline-primary w-100 mb-2">
                            <i class="material-icons">download</i>
                            This Month (CSV)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Export Guidelines</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>CSV Export:</h5>
                        <ul>
                            <li>Compatible with Excel, Google Sheets, and other spreadsheet applications</li>
                            <li>Uses comma (,) as delimiter</li>
                            <li>UTF-8 encoding for proper character display</li>
                            <li>Automatic download to your device</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Excel Export:</h5>
                        <ul>
                            <li>Requires PhpSpreadsheet library</li>
                            <li>Native Excel (.xlsx) format</li>
                            <li>Better formatting and multiple sheets support</li>
                            <li>Larger file sizes but better compatibility</li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong>Note:</strong> For large datasets, CSV export is recommended as it's faster and creates smaller files. Excel export is better for smaller datasets where formatting is important.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-4">
    <a href="/import" class="btn btn-outline-success me-2">Go to Import</a>
    <a href="/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
</div>

<?php $this->render('layout', ['content' => ob_get_clean()]); ?>
