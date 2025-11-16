# API Documentation - Sistem Akuntansi

## Base URL
```
http://localhost:8000/api
```

## Authentication
Semua endpoint (kecuali login) memerlukan Bearer token di header:
```
Authorization: Bearer {token}
```

## 1. Authentication Endpoints

### POST /auth/login
Login user dan mendapatkan token

**Request:**
```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

**Response Success (200):**
```json
{
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Administrator",
            "email": "admin@example.com",
            "roles": ["admin"],
            "permissions": ["users.view", "users.create", "..."]
        },
        "token": "1|abc123def456..."
    }
}
```

### POST /auth/logout
Logout user dan hapus token

**Response Success (200):**
```json
{
    "message": "Logout successful"
}
```

### GET /auth/me
Mendapatkan informasi user yang sedang login

**Response Success (200):**
```json
{
    "data": {
        "id": 1,
        "name": "Administrator",
        "email": "admin@example.com",
        "roles": ["admin"],
        "permissions": ["users.view", "users.create", "..."]
    }
}
```

## 2. Cash Transaction Endpoints

### GET /cash-transactions
Mendapatkan daftar transaksi kas

**Query Parameters:**
- `type` (optional): `in` atau `out`
- `start_date` (optional): Format YYYY-MM-DD
- `end_date` (optional): Format YYYY-MM-DD
- `per_page` (optional): Default 15

**Response Success (200):**
```json
{
    "message": "Cash transactions retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "date": "2024-01-15",
                "number": "KM20240100001",
                "type": "in",
                "amount": "1000000.00",
                "description": "Penerimaan dari pelanggan",
                "reference": "INV-001",
                "cash_account": {
                    "id": 1,
                    "code": "1100",
                    "name": "Kas"
                },
                "contra_account": {
                    "id": 15,
                    "code": "1300",
                    "name": "Piutang Usaha"
                },
                "cashflow_category": {
                    "id": 1,
                    "name": "Penerimaan dari Pelanggan",
                    "type": "operating"
                },
                "journal": {
                    "id": 1,
                    "number": "JU20240100001",
                    "is_posted": true
                },
                "creator": {
                    "id": 1,
                    "name": "Administrator"
                },
                "created_at": "2024-01-15T10:30:00.000000Z"
            }
        ],
        "per_page": 15,
        "total": 1
    }
}
```

### POST /cash-transactions
Membuat transaksi kas baru

**Request:**
```json
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

**Response Success (201):**
```json
{
    "message": "Cash transaction created successfully",
    "data": {
        "id": 1,
        "date": "2024-01-15",
        "number": "KM20240100001",
        "type": "in",
        "amount": "1000000.00",
        "description": "Penerimaan dari pelanggan",
        "reference": "INV-001",
        "cash_account": {
            "id": 1,
            "code": "1100",
            "name": "Kas"
        },
        "contra_account": {
            "id": 15,
            "code": "1300", 
            "name": "Piutang Usaha"
        },
        "cashflow_category": {
            "id": 1,
            "name": "Penerimaan dari Pelanggan",
            "type": "operating"
        },
        "journal": {
            "id": 1,
            "number": "JU20240100001",
            "details": [
                {
                    "id": 1,
                    "account": {
                        "id": 1,
                        "code": "1100",
                        "name": "Kas"
                    },
                    "description": "Penerimaan dari pelanggan",
                    "debit": "1000000.00",
                    "credit": "0.00"
                },
                {
                    "id": 2,
                    "account": {
                        "id": 15,
                        "code": "1300",
                        "name": "Piutang Usaha"
                    },
                    "description": "Penerimaan dari pelanggan",
                    "debit": "0.00",
                    "credit": "1000000.00"
                }
            ]
        }
    }
}
```

## 3. Report Endpoints

### GET /reports/trial-balance
Mendapatkan neraca saldo

**Query Parameters:**
- `as_of_date` (required): Format YYYY-MM-DD

