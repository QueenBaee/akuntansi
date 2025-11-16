# Database Design - Sistem Akuntansi

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     users       │    │     roles       │    │  permissions    │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │    │ id (PK)         │
│ name            │    │ name            │    │ name            │
│ email (UNIQUE)  │    │ guard_name      │    │ guard_name      │
│ password        │    │ created_at      │    │ created_at      │
│ is_active       │    │ updated_at      │    │ updated_at      │
│ created_at      │    └─────────────────┘    └─────────────────┘
│ updated_at      │           │                        │
└─────────────────┘           │                        │
         │                    └────────┬───────────────┘
         │                             │
         └─────────────┬─────────────────────────────────┐
                       │                                 │
         ┌─────────────────┐              ┌─────────────────┐
         │ model_has_roles │              │role_has_permissions│
         ├─────────────────┤              ├─────────────────┤
         │ role_id (FK)    │              │ permission_id(FK)│
         │ model_type      │              │ role_id (FK)    │
         │ model_id        │              └─────────────────┘
         └─────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    accounts     │    │    journals     │    │ journal_details │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │◄───┤ id (PK)         │◄───┤ id (PK)         │
│ code (UNIQUE)   │    │ date            │    │ journal_id (FK) │
│ name            │    │ number (UNIQUE) │    │ account_id (FK) │
│ type            │    │ reference       │    │ description     │
│ category        │    │ description     │    │ debit           │
│ opening_balance │    │ source_module   │    │ credit          │
│ is_active       │    │ source_id       │    │ created_at      │
│ parent_id (FK)  │    │ total_debit     │    │ updated_at      │
│ created_at      │    │ total_credit    │    └─────────────────┘
│ updated_at      │    │ is_posted       │
└─────────────────┘    │ created_by (FK) │
         │              │ created_at      │
         └──────────────┤ updated_at      │
                        └─────────────────┘
                                 │
                                 ▼
                    ┌─────────────────┐
                    │cashflow_categories│
                    ├─────────────────┤
                    │ id (PK)         │
                    │ name            │
                    │ type            │
                    │ is_active       │
                    │ created_at      │
                    │ updated_at      │
                    └─────────────────┘
                             │
                             ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│cash_transactions│    │bank_transactions│    │    assets       │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │    │ id (PK)         │
│ date            │    │ date            │    │ code (UNIQUE)   │
│ number (UNIQUE) │    │ number (UNIQUE) │    │ name            │
│ type            │    │ type            │    │ purchase_date   │
│ cash_account_id │    │ bank_account_id │    │ purchase_price  │
│ contra_account_id│    │ contra_account_id│   │ residual_value  │
│ cashflow_cat_id │    │ cashflow_cat_id │    │ useful_life_months│
│ amount          │    │ amount          │    │ depreciation_method│
│ description     │    │ description     │    │ asset_account_id│
│ reference       │    │ reference       │    │ depreciation_acc_id│
│ journal_id (FK) │    │ journal_id (FK) │    │ expense_account_id│
│ created_by (FK) │    │ created_by (FK) │    │ accumulated_depr│
│ created_at      │    │ created_at      │    │ is_active       │
│ updated_at      │    │ updated_at      │    │ created_by (FK) │
└─────────────────┘    └─────────────────┘    │ created_at      │
                                              │ updated_at      │
                                              └─────────────────┘
                                                       │
                                                       ▼
                                              ┌─────────────────┐
                                              │  depreciations  │
                                              ├─────────────────┤
                                              │ id (PK)         │
                                              │ asset_id (FK)   │
                                              │ period_date     │
                                              │ depreciation_amt│
                                              │ accumulated_depr│
                                              │ book_value      │
                                              │ journal_id (FK) │
                                              │ is_posted       │
                                              │ created_at      │
                                              │ updated_at      │
                                              └─────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│maklon_transactions│  │  rent_incomes   │    │  rent_expenses  │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │    │ id (PK)         │
│ date            │    │ contract_number │    │ contract_number │
│ number (UNIQUE) │    │ tenant_name     │    │ landlord_name   │
│ customer_name   │    │ property_desc   │    │ property_desc   │
│ product_name    │    │ start_date      │    │ start_date      │
│ quantity        │    │ end_date        │    │ end_date        │
│ unit            │    │ monthly_amount  │    │ total_amount    │
│ unit_cost       │    │ revenue_acc_id  │    │ period_months   │
│ total_cost      │    │ receivable_acc_id│   │ monthly_amount  │
│ expense_acc_id  │    │ is_active       │    │ expense_acc_id  │
│ allocation_acc_id│   │ created_by (FK) │    │ prepaid_acc_id  │
│ description     │    │ created_at      │    │ amortized_amount│
│ journal_id (FK) │    │ updated_at      │    │ is_active       │
│ created_by (FK) │    └─────────────────┘    │ created_by (FK) │
│ created_at      │             │              │ created_at      │
│ updated_at      │             ▼              │ updated_at      │
└─────────────────┘    ┌─────────────────┐    └─────────────────┘
                       │rent_income_sched│             │
                       ├─────────────────┤             ▼
                       │ id (PK)         │    ┌─────────────────┐
                       │ rent_income_id  │    │rent_expense_sched│
                       │ period_date     │    ├─────────────────┤
                       │ amount          │    │ id (PK)         │
                       │ journal_id (FK) │    │ rent_expense_id │
                       │ is_posted       │    │ period_date     │
                       │ created_at      │    │ amount          │
                       │ updated_at      │    │ journal_id (FK) │
                       └─────────────────┘    │ is_posted       │
                                              │ created_at      │
                                              │ updated_at      │
                                              └─────────────────┘

