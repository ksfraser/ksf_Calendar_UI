# Calendar UI Module - Test Plan

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Test Plan |
| Module | ksf_Calendar_UI |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Test Overview

### 1.1 Scope

**In Scope:**
- CalendarWidget class
- AJAX event operations
- FullCalendar integration
- Multi-source filtering
- iCal operations

**Out of Scope:**
- CalendarService business logic (tested in ksf-calendar)
- Database operations
- Authentication/authorization

---

## 2. Unit Test Cases

### 2.1 CalendarWidget Tests

#### TC-WGT-001: Render Calendar HTML

**Test ID:** TC-WGT-001  
**Priority:** High

**Steps:**
1. Create CalendarWidget with mock container
2. Call renderCalendar() with options
3. Assert HTML string returned
4. Assert contains FullCalendar container div
5. Assert filter toggles rendered

**Expected Result:** Complete HTML for calendar display

---

#### TC-WGT-002: Get Events for Date Range

**Test ID:** TC-WGT-002  
**Priority:** High

**Steps:**
1. Create widget with mock CalendarService
2. Setup mock entries in service
3. Call getEventsForRange()
4. Assert array returned
5. Assert entries transformed to FullCalendar format

**Expected Result:** Events in FullCalendar array format

---

#### TC-WGT-003: Get Event Sources

**Test ID:** TC-WGT-003  
**Priority:** High

**Steps:**
1. Create widget with mock CalendarService
2. Setup mock sources in service
3. Call getEventSources()
4. Assert array of sources returned
5. Assert each source has id, name, color

**Expected Result:** Source configuration array

---

#### TC-WGT-004: Create Event Success

**Test ID:** TC-WGT-004  
**Priority:** Critical

**Steps:**
1. Create widget
2. Mock CalendarService::createEntry()
3. Call createEventFromAjax() with valid data
4. Assert success returned
5. Assert data contains event in FullCalendar format

**Expected Result:**
```json
{
    "success": true,
    "data": { /* event in FC format */ }
}
```

---

#### TC-WGT-005: Create Event Failure

**Test ID:** TC-WGT-005  
**Priority:** High

**Steps:**
1. Create widget
2. Mock CalendarService throws exception
3. Call createEventFromAjax()
4. Assert success = false
5. Assert error message returned

**Expected Result:**
```json
{
    "success": false,
    "error": "Error message"
}
```

---

#### TC-WGT-006: Update Event Success

**Test ID:** TC-WGT-006  
**Priority:** Critical

**Steps:**
1. Create widget
2. Mock CalendarService::updateEntry()
3. Call updateEventFromAjax() with event ID and data
4. Assert success returned
5. Assert updated event in response

**Expected Result:** Success with updated event data

---

#### TC-WGT-007: Update Event Not Found

**Test ID:** TC-WGT-007  
**Priority:** High

**Steps:**
1. Create widget
2. Mock CalendarService returns null
3. Call updateEventFromAjax()
4. Assert failure response

**Expected Result:** Error for not found event

---

#### TC-WGT-008: Delete Event Success

**Test ID:** TC-WGT-008  
**Priority:** Critical

**Steps:**
1. Create widget
2. Mock CalendarService::deleteEntry()
3. Call deleteEventFromAjax() with event ID
4. Assert success returned

**Expected Result:**
```json
{ "success": true }
```

---

### 2.2 Event Format Tests

#### TC-FMT-001: FullCalendar Event Array Format

**Test ID:** TC-FMT-001  
**Priority:** High

**Steps:**
1. Create CalendarEntryDTO from entity
2. Call toFullCalendarArray()
3. Assert expected fields present:
   - id
   - title
   - start
   - end
   - allDay
   - color
   - extendedProps

**Expected Result:** Valid FullCalendar event object

---

#### TC-FMT-002: All-Day Event Format

**Test ID:** TC-FMT-002  
**Priority:** Medium

**Steps:**
1. Create all-day event
2. Transform to FullCalendar format
3. Assert allDay = true
4. Assert no end time (or same as start)

