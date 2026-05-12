<?php
/**
 * PublicController - handles all public-facing pages
 */
class PublicController {

    public function home() {
        $db = getDB();
        $settings = getSettings();

        // Sliders
        $res = $db->query("SELECT * FROM sliders WHERE is_active=1 ORDER BY sort_order ASC LIMIT 5");
        $sliders = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        if (empty($sliders)) {
            $sliders = [['title' => $settings['school_name'] ?? 'SMK Pertamaku', 'subtitle' => $settings['school_tagline'] ?? '', 'image' => '', 'button_text' => 'Daftar Sekarang', 'button_url' => '/spmb']];
        }

        // Programs
        $res = $db->query("SELECT * FROM programs WHERE is_active=1 ORDER BY sort_order ASC LIMIT 4");
        $programs = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // News
        $res = $db->query("SELECT * FROM news WHERE is_published=1 ORDER BY published_at DESC LIMIT 3");
        $news = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Achievements
        $res = $db->query("SELECT * FROM achievements WHERE is_published=1 ORDER BY year DESC LIMIT 4");
        $achievements = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Testimonials
        $res = $db->query("SELECT * FROM testimonials WHERE is_published=1 ORDER BY id DESC LIMIT 6");
        $testimonials = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Agenda (upcoming)
        $res = $db->query("SELECT * FROM agenda WHERE is_published=1 AND start_date >= CURDATE() ORDER BY start_date ASC LIMIT 5");
        $agenda = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        require_once __DIR__ . '/../../views/public/home.php';
    }

    public function about() {
        $settings = getSettings();
        require_once __DIR__ . '/../../views/public/about.php';
    }

    public function programs() {
        $db = getDB();
        $settings = getSettings();
        $res = $db->query("SELECT * FROM programs WHERE is_active=1 ORDER BY sort_order ASC");
        $programs = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/public/programs.php';
    }

    public function programDetail($id) {
        $db = getDB();
        $settings = getSettings();
        $id = (int)$id;
        $res = $db->query("SELECT * FROM programs WHERE id=$id AND is_active=1 LIMIT 1");
        if (!$res || !($program = $res->fetch_assoc())) {
            redirect('/jurusan');
        }
        // Related news (simple: last 4 news)
        $rn = $db->query("SELECT id,title,slug,published_at FROM news WHERE is_published=1 ORDER BY published_at DESC LIMIT 4");
        $relatedNews = $rn ? $rn->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/public/program_detail.php';
    }

    public function teachers() {
        $db = getDB();
        $settings = getSettings();
        $res = $db->query("SELECT * FROM teachers WHERE is_active=1 ORDER BY sort_order ASC");
        $teachers = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $resS = $db->query("SELECT * FROM staff WHERE is_active=1 ORDER BY sort_order ASC");
        $staff = $resS ? $resS->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/public/teachers.php';
    }

    public function news($page = 1) {
        $db = getDB();
        $settings = getSettings();
        $page = max(1, (int)$page);
        $perPage = 6;
        $search = $db->real_escape_string($_GET['q'] ?? '');
        $category = $db->real_escape_string($_GET['category'] ?? '');

        $where = "is_published=1";
        if ($search) $where .= " AND (title LIKE '%$search%' OR excerpt LIKE '%$search%')";
        if ($category) $where .= " AND category='$category'";

        $totalRes = $db->query("SELECT COUNT(*) as cnt FROM news WHERE $where");
        $total = $totalRes ? (int)$totalRes->fetch_assoc()['cnt'] : 0;
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $res = $db->query("SELECT * FROM news WHERE $where ORDER BY published_at DESC LIMIT $perPage OFFSET $offset");
        $news = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Categories
        $catRes = $db->query("SELECT DISTINCT category FROM news WHERE is_published=1 ORDER BY category");
        $categories = [];
        if ($catRes) { while ($r = $catRes->fetch_assoc()) $categories[] = $r['category']; }

        $currentPage = $page;
        require_once __DIR__ . '/../../views/public/news.php';
    }

    public function newsDetail($slug) {
        $db = getDB();
        $settings = getSettings();
        $slug = $db->real_escape_string($slug);
        $res = $db->query("SELECT * FROM news WHERE slug='$slug' AND is_published=1 LIMIT 1");
        if (!$res || !($news = $res->fetch_assoc())) {
            redirect('/berita');
        }
        // Increment views
        $db->query("UPDATE news SET views=views+1 WHERE id=" . (int)$news['id']);
        require_once __DIR__ . '/../../views/public/news_detail.php';
    }

