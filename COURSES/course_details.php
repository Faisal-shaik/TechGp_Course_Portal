<?php
include 'db_connect.php';
session_start();

$course_id = $_GET['id'];
$user_id   = $_SESSION["user_id"] ?? 0;

// Fetch course info
$course_sql = "SELECT * FROM courses WHERE course_id = $course_id";
$course = $conn->query($course_sql)->fetch_assoc();

// Fetch modules and lessons
$modules_sql = "
    SELECT m.module_id, m.module_title, l.lesson_id, l.lesson_title, l.video_link 
    FROM modules m
    LEFT JOIN lessons l ON m.module_id = l.module_id
    WHERE m.course_id = $course_id
    ORDER BY m.module_order, l.lesson_order
";
$modules = $conn->query($modules_sql);

// Group lessons under each module
$course_structure = [];
while ($row = $modules->fetch_assoc()) {
    $course_structure[$row['module_id']]['module_title'] = $row['module_title'];
    $course_structure[$row['module_id']]['lessons'][] = [
        'lesson_id' => $row['lesson_id'],
        'lesson_title' => $row['lesson_title'],
        'video_link' => $row['video_link']
    ];
}

// Check if user is enrolled
$enroll_check = $conn->query("SELECT * FROM enrollment WHERE user_id = $user_id AND course_id = $course_id");
$is_enrolled = $enroll_check->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $course['course_title']; ?> - Course Details</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ===== Body ===== */
body { background-color: #f8f9fa; margin:0; padding:0; font-family: Arial, sans-serif; }

/* ===== Navbar ===== */
.navbar {
  display: flex; justify-content: space-between; align-items: center;
  background-color: #003366; padding: 10px 20px; color: white; position: relative; z-index: 1000;
}
.navbar .logo { display: flex; align-items: center; font-weight: bold; font-size: 20px; }
.navbar .logo img { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
.nav-links { display: flex; align-items: center; gap: 15px; }
.nav-links a { color: white; text-decoration: none; font-weight: 500; }
.nav-links a:hover { text-decoration: underline; }

/* Hamburger menu for mobile */
.hamburger { display: none; flex-direction: column; cursor: pointer; gap: 4px; }
.hamburger div { width: 25px; height: 3px; background-color: white; border-radius: 2px; }

/* Profile dropdown - image only, no white box */
.dropdown { position: relative; display: inline-block; }
.profile-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid white;
    transition: transform 0.2s;
}
.profile-img:hover { transform: scale(1.1); }

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: #f9f9f9;
    min-width: 150px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
    z-index: 1000;
}
.dropdown-content a {
    color: black;
    padding: 8px 12px;
    text-decoration: none;
    display: block;
}
.dropdown-content a:hover { background-color: #ddd; }
.show { display: block; }

/* ===== Sidebar & Content ===== */
.sidebar { height: 100vh; overflow-y: auto; background: #fff; border-right: 1px solid #ddd; padding: 20px; }
.lesson-btn { cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding: 8px; margin-bottom: 5px; border-radius: 5px; }
.lesson-btn:hover { background: #f0f0f0; }
.module-checkbox { margin-right: 10px; }

/* Custom checkbox */
.lesson-checkbox {
    appearance: none; -webkit-appearance: none;
    width: 18px; height: 18px; border: 2px solid #5a5b62ff; border-radius: 4px; position: relative;
    cursor: pointer; margin-left: 10px; outline: none; display: inline-block;
}
.lesson-checkbox:checked {
    background-color: #1a73e8; border-color: #0c4ca3;
}
.lesson-checkbox:checked::after {
    content: '✓'; color: white; position: absolute;
    top: 50%; left: 50%; transform: translate(-50%, -50%);
    font-size: 14px; line-height: 1;
}

.current-lesson { background-color: #e9f0f6ff !important; border-radius:5px; }
#nextVideoBox { margin-bottom: 15px; display: none; }

/* ===== Responsive ===== */
@media (max-width: 768px){
  .nav-links { display: none; flex-direction: column; width: 100%; background-color: #003366; position: absolute; top: 60px; left: 0; }
  .nav-links a { padding: 10px; border-top: 1px solid rgba(255,255,255,0.2); }
  .hamburger { display: flex; }
  .sidebar { height: auto; max-height: 300px; margin-bottom: 15px; border-right: none; }
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="logo">
    <a href="home.html"><img src="TECHGP.png" alt="Logo"></a>
    TECHGP
  </div>

  <div class="hamburger" id="hamburger">
    <div></div><div></div><div></div>
  </div>

  <div class="nav-links" id="navLinks">
    <a href="home.html">Home</a>
    <a href="about.html">About</a>
    <a href="courses.php">Courses</a>
    <a href="contact.html">Contact</a>
    <div class="dropdown">
        <img src="profile.jpg" alt="Profile" class="profile-img" id="profileBtn">
        <div class="dropdown-content" id="dropdownMenu">
            <a href="profile.php">Profile</a>
            <a href="my_courses.php">My Courses</a>
            <a href="#">Progress</a>
            <a href="#">Settings</a>
            <a href="../INTERFACE/interface.html">Logout</a>
        </div>
    </div>
  </div>
</nav>

<!-- Main Page -->
<div class="container-fluid">
  <div class="row flex-column-reverse flex-md-row">
    <!-- Sidebar -->
    <div class="col-12 col-md-3 sidebar">
      <h4>📚 Course Content</h4>
      <?php if ($is_enrolled) { ?>
        <a href="my_courses.php" class="btn btn-outline-success w-100 mb-3" disabled>✅ Enrolled</a>
      <?php } else { ?>
        <a href="enroll.php?course_id=<?php echo $course_id; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-success w-100 mb-3">Enroll Now</a>
      <?php } ?>
      <div class="accordion" id="courseAccordion">
        <?php $i = 1; foreach ($course_structure as $module_id => $module) { ?>
          <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?php echo $i; ?>">
              <button class="accordion-button <?php echo $i>1?'collapsed':''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>">
                <input type="checkbox" class="module-checkbox" id="module-<?php echo $module_id; ?>" disabled>
                <?php echo $module['module_title']; ?>
              </button>
            </h2>
            <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i==1?'show':''; ?>" data-bs-parent="#courseAccordion">
              <div class="accordion-body">
                <?php foreach($module['lessons'] as $lesson) { ?>
                  <div class="lesson-btn" id="lesson-row-<?php echo $lesson['lesson_id']; ?>">
                    <span onclick="playLesson(<?php echo $lesson['lesson_id']; ?>)"><?php echo $lesson['lesson_title']; ?></span>
                    <input type="checkbox" class="lesson-checkbox" id="lesson-<?php echo $lesson['lesson_id']; ?>" disabled>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php $i++; } ?>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-12 col-md-9 content">
      <h2><?php echo $course['course_title']; ?></h2>
      <div id="courseDescription"><?php echo nl2br($course['course_description']); ?></div>
      <div class="ratio ratio-16x9 mb-3">
        <div id="player"></div>
      </div>
      <div id="nextVideoBox">
        <button class="btn btn-primary" onclick="playNextLesson()">▶ Mark as completed</button>
      </div>
    </div>
  </div>
</div>

<script>
// Hamburger toggle
const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("navLinks");
hamburger.addEventListener("click", () => {
  navLinks.style.display = navLinks.style.display === "flex" ? "none" : "flex";
});

// Profile dropdown toggle
const profileBtn = document.getElementById("profileBtn");
const dropdownMenu = document.getElementById("dropdownMenu");
profileBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  dropdownMenu.classList.toggle("show");
});
document.addEventListener("click", () => dropdownMenu.classList.remove("show"));

// Lessons & YouTube Player
let lessons = [];
<?php
foreach($course_structure as $moduleId => $module){
    foreach($module['lessons'] as $lesson){
        echo "lessons.push({module_id: $moduleId, lesson_id: ".$lesson['lesson_id'].", video_link: '".$lesson['video_link']."'});\n";
    }
}
?>
let currentIndex = 0;
let player;
let previousLessonRow = null;

function getYouTubeID(url){
    let match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    return match ? match[1] : null;
}

let tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
document.body.appendChild(tag);

function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        height: '390',
        width: '100%',
        videoId: getYouTubeID(lessons[currentIndex].video_link),
        events: { 'onStateChange': onPlayerStateChange }
    });
}

function onPlayerStateChange(event){
    if(event.data === YT.PlayerState.ENDED){
        document.getElementById('nextVideoBox').style.display = 'block';
    }
}

function loadLesson(index){
    currentIndex = index;
    let lesson = lessons[index];
    let videoId = getYouTubeID(lesson.video_link);

    if(previousLessonRow) previousLessonRow.classList.remove('current-lesson');
    previousLessonRow = document.getElementById('lesson-row-' + lesson.lesson_id);
    previousLessonRow.classList.add('current-lesson');

    document.getElementById('nextVideoBox').style.display = 'none';
    document.getElementById("courseDescription").style.display = "none";

    if(player && player.loadVideoById){
        player.loadVideoById(videoId);
    }
}

function playLesson(lessonId){
    let idx = lessons.findIndex(l => l.lesson_id == lessonId);
    if(idx >= 0) loadLesson(idx);
}

function playNextLesson(){
    let lesson = lessons[currentIndex];
    document.getElementById('lesson-' + lesson.lesson_id).checked = true;

    let moduleCollapse = document.querySelector('#collapse' + lesson.module_id);
    let allLessons = moduleCollapse.querySelectorAll('.lesson-checkbox');
    let allDone = true;
    allLessons.forEach(chk => { if(!chk.checked) allDone = false; });
    if(allDone) document.getElementById('module-' + lesson.module_id).checked = true;

    currentIndex++;
    if(currentIndex < lessons.length){
        loadLesson(currentIndex);
        let nextLesson = lessons[currentIndex];
        let collapseEl = document.querySelector('#collapse' + nextLesson.module_id);
        if(!collapseEl.classList.contains('show')){
            new bootstrap.Collapse(collapseEl, {toggle:true});
        }
    } else {
        alert("You have completed all lessons!");
        document.getElementById('nextVideoBox').style.display = 'none';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
