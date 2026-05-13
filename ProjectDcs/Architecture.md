# Calendar UI Module - Architecture

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Technical Architecture Specification |
| Module | ksf_Calendar_UI |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Architecture Overview

### 1.1 Design Philosophy

The `ksf_Calendar_UI` module provides a UI adapter for the `ksfraser/ksf-calendar` business logic. It follows the widget pattern with FullCalendar.js as the rendering engine, supporting multiple calendar sources and interactive editing.

### 1.2 Module Structure

```
ksf_Calendar_UI/
├── src/
│   └── Ksfraser/
│       ├── API/
│       │   └── CalendarApiController.php
│       ├── UI/
│       │   └── CalendarPageController.php
│       └── Widget/
│           └── CalendarWidget.php
├── templates/
│   ├── calendar.php
│   ├── calendar-list.php
│   ├── entry-form.php
│   ├── filters.php
│   └── calendar-widget.php
├── assets/
│   └── js/
│       └── calendar.js
├── tests/
│   └── Widget/
│       └── CalendarWidgetTest.php
├── composer.json
└── ProjectDcs/
    └── ProjectDcs/
```

### 1.3 Component Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                      Calendar UI Architecture                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                  FullCalendar.js (Browser)                   │ │
│  │  - dayGridMonth, timeGridWeek, timeGridDay, listMonth         │ │
│  │  - Event rendering, drag-drop, resize                         │ │
│  │  - AJAX calls to API                                          │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                              ▲                                    │
│                              │ AJAX                               │
│                              │                                    │
│  ┌───────────────────────────┴─────────────────────────────────┐ │
│  │              CalendarApiController                           │ │
│  │  - /api/calendar/entries                                     │ │
│  │  - /api/calendar/sources                                     │ │
│  └───────────────────────────┬─────────────────────────────────┘ │
│                              │                                    │
│  ┌───────────────────────────┴─────────────────────────────────┐ │
│  │                 CalendarWidget                               │ │
│  │  - renderCalendar()                                          │ │
│  │  - getEventsForRange()                                       │ │
│  │  - createEventFromAjax()                                     │ │
│  │  - updateEventFromAjax()                                     │ │
│  │  - deleteEventFromAjax()                                     │ │
│  └───────────────────────────┬─────────────────────────────────┘ │
│                              │                                    │
│  ┌───────────────────────────┴─────────────────────────────────┐ │
│  │                 CalendarService (ksf-calendar)               │ │
│  │  - Business logic for entries and sources                    │ │
│  │  - Repository pattern implementation                         │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. Core Components

### 2.1 CalendarWidget

The main widget class that integrates FullCalendar.js with the business logic.

```php
namespace Ksfraser\Widget;

use Ksfraser\Calendar\Service\CalendarService;
use Ksfraser\Calendar\DTO\CalendarEntryDTO;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class CalendarWidget
{
    private CalendarService $calendarService;
    private LoggerInterface $logger;
    
    // View constants
    public const VIEW_MONTH = 'month';
    public const VIEW_WEEK = 'week';
    public const VIEW_DAY = 'day';
    public const VIEW_LIST = 'listMonth';
    
    public function renderCalendar(string $userId, array $options = []): string;
    public function getEventsForRange(string $userId, string $start, string $end, array $filters = []): array;
    public function getEventSources(string $userId): array;
    public function createEventFromAjax(array $data): array;
    public function updateEventFromAjax(int $id, array $data): array;
    public function deleteEventFromAjax(int $id): array;
}
```

### 2.2 CalendarApiController

REST API endpoints for calendar operations.

```php
// Endpoints
GET    /api/calendar/entries      // List entries
GET    /api/calendar/entries/:id   // Get entry
POST   /api/calendar/entries      // Create entry
PUT    /api/calendar/entries/:id  // Update entry
DELETE /api/calendar/entries/:id  // Delete entry
GET    /api/calendar/sources      // List sources
GET    /api/calendar/ical/:id    // iCal export
POST   /api/calendar/subscribe   // iCal subscribe
```

### 2.3 CalendarPageController

Server-rendered page controller for non-JS environments.

```php
class CalendarPageController
{
    public function renderPage(string $userId): string;
    public function renderListView(string $userId): string;
    public function renderEntryForm(int $entryId = null): string;
}
```

---

## 3. Data Flow

### 3.1 Event Fetching Flow

```
Page Load
    │
    ▼
CalendarWidget::renderCalendar()
    │
    ▼
Initialize FullCalendar with event sources
    │
    ▼
FullCalendar requests events via AJAX
    │
    ▼
CalendarApiController::handle()
    │
    ▼
CalendarService::getEntriesForDateRange()
    │
    ▼
Transform to FullCalendar format via CalendarEntryDTO
    │
    ▼
Return JSON to FullCalendar
    │
    ▼
Render events on calendar
```

### 3.2 Event Creation Flow