    public function gallery($page = 1) {
        $db = getDB();
        $settings = getSettings();
        $page = max(1, (int)$page);
        $perPage = 12;
        $category = $db->real_escape_string($_GET['category'] ?? '');

        $where = "is_published=1";
        if ($category) $where .= " AND category='$category'";

        $totalRes = $db->query("SELECT COUNT(*) as cnt FROM gallery WHERE $where");
        $total = $totalRes ? (int)$totalRes->fetch_assoc()['cnt'] : 0;
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $res = $db->query("SELECT * FROM gallery WHERE $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
        $gallery = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        // Categories
        $catRes = $db->query("SELECT DISTINCT category FROM gallery WHERE is_published=1 ORDER BY category");
        $categories = [];
        if ($catRes) { while ($r = $catRes->fetch_assoc()) $categories[] = $r['category']; }

        $currentPage = $page;
        require_once __DIR__ . '/../../views/public/gallery.php';
    }

    public function achievements() {
        $db = getDB();
        $settings = getSettings();
        $level = $db->real_escape_string(isset($_GET['level']) ? $_GET['level'] : '');
        $where = "is_published=1";
        if ($level) $where .= " AND level='$level'";
        $res = $db->query("SELECT * FROM achievements WHERE $where ORDER BY year DESC, id DESC");
        $achievements = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/public/achievements.php';
    }

    public function testimonials() {
        $db = getDB();
        $settings = getSettings();
        $res = $db->query("SELECT * FROM testimonials WHERE is_published=1 ORDER BY id DESC");
        $testimonials = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/public/testimonials.php';
    }

    public function agenda() {
        $db = getDB();
        $settings = getSettings();
        $res = $db->query("SELECT * FROM agenda WHERE is_published=1 ORDER BY start_date ASC");
        $agendas = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/public/agenda.php';
    }

    public function contact() {
        $settings = getSettings();
        $success = $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = getDB();
            $name = clean($_POST['name'] ?? '');
            $email = clean($_POST['email'] ?? '');
            $phone = clean($_POST['phone'] ?? '');
            $subject = clean($_POST['subject'] ?? '');
            $message = cleanRaw($_POST['message'] ?? '');

            if (empty($name) || empty($email) || empty($message)) {
                $error = 'Nama, email, dan pesan wajib diisi.';
            } elseif (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid.';
            } else {
                $db->query("INSERT INTO contacts (name,email,phone,subject,message) VALUES ('$name','$email','$phone','$subject','$message')");
                $success = 'Pesan Anda telah berhasil terkirim. Kami akan merespons secepatnya.';
            }
        }

        require_once __DIR__ . '/../../views/public/contact.php';
    }

    public function spmbInfo() {
        $db = getDB();
        $settings = getSettings();
        $res = $db->query("SELECT * FROM spmb_settings ORDER BY id DESC LIMIT 1");
        $spmbSettings = $res ? ($res->fetch_assoc() ?: []) : [];
        $resP = $db->query("SELECT * FROM programs WHERE is_active=1 ORDER BY sort_order");
        $programs = $resP ? $resP->fetch_all(MYSQLI_ASSOC) : [];
        require_once __DIR__ . '/../../views/public/spmb.php';
    }

