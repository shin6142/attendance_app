<?php

namespace AttendanceApp\Src\test\Domain\UseCase;

require_once(__DIR__ . "/../../../../vendor/autoload.php");

use AttendanceApp\Src\Domain\UseCase\StampUseCase;
use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StampUseCaseTest extends TestCase
{
    /** @var StampGateway&MockObject */
    private StampGateway $gatewayMock;

    /** @var StampUseCase*/
    private StampUseCase $useCase;


    public function setUp(): void
    {
        $this->gatewayMock  = $this->createMock(StampGateway::class);
        $this->useCase = new StampUseCase($this->gatewayMock);
    }

    public function test_get()
    {
        //given
        $company_id = 1884310;
        $employee_id = 1164735;
        $base_date = '2023-04-29';
        //when
        $actual = $this->useCase->getBy($company_id, $employee_id, $base_date);
        //then
        $expected = [
            [
                "id" => 1,
                "0" => 1,
                "employee_id" => 1164735,
                "1" => 1164735,
                "company_id" => 1884310,
                "2" => 1884310,
                "type" => "1",
                "3" => "1",
                "base_date" => "2023-04-29",
                "4" => "2023-04-29",
                "datetime" => "2023-04-29 21:04:20",
                "5" => "2023-04-29 21:04:20",
                "created_at" => "2023-04-29 12:50:20",
                "6" => "2023-04-29 12:50:20"
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    public function test_getNew()
    {
        //given
        $company_id = 1884310;
        $employee_id = 1164735;
        $base_date = '2023-04-29';

        //then
        $expected = [
            [
                "id" => 1,
                "0" => 1,
                "employee_id" => 1164735,
                "1" => 1164735,
                "company_id" => 1884310,
                "2" => 1884310,
                "type" => "1",
                "3" => "1",
                "base_date" => "2023-04-29",
                "4" => "2023-04-29",
                "datetime" => "2023-04-29 21:04:20",
                "5" => "2023-04-29 21:04:20",
                "created_at" => "2023-04-29 12:50:20",
                "6" => "2023-04-29 12:50:20"
            ]
        ];

        $this->gatewayMock->expects($this->once())
            ->method('findBy')
            ->with(
                $company_id,
                $employee_id,
                $base_date
            )
            ->willReturn($expected);

        //when
        $actual = $this->useCase->getByNew($company_id, $employee_id, $base_date);
        $this->assertEquals($expected, $actual);
    }
}