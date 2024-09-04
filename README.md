# Budgeting System

## Overview

The Budgeting System is a web application designed to manage the creation, approval, and allocation of budgets across different departments within an organization. The system supports various user roles with specific permissions, including Admin, Viewer, Editor, Finance Manager, and Budget Controller. Each role has a dedicated dashboard to perform their respective tasks.

## Features

- **Role-Based Access Control:** Each user role has specific permissions and access to different parts of the system.
- **Budget Creation and Approval:** Editors (Heads of Departments) can create budgets, which are then reviewed and approved by the Admin or Budget Controller.
- **Fund Allocation:** The Finance Manager allocates funds to approved budgets, either partially or fully.
- **Notifications:** The system sends notifications when a budget is approved, rejected, or when funds are allocated.
- **Departmental Organization:** Budgets and messages are organized by departments, ensuring that each department has control over its financial planning.

## User Roles and Permissions

### 1. **Admin**
   - Manage user accounts.
   - Review and approve budgets.
   - Access all department budgets.

### 2. **Viewer**
   - View budgets created by the Head of Department (HOD) of their department.
   - Cannot create or edit budgets.

### 3. **Editor (Head of Department)**
   - Create and edit budgets for their department.
   - Submit budgets for approval.

### 4. **Finance Manager**
   - View approved budgets.
   - Allocate funds to approved budgets.

### 5. **Budget Controller**
   - Review and approve or reject budgets.
   - Does not belong to any specific department.

## Installation

### Prerequisites

- PHP 7.4 or later
- MySQL 5.7 or later
- Apache or Nginx server
- Composer (optional, for dependency management)

### Setup

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/OnyangoOdipo/Budgeting.git
   cd Budgeting
   ```

2. **Set Up the Database:**
   - Create a new MySQL database.
   - Import the provided SQL schema to set up the necessary tables.
   - Update the `db.php` file with your database credentials.

3. **Configure the Environment:**
   - Ensure all environment variables and configurations are correctly set in the `db.php` file.
   - Set up your web server to point to the `frontend/src/` directory as the document root.

4. **Install Dependencies:**
   - If you're using Composer, navigate to the root directory and run:
     ```bash
     composer install
     ```

5. **Run the Application:**
   - Start your local server (Apache or Nginx).
   - Access the application via `http://localhost/budgeting-system/frontend/src/index.php`.

## Usage

### Registering Users

- Navigate to the Admin dashboard.
- Click on the "Register New User" button to open the registration modal.
- Fill in the user details, including username, first name, last name, email, phone number, and role.
- Select the appropriate department for Editors. Leave the department field empty for Finance Manager and Budget Controller roles.
- If the user is an Editor and is the Head of Department (HOD), check the "Is Head of Department" box.
- Submit the form to register the user.

### Logging In

- Users can log in by navigating to the login page and entering their email and password.
- Based on their role, they will be redirected to their respective dashboard.

### Creating Budgets (Editor)

- Editors can log in and navigate to their dashboard.
- Use the "Create Budget" button to add new budgets, including items, quantities, and costs.
- Submit the budget for review and approval.

### Approving Budgets (Admin/Budget Controller)

- Log in as Admin or Budget Controller.
- Review budgets in the "Processing" state.
- Approve or reject budgets using the appropriate action buttons.

### Allocating Funds (Finance Manager)

- Log in as Finance Manager.
- View approved budgets.
- Allocate funds by entering the amount and confirming the allocation.

### Notifications

- The system sends automatic notifications to users when their budget is approved, rejected, or when funds are allocated.

## Folder Structure

- `backend/` - Contains backend PHP scripts, including database connection, registration, and login scripts.
- `frontend/` - Contains the frontend files, including HTML, CSS, and JavaScript for the user interface.
- `uploads/` - Stores user profile images.
- `db.php` - Database connection configuration.
- `register.php` - Script to handle user registration.
- `login.php` - Script to handle user login.

## Contributing

If you'd like to contribute to this project, please fork the repository and create a new branch for your features or bug fixes. Once your changes are tested, submit a pull request.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Contact

For any questions or support, please contact [shadrackonyango30@gmail.com].