<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor's Society - Student Management</title>
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
        .dashboard-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 2rem 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .dashboard-card .card-label {
            font-size: 1.125rem;
            font-weight: 600;
            color: #555;
        }
        .dashboard-card .card-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1d4ed8;
        }
        .table-header {
            font-weight: 700;
            color: #22223b;
            font-size: 1rem;
            background: #f8f6f4;
        }
        .table-row {
            font-size: 1rem;
            font-weight: 500;
            color: #22223b;
        }
        .btn-primary {
            background: #2563eb;
            color: #fff;
            font-weight: 700;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-size: 1.125rem;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-action {
            font-weight: 700;
            border-radius: 0.5rem;
            padding: 0.5rem 1.25rem;
            font-size: 1rem;
        }
        .btn-action-view {
            background: #2563eb;
            color: #fff;
        }
        .btn-action-edit {
            background: #facc15;
            color: #22223b;
        }
        .btn-action-deactivate {
            background: #ef4444;
            color: #fff;
        }
        .btn-action-activate {
            background: #22c55e;
            color: #fff;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col py-6 px-4">
        <div class="flex flex-col items-center mb-8">
            <img src="/ms_logo.png" alt="Logo" class="w-16 h-16 mb-2" />
            <span class="font-extrabold text-lg text-gray-800 tracking-wide">Mentor's Society</span>
        </div>
        <nav class="flex-1">
            <ul class="space-y-2">
                <li><a href="/officer_dashboard" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="/student-management" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-user-graduate"></i> Student</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-calendar-days"></i> Event</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-peso-sign"></i> Financial</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-boxes-stacked"></i> Inventory</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-file-lines"></i> Report</a></li>
                <li><a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-gray-700 hover:bg-gray-100"><span class="sidebar-indicator"></span><i class="fa-solid fa-gear"></i> System</a></li>
            </ul>
        </nav>
        <a href="/logout" class="mt-8 flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-red-600 hover:bg-red-50 transition-colors"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-0">
        <!-- Header -->
        <div class="bg-yellow-400 px-10 py-6 flex items-center justify-between">
            <h1 class="text-3xl font-extrabold text-black">Student Management</h1>
            <!-- Profile Dropdown Trigger -->
            <div class="flex items-center gap-6 relative">
                <button class="relative text-gray-700 hover:text-gray-900">
                    <i class="fa-regular fa-bell text-2xl"></i>
                </button>
                <div id="profileArea" class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg shadow cursor-pointer relative select-none">
                    <div class="flex flex-col items-end">
                        <span id="userName" class="font-bold text-gray-800 leading-tight">User Name</span>
                        <span id="userNumber" class="text-xs text-gray-500">2021-00112-TG-0</span>
                    </div>
                    <i class="fa-solid fa-user-circle text-yellow-500 text-3xl"></i>
                    <i id="chevronIcon" class="fa-solid fa-chevron-down text-gray-500 text-lg transition-transform duration-200"></i>
                    <div id="profileDropdown" class="profile-dropdown absolute right-0 top-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 py-4 px-6 hidden">
                        <div class="mb-2">
                            <span class="block font-bold text-gray-800 text-base" id="dropdownName">User Name</span>
                            <span class="block text-xs text-gray-500" id="dropdownNumber">2021-00112-TG-0</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-4" id="dropdownEmail">student@email.com</div>
                        <button id="officerBtn" class="w-full flex items-center gap-2 justify-center bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-4 py-2 rounded-lg transition-colors mb-2" style="display:none"><i class="fa-solid fa-user-tie"></i> Officer</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-10 py-6">
            <!-- Register Button -->
            <button class="btn-primary flex items-center gap-2 mb-6" id="openRegisterModal"><i class="fa-solid fa-user-plus"></i> Register Student</button>

            <!-- Floating Register Modal -->
            <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl p-10 relative font-montserrat">
                    <button id="closeRegisterModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl"><i class="fa-solid fa-times"></i></button>
                    <h2 class="text-2xl font-extrabold text-black text-center mb-8">Register Student</h2>
                    <form id="registerStudentForm" class="grid grid-cols-2 gap-6">
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">Student Number</label>
                            <input name="student_number" type="text" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" required />
                        </div>
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">Email</label>
                            <input name="email" type="email" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" required />
                        </div>
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">Last Name</label>
                            <input name="last_name" type="text" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" required />
                        </div>
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">First Name</label>
                            <input name="first_name" type="text" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" required />
                        </div>
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">Middle Initial</label>
                            <input name="middle_initial" type="text" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" maxlength="5" />
                        </div>
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">Course</label>
                            <input name="course" type="text" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" required />
                        </div>
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">Year Level</label>
                            <input name="year_level" type="text" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" required />
                        </div>
                        <div class="col-span-1">
                            <label class="block font-bold mb-2 text-gray-700">Section</label>
                            <input name="section" type="text" class="w-full border rounded-lg px-4 py-3 shadow focus:ring-2 focus:ring-yellow-400 font-montserrat text-base" required />
                        </div>
                        <div class="col-span-2">
                            <div id="registerError" class="text-red-600 text-sm mt-2 hidden"></div>
                            <div id="registerSuccess" class="text-green-600 text-sm mt-2 hidden"></div>
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg px-6 py-3 rounded-lg shadow mt-6 transition-colors"><i class="fa-solid fa-user-plus"></i> Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Search & Filter Fields -->
            <div class="flex space-x-4 mb-4">
                <input id="searchInput" type="text" placeholder="Search by Name or Student ID" class="bg-gray-200 rounded-lg px-4 py-2 w-1/3 font-bold text-gray-700" />
                <select id="searchClass" class="bg-gray-200 rounded-lg px-4 py-2 w-1/4 font-bold text-gray-700">
                    <option value="">Class</option>
                </select>
                <select id="statusFilter" class="bg-gray-200 rounded-lg px-4 py-2 w-1/4 font-bold text-gray-700">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="graduated">Graduated</option>
                </select>
                <!-- Sort By Dropdown -->
                <select id="sortBy" class="font-bold text-lg px-4 py-2 rounded-lg bg-gray-100 border-none shadow focus:ring-2 focus:ring-yellow-400 font-montserrat">
                    <option value="">Sort By</option>
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="student_id_asc">Student ID (Asc)</option>
                    <option value="student_id_desc">Student ID (Desc)</option>
                    <option value="status_asc">Status (Asc)</option>
                    <option value="status_desc">Status (Desc)</option>
                </select>
            </div>
            <!-- Table -->
            <div class="bg-white rounded-xl shadow p-6">
                <table class="w-full mt-4 rounded-xl overflow-hidden shadow">
                    <thead>
                        <tr class="table-header">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Student ID</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <tr><td colspan="6" class="text-center py-4 text-gray-400">Loading...</td></tr>
                    </tbody>
                </table>
                <div id="pagination" class="flex justify-center mt-4 space-x-2"></div>
            </div>
        </div>
    </main>
</div>
<!-- View/Edit Modal -->
<div id="studentModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-xl p-8 relative">
        <button id="closeStudentModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl"><i class="fa-solid fa-times"></i></button>
        <h2 id="studentModalTitle" class="text-2xl font-bold mb-6 text-center">Student Info</h2>
        <form id="studentInfoForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block font-semibold mb-1">Student Number</label><input name="student_number" type="text" class="w-full border rounded px-3 py-2" readonly /></div>
                <div><label class="block font-semibold mb-1">Email</label><input name="email" type="email" class="w-full border rounded px-3 py-2" /></div>
                <div><label class="block font-semibold mb-1">Last Name</label><input name="last_name" type="text" class="w-full border rounded px-3 py-2" /></div>
                <div><label class="block font-semibold mb-1">First Name</label><input name="first_name" type="text" class="w-full border rounded px-3 py-2" /></div>
                <div><label class="block font-semibold mb-1">Middle Initial</label><input name="middle_initial" type="text" class="w-full border rounded px-3 py-2" maxlength="5" /></div>
                <div><label class="block font-semibold mb-1">Course</label><input name="course" type="text" class="w-full border rounded px-3 py-2" /></div>
                <div><label class="block font-semibold mb-1">Year Level</label><input name="year_level" type="text" class="w-full border rounded px-3 py-2" /></div>
                <div><label class="block font-semibold mb-1">Section</label><input name="section" type="text" class="w-full border rounded px-3 py-2" /></div>
                <div><label class="block font-semibold mb-1">Academic Status</label><input name="academic_status" type="text" class="w-full border rounded px-3 py-2" /></div>
                <div><label class="block font-semibold mb-1">Status</label><input name="status" type="text" class="w-full border rounded px-3 py-2" readonly /></div>
            </div>
            <div id="studentModalError" class="text-red-600 text-sm mt-2 hidden"></div>
            <div id="studentModalSuccess" class="text-green-600 text-sm mt-2 hidden"></div>
            <div class="flex justify-end mt-6" id="studentModalActions">
                <button type="submit" class="bg-yellow-400 text-white px-6 py-2 rounded-lg font-bold hover:bg-yellow-500 flex items-center" id="saveStudentBtn"><i class="fa-solid fa-save mr-2"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
<!-- Deactivate Confirmation Modal -->
<div id="deactivateModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8 relative">
        <h2 class="text-xl font-bold mb-4 text-center">Deactivate Student Account?</h2>
        <p class="mb-6 text-center">This will prevent the student from logging in. Are you sure?</p>
        <div class="flex justify-end gap-4">
            <button id="cancelDeactivateBtn" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
            <button id="confirmDeactivateBtn" class="px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white font-bold">Deactivate</button>
        </div>
    </div>
</div>
<script>
// Modal open/close logic
const openBtn = document.getElementById('openRegisterModal');
const modal = document.getElementById('registerModal');
const closeBtn = document.getElementById('closeRegisterModal');
openBtn.addEventListener('click', () => { modal.classList.remove('hidden'); });
closeBtn.addEventListener('click', () => { modal.classList.add('hidden'); });
window.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });

// Form submit logic
const form = document.getElementById('registerStudentForm');
const errorDiv = document.getElementById('registerError');
const successDiv = document.getElementById('registerSuccess');
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.academic_status = 'active'; // Always set to active
    // Auto-generate password as FirstNameLastname
    data.password = (data.first_name || '') + (data.last_name || '');
    try {
        const response = await fetch('/api/students', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || '')
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (response.ok && result.success) {
            successDiv.textContent = 'Student registered successfully!';
            successDiv.classList.remove('hidden');
            form.reset();
            setTimeout(() => { modal.classList.add('hidden'); successDiv.classList.add('hidden'); }, 1500);
            // Optionally, refresh the student list here
        } else {
            errorDiv.textContent = result.message || 'Registration failed.';
            if (result.errors) errorDiv.textContent += ' ' + Object.values(result.errors).join(' ');
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'An error occurred. Please try again.';
        errorDiv.classList.remove('hidden');
    }
});

// --- Student Management Dynamic Table ---
const studentTableBody = document.getElementById('studentTableBody');
const searchInput = document.getElementById('searchInput');
const searchClass = document.getElementById('searchClass');
const statusFilter = document.getElementById('statusFilter');
const sortBy = document.getElementById('sortBy');

// Get token from localStorage
const token = localStorage.getItem('auth_token');
const apiHeaders = {
    'Accept': 'application/json',
    'Authorization': token ? 'Bearer ' + token : ''
};

