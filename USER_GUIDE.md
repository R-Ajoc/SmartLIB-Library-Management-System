# 📘 SmartLIB – User Guide
### Welcome to SmartLIB Library Management System.
This guide provides simple, step-by-step instructions for each user role: Student, Teacher, Staff, and Librarian.

---

## 🔐 Logging In
1. Open any web browser (Chrome, Edge, etc.).
2. In the address bar, go to: **http://localhost/library-system**
3. You will be taken to the Login Page

![Login Page](doc_images/login.png)

4. Enter your **Username** and **Password**.
5. Click **Login** to continue to your dashboard.

### ✔ Notes
- Make sure your credentials are correct.
- If login fails, an error message will appear on the screen.
- Refer to sample accounts in index.php
  
---

## 📝 Signing Up
1. From the Login Page, click **Sign Up**.
2. You will be redirected to the registration form.

![Sign Up Page](doc_images/signup.png)

3. You must fill out all required fields to proceed to the next page of the form to fill out the username and password.
4. Review your information and click **Complete Sign up**
5. If registration is successful, you will be redirected to the login page.

### ✔ Notes
- All fields must be filled out to proceed.
- Password and Confirm Password must match.
- If an account with the same username already exists, an error message will appear.
- Each Roles have different access levels once logged in.

---

## 👩‍🎓 Student & Teacher Pages

Navigate the Top Menu to go to the pages.

![Student Dashboard Page](doc_images/student_page.png)
![Teacher Dashboard Page](doc_images/teacher_page.png)

### 1. Dashboard

The **Dashboard** provides a summary of your library activity:

- **Latest Books**: Displays recently added books.  
  - Click **Borrow** or **Reserve** to request a book.  
  - Buttons will be **disabled** if you have reached your borrowing limit.
- **Browse Library**:  
  - Click the **“Go to Library”** button in Latest Books or use the **Top Menu** to access the full library catalog.
- **Top Menu User Dropdown**:  
  - Click your **name** in the top menu to reveal a dropdown menu.  
  - Dropdown options:
    - **Profile** – View your profile
    - **Settings** – Update Profile and other account settings
    - **Logout** – Sign out of the system

Successful requests show a **confirmation message**.  
Failed requests show an **error message** (e.g., book already borrowed or request already exists).

---

### 2. Library Catalog

The **Library Catalog** allows users to browse and search for books:

- **Search Bar**: Filter books by title, author, or category.  
- **Book Cards**: Each book displays:
  - Title, author, description, and category
  - **Borrow** and **Reserve** buttons
    - Buttons are disabled if the borrowing limit is reached
    - Successful requests show a success message
    - Failed requests show an error message

---

### 3. Teacher Exclusive Page

Teachers have an additional page called **Exclusive Books**:

- Layout and navigation are the same as the Library Catalog.
- Only books intended for teachers are displayed.
- Borrowing and reserving works the same way as the general library.

---

### 4. My Records

The **My Records** page allows users to track their activity:

- **Borrowed**: Shows books that have been approved by staff.
  - **Sem 1** – Accessible by default.
  - **Sem 2** – Locked until clearance is approved by staff.
- **Book Requests Tab**: Track pending borrow requests.
- **Reservations Tab**: View reserved books and their pickup status.

---

# 🧑‍💼 Librarian Pages

The Librarian has access to two main pages via the **Top Menu**:

1. **Dashboard**  
2. **Book Management**

---

## 1. Dashboard

The **Dashboard** provides a quick overview of the library collection:

- **Library Collection Status**:
  - Total books
  - Available books
  - Categories
  - Other statistics
- **Recent Activities**:
  - Displays the last 5 book activities, such as modifications or new additions.

This allows the Librarian to monitor the library’s overall status at a glance.

---

## 2. Book Management

The **Book Management** page allows the Librarian to manage all books:

![Librarian Dashboard Page](doc_images/librarian_page.png)

- **Books Table**:
  - Each row represents a book.
  - **Edit Button (Blue)** – Click to modify the book’s metadata.
  - **Archive Button (Red)** – Click to archive a book.  
    - Once archived, the button changes to **Restore**, which can be clicked to bring the book back.

- **Top Right Buttons**:
  - **Add Book** – Opens a modal to add a new book with required details.
  - **Category Management** – Manage categories:
    - Add, edit, or delete categories.
    - The system ensures a category is empty before allowing deletion to prevent accidental removal of books.

This page is designed for **simple and efficient navigation**, making it easy for the Librarian to maintain the library catalog.
