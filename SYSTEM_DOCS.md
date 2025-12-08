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
- **4.2** [Entity-Relationship Diagram](#entity-relationship-diagram)
- **4.3** [Key Relationships](#key-relationships)

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

## 3.2 Detailed Directory Breakdowns

### 📌 A. `/models` — Data Access Layer

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

### 📌 B. `/views` — Presentation Layer

| View File/Folder | Purpose |
| :--- | :--- |
| `login`, `signup` | Login and registration templates. |
| `librarian` | Templates for librarian dashboards and functions. |
| `staff` | Templates for staff dashboards and operations. |
| `student`, `teacher` | Templates for user dashboards (students/teachers). |
| `modals` | Reusable partial files/components that student and teachers uses. |

---

### 📌 C. `/helpers` — Utility Layer

| Helper File | Purpose |
| :--- | :--- |
| `auth_check` | Performs authentication and session validation, including handling the Remember Me token before granting access to protected pages. |
| `utils` | General-purpose helper functions. |

---

### 📌 D. `/assets` — Static Content

| File/Folder | Purpose |
| :--- | :--- |
| `bootstrap`, `jQuery`, `DataTables` | Third-party libraries for UI and table features. |
| `images` | Logos, icons, and illustrations. |
| Custom `.css` & `.js` | Application-specific styles and scripts. |

