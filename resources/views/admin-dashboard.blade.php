<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Support Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Support Ticket Dashboard</h1>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">Admin: <strong id="adminName"></strong></span>
                    <button onclick="logout()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Logout
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Login Form (shown when not authenticated) -->
            <div id="loginForm" class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Admin Login</h2>
                <div id="loginError"
                    class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
                <form onsubmit="login(event)">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="loginEmail" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="loginPassword" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                        Login
                    </button>
                </form>
            </div>

            <!-- Dashboard (shown when authenticated) -->
            <div id="dashboard" class="hidden">
                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <div class="flex gap-4 items-center">
                        <label class="text-sm font-medium text-gray-700">Filter by Status:</label>
                        <select id="statusFilter" onchange="filterTickets()"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="OPEN">Open</option>
                            <option value="IN_PROGRESS">In Progress</option>
                            <option value="RESOLVED">Resolved</option>
                            <option value="CLOSED">Closed</option>
                        </select>
                        <button onclick="loadTickets()"
                            class="ml-auto bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Refresh
                        </button>
                    </div>
                </div>

                <!-- Tickets Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ticket ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created By</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ticketsBody" class="bg-white divide-y divide-gray-200">
                            <!-- Tickets will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Ticket Detail Modal -->
    <div id="ticketModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-3xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-semibold" id="modalTicketId"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="space-y-4">
                <!-- Content loaded dynamically -->
            </div>

            <!-- Update Status Form -->
            <div class="mt-6 pt-6 border-t">
                <h4 class="font-semibold mb-3">Update Ticket</h4>
                <form onsubmit="updateTicket(event)">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="updateStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="OPEN">Open</option>
                                <option value="IN_PROGRESS">In Progress</option>
                                <option value="RESOLVED">Resolved</option>
                                <option value="CLOSED">Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign To (Email)</label>
                            <input type="email" id="assignTo" placeholder="admin@example.com"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Internal Note</label>
                        <textarea id="internalNote" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Update Ticket
                    </button>
                </form>
            </div>

            <!-- Add Comment Form -->
            <div class="mt-6 pt-6 border-t">
                <h4 class="font-semibold mb-3">Add Comment</h4>
                <form onsubmit="addComment(event)">
                    <textarea id="commentContent" rows="3" required placeholder="Enter your comment..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2"></textarea>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" id="isInternal" class="mr-2">
                            <span class="text-sm text-gray-700">Internal Comment (Admin Only)</span>
                        </label>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Add Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    const API_BASE = '/api/v1';
    let authToken = localStorage.getItem('auth_token');
    let currentTicketId = null;
    let allTickets = [];

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        if (authToken) {
            showDashboard();
            loadTickets();
        } else {
            showLoginForm();
        }
    });

    // Authentication
    async function login(event) {
        event.preventDefault();
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        try {
            const response = await fetch(`${API_BASE}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email,
                    password
                })
            });

            const data = await response.json();

            if (response.ok) {
                authToken = data.token;
                localStorage.setItem('auth_token', authToken);
                localStorage.setItem('user_name', data.user.name);
                showDashboard();
                loadTickets();
            } else {
                showError('loginError', data.message || 'Login failed');
            }
        } catch (error) {
            showError('loginError', 'Network error. Please try again.');
        }
    }

    function logout() {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_name');
        authToken = null;
        showLoginForm();
    }

    function showLoginForm() {
        document.getElementById('loginForm').classList.remove('hidden');
        document.getElementById('dashboard').classList.add('hidden');
    }

    function showDashboard() {
        document.getElementById('loginForm').classList.add('hidden');
        document.getElementById('dashboard').classList.remove('hidden');
        document.getElementById('adminName').textContent = localStorage.getItem('user_name');
    }

    // Load Tickets
    async function loadTickets() {
        try {
            const response = await fetch(`${API_BASE}/tickets`, {
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            });

            if (response.ok) {
                allTickets = await response.json();
                filterTickets();
            }
        } catch (error) {
            console.error('Failed to load tickets:', error);
        }
    }

    function filterTickets() {
        const statusFilter = document.getElementById('statusFilter').value;
        const filtered = statusFilter ?
            allTickets.filter(t => t.status === statusFilter) :
            allTickets;

        renderTickets(filtered);
    }

    function renderTickets(tickets) {
        const tbody = document.getElementById('ticketsBody');

        if (tickets.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No tickets found</td></tr>';
            return;
        }

        tbody.innerHTML = tickets.map(ticket => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${ticket.id || ticket.ticket_id || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${ticket.subject || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            ${getStatusColor(ticket.status)}">${ticket.status}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            ${getPriorityColor(ticket.priority)}">${ticket.priority}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${ticket.category}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${ticket.created_by || ticket.creator?.email || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button onclick="viewTicket('${ticket.id || ticket.ticket_id}')" 
                            class="text-blue-600 hover:text-blue-900">View Details</button>
                    </td>
                </tr>
            `).join('');
    }

    // View Ticket Details
    async function viewTicket(ticketId) {
        currentTicketId = ticketId;

        try {
            const response = await fetch(`${API_BASE}/tickets/${ticketId}`, {
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            });

            if (response.ok) {
                const ticket = await response.json();
                showTicketModal(ticket);
            } else {
                alert('Failed to load ticket details');
            }
        } catch (error) {
            console.error('Failed to load ticket:', error);
            alert('Error loading ticket details');
        }
    }

    function showTicketModal(ticket) {
        document.getElementById('modalTicketId').textContent = ticket.id || ticket.ticket_id;
        document.getElementById('updateStatus').value = ticket.status;

        // Handle assigned_to - could be email string or object
        let assigneeEmail = '';
        if (typeof ticket.assigned_to === 'string') {
            assigneeEmail = ticket.assigned_to;
        } else if (ticket.assignee?.email) {
            assigneeEmail = ticket.assignee.email;
        }
        document.getElementById('assignTo').value = assigneeEmail;

        const content = `
                <div class="space-y-3">
                    <div><strong>Subject:</strong> ${ticket.subject}</div>
                    <div><strong>Description:</strong> ${ticket.description || 'N/A'}</div>
                    <div><strong>Priority:</strong> <span class="px-2 py-1 text-xs rounded ${getPriorityColor(ticket.priority)}">${ticket.priority}</span></div>
                    <div><strong>Category:</strong> ${ticket.category}</div>
                    <div><strong>Created By:</strong> ${ticket.created_by || ticket.creator?.email || 'N/A'}</div>
                    <div><strong>Assigned To:</strong> ${assigneeEmail || '<span class="text-gray-400">Unassigned</span>'}</div>
                    <div><strong>Created:</strong> ${new Date(ticket.created_at).toLocaleString()}</div>
                </div>
                
                ${ticket.comments && ticket.comments.length > 0 ? `
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="font-semibold mb-3">Comments (${ticket.comments.length})</h4>
                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            ${ticket.comments.map(comment => `
                                <div class="bg-gray-50 p-3 rounded ${comment.is_internal ? 'border-l-4 border-yellow-500' : ''}">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span><strong>${comment.user || comment.user?.name || 'Unknown'}</strong></span>
                                        <span>${new Date(comment.created_at).toLocaleString()}</span>
                                    </div>
                                    <p class="text-sm">${comment.content}</p>
                                    ${comment.is_internal ? '<span class="text-xs text-yellow-700 font-semibold">Internal</span>' : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            `;

        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('ticketModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('ticketModal').classList.add('hidden');
        document.getElementById('internalNote').value = '';
        document.getElementById('commentContent').value = '';
        currentTicketId = null;
    }

    // Update Ticket
    async function updateTicket(event) {
        event.preventDefault();

        const data = {
            status: document.getElementById('updateStatus').value,
        };

        const assignTo = document.getElementById('assignTo').value;
        if (assignTo) data.assigned_to = assignTo;

        const internalNote = document.getElementById('internalNote').value;
        if (internalNote) data.internal_note = internalNote;

        try {
            const response = await fetch(`${API_BASE}/admin/tickets/${currentTicketId}`, {
                method: 'PATCH',
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Ticket updated successfully!');
                closeModal();
                loadTickets();
            }
        } catch (error) {
            console.error('Failed to update ticket:', error);
        }
    }

    // Add Comment
    async function addComment(event) {
        event.preventDefault();

        const content = document.getElementById('commentContent').value;
        const isInternal = document.getElementById('isInternal').checked;

        try {
            const response = await fetch(`${API_BASE}/tickets/${currentTicketId}/comments`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    content,
                    is_internal: isInternal
                })
            });

            if (response.ok) {
                alert('Comment added successfully!');
                document.getElementById('commentContent').value = '';
                document.getElementById('isInternal').checked = false;
                viewTicket(currentTicketId); // Reload ticket details
            }
        } catch (error) {
            console.error('Failed to add comment:', error);
        }
    }

    // Helper Functions
    function getStatusColor(status) {
        const colors = {
            'OPEN': 'bg-blue-100 text-blue-800',
            'IN_PROGRESS': 'bg-yellow-100 text-yellow-800',
            'RESOLVED': 'bg-green-100 text-green-800',
            'CLOSED': 'bg-gray-100 text-gray-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }

    function getPriorityColor(priority) {
        const colors = {
            'LOW': 'bg-gray-100 text-gray-800',
            'MEDIUM': 'bg-blue-100 text-blue-800',
            'HIGH': 'bg-orange-100 text-orange-800',
            'URGENT': 'bg-red-100 text-red-800'
        };
        return colors[priority] || 'bg-gray-100 text-gray-800';
    }

    function showError(elementId, message) {
        const element = document.getElementById(elementId);
        element.textContent = message;
        element.classList.remove('hidden');
        setTimeout(() => element.classList.add('hidden'), 5000);
    }
    </script>
</body>

</html>