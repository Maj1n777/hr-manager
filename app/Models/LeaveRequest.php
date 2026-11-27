<?php

require_once __DIR__ . '/../Core/Database.php';

class LeaveRequest
{
    public function getByEmployee(int $employeeId): array
    {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'SELECT *
             FROM leave_requests
             WHERE employee_id = :employee_id
             ORDER BY created_at DESC, id DESC'
        );

        $stmt->execute([
            'employee_id' => $employeeId
        ]);

        return $stmt->fetchAll();
    }

    public function create(
        int $employeeId,
        string $startDate,
        string $endDate,
        string $reason
    ): bool {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            "INSERT INTO leave_requests
             (employee_id, start_date, end_date, reason)
             SELECT :employee_id, :start_date,
                    :end_date, :reason
             WHERE EXISTS (
                 SELECT 1 FROM employees
                 WHERE id = :active_employee
                 AND is_active = 1
             )
             AND NOT EXISTS (
                 SELECT 1
                 FROM leave_requests
                 WHERE employee_id = :check_employee
                 AND status IN ('pending', 'approved')
                 AND start_date <= :check_end
                 AND end_date >= :check_start
             )"
        );

        $stmt->execute([
            'employee_id' => $employeeId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $reason,
            'active_employee' => $employeeId,
            'check_employee' => $employeeId,
            'check_end' => $endDate,
            'check_start' => $startDate
        ]);

        return $stmt->rowCount() === 1;
    }
}