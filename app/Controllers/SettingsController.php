<?php

namespace App\Controllers;

use App\Core\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        
        $settings = $this->db->fetchAll("SELECT * FROM settings");
        $settingsArray = [];
        
        foreach ($settings as $setting) {
            $settingsArray[$setting['key']] = $setting['value'];
        }
        
        $this->render('settings/index', ['settings' => $settingsArray]);
    }
    
    public function update()
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        
        try {
            foreach ($input as $key => $value) {
                if (strpos($key, 'setting_') === 0) {
                    $settingKey = substr($key, 8);
                    $sql = "INSERT INTO settings (skey, svalue, updated_at) VALUES (?, ?, datetime('now'))
                ON CONFLICT(skey) DO UPDATE SET svalue = ?, updated_at = datetime('now')";
                    $this->db->execute(
                        $sql,
                        [$settingKey, $value, $value]
                    );
                }
            }
            
            $this->setFlash('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error updating settings: ' . $e->getMessage());
        }
        
        $this->redirect('/settings');
    }
}