// Fetch and populate class dropdown
async function loadClasses() {
    const res = await fetch('/api/students/classes/available', {
        headers: apiHeaders
    });
    const data = await res.json();
    if (data.success) {
        data.data.forEach(cls => {
            const opt = document.createElement('option');
            opt.value = cls.class_id;
            opt.textContent = cls.class_name;
            searchClass.appendChild(opt);
        });
    }
}

let currentPage = 1;
let lastPage = 1;
let currentSort = '';

async function loadStudents(page = 1) {
    studentTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-400">Loading...</td></tr>';
    let url = `/api/students?per_page=5&page=${page}`;
    const params = [];
    if (searchInput.value) params.push(`search=${encodeURIComponent(searchInput.value)}`);
    if (searchClass.value) params.push(`class_id=${encodeURIComponent(searchClass.value)}`);
    if (statusFilter.value) params.push(`status=${encodeURIComponent(statusFilter.value)}`);
    if (currentSort) {
        const [field, dir] = currentSort.split('_');
        if (field === 'name') {
            params.push(`sort_by=${encodeURIComponent(field)}&sort_order=${dir === 'asc' ? 'asc' : 'desc'}`);
        } else if (field === 'student') {
            params.push(`sort_by=${encodeURIComponent(field)}&sort_order=${dir === 'asc' ? 'asc' : 'desc'}`);
        } else if (field === 'status') {
            params.push(`sort_by=${encodeURIComponent(field)}&sort_order=${dir === 'asc' ? 'asc' : 'desc'}`);
        }
    }
    if (params.length) url += '&' + params.join('&');
    const res = await fetch(url, {
        headers: apiHeaders
    });
    const data = await res.json();
    if (data.success && data.data && data.data.data.length) {
        studentTableBody.innerHTML = '';
        let rowNum = (data.data.current_page - 1) * data.data.per_page + 1;
        window._students = data.data.data;
        if (currentSort) {
            const [field, dir] = currentSort.split('_');
            window._students.sort((a, b) => {
                let valA = '', valB = '';
                if (field === 'name') {
                    valA = (a.user && a.user.last_name ? a.user.last_name : '').toLowerCase();
                    valB = (b.user && b.user.last_name ? b.user.last_name : '').toLowerCase();
                } else if (field === 'student') {
                    valA = a.student_number || '';
                    valB = b.student_number || '';
                } else if (field === 'status') {
                    valA = a.user && a.user.status ? a.user.status : '';
                    valB = b.user && b.user.status ? b.user.status : '';
                }
                if (valA < valB) return dir === 'asc' ? -1 : 1;
                if (valA > valB) return dir === 'asc' ? 1 : -1;
                return 0;
            });
        }
        data.data.data.forEach((student, idx) => {
            // Always show the class name for the selected class, or list all classes joined by comma if not filtered
            let className = '';
            if (student.student_classes && student.student_classes.length > 0) {
                if (searchClass.value) {
                    const classObj = student.student_classes.find(sc => sc.class && sc.class.class_id == searchClass.value);
                    if (classObj && classObj.class) {
                        className = classObj.class.class_name;
                    }
                } else {
                    // Show all classes joined by comma
                    className = student.student_classes.map(sc => sc.class ? sc.class.class_name : '').filter(Boolean).join(', ');
                }
            }
            // Determine action button label and icon based on status
            let isInactiveOrGraduated = student.user && (student.user.status === 'inactive' || student.user.status === 'graduated');
            let actionLabel = isInactiveOrGraduated ? 'Activate' : 'Deactivate';
            let actionIcon = isInactiveOrGraduated ? 'fa-user-check' : 'fa-user-slash';
            let actionBtnClass = isInactiveOrGraduated ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600';
            studentTableBody.innerHTML += `
                <tr>
                    <td class="py-2 px-4 font-bold">${rowNum++}</td>
                    <td class="py-2 px-4 font-bold">${student.first_name} ${student.last_name}</td>
                    <td class="py-2 px-4 font-bold">${student.student_number}</td>
                    <td class="py-2 px-4 font-bold">${className}</td>
                    <td class="py-2 px-4 font-bold">${student.user && student.user.status ? student.user.status : ''}</td>
                    <td class="py-2 px-4 flex space-x-2">
                        <button class="view-btn bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded" title="View" data-idx="${idx}"><i class="fa-solid fa-eye"></i></button>
                        <button class="edit-btn bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded" title="Edit" data-idx="${idx}"><i class="fa-solid fa-pen"></i></button>
                        <button class="deactivate-btn ${actionBtnClass} text-white px-2 py-1 rounded" title="${actionLabel}" data-student-number="${student.student_number}" data-action="${actionLabel.toLowerCase()}"><i class="fa-solid ${actionIcon}"></i> ${actionLabel}</button>
                    </td>
                </tr>
            `;
        });
        attachActionEvents();
        currentPage = data.data.current_page;
        lastPage = data.data.last_page;
        renderPagination();
    } else {
        studentTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-400">No students found.</td></tr>';
        document.getElementById('pagination').innerHTML = '';
    }
}

