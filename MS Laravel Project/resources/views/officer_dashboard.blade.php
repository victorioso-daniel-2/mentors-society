<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor's Society - Dashboard</title>
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
        .quick-action-icon {
            color: #ffd600 !important;
        }
        .profile-dropdown {
            min-width: 220px;
            z-index: 50;
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
                <li><a href="student_management" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-user-graduate"></i> Student</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-calendar-days"></i> Event</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-peso-sign"></i> Financial</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-boxes-stacked"></i> Inventory</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-file-lines"></i> Report</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-gear"></i> System</a></li>
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
            <div class="flex items-center gap-6 relative">
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
                        <button id="studentBtn" class="w-full flex items-center gap-2 justify-center bg-black hover:bg-gray-800 text-white font-bold px-4 py-2 rounded-lg transition-colors mb-2"><i class="fa-solid fa-user-graduate"></i> Student</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 px-10 mt-8" id="statsCards">
            <div class="bg-white rounded-xl shadow flex flex-col items-center py-6">
                <i class="fa-solid fa-users text-3xl text-blue-500 mb-2"></i>
                <span class="text-2xl font-bold text-blue-700 stat-total-users">0</span>
                <span class="text-gray-500 font-semibold mt-1">Total Users</span>
            </div>
            <div class="bg-white rounded-xl shadow flex flex-col items-center py-6">
                <i class="fa-solid fa-calendar-check text-3xl text-green-500 mb-2"></i>
                <span class="text-2xl font-bold text-green-700 stat-active-events">0</span>
                <span class="text-gray-500 font-semibold mt-1">Active Events</span>
            </div>
            <div class="bg-white rounded-xl shadow flex flex-col items-center py-6">
                <i class="fa-solid fa-list-check text-3xl text-yellow-500 mb-2"></i>
                <span class="text-2xl font-bold text-yellow-600 stat-pending-tasks">0</span>
                <span class="text-gray-500 font-semibold mt-1">Pending Tasks</span>
            </div>
            <div class="bg-white rounded-xl shadow flex flex-col items-center py-6">
                <i class="fa-solid fa-piggy-bank text-3xl text-pink-500 mb-2"></i>
                <span class="text-2xl font-bold text-pink-600 stat-total-budget">0</span>
                <span class="text-gray-500 font-semibold mt-1">Total Budget</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="px-10 mt-10">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-4">
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-user-plus quick-action-icon"></i> Add Student</button>
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-calendar-plus quick-action-icon"></i> Create Event</button>
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-receipt quick-action-icon"></i> Record Transaction</button>
                    <button class="flex items-center gap-2 px-6 py-3 border-2 border-dashed border-yellow-400 rounded-lg font-semibold text-gray-700 hover:bg-yellow-50 transition"><i class="fa-solid fa-tasks quick-action-icon"></i> Assign Task</button>
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
        document.getElementById('logoutBtn').addEventListener('click', function() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            localStorage.removeItem('user_roles');
            window.location.href = '/';
        });

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
        // Prevent closing when clicking inside dropdown
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Remove logout button from dropdown (handled in sidebar)
        const dropdownLogoutBtn = document.querySelector('#profileDropdown #logoutBtn');
        if (dropdownLogoutBtn) {
            dropdownLogoutBtn.parentNode.removeChild(dropdownLogoutBtn);
        }
        // Student button logic
        const studentBtn = document.getElementById('studentBtn');
        if (studentBtn) {
            studentBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                window.location.href = '/student-dashboard';
            });
        }

        // Fetch officer dashboard stats
        async function loadOfficerDashboard() {
            try {
                const res = await fetch('/api/dashboard/overview', {
                    headers: {
                        'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data && data.success && data.data) {
                    document.querySelectorAll('.stat-total-users').forEach(e => e.textContent = data.data.total_users || 0);
                    document.querySelectorAll('.stat-active-events').forEach(e => e.textContent = data.data.active_events || 0);
                    document.querySelectorAll('.stat-pending-tasks').forEach(e => e.textContent = data.data.pending_tasks || 0);
                    document.querySelectorAll('.stat-total-budget').forEach(e => e.textContent = data.data.total_budget || 0);
                }
            } catch (err) {}
        }
        loadOfficerDashboard();
    });
</script>
</body>
</html> 