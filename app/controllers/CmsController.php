<?php
/**
 * CmsController - Full CRUD for all CMS features
 */
class CmsController {

    private function flash($key, $msg) {
        $_SESSION[$key] = $msg;
    }

    // ===================== NEWS =====================
    public function newsList() {
        requireLogin();
        $db = getDB();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $q = $db->real_escape_string($_GET['q'] ?? '');
        $where = $q ? "WHERE title LIKE '%$q%'" : '';
        $total = (int)$db->query("SELECT COUNT(*) as c FROM news $where")->fetch_assoc()['c'];
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $res = $db->query("SELECT * FROM news $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
        $news = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $currentPage = $page;
        require_once __DIR__ . '/../../views/admin/news_list.php';
    }

    public function newsForm($id = null) {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $news = null;
        if ($id) {
            $res = $db->query("SELECT * FROM news WHERE id=" . (int)$id . " LIMIT 1");
            $news = $res ? $res->fetch_assoc() : null;
            if (!$news) redirect('/admin/berita');
        }
        require_once __DIR__ . '/../../views/admin/news_form.php';
    }

    public function newsSave($id = null) {
        requireLogin();
        $db = getDB();
        $title       = clean(isset($_POST['title'])    ? $_POST['title']    : '');
        $slug        = $db->real_escape_string(preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', trim(isset($_POST['slug']) ? $_POST['slug'] : $title)))));
        $excerpt     = clean(isset($_POST['excerpt'])  ? $_POST['excerpt']  : '');
        $content     = $db->real_escape_string(isset($_POST['content'])  ? $_POST['content']  : '');
        $category    = clean(isset($_POST['category']) ? $_POST['category'] : 'Berita');
        $author      = clean(isset($_POST['author'])   ? $_POST['author']   : (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'));
        $programId   = !empty($_POST['program_id']) ? (int)$_POST['program_id'] : 'NULL';
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if (empty($title)) {
            $this->flash('flash_error', 'Judul berita wajib diisi.');
            redirect($id ? '/admin/berita/edit/' . $id : '/admin/berita/tambah');
        }

        $imageVal = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $imageVal = uploadFile($_FILES['image'], 'news');
        }

        if ($id) {
            $imgSql = $imageVal ? ", image='$imageVal'" : '';
            $db->query("UPDATE news SET title='$title',slug='$slug',excerpt='$excerpt',content='$content',category='$category',author='$author',program_id=$programId,is_published=$isPublished$imgSql WHERE id=" . (int)$id);
            $this->flash('flash_success', 'Berita berhasil diperbarui.');
        } else {
            $db->query("INSERT INTO news (title,slug,excerpt,content,category,author,image,program_id,is_published) VALUES ('$title','$slug','$excerpt','$content','$category','$author','$imageVal',$programId,$isPublished)");
            $this->flash('flash_success', 'Berita berhasil ditambahkan.');
        }
        redirect('/admin/berita');
    }

    public function newsToggle($id) {
        requireLogin();
        $db = getDB();
        $db->query("UPDATE news SET is_published = NOT is_published WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Status berita berhasil diubah.');
        redirect('/admin/berita');
    }

    public function newsDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM news WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Berita berhasil dihapus.');
        redirect('/admin/berita');
    }

    // ===================== GALLERY =====================
    public function galleryList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM gallery ORDER BY created_at DESC");
        $gallery = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/gallery_list.php';
    }

    public function galleryForm($id = null) {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $gallery = null;
        if ($id) {
            $res = $db->query("SELECT * FROM gallery WHERE id=" . (int)$id . " LIMIT 1");
            $gallery = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/gallery_form.php';
    }

    public function gallerySave($id = null) {
        requireLogin();
        $db = getDB();
        $title = clean($_POST['title'] ?? '');
        $category = clean($_POST['category'] ?? 'Umum');
        $description = clean($_POST['description'] ?? '');
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        $imageVal = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $imageVal = uploadFile($_FILES['image'], 'gallery');
        }

        if ($id) {
            $imgSql = $imageVal ? ", image='$imageVal'" : '';
            $db->query("UPDATE gallery SET title='$title',category='$category',description='$description',is_published=$isPublished$imgSql WHERE id=" . (int)$id);
        } else {
            if (empty($imageVal)) { $this->flash('flash_error', 'Gambar wajib diupload.'); redirect('/admin/galeri/tambah'); }
            $db->query("INSERT INTO gallery (title,category,description,image,is_published) VALUES ('$title','$category','$description','$imageVal',$isPublished)");
        }
        $this->flash('flash_success', 'Foto galeri berhasil disimpan.');
        redirect('/admin/galeri');
    }

    public function galleryDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM gallery WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Foto berhasil dihapus.');
        redirect('/admin/galeri');
    }

    // ===================== SLIDERS =====================
    public function sliderList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM sliders ORDER BY sort_order ASC");
        $sliders = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/sliders_list.php';
    }

