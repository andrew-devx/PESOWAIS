# 💰 PesoWais

**PesoWais** is a smart, web-based budgeting and expense tracking application designed for Filipinos. It helps users manage their finances, track loans (utang), set savings goals, and monitor their financial mood. Built with **PHP** and **MySQL**, it features a modern, responsive interface powered by **Tailwind CSS**.

## 🚀 Key Features

*   **📊 Dynamic Dashboard:**
    *   **Mood Meter:** Visual representation of financial health based on spending.
    *   **Financial Summary:** Quick view of Total Balance, Income, Expenses, and Debts.
    *   **Recent Activity:** Snapshot of latest transactions.
*   **💸 Transaction Management:**
    *   Full CRUD (Create, Read, Update, Delete) for Income and Expenses.
    *   **Export to CSV:** Download transaction history with filters (Date, Category).
    *   **Category Analysis:** Visual breakdown of spending habits.
*   **🎯 Savings Goals:**
    *   Create and track progress for specific financial targets.
    *   "Add Money" feature to incrementally reach goals.
    *   Visual progress bars and "Achieved" status.
*   **🤝 Loan Tracker (Utang):**
    *   Manage money owed to you and money you owe others.
    *   Track partial payments and mark loans as fully paid.
*   **🔐 Secure & User-Friendly:**
    *   User authentication with email verification (OTP).
    *   Password reset via email.
    *   Profile management with avatar upload.
    *   Mobile-responsive design with a dedicated mobile menu.

## 🛠️ Tech Stack

*   **Frontend:** HTML5, CSS3, JavaScript, **Tailwind CSS** (CDN), DaisyUI, FontAwesome
*   **Backend:** PHP (Vanilla)
*   **Database:** MySQL
*   **Deployment:** XAMPP / WAMP / Shared Hosting

## 💻 Installation & Setup

1.  **Clone the repository**
    ```bash
    git clone https://github.com/YourUsername/PesoWais.git
    cd PesoWais
    ```

2.  **Configure the Database**
    *   Open **phpMyAdmin** (usually `http://localhost/phpmyadmin`).
    *   Create a new database named `pesowais_db`.
    *   Import the provided SQL file (e.g., `database.sql` or `migrations/*.sql`) to set up tables.

3.  **Set Up Configuration Files**
    *   **Database:**
        *   Rename `includes/constants.example.php` to `includes/constants.php`.
        *   Update the file with your database credentials (hostname, username, password, database name).
    *   **API & Email:**
        *   Rename `includes/config.example.php` to `includes/config.php`.
        *   Add your **Gemini API Key** (for AI features) and **SMTP Settings** (for email verification/reset).

4.  **Run the Application**
    *   Ensure the project folder is in your local server directory (e.g., `htdocs` in XAMPP).
    *   Open your browser and navigate to `http://localhost/PesoWais`.

## 👤 Author

**Charles Andrew**
*   Aspiring Full Stack Web Developer

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
