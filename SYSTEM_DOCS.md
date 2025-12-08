# ⚙️ System Documentation: Library Management System
This document details the technical architecture, database design, and key components of the Library Management System.


## Table of Contents

1. [System Overview](#system-overview)  
   1.1 [Purpose and Scope](#purpose-and-scope)  
   1.2 [Key Features](#key-features)  

2. [Architecture and Technology Stack](#architecture-and-technology-stack)  
   2.1 [High-Level Architecture](#high-level-architecture)  
   2.2 [Technology Stack](#technology-stack)  

3. [Code and Directory Structure](#code-and-directory-structure)  
   3.1 [Main Directories and Their Purpose](#main-directories-and-their-purpose)  
   3.2 [Entry Point and Configuration Files](#entry-point-and-configuration-files)  

4. [Database Schema Details](#database-schema-details)  
   4.1 [Core Tables](#core-tables)  
   4.2 [Entity-Relationship Diagram](#entity-relationship-diagram)  
   4.3 [Key Relationships](#key-relationships)  

5. [Design and Implementation Decisions](#design-and-implementation-decisions)  
   5.1 [Business Logic Decisions](#business-logic-decisions)  
   5.2 [Security Considerations](#security-considerations)  
   5.3 [Error Handling Strategy](#error-handling-strategy)  

6. [Deployment and Maintenance](#deployment-and-maintenance)  
   6.1 [Installation Instructions for Developers](#installation-instructions-for-developers)  
   6.2 [System Dependencies](#system-dependencies)  
   6.3 [Testing Strategy](#testing-strategy)



## System Overview

### Purpose and Scope
The Library System is a **web-based application** designed to efficiently manage library operations and cater to the needs of different users, including librarians, staff, students, and teachers. Its main purpose is to streamline borrowing, reservation, and book management processes, as well as to track user transactions, penalties, and clearance status. The system ensures accuracy and reduces human errors in handling library operations, making the management of books and user transactions more efficient and organized.

The system serves the following roles:  
- **Librarian**: Manages all aspects of the library's books, including metadata and categories.  
- **Staff**: Handles borrowing and reservation transactions, monitors user activities, manages clearance and penalties.  
- **Students and Teachers (Users)**: Request book borrowings and reservations, track their borrow history, and manage their clearance. Teachers have additional privileges, including no borrowing limit and access to exclusive books, while students have borrowing limits and must settle unpaid books if not returned.

### Key Features

#### Librarian
- **Dashboard**: View reports such as total books, available books, book categories, low-stock books, archived books, and recent activity (timeline of last 5 added or modified books).  
- **Books Management**: Manage books including adding, editing metadata, archiving/restoring books, and managing categories (add, edit, delete).

#### Staff
- **Dashboard**: Access analytics including pending requests, active reservations, overdue loans, total clearance, and students with penalties.  
- **Borrower Management**: View detailed user activities, including borrowed books, returned books, and overdue items.  
- **Loan Management**: Manage borrowing, reservation requests, returns, and reservation pickups through four tabs.  
- **Clearance**: Track user clearance for students and teachers, preventing mistakes by automatically disabling clearance for users with unreturned books or pending penalties.  
- **Penalties**: Apply penalties only after clearance week with a warning system to prevent errors.  

#### Students
- **Dashboard**: View reports including clearance status, borrowed books, reserved books, and remaining borrowing limits. Preview latest books and access the library catalog quickly.  
- **Library Catalog**: Search, borrow, or reserve publicly available books, filter by categories.  
- **My Records**: View borrow and reservation history for each semester with dates preserved.

#### Teachers
- **Dashboard**: Similar to students but with overdue tracking instead of borrowing limits.  
- **Exclusive Catalog**: Access books exclusive for faculty use.  
- **My Records**: Same as students, including borrow history and reservations.

