# Calendar UI Module - Business Requirements

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Business Requirements Specification |
| Module | ksf_Calendar_UI |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Project Overview

### 1.1 Purpose Statement

The `ksf_Calendar_UI` module provides a standalone calendar interface built on FullCalendar.js for the `ksfraser/ksf-calendar` business logic module. It delivers a Google Calendar-like experience with multi-calendar support, drag-and-drop editing, and iCal synchronization.

### 1.2 Problem Statement

Organizations need unified calendar views across multiple business functions:

- **Fragmentation**: Activities scattered across CRM, PM, HRM, and other systems
- **Limited Visibility**: No single view of all scheduled activities
- **Manual Entry**: Duplicated effort entering data in multiple places
- **Sync Issues**: No integration with external calendar systems

The Calendar UI addresses these by providing:
- Unified calendar interface aggregating multiple sources
- Interactive drag-and-drop scheduling
- Filter toggles for different activity types
- iCal import/export for external sync

### 1.3 Module Positioning

```
ksfraser/ksf-calendar (Business Logic)
    └── src/Ksfraser/Calendar/
            ├── Service/CalendarService.php
            ├── Entity/CalendarEntry.php
            └── DTO/CalendarEntryDTO.php

ksf_Calendar_UI (Platform Adapter)
    └── src/Ksfraser/Widget/
            └── CalendarWidget.php
    └── templates/
            └── calendar-widget.php
```

---

## 2. Scope Definition

### 2.1 In-Scope Features

#### Calendar Views
- **Month View**: Traditional monthly calendar grid
- **Week View**: Weekly agenda with time slots
- **Day View**: Single day with hourly breakdown
- **List View**: Text-based list of upcoming events

#### Calendar Sources
- **Project Management**: Tasks and milestones
- **CRM Activities**: Calls, meetings, follow-ups, anniversaries
- **HRM Time Tracking**: Time entries, shift schedules
- **Client Dates**: Birthdays, anniversaries, renewals
- **Personal Events**: User-created events
- **External iCal**: Subscribed calendars

#### Interactive Features
- **Drag-and-Drop**: Move events by dragging
- **Event Resize**: Adjust event duration by dragging edges
- **Event Click**: View/edit event details
- **Event Creation**: Click date to create new event
- **Filter Toggles**: Show/hide calendar sources
- **Task Sidebar**: Display unscheduled tasks ordered by priority/due date
- **Color Coding**: Visual indicators for event status (planned vs completed)

#### iCal Support
- **Import**: Load events from .ics files
- **Export**: Generate .ics feed URL
- **Subscribe**: Subscribe to external calendars

#### Advanced Features
- **Meeting Status Tracking**: Planned/Held/Not Held/Rescheduled
- **Call Outcome Tracking**: Planned/Held/RNA/VMail/RNA-followup/VMail-followup
- **Shift Schedule View**: Special view for employee shift patterns
- **Project Task Gantt**: Optional Gantt chart view for project tasks
- **Source Grouping**: Group sources by customer, project, or type

### 2.2 Out-of-Scope Features

- Direct email/notification sending
- Resource scheduling (rooms, equipment)
- Availability checking
- Mobile-specific UI
- Calendar sharing permissions

### 2.3 Dependencies

| Dependency | Purpose |
|------------|---------|
| `ksfraser/ksf-calendar` | Business logic (CalendarEntry entities) |
| `FullCalendar.js` | UI rendering (v6.1.11+) |
| `league/csv` | iCal parsing |

---

## 3. Feature Specifications

### 3.1 Calendar Views

#### 3.1.1 View Types

| View | FullCalendar ID | Description |
|------|-----------------|-------------|
| Month | dayGridMonth | Monthly grid with day cells |
| Week | timeGridWeek | Weekly agenda with time slots |
| Day | timeGridDay | Single day hourly view |
| List | listMonth | Chronological list of events |

#### 3.1.2 Navigation

- **Header Toolbar**: prev/next/today buttons
- **View Selector**: Dropdown to change view type
- **Date Picker**: Jump to specific date
- **Today Indicator**: Visual highlight for current date

### 3.2 Calendar Sources

Each source has:
- Unique identifier
- Display name
- Color coding
- Toggle visibility

**Source Configuration:**
```php
[
    'id' => 'pm_tasks',
    'name' => 'Project Management',
    'color' => '#4CAF50',
    'source' => 'projects',
    'filters' => [
        'events' => true,
        'tasks' => true,
        'milestones' => true,
    ]
]
```

### 3.3 Event Operations

#### 3.3.1 Create Event

**Trigger:** Click on date/time slot or "Add Event" button

**Fields:**
- Title (required)
- Start date/time
- End date/time
- All-day flag
- Calendar source
- Description
- Assigned user

#### 3.3.2 Edit Event

**Trigger:** Click on event or drag-and-drop

**Operations:**
- Move: Drag event to new date/time
- Resize: Drag edges to change duration
- Update: Modal form for full editing

#### 3.3.3 Delete Event

**Trigger:** Click delete button in event modal

**Confirmation:** Yes/No dialog before deletion

---

## 4. API Endpoints

### 4.1 Event Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/calendar/entries | List entries for date range |
| GET | /api/calendar/entries/:id | Get single entry |
| POST | /api/calendar/entries | Create entry |
| PUT | /api/calendar/entries/:id | Update entry |
| DELETE | /api/calendar/entries/:id | Delete entry |

### 4.2 Calendar Source Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/calendar/sources | List available sources |
| GET | /api/calendar/ical/:id | Export iCal feed |
| POST | /api/calendar/subscribe | Subscribe to external iCal |

---

## 5. Integration Dependencies

### 5.1 Internal Dependencies

| Module | Integration Point |
|--------|-------------------|
| `ksfraser/ksf-calendar` | CalendarService, CalendarEntry |
| `ksf_PM` | Project tasks and milestones |
| `ksf_CRM` | CRM activities and client dates |
| `ksf_HRM` | Time tracking entries |

### 5.2 External Dependencies

| Library | Version | Purpose |
|---------|---------|---------|
| FullCalendar.js | 6.1.11+ | Calendar UI |
| league/csv | Latest | iCal parsing |

---

## 6. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |
| 1.1.0 | May 2026 | KSF Development Team | Added: Meeting/Call status tracking, Shift schedule view, Unscheduled task sidebar, Source grouping by customer/project |