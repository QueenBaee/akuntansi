# Sistem Akuntansi Laravel

Sistem akuntansi lengkap berbasis Laravel dengan fitur double-entry bookkeeping, manajemen aset, penyusutan otomatis, maklon, sewa, dan laporan keuangan komprehensif.

## 🚀 Fitur Utama

### 📊 Modul Akuntansi Inti
- **Chart of Accounts** - Bagan akun dengan hierarki
- **Journal Entry** - Jurnal umum dengan validasi double-entry
- **Cash Management** - Transaksi kas masuk/keluar dengan kategorisasi arus kas
- **Bank Management** - Mutasi bank dan rekonsiliasi
- **General Ledger** - Buku besar per akun

### 🏢 Modul Bisnis
- **Fixed Assets** - Manajemen aset tetap dengan penyusutan otomatis
- **Depreciation** - Penyusutan bulanan menggunakan metode garis lurus
- **Maklon Processing** - Transaksi jasa maklon dengan alokasi biaya
- **Rent Income** - Pendapatan sewa dengan jadwal otomatis
- **Rent Expense** - Biaya sewa dengan amortisasi

### 📈 Laporan Keuangan
- **Trial Balance** - Neraca saldo
- **Income Statement** - Laporan laba rugi
- **Balance Sheet** - Neraca
- **Cash Flow Statement** - Laporan arus kas
- **General Ledger** - Buku besar detail

### 🔐 Sistem Keamanan
- **Role-based Access Control** - Manajemen peran dan izin
- **Audit Trail** - Log semua aktivitas transaksi
- **API Authentication** - Sanctum token-based auth
- **Data Validation** - Validasi komprehensif di semua level

## 🛠 Teknologi Stack

### Backend
- **Laravel 10** - PHP Framework
- **PostgreSQL** - Database utama
- **Redis** - Cache dan queue
- **Sanctum** - API Authentication
- **Spatie Permission** - Role & Permission management

### Frontend (Planned)
- **Vue.js 3** atau **React 18** - SPA Framework
- **Tailwind CSS** - Styling
- **Axios** - HTTP Client
- **Chart.js** - Data visualization

### DevOps
- **Docker** - Containerization
- **GitHub Actions** - CI/CD
- **Nginx** - Web server
- **Supervisor** - Queue worker management

## 📋 Persyaratan Sistem

### Minimum Requirements
- PHP 8.1+
- PostgreSQL 13+
- Redis 6+
- Composer 2+
- Node.js 16+ (untuk frontend)

### Recommended
- PHP 8.2+
- PostgreSQL 15+
- Redis 7+
- 4GB RAM
- 2 CPU cores

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone https://github.com/your-repo/akuntansi.git
cd akuntansi
```

### 2. Setup dengan Docker
```bash
# Copy environment file
cp .env.example .env

# Build dan start containers
docker-compose up -d

# Install dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations dan seeders
docker-compose exec app php artisan migrate --seed

# Start queue worker
docker-compose exec app php artisan queue:work
```

### 3. Setup Manual (Tanpa Docker)
```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database di .env file
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=akuntansi
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate --seed

# Start development server
php artisan serve

# Start queue worker (terminal terpisah)
php artisan queue:work
```

## 📚 Dokumentasi

### API Documentation
Lihat [API_DOCUMENTATION.md](API_DOCUMENTATION.md) untuk detail lengkap semua endpoint API.

### Database Design
Lihat [DATABASE_DESIGN.md](DATABASE_DESIGN.md) untuk ERD dan spesifikasi database.

### Development Plan
Lihat [DEVELOPMENT_PLAN.md](DEVELOPMENT_PLAN.md) untuk roadmap dan standar development.

## 🔧 Konfigurasi

### Environment Variables
```bash
# Accounting specific settings
ACCOUNTING_FISCAL_YEAR_START=01-01
ACCOUNTING_DEFAULT_CURRENCY=IDR
ACCOUNTING_DECIMAL_PLACES=2
ACCOUNTING_AUTO_BACKUP=true
ACCOUNTING_BACKUP_RETENTION_DAYS=30
```

### Queue Configuration
```bash
# Untuk production, gunakan Redis
QUEUE_CONNECTION=redis

