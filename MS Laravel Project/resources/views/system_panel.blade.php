<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Administration - Mentor's Society</title>
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
        .modal-bg {
            background: rgba(0,0,0,0.3);
        }
        .edit-icon-btn {
            background: #f1f8ff;
            border-radius: 50%;
            padding: 6px;
            margin-left: 8px;
            border: none;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px rgba(33,150,243,0.07);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .edit-icon-btn:hover {
            background: #e3f2fd;
            box-shadow: 0 2px 8px rgba(33,150,243,0.13);
        }
        .edit-icon-btn i {
            color: #2196f3;
            font-size: 1.1em;
        }
        .edit-icon-btn[title]:hover:after {
            content: attr(title);
            position: absolute;
            left: 110%;
            top: 50%;
            transform: translateY(-50%);
            background: #22223b;
            color: #fff;
            font-size: 0.95em;
            padding: 3px 10px;
            border-radius: 6px;
            white-space: nowrap;
            z-index: 100;
            opacity: 0.95;
            pointer-events: none;
        }
        .officer-na {
            font-weight: bold;
            color: #22223b;
        }
        .system-card {
            background: linear-gradient(120deg, #fff 80%, #f1f8ff 100%);
            border-radius: 22px;
            box-shadow: 0 6px 32px rgba(33,150,243,0.07), 0 1.5px 6px rgba(0,0,0,0.04);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            margin-bottom: 3.5rem;
            max-width: 100%;
            width: 100%;
            border-left: 7px solid #ffd600;
        }
        .system-table th, .system-table td {
            border-bottom: 2px solid #ececec;
            padding: 1rem 1.5rem;
            font-size: 1.08em;
        }
        .system-table th {
            background: #f7f7f7;
            font-weight: bold;
            color: #22223b;
            letter-spacing: 0.02em;
        }
        .system-table td {
            background: #fff;
            font-weight: 500;
            color: #22223b;
        }
        .system-table tr:last-child td {
            border-bottom: none;
        }
        .system-section-title {
            font-size: 1.45rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #22223b;
        }
        .fixed-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 40;
            width: 16rem;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
        }
        .fixed-header {
            position: fixed;
            top: 0;
            left: 16rem;
            right: 0;
            z-index: 30;
            background: #ffd600;
            height: 72px;
            display: flex;
            align-items: center;
            padding-left: 2.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .main-content-scroll {
            margin-left: 16rem;
            margin-top: 72px;
            padding: 2.5rem 2rem 0 2rem;
            min-height: calc(100vh - 72px);
            background: #f8f6f4;
            width: calc(100vw - 16rem);
            max-width: 100vw;
        }
        @media (min-width: 900px) {
            .system-card {
                width: 95%;
                margin-left: auto;
                margin-right: auto;
            }
        }
        .modern-modal {
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(33,150,243,0.13), 0 2px 8px rgba(0,0,0,0.08);
            background: #fff;
            max-width: 420px;
            width: 100%;
            position: relative;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            animation: fadeInModal 0.25s ease;
        }
        @keyframes fadeInModal {
            from { opacity: 0; transform: translateY(30px) scale(0.98); }
            to { opacity: 1; transform: none; }
        }
        .modern-modal-title {
            font-size: 1.35rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            color: #22223b;
            letter-spacing: -0.01em;
        }
        .modern-modal-close {
            position: absolute;
            top: 1.1rem;
            right: 1.1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #888;
            cursor: pointer;
            transition: color 0.18s;
        }
        .modern-modal-close:hover {
            color: #22223b;
        }
        .modern-modal-input-wrap {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .modern-modal-input {
            width: 100%;
            padding: 0.85rem 1.1rem 0.85rem 2.5rem;
            border-radius: 10px;
            border: 1.5px solid #e0e0e0;
            font-size: 1.08em;
            background: #fafbfc;
            transition: border 0.18s, box-shadow 0.18s;
        }
        .modern-modal-input:focus {
            border: 1.5px solid #ffd600;
            outline: none;
            box-shadow: 0 0 0 2px #fffde7;
            background: #fff;
        }
        .modern-modal-input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #bdbdbd;
            font-size: 1.1em;
            pointer-events: none;
        }
        .modern-modal-btn {
            width: 100%;
            padding: 0.95rem 0;
            border-radius: 10px;
            font-size: 1.08em;
            font-weight: 700;
            border: none;
            margin-bottom: 1rem;
            background: #ffd600;
            color: #22223b;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
            box-shadow: 0 1.5px 6px rgba(255,214,0,0.07);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .modern-modal-btn:hover {
            background: #ffe066;
            color: #111;
        }
        .modern-modal-btn-cancel {
            background: #ff4d4f;
            color: #fff;
            border: none;
            margin-bottom: 0;
        }
        .modern-modal-btn-cancel:hover {
            background: #d32f2f;
            color: #fff;
        }
        .modern-modal-results {
            margin-bottom: 1.2rem;
        }
        .edit-icon {
            color: #2196f3 !important;
        }
    </style>
</head>
<body class="bg-[#f8f6f4] min-h-screen">
<div class="flex min-h-screen">
    <!-- Sidebar Navigation (inline) -->
    <aside class="fixed-sidebar py-6 px-4">
        <div class="flex flex-col items-center mb-8">
            <img src="/ms_logo.png" alt="Logo" class="w-16 h-16 mb-2" />
            <span class="font-extrabold text-lg text-gray-800 tracking-wide">Mentor's Society</span>
        </div>
        <nav class="flex-1">
            <ul class="space-y-2">
                <li><a href="/dashboard" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="/students" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-user-graduate"></i> Student</a></li>
                <li><a href="/events" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-calendar-days"></i> Event</a></li>
                <li><a href="/financial" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-coins"></i> Financial</a></li>
                <li><a href="/inventory" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-boxes-stacked"></i> Inventory</a></li>
                <li><a href="/reports" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg"><span class="sidebar-indicator"></span><i class="fa-solid fa-file-lines"></i> Report</a></li>
                <li><a href="/system-panel" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-[#22223b]"><span class="sidebar-indicator"></span><i class="fa-solid fa-gear"></i> System</a></li>
            </ul>
        </nav>
        <button id="logoutBtn" class="mt-auto flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-lg text-red-600 hover:bg-red-50 transition-colors"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
    </aside>
    <!-- Fixed Header -->
    <div class="fixed-header">
        <h1 class="text-3xl font-extrabold text-[#22223b]">System Administration</h1>
    </div>
    <!-- Main Content -->
    <main class="main-content-scroll">
        <!-- Roles & Permissions Table -->
        <div class="system-card">
            <h2 class="system-section-title">Roles & Permissions</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left system-table">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 font-bold">Role</th>
                            <th class="px-6 py-3 font-bold">Current Officer</th>
                            <th class="px-6 py-3 font-bold">Permissions</th>
                        </tr>
                    </thead>
                    <tbody id="rolesTableBody">
                        <!-- Dynamic rows here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Presidency Transfer Panel -->
        <div class="system-card">
            <h2 class="system-section-title">Presidency Transfer</h2>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Select New President</label>
                <div class="flex items-center gap-2">
                    <input type="text" placeholder="Search Using Student ID" class="w-full px-4 py-2 border rounded-lg" id="searchPresidentInput">
                    <button id="searchPresidentBtn" class="bg-gray-200 px-4 py-2 rounded-lg"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <ul id="presidentSearchResults" class="mt-4"></ul>
        </div>
    </main>
</div>

<!-- Edit Officer Modal (modernized) -->
<div id="editOfficerModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-bg absolute inset-0" onclick="closeModal('editOfficerModal')"></div>
    <div class="modern-modal">
        <button class="modern-modal-close" onclick="closeModal('editOfficerModal')" title="Close">&times;</button>
        <div class="modern-modal-title">Assign Officer to <span id="editOfficerRoleName"></span></div>
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-id-card modern-modal-input-icon"></i>
            <input type="text" placeholder="Search by Student Number" class="modern-modal-input flex-1" id="officerSearchInput" style="padding-left:2.5rem;">
            <button id="officerSearchBtn" type="button" style="background:#ffd600; border:none; border-radius:50%; width:44px; height:44px; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:background 0.18s; margin-left:4px;">
                <i class="fa-solid fa-magnifying-glass" style="color:#22223b; font-size:1.2em;"></i>
            </button>
        </div>
        <div id="officerSearchResults"></div>
        <div id="officerExtraFields" class="mb-2" style="display:none;"></div>
        <div id="officerActionBtns" class="flex flex-col sm:flex-row gap-2 mt-2">
            <button id="assignOfficerBtn" class="modern-modal-btn flex-1 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold" disabled>Assign</button>
            <button class="modern-modal-btn modern-modal-btn-cancel flex-1" onclick="closeModal('editOfficerModal')">Cancel</button>
        </div>
    </div>
</div>

<!-- Edit Permission Modal (dynamic) -->
<div id="editPermissionModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-bg absolute inset-0" onclick="closeModal('editPermissionModal')"></div>
    <div class="bg-white rounded-lg shadow-lg p-8 z-10 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Edit Permissions for <span id="editPermissionRoleName"></span></h3>
        <form id="permissionForm" class="mb-4 max-h-64 overflow-y-auto"></form>
        <button id="savePermissionBtn" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-4 py-2 rounded-lg w-full mb-2">Save</button>
        <button class="bg-gray-200 px-4 py-2 rounded-lg w-full" onclick="closeModal('editPermissionModal')">Cancel</button>
    </div>
</div>

<!-- Presidency Transfer Confirmation Modal (dynamic) -->
<div id="presidencyTransferModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="modal-bg absolute inset-0" onclick="closeModal('presidencyTransferModal')"></div>
    <div class="bg-white rounded-lg shadow-lg p-8 z-10 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Confirm Presidency Transfer</h3>
        <p class="mb-4">Are you sure you want to transfer the presidency to <span id="newPresidentName">[Name]</span>?</p>
        <div class="mb-4">
            <label class="block font-semibold mb-2">Set Academic Year</label>
            <select id="academicYearSelect" class="w-full px-4 py-2 border rounded-lg mb-2">
                <option value="2024-2025">2024-2025</option>
                <option value="new">Input new academic year</option>
            </select>
            <input type="text" id="newAcademicYearInput" placeholder="New Academic Year (if selected)" class="w-full px-4 py-2 border rounded-lg hidden">
        </div>
        <button id="confirmTransferBtn" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-4 py-2 rounded-lg w-full mb-2">Confirm Transfer</button>
        <button class="bg-gray-200 px-4 py-2 rounded-lg w-full" onclick="closeModal('presidencyTransferModal')">Cancel</button>
    </div>
</div>

<script>
const API_BASE = '/api';
let rolesList = [
    { name: 'VPI' },
    { name: 'VPX' },
    { name: 'Secretary General' },
    { name: 'Assistant Secretary' },
    { name: 'Treasurer' },
    { name: 'Auditor' },
    { name: 'P.R.O - Math' },
    { name: 'P.R.O - English' },
    { name: 'Business Manager - Math' },
    { name: 'Business Manager - English' },
    { name: 'MS Representative' },
    { name: 'Student' },
    { name: 'Class President' }
];
let permissionsAll = [];
let rolesData = [];
let presidentAcademicYearId = null;

// Default permissions for each role (canonical names)
const defaultRolePermissions = {
    'VPI': [
        'user.view','user.create','user.edit','user.delete',
        'student.view','student.create','student.edit','student.delete','student.manage_classes','student.import_csv',
        'event.view','event.create','event.edit','event.delete','event.manage_registrations','event.manage_participants','event.view_evaluations',
        'financial.view','financial.create','financial.edit','financial.delete','financial.verify','financial.export','financial.view_receipts',
        'inventory.view','inventory.create','inventory.edit','inventory.delete','inventory.manage_borrowings','inventory.record_conditions','inventory.export',
        'academic_year.view','academic_year.create','academic_year.edit','academic_year.delete',
        'class.view','class.create','class.edit','class.delete',
        'task.view','task.create','task.edit','task.delete','task.assign','task.complete',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'VPX': [
        'event.view','event.create','event.edit','event.delete','event.manage_registrations','event.manage_participants','event.view_evaluations',
        'sponsor.view','sponsor.create','sponsor.edit','sponsor.delete','sponsor.assign_events',
        'task.view','task.create','task.edit','task.delete','task.assign','task.complete',
        'financial.view','financial.create','financial.edit','financial.delete',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'Secretary General': [
        'user.view','user.create','user.edit','user.delete',
        'student.view','student.create','student.edit','student.delete','student.manage_classes','student.import_csv',
        'event.view','event.create','event.edit','event.delete','event.manage_registrations','event.manage_participants','event.view_evaluations',
        'task.view','task.create','task.edit','task.delete','task.assign','task.complete',
        'academic_year.view','academic_year.create','academic_year.edit','academic_year.delete',
        'class.view','class.create','class.edit','class.delete',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'Assistant Secretary': [
        'user.view','user.edit',
        'student.view','student.edit',
        'event.view','event.manage_registrations',
        'task.view','task.complete',
        'academic_year.view','academic_year.create','academic_year.edit','academic_year.delete',
        'class.view','class.create','class.edit','class.delete',
        'report.view'
    ],
    'Treasurer': [
        'financial.view','financial.create','financial.edit','financial.delete','financial.verify','financial.export','financial.view_receipts',
        'event.view','event.view_evaluations',
        'sponsor.view','sponsor.create','sponsor.edit','sponsor.delete','sponsor.assign_events',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'Auditor': [
        'financial.verify','financial.export',
        'inventory.record_conditions',
        'event.view_evaluations',
        'system.view_logs',
        'report.export'
    ],
    'P.R.O - Math': [
        'event.view','event.create','event.edit','event.delete','event.manage_registrations','event.manage_participants','event.view_evaluations',
        'sponsor.view','sponsor.create','sponsor.edit','sponsor.delete','sponsor.assign_events',
        'task.view','task.create','task.edit','task.delete','task.assign','task.complete',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'P.R.O - English': [
        'event.view','event.create','event.edit','event.delete','event.manage_registrations','event.manage_participants','event.view_evaluations',
        'sponsor.view','sponsor.create','sponsor.edit','sponsor.delete','sponsor.assign_events',
        'task.view','task.create','task.edit','task.delete','task.assign','task.complete',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'Business Manager - Math': [
        'inventory.view','inventory.create','inventory.edit','inventory.delete','inventory.manage_borrowings','inventory.record_conditions','inventory.export',
        'financial.view','financial.create','financial.edit','financial.delete',
        'sponsor.view','sponsor.create','sponsor.edit','sponsor.delete','sponsor.assign_events',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'Business Manager - English': [
        'inventory.view','inventory.create','inventory.edit','inventory.delete','inventory.manage_borrowings','inventory.record_conditions','inventory.export',
        'financial.view','financial.create','financial.edit','financial.delete',
        'sponsor.view','sponsor.create','sponsor.edit','sponsor.delete','sponsor.assign_events',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'MS Representative': [
        'event.view','event.create','event.edit','event.delete','event.manage_registrations','event.manage_participants','event.view_evaluations',
        'task.view','task.create','task.edit','task.delete','task.assign','task.complete',
        'report.view','report.generate','report.export','report.dashboard'
    ],
    'Student': [
        'event.view','task.complete','inventory.view'
    ],
    'Class President': [
        'class.edit_own','class.manage_students','class.manage_subjects','class.manage_schedules','class.manage_professors'
    ]
};

// Utility: fetch with auth
async function apiFetch(url, options = {}) {
    const token = localStorage.getItem('auth_token') || '';
    options.headers = Object.assign({
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }, options.headers || {});
    return fetch(url, options);
}

// Load all permissions for editing
async function loadAllPermissions() {
    const permRes = await apiFetch(`${API_BASE}/roles/permissions/all`);
    const permJson = await permRes.json();
    permissionsAll = permJson.data || [];
}

// Load and display roles table
async function loadRolesTable() {
    await loadAllPermissions();
    const tbody = document.getElementById('rolesTableBody');
    tbody.innerHTML = '';
    for (const role of rolesList) {
        // Get role info from API (to get role_id)
        let roleApi = await getRoleByName(role.name);
        let role_id = roleApi ? roleApi.role_id : null;
        // Get current officer for this role
        let officer = role_id ? await getCurrentOfficerForRole(roleApi) : null;
        // Get permissions for this role
        let perms = role_id ? await getPermissionsForRole(role_id) : [];
        let permSummary = summarizePermissions(role.name, perms);
        tbody.innerHTML += `
        <tr>
            <td class="">${role.name}</td>
            <td class="">
                ${officer ? `<span>${officer.first_name} ${officer.last_name} <span class='text-xs text-gray-500'>(${officer.student_number})</span></span>` : `<span class='officer-na'>N/A</span>`}
                <i class="fa-solid fa-pen-to-square edit-icon" title="Edit Officer" onclick="openEditOfficerModal('${role_id ? role_id : ''}', '${role.name}')"></i>
            </td>
            <td class="">
                ${permSummary}
                <i class="fa-solid fa-pen-to-square edit-icon" title="Edit Permissions" onclick="openEditPermissionModal('${role_id ? role_id : ''}', '${role.name}')"></i>
            </td>
        </tr>`;
    }
}

function summarizePermissions(roleName, perms) {
    return 'Edit Permissions';
}

async function getRoleByName(roleName) {
    // Get all roles from API and find by name
    if (!rolesData.length) {
        const res = await apiFetch(`${API_BASE}/roles?per_page=100`);
        const json = await res.json();
        rolesData = json.data.data || json.data;
    }
    return rolesData.find(r => r.role_name.toLowerCase() === roleName.toLowerCase());
}

async function getCurrentOfficerForRole(role) {
    if (!role) return null;
    const res = await apiFetch(`${API_BASE}/users/role/${encodeURIComponent(role.role_name)}`);
    const json = await res.json();
    if (!json.success || !Array.isArray(json.data)) return null;
    let officer = json.data.find(u => u.is_active);
    return officer || json.data[0] || null;
}

async function getPermissionsForRole(role_id) {
    const res = await apiFetch(`${API_BASE}/roles/${role_id}/permissions`);
    const json = await res.json();
    return json.data || [];
}

// Edit Officer Modal logic
window.openEditOfficerModal = async function(role_id, role_name) {
    selectedRole = { role_id, role_name };
    document.getElementById('editOfficerRoleName').textContent = role_name;
    document.getElementById('editOfficerModal').classList.remove('hidden');
    document.getElementById('officerSearchInput').value = '';
    document.getElementById('officerSearchResults').innerHTML = '';
    document.getElementById('officerActionBtns').style.display = 'flex';
    const assignBtn = document.getElementById('assignOfficerBtn');
    assignBtn.disabled = true;
    assignBtn.classList.remove('hidden');
    selectedOfficerUser = null;
    document.getElementById('officerExtraFields').innerHTML = '';
    document.getElementById('officerExtraFields').style.display = 'none';
    await fetchPresidentAcademicYear();
};

// Edit Permission Modal logic
window.openEditPermissionModal = async function(role_id, role_name) {
    selectedPermissionRole = { role_id, role_name };
    document.getElementById('editPermissionRoleName').textContent = role_name;
    // Load permissions for this role
    let perms = await getPermissionsForRole(role_id);
    // Show all permissions, but only default ones checked by default
    let defaultPerms = defaultRolePermissions[role_name] || [];
    let form = document.getElementById('permissionForm');
    form.innerHTML = '';
    for (const perm of permissionsAll) {
        // Checked if this permission is currently assigned to the role OR is a default for this role
        let checked = perms.some(p => p.permission_id === perm.permission_id) || defaultPerms.includes(perm.permission_name);
        form.innerHTML += `<label class='flex items-center gap-2 mb-2'><input type='checkbox' name='permissions' value='${perm.permission_id}' ${checked ? 'checked' : ''}> ${perm.permission_name.replace(/\./g, ' ')}</label>`;
    }
    document.getElementById('editPermissionModal').classList.remove('hidden');
};

// Officer search and assign
const officerSearchBtn = document.getElementById('officerSearchBtn');
officerSearchBtn.onclick = async function(e) {
    e.preventDefault();
    const q = document.getElementById('officerSearchInput').value.trim();
    if (!q) return;
    const res = await apiFetch(`${API_BASE}/users/search?q=${encodeURIComponent(q)}`);
    const json = await res.json();
    const results = json.data || [];
    const resultsDiv = document.getElementById('officerSearchResults');
    const actionBtns = document.getElementById('officerActionBtns');
    resultsDiv.innerHTML = '';
    actionBtns.style.display = 'flex';
    selectedOfficerUser = null;
    const assignBtn = document.getElementById('assignOfficerBtn');
    assignBtn.disabled = true;
    if (results.length === 1) {
        const user = results[0];
        resultsDiv.innerHTML = `<div class='officer-select-item bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-2 font-semibold text-lg flex items-center' style='transition:background 0.2s;'>${user.first_name} ${user.last_name} <span class='text-xs text-gray-500 ml-2'>(${user.student_number})</span></div>`;
        selectedOfficerUser = user;
        assignBtn.disabled = false;
    } else if (results.length > 1) {
        resultsDiv.innerHTML = results.map(user => `<div class='officer-select-item bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-2 cursor-pointer font-semibold text-lg flex items-center' style='transition:background 0.2s;'>${user.first_name} ${user.last_name} <span class='text-xs text-gray-500 ml-2'>(${user.student_number})</span></div>`).join('');
        document.querySelectorAll('.officer-select-item').forEach((item, idx) => {
            item.onclick = function() {
                document.querySelectorAll('.officer-select-item').forEach(el => el.style.background = '#e3f2fd');
                item.style.background = '#bbdefb';
                selectedOfficerUser = results[idx];
                assignBtn.disabled = false;
            };
        });
    } else {
        resultsDiv.innerHTML = `<div class='text-red-500 font-semibold mt-2'>No user found with that ID.</div>`;
        assignBtn.disabled = true;
    }
};
const assignOfficerBtn = document.getElementById('assignOfficerBtn');
assignOfficerBtn.style.display = '';
assignOfficerBtn.disabled = true;
assignOfficerBtn.onclick = async function() {
    if (!selectedOfficerUser || !selectedRole) return;
    if (!presidentAcademicYearId) {
        alert('No academic year found for the current president.');
        return;
    }
    const start_date = new Date().toISOString().slice(0, 10);
    const end_date = null;
    const body = JSON.stringify({ role_id: selectedRole.role_id, academic_year_id: presidentAcademicYearId, start_date, end_date });
    const res = await apiFetch(`${API_BASE}/users/${selectedOfficerUser.student_number}/roles`, { method: 'POST', body });
    if (res.ok) {
        closeModal('editOfficerModal');
        loadRolesTable();
    } else {
        alert('Failed to assign officer.');
    }
};

// Permission save
const savePermissionBtn = document.getElementById('savePermissionBtn');
savePermissionBtn.onclick = async function() {
    if (!selectedPermissionRole) return;
    const form = document.getElementById('permissionForm');
    const checked = Array.from(form.querySelectorAll('input[name="permissions"]:checked')).map(cb => parseInt(cb.value));
    const res = await apiFetch(`${API_BASE}/roles/${selectedPermissionRole.role_id}/permissions`, {
        method: 'POST',
        body: JSON.stringify({ permissions: checked })
    });
    if (res.ok) {
        closeModal('editPermissionModal');
        loadRolesTable();
    } else {
        alert('Failed to update permissions.');
    }
};

// Presidency transfer logic (unchanged)
// ...

// Modal close utility
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

async function fetchPresidentAcademicYear() {
    try {
        const res = await apiFetch('/api/me');
        if (res.ok) {
            const json = await res.json();
            // If roles are not in /api/me, fetch from /api/users/role/President
            if (json.data && json.data.roles) {
                const presidentRole = json.data.roles.find(r => r.role_name === 'President');
                if (presidentRole) {
                    presidentAcademicYearId = presidentRole.academic_year_id;
                }
            } else {
                // fallback: fetch from /api/users/role/President
                const res2 = await apiFetch('/api/users/role/President');
                if (res2.ok) {
                    const json2 = await res2.json();
                    const currentUser = localStorage.getItem('student_number');
                    const user = json2.data.find(u => u.student_number === currentUser);
                    if (user && user.roles && user.roles.length > 0) {
                        const presidentRole = user.roles.find(r => r.role_name === 'President');
                        if (presidentRole) {
                            presidentAcademicYearId = presidentRole.academic_year_id;
                        }
                    }
                }
            }
        }
    } catch (e) { presidentAcademicYearId = null; }
}

// Initial load
loadRolesTable();
</script>
</body>
</html> 