function renderPagination() {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    if (lastPage <= 1) return;
    // First Page button
    const firstBtn = document.createElement('button');
    firstBtn.textContent = 'First Page';
    firstBtn.className = 'px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300';
    firstBtn.disabled = currentPage === 1;
    firstBtn.onclick = () => loadStudents(1);
    pagination.appendChild(firstBtn);
    // Previous button
    const prevBtn = document.createElement('button');
    prevBtn.textContent = 'Prev';
    prevBtn.className = 'px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300';
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => loadStudents(currentPage - 1);
    pagination.appendChild(prevBtn);
    // Current page number (centered)
    const pageNum = document.createElement('span');
    pageNum.textContent = currentPage;
    pageNum.className = 'px-4 py-1 rounded border bg-yellow-400 font-bold mx-2 flex items-center justify-center';
    pagination.appendChild(pageNum);
    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.textContent = 'Next';
    nextBtn.className = 'px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300';
    nextBtn.disabled = currentPage === lastPage;
    nextBtn.onclick = () => loadStudents(currentPage + 1);
    pagination.appendChild(nextBtn);
    // Last Page button
    const lastBtn = document.createElement('button');
    lastBtn.textContent = 'Last Page';
    lastBtn.className = 'px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300';
    lastBtn.disabled = currentPage === lastPage;
    lastBtn.onclick = () => loadStudents(lastPage);
    pagination.appendChild(lastBtn);
}