# Start queue worker
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Scheduler Setup
Tambahkan ke crontab untuk menjalankan scheduler Laravel:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 🧪 Testing

### Run Tests
```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# All tests dengan coverage
php artisan test --coverage
```

### Test Data
```bash
# Seed test data
php artisan db:seed --class=TestDataSeeder

# Reset database
php artisan migrate:fresh --seed
```

## 📊 Usage Examples

### 1. Membuat Transaksi Kas
```bash
POST /api/cash-transactions
{
    "date": "2024-01-15",
    "type": "in",
    "cash_account_id": 1,
    "contra_account_id": 15,
    "cashflow_category_id": 1,
    "amount": 1000000,
    "description": "Penerimaan dari pelanggan",
    "reference": "INV-001"
}
```

### 2. Proses Penyusutan Bulanan
```bash
POST /api/depreciations/process-monthly
{
    "period_date": "2024-01-31"
}
```

### 3. Generate Laporan Laba Rugi
```bash
GET /api/reports/income-statement?start_date=2024-01-01&end_date=2024-01-31
```

## 🔐 Default Users

Setelah menjalankan seeder, tersedia user default:

| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| Admin | admin@example.com | password | Full access |
| Staff Akuntansi | staff@example.com | password | Transaction & Journal |
| Manajer | manager@example.com | password | Reports & Dashboard |

## 🏗 Arsitektur

### Service Layer Pattern
```php
// Controller hanya handle HTTP request/response
class CashTransactionController extends Controller
{
    public function store(CashTransactionRequest $request)
    {
        $transaction = $this->transactionService->createCashTransaction($request->validated());
        return response()->json(['data' => $transaction], 201);
    }
}

// Service handle business logic
class TransactionService
{
    public function createCashTransaction(array $data): CashTransaction
    {
        // Business logic here
        return DB::transaction(function () use ($data) {
            // Create transaction and journal
        });
    }
}
```

### Double Entry Validation
```php
// Setiap transaksi otomatis generate jurnal
$journal = $this->transactionService->createJournal([
    'details' => [
        ['account_id' => 1, 'debit' => 1000000, 'credit' => 0],
        ['account_id' => 15, 'debit' => 0, 'credit' => 1000000],
    ]
]);

// Validasi total debit = total credit
if ($totalDebit != $totalCredit) {
    throw new \Exception('Total debit must equal total credit');
}
```

## 🔄 Scheduled Jobs

### Monthly Processing
- **Depreciation**: Setiap tanggal 1 jam 02:00
- **Rent Income**: Setiap tanggal 1 jam 03:00  
- **Rent Expense**: Setiap tanggal 1 jam 04:00

### Daily Tasks
- **Database Backup**: Setiap hari jam 01:00
- **Log Cleanup**: Setiap minggu

## 🚨 Troubleshooting

### Common Issues

#### 1. Queue Jobs Tidak Jalan
```bash
# Check queue worker status
php artisan queue:work --verbose

# Restart queue worker
php artisan queue:restart
```

#### 2. Permission Denied Errors
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 3. Database Connection Error
```bash
# Check database configuration
php artisan config:cache
php artisan config:clear

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Coding Standards
- Follow PSR-12 coding standard
- Write tests for new features
- Update documentation
- Use conventional commit messages

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

- **Documentation**: [Wiki](https://github.com/your-repo/akuntansi/wiki)
- **Issues**: [GitHub Issues](https://github.com/your-repo/akuntansi/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-repo/akuntansi/discussions)
- **Email**: support@yourcompany.com

## 🙏 Acknowledgments

- Laravel Framework Team
- Spatie untuk package Permission
- PostgreSQL Community
- Docker Community
- Semua contributor yang telah membantu project ini