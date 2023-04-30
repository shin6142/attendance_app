<?php

namespace AttendanceApp\Src\test\Domain\UseCase;

require_once(__DIR__ . "/../../../../vendor/autoload.php");

use AttendanceApp\Src\Domain\Model\Stamp;
use AttendanceApp\Src\Domain\Model\Stamps;
use AttendanceApp\Src\Domain\UseCase\DailyStampsDto;
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

    public function test_getByDate()
    {
        //given
        $companyId = 1;
        $employeeId = 1;
        $date = '2023-04-29';
        $startDateTime = "2023-04-29 8:04:20";
        $leaveDateTime = "2023-04-29 12:04:20";
        $backDateTime = "2023-04-29 13:04:20";
        $endDateTime = "2023-04-29 18:04:20";

        //then
        $stampArr = [
            Stamp::create($companyId,$employeeId,1, $date, $startDateTime),
            Stamp::create($companyId,$employeeId,2, $date, $leaveDateTime),
            Stamp::create($companyId,$employeeId,3, $date, $backDateTime),
            Stamp::create($companyId,$employeeId,4, $date, $endDateTime)
        ];
        $stamps = new Stamps($stampArr);

        $this->gatewayMock->expects($this->once())
            ->method('findByDate')
            ->with(
                $companyId,
                $employeeId,
                $date
            )
            ->willReturn($stamps);

        //when
        $expected = new DailyStampsDto(
            $companyId,
            $employeeId,
            $date,
            $startDateTime,
            $leaveDateTime,
            $backDateTime,
            $endDateTime,
        );
        $actual = $this->useCase->getByDate($companyId, $employeeId, $date);
        $this->assertEquals($expected, $actual);
    }
}