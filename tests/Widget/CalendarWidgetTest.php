<?php
/**
 * CalendarWidget Test
 *
 * @package Ksfraser\CalendarUI\Tests\Widget
 */

declare(strict_types=1);

namespace Ksfraser\CalendarUI\Tests\Widget;

use DateTime;
use Ksfraser\Calendar\Entity\CalendarEntry;
use Ksfraser\Calendar\Entity\CalendarSource;
use Ksfraser\Calendar\Service\CalendarService;
use Ksfraser\CalendarUI\Widget\CalendarWidget;
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
            ->willReturnMap([
                [CalendarService::class, $this->mockCalendarService],
                [LoggerInterface::class, $this->mockLogger],
            ]);

        $this->widget = new CalendarWidget($this->mockContainer);
    }

    public function testViewConstants(): void
    {
        $this->assertSame('month', CalendarWidget::VIEW_MONTH);
        $this->assertSame('week', CalendarWidget::VIEW_WEEK);
        $this->assertSame('day', CalendarWidget::VIEW_DAY);
        $this->assertSame('listMonth', CalendarWidget::VIEW_LIST);
    }

    public function testGetEventsForRange(): void
    {
        $entry = new CalendarEntry(
            source: 'pm',
            sourceId: 'task-1',
            sourceType: 'task',
            title: 'Test Task',
            startDate: new DateTime('2024-01-15T09:00:00')
        );

        $this->mockCalendarService->expects($this->once())
            ->method('getEntriesForDateRange')
            ->willReturn([$entry]);

        $events = $this->widget->getEventsForRange('user1', '2024-01-01', '2024-01-31');

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertSame('Test Task', $events[0]['title']);
    }

    public function testGetEventsForRangeWithFilters(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('getEntriesForDateRange')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function ($filters) {
                    return $filters['assigned_to'] === 'user1' && $filters['source'] === 'pm';
                })
            )
            ->willReturn([]);

        $events = $this->widget->getEventsForRange('user1', '2024-01-01', '2024-01-31', ['source' => 'pm']);

        $this->assertIsArray($events);
    }

    public function testGetEventSources(): void
    {
        $source = new CalendarSource(
            name: 'PM Tasks',
            type: CalendarSource::TYPE_INTERNAL,
            source: CalendarEntry::SOURCE_PM,
            id: 1
        );

        $this->mockCalendarService->expects($this->once())
            ->method('getSourcesForUser')
            ->with('user1')
            ->willReturn([$source]);

        $sources = $this->widget->getEventSources('user1');

        $this->assertIsArray($sources);
        $this->assertCount(1, $sources);
        $this->assertSame(1, $sources[0]['id']);
        $this->assertSame('PM Tasks', $sources[0]['name']);
    }

    public function testCreateEventFromAjaxSuccess(): void
    {
        $entry = new CalendarEntry(
            source: 'user',
            sourceId: 'event-1',
            sourceType: 'event',
            title: 'New Event',
            startDate: new DateTime('2024-01-15T10:00:00')
        );

        $this->mockCalendarService->expects($this->once())
            ->method('createEntry')
            ->willReturn($entry);

        $result = $this->widget->createEventFromAjax([
            'title' => 'New Event',
            'start_date' => '2024-01-15T10:00:00',
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }

    public function testCreateEventFromAjaxFailure(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('createEntry')
            ->willThrowException(new \Ksfraser\Calendar\Exception\CalendarException('Title required'));

        $result = $this->widget->createEventFromAjax([
            'title' => '',
            'start_date' => '2024-01-15T10:00:00',
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame('Title required', $result['error']);
    }

    public function testUpdateEventFromAjaxSuccess(): void
    {
        $entry = new CalendarEntry(
            source: 'user',
            sourceId: 'event-1',
            sourceType: 'event',
            title: 'Updated Event',
            startDate: new DateTime('2024-01-15T10:00:00')
        );

        $this->mockCalendarService->expects($this->once())
            ->method('updateEntry')
            ->willReturn($entry);

        $result = $this->widget->updateEventFromAjax(1, ['title' => 'Updated Event']);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }

    public function testUpdateEventFromAjaxFailure(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('updateEntry')
            ->willThrowException(new \Ksfraser\Calendar\Exception\CalendarException('Not found'));

        $result = $this->widget->updateEventFromAjax(999, ['title' => 'Updated']);

        $this->assertFalse($result['success']);
        $this->assertSame('Not found', $result['error']);
    }

    public function testDeleteEventFromAjaxSuccess(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('deleteEntry')
            ->with(1);

        $result = $this->widget->deleteEventFromAjax(1);

        $this->assertTrue($result['success']);
    }

    public function testDeleteEventFromAjaxFailure(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('deleteEntry')
            ->willThrowException(new \Ksfraser\Calendar\Exception\CalendarException('Not found'));

        $result = $this->widget->deleteEventFromAjax(999);

        $this->assertFalse($result['success']);
        $this->assertSame('Not found', $result['error']);
    }

    public function testRenderCalendarDefaultOptions(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('getSourcesForUser')
            ->with('user1')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1');

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithCustomView(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('getSourcesForUser')
            ->with('user1')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'defaultView' => CalendarWidget::VIEW_WEEK,
        ]);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithCustomHeight(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('getSourcesForUser')
            ->with('user1')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'height' => 800,
        ]);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithSpecificSources(): void
    {
        $source = new CalendarSource(
            name: 'PM Tasks',
            type: CalendarSource::TYPE_INTERNAL,
            source: CalendarEntry::SOURCE_PM,
            id: 1
        );

        $this->mockCalendarService->expects($this->once())
            ->method('getSourcesForUser')
            ->with('user1')
            ->willReturn([$source]);

        $html = $this->widget->renderCalendar('user1', [
            'sources' => [1],
        ]);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithFiltersDisabled(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('getSourcesForUser')
            ->with('user1')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'showFilters' => false,
        ]);

        $this->assertIsString($html);
    }

    public function testRenderCalendarWithNonEditable(): void
    {
        $this->mockCalendarService->expects($this->once())
            ->method('getSourcesForUser')
            ->with('user1')
            ->willReturn([]);

        $html = $this->widget->renderCalendar('user1', [
            'editable' => false,
        ]);

        $this->assertIsString($html);
    }
}