// Update event listeners to reload from page 1
[searchInput, searchClass, statusFilter, sortBy].forEach(input => {
    input.addEventListener('input', () => loadStudents(1));
    input.addEventListener('change', () => loadStudents(1));
});

// Initial load
loadClasses();
loadStudents();

// JS for view, edit, deactivate
let selectedStudent = null;
let selectedStudentNumber = null;

function openStudentModal(student, editable = false) {
    selectedStudent = student;
    const modal = document.getElementById('studentModal');
    const form = document.getElementById('studentInfoForm');
    const title = document.getElementById('studentModalTitle');
    const actions = document.getElementById('studentModalActions');
    const errorDiv = document.getElementById('studentModalError');
    const successDiv = document.getElementById('studentModalSuccess');
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    // Fill form
    form.student_number.value = student.student_number;
    form.email.value = student.email || '';
    form.last_name.value = student.last_name || '';
    form.first_name.value = student.first_name || '';
    form.middle_initial.value = student.middle_initial || '';
    form.course.value = student.course || '';
    form.year_level.value = student.year_level || '';
    form.section.value = student.section || '';
    form.academic_status.value = student.academic_status || '';
    form.status.value = student.status || '';
    // Set readonly
    [...form.elements].forEach(el => {
        if (el.name === 'student_number' || el.name === 'status') {
            el.readOnly = true;
        } else {
            el.readOnly = !editable;
        }
    });
    title.textContent = editable ? 'Edit Student' : 'Student Info';
    actions.style.display = editable ? '' : 'none';
    modal.classList.remove('hidden');
}
document.getElementById('closeStudentModal').onclick = () => document.getElementById('studentModal').classList.add('hidden');

