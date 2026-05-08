<?php
/**
 * Calendar Widget
 *
 * Integrates FullCalendar.js with ksf_Calendar
 *
 * @package Ksfraser\CalendarUI\Widget
 */

declare(strict_types=1);

namespace Ksfraser\Widget;

use Ksfraser\Calendar\Service\CalendarService;
use Ksfraser\Calendar\DTO\CalendarEntryDTO;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class CalendarWidget
{
    private CalendarService $calendarService;
    private LoggerInterface $logger;

    public const VIEW_MONTH = 'month';
    public const VIEW_WEEK = 'week';
    public const VIEW_DAY = 'day';
    public const VIEW_LIST = 'listMonth';

    public function __construct(ContainerInterface $container)
    {
        $this->calendarService = $container->get(CalendarService::class);
        $this->logger = $container->get(LoggerInterface::class);
    }

    public function renderCalendar(string $userId, array $options = []): string
    {
        $view = $options['defaultView'] ?? self::VIEW_MONTH;
        $height = $options['height'] ?? 600;
        $sources = $options['sources'] ?? [];
        $showFilters = $options['showFilters'] ?? true;
        $editable = $options['editable'] ?? true;

        $initialSources = $this->getEnabledSources($userId, $sources);

        return $this->buildCalendarHtml($userId, $view, $height, $initialSources, $showFilters, $editable);
    }

    public function getEventsForRange(string $userId, string $start, string $end, array $filters = []): array
    {
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $filters['assigned_to'] = $userId;

        $entries = $this->calendarService->getEntriesForDateRange($startDate, $endDate, $filters);

        return array_map(
            fn($entry) => CalendarEntryDTO::fromEntity($entry)->toFullCalendarArray(),
            $entries
        );
    }

    public function getEventSources(string $userId): array
    {
        $sources = $this->calendarService->getSourcesForUser($userId);

        return array_map(function ($source) {
            return [
                'id' => $source->getId(),
                'name' => $source->getName(),
                'color' => $source->getColor(),
                'enabled' => $source->isEnabled(),
                'source' => $source->getSource(),
                'filters' => [
                    'events' => $source->shouldShowEvents(),
                    'tasks' => $source->shouldShowTasks(),
                    'calls' => $source->shouldShowCalls(),
                    'meetings' => $source->shouldShowMeetings(),
                    'client_dates' => $source->shouldShowClientDates(),
                    'birthdays' => $source->shouldShowBirthdays(),
                    'anniversaries' => $source->shouldShowAnniversaries(),
                    'renewals' => $source->shouldShowRenewals(),
                    'time_tracking' => $source->shouldShowTimeTracking(),
                ],
            ];
        }, $sources);
    }

    public function createEventFromAjax(array $data): array
    {
        try {
            $entry = $this->calendarService->createEntry($data);
            return [
                'success' => true,
                'data' => CalendarEntryDTO::fromEntity($entry)->toFullCalendarArray(),
            ];
        } catch (\Exception $e) {
            $this->logger->error('Failed to create event', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function updateEventFromAjax(int $id, array $data): array
    {
        try {
            $entry = $this->calendarService->updateEntry($id, $data);
            return [
                'success' => true,
                'data' => CalendarEntryDTO::fromEntity($entry)->toFullCalendarArray(),
            ];
        } catch (\Exception $e) {
            $this->logger->error('Failed to update event', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function deleteEventFromAjax(int $id): array
    {
        try {
            $this->calendarService->deleteEntry($id);
            return ['success' => true];
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete event', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getEnabledSources(string $userId, array $requestedSources): array
    {
        $allSources = $this->calendarService->getSourcesForUser($userId);

        if (empty($requestedSources)) {
            return $allSources;
        }

        return array_filter($allSources, fn($source) => in_array($source->getId(), $requestedSources));
    }

    private function buildCalendarHtml(
        string $userId,
        string $view,
        int $height,
        array $sources,
        bool $showFilters,
        bool $editable
    ): string {
        $sourcesJson = json_encode(array_map(fn($s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'color' => $s->getColor(),
            'source' => $s->getSource(),
        ], $sources));

        ob_start();
        include __DIR__ . '/../../templates/calendar-widget.php';
        return ob_get_clean();
    }
}