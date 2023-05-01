<?php

namespace AttendanceApp\Src\test\Interface\Controller;

use AttendanceApp\Src\Inteface\Controller\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function test_create()
    {
        // given
        $employeeId = 1;
        $companyId = 2;
        $baseDate = '2023-04-01';
        $request = [
            'employee_id' => $employeeId,
            'company_id' => $companyId,
            'base_date' => $baseDate
        ];
        // when
        $actual = Request::create($request);
        // then
        $this->assertEquals($employeeId, $actual->getEmployeeId());
        $this->assertEquals($companyId, $actual->getCompanyId());
        $this->assertEquals($baseDate, $actual->getBaseDate());
    }
}