    public function spmbForm() {
        $db = getDB();
        $settings = getSettings();
        $resP = $db->query("SELECT * FROM programs WHERE is_active=1 ORDER BY sort_order");
        $programs = $resP ? $resP->fetch_all(MYSQLI_ASSOC) : [];
        $resSP = $db->query("SELECT * FROM spmb_settings WHERE is_active=1 ORDER BY id DESC LIMIT 1");
        $spmbSettings = $resSP ? ($resSP->fetch_assoc() ?: ['academic_year' => date('Y').'/'.(date('Y')+1)]) : ['academic_year' => date('Y').'/'.(date('Y')+1)];

        $error = '';
        $success = false;
        $regNumber = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate required
            $required = ['full_name','gender','birth_place','birth_date','religion','address','school_origin','nisn','program_id','father_name','mother_name'];
            $missing = [];
            foreach ($required as $f) {
                if (empty($_POST[$f])) $missing[] = $f;
            }

            if (!empty($missing)) {
                $error = 'Mohon lengkapi semua field yang wajib diisi.';
            } else {
                // Generate registration number
                $year = date('Y');
                $countRes = $db->query("SELECT COUNT(*) as cnt FROM spmb_registrations WHERE academic_year='" . $db->real_escape_string($spmbSettings['academic_year']) . "'");
                $count = $countRes ? (int)$countRes->fetch_assoc()['cnt'] : 0;
                $regNumber = 'REG-' . $year . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                // Handle file uploads
                $photo = !empty($_FILES['photo']['tmp_name']) ? uploadFile($_FILES['photo'], 'spmb_photo') : '';
                $docKk = !empty($_FILES['doc_kk']['tmp_name']) ? uploadFile($_FILES['doc_kk'], 'kk') : '';
                $docAkta = !empty($_FILES['doc_akta']['tmp_name']) ? uploadFile($_FILES['doc_akta'], 'akta') : '';
                $docIjazah = !empty($_FILES['doc_ijazah']['tmp_name']) ? uploadFile($_FILES['doc_ijazah'], 'ijazah') : '';
                $docRaport = !empty($_FILES['doc_raport']['tmp_name']) ? uploadFile($_FILES['doc_raport'], 'raport') : '';

                // Sanitize fields
                $fields = [];
                $textFields = ['full_name','nick_name','gender','birth_place','religion','address','phone','email','school_origin','nisn','father_name','father_job','father_phone','mother_name','mother_job','guardian_name','guardian_phone','parent_income'];
                foreach ($textFields as $f) {
                    $fields[$f] = "'" . clean($_POST[$f] ?? '') . "'";
                }
                $fields['birth_date'] = "'" . $db->real_escape_string($_POST['birth_date'] ?? '') . "'";
                $fields['program_id'] = !empty($_POST['program_id']) ? (int)$_POST['program_id'] : 'NULL';
                $fields['program_choice2'] = !empty($_POST['program_choice2']) ? (int)$_POST['program_choice2'] : 'NULL';
                $fields['un_score'] = !empty($_POST['un_score']) ? (float)$_POST['un_score'] : 'NULL';
                $acYear = "'" . $db->real_escape_string($spmbSettings['academic_year']) . "'";

                $sql = "INSERT INTO spmb_registrations (registration_number,academic_year,full_name,nick_name,gender,birth_place,birth_date,religion,address,phone,email,school_origin,nisn,un_score,program_id,program_choice2,father_name,father_job,father_phone,mother_name,mother_job,guardian_name,guardian_phone,parent_income,photo,doc_kk,doc_akta,doc_ijazah,doc_raport,status)
                        VALUES ('" . $db->real_escape_string($regNumber) . "',$acYear,{$fields['full_name']},{$fields['nick_name']},{$fields['gender']},{$fields['birth_place']},{$fields['birth_date']},{$fields['religion']},{$fields['address']},{$fields['phone']},{$fields['email']},{$fields['school_origin']},{$fields['nisn']},{$fields['un_score']},{$fields['program_id']},{$fields['program_choice2']},{$fields['father_name']},{$fields['father_job']},{$fields['father_phone']},{$fields['mother_name']},{$fields['mother_job']},{$fields['guardian_name']},{$fields['guardian_phone']},{$fields['parent_income']},'" . $db->real_escape_string($photo) . "','" . $db->real_escape_string($docKk) . "','" . $db->real_escape_string($docAkta) . "','" . $db->real_escape_string($docIjazah) . "','" . $db->real_escape_string($docRaport) . "','pending')";

                if ($db->query($sql)) {
                    $success = true;
                } else {
                    $error = 'Gagal menyimpan pendaftaran. Silakan coba lagi.';
                }
            }
        }

        require_once __DIR__ . '/../../views/public/spmb_form.php';
    }

    public function spmbCheck() {
        $settings = getSettings();
        $error = '';
        $registration = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = getDB();
            $regNum = $db->real_escape_string(trim($_POST['reg_number'] ?? ''));
            if (empty($regNum)) {
                $error = 'Masukkan nomor pendaftaran terlebih dahulu.';
            } else {
                $res = $db->query("SELECT r.*, p.name as program_name, p2.name as program_choice2_name FROM spmb_registrations r LEFT JOIN programs p ON r.program_id=p.id LEFT JOIN programs p2 ON r.program_choice2=p2.id WHERE r.registration_number='$regNum' LIMIT 1");
                if ($res && $row = $res->fetch_assoc()) {
                    $registration = $row;
                } else {
                    $error = 'Nomor pendaftaran tidak ditemukan. Pastikan nomor yang Anda masukkan benar.';
                }
            }
        }

        require_once __DIR__ . '/../../views/public/spmb_check.php';
    }
}
