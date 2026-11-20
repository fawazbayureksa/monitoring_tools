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
   ├─ (Workshop Destination) ───────> Action: 'release' ───────────────┐
   └─ (Destination contains 'SITE')  ──> Action: 'send_supervisor'      │
                                                │ Status:              │
                                                │ 'Waiting Approval'   │
                                                ▼                      │
                                      [Supervisor Approval]            │
                                         ├─ Action: 'cancel'           │
                                         │  └─ Status: 'Declined'      │
                                         │     (Restores Stock 1x)     │
                                         └─ Action: 'approve_spv'      │
                                                │ Status: 'Approved'   │
                                                ▼                      │
                                      [Toolkeeper Shipping]            │
                                         Action: 'ship'                │
                                         Upload: shipping_receipt      │
                                                │ Status:              │
                                                │ 'Waiting Received'   │
                                                │ shipped_at = NOW()   │
                                                ▼ <────────────────────┘
                                      [Delivery to Site (In-Transit)]
                                                │
                                                ▼
                                      [Recipient Confirms Physical Receipt]
                                         Action: 'receive'
                                         Upload: received_proof
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
                                      [Toolkeeper Return Inspection (#modal-returned)]
                                         Action: 'validate_return'
                                         ├─ Item Condition: 'good' ────> Stock Restored (stock_restored = 1)
                                         ├─ Item Condition: 'broken' ──> BAKK Record (Stock NOT restored)
                                         └─ Item Condition: 'lost' ────> BAKK Record (Stock NOT restored)
                                                │
                                                ├─ If ALL items good: Status 'Completed'
                                                └─ If ANY broken/lost: Status 'BAKK'
```

### Detailed State Descriptions
1. **`Submitted`**:
   - Created via `Request_tools::submit()`.
   - Tool stock is immediately decremented (`tools.quantity - qty_requested`) to avoid double-borrowing.
2. **`Waiting Approval`** (UI: `Waiting SPV`):
   - Triggered when destination contains `"SITE"` via `send_supervisor`.
   - Locked for Toolkeeper & Leader; interactive button `Approve SPV` shown only to `supervisor` and `master`.
   - No shipping receipts can be uploaded at this stage.
3. **`Approved`**:
   - SPV approves the entire request via `approve_spv`.
   - Returns to Toolkeeper/Admin dashboard to upload shipping receipt and confirm dispatch.
4. **`Waiting Received`**:
   - Tool is in delivery. `requests.shipped_at` is stamped upon `ship` confirmation.
   - Resi pengiriman (`shipping_receipt`) wajib diunggah oleh Toolkeeper/Admin pada action `ship`.
   - Automatically counted in dashboard widget **"Delivery to Site"** (`TIMESTAMPDIFF` against SLA, default 2 days).
   - Recipient Leader at site clicks `Receive Tool` which opens `#modal-receive-tool`, requiring upload of physical receipt proof (`received_proof`) to `assets/uploads/received_proof/` and optional receipt notes before transitioning to `Borrowed`.
5. **`Borrowed`**:
   - Physical tool is with borrower.
   - Automatically monitored for due dates via widget **"Need to Return"** based on `requests.return_date_est`.
6. **`Returned`**:
   - Tool handed back to warehouse. Pending physical item-level inspection by Toolkeeper.
7. **`Completed`**:
   - All physical items confirmed "Good". Quantity restored to warehouse. Archived.
8. **`Declined`**:
   - Request rejected by Toolkeeper or Supervisor. Stock automatically refunded to `tools.quantity` idempotently. Leader notified.
9. **`BAKK`**:
   - One or more items damaged or lost during return inspection. Requires incident document upload in `bakk` module. Damaged/lost item stock remains un-restored.

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
- **`Database/006_multiple_tools_per_request.sql`**: Created `request_items` table and migrated historical data from `requests` to support multiple tools per request.

### Key Database Tables
1. **`users`**:
   - `id` (INT PK AI), `nrp` (VARCHAR 50 UNIQUE), `username` (VARCHAR 50), `name` (VARCHAR 100), `role` (ENUM: `master`,`admin`,`leader`,`user`,`supervisor`), `password` (VARCHAR 255).
2. **`tools`**:
   - `id` (INT PK AI), `tool_name`, `part_number`, `type`, `quantity` (INT), `rack_location`, `condition_status`.
3. **`requests`** (Header):
   - `id` (INT PK AI), `request_number` (VARCHAR 50), `user_id` (requester ID), `tool_id` (legacy fallback), `borrower_name`, `borrower_nrp`, `qty_requested` (legacy fallback), `site_location`, `borrow_date`, `return_date_est`, `return_date_actual`, `status`, `shipped_at`, `shipping_receipt`, `received_proof`, `received_at`, `received_notes`, `notes`, `created_at`, `is_read`.
4. **`request_items`** (Detail):
   - `id` (INT PK AI), `request_id` (FK to `requests.id`), `tool_id` (FK to `tools.id`), `qty_requested` (INT), `qty_returned` (INT), `return_condition` (ENUM: `good`,`broken`,`lost`), `created_at`, `updated_at`.
5. **`bakk`**:
   - `id`, `request_id`, `tool_id`, `condition_status` (RUSAK / HILANG), `upload_status`, `file_path`, `notes`, `created_at`.

---

## 6. Developer & AI Agent Guidelines

When modifying this repository, follow these rules:

1. **Multiple Tools per Request (All-or-Nothing Decision)**:
   - One request number (`REQ-...`) can have multiple items in `request_items`.
   - Admin and SPV approvals apply to the **entire request** (no partial approvals/rejections).
   - If a request is declined/cancelled, stock for **all items** in `request_items` is restored atomically via `M_Requests::return_request_items_stock()`.
2. **Item-Level Inspection on Return**:
   - Toolkeeper inspects each item in `#modal-returned`.
   - Items with condition `good` restore their corresponding stock quantity.
   - Items with condition `broken` or `lost` do **NOT** restore stock and generate entries in `bakk`.
   - If any item is broken or lost, the overall request status becomes `BAKK`.
3. **Always Synchronize Stock with Requests**:
   - Decrement `tools.quantity` immediately when a request is inserted (`process_request` in DB transaction with conditional update `quantity >= qty`).
   - Increment `tools.quantity` back whenever an item/request is completed in good condition or declined.
4. **Preserve State Machine Role Checks**:
   - `approve_spv` must be restricted to `master` and `supervisor`.
   - `receive` must only be allowed for the designated borrower or `master`.
   - `validate_return` must be restricted to `admin` and `master`.
5. **Line Endings & Formatting**:
   - Keep UNIX (`LF`) line endings across all PHP and view files.
6. **Notifications**:
   - Whenever adding a new status that requires human attention, update `M_Requests::get_notifications()` so appropriate roles receive top navbar bell alerts.
7. **Audit Trails & Dates**:
   - Always record `shipped_at` when tools enter delivery (`Waiting Received`).
   - Always record `return_date_actual` when tools are handed back.
8. **Downloadable Tool Borrowing Form (PDF)**:
   - Official document matching corporate standard ("FORM PEMINJAMAN TOOLS – Cabang Ujung Pandang – PT UNITED TRACTORS Tbk.").
   - Implemented via `Pdf_borrowing_form.php` library (FPDF) and exposed at `History::download_pdf($request_id)`.
   - **Availability Rule**: Strictly restricted to finalized states (`Completed` or `BAKK`). Non-finalized requests cannot be downloaded.
   - **Authorization**: Mechanics/leaders can download only their own requests; Supervisors, Admins, and Master can download any request.


