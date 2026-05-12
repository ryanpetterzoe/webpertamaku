<?php
/**
 * AdminController - Admin dashboard
 */
class AdminController {

    public function dashboard() {
        requireLogin();
        $db = getDB();

        // Stats
        $stats = [];
        $queries = [
            'news' => "SELECT COUNT(*) as c FROM news",
            'gallery' => "SELECT COUNT(*) as c FROM gallery",
            'programs' => "SELECT COUNT(*) as c FROM programs WHERE is_active=1",
            'spmb_total' => "SELECT COUNT(*) as c FROM spmb_registrations",
            'spmb_pending' => "SELECT COUNT(*) as c FROM spmb_registrations WHERE status='pending'",
            'spmb_accepted' => "SELECT COUNT(*) as c FROM spmb_registrations WHERE status='diterima'",
            'spmb_rejected' => "SELECT COUNT(*) as c FROM spmb_registrations WHERE status='ditolak'",
            'unread_contacts' => "SELECT COUNT(*) as c FROM contacts WHERE is_read=0",
        ];
        foreach ($queries as $key => $sql) {
            $r = $db->query($sql);
            $stats[$key] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        }

        // Recent SPMB registrations (with program name)
        $res = $db->query("SELECT r.*, p.name as program_name FROM spmb_registrations r LEFT JOIN programs p ON r.program_id=p.id ORDER BY r.created_at DESC LIMIT 8");
        $recentSpmb = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Recent contacts
        $res = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
        $recentContacts = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        require_once __DIR__ . '/../../views/admin/dashboard.php';
    }
}
