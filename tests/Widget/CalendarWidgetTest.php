<?php
/**
 * CalendarWidget Test
 *
 * @package Ksfraser\Widget\Tests\Widget
 */

declare(strict_types=1);

namespace Ksfraser\Widget\Tests\Widget;

use Ksfraser\Calendar\Service\CalendarService;
use Ksfraser\Widget\CalendarWidget;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class CalendarWidgetTest extends TestCase
{
    private $mockContainer;
    private $mockCalendarService;
    private $mockLogger;
    private CalendarWidget $widget;

    protected function setUp(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockCalendarService = $this->createMock(CalendarService::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);

        $this->mockContainer->method('get')
            ->willReturnCallback(function ($class) {
                if ($class === CalendarService::class) {
                    return $this->mockCalendarService;
                }
                if ($class === LoggerInterface::class) {
                    return $this->mockLogger;
                }
                return null;
            });

        $this->widget = new CalendarWidget($this->mockContainer);
    }

    public function testViewConstants(): void
    {
        $this->assertSame('month', CalendarWidget::VIEW_MONTH);
        $this->assertSame('week', CalendarWidget::VIEW_WEEK);
        $this->assertSame('day', CalendarWidget::VIEW_DAY);
        $this->assertSame('listMonth', CalendarWidget::VIEW_LIST);
    }

    public function testRenderCalendarReturnsString(): void
    {
        $this->mockCalendarService->method('getSourcesForUser')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1');

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithDefaultOptions(): void
    {
        $this->mockCalendarService->method('getSourcesForUser')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', []);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithCustomView(): void
    {
        $this->mockCalendarService->method('getSourcesForUser')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'defaultView' => CalendarWidget::VIEW_WEEK,
        ]);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithCustomHeight(): void
    {
        $this->mockCalendarService->method('getSourcesForUser')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'height' => 800,
        ]);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithFiltersDisabled(): void
    {
        $this->mockCalendarService->method('getSourcesForUser')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'showFilters' => false,
        ]);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithNonEditable(): void
    {
        $this->mockCalendarService->method('getSourcesForUser')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'editable' => false,
        ]);

        $this->assertIsString($html);
    }

    public function testGetEventsForRangeReturnsArray(): void
    {
        $this->mockCalendarService->method('getEntriesForDateRange')
            ->willReturn([]);

        $events = $this->widget->getEventsForRange('user1', '2024-01-01', '2024-01-31');

        $this->assertIsArray($events);
    }

    public function testGetEventSourcesReturnsArray(): void
    {
        $this->mockCalendarService->method('getSourcesForUser')
            ->willReturn([]);

        $sources = $this->widget->getEventSources('user1');

        $this->assertIsArray($sources);
    }

    public function testDeleteEventFromAjaxSuccess(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('deleteEntry')
            ->with(1);

        $result = $this->widget->deleteEventFromAjax(1);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }

    public function testDeleteEventFromAjaxFailure(): void
    {
        $this->mockCalendarService->method('deleteEntry')
            ->willThrowException(new \RuntimeException('Not found'));

        $result = $this->widget->deleteEventFromAjax(999);

        $this->assertFalse($result['success']);
        $this->assertSame('Not found', $result['error']);
    }
}