**Response Success (200):**
```json
{
    "message": "Trial balance retrieved successfully",
    "data": {
        "as_of_date": "2024-01-31",
        "accounts": [
            {
                "account_code": "1100",
                "account_name": "Kas",
                "account_type": "asset",
                "debit_balance": 5000000.00,
                "credit_balance": 0.00
            },
            {
                "account_code": "4100",
                "account_name": "Pendapatan Penjualan",
                "account_type": "revenue",
                "debit_balance": 0.00,
                "credit_balance": 10000000.00
            }
        ],
        "total_debit": 15000000.00,
        "total_credit": 15000000.00,
        "is_balanced": true
    }
}
```

### GET /reports/income-statement
Mendapatkan laporan laba rugi

**Query Parameters:**
- `start_date` (required): Format YYYY-MM-DD
- `end_date` (required): Format YYYY-MM-DD

**Response Success (200):**
```json
{
    "message": "Income statement retrieved successfully",
    "data": {
        "period": {
            "start_date": "2024-01-01",
            "end_date": "2024-01-31"
        },
        "revenues": [
            {
                "account_code": "4100",
                "account_name": "Pendapatan Penjualan",
                "account_category": "operating_revenue",
                "balance": 10000000.00
            },
            {
                "account_code": "4300",
                "account_name": "Pendapatan Sewa",
                "account_category": "operating_revenue", 
                "balance": 2000000.00
            }
        ],
        "expenses": [
            {
                "account_code": "5100",
                "account_name": "Beban Pokok Penjualan",
                "account_category": "operating_expense",
                "balance": 6000000.00
            },
            {
                "account_code": "5200",
                "account_name": "Beban Gaji",
                "account_category": "operating_expense",
                "balance": 3000000.00
            }
        ],
        "total_revenue": 12000000.00,
        "total_expense": 9000000.00,
        "net_income": 3000000.00
    }
}
```

### GET /reports/balance-sheet
Mendapatkan laporan neraca

**Query Parameters:**
- `as_of_date` (required): Format YYYY-MM-DD

**Response Success (200):**
```json
{
    "message": "Balance sheet retrieved successfully",
    "data": {
        "as_of_date": "2024-01-31",
        "assets": [
            {
                "account_code": "1100",
                "account_name": "Kas",
                "account_category": "current_asset",
                "balance": 5000000.00
            },
            {
                "account_code": "1610",
                "account_name": "Bangunan",
                "account_category": "fixed_asset",
                "balance": 50000000.00
            }
        ],
        "liabilities": [
            {
                "account_code": "2100",
                "account_name": "Hutang Usaha",
                "account_category": "current_liability",
                "balance": 2000000.00
            }
        ],
        "equity": [
            {
                "account_code": "3100",
                "account_name": "Modal Saham",
                "account_category": "equity",
                "balance": 50000000.00
            }
        ],
        "retained_earnings": 3000000.00,
        "total_assets": 55000000.00,
        "total_liabilities": 2000000.00,
        "total_equity": 53000000.00,
        "is_balanced": true
    }
}
```

## 4. Error Responses

### Validation Error (422):
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "amount": ["The amount must be at least 0.01."]
    }
}
```

### Unauthorized (401):
```json
{
    "message": "Unauthenticated."
}
```

### Forbidden (403):
```json
{
    "message": "This action is unauthorized."
}
```

### Not Found (404):
```json
{
    "message": "Resource not found."
}
```

### Server Error (500):
```json
{
    "message": "Internal server error.",
    "error": "Detailed error message"
}
```

## 5. Pagination Format

Semua endpoint yang mengembalikan list menggunakan format pagination Laravel:

```json
{
    "current_page": 1,
    "data": [...],
    "first_page_url": "http://localhost:8000/api/endpoint?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://localhost:8000/api/endpoint?page=5",
    "links": [...],
    "next_page_url": "http://localhost:8000/api/endpoint?page=2",
    "path": "http://localhost:8000/api/endpoint",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 67
}
```