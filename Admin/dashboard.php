<?php require __DIR__ . '/../auth/guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NSBM Lecture Hall Manager </title>
  <meta name="description" content="Data-driven & AI-powered lecture hall management (frontend-only) for NSBM Faculty of Computing."/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="../js/app.js" defer></script>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="app">
  <!-- Header -->
<header>
  <div class="brand">
    <div class="logo" aria-hidden="true">NH</div>
    <div>
      <div class="title">NSBM Lecture Hall Manager</div>
    </div>
    <span class="chip" title="Thesis-aligned">AI‑assisted Scheduling</span>
  </div>
  <div class="grow"></div>
  <div class="toolbar" role="group" aria-label="User controls">
    <button class="btn pill" onclick="location.href='../index.php'" title="Return to Home">Return to Home</button>
  </div>
  <button class="btn pill" onclick="location.href='/nsbm/auth/logout.php'" title="Logout">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
    <path d="M8 0a8 8 0 1 0 8 8 8 8 0 0 0-8-8zm0 15a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm1-10a1 1 0 0 1-2 0V4a1 1 0 0 1 2 0v1z"/>
  </svg>
  Logout
</button>
</header>

  
  <main>
    <!-- Sidebar -->
    <aside>
      <div class="stack">
        <div class="section-title">Navigation</div>
        <div class="nav" role="tablist">
          <button class="tab active" data-tab="dashboard" role="tab" aria-selected="true">Dashboard</button>
          <button class="tab" data-tab="timetable" role="tab">Timetable</button>
          <button class="tab" data-tab="halls" role="tab">Halls</button>
          <button class="tab" data-tab="requests" role="tab">Requests</button>
          <button class="tab" data-tab="analytics" role="tab">Analytics</button>
          <button class="tab" data-tab="settings" role="tab">Settings</button>
        </div>
      </div>

      <div class="divider"></div>

      <div class="stack">
        <div class="section-title">Filters</div>
        <div class="row">
          <label for="dayFilter">Day</label>
          <select id="dayFilter" class="select">
            <option value="All">All</option>
            <option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option>
          </select>
        </div>
        <div class="row">
          <label for="hallFilter">Hall</label>
          <select id="hallFilter" class="select"></select>
        </div>
        <div class="row">
          <label for="capacityFilter">Min Capacity</label>
          <input id="capacityFilter" class="input" type="number" min="0" step="10" placeholder="0" />
        </div>
        <div class="row">
          <label for="equipmentFilter">Equipment</label>
          <select id="equipmentFilter" class="select">
            <option value="Any">Any</option>
            <option>Projector</option>
            <option>Lab PCs</option>
            <option>Smart Board</option>
          </select>
        </div>
        <button class="btn" id="applyFilters">Apply Filters</button>
      </div>

      <div class="divider"></div>

      <div class="stack">
        <div class="section-title">Quick Actions</div>
        <button class="btn" id="newBookingBtn">New Booking</button>
        <button class="btn" id="exportBtn">Export CSV</button>
        <button class="btn" id="printBtn">Print</button>
      </div>

      <div class="divider"></div>

      <div class="alert" role="status" aria-live="polite">
        <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9v4m0 4h.01M12 3l9 18H3L12 3z" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <div>
          <strong>Predictive Notice</strong>
          <div class="hint">Exam season forecast next week: high demand Tue–Thu 10:00–14:00. Prefer large halls (FOC-401/402). <em>(Demo logic)</em></div>
        </div>
      </div>
    </aside>

    <!-- Content -->
    <section id="content" class="stack" aria-live="polite"></section>
  </main>
</div>


</body>
</html>