// Save (edit) student
const studentInfoForm = document.getElementById('studentInfoForm');
studentInfoForm.onsubmit = async function(e) {
    e.preventDefault();
    const errorDiv = document.getElementById('studentModalError');
    const successDiv = document.getElementById('studentModalSuccess');
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    const formData = new FormData(studentInfoForm);
    const data = Object.fromEntries(formData.entries());
    try {
        const res = await fetch(`/api/students/${selectedStudent.student_number}`, {
            method: 'PUT',
            headers: { ...apiHeaders, 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (res.ok && result.success) {
            successDiv.textContent = 'Student updated successfully!';
            successDiv.classList.remove('hidden');
            loadStudents(currentPage);
            setTimeout(() => document.getElementById('studentModal').classList.add('hidden'), 1200);
        } else {
            errorDiv.textContent = result.message || 'Update failed.';
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'An error occurred. Please try again.';
        errorDiv.classList.remove('hidden');
    }
};

// Deactivate logic
let deactivateStudentNumber = null;
document.getElementById('cancelDeactivateBtn').onclick = () => document.getElementById('deactivateModal').classList.add('hidden');
document.getElementById('confirmDeactivateBtn').onclick = async function() {
    if (!deactivateStudentNumber) return;
    const modal = document.getElementById('deactivateModal');
    const action = modal.getAttribute('data-action');
    const newStatus = action === 'activate' ? 'active' : 'inactive';
    const res = await fetch(`/api/users/${deactivateStudentNumber}`, {
        method: 'PUT',
        headers: { ...apiHeaders, 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: newStatus })
    });
    if (res.ok) {
        loadStudents(currentPage);
        modal.classList.add('hidden');
    }
};

// Attach button events after rendering table
function attachActionEvents() {
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.onclick = function() {
            const idx = this.getAttribute('data-idx');
            openStudentModal(window._students[idx], false);
        };
    });
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = function() {
            const idx = this.getAttribute('data-idx');
            openStudentModal(window._students[idx], true);
        };
    });
    document.querySelectorAll('.deactivate-btn').forEach(btn => {
        btn.onclick = function() {
            deactivateStudentNumber = this.getAttribute('data-student-number');
            const action = this.getAttribute('data-action');
            // Update modal text based on action
            const modal = document.getElementById('deactivateModal');
            const title = modal.querySelector('h2');
            const desc = modal.querySelector('p');
            if (action === 'activate') {
                title.textContent = 'Activate Student Account?';
                desc.textContent = 'This will allow the student to log in again. Are you sure?';
            } else {
                title.textContent = 'Deactivate Student Account?';
                desc.textContent = 'This will prevent the student from logging in. Are you sure?';
            }
            // Update confirm button label and color
            const confirmBtn = document.getElementById('confirmDeactivateBtn');
            if (action === 'activate') {
                confirmBtn.textContent = 'Activate';
                confirmBtn.className = 'px-4 py-2 rounded bg-green-500 hover:bg-green-600 text-white font-bold';
            } else {
                confirmBtn.textContent = 'Deactivate';
                confirmBtn.className = 'px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white font-bold';
            }
            modal.setAttribute('data-action', action);
            modal.classList.remove('hidden');
        };
    });
}

// Profile dropdown logic and user info
document.addEventListener('DOMContentLoaded', function() {
    const userData = localStorage.getItem('user_data');
    const userRoles = localStorage.getItem('user_roles');
    if (userData) {
        const user = JSON.parse(userData);
        document.getElementById('userName').textContent = `${user.first_name} ${user.last_name}`;
        document.getElementById('userNumber').textContent = user.student_number;
        document.getElementById('dropdownName').textContent = `${user.first_name} ${user.last_name}`;
        document.getElementById('dropdownNumber').textContent = user.student_number;
        document.getElementById('dropdownEmail').textContent = user.email || 'No email';
    }
    // Show Officer button if user has a role with role_priority <= 20
    if (userRoles) {
        try {
            const roles = JSON.parse(userRoles);
            const hasOfficerAccess = roles.some(r => Number(r.role_priority) <= 20);
            if (hasOfficerAccess) {
                document.getElementById('officerBtn').style.display = '';
            }
        } catch (e) {}
    }
    // Handle logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
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
    if (profileArea && profileDropdown && chevronIcon) {
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
    }
    // Officer/Student button logic
    const studentBtn = document.getElementById('studentBtn');
    if (studentBtn) {
        // Show the button if on officer view
        studentBtn.style.display = '';
        studentBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            window.location.href = '/student-dashboard';
        });
    }
    // Remove logout button from dropdown (handled in sidebar)
    const dropdownLogoutBtn = document.querySelector('#profileDropdown #logoutBtn');
    if (dropdownLogoutBtn) {
        dropdownLogoutBtn.parentNode.removeChild(dropdownLogoutBtn);
    }
});

// Sort by logic
sortBy.addEventListener('change', function() {
    currentSort = this.value;
    loadStudents(1);
});
</script>
</body>
</html> 