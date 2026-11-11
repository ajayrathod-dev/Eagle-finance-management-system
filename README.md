# Eagle Finance Management System

Eagle Finance is a comprehensive web-based loan and finance management system built with PHP and MySQL. It enables administrators and finance managers to manage loans, track interest, oversee business correspondents (BC), record payments, and monitor member activity with geolocation tracking.

---

## 🌟 Key Features

- **Dashboard & Analytics**: Real-time overview of active loans, collected payments, and financial metrics.
- **Loan Management**:
  - Issue and view loans.
  - Track borrower details and loan histories.
  - Calculate interest and payment schedules.
- **Payment & Recovery Tracking**:
  - Record payments received.
  - Track individual member contributions.
  - Loan payment histories and transaction logs.
- **Business Correspondent (BC) Funds**:
  - Fund distribution and tracking for BC members.
  - View detailed BC member profiles and ledger logs.
- **Location & Geolocation Tracking**:
  - Track agent and field recovery coordinates.
- **Reports & Exporting**:
  - Integrated DataTables with export options (CSV, Excel, PDF, Print).

---

## 🛠️ Technology Stack

- **Backend**: PHP (7.4+ / 8.x)
- **Database**: MySQL / MariaDB
- **Frontend**: Bootstrap 5, jQuery, HTML5, CSS3
- **Plugins & Libraries**:
  - MetisMenu (Sidebar navigation)
  - DataTables (Advanced table sorting & export)
  - SimpleBar & Perfect Scrollbar
  - Lobibox (Notification popups)
  - VectorMap

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) / WAMP / LAMP with PHP and MySQL installed.
- Modern web browser (Chrome, Edge, Firefox).

### Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/ajayrathod-dev/Eagle-finance-management-system.git
   ```
   Move the folder into your web root directory (e.g., `C:/xampp/htdocs/eagle`).

2. **Configure Database**:
   - Start Apache and MySQL services in your XAMPP Control Panel.
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
   - Create a new database named `finance`.
   - Run the table setup scripts if needed (`create_payments_table.php`, `create_payment_members_table.php`, `create_payment_received_table.php`).

3. **Database Connection Settings**:
   - Rename or verify `connection.php`:
     ```php
     $DB_SERVER = "localhost";
     $DB_NAME = "finance";
     $username = "root";
     $password = "";
     ```

4. **Run Application**:
   - Open your browser and navigate to:
     ```
     http://localhost/eagle/login.php
     ```

---

## 📂 Project Structure

```
eagle/
├── assets/                 # CSS, JavaScript, vendor plugins, icons, themes
├── images/                 # Image assets
├── uploads/                # User document and file uploads
├── connection.php          # Database connection file
├── connection.example.php  # Sample database config
├── dashboard.php           # Main administrator dashboard
├── all_loans.php           # Loan management page
├── view_loan.php           # Single loan inspector
├── loan_payment.php        # Loan payment entry
├── payment_received.php    # Payment ledger records
├── gives_bc_funds.php      # Business correspondent funds
├── track.php               # Field location tracking
└── login.php               # User authentication
```

---

## 📄 License

This project is licensed under the MIT License.
