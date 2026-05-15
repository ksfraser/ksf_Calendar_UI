# Calendar UI Module - Functional Requirements

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Functional Requirements Specification |
| Module | ksf_Calendar_UI |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Functional Requirements

### 1.1 Calendar Rendering

#### FR-CAL-001: Initialize FullCalendar

**Description:** Initialize FullCalendar.js with configured options.

**Pre-conditions:**
- CalendarWidget instantiated with CalendarService
- User ID provided
- Options configured

**Post-conditions:**
- Calendar HTML rendered
- FullCalendar.js initialized

**Options:**
| Option | Type | Default | Description |
|--------|------|---------|-------------|
| defaultView | string | 'month' | Initial view type |
| height | int | 600 | Calendar height in pixels |
| sources | array | [] | Specific sources to load |
| showFilters | bool | true | Show filter toggles |
| editable | bool | true | Enable event editing |

---

#### FR-CAL-002: Render Multiple Views

**Description:** Support all standard calendar views.

**Pre-conditions:**
- FullCalendar initialized

**Post-conditions:**
- User can switch between views

**Views:**
| View | FullCalendar ID | Description |
|------|-----------------|-------------|
| Month | dayGridMonth | Monthly grid |
| Week | timeGridWeek | Weekly agenda |
| Day | timeGridDay | Daily hourly view |
| List | listMonth | Chronological list |

---

### 1.2 Event Display

#### FR-EVT-001: Fetch Events for Date Range

**Description:** Retrieve calendar entries for a specified date range.

**Pre-conditions:**
- User authenticated
- Valid date range provided

**Post-conditions:**
- Events returned in FullCalendar format

**Input:**
| Parameter | Type | Description |
|-----------|------|-------------|
| userId | string | User identifier |
| start | string | Range start date (ISO 8601) |
| end | string | Range end date (ISO 8601) |
| filters | array | Source/type filters |

**Output:**
```php
[
    [
        'id' => 1,
        'title' => 'Team Meeting',
        'start' => '2026-05-13T10:00:00',
        'end' => '2026-05-13T11:00:00',
        'allDay' => false,
        'color' => '#4CAF50',
        'source' => 'projects',
    ]
]
```

---

#### FR-EVT-002: Filter by Calendar Source

**Description:** Filter displayed events by calendar source.

**Pre-conditions:**
- Multiple sources configured

**Post-conditions:**
- Only selected source events displayed

**Filter Types:**
- Toggle visibility by source
- Filter by activity type within source
- Date range filtering

---

### 1.3 Event Operations

#### FR-OP-001: Create Event via AJAX

**Description:** Create new calendar entry via AJAX request.

**Pre-conditions:**
- Valid event data provided
- User authenticated

**Post-conditions:**
- Entry created in database
- Calendar updated with new event

**Input:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| title | string | Yes | Event title |
| start_date | datetime | Yes | Start time |
| end_date | datetime | No | End time |
| all_day | bool | No | Full-day event |
| source | string | Yes | Calendar source |
| assigned_to | string | No | User assignment |
| description | string | No | Event details |

**Output:**
```json
{
    "success": true,
    "data": {
        "id": 123,
        "title": "New Event",
        "start": "2026-05-15T10:00:00",
        "end": "2026-05-15T11:00:00"
    }
}
```

---

#### FR-OP-002: Update Event via AJAX

**Description:** Update existing event via AJAX request.

**Pre-conditions:**
- Valid event ID and update data
- User authorized

**Post-conditions:**
- Entry updated in database
- Calendar reflects changes

**Input:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | int | Yes | Event ID |
| title | string | No | New title |
| start_date | datetime | No | New start |
| end_date | datetime | No | New end |
| all_day | bool | No | All-day flag |

**Output:**
```json
{
    "success": true,
    "data": { /* updated event */ }
}
```

---

#### FR-OP-003: Delete Event via AJAX

**Description:** Delete calendar entry via AJAX request.

**Pre-conditions:**
- Valid event ID
- User authorized

**Post-conditions:**
- Entry removed from database
- Event removed from calendar

**Output:**
```json
{
    "success": true
}
```

---

### 1.4 Interactive Features

#### FR-INT-001: Drag-and-Drop Move