    public function sliderForm($id = null) {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $slider = null;
        if ($id) {
            $res = $db->query("SELECT * FROM sliders WHERE id=" . (int)$id . " LIMIT 1");
            $slider = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/sliders_form.php';
    }

    public function sliderSave($id = null) {
        requireLogin();
        $db = getDB();
        $title = clean($_POST['title'] ?? '');
        $subtitle = clean($_POST['subtitle'] ?? '');
        $btnText = clean($_POST['button_text'] ?? '');
        $btnUrl = clean($_POST['button_url'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 1);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $imageVal = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $imageVal = uploadFile($_FILES['image'], 'slider');
        }
        if ($id) {
            $imgSql = $imageVal ? ", image='$imageVal'" : '';
            $db->query("UPDATE sliders SET title='$title',subtitle='$subtitle',button_text='$btnText',button_url='$btnUrl',sort_order=$sortOrder,is_active=$isActive$imgSql WHERE id=" . (int)$id);
        } else {
            if (empty($imageVal)) { $this->flash('flash_error', 'Gambar slider wajib diupload.'); redirect('/admin/slider/tambah'); }
            $db->query("INSERT INTO sliders (title,subtitle,image,button_text,button_url,sort_order,is_active) VALUES ('$title','$subtitle','$imageVal','$btnText','$btnUrl',$sortOrder,$isActive)");
        }
        $this->flash('flash_success', 'Slider berhasil disimpan.');
        redirect('/admin/slider');
    }

    public function sliderDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM sliders WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Slider berhasil dihapus.');
        redirect('/admin/slider');
    }

