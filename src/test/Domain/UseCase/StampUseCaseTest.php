<?php

namespace AttendanceApp\Src\test\Domain\UseCase;

require_once(__DIR__ . "/../../../../vendor/autoload.php");

use AttendanceApp\Src\Domain\Model\Stamp;
use AttendanceApp\Src\Domain\UseCase\StampUseCase;
use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StampUseCaseTest extends TestCase
{
    /** @var StampGateway&MockObject */
    private StampGateway $gatewayMock;

    /** @var StampUseCase */
    private StampUseCase $useCase;


    public function setUp(): void
    {
        $this->gatewayMock = $this->createMock(StampGateway::class);
        $this->useCase = new StampUseCase($this->gatewayMock);
    }

    public function test_getBy()
    {
        //given
        $company_id = 1884310;
        $employee_id = 1164735;
        $type = 1;
        $base_date = '2023-04-29';
        $date_time = "2023-04-29 21:04:20";
        //then

        $expected = Stamp::create(
            $company_id,
            $employee_id,
            $type,
            $base_date,
            $date_time
        );

        $this->gatewayMock->expects($this->once())
            ->method('findBy')
            ->with(
                $company_id,
                $employee_id,
                $base_date
            )
            ->willReturn($expected);

        //when
        $actual = $this->useCase->getBy($company_id, $employee_id, $base_date);
        $this->assertEquals($expected, $actual);
    }
}