**Description:** Move events by dragging to new position.

**Pre-conditions:**
- Calendar editable = true
- Event exists

**Post-conditions:**
- Event dates updated
- Database updated

**Implementation:**
- FullCalendar eventDrop callback
- AJAX PUT with new dates
- Revert on failure

---

#### FR-INT-002: Event Resize

**Description:** Change event duration by dragging edges.

**Pre-conditions:**
- Calendar editable = true
- Non-all-day event

**Post-conditions:**
- Event duration updated
- Database updated

**Implementation:**
- FullCalendar eventResize callback
- AJAX PUT with new end date

---

#### FR-INT-003: Event Click

**Description:** Handle click on event to view details.

**Pre-conditions:**
- Event rendered on calendar

**Post-conditions:**
- Event details displayed or navigated

**Implementation:**
- Navigate to entry detail page
- URL: /calendar/entry/{event_id}

---

#### FR-INT-004: Date Selection

**Description:** Create event by selecting date/time range.

**Pre-conditions:**
- Calendar selectable = true

**Post-conditions:**
- Event creation form opened
- Pre-filled with selected dates

**Implementation:**
- FullCalendar select callback
- Prompt for event title
- AJAX POST with dates

---

### 1.5 Calendar Sources

#### FR-SRC-001: Get Available Sources

**Description:** Retrieve list of configured calendar sources for user.

**Pre-conditions:**
- User authenticated

**Post-conditions:**
- Source configuration returned

**Output:**
```json
[
    {
        "id": "projects",
        "name": "Project Management",
        "color": "#4CAF50",
        "enabled": true,
        "source": "projects",
        "filters": {
            "events": true,
            "tasks": true,
            "milestones": true
        }
    }
]
```

---

#### FR-SRC-002: Toggle Source Visibility

**Description:** Show/hide events from specific calendar source.

**Pre-conditions:**
- Source checkbox toggled

**Post-conditions:**
- Source events added/removed from view

**Implementation:**
- Toggle event source visibility in FullCalendar
- Store preference in user settings

---

### 1.6 iCal Integration

#### FR-ICAL-001: Export iCal Feed

**Description:** Generate iCal export URL for calendar source.

**Pre-conditions:**
- Valid source ID
- User authenticated

**Post-conditions:**
- iCal URL generated

**Output:**
```
GET /api/calendar/ical/{source_id}

Response: text/calendar (RFC 5545)
```

---

#### FR-ICAL-002: Subscribe to External Calendar

**Description:** Import events from external iCal URL.

**Pre-conditions:**
- Valid iCal URL provided

**Post-conditions:**
- External events imported as new source

**Input:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| url | string | Yes | iCal URL |
| name | string | Yes | Display name |
| color | string | No | Event color |

---

### 1.7 Meeting and Call Status Tracking

#### FR-STS-001: Meeting Status Constants

**Description:** Define meeting status constants for workflow integration.

**Meeting Statuses:**
| Status | Description | Workflow Action |
|--------|-------------|------------------|
| meeting_planned | Scheduled but not held | Reschedule if past |
| meeting_held | Successfully completed | Archive |
| meeting_not_held | Could not hold meeting | Follow up |
| meeting_rescheduled | Moved to new time | Update schedule |

**Call Outcomes:**
| Status | Description | Workflow Action |
|--------|-------------|------------------|
| call_planned | Scheduled call | Reminder |
| call_held | Call completed | Log notes |
| call_rna | Ring No Answer | Auto-retry/followup |
| call_vmail | Voicemail left | Follow up |
| call_rna_followup | RNA with planned followup | Schedule |
| call_vmail_followup | VM with planned followup | Schedule |

---

#### FR-STS-002: Status-Based Event Display

**Description:** Visual differentiation of event status using color intensity.

**Visual Rules:**
| Status Type | Color Style | Opacity |
|-------------|-------------|---------|
| Planned/Future | Bright, solid | 100% |
| In Progress | Normal | 100% |
| Completed/Held | Dimmed, faded | 50% |
| Cancelled/No-Show | Greyed out | 30% |

