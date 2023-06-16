<?php

class AttendanceLog
{
    public function __construct(
        private readonly int $employee_id,
        private readonly int $company_id,
        private readonly string $base_date,
        private readonly string $start_datetime,
        private readonly string $leave_datetime,
        private readonly string $back_datetime,
        private readonly string $end_datetime,
    ){}

}