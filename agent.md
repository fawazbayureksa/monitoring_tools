# AGENT.MD — Monitoring Tools Documentation

> **Project**: Monitoring Tools — PT. United Tractors Tbk. (Cabang Ujung Pandang)  
> **Framework**: CodeIgniter 3.x (PHP 7.4 / 8.x)  
> **Database**: MySQL / MariaDB (`monitoring_tools`)  
> **Frontend**: Able Pro / Bootstrap 4, Feather Icons, DataTables, FontAwesome  

This document serves as the single source of truth for AI agents (and human developers) working on the **Monitoring Tools** repository.

---

## 1. System Overview & Purpose

The Monitoring Tools application manages and tracks the end-to-end lifecycle of heavy-duty tools and special tools borrowed by workshop mechanics and remote field sites (Kendari, Morowali, Routa, Pomalaa, Langgikima, etc.).

Key operational objectives:
1. **Prevent Tool Loss / Hoarding**: Track every tool's borrower, location, and due date.
2. **Delivery & Transit Visibility**: Monitor tools shipped from central workshop/warehouse to remote project sites with SLA tracking.
3. **Formal Approval Chains**: Require Supervisor (SPV) authorization before tools leave the warehouse for remote sites.
4. **Damage & Loss Accountability (BAKK)**: Handle broken or lost tools via formal *Berita Acara Kerusakan / Kehilangan* with document upload workflows.

---

## 2. User Roles & Permission Matrix

| Role | Label in UI | Primary Personas | Capabilities & Access |
| :--- | :--- | :--- | :--- |
| `master` | **MASTER** | Service Dept. Head / System Admin | Full system access. User Management, SPV Approvals, Stock CRUD, BAKK validation, Reports, and System Configuration. |
| `supervisor` | **SUPERVISOR** | Field / Workshop Supervisor (SPV) | Approves/declines site dispatch requests (`Waiting Approval`), monitors delivery tracking, views stocks and history. |
| `admin` | **ADMIN** | Toolkeeper (Warehouse Officer) | Manages stock catalog, validates loan requests, releases tools to workshop/site, validates returned tools, registers BAKK. |
| `leader` | **LEADER MECHANIC** | Site / Workshop Leader | Submits tool borrow requests on behalf of mechanics, confirms tool receipt at site (`Receive Tool`), initiates tool return. |
| `user` | **MECHANIC** | Field / Workshop Mechanics | View-only dashboard access. Receives physical tools assigned to their NRP. |

### Default Seeded Accounts
| Role | NRP / Username | Password | Purpose |
| :--- | :--- | :--- | :--- |
| `master` | `master` | `master123` | Master Superadmin |
| `supervisor` | `spv` | `spv123` | Main Supervisor (Workshop / General) |
| `supervisor` | `spv_field` | `spv123` | Field / Site Supervisor |
| `admin` | `admin` | `admin123` | Main Toolkeeper (Makassar) |
| `admin` | `tkp_field` | `tkp123` | Field Toolkeeper |
| `leader` | `leader_wsp` | `leader123` | Leader Workshop |
| `leader` | `leader_kdi`, `leader_mrw` | `leader123` | Site Leaders (Kendari, Morowali, etc.) |
| `user` | `mech_wsp`, `mech_field` | `UPG_123` | Mechanics |

---

## 3. Tool Loan Lifecycle & State Machine

Every request follows a deterministic finite state machine:

```
[Request Tools (Leader)]
          │ Status: 'Submitted'
          ▼
[Toolkeeper Validation (#modal-submitted)]
   ├─ (Local / Workshop Destination) ──> Action: 'release' ────────┐
   └─ (Destination contains 'SITE')  ──> Action: 'send_supervisor'  │
                                                │ Status:           │
                                                │ 'Waiting Approval'│
                                                ▼                   │
                                      [Supervisor Approval]         │
                                         ├─ Action: 'cancel'        │
                                         │  └─ Status: 'Declined'   │
                                         │     (Restores Stock)     │
                                         └─ Action: 'approve_spv'   │
                                                │ Status:           │
                                                │ 'Waiting Received'│
                                                │ shipped_at = NOW()│
                                                ▼ <─────────────────┘
                                      [Delivery to Site (In-Transit)]
                                                │
                                                ▼
                                      [Recipient Confirms Physical Receipt]
                                         Action: 'receive'
                                                │ Status: 'Borrowed'
                                                ▼
                                      [Tool In-Use (Return Reminders Active)]
                                                │
                                                ▼
                                      [Borrower Returns Tool]
                                         Action: 'return'
                                                │ Status: 'Returned'
                                                │ return_date_actual = NOW()
                                                ▼
                                      [Toolkeeper Return Validation (#modal-returned)]
                                         Action: 'validate_return'
                                         ├─ Condition: 'good'
                                         │  └─ Status: 'Completed' (Stock +1)
                                         └─ Condition: 'broken' / 'lost'
                                            └─ Status: 'BAKK' (Records in `bakk`, Stock not restored)
```

### Detailed State Descriptions
1. **`Submitted`**:
   - Created via `Request_tools::submit()`.
   - Tool stock is immediately decremented (`tools.quantity - qty_requested`) to avoid double-borrowing.
2. **`Waiting Approval`** (UI: `Waiting SPV`):
   - Triggered when destination contains `"SITE"`.
   - Toolkeeper prepares the tool and clicks "Send to Supervisor".
   - Locked for Toolkeeper & Leader; interactive button `Approve SPV` shown only to `supervisor` and `master`.
3. **`Waiting Received`**:
   - Tool is in delivery. `requests.shipped_at` is stamped.
   - Resi pengiriman diunggah oleh Toolkeeper/Admin (pada `release` untuk pengiriman langsung, atau pada `send_supervisor` saat diajukan ke SPV). SPV meninjau resi tersebut dan memberikan persetujuan tanpa perlu upload ulang.
   - Automatically counted in dashboard widget **"Delivery to Site"** (`TIMESTAMPDIFF` against SLA, default 2 days).
   - Recipient Leader at site clicks `Receive Tool` which opens `#modal-receive-tool`, requiring upload of physical receipt proof (`received_proof`) to `assets/uploads/received_proof/` and optional receipt notes before transitioning to `Borrowed`.
4. **`Borrowed`**:
   - Physical tool is with borrower.
   - Automatically monitored for due dates via widget **"Need to Return"** based on `requests.return_date_est`.
5. **`Returned`**:
   - Tool handed back to warehouse. Pending physical inspection by Toolkeeper.
6. **`Completed`**:
   - Physical condition confirmed "Good". Quantity restored to warehouse. Archived.
7. **`Declined`**:
   - Request rejected by Toolkeeper or Supervisor. Stock automatically refunded to `tools.quantity`. Leader notified.
8. **`BAKK`**:
   - Tool damaged or lost. Requires incident document upload in `bakk` module. Stock remains un-restored.

---

## 4. Codebase Architecture & Key Files

### Controllers (`application/controllers/`)
- **[Dashboard.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/controllers/Dashboard.php)**:
  - `index()`: Aggregates cards, delivery lists, return reminders, and active requests based on user role.
  - `process_action()`: Central state dispatcher handling `release`, `receive`, `send_supervisor`, `approve_spv`, `cancel`, `return`, `validate_return`.
- **[Request_tools.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/controllers/Request_tools.php)**:
  - Form submission for borrowing tools.
  - Generates standardized request numbers: `REQ-{PREFIX}-{YYYYMMDD}-{SEQ}`.
- **[Stock_tools.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/controllers/Stock_tools.php)**:
  - Inventory management (Add, edit, delete, stock adjust).
- **[bakk.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/controllers/bakk.php)**:
  - BAKK handling, PDF upload, and tracking broken/lost tools.
- **[History.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/controllers/History.php)**:
  - Historical query with date-range filters and PDF/Print exports.
- **[User_management.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/controllers/User_management.php)**:
  - CRUD for user accounts (`master` access only).
- **[Seeder.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/controllers/Seeder.php)**:
  - CLI/Web seeder runner (e.g. `php index.php seeder spv`, `php index.php seeder migration_005`).

