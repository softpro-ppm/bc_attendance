<?php $this->render('layout', ['title' => 'Import Data', 'content' => ob_start()]); ?>

<div class="page-header">
    <h1>Import Data</h1>
    <p class="text-muted">Import candidates and attendance data from CSV files</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Import Candidates</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/import" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="candidates">
                    
                    <div class="mb-3">
                        <label for="candidates_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="candidates_file" name="file" accept=".csv" required>
                        <div class="form-text">File should contain: Name, Reg No, Batch Name, Phone (optional), Email (optional)</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Import Candidates</button>
                </form>
                
                <div class="mt-3">
                    <h5>CSV Format Example:</h5>
                    <pre class="bg-light p-2 rounded">Name,Reg No,Batch Name,Phone,Email
John Doe,REG001,Batch A,1234567890,john@example.com
Jane Smith,REG002,Batch B,0987654321,jane@example.com</pre>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Import Attendance</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/import" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="attendance">
                    
                    <div class="mb-3">
                        <label for="attendance_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="attendance_file" name="file" accept=".csv" required>
                        <div class="form-text">File should contain: Reg No, Date, Status, Notes (optional)</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Import Attendance</button>
                </form>
                
                <div class="mt-3">
                    <h5>CSV Format Example:</h5>
                    <pre class="bg-light p-2 rounded">Reg No,Date,Status,Notes
REG001,2025-08-14,present,
REG002,2025-08-14,absent,Medical leave
REG003,2025-08-14,late,Traffic delay</pre>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Import Guidelines</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>For Candidates Import:</h5>
                        <ul>
                            <li>First row should be headers</li>
                            <li>Name, Reg No, and Batch Name are required</li>
                            <li>Phone and Email are optional</li>
                            <li>Batch names must exist in the system</li>
                            <li>Existing candidates will be updated</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>For Attendance Import:</h5>
                        <ul>
                            <li>First row should be headers</li>
                            <li>Reg No, Date, and Status are required</li>
                            <li>Status must be: present, absent, late, or excused</li>
                            <li>Date format: YYYY-MM-DD</li>
                            <li>Existing records will be updated</li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong>Note:</strong> Make sure your CSV files use comma (,) as the delimiter and are saved in UTF-8 encoding for proper character handling.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-4">
    <a href="/export" class="btn btn-outline-primary me-2">Go to Export</a>
    <a href="/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
</div>

<?php $this->render('layout', ['content' => ob_get_clean()]); ?>
