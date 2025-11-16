// Simple client-side router
class Router {
    constructor() {
        this.routes = {};
        this.currentPath = window.location.pathname;
        
        // Handle browser back/forward
        window.addEventListener('popstate', () => {
            this.navigate(window.location.pathname, false);
        });
        
        // Handle navigation clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="/"]')) {
                e.preventDefault();
                this.navigate(e.target.getAttribute('href'));
            }
        });
    }
    
    route(path, handler) {
        this.routes[path] = handler;
    }
    
    navigate(path, pushState = true) {
        if (!requireAuth() && path !== '/login') {
            return;
        }
        
        this.currentPath = path;
        
        if (pushState) {
            history.pushState(null, '', path);
        }
        
        // Update active nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === path) {
                link.classList.add('active');
            }
        });
        
        // Execute route handler
        const handler = this.routes[path] || this.routes['*'];
        if (handler) {
            handler();
        }
    }
    
    start() {
        this.navigate(this.currentPath, false);
    }
}

// Initialize router
const router = new Router();

// Define routes
router.route('/', loadDashboard);
router.route('/accounts', loadAccounts);
router.route('/cash-transactions', loadCashTransactions);
router.route('/reports', loadReports);
router.route('*', load404);

// Route handlers
async function loadDashboard() {
    const content = document.getElementById('content');
    content.innerHTML = `
        <div class="card-header">
            <h1 class="card-title">Dashboard</h1>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" id="totalCash">-</div>
                <div class="stat-label">Total Kas</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalAccounts">-</div>
                <div class="stat-label">Total Akun</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="monthlyTransactions">-</div>
                <div class="stat-label">Transaksi Bulan Ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalAssets">-</div>
                <div class="stat-label">Total Aset</div>
            </div>
        </div>
        
        <div class="card">
            <h3 class="mb-2">Transaksi Terbaru</h3>
            <div id="recentTransactions">Loading...</div>
        </div>
    `;
    
    try {
        // Load dashboard data
        const [statsData, transactionsData] = await Promise.all([
            apiCall('/dashboard/stats'),
            apiCall('/dashboard/recent-transactions')
        ]);
        
        // Update stats
        document.getElementById('totalCash').textContent = formatCurrency(statsData.data.totalCash);
        document.getElementById('totalAccounts').textContent = statsData.data.totalAccounts;
        document.getElementById('monthlyTransactions').textContent = statsData.data.monthlyTransactions;
        document.getElementById('totalAssets').textContent = statsData.data.totalAssets;
        
        // Update recent transactions
        const transactionsHtml = transactionsData.data.length > 0 ? `
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Akun</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    ${transactionsData.data.map(t => `
                        <tr>
                            <td>${formatDate(t.date)}</td>
                            <td>${t.description}</td>
                            <td>${t.account_name}</td>
                            <td class="text-right">${formatCurrency(t.amount)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : '<p class="text-center">Belum ada transaksi</p>';
        
        document.getElementById('recentTransactions').innerHTML = transactionsHtml;
        
    } catch (error) {
        showError('Gagal memuat data dashboard');
    }
}

async function loadAccounts() {
    const content = document.getElementById('content');
    content.innerHTML = `
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Chart of Accounts</h1>
                <button class="btn btn-primary" onclick="showModal('accountModal')">Tambah Akun</button>
            </div>
            <div id="accountsTable">Loading...</div>
        </div>
        
        <!-- Account Modal -->
        <div id="accountModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Akun</h3>
                    <button class="close" onclick="hideModal('accountModal')">&times;</button>
                </div>
                <form id="accountForm">
                    <div class="form-group">
                        <label class="form-label">Kode</label>
                        <input type="text" name="code" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-input" required>
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="flex justify-between gap-2">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('accountModal')">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    await loadAccountsData();
    
    // Handle form submission
    document.getElementById('accountForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = getFormData('accountForm');
        
        try {
            await apiCall('/accounts', {
                method: 'POST',
                body: JSON.stringify(formData)
            });
            
            hideModal('accountModal');
            resetForm('accountForm');
            await loadAccountsData();
            showSuccess('Akun berhasil ditambahkan');
        } catch (error) {
            showError(error.message);
        }
    });
}

async function loadAccountsData() {
    try {
        const data = await apiCall('/accounts');
        const tableHtml = `
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th class="text-right">Saldo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.data.map(account => `
                        <tr>
                            <td>${account.code}</td>
                            <td>${account.name}</td>
                            <td>${account.type}</td>
                            <td class="text-right">${formatCurrency(account.balance || 0)}</td>
                            <td>
                                <button class="btn btn-danger" onclick="deleteAccount(${account.id})">Hapus</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        document.getElementById('accountsTable').innerHTML = tableHtml;
    } catch (error) {
        document.getElementById('accountsTable').innerHTML = '<p class="text-center">Gagal memuat data akun</p>';
    }
}

async function deleteAccount(id) {
    if (confirm('Yakin ingin menghapus akun ini?')) {
        try {
            await apiCall(`/accounts/${id}`, { method: 'DELETE' });
            await loadAccountsData();
            showSuccess('Akun berhasil dihapus');
        } catch (error) {
            showError(error.message);
        }
    }
}

async function loadCashTransactions() {
    const content = document.getElementById('content');
    content.innerHTML = `
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Transaksi Kas</h1>
                <button class="btn btn-primary" onclick="showModal('transactionModal')">Tambah Transaksi</button>
            </div>
            <div id="transactionsTable">Loading...</div>
        </div>
        
        <!-- Transaction Modal -->
        <div id="transactionModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Transaksi Kas</h3>
                    <button class="close" onclick="hideModal('transactionModal')">&times;</button>
                </div>
                <form id="transactionForm">
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" class="form-input" value="${new Date().toISOString().split('T')[0]}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-input" required>
                            <option value="in">Kas Masuk</option>
                            <option value="out">Kas Keluar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="amount" class="form-input" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-input" rows="3" required></textarea>
                    </div>
                    <div class="flex justify-between gap-2">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('transactionModal')">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    await loadTransactionsData();
    
    // Handle form submission
    document.getElementById('transactionForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = getFormData('transactionForm');
        
        try {
            await apiCall('/cash-transactions', {
                method: 'POST',
                body: JSON.stringify(formData)
            });
            
            hideModal('transactionModal');
            resetForm('transactionForm');
            await loadTransactionsData();
            showSuccess('Transaksi berhasil ditambahkan');
        } catch (error) {
            showError(error.message);
        }
    });
}

async function loadTransactionsData() {
    try {
        const data = await apiCall('/cash-transactions');
        const tableHtml = `
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.data.map(transaction => `
                        <tr>
                            <td>${formatDate(transaction.date)}</td>
                            <td class="${transaction.type === 'in' ? 'text-success' : 'text-danger'}">
                                ${transaction.type === 'in' ? 'Masuk' : 'Keluar'}
                            </td>
                            <td>${transaction.description}</td>
                            <td class="text-right">${formatCurrency(transaction.amount)}</td>
                            <td>
                                <button class="btn btn-danger" onclick="deleteTransaction(${transaction.id})">Hapus</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        document.getElementById('transactionsTable').innerHTML = tableHtml;
    } catch (error) {
        document.getElementById('transactionsTable').innerHTML = '<p class="text-center">Gagal memuat data transaksi</p>';
    }
}

async function deleteTransaction(id) {
    if (confirm('Yakin ingin menghapus transaksi ini?')) {
        try {
            await apiCall(`/cash-transactions/${id}`, { method: 'DELETE' });
            await loadTransactionsData();
            showSuccess('Transaksi berhasil dihapus');
        } catch (error) {
            showError(error.message);
        }
    }
}

function loadReports() {
    const content = document.getElementById('content');
    content.innerHTML = `
        <div class="card">
            <h1 class="card-title">Laporan Keuangan</h1>
            <div class="stats-grid">
                <div class="card">
                    <h3>Neraca Saldo</h3>
                    <p>Laporan saldo semua akun pada tanggal tertentu</p>
                    <button class="btn btn-primary" onclick="loadTrialBalance()">Lihat Laporan</button>
                </div>
                <div class="card">
                    <h3>Laporan Laba Rugi</h3>
                    <p>Laporan pendapatan dan beban dalam periode tertentu</p>
                    <button class="btn btn-primary" onclick="loadIncomeStatement()">Lihat Laporan</button>
                </div>
                <div class="card">
                    <h3>Neraca</h3>
                    <p>Laporan posisi keuangan pada tanggal tertentu</p>
                    <button class="btn btn-primary" onclick="loadBalanceSheet()">Lihat Laporan</button>
                </div>
            </div>
        </div>
        <div id="reportContent"></div>
    `;
}

async function loadTrialBalance() {
    const reportContent = document.getElementById('reportContent');
    reportContent.innerHTML = '<div class="card"><div class="text-center">Loading...</div></div>';
    
    try {
        const data = await apiCall('/reports/trial-balance');
        const totalDebit = data.data.reduce((sum, item) => sum + item.debit, 0);
        const totalCredit = data.data.reduce((sum, item) => sum + item.credit, 0);
        
        reportContent.innerHTML = `
            <div class="card">
                <div class="text-center mb-3">
                    <h3>NERACA SALDO</h3>
                    <p>Per ${formatDate(new Date())}</p>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Akun</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.data.map(item => `
                            <tr>
                                <td>${item.account_code}</td>
                                <td>${item.account_name}</td>
                                <td class="text-right">${item.debit > 0 ? formatCurrency(item.debit) : '-'}</td>
                                <td class="text-right">${item.credit > 0 ? formatCurrency(item.credit) : '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; border-top: 2px solid #333;">
                            <td colspan="2">TOTAL</td>
                            <td class="text-right">${formatCurrency(totalDebit)}</td>
                            <td class="text-right">${formatCurrency(totalCredit)}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;
    } catch (error) {
        reportContent.innerHTML = '<div class="card"><p class="text-center">Gagal memuat laporan</p></div>';
    }
}

function load404() {
    const content = document.getElementById('content');
    content.innerHTML = `
        <div class="card text-center">
            <h1>404 - Halaman Tidak Ditemukan</h1>
            <p>Halaman yang Anda cari tidak tersedia.</p>
            <button class="btn btn-primary" onclick="router.navigate('/')">Kembali ke Dashboard</button>
        </div>
    `;
}

// Start router when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname === '/login') {
        return; // Don't start router on login page
    }
    
    if (!requireAuth()) {
        return;
    }
    
    router.start();
});