┌─────────────────┐
│   audit_logs    │
├─────────────────┤
│ id (PK)         │
│ model_type      │
│ model_id        │
│ action          │
│ old_values      │
│ new_values      │
│ user_id (FK)    │
│ ip_address      │
│ user_agent      │
│ created_at      │
│ updated_at      │
└─────────────────┘
```

## Table Specifications

### 1. users
Tabel untuk menyimpan data pengguna sistem.

| Column | Type | Length | Null | Default | Index | Description |
|--------|------|--------|------|---------|-------|-------------|
| id | BIGINT | - | NO | AUTO_INCREMENT | PRIMARY | Primary key |
| name | VARCHAR | 255 | NO | - | - | Nama lengkap user |
| email | VARCHAR | 255 | NO | - | UNIQUE | Email user (unique) |
| email_verified_at | TIMESTAMP | - | YES | NULL | - | Waktu verifikasi email |
| password | VARCHAR | 255 | NO | - | - | Password (hashed) |
| is_active | BOOLEAN | - | NO | TRUE | INDEX | Status aktif user |
| remember_token | VARCHAR | 100 | YES | NULL | - | Token remember me |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu diupdate |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (email)
- INDEX (email, is_active)

### 2. accounts
Tabel untuk menyimpan chart of accounts (bagan akun).

| Column | Type | Length | Null | Default | Index | Description |
|--------|------|--------|------|---------|-------|-------------|
| id | BIGINT | - | NO | AUTO_INCREMENT | PRIMARY | Primary key |
| code | VARCHAR | 20 | NO | - | UNIQUE | Kode akun (unique) |
| name | VARCHAR | 255 | NO | - | - | Nama akun |
| type | ENUM | - | NO | - | INDEX | Tipe akun (asset/liability/equity/revenue/expense) |
| category | ENUM | - | NO | - | INDEX | Kategori akun |
| opening_balance | DECIMAL | 15,2 | NO | 0 | - | Saldo awal |
| is_active | BOOLEAN | - | NO | TRUE | INDEX | Status aktif |
| parent_id | BIGINT | - | YES | NULL | FOREIGN | Parent akun (untuk hierarki) |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu diupdate |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (code)
- INDEX (code, is_active)
- INDEX (type, category)
- FOREIGN KEY (parent_id) REFERENCES accounts(id)

### 3. journals
Tabel untuk menyimpan header jurnal.

| Column | Type | Length | Null | Default | Index | Description |
|--------|------|--------|------|---------|-------|-------------|
| id | BIGINT | - | NO | AUTO_INCREMENT | PRIMARY | Primary key |
| date | DATE | - | NO | - | INDEX | Tanggal jurnal |
| number | VARCHAR | 50 | NO | - | UNIQUE | Nomor jurnal (unique) |
| reference | VARCHAR | 255 | YES | NULL | - | Referensi eksternal |
| description | TEXT | - | NO | - | - | Deskripsi jurnal |
| source_module | ENUM | - | NO | - | INDEX | Modul sumber jurnal |
| source_id | BIGINT | - | YES | NULL | INDEX | ID sumber dari modul |
| total_debit | DECIMAL | 15,2 | NO | - | - | Total debit |
| total_credit | DECIMAL | 15,2 | NO | - | - | Total kredit |
| is_posted | BOOLEAN | - | NO | FALSE | - | Status posting |
| created_by | BIGINT | - | NO | - | FOREIGN | User pembuat |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu diupdate |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (number)
- INDEX (date, number)
- INDEX (source_module, source_id)
- FOREIGN KEY (created_by) REFERENCES users(id)

### 4. journal_details
Tabel untuk menyimpan detail jurnal (baris debit/kredit).

| Column | Type | Length | Null | Default | Index | Description |
|--------|------|--------|------|---------|-------|-------------|
| id | BIGINT | - | NO | AUTO_INCREMENT | PRIMARY | Primary key |
| journal_id | BIGINT | - | NO | - | FOREIGN | ID jurnal header |
| account_id | BIGINT | - | NO | - | FOREIGN | ID akun |
| description | VARCHAR | 255 | NO | - | - | Deskripsi baris |
| debit | DECIMAL | 15,2 | NO | 0 | - | Jumlah debit |
| credit | DECIMAL | 15,2 | NO | 0 | - | Jumlah kredit |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu diupdate |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (journal_id, account_id)
- FOREIGN KEY (journal_id) REFERENCES journals(id) ON DELETE CASCADE
- FOREIGN KEY (account_id) REFERENCES accounts(id)

### 5. cash_transactions
Tabel untuk menyimpan transaksi kas masuk dan keluar.

| Column | Type | Length | Null | Default | Index | Description |
|--------|------|--------|------|---------|-------|-------------|
| id | BIGINT | - | NO | AUTO_INCREMENT | PRIMARY | Primary key |
| date | DATE | - | NO | - | INDEX | Tanggal transaksi |
| number | VARCHAR | 50 | NO | - | UNIQUE | Nomor transaksi |
| type | ENUM | - | NO | - | INDEX | Tipe (in/out) |
| cash_account_id | BIGINT | - | NO | - | FOREIGN | ID akun kas |
| contra_account_id | BIGINT | - | NO | - | FOREIGN | ID akun lawan |
| cashflow_category_id | BIGINT | - | NO | - | FOREIGN | ID kategori arus kas |
| amount | DECIMAL | 15,2 | NO | - | - | Jumlah transaksi |
| description | VARCHAR | 255 | NO | - | - | Deskripsi |
| reference | VARCHAR | 100 | YES | NULL | - | Referensi |
| journal_id | BIGINT | - | YES | NULL | FOREIGN | ID jurnal terkait |
| created_by | BIGINT | - | NO | - | FOREIGN | User pembuat |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu diupdate |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (number)
- INDEX (date, type)
- FOREIGN KEY (cash_account_id) REFERENCES accounts(id)
- FOREIGN KEY (contra_account_id) REFERENCES accounts(id)
- FOREIGN KEY (cashflow_category_id) REFERENCES cashflow_categories(id)
- FOREIGN KEY (journal_id) REFERENCES journals(id) ON DELETE SET NULL
- FOREIGN KEY (created_by) REFERENCES users(id)

## Business Rules

### 1. Double Entry Accounting
- Setiap jurnal harus memiliki total debit = total kredit
- Minimal 2 baris detail (1 debit, 1 kredit)
- Tidak boleh ada baris dengan debit dan kredit bersamaan

### 2. Account Balance Calculation
```sql
-- Untuk akun Asset dan Expense
balance = opening_balance + SUM(debit) - SUM(credit)

-- Untuk akun Liability, Equity, dan Revenue  
balance = opening_balance + SUM(credit) - SUM(debit)
```

### 3. Journal Numbering
Format: `[PREFIX][YYYYMM][SEQUENCE]`
- JU = Jurnal Umum
- KM = Kas Masuk  
- KK = Kas Keluar
- BK = Bank
- DP = Depreciation
- MK = Maklon
- RI = Rent Income
- RE = Rent Expense

### 4. Depreciation Calculation
```
Monthly Depreciation = (Purchase Price - Residual Value) / Useful Life in Months
```

### 5. Data Integrity Rules
- Transaksi yang sudah di-posting ke jurnal tidak bisa diedit/hapus
- Akun yang sudah digunakan di transaksi tidak bisa dihapus
- User yang tidak aktif tidak bisa login
- Semua transaksi harus memiliki audit trail