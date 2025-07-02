<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor's Society - Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .sidebar-link.active {
            background: #ffd600;
            color: #22223b;
            position: relative;
        }
        .sidebar-link.active .sidebar-indicator {
            display: block;
        }
        .sidebar-link .sidebar-indicator {
            display: none;
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #111;
            border-radius: 6px 0 0 6px;
        }
        .sidebar-link {
            transition: background 0.2s, color 0.2s;
            position: relative;
        }
        .profile-dropdown {
            min-width: 220px;
            z-index: 50;
        }
        .quick-action-icon {
            color: #ffd600 !important;
        }
    </style>
</head>
<body class="bg-[#f8f6f4] min-h-screen">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col py-6 px-4">
        <div class="flex flex-col items-center mb-8">
            <img src="/ms_logo.png" alt="Logo" class="w-16 h-16 mb-2" />
            <span class="font-extrabold text-lg text-gray-800 tracking-wide">Mentor's Society</span>
        </div>
        <nav class="flex-1">
            <ul class="space-y-2">
                <li><a href="#" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            </ul>
        </nav>
        <button id="logoutBtn" class="mt-8 flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-red-600 hover:bg-red-50 transition-colors"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col bg-[#f8f6f4]">
        <!-- Topbar -->
        <div class="flex items-center justify-between px-10 py-5 bg-[#ffd600] shadow relative">
            <div class="flex items-center w-1/2">
                <div class="relative w-full">
                    <input type="text" placeholder="Search..." class="w-full pl-12 pr-4 py-2 rounded-lg bg-white border-none shadow focus:outline-none text-lg" />
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                </div>
            </div>
            <div class="flex items-center gap-4 relative">
                <button class="relative text-gray-700 hover:text-gray-900">
                    <i class="fa-regular fa-bell text-2xl"></i>
                </button>
                <!-- Profile Dropdown Trigger -->
                <div id="profileArea" class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg shadow cursor-pointer relative select-none">
                    <div class="flex flex-col items-end">
                        <span id="userName" class="font-bold text-gray-800 leading-tight">User Name</span>
                        <span id="userNumber" class="text-xs text-gray-500">2021-00112-TG-0</span>
                    </div>
                    <i class="fa-solid fa-user-circle text-yellow-500 text-3xl"></i>
                    <i id="chevronIcon" class="fa-solid fa-chevron-down text-gray-500 text-lg transition-transform duration-200"></i>
                    <!-- Dropdown -->
                    <div id="profileDropdown" class="profile-dropdown absolute right-0 top-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 py-4 px-6 hidden">
                        <div class="mb-2">
                            <span class="block font-bold text-gray-800 text-base" id="dropdownName">User Name</span>
                            <span class="block text-xs text-gray-500" id="dropdownNumber">2021-00112-TG-0</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-4" id="dropdownEmail">student@email.com</div>
                        <button id="officerBtn" class="w-full flex items-center gap-2 justify-center bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-4 py-2 rounded-lg transition-colors mb-2"><i class="fa-solid fa-user-tie"></i> Officer</button>
                        <button id="logoutBtn" class="w-full flex items-center gap-2 justify-center bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded-lg transition-colors"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 px-10 mt-8" id="statsCards">
            <div class="bg-white rounded-xl shadow flex flex-col items-start py-6 px-8">
                <div class="flex items-center gap-4 mb-2">
                    <i class="fa-solid fa-calendar-days text-3xl text-blue-500"></i>
                    <span class="text-lg font-semibold text-gray-700">Events Registered</span>
                </div>
                <span class="text-3xl font-bold text-blue-700 stat-events-registered">0</span>
            </div>
            <div class="bg-white rounded-xl shadow flex flex-col items-start py-6 px-8">
                <div class="flex items-center gap-4 mb-2">
                    <i class="fa-solid fa-calendar-check text-3xl text-green-500"></i>
                    <span class="text-lg font-semibold text-gray-700">Events Attended</span>
                </div>
                <span class="text-3xl font-bold text-green-700 stat-events-attended">0</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="px-10 mt-10">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-4">
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-calendar-plus quick-action-icon"></i> Register for Event</button>
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-user-pen quick-action-icon"></i> Edit Profile</button>
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-file-lines quick-action-icon"></i> View Report</button>
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-circle-question quick-action-icon"></i> Get Help</button>
                </div>
            </div>
        </div>

        <!-- Activities and Events -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-10 mt-10 mb-10">
            <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Recent Activities</h2>
                <ul class="space-y-4" id="recentActivities"></ul>
            </div>
            <!-- Upcoming Events -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Upcoming Events</h2>
                <ul class="space-y-4" id="upcomingEvents"></ul>
            </div>
        </div>
    </main>
</div>
<script>
    // Display user information from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const userData = localStorage.getItem('user_data');
        if (userData) {
            const user = JSON.parse(userData);
            document.getElementById('userName').textContent = `${user.first_name} ${user.last_name}`;
            document.getElementById('userNumber').textContent = user.student_number;
            document.getElementById('dropdownName').textContent = `${user.first_name} ${user.last_name}`;
            document.getElementById('dropdownNumber').textContent = user.student_number;
            document.getElementById('dropdownEmail').textContent = user.email || 'No email';
        }
        // Handle logout
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function(e) {
                e.stopPropagation();
                try {
                    await fetch('/api/auth/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        credentials: 'include',
                    });
                } catch (err) {}
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');
                localStorage.removeItem('user_roles');
                window.location.href = '/';
            });
        }
        // Officer button logic
        const officerBtn = document.getElementById('officerBtn');
        if (officerBtn) {
            officerBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                window.location.href = '/dashboard';
            });
        }
        // Profile dropdown logic
        const profileArea = document.getElementById('profileArea');
        const profileDropdown = document.getElementById('profileDropdown');
        let dropdownOpen = false;
        const chevronIcon = document.getElementById('chevronIcon');
        profileArea.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownOpen = !dropdownOpen;
            profileDropdown.classList.toggle('hidden', !dropdownOpen);
            chevronIcon.style.transform = dropdownOpen ? 'rotate(180deg)' : 'rotate(0deg)';
        });
        document.addEventListener('click', function(e) {
            if (dropdownOpen) {
                profileDropdown.classList.add('hidden');
                dropdownOpen = false;
                chevronIcon.style.transform = 'rotate(0deg)';
            }
        });
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        // Dynamic dashboard data
        async function loadStudentDashboard() {
            try {
                const res = await fetch('/api/dashboard/student-overview', {
                    headers: {
                        'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data && data.success && data.data) {
                    // Stats
                    document.querySelectorAll('.stat-events-registered').forEach(e => e.textContent = data.data.events_registered || 0);
                    document.querySelectorAll('.stat-events-attended').forEach(e => e.textContent = data.data.events_attended || 0);
                    // Activities
                    const recentActivities = document.getElementById('recentActivities');
                    if (recentActivities && Array.isArray(data.data.recent_activities)) {
                        recentActivities.innerHTML = data.data.recent_activities.map(act => `
                            <li class="flex items-start gap-3">
                                <i class="fa-solid ${act.icon} text-${act.color}-500 text-xl mt-1"></i>
                                <div>
                                    <span class="font-semibold text-gray-700">${act.title}</span>
                                    <div class="text-gray-500 text-sm">${act.description}<br><span class="text-xs">${act.time}</span></div>
                                </div>
                            </li>
                        `).join('');
                    }
                    // Events
                    const upcomingEvents = document.getElementById('upcomingEvents');
                    if (upcomingEvents && Array.isArray(data.data.upcoming_events)) {
                        upcomingEvents.innerHTML = data.data.upcoming_events.map(ev => `
                            <li>
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid ${ev.icon} text-${ev.color}-500 text-xl"></i>
                                    <div>
                                        <span class="font-semibold text-gray-700">${ev.title}</span>
                                        <div class="text-gray-500 text-sm">${ev.datetime}<br>${ev.location}</div>
                                    </div>
                                </div>
                            </li>
                        `).join('');
                    }
                }
            } catch (err) {}
        }
        loadStudentDashboard();
    });
</script>
</body>
</html> 