**CSS Classes:**
```css
.event-status-planned { opacity: 1.0; }
.event-status-held { opacity: 0.5; }
.event-status-rna { opacity: 0.7; background-color: #FF9800; }
.event-status-cancelled { opacity: 0.3; background-color: #9E9E9E; }
```

---

### 1.8 Shift Schedule View

#### FR-SHF-001: Display Shift Calendar

**Description:** Special calendar view for employee shift schedules.

**Pre-conditions:**
- HRM Roster module installed
- User has shift assignments

**Post-conditions:**
- Shifts displayed in calendar with shift-specific colors

**Shift Types:**
| Shift | Color | Time Range |
|-------|-------|------------|
| Morning | Orange (#FF9800) | 06:00-14:00 |
| Afternoon | Blue (#2196F3) | 14:00-22:00 |
| Night | Purple (#9C27B0) | 22:00-06:00 |
| Swing | Red (#F44336) | Variable |

---

#### FR-SHF-002: Shift Entry Conversion

**Description:** Convert Roster shifts to CalendarEntry for display.

**Implementation:**
```php
CalendarEntry::fromRosterShift(Roster $roster): CalendarEntry
```

**Fields Mapping:**
| Roster Field | Calendar Entry |
|--------------|----------------|
| id | sourceId |
| shift | title (e.g., "Morning Shift") |
| date + start_time | startDate |
| date + end_time | endDate |
| employee_id | assignedTo |
| status | status |
| notes | description |

---

### 1.9 Unscheduled Task Sidebar

#### FR-SDB-001: Task Sidebar Display

**Description:** Sidebar panel showing tasks without scheduled times.

**Pre-conditions:**
- ProjectManagement module installed
- User has assigned tasks

**Post-conditions:**
- Sidebar shows unscheduled tasks sorted by priority/due date

**Output Format:**
```json
[
    {
        "id": "task_1",
        "title": "Review proposal",
        "priority": "high",
        "due_date": "2026-05-20",
        "project": "Project Alpha"
    },
    {
        "id": "task_2",
        "title": "Send invoice",
        "priority": "medium",
        "due_date": "2026-05-25",
        "project": "Project Beta"
    }
]
```

**Sorting:**
1. Priority (high → medium → low)
2. Due date (sooner → later)

---

#### FR-SDB-002: Drag Task to Calendar

**Description:** Allow dragging unscheduled tasks to calendar to schedule.

**Pre-conditions:**
- Task visible in sidebar
- Calendar has available time slot

**Post-conditions:**
- Task start_date updated to dropped time
- Task appears in calendar

**Implementation:**
- FullCalendar eventDrop callback
- AJAX PUT to update task
- Remove from sidebar if now scheduled

---

### 1.10 Source Grouping by Customer/Project

#### FR-GRP-001: Group Sources

**Description:** Organize calendar sources by customer, project, or type.

**Group Types:**
| Group | Description | Example |
|-------|-------------|---------|
| customer | Group by customer | Acme Corp, Beta Inc |
| project | Group by project | Project Alpha, Project Beta |
| type | Group by event type | Meetings, Calls, Tasks |

**Configuration:**
```php
$options['sourceGrouping'] = 'customer'; // or 'project', 'type'
```

---

#### FR-GRP-002: Color-Coded Sources

**Description:** User-controllable colors for each source/group.

**Features:**
- Color picker in source settings
- Color saved per user preference
- Default colors per source type

---

## 2. Non-Functional Requirements

### 2.1 Performance

| Requirement | Target | Description |
|-------------|--------|-------------|
| Initial load | < 1s | Calendar renders |
| Event fetch | < 200ms | Single range query |
| Event create | < 300ms | Database write + response |
| Drag-drop | < 100ms | Perceived responsiveness |
| Task list fetch | < 150ms | Fetch unscheduled tasks |

### 2.2 Browser Support

| Browser | Version |
|---------|---------|
| Chrome | 90+ |
| Firefox | 88+ |
| Safari | 14+ |
| Edge | 90+ |

---

## 3. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |
| 1.1.0 | May 2026 | KSF Development Team | Added: Meeting/Call status tracking (FR-STS-*), Shift schedule view (FR-SHF-*), Unscheduled task sidebar (FR-SDB-*), Source grouping (FR-GRP-*), Status-based color coding (FR-CLR-*) |