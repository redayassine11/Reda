<?php
// config.php - إعدادات النظام
session_start();
define('USERS_FILE', 'users.json');
define('TEAMS_FILE', 'teams.json');
define('MESSAGES_FILE', 'messages.json');
define('PROFILE_IMG_DIR', 'uploads/');

// إنشاء المجلدات إذا لم تكن موجودة
if (!file_exists(PROFILE_IMG_DIR)) {
    mkdir(PROFILE_IMG_DIR, 0777, true);
}

// وظائف مساعدة
function loadJSON($file) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    return json_decode(file_get_contents($file), true);
}

function saveJSON($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUser($id) {
    $users = loadJSON(USERS_FILE);
    return $users[$id] ?? null;
}

function currentUser() {
    if (isLoggedIn()) {
        return getUser($_SESSION['user_id']);
    }
    return null;
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// CSS أنماط
$styles = '
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", system-ui, sans-serif;
}

:root {
    --primary: #4361ee;
    --secondary: #3a0ca3;
    --success: #4cc9f0;
    --danger: #f72585;
    --warning: #f8961e;
    --light: #f8f9fa;
    --dark: #212529;
    --gray: #6c757d;
    --bg: #f5f7fb;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* تصميم الكروت */
.card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 80px rgba(0,0,0,0.15);
}

/* النماذج */
.form-group {
    margin-bottom: 25px;
}

.form-control {
    width: 100%;
    padding: 15px;
    border: 2px solid #e1e5eb;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    outline: none;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark);
}

/* الأزرار */
.btn {
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-block;
    text-decoration: none;
    text-align: center;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--secondary);
    transform: scale(1.05);
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-outline {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}

.btn-outline:hover {
    background: var(--primary);
    color: white;
}

/* التنقل */
.navbar {
    background: white;
    border-radius: 20px;
    padding: 20px 30px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.nav-brand {
    font-size: 28px;
    font-weight: 800;
    background: linear-gradient(45deg, var(--primary), var(--danger));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.nav-menu {
    display: flex;
    gap: 20px;
    align-items: center;
}

.nav-link {
    color: var(--dark);
    text-decoration: none;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 10px;
    transition: all 0.3s;
}

.nav-link:hover {
    background: var(--bg);
    color: var(--primary);
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary);
}

/* شبكة العناصر */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.team-card, .user-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.team-card:hover, .user-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
}

.status-online {
    color: var(--success);
    font-weight: 600;
}

.status-offline {
    color: var(--gray);
}

/* الدردشة */
.chat-container {
    display: flex;
    gap: 30px;
    margin-top: 30px;
}

.chat-list {
    flex: 1;
    max-width: 350px;
}

.chat-window {
    flex: 2;
    background: white;
    border-radius: 20px;
    padding: 30px;
}

.chat-messages {
    height: 400px;
    overflow-y: auto;
    padding: 20px;
    border: 2px solid var(--bg);
    border-radius: 15px;
    margin-bottom: 20px;
}

.message {
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 15px;
    max-width: 70%;
}

.message.sent {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    margin-left: auto;
}

.message.received {
    background: var(--bg);
    color: var(--dark);
}

/* الصور */
.avatar-upload {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto 30px;
}

.avatar-preview {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid var(--primary);
}

.avatar-upload input {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    border: none;
    cursor: pointer;
}

/* التنبيهات */
.alert {
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 30px;
    font-weight: 600;
}

.alert-success {
    background: rgba(76, 201, 240, 0.2);
    color: var(--success);
    border: 2px solid var(--success);
}

.alert-error {
    background: rgba(247, 37, 133, 0.2);
    color: var(--danger);
    border: 2px solid var(--danger);
}

/* الهيدر */
.header {
    text-align: center;
    margin-bottom: 50px;
    color: white;
}

.header h1 {
    font-size: 3.5rem;
    margin-bottom: 15px;
    text-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.header p {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* تصميم متجاوب */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        gap: 20px;
    }
    
    .nav-menu {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .chat-container {
        flex-direction: column;
    }
    
    .chat-list {
        max-width: 100%;
    }
    
    .header h1 {
        font-size: 2.5rem;
    }
}
</style>
';

// معالجة الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'register':
                $email = sanitize($_POST['email']);
                $name = sanitize($_POST['name']);
                $password = $_POST['password'];
                $gender = sanitize($_POST['gender']);
                
                $users = loadJSON(USERS_FILE);
                
                // التحقق من وجود الإيميل
                foreach ($users as $user) {
                    if ($user['email'] === $email) {
                        $error = "الإيميل مسجل مسبقاً";
                        break;
                    }
                }
                
                if (!isset($error)) {
                    $id = uniqid('user_');
                    $users[$id] = [
                        'id' => $id,
                        'email' => $email,
                        'name' => $name,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'gender' => $gender,
                        'bio' => '',
                        'avatar' => '',
                        'last_seen' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    saveJSON(USERS_FILE, $users);
                    $_SESSION['user_id'] = $id;
                    header('Location: ?');
                    exit;
                }
                break;
                
            case 'login':
                $email = sanitize($_POST['email']);
                $password = $_POST['password'];
                
                $users = loadJSON(USERS_FILE);
                $found = false;
                
                foreach ($users as $id => $user) {
                    if ($user['email'] === $email && password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $id;
                        $users[$id]['last_seen'] = date('Y-m-d H:i:s');
                        saveJSON(USERS_FILE, $users);
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $error = "الإيميل أو كلمة المرور غير صحيحة";
                } else {
                    header('Location: ?');
                    exit;
                }
                break;
                
            case 'update_profile':
                if (isLoggedIn()) {
                    $users = loadJSON(USERS_FILE);
                    $user = $users[$_SESSION['user_id']];
                    
                    if (isset($_POST['name'])) {
                        $user['name'] = sanitize($_POST['name']);
                    }
                    
                    if (isset($_POST['bio'])) {
                        $user['bio'] = sanitize($_POST['bio']);
                    }
                    
                    // رفع صورة
                    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                        $filename = $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                        $target = PROFILE_IMG_DIR . $filename;
                        
                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                            // حذف الصورة القديمة إذا موجودة
                            if ($user['avatar'] && file_exists(PROFILE_IMG_DIR . $user['avatar'])) {
                                unlink(PROFILE_IMG_DIR . $user['avatar']);
                            }
                            $user['avatar'] = $filename;
                        }
                    }
                    
                    $users[$_SESSION['user_id']] = $user;
                    saveJSON(USERS_FILE, $users);
                    $success = "تم تحديث الملف الشخصي بنجاح";
                }
                break;
                
            case 'create_team':
                if (isLoggedIn()) {
                    $teams = loadJSON(TEAMS_FILE);
                    $team_id = uniqid('team_');
                    
                    $teams[$team_id] = [
                        'id' => $team_id,
                        'name' => sanitize($_POST['team_name']),
                        'leader' => $_SESSION['user_id'],
                        'members' => [$_SESSION['user_id']],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    saveJSON(TEAMS_FILE, $teams);
                    $success = "تم إنشاء الفريق بنجاح";
                }
                break;
                
            case 'send_message':
                if (isLoggedIn() && isset($_POST['receiver_id'], $_POST['message'])) {
                    $messages = loadJSON(MESSAGES_FILE);
                    $message_id = uniqid('msg_');
                    
                    $messages[$message_id] = [
                        'id' => $message_id,
                        'sender_id' => $_SESSION['user_id'],
                        'receiver_id' => sanitize($_POST['receiver_id']),
                        'message' => sanitize($_POST['message']),
                        'timestamp' => date('Y-m-d H:i:s'),
                        'read' => false
                    ];
                    
                    saveJSON(MESSAGES_FILE, $messages);
                }
                break;
                
            case 'logout':
                session_destroy();
                header('Location: ?');
                exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eFootball بطولة - eFootball Tournament</title>
    <?php echo $styles; ?>
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (!isLoggedIn()): ?>
            <!-- صفحة التسجيل/الدخول -->
            <div class="header">
                <h1>⚽ بطولة eFootball</h1>
                <p>انضم إلى البطولة الأكبر وتنافس مع أفضل اللاعبين</p>
            </div>
            
            <div class="card">
                <h2 style="text-align: center; margin-bottom: 30px; color: var(--primary);">تسجيل حساب جديد</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">اسم اللاعب</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">الجنس</label>
                        <select name="gender" class="form-control" required>
                            <option value="">اختر...</option>
                            <option value="male">👨 ذكر</option>
                            <option value="female">👩 أنثى</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">تسجيل حساب</button>
                </form>
                
                <hr style="margin: 30px 0; border: 1px solid #eee;">
                
                <h3 style="text-align: center; margin-bottom: 20px;">لديك حساب بالفعل؟</h3>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-outline" style="width: 100%;">دخول إلى الحساب</button>
                </form>
            </div>
        <?php else: 
            $user = currentUser();
            $users = loadJSON(USERS_FILE);
            $teams = loadJSON(TEAMS_FILE);
            $messages = loadJSON(MESSAGES_FILE);
        ?>
            <!-- لوحة التحكم بعد الدخول -->
            <nav class="navbar">
                <div class="nav-brand">⚽ eFootball Tournament</div>
                <div class="nav-menu">
                    <span style="color: var(--primary); font-weight: 600;">مرحباً، <?php echo $user['name']; ?>!</span>
                    
                    <?php if ($user['avatar']): ?>
                        <img src="<?php echo PROFILE_IMG_DIR . $user['avatar']; ?>" class="user-avatar" alt="صورة">
                    <?php endif; ?>
                    
                    <a href="?profile" class="nav-link">👤 الملف الشخصي</a>
                    <a href="?teams" class="nav-link">👥 الفرق</a>
                    <a href="?search" class="nav-link">🔍 البحث عن لاعبين</a>
                    <a href="?messages" class="nav-link">💬 الرسائل</a>
                    
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn btn-danger">🚪 خروج</button>
                    </form>
                </div>
            </nav>
            
            <?php 
            // تحديد الصفحة المطلوبة
            $page = isset($_GET['profile']) ? 'profile' : 
                    (isset($_GET['teams']) ? 'teams' : 
                    (isset($_GET['search']) ? 'search' : 
                    (isset($_GET['messages']) ? 'messages' : 'dashboard')));
            ?>
            
            <?php if ($page === 'dashboard'): ?>
                <!-- لوحة التحكم الرئيسية -->
                <div class="card">
                    <h1>🎮 لوحة التحكم</h1>
                    <p style="color: var(--gray); margin: 20px 0;">مرحباً بك في بطولة eFootball، <?php echo $user['name']; ?>!</p>
                    
                    <div class="grid">
                        <div class="user-card" style="text-align: center;">
                            <h3>📊 إحصائياتك</h3>
                            <?php if ($user['avatar']): ?>
                                <img src="<?php echo PROFILE_IMG_DIR . $user['avatar']; ?>" style="width: 100px; height: 100px; border-radius: 50%; margin: 15px auto;">
                            <?php endif; ?>
                            <p><strong>الاسم:</strong> <?php echo $user['name']; ?></p>
                            <p><strong>آخر ظهور:</strong> <?php echo $user['last_seen']; ?></p>
                            <p><strong>الجنس:</strong> <?php echo $user['gender'] === 'male' ? '👨 ذكر' : '👩 أنثى'; ?></p>
                        </div>
                        
                        <div class="user-card">
                            <h3>🚀 إجراءات سريعة</h3>
                            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 15px;">
                                <a href="?profile" class="btn btn-primary">تعديل الملف الشخصي</a>
                                <a href="?teams" class="btn btn-success">إنشاء فريق جديد</a>
                                <a href="?search" class="btn btn-outline">البحث عن لاعبين</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- الفرق النشطة -->
                <div class="card">
                    <h2>👥 الفريق الخاص بك</h2>
                    <div class="grid">
                        <?php 
                        $user_teams = [];
                        foreach ($teams as $team) {
                            if (in_array($user['id'], $team['members'])) {
                                $user_teams[] = $team;
                            }
                        }
                        
                        if (empty($user_teams)): ?>
                            <p style="color: var(--gray); text-align: center; padding: 30px;">لم تنضم إلى أي فريق بعد.</p>
                            <a href="?teams" class="btn btn-primary" style="width: 200px; margin: 0 auto;">إنشاء فريق</a>
                        <?php else: 
                            foreach ($user_teams as $team): 
                                $leader = getUser($team['leader']);
                        ?>
                            <div class="team-card">
                                <h3><?php echo $team['name']; ?></h3>
                                <p><strong>القائد:</strong> <?php echo $leader['name']; ?></p>
                                <p><strong>الأعضاء:</strong> <?php echo count($team['members']); ?> لاعب</p>
                                <p><strong>تاريخ الإنشاء:</strong> <?php echo $team['created_at']; ?></p>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                
            <?php elseif ($page === 'profile'): ?>
                <!-- تعديل الملف الشخصي -->
                <div class="card">
                    <h1>👤 تعديل الملف الشخصي</h1>
                    
                    <div class="avatar-upload">
                        <?php if ($user['avatar']): ?>
                            <img src="<?php echo PROFILE_IMG_DIR . $user['avatar']; ?>" class="avatar-preview" id="avatarPreview">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=random&size=150" class="avatar-preview" id="avatarPreview">
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label class="form-label">تغيير الصورة الشخصية</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewImage(this)">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">اسم اللاعب</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">نبذة عنك</label>
                            <textarea name="bio" class="form-control" rows="4" placeholder="أخبرنا عن مهاراتك في اللعبة..."><?php echo $user['bio']; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">💾 حفظ التعديلات</button>
                    </form>
                </div>
                
                <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('avatarPreview').src = e.target.result;
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
                </script>
                
            <?php elseif ($page === 'teams'): ?>
                <!-- إدارة الفرق -->
                <div class="card">
                    <h1>👥 إدارة الفرق</h1>
                    
                    <div style="background: var(--bg); padding: 25px; border-radius: 15px; margin-bottom: 30px;">
                        <h3>إنشاء فريق جديد</h3>
                        <form method="POST" action="" style="margin-top: 20px;">
                            <input type="hidden" name="action" value="create_team">
                            
                            <div class="form-group">
                                <label class="form-label">اسم الفريق</label>
                                <input type="text" name="team_name" class="form-control" placeholder="أدخل اسم فريقك" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success">➕ إنشاء فريق</button>
                        </form>
                    </div>
                    
                    <h2 style="margin-bottom: 25px;">جميع الفرق</h2>
                    <div class="grid">
                        <?php if (empty($teams)): ?>
                            <p style="color: var(--gray); text-align: center; padding: 30px; grid-column: 1/-1;">لا توجد فرق حتى الآن.</p>
                        <?php else: 
                            foreach ($teams as $team): 
                                $leader = getUser($team['leader']);
                        ?>
                            <div class="team-card">
                                <h3><?php echo $team['name']; ?></h3>
                                <p><strong>القائد:</strong> <?php echo $leader['name']; ?></p>
                                <p><strong>عدد الأعضاء:</strong> <?php echo count($team['members']); ?></p>
                                <p><strong>تاريخ الإنشاء:</strong> <?php echo $team['created_at']; ?></p>
                                
                                <?php if ($team['leader'] === $user['id']): ?>
                                    <span style="background: var(--primary); color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px;">أنت القائد</span>
                                <?php elseif (in_array($user['id'], $team['members'])): ?>
                                    <span style="background: var(--success); color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px;">عضو في الفريق</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                
            <?php elseif ($page === 'search'): ?>
                <!-- البحث عن لاعبين -->
                <div class="card">
                    <h1>🔍 البحث عن لاعبين</h1>
                    
                    <form method="GET" action="" style="margin-bottom: 30px;">
                        <input type="hidden" name="search" value="1">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" placeholder="ابحث باسم اللاعب..." value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">🔍 بحث</button>
                    </form>
                    
                    <div class="grid">
                        <?php 
                        $search_results = $users;
                        if (isset($_GET['q']) && !empty($_GET['q'])) {
                            $query = strtolower($_GET['q']);
                            $search_results = array_filter($users, function($u) use ($query) {
                                return strpos(strtolower($u['name']), $query) !== false;
                            });
                        }
                        
                        // إزالة المستخدم الحالي من النتائج
                        unset($search_results[$user['id']]);
                        
                        if (empty($search_results)): ?>
                            <p style="color: var(--gray); text-align: center; padding: 30px; grid-column: 1/-1;">لا توجد نتائج للبحث.</p>
                        <?php else: 
                            foreach ($search_results as $player): 
                        ?>
                            <div class="user-card">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                                    <?php if ($player['avatar']): ?>
                                        <img src="<?php echo PROFILE_IMG_DIR . $player['avatar']; ?>" style="width: 60px; height: 60px; border-radius: 50%;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(45deg, var(--primary), var(--success)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?php echo substr($player['name'], 0, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <h3><?php echo $player['name']; ?></h3>
                                        <p class="<?php echo (strtotime($player['last_seen']) > time() - 300) ? 'status-online' : 'status-offline'; ?>">
                                            ● <?php echo (strtotime($player['last_seen']) > time() - 300) ? 'متصل الآن' : 'آخر ظهور: ' . $player['last_seen']; ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if ($player['bio']): ?>
                                    <p style="color: var(--gray); font-size: 14px; margin-bottom: 15px;"><?php echo substr($player['bio'], 0, 100) . '...'; ?></p>
                                <?php endif; ?>
                                
                                <div style="display: flex; gap: 10px;">
                                    <a href="?messages&to=<?php echo $player['id']; ?>" class="btn btn-outline" style="flex: 1;">💬 مراسلة</a>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                
            <?php elseif ($page === 'messages'): ?>
                <!-- نظام الرسائل -->
                <div class="card">
                    <h1>💬 الرسائل</h1>
                    
                    <div class="chat-container">
                        <div class="chat-list">
                            <h3 style="margin-bottom: 20px;">المحادثات</h3>
                            <?php 
                            // جمع المحادثات
                            $conversations = [];
                            foreach ($messages as $msg) {
                                if ($msg['sender_id'] === $user['id'] || $msg['receiver_id'] === $user['id']) {
                                    $other_id = $msg['sender_id'] === $user['id'] ? $msg['receiver_id'] : $msg['sender_id'];
                                    $conversations[$other_id][] = $msg;
                                }
                            }
                            
                            if (empty($conversations)): ?>
                                <p style="color: var(--gray); text-align: center; padding: 20px;">لا توجد محادثات بعد.</p>
                            <?php else: 
                                foreach ($conversations as $other_id => $msgs): 
                                    $other_user = getUser($other_id);
                                    $last_msg = end($msgs);
                            ?>
                                <a href="?messages&to=<?php echo $other_id; ?>" style="display: block; text-decoration: none; color: inherit;">
                                    <div style="padding: 15px; border-radius: 10px; margin-bottom: 10px; background: <?php echo isset($_GET['to']) && $_GET['to'] === $other_id ? 'var(--bg)' : 'white'; ?>; border: 2px solid <?php echo isset($_GET['to']) && $_GET['to'] === $other_id ? 'var(--primary)' : '#eee'; ?>;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <?php if ($other_user['avatar']): ?>
                                                <img src="<?php echo PROFILE_IMG_DIR . $other_user['avatar']; ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                            <?php else: ?>
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                    <?php echo substr($other_user['name'], 0, 1); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div style="flex: 1;">
                                                <strong><?php echo $other_user['name']; ?></strong>
                                                <p style="color: var(--gray); font-size: 14px; margin: 5px 0 0;"><?php echo substr($last_msg['message'], 0, 30) . '...'; ?></p>
                                            </div>
                                            
                                            <?php if (!$last_msg['read'] && $last_msg['sender_id'] !== $user['id']): ?>
                                                <span style="background: var(--primary); width: 10px; height: 10px; border-radius: 50%;"></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; endif; ?>
                        </div>
                        
                        <div class="chat-window">
                            <?php if (isset($_GET['to']) && isset($users[$_GET['to']])): 
                                $receiver = $users[$_GET['to']];
                                $chat_messages = [];
                                
                                foreach ($messages as $msg) {
                                    if (($msg['sender_id'] === $user['id'] && $msg['receiver_id'] === $receiver['id']) ||
                                        ($msg['sender_id'] === $receiver['id'] && $msg['receiver_id'] === $user['id'])) {
                                        $chat_messages[] = $msg;
                                    }
                                }
                                
                                // تحديث حالة الرسائل كمقروءة
                                foreach ($messages as $id => $msg) {
                                    if ($msg['sender_id'] === $receiver['id'] && $msg['receiver_id'] === $user['id'] && !$msg['read']) {
                                        $messages[$id]['read'] = true;
                                    }
                                }
                                saveJSON(MESSAGES_FILE, $messages);
                            ?>
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid var(--bg);">
                                    <?php if ($receiver['avatar']): ?>
                                        <img src="<?php echo PROFILE_IMG_DIR . $receiver['avatar']; ?>" style="width: 60px; height: 60px; border-radius: 50%;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(45deg, var(--warning), var(--danger)); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">
                                            <?php echo substr($receiver['name'], 0, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <h2><?php echo $receiver['name']; ?></h2>
                                        <p class="<?php echo (strtotime($receiver['last_seen']) > time() - 300) ? 'status-online' : 'status-offline'; ?>">
                                            ● <?php echo (strtotime($receiver['last_seen']) > time() - 300) ? 'متصل الآن' : 'آخر ظهور: ' . $receiver['last_seen']; ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="chat-messages" id="chatMessages">
                                    <?php if (empty($chat_messages)): ?>
                                        <p style="color: var(--gray); text-align: center; padding: 30px;">ابدأ محادثة جديدة...</p>
                                    <?php else: 
                                        foreach ($chat_messages as $msg): 
                                            $is_sent = $msg['sender_id'] === $user['id'];
                                    ?>
                                        <div class="message <?php echo $is_sent ? 'sent' : 'received'; ?>">
                                            <p><?php echo $msg['message']; ?></p>
                                            <small style="opacity: 0.8; font-size: 12px;"><?php echo $msg['timestamp']; ?></small>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                                
                                <form method="POST" action="" onsubmit="sendMessage(event)">
                                    <input type="hidden" name="action" value="send_message">
                                    <input type="hidden" name="receiver_id" value="<?php echo $receiver['id']; ?>">
                                    
                                    <div style="display: flex; gap: 10px;">
                                        <input type="text" name="message" class="form-control" placeholder="اكتب رسالتك هنا..." id="messageInput" required>
                                        <button type="submit" class="btn btn-primary">إرسال</button>
                                    </div>
                                </form>
                                
                                <script>
                                function sendMessage(e) {
                                    e.preventDefault();
                                    var form = e.target;
                                    var formData = new FormData(form);
                                    
                                    fetch('', {
                                        method: 'POST',
                                        body: formData
                                    }).then(function() {
                                        form.reset();
                                        location.reload();
                                    });
                                }
                                
                                // تمرير لأسفل في الدردشة
                                var chatDiv = document.getElementById('chatMessages');
                                if (chatDiv) {
                                    chatDiv.scrollTop = chatDiv.scrollHeight;
                                }
                                </script>
                                
                            <?php else: ?>
                                <div style="text-align: center; padding: 50px;">
                                    <h2 style="color: var(--gray); margin-bottom: 20px;">👈 اختر محادثة من القائمة</h2>
                                    <p style="color: var(--gray);">أو ابحث عن لاعب لبدء محادثة جديدة</p>
                                    <a href="?search" class="btn btn-primary" style="margin-top: 20px;">🔍 البحث عن لاعبين</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- تحديث آخر ظهور -->
            <script>
            // تحديث وقت الاتصال كل دقيقة
            setInterval(function() {
                fetch('?ping=1');
            }, 60000);
            
            // عند إغلاق الصفحة
            window.addEventListener('beforeunload', function() {
                fetch('?ping=1');
            });
            </script>
            
            <?php 
            // تحديث آخر ظهور
            if (isset($_GET['ping'])) {
                $users = loadJSON(USERS_FILE);
                if (isset($users[$user['id']])) {
                    $users[$user['id']]['last_seen'] = date('Y-m-d H:i:s');
                    saveJSON(USERS_FILE, $users);
                }
                exit;
            }
            ?>
            
        <?php endif; ?>
        
        <!-- تذييل الصفحة -->
        <footer style="text-align: center; color: white; margin-top: 50px; padding: 20px; opacity: 0.8;">
            <p>© 2024 بطولة eFootball - جميع الحقوق محفوظة</p>
            <p style="font-size: 14px; margin-top: 10px;">نظام إدارة البطولات الإلكترونية</p>
        </footer>
    </div>
</body>
</html>