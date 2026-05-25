<div id="logsPage" class="fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Activity Logs</h3>
            <p class="text-sm text-slate-500 mt-0.5">Track all your account activities</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 mb-6 flex flex-wrap gap-3 items-center">
        <label class="text-sm font-medium text-slate-600">Filter by:</label>
        <select id="filterActionType" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Actions</option>
        </select>
        <select id="filterDateRange" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="">All time</option>
        </select>
        <button id="applyLogFilter" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Apply
        </button>
    </div>

    <!-- Logs table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date / Time</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Details</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody id="logsTableBody">
                <tr>
                    <td colspan="4" class="text-center py-12 text-slate-400">
                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                        <p class="text-sm">Loading logs...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-4" id="logsPagination">
        <p class="text-sm text-slate-500" id="logsCount">Showing -- entries</p>
        <div class="flex gap-2">
            <button id="prevPage" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <i class="fas fa-chevron-left"></i> Prev
            </button>
            <button id="nextPage" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                Next <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>