**Expected Result:** Correct all-day formatting

---

### 2.3 Source Filtering Tests

#### TC-FLT-001: Filter Enabled Sources

**Test ID:** TC-FLT-001  
**Priority:** High

**Steps:**
1. Mock service returns multiple sources
2. Call getEnabledSources() with specific source IDs
3. Assert only requested sources returned

**Expected Result:** Filtered source array

---

#### TC-FLT-002: Empty Source Filter

**Test ID:** TC-FLT-002  
**Priority:** Medium

**Steps:**
1. Mock service returns multiple sources
2. Call getEnabledSources() with empty array
3. Assert all sources returned

**Expected Result:** All sources (no filter applied)

---

## 3. Integration Test Cases

### 3.1 FullCalendar Integration

#### ITC-FC-001: Render with Multiple Sources

**Test ID:** ITC-FC-001  
**Priority:** High

**Pre-conditions:**
- FullCalendar.js loaded
- Multiple sources configured

**Steps:**
1. Render calendar with 3 event sources
2. Assert each source has different color
3. Assert all events loaded

**Expected Result:** Calendar displays multi-colored events

---

#### ITC-FC-002: Event Drop Callback

**Test ID:** ITC-FC-002  
**Priority:** High

**Steps:**
1. Create calendar with editable = true
2. Drag event to new position
3. Assert AJAX PUT request made
4. Assert dates in request body

**Expected Result:** Update request with new dates

---

#### ITC-FC-003: Event Resize Callback

**Test ID:** ITC-FC-003  
**Priority:** Medium

**Steps:**
1. Create calendar with editable = true
2. Resize event duration
3. Assert AJAX PUT request made
4. Assert new end date in body

**Expected Result:** Update request with new duration

---

### 3.2 View Switching

#### ITC-VW-001: Switch to Month View

**Test ID:** ITC-VW-001  
**Priority:** High

**Steps:**
1. Render calendar with default view
2. Select Month view from dropdown
3. Assert FullCalendar changes view
4. Assert header toolbar updates

**Expected Result:** Month grid displayed

---

#### ITC-VW-002: Switch to Week View

**Test ID:** ITC-VW-002  
**Priority:** High

**Steps:**
1. Render calendar
2. Select Week view
3. Assert weekly agenda displayed

**Expected Result:** Week view with time slots

---

## 4. Test Data

### 4.1 Sample Calendar Entry

```php
$sampleEntry = [
    'id' => 1,
    'title' => 'Team Meeting',
    'start_date' => '2026-05-13T10:00:00',
    'end_date' => '2026-05-13T11:00:00',
    'all_day' => false,
    'source' => 'projects',
    'assigned_to' => 'user123',
    'description' => 'Weekly team sync',
];
```

### 4.2 Sample Calendar Sources

```php
$sources = [
    [
        'id' => 'projects',
        'name' => 'Project Management',
        'color' => '#4CAF50',
        'enabled' => true,
    ],
    [
        'id' => 'crm',
        'name' => 'CRM Activities',
        'color' => '#2196F3',
        'enabled' => true,
    ],
    [
        'id' => 'hrm',
        'name' => 'HRM Time Tracking',
        'color' => '#FF9800',
        'enabled' => false,
    ],
];
```

---

## 5. Traceability Matrix

| Requirement | Test Case |
|-------------|-----------|
| FR-CAL-001 | TC-WGT-001 |
| FR-EVT-001 | TC-WGT-002 |
| FR-EVT-002 | TC-WGT-004, TC-WGT-005 |
| FR-OP-001 | TC-WGT-004, TC-WGT-005 |
| FR-OP-002 | TC-WGT-006, TC-WGT-007 |
| FR-OP-003 | TC-WGT-008 |
| FR-INT-001 | ITC-FC-002 |
| FR-INT-002 | ITC-FC-003 |
| FR-SRC-001 | TC-WGT-003, TC-FLT-001 |
| FR-SRC-002 | TC-FLT-001, TC-FLT-002 |

---

## 6. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial test plan |