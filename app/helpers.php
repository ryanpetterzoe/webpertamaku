<?php

function redirect($url) {
    header("Location: " . APP_URL . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/admin/login');
    }
}

function clean($data) {
    $db = getDB();
    return $db->real_escape_string(htmlspecialchars(strip_tags(trim($data))));
}

function cleanRaw($data) {
    $db = getDB();
    return $db->real_escape_string(trim($data));
}

function uploadFile($file, $prefix = 'img') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) return '';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) return '';
    $filename = $prefix . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $dest = UPLOAD_PATH . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $filename;
    }
    return '';
}

function getSetting($key) {
    $db = getDB();
    $key = $db->real_escape_string($key);
    $res = $db->query("SELECT value FROM settings WHERE `key` = '$key' LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) return $row['value'];
    return '';
}

function getSettings() {
    $db = getDB();
    $res = $db->query("SELECT `key`, value FROM settings");
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[$row['key']] = $row['value'];
    }
    return $data;
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return $time . ' detik lalu';
    if ($time < 3600) return round($time/60) . ' menit lalu';
    if ($time < 86400) return round($time/3600) . ' jam lalu';
    if ($time < 604800) return round($time/86400) . ' hari lalu';
    return date('d M Y', strtotime($datetime));
}

function activeMenu($page, $current) {
    return $page === $current ? 'active' : '';
}

function formatDate($date) {
    $months = ['','Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];
    $d = date('j', strtotime($date));
    $m = $months[(int)date('n', strtotime($date))];
    $y = date('Y', strtotime($date));
    return "$d $m $y";
}

function paginate($total, $perPage, $currentPage, $url) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    $html = '<nav class="pagination-nav"><ul class="pagination">';
    if ($currentPage > 1) {
        $html .= '<li><a href="'.$url.'?page='.($currentPage-1).'">&laquo;</a></li>';
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $currentPage ? ' class="active"' : '';
        $html .= '<li'.$active.'><a href="'.$url.'?page='.$i.'">'.$i.'</a></li>';
    }
    if ($currentPage < $totalPages) {
        $html .= '<li><a href="'.$url.'?page='.($currentPage+1).'">&raquo;</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}
