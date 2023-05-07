<?php

namespace AttendanceApp\Src\Context\stamp\test\Domain\UseCase;

require_once(__DIR__ . "/../../../../../../vendor/autoload.php");

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use AttendanceApp\Src\Context\stamp\Domain\Service\DailyStampsService;
use AttendanceApp\Src\Context\stamp\Domain\UseCase\DailyStampsDto;
use AttendanceApp\Src\Context\stamp\Domain\UseCase\StampUseCase;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\StampGateway;
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
        $this->service = new DailyStampsService();
        $this->useCase = new StampUseCase($this->gatewayMock, $this->service);
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

        //then
        $stampArr = [
            Stamp::create($companyId, $employeeId, 1, $date, $startDateTime),
            Stamp::create($companyId, $employeeId, 2, $date, $leaveDateTime),
            Stamp::create($companyId, $employeeId, 3, $date, $backDateTime),
        ];
        $stamps = new Stamps($stampArr);

        $this->gatewayMock->expects($this->once())
            ->method('findBy')
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
            null
        );
        $actual = $this->useCase->getByDate($companyId, $employeeId, $date);
        $this->assertEquals($expected, $actual);
    }

    public function test_record()
    {
        //given
        $companyId = 1;
        $employeeId = 1;
        $date = '2023-04-01';
        $datetime = '2023-04-01 12:00:00';

        $stamp1 = Stamp::create($companyId, $employeeId, 1, '2023-04-01', '2023-04-01 8:00:00');
        $stamps = new Stamps([$stamp1]);
        //then
        $this->gatewayMock->expects($this->once())
            ->method('findBy')
            ->with($companyId, $employeeId, $date)
            ->willReturn($stamps);

        $stamp2 = Stamp::create($companyId, $employeeId, 2, '2023-04-01', '2023-04-01 12:00:00');
        $this->gatewayMock->expects($this->once())
            ->method('save')
            ->with($stamp2);

        //when
        $this->useCase->record($companyId, $employeeId, $date, $datetime);
    }
}