```
User clicks "Add Event" or date slot
    │
    ▼
FullCalendar select callback triggered
    │
    ▼
Prompt for event title
    │
    ▼
AJAX POST to /api/calendar/entries
    │
    ▼
CalendarApiController::create()
    │
    ▼
CalendarService::createEntry()
    │
    ▼
Return CalendarEntry to controller
    │
    ▼
Transform via CalendarEntryDTO
    │
    ▼
FullCalendar renders new event
```

### 3.3 Drag-and-Drop Update Flow

```
User drags event to new date/time
    │
    ▼
FullCalendar eventDrop callback triggered
    │
    ▼
AJAX PUT to /api/calendar/entries/:id
    │
    ▼
CalendarApiController::update()
    │
    ▼
CalendarService::updateEntry()
    │
    ▼
On success: event stays in new position
    │
    ▼
On failure: event.revert() to original
```

---

## 4. FullCalendar Integration

### 4.1 Initialization

```javascript
var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: '100%',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    editable: true,
    selectable: true,
    selectMirror: true,
    nowIndicator: true,
    eventSources: sources,
    eventDrop: function(info) { /* handle move */ },
    eventResize: function(info) { /* handle resize */ },
    eventClick: function(info) { /* handle click */ },
    select: function(info) { /* handle selection */ }
});
```

### 4.2 Event Source Format

```javascript
eventSources: [
    {
        id: 'pm_tasks',
        url: '/api/calendar/entries?source=projects&user_id=123',
        color: '#4CAF50',
        textColor: '#ffffff'
    },
    {
        id: 'crm_calls',
        url: '/api/calendar/entries?source=crm&user_id=123',
        color: '#2196F3',
        textColor: '#ffffff'
    }
]
```

### 4.3 CalendarEntryDTO to FullCalendar

```php
public function toFullCalendarArray(): array
{
    return [
        'id' => $this->getId(),
        'title' => $this->getTitle(),
        'start' => $this->getStartDate()->format('Y-m-d\TH:i:s'),
        'end' => $this->getEndDate()?->format('Y-m-d\TH:i:s'),
        'allDay' => $this->isAllDay(),
        'color' => $this->getSourceColor(),
        'extendedProps' => [
            'source' => $this->getSource(),
            'description' => $this->getDescription(),
        ]
    ];
}
```

---

## 5. Multi-Source Filtering

### 5.1 Source Configuration

```php
$config = [
    'sources' => [
        'projects' => [
            'name' => 'Project Management',
            'color' => '#4CAF50',
            'showTasks' => true,
            'showMilestones' => true,
        ],
        'crm' => [
            'name' => 'CRM Activities',
            'color' => '#2196F3',
            'showCalls' => true,
            'showMeetings' => true,
            'showTasks' => true,
        ],
        'hrm' => [
            'name' => 'HRM Time Tracking',
            'color' => '#FF9800',
            'showTimeTracking' => true,
        ],
        'client' => [
            'name' => 'Client Dates',
            'color' => '#9C27B0',
            'showBirthdays' => true,
            'showAnniversaries' => true,
            'showRenewals' => true,
        ],
    ]
];
```

### 5.2 Filter Toggle UI

```html
<div class="ksf-calendar-filters">
    <div class="ksf-calendar-legend">
        <label class="ksf-cal-source" data-source-id="projects">
            <input type="checkbox" class="ksf-cal-toggle" checked>
            <span class="ksf-cal-color" style="background:#4CAF50"></span>
            <span class="ksf-cal-name">Project Management</span>
        </label>
        <!-- More sources -->
    </div>
</div>
```

---

## 6. API Specification

### 6.1 List Entries

**Request:**
```
GET /api/calendar/entries?start=2026-05-01&end=2026-05-31&source=projects
```

**Response:**
```json
[
    {
        "id": 1,
        "title": "Team Meeting",
        "start": "2026-05-13T10:00:00",
        "end": "2026-05-13T11:00:00",
        "allDay": false,
        "color": "#4CAF50",
        "source": "projects"
    }
]
```

### 6.2 Create Entry

**Request:**
```
POST /api/calendar/entries
Content-Type: application/json

{
    "title": "New Event",
    "start_date": "2026-05-15T14:00:00",
    "end_date": "2026-05-15T15:00:00",
    "all_day": false,
    "source": "crm",
    "assigned_to": "user123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 42,
        "title": "New Event",
        "start": "2026-05-15T14:00:00",
        "end": "2026-05-15T15:00:00"
    }
}
```

### 6.3 Update Entry

**Request:**
```
PUT /api/calendar/entries/42
Content-Type: application/json

{
    "start_date": "2026-05-16T14:00:00",
    "end_date": "2026-05-16T15:00:00"
}
```

---

## 7. Dependencies

### 7.1 PHP Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `ksfraser/ksf-calendar` | Latest | Business logic |
| `league/csv` | Latest | CSV/iCal parsing |
| `psr/container` | ^2.0 | Dependency injection |
| `psr/log` | ^2.0 | Logging |

### 7.2 JavaScript Dependencies

| Library | Version | Purpose |
|---------|---------|---------|
| FullCalendar | 6.1.11+ | Calendar UI |
| jQuery | 3.x | DOM manipulation (optional) |

---

## 8. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |