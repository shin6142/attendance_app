<?php

namespace AttendanceApp\Src\Context\stamp\test\Domain\UseCase;

require_once(__DIR__ . "/../../../../../../vendor/autoload.php");

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use AttendanceApp\Src\Context\stamp\Domain\Service\DailyStampsService;
use AttendanceApp\Src\Context\stamp\Domain\UseCase\DailyStampsDto;
use AttendanceApp\Src\Context\stamp\Domain\UseCase\StampUseCase;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\FreeeApiGateway;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\SlackAPIGateway;
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
        $this->slackApiMock = $this->createMock(SlackAPIGateway::class);
        $this->freeeApiMock = $this->createMock(FreeeApiGateway::class);
        $this->service = new DailyStampsService();
        $this->useCase = new StampUseCase($this->gatewayMock, $this->service, $this->slackApiMock, $this->freeeApiMock);
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
        $datetime_start = '2023-04-01 8:00:00';
        $datetime_leave = '2023-04-01 12:00:00';
        $datetime_back = '2023-04-01 13:00:00';
        $datetime_end = '2023-04-01 18:00:00';

        $stamp1 = Stamp::create($companyId, $employeeId, 1, '2023-04-01', $datetime_start);
        $stamp2 = Stamp::create($companyId, $employeeId, 2, '2023-04-01', $datetime_leave);
        $stamp3 = Stamp::create($companyId, $employeeId, 3, '2023-04-01', $datetime_back);
        $stamp4 = Stamp::create($companyId, $employeeId, 4, '2023-04-01', $datetime_end);

        $stamps = new Stamps([$stamp1, $stamp2, $stamp3]);
        $stamps2 = new Stamps([$stamp1, $stamp2, $stamp3, $stamp4]);
        //then
        $this->gatewayMock->expects($this->exactly(2))
            ->method('findBy')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($stamps, $stamps2);

        $this->gatewayMock->expects($this->once())
            ->method('save')
            ->with($stamp4);

        $status = 4;
        $this->slackApiMock->expects($this->once())
            ->method('send')
            ->with($status);

        $dto = new DailyStampsDto($employeeId, $companyId, $date, $datetime_start, $datetime_leave, $datetime_back, $datetime_end);
        $this->freeeApiMock->expects($this->once())
            ->method('registerAttendance')
            ->with($dto);

        //when
        $this->useCase->record($companyId, $employeeId, $date, $datetime_end);
    }
}