    // ===================== PROGRAMS =====================
    public function programsList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM programs ORDER BY sort_order ASC");
        $programs = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/programs_list.php';
    }

    public function programForm($id = null) {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $program = null;
        if ($id) {
            $res = $db->query("SELECT * FROM programs WHERE id=" . (int)$id . " LIMIT 1");
            $program = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/programs_form.php';
    }

    public function programSave($id = null) {
        requireLogin();
        $db = getDB();
        $name = clean($_POST['name'] ?? '');
        $code = clean($_POST['code'] ?? '');
        $description = $db->real_escape_string($_POST['description'] ?? '');
        $icon = clean($_POST['icon'] ?? 'fas fa-book');
        $quota = (int)($_POST['quota'] ?? 36);
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $imageVal = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $imageVal = uploadFile($_FILES['image'], 'program');
        }
        if ($id) {
            $imgSql = $imageVal ? ", image='$imageVal'" : '';
            $db->query("UPDATE programs SET name='$name',code='$code',description='$description',icon='$icon',quota=$quota,sort_order=$sortOrder,is_active=$isActive$imgSql WHERE id=" . (int)$id);
        } else {
            $db->query("INSERT INTO programs (name,code,description,icon,quota,sort_order,is_active,image) VALUES ('$name','$code','$description','$icon',$quota,$sortOrder,$isActive,'$imageVal')");
        }
        $this->flash('flash_success', 'Jurusan berhasil disimpan.');
        redirect('/admin/jurusan');
    }

    public function programDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM programs WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Jurusan berhasil dihapus.');
        redirect('/admin/jurusan');
    }

    // ===================== TEACHERS =====================
    public function teachersList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM teachers ORDER BY sort_order ASC");
        $teachers = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/teachers_list.php';
    }

    public function teacherForm($id = null) {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $teacher = null;
        if ($id) {
            $res = $db->query("SELECT * FROM teachers WHERE id=" . (int)$id . " LIMIT 1");
            $teacher = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/teachers_form.php';
    }

    public function teacherSave($id = null) {
        requireLogin();
        $db = getDB();
        $fields = ['name','nip','position','subject','education','email','phone'];
        $vals = [];
        foreach ($fields as $f) $vals[$f] = "'" . clean($_POST[$f] ?? '') . "'";
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $photoVal = '';
        if (!empty($_FILES['photo']['tmp_name'])) {
            $photoVal = uploadFile($_FILES['photo'], 'teacher');
        }
        if ($id) {
            $photoSql = $photoVal ? ", photo='$photoVal'" : '';
            $db->query("UPDATE teachers SET name={$vals['name']},nip={$vals['nip']},position={$vals['position']},subject={$vals['subject']},education={$vals['education']},email={$vals['email']},phone={$vals['phone']},sort_order=$sortOrder,is_active=$isActive$photoSql WHERE id=" . (int)$id);
        } else {
            $db->query("INSERT INTO teachers (name,nip,position,subject,education,email,phone,photo,sort_order,is_active) VALUES ({$vals['name']},{$vals['nip']},{$vals['position']},{$vals['subject']},{$vals['education']},{$vals['email']},{$vals['phone']},'$photoVal',$sortOrder,$isActive)");
        }
        $this->flash('flash_success', 'Data guru berhasil disimpan.');
        redirect('/admin/guru');
    }

    public function teacherDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM teachers WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Data guru berhasil dihapus.');
        redirect('/admin/guru');
    }

    // ===================== CONTACTS =====================
    public function contactsList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM contacts ORDER BY is_read ASC, created_at DESC");
        $contacts = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $unreadRes = $db->query("SELECT COUNT(*) as c FROM contacts WHERE is_read=0");
        $unreadCount = $unreadRes ? (int)$unreadRes->fetch_assoc()['c'] : 0;
        require_once __DIR__ . '/../../views/admin/contacts_list.php';
    }

    public function contactRead($id) {
        requireLogin();
        $db = getDB();
        $db->query("UPDATE contacts SET is_read=1 WHERE id=" . (int)$id);
        // AJAX friendly
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => true]);
            exit;
        }
        redirect('/admin/kontak');
    }

    public function contactDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM contacts WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Pesan berhasil dihapus.');
        redirect('/admin/kontak');
    }

    // ===================== SPMB REGISTRATIONS =====================
    public function spmbList() {
        requireLogin();
        $db = getDB();
        $status = $db->real_escape_string($_GET['status'] ?? '');
        $programFilter = (int)($_GET['program'] ?? 0);
        $q = $db->real_escape_string($_GET['q'] ?? '');

        $where = "1=1";
        if ($status) $where .= " AND r.status='$status'";
        if ($programFilter) $where .= " AND r.program_id=$programFilter";
        if ($q) $where .= " AND r.full_name LIKE '%$q%'";

        $res = $db->query("SELECT r.*, p.name as program_name FROM spmb_registrations r LEFT JOIN programs p ON r.program_id=p.id WHERE $where ORDER BY r.created_at DESC");
        $registrations = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Status counts
        $statusCount = [];
        foreach (['pending','verifikasi','diterima','ditolak'] as $st) {
            $r = $db->query("SELECT COUNT(*) as c FROM spmb_registrations WHERE status='$st'");
            $statusCount[$st] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        }

        $resP = $db->query("SELECT id,name FROM programs WHERE is_active=1 ORDER BY sort_order");
        $programs = $resP ? $resP->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/spmb_list.php';
    }

    public function spmbDetail($id) {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $res = $db->query("SELECT r.*, p.name as program_name, p2.name as program_choice2_name FROM spmb_registrations r LEFT JOIN programs p ON r.program_id=p.id LEFT JOIN programs p2 ON r.program_choice2=p2.id WHERE r.id=" . (int)$id . " LIMIT 1");
        if (!$res || !($reg = $res->fetch_assoc())) {
            $this->flash('flash_error', 'Data tidak ditemukan.');
            redirect('/admin/spmb/pendaftar');
        }
        require_once __DIR__ . '/../../views/admin/spmb_detail.php';
    }

    public function spmbUpdateStatus($id) {
        requireLogin();
        $db = getDB();
        $status = $db->real_escape_string($_POST['status'] ?? 'pending');
        $notes = $db->real_escape_string($_POST['notes'] ?? '');
        $adminId = (int)($_SESSION['admin_id'] ?? 0);
        $db->query("UPDATE spmb_registrations SET status='$status',notes='$notes',verified_by=$adminId,verified_at=NOW() WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Status pendaftar berhasil diperbarui.');
        redirect('/admin/spmb/pendaftar');
    }

    public function spmbDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM spmb_registrations WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Data pendaftar berhasil dihapus.');
        redirect('/admin/spmb/pendaftar');
    }

    public function spmbExport() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT r.*, p.name as program_name FROM spmb_registrations r LEFT JOIN programs p ON r.program_id=p.id ORDER BY r.created_at DESC");
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="spmb_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        fputcsv($out, ['No Daftar','Nama Lengkap','Gender','TTL','Asal Sekolah','NISN','Jurusan','Nama Ayah','Nama Ibu','Status','Tgl Daftar']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['registration_number'], $r['full_name'], $r['gender'],
                $r['birth_place'] . ', ' . $r['birth_date'], $r['school_origin'],
                $r['nisn'], $r['program_name'] ?? '-',
                $r['father_name'] ?? '-', $r['mother_name'] ?? '-',
                $r['status'], $r['created_at']
            ]);
        }
        fclose($out);
        exit;
    }

    // ===================== SPMB SETTINGS =====================
    public function spmbSettings() {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $res = $db->query("SELECT * FROM spmb_settings ORDER BY id DESC LIMIT 1");
        $spmb = $res ? ($res->fetch_assoc() ?: []) : [];
        require_once __DIR__ . '/../../views/admin/spmb_settings.php';
    }

    public function spmbSettingsSave() {
        requireLogin();
        $db = getDB();
        $ay = clean($_POST['academic_year'] ?? '');
        $open = $db->real_escape_string($_POST['open_date'] ?? '');
        $close = $db->real_escape_string($_POST['close_date'] ?? '');
        $ann = !empty($_POST['announcement_date']) ? "'" . $db->real_escape_string($_POST['announcement_date']) . "'" : 'NULL';
        $quota = (int)($_POST['quota_total'] ?? 144);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $info = $db->real_escape_string($_POST['info'] ?? '');
        $reqs = $db->real_escape_string($_POST['requirements'] ?? '');
        $id = (int)($_POST['id'] ?? 0);

        if ($id) {
            $db->query("UPDATE spmb_settings SET academic_year='$ay',open_date='$open',close_date='$close',announcement_date=$ann,quota_total=$quota,is_active=$isActive,info='$info',requirements='$reqs' WHERE id=$id");
        } else {
            $db->query("INSERT INTO spmb_settings (academic_year,open_date,close_date,announcement_date,quota_total,is_active,info,requirements) VALUES ('$ay','$open','$close',$ann,$quota,$isActive,'$info','$reqs')");
        }
        $this->flash('flash_success', 'Pengaturan SPMB berhasil disimpan.');
        redirect('/admin/spmb/pengaturan');
    }

    // ===================== SETTINGS =====================
    public function settingsGeneral() {
        requireLogin();
        $db = getDB();
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $group = $_POST['group'] ?? 'general';
            // Handle file uploads
            if (!empty($_FILES['school_logo_file']['tmp_name'])) {
                $logo = uploadFile($_FILES['school_logo_file'], 'logo');
                if ($logo) $this->saveSetting('school_logo', $logo, 'general');
            }
            if (!empty($_FILES['school_building_photo_file']['tmp_name'])) {
                $bldg = uploadFile($_FILES['school_building_photo_file'], 'building');
                if ($bldg) $this->saveSetting('school_building_photo', $bldg, 'general');
            }
            if (!empty($_FILES['principal_photo_file']['tmp_name'])) {
                $photo = uploadFile($_FILES['principal_photo_file'], 'principal');
                if ($photo) $this->saveSetting('principal_photo', $photo, 'about');
            }
            // Save all text settings
            $exclude = ['_token','group','school_logo_file','principal_photo_file'];
            foreach ($_POST as $key => $val) {
                if (in_array($key, $exclude)) continue;
                $this->saveSetting($key, $val, $group);
            }
            $this->flash('flash_success', 'Pengaturan berhasil disimpan.');
            redirect('/admin/settings/umum');
        }

        $settings = getSettings();
        require_once __DIR__ . '/../../views/admin/settings_general.php';
    }

    // ===================== ACHIEVEMENTS / PRESTASI =====================
    public function achievementsList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM achievements ORDER BY year DESC, id DESC");
        $achievements = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/achievements_list.php';
    }
    public function achievementForm($id = null) {
        requireLogin();
        $db = getDB();
        $achievement = null;
        if ($id) {
            $res = $db->query("SELECT * FROM achievements WHERE id=" . (int)$id . " LIMIT 1");
            $achievement = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/achievements_form.php';
    }
    public function achievementSave($id = null) {
        requireLogin();
        $db = getDB();
        $title = clean(isset($_POST['title']) ? $_POST['title'] : '');
        $desc  = $db->real_escape_string(isset($_POST['description']) ? $_POST['description'] : '');
        $level = clean(isset($_POST['level']) ? $_POST['level'] : 'sekolah');
        $year  = (int)(isset($_POST['year']) ? $_POST['year'] : date('Y'));
        $pub   = isset($_POST['is_published']) ? 1 : 0;
        $imgVal = '';
        if (!empty($_FILES['image']['tmp_name'])) $imgVal = uploadFile($_FILES['image'], 'ach');
        if ($id) {
            $img = $imgVal ? ", image='$imgVal'" : '';
            $db->query("UPDATE achievements SET title='$title',description='$desc',level='$level',year=$year,is_published=$pub$img WHERE id=" . (int)$id);
        } else {
            $db->query("INSERT INTO achievements (title,description,image,level,year,is_published) VALUES ('$title','$desc','$imgVal','$level',$year,$pub)");
        }
        $this->flash('flash_success', 'Prestasi berhasil disimpan.');
        redirect('/admin/prestasi');
    }
    public function achievementDelete($id) {
        requireLogin();
        getDB()->query("DELETE FROM achievements WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Prestasi berhasil dihapus.');
        redirect('/admin/prestasi');
    }

    // ===================== TESTIMONIALS =====================
    public function testimonialsList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM testimonials ORDER BY id DESC");
        $testimonials = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/testimonials_list.php';
    }
    public function testimonialForm($id = null) {
        requireLogin();
        $db = getDB();
        $testimonial = null;
        if ($id) {
            $res = $db->query("SELECT * FROM testimonials WHERE id=" . (int)$id . " LIMIT 1");
            $testimonial = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/testimonials_form.php';
    }
    public function testimonialSave($id = null) {
        requireLogin();
        $db = getDB();
        $name    = clean(isset($_POST['name']) ? $_POST['name'] : '');
        $pos     = clean(isset($_POST['position']) ? $_POST['position'] : '');
        $content = $db->real_escape_string(isset($_POST['content']) ? $_POST['content'] : '');
        $rating  = (int)(isset($_POST['rating']) ? $_POST['rating'] : 5);
        $pub     = isset($_POST['is_published']) ? 1 : 0;
        $photoVal = '';
        if (!empty($_FILES['photo']['tmp_name'])) $photoVal = uploadFile($_FILES['photo'], 'testi');
        if ($id) {
            $ph = $photoVal ? ", photo='$photoVal'" : '';
            $db->query("UPDATE testimonials SET name='$name',position='$pos',content='$content',rating=$rating,is_published=$pub$ph WHERE id=" . (int)$id);
        } else {
            $db->query("INSERT INTO testimonials (name,position,content,photo,rating,is_published) VALUES ('$name','$pos','$content','$photoVal',$rating,$pub)");
        }
        $this->flash('flash_success', 'Testimoni berhasil disimpan.');
        redirect('/admin/testimonial');
    }
    public function testimonialDelete($id) {
        requireLogin();
        getDB()->query("DELETE FROM testimonials WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Testimoni berhasil dihapus.');
        redirect('/admin/testimonial');
    }

    // ===================== AGENDA =====================
    public function agendaList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM agenda ORDER BY start_date DESC");
        $agendas = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/agenda_list.php';
    }
    public function agendaForm($id = null) {
        requireLogin();
        $db = getDB();
        $agenda = null;
        if ($id) {
            $res = $db->query("SELECT * FROM agenda WHERE id=" . (int)$id . " LIMIT 1");
            $agenda = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/agenda_form.php';
    }
    public function agendaSave($id = null) {
        requireLogin();
        $db = getDB();
        $title    = clean(isset($_POST['title']) ? $_POST['title'] : '');
        $desc     = $db->real_escape_string(isset($_POST['description']) ? $_POST['description'] : '');
        $location = clean(isset($_POST['location']) ? $_POST['location'] : '');
        $start    = $db->real_escape_string(isset($_POST['start_date']) ? $_POST['start_date'] : '');
        $end      = !empty($_POST['end_date']) ? "'" . $db->real_escape_string($_POST['end_date']) . "'" : 'NULL';
        $pub      = isset($_POST['is_published']) ? 1 : 0;
        if ($id) {
            $db->query("UPDATE agenda SET title='$title',description='$desc',location='$location',start_date='$start',end_date=$end,is_published=$pub WHERE id=" . (int)$id);
        } else {
            $db->query("INSERT INTO agenda (title,description,location,start_date,end_date,is_published) VALUES ('$title','$desc','$location','$start',$end,$pub)");
        }
        $this->flash('flash_success', 'Agenda berhasil disimpan.');
        redirect('/admin/agenda');
    }
    public function agendaDelete($id) {
        requireLogin();
        getDB()->query("DELETE FROM agenda WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Agenda berhasil dihapus.');
        redirect('/admin/agenda');
    }

    // ===================== STAFF =====================
    public function staffList() {
        requireLogin();
        $db = getDB();
        $res = $db->query("SELECT * FROM staff ORDER BY sort_order ASC");
        $staff = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/admin/staff_list.php';
    }
    public function staffForm($id = null) {
        requireLogin();
        $db = getDB();
        $staff = null;
        if ($id) {
            $res = $db->query("SELECT * FROM staff WHERE id=" . (int)$id . " LIMIT 1");
            $staff = $res ? $res->fetch_assoc() : null;
        }
        require_once __DIR__ . '/../../views/admin/staff_form.php';
    }
    public function staffSave($id = null) {
        requireLogin();
        $db = getDB();
        $flds = ['name','nip','position','department','email','phone'];
        $v = [];
        foreach ($flds as $f) $v[$f] = "'" . clean(isset($_POST[$f]) ? $_POST[$f] : '') . "'";
        $sort = (int)(isset($_POST['sort_order']) ? $_POST['sort_order'] : 0);
        $pub  = isset($_POST['is_active']) ? 1 : 0;
        $ph   = '';
        if (!empty($_FILES['photo']['tmp_name'])) $ph = uploadFile($_FILES['photo'], 'staff');
        if ($id) {
            $phs = $ph ? ", photo='$ph'" : '';
            $db->query("UPDATE staff SET name={$v['name']},nip={$v['nip']},position={$v['position']},department={$v['department']},email={$v['email']},phone={$v['phone']},sort_order=$sort,is_active=$pub$phs WHERE id=" . (int)$id);
        } else {
            $db->query("INSERT INTO staff (name,nip,position,department,email,phone,photo,sort_order,is_active) VALUES ({$v['name']},{$v['nip']},{$v['position']},{$v['department']},{$v['email']},{$v['phone']},'$ph',$sort,$pub)");
        }
        $this->flash('flash_success', 'Data staff berhasil disimpan.');
        redirect('/admin/staff');
    }
    public function staffDelete($id) {
        requireLogin();
        getDB()->query("DELETE FROM staff WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Staff berhasil dihapus.');
        redirect('/admin/staff');
    }

    // ===================== SETTINGS TAMPILAN (ACCENT COLOR) =====================
    public function settingsTampilan() {
        requireLogin();
        $db = getDB();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keys = ['accent_primary','accent_dark','theme_default'];
            foreach ($keys as $k) {
                if (isset($_POST[$k])) $this->saveSetting($k, $_POST[$k], 'appearance');
            }
            $this->flash('flash_success', 'Pengaturan tampilan berhasil disimpan.');
            redirect('/admin/settings/tampilan');
        }
        $settings = getSettings();
        require_once __DIR__ . '/../../views/admin/settings_tampilan.php';
    }

    // ===================== SOCIAL MEDIA =====================
    public function socialMediaSave($id = null) {
        requireLogin();
        $db = getDB();
        $platform = clean($_POST['platform'] ?? '');
        $url      = $db->real_escape_string($_POST['url'] ?? '');
        $icon     = clean($_POST['icon'] ?? 'fas fa-link');
        $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        if (empty($platform) || empty($url)) {
            $this->flash('flash_error', 'Platform dan URL wajib diisi.');
            redirect('/admin/settings/umum#social');
        }

        if ($id) {
            $db->query("UPDATE social_media SET platform='$platform', url='$url', icon='$icon', is_active=$isActive WHERE id=" . (int)$id);
            $this->flash('flash_success', 'Sosial media berhasil diperbarui.');
        } else {
            $db->query("INSERT INTO social_media (platform, url, icon, is_active) VALUES ('$platform', '$url', '$icon', $isActive)");
            $this->flash('flash_success', 'Sosial media berhasil ditambahkan.');
        }
        redirect('/admin/settings/umum#social');
    }

    public function socialMediaToggle($id) {
        requireLogin();
        $db = getDB();
        $db->query("UPDATE social_media SET is_active = NOT is_active WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Status sosial media berhasil diubah.');
        redirect('/admin/settings/umum#social');
    }

    public function socialMediaDelete($id) {
        requireLogin();
        $db = getDB();
        $db->query("DELETE FROM social_media WHERE id=" . (int)$id);
        $this->flash('flash_success', 'Sosial media berhasil dihapus.');
        redirect('/admin/settings/umum#social');
    }

    private function saveSetting($key, $value, $group = 'general') {
        $db = getDB();
        $k = $db->real_escape_string($key);
        $v = $db->real_escape_string($value);
        $g = $db->real_escape_string($group);
        $db->query("INSERT INTO settings (`key`,`value`,`group`) VALUES ('$k','$v','$g') ON DUPLICATE KEY UPDATE `value`='$v'");
    }
}