### Models (`application/models/`)
- **[M_Requests.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/models/M_Requests.php)**:
  - Core database operations for requests: stock deduction (`process_request`), stock restoration (`return_tool_stock`), delivery metrics (`get_in_delivery`), return reminders (`get_return_reminders`), and bell notification aggregation (`get_notifications`).
- **`M_Tools.php`**: Inventory data queries.
- **`M_User.php`**: User management queries.
- **`M_Auth.php`**: Credentials verification.
- **`M_Auth.php`**: Credentials verification.

### Views (`application/views/`)
- **[dashboard.php](file:///Users/fawwazbayureksa/Documents/project/akbar/monitoring_tools/application/views/dashboard.php)**:
  - Summary cards, "Delivery to Site" table, "Need to Return" table, and "Currently Borrowed Tools" table.
  - Modals: `#modal-submitted` (Validation/Release with resi upload), `#modal-spv-approval` (Supervisor Approval with resi upload), `#modal-receive-tool` (Leader receipt with proof upload), `#modal-returned` (Return Inspection), `#modal-declined` (Decline with Reason).
- **`templates/sidebar.php`**, **`templates/header.php`**, **`templates/footer.php`**: Common layout and navigation.

---

## 5. Database Schema & Migrations

Database files reside in `Database/`:
- **`Database/monitoring_tools.sql`**: Base database dump and initial schema.
- **`Database/002_delivery_tracking.sql`**: Added `shipped_at DATETIME NULL` to `requests` table.
- **`Database/003_add_supervisor_role.sql`**: Updated `users.role` ENUM to include `'supervisor'`.
- **`Database/004_seed_supervisor_account.sql`**: Seeded `spv` and `spv_field` accounts.
- **`Database/005_add_shipping_and_receipt_proof.sql`**: Added `shipping_receipt`, `received_proof`, `received_at`, and `received_notes` to `requests` table.

### Key Database Tables
1. **`users`**:
   - `id` (INT PK AI), `nrp` (VARCHAR 50 UNIQUE), `username` (VARCHAR 50), `name` (VARCHAR 100), `role` (ENUM: `master`,`admin`,`leader`,`user`,`supervisor`), `password` (VARCHAR 255).
2. **`tools`**:
   - `id` (INT PK AI), `tool_name`, `part_number`, `type`, `quantity` (INT), `rack_location`, `condition_status`.
3. **`requests`**:
   - `id` (INT PK AI), `request_number` (VARCHAR 50), `user_id` (requester ID), `tool_id`, `borrower_name`, `borrower_nrp`, `qty_requested`, `site_location`, `borrow_date`, `return_date_est`, `return_date_actual`, `status`, `shipped_at`, `shipping_receipt`, `received_proof`, `received_at`, `received_notes`, `notes`, `created_at`, `is_read`.
4. **`bakk`**:
   - `id`, `request_id`, `tool_id`, `condition_status` (RUSAK / HILANG), `upload_status`, `file_path`, `notes`, `created_at`.

---

## 6. Developer & AI Agent Guidelines

When modifying this repository, follow these rules:

1. **Always Synchronize Stock with Requests**:
   - Decrement `tools.quantity` when a request is inserted (`process_request`).
   - Increment `tools.quantity` back via `M_Requests::return_tool_stock()` whenever a request is **`Declined`** or validated **`Completed` (Good Condition)**.
   - **Do NOT** restore stock if the condition is `broken` or `lost` (handled via `BAKK`).
2. **Preserve State Machine Role Checks**:
   - `approve_spv` must be restricted to `master` and `supervisor`.
   - `receive` must only be allowed for the designated borrower or `master`.
   - `validate_return` must be restricted to `admin` and `master`.
3. **Line Endings & Formatting**:
   - Keep UNIX (`LF`) line endings across all PHP and view files.
4. **Notifications**:
   - Whenever adding a new status that requires human attention, update `M_Requests::get_notifications()` so appropriate roles receive top navbar bell alerts.
5. **Audit Trails & Dates**:
   - Always record `shipped_at` when tools enter delivery (`Waiting Received`).
   - Always record `return_date_actual` when tools are handed back.
