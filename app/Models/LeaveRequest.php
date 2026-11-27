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

    public function getAll(): array
    {
        $db = Database::getConnection();

        return $db->query(
            "SELECT lr.*,
                    e.full_name AS employee_name,
                    e.is_active AS employee_active,
                    d.name AS department_name,
                    u.username AS reviewer_name
             FROM leave_requests lr
             JOIN employees e
                 ON lr.employee_id = e.id
             JOIN departments d
                 ON e.department_id = d.id
             LEFT JOIN users u
                 ON lr.reviewed_by = u.id
             ORDER BY
                 CASE WHEN lr.status = 'pending'
                      THEN 0 ELSE 1 END,
                 lr.created_at DESC,
                 lr.id DESC"
        )->fetchAll();
    }

    public function review(
        int $id,
        string $status,
        int $reviewerId
    ): bool {
        if (!in_array(
            $status,
            ['approved', 'rejected'],
            true
        )) {
            return false;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare(
            "UPDATE leave_requests
             SET status = :status,
                 reviewed_by = :reviewer,
                 reviewed_at = CURRENT_TIMESTAMP
             WHERE id = :id
             AND status = 'pending'
             AND (
                 :decision = 'rejected'
                 OR EXISTS (
                     SELECT 1 FROM employees
                     WHERE employees.id =
                         leave_requests.employee_id
                     AND is_active = 1
                 )
             )"
        );

        $stmt->execute([
            'status' => $status,
            'reviewer' => $reviewerId,
            'id' => $id,
            'decision' => $status
        ]);

        return $stmt->rowCount() === 1;
    }
}