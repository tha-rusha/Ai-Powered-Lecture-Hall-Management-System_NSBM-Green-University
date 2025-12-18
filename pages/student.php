<?php
// Lecturer/dashboard.php
// session_start();
// if (!isset($_SESSION['user'])) { header('Location: ../auth/login.php'); exit; }
// if ($_SESSION['user']['role'] !== 'lecturer') { header('Location: ../index.php'); exit; }

// $lecturerName = $_SESSION['user']['name']; // e.g., "Ms. Perera"
// ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Lecturer • NSBM Lecture Hall Manager</title>
  <link rel="stylesheet" href="../css/style.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- Expose mode + current lecturer to JS -->
  <script>
    window.APP_MODE = 'lecturer';
    window.CURRENT_LECTURER = <?php echo json_encode($lecturerName); ?>;
  </script>

  <!-- Shared app code -->
  <script src="../js/app.js" defer></script>
  <!-- Lecturer-specific glue (below) -->
  <script src="./student.js" defer></script>
</head>
<body>
<div class="app">
  <?php include('../partials/header.php'); ?>
  <main>
    <aside>
      <div class="stack">
        <div class="section-title">student</div>
        <div class="nav" role="tablist">
        
          <button class="tab" data-tab="timetable">Timetable</button>
        
          <!-- Hide global 'Halls' / 'Analytics' for lecturers -->
        </div>
      </div>

      <div class="divider"></div>
      <div class="stack">
        <div class="section-title">Quick Actions</div>
        <button class="btn" id="printBtn">Print</button>
      </div>
    </aside>

    <section id="content" class="stack" aria-live="polite"></section>
  </main>
</div>
</body>
</html>
