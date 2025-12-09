# ⚙️ System Documentation: Library Management System
This document details the technical architecture, database design, and key components of the Library Management System.

---

## 📑 Table of Contents

### 1. [System Overview](#system-overview)
- **1.1** [Purpose and Scope](#purpose-and-scope)
- **1.2** [Key Features](#key-features)

### 2. [Architecture and Technology Stack](#architecture-and-technology-stack)
- **2.1** [High-Level Architecture](#high-level-architecture)
- **2.2** [Technology Stack](#technology-stack)

### 3. [Code and Directory Structure](#code-and-directory-structure)
- **3.1** [Main Directories and Their Purpose](#main-directories-and-their-purpose)
- **3.2** [Entry Point and Configuration Files](#entry-point-and-configuration-files)

### 4. [Database Schema Details](#database-schema-details)
- **4.1** [Core Tables](#core-tables)
- **4.2** [Key Relationships](#key-relationships)

### 5. [Design and Implementation Decisions](#design-and-implementation-decisions)
- **5.1** [Business Logic Decisions](#business-logic-decisions)
- **5.2** [Security Considerations](#security-considerations)
- **5.3** [Error Handling Strategy](#error-handling-strategy)

### 6. [Deployment and Maintenance](#deployment-and-maintenance)
- **6.1** [Installation Instructions for Developers](#installation-instructions-for-developers)
- **6.2** [System Dependencies](#system-dependencies)
- **6.3** [Testing Strategy](#testing-strategy)

---

# 1. 🖥️ System Overview

---

## 1.1 Purpose and Scope
The Library System is a **web-based application** designed to efficiently manage library operations and cater to the needs of different users, including librarians, staff, students, and teachers. Its main purpose is to streamline borrowing, reservation, and book management processes, as well as to track user transactions, penalties, and clearance status. The system ensures accuracy and reduces human errors in handling library operations, making the management of books and user transactions more efficient and organized.

The system serves the following roles:  
- **Librarian**: Manages all aspects of the library's books, including metadata and categories.  
- **Staff**: Handles borrowing and reservation transactions, monitors user activities, manages clearance and penalties.  
- **Students and Teachers (Users)**: Request book borrowings and reservations, track their borrowing history, and view their clearance status. Teachers have additional privileges, such as no borrowing limit and access to exclusive books, while students have borrowing limits and must settle any outstanding or unreturned books.

---

## 1.2 Key Features

### Librarian
- **Dashboard**: View reports such as total books, available books, book categories, low-stock books, archived books, and recent activity (timeline of last 5 added or modified books).  
- **Books Management**: Manage books including adding, editing metadata, archiving/restoring books, and managing categories (add, edit, delete).

### Staff
- **Dashboard**: Access analytics including pending requests, active reservations, overdue loans, total clearance, and students with penalties.  
- **Borrower Management**: View detailed user activities, including borrowed books, returned books, and overdue items.  
- **Loan Management**: Manage borrowing, reservation requests, returns, and reservation pickups through four tabs.  
- **Clearance**: Track user clearance for students and teachers, preventing mistakes by automatically disabling clearance for users with unreturned books or pending penalties.  
- **Penalties**: Apply penalties only after clearance week with a warning system to prevent errors.

### Students
- **Dashboard**: View reports including clearance status, borrowed books, reserved books, and remaining borrowing limits. Preview latest books and access the library catalog quickly.  
- **Library Catalog**: Search, borrow, or reserve publicly available books, filter by categories.  
- **My Records**: View borrow and reservation history for each semester with dates preserved.

### Teachers
- **Dashboard**: Similar to students but with overdue tracking instead of borrowing limits.  
- **Exclusive Catalog**: Access books exclusive for faculty use.  
- **My Records**: Same as students, including borrow history and reservations.

---

# 2. 🏗️ Architecture and Technology Stack

---

## 2.1 High-Level Architecture
The Library System follows a **basic web-based architecture** using the **three-layer model**:

1. **Presentation Layer**  
   - HTML, CSS, Bootstrap, jQuery, and DataTables  
   - UI components such as dashboards, forms, and tables  
   - Some data fetched dynamically using placeholder APIs  

2. **Application Layer (Backend)**  
   - Built with **PHP** following a simple **MVC structure**  
   - Handles borrowing, reservations, returns, penalties, clearance, and reports  

3. **Data Layer (Database)**  
   - MySQL tables for all major entities  
   - Managed via phpMyAdmin  

---

## 2.2 Technology Stack

| Layer           | Technology / Tool                               |
|-----------------|------------------------------------------------|
| Frontend        | HTML, CSS, Bootstrap, jQuery, DataTables      |
| Backend         | PHP (MVC architecture, custom functions)      |
| Database        | MySQL, phpMyAdmin                              |
| Libraries       | Placeholder APIs, frontend libraries           |

---

# 3. 📂 Code and Directory Structure

The project implements a structured architecture similar to MVC, ensuring maintainability and modularity.

---

## 3.1 Main Directories and Their Purpose

### 📁 Root Directory (`library-system/`)

| Directory/File | Layer/Concern | Purpose |
| :--- | :--- | :--- |
| `index` | **Application Entry Point** | The main file that initializes the application, loads configuration, and starts the server. |
| `assets` | **Static Content** | Contains all client-side resources (CSS, JavaScript, images). |
| `config` | **Configuration** | Holds environment, database, and application-wide settings. |
| `controllers` | **Business Logic/Routing** | Manages application flow, processes user input, and interacts with the `models` layer. |
| `helpers` | **Utility Functions** | Stores reusable functions that support logic across multiple layers. |
| `models` | **Data Access Layer (DAL)** | Manages all direct database interactions (CRUD operations). |
| `views` | **Presentation Layer** | Contains files responsible for generating the User Interface (UI). |

---

### 📁 Detailed Directory Breakdowns

#### 📌 A. `/models` — Data Access Layer

| Model File | Purpose |
| :--- | :--- |
| `BaseModel` | Provides common database functionality (connection handling). |
| `AuthModel` | Handles database operations related to user authentication. |
| `BookModel` | Manages information for books, inventory, and status. |
| `BorrowModel` | Records successful loan transactions. |
| `BorrowRequestModel` | Handles pending requests before the borrowModel handles them. |
| `ReservationModel` | Manages book reservations. |
| `PenaltyModel` | Handles overdue and fine-related records. |
| `ClearanceModel` | Tracks clearance-related data for users. |
| `CatalogModel`, `CategoryModel`, `StaffModel` | Additional models for other core entities. |

---

#### 📌 B. `/views` — Presentation Layer

| View File/Folder | Purpose |
| :--- | :--- |
| `login`, `signup` | Login and registration templates. |
| `librarian` | Templates for librarian dashboards and functions. |
| `staff` | Templates for staff dashboards and operations. |
| `student`, `teacher` | Templates for user dashboards (students/teachers). |
| `modals` | Reusable partial files/components that student and teachers uses. |

---

#### 📌 C. `/helpers` — Utility Layer

| Helper File | Purpose |
| :--- | :--- |
| `auth_check` | Performs authentication and session validation, including handling the Remember Me token before granting access to protected pages. |
| `utils` | General-purpose helper functions. |

---

#### 📌 D. `/assets` — Static Content

| File/Folder | Purpose |
| :--- | :--- |
| `bootstrap`, `jQuery`, `DataTables` | Third-party libraries for UI and table features. |
| `images` | Logos, icons, and illustrations. |
| Custom `.css` & `.js` | Application-specific styles and scripts. |

---

## 3.2 Entry Point and Configuration Files

The system uses a simple entry-point structure. The main access file, index.php, serves as the initial redirect to the login page, ensuring that all users begin authentication before accessing any part of the system.

The /config directory contains only the database configuration file, which establishes the connection used by all models. This centralizes the database credentials and allows models to reuse a single connection setup, improving consistency and maintainability.

---

# 4. 📊 Database Schema Details

The system uses a relational database structure designed to efficiently manage books, users, transactions, reservations, penalties, and semester clearances. The schema is organized into eight core tables that support all major operations of the Library Management System.

---

## 4.1 Core Tables

Below is a summary of the essential tables and their roles in the system:

- **books** – Stores book information such as titles, authors, categories, and availability status.  
- **categories** – Defines and organizes the categories used by the book catalog.  
- **users** – Contains all user accounts, including librarians, staff, students, and teachers.  
- **borrow_requests** – Holds pending borrow requests submitted by users awaiting approval.  
- **borrows** – Stores approved borrow transactions along with due dates, return dates, and related records.  
- **reservations** – Manages book reservations made by users before borrowing.  
- **penalties** – Tracks fines for late returns and other penalty-related information.  
- **clearance** – Records a user’s clearance status for each semester based on their completed obligations.

This structure supports the end-to-end workflow—from catalog browsing, borrowing, and returning books, up to clearance verification.

---

## 4.2 Key Relationships

Below is a high-level summary of how the tables interact:

- **users ↔ borrows / reservations / borrow_requests**  
  A user can submit multiple requests, borrow several books, and place reservations.

- **books ↔ borrows / reservations / borrow_requests**  
  Each transaction or request references a specific book.

- **borrows ↔ penalties**  
  Overdue or late returns may generate penalty records linked to the borrow.

- **users ↔ penalties**  
  Each penalty is associated with a particular user.

- **users ↔ clearance**  
  Each user has clearance status tied to their account every semester.

- **categories ↔ books**  
  Every book belongs to a single category.

These relationships ensure the database maintains consistency, accurate tracking, and efficient reporting throughout the system.

---

# 5. 📐 Design and Implementation Decisions

---

## 5.1 Business Logic Decisions

### A. Borrowing & Returning

#### **Borrow Workflow**
Users submit a borrow request → staff reviews and approves → a new record is created in the **borrows** table.  
This ensures all borrow activities are supervised by library staff.

#### **Borrowing Limit (Students)**
Students can borrow up to **3 books at the same time**, including picked-up reservations.

This limit exists to:
- Maintain integrity in the borrowing process  
- Prevent book hoarding  
- Simplify borrowing rules for users  
- Satisfy project requirements  

If a student reaches the limit, the **borrow button is disabled**.  
When they return a book, the limit resets and they can borrow again.

#### **Due Date Rules**
- Every borrowed book has a **fixed 14-day (2-week) due date**.  
- There are **no automatic penalties** for overdue books.
- However, overdue books **keep the student’s borrowing limit reduced** until returned, preventing them from borrowing again.

#### **Penalty Logic**
- Penalties are **manually triggered** by staff during clearance week.
- This reflects real school practice where clearance schedules vary.
- A system message warns staff to follow proper penalty procedures.
- Future versions can automate penalties if clearance dates become fixed.

---

### B. Reservations & Requests

#### **Reservation System**
- Follows a **first-come-first-served** rule.
- If the user does not pick up the book within **3 days**, the reservation is automatically canceled.

#### **Separation of `borrow_requests` and `borrows`**
Two tables are intentionally used:

- **borrow_requests** → pending requests waiting for staff approval  
- **borrows** → approved and official borrow records  

This design:
- Makes backend logic easier to read  
- Prevents accidental borrowing without approval  
- Keeps workflows clear and maintainable  
- Allows future expansion (e.g., audit logs, notification history)

---

### C. User Roles

- **Librarian** – manages all book-related operations (books, categories, inventory).  
- **Staff** – handles transactions such as borrow approvals, reservation processing, returns, clearance, and penalties.  
- **Students/Teachers (Users)** – can submit borrow and reservation requests only.

#### **Reason for Role Separation**
Role separation:
- Ensures clean access control  
- Supports future scalability  
- Reflects real-life library operations  
- Distinguishes students (limited borrowing) from teachers (more flexible access)

---

## 5.2 Security Considerations

### A. Authentication
- Users log in using **username + password**.
- Passwords are **hashed** before being stored.
- Input fields use sanitization (`htmlspecialchars()`, `trim()`) and XSS protection.

### B. Authorization
- Page access is controlled through **session variables**.
- A role-checking function ensures users only access their designated dashboard  
  (e.g., students cannot open staff or librarian pages).

### C. Data Security

#### **SQL Injection Protection**
All queries use:
- **Prepared statements** with `bind_param()`
- Sanitized inputs
- Both backend and frontend validation  

> Standard PHP prepared statements were used to prevent SQL injection.

#### **Password Security**
Passwords are stored using secure PHP hashing functions.

#### **Session & Cookie Usage**
The system includes:
- Secure session handling  
- Logout logic  
- Optional cookies for controlled “remember me” functionality  

---

## 5.3 Error Handling Strategy

### A. System Errors
- Backend errors are wrapped in **try/catch** blocks.
- Users never see raw PHP/database errors; instead, they receive safe messages.

### B. Form & Input Errors
Users receive immediate, clear feedback through page alerts when:
- Inputs are invalid  
- Required fields are missing  
- Requests break borrowing rules  

### C. Fail-Safe Logic
- The system checks if the book was just borrowed before approving a request.
- Users cannot borrow the same book twice or hoard copies because available stock is tracked.
- Returns must be manually confirmed by staff, matching real-life physical return processes.

---

# 6. 🚀 Deployment and Maintenance

This section describes how the system is installed, what it requires to run, and how it is maintained during development.

---

## 6.1 Installation Instructions for Developers

1. **Clone or download the project folder**  
   Place it inside your server root directory (e.g., `htdocs` for XAMPP).

2. **Import the database**  
   - Open **phpMyAdmin**  
   - Create a new database (e.g., `library_system`)  
   - Import the provided `.sql` file

3. **Configure database connection**  
   Update `/config/database.php` with your local MySQL credentials:  
   - Host  
   - Username  
   - Password  
   - Database name  

4. **Start the server**  
   - Start Apache + MySQL in XAMPP  
   - Access the system via:  
     `http://localhost/library-system/`

5. **Initial login**  
   Use the provided test accounts for admin, staff, librarian, or students. (Found in the index.php as comments).

---

## 6.2 System Dependencies

### **Software Requirements**
- **PHP 8+**
- **MySQL / MariaDB**
- **Apache Server** (XAMPP recommended)
- Runs on **pure PHP, HTML, CSS, JS** (no external frameworks)

### **Required PHP Extensions**
- `mysqli`
- `session`
- `mbstring`

### **Browser Compatibility**
- Google Chrome (recommended)  
- Firefox  
- Microsoft Edge

---

## 6.3 Testing Strategy

### **Functional Testing**
Manually tested all core features:
- Login & role redirection  
- Borrow request workflow  
- Reservation creation and auto-cancellation  
- Borrow limits  
- Return and clearance process  
- Penalty application  
- Book and category management  

### **Validation Testing**
Ensured that:
- Invalid inputs are rejected  
- Borrow limits cannot be bypassed  
- Students cannot access staff/librarian pages  
- Duplicate borrowing of the same book is prevented  

### **Fail-Safe Testing**
- Attempted borrowing unavailable/out-of-stock books  
- Tried accessing restricted pages while logged out  
- Checked expired reservations  
- Validated session recovery after server restart  

### **Error Handling**
Verified that:
- Errors return user-friendly messages  
- No raw PHP or SQL errors are exposed  
- try/catch blocks manage unexpected failures

