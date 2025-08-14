<?php $this->render('layout', ['title' => 'Settings', 'content' => ob_start()]); ?>

<div class="page-header">
    <h1>Settings</h1>
    <p class="text-muted">Configure application settings</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Application Settings</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/settings/update">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_app_name" class="form-label">Application Name</label>
                                <input type="text" class="form-control" id="setting_app_name" name="setting_app_name" value="<?= htmlspecialchars($settings['app_name'] ?? 'BC Attendance System') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="setting_organization" class="form-label">Organization</label>
                                <input type="text" class="form-control" id="setting_organization" name="setting_organization" value="<?= htmlspecialchars($settings['organization'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="setting_timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="setting_timezone" name="setting_timezone">
                                    <option value="Asia/Kolkata" <?= ($settings['timezone'] ?? 'Asia/Kolkata') === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST)</option>
                                    <option value="Asia/Delhi" <?= ($settings['timezone'] ?? '') === 'Asia/Delhi' ? 'selected' : '' ?>>Asia/Delhi</option>
                                    <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_attendance_start_time" class="form-label">Attendance Start Time</label>
                                <input type="time" class="form-control" id="setting_attendance_start_time" name="setting_attendance_start_time" value="<?= htmlspecialchars($settings['attendance_start_time'] ?? '09:00') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="setting_late_threshold" class="form-label">Late Threshold (minutes)</label>
                                <input type="number" class="form-control" id="setting_late_threshold" name="setting_late_threshold" value="<?= htmlspecialchars($settings['late_threshold'] ?? '15') ?>" min="0" max="120">
                            </div>
                            
                            <div class="mb-3">
                                <label for="setting_default_attendance_status" class="form-label">Default Attendance Status</label>
                                <select class="form-select" id="setting_default_attendance_status" name="setting_default_attendance_status">
                                    <option value="present" <?= ($settings['default_attendance_status'] ?? 'absent') === 'present' ? 'selected' : '' ?>>Present</option>
                                    <option value="absent" <?= ($settings['default_attendance_status'] ?? 'absent') === 'absent' ? 'selected' : '' ?>>Absent</option>
                                    <option value="late" <?= ($settings['default_attendance_status'] ?? 'absent') === 'late' ? 'selected' : '' ?>>Late</option>
                                    <option value="excused" <?= ($settings['default_attendance_status'] ?? 'absent') === 'excused' ? 'selected' : '' ?>>Excused</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="setting_email_notifications" class="form-label">Email Notifications</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="setting_email_notifications" name="setting_email_notifications" value="1" <?= ($settings['email_notifications'] ?? '') === '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="setting_email_notifications">
                                        Enable email notifications for attendance reports
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="setting_auto_backup" class="form-label">Auto Backup</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="setting_auto_backup" name="setting_auto_backup" value="1" <?= ($settings['auto_backup'] ?? '') === '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="setting_auto_backup">
                                        Enable automatic daily backup
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
                        <a href="/dashboard" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>System Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
                        <p><strong>Database:</strong> MySQL</p>
                        <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Application Version:</strong> <?= $settings['app_version'] ?? '1.0.0' ?></p>
                        <p><strong>Last Updated:</strong> <?= date('d M Y H:i:s') ?></p>
                        <p><strong>Environment:</strong> <?= ($settings['debug'] ?? '') === '1' ? 'Development' : 'Production' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->render('layout', ['content' => ob_get_clean()]); ?>
