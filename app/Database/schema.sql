CREATE TABLE IF NOT EXISTS departments (
                                           id INTEGER PRIMARY KEY AUTOINCREMENT,
                                           name TEXT NOT NULL UNIQUE,
                                           description TEXT
);

CREATE TABLE IF NOT EXISTS positions (
                                         id INTEGER PRIMARY KEY AUTOINCREMENT,
                                         name TEXT NOT NULL UNIQUE,
                                         description TEXT
);

CREATE TABLE IF NOT EXISTS employees (
                                         id INTEGER PRIMARY KEY AUTOINCREMENT,
                                         full_name TEXT NOT NULL,
                                         email TEXT NOT NULL UNIQUE,
                                         phone TEXT,
                                         hire_date TEXT NOT NULL,
                                         department_id INTEGER NOT NULL,
                                         position_id INTEGER NOT NULL,
                                         is_active INTEGER NOT NULL DEFAULT 1
                                         CHECK (is_active IN (0, 1)),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (position_id) REFERENCES positions(id)
    );

CREATE TABLE IF NOT EXISTS users (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     employee_id INTEGER UNIQUE,
                                     username TEXT NOT NULL UNIQUE,
                                     password_hash TEXT NOT NULL,
                                     role TEXT NOT NULL DEFAULT 'employee'
                                     CHECK (role IN ('admin', 'employee')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (role = 'admin' OR employee_id IS NOT NULL),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
    );

CREATE TABLE IF NOT EXISTS leave_requests (
                                              id INTEGER PRIMARY KEY AUTOINCREMENT,
                                              employee_id INTEGER NOT NULL,
                                              start_date TEXT NOT NULL,
                                              end_date TEXT NOT NULL,
                                              reason TEXT,
                                              status TEXT NOT NULL DEFAULT 'pending'
                                              CHECK (status IN ('pending', 'approved', 'rejected')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INTEGER,
    reviewed_at TEXT,
    CHECK (start_date <= end_date),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
    );

CREATE INDEX IF NOT EXISTS idx_employees_department
    ON employees(department_id);

CREATE INDEX IF NOT EXISTS idx_employees_position
    ON employees(position_id);

CREATE INDEX IF NOT EXISTS idx_leave_employee
    ON leave_requests(employee_id);