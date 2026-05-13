# Calendar UI Module - Use Cases

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Use Case Specification |
| Module | ksf_Calendar_UI |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Use Case Overview

### 1.1 Actor Definitions

| Actor | Description | Access Level |
|-------|-------------|--------------|
| **User** | End user viewing calendar | View, create, edit own events |
| **Manager** | Team manager | View all team events |
| **Admin** | System administrator | Full access, manage sources |

### 1.2 Use Case Index

| ID | Use Case | Primary Actor | Priority |
|----|----------|---------------|----------|
| UC-CAL-001 | View Calendar | User | Critical |
| UC-CAL-002 | Switch Calendar View | User | High |
| UC-CAL-003 | Navigate Date Range | User | High |
| UC-EVT-001 | Create Event | User | Critical |
| UC-EVT-002 | View Event Details | User | High |
| UC-EVT-003 | Edit Event | User | High |
| UC-EVT-004 | Delete Event | User | High |
| UC-EVT-005 | Drag Event | User | High |
| UC-EVT-006 | Resize Event | User | Medium |
| UC-FLT-001 | Toggle Source Visibility | User | Medium |
| UC-FLT-002 | Filter by Date Range | User | Low |
| UC-ICAL-001 | Export iCal | User | Medium |
| UC-ICAL-002 | Subscribe External Calendar | User | Low |

---

## 2. Use Case Specifications

### 2.1 UC-CAL-001: View Calendar

**Description:** Display calendar with events from all enabled sources.

**Primary Actor:** User

**Pre-conditions:**
- User authenticated
- Calendar page loaded

**Post-conditions:**
- Calendar rendered with events
- All enabled source events visible

**Basic Flow:**
1. User navigates to calendar page
2. CalendarWidget::renderCalendar() called
3. FullCalendar.js initialized
4. Event sources loaded via AJAX
5. Events rendered on calendar
6. Filter toggles displayed

**Alternative Flows:**

**A1: No Events**
1. Calendar renders without events
2. Empty state message shown
3. "Add Event" button highlighted

**A2: Loading**
1. Calendar shows loading indicator
2. Events loaded asynchronously
3. Loading indicator removed when complete

---

### 2.2 UC-CAL-002: Switch Calendar View

**Description:** Change between different calendar view modes.

**Primary Actor:** User

**Pre-conditions:**
- Calendar loaded

**Post-conditions:**
- View updated to selected type

**Basic Flow:**
1. User clicks view selector dropdown
2. User selects desired view (Month/Week/Day/List)
3. FullCalendar changes view
4. Events refetched for new range
5. View persists during session

**Views:**
- Month: Default view, shows month grid
- Week: Shows week with daily columns and time slots
- Day: Single day with hourly breakdown
- List: Chronological list of events

---

### 2.3 UC-CAL-003: Navigate Date Range

**Description:** Move between date ranges in calendar view.

**Primary Actor:** User

**Pre-conditions:**
- Calendar loaded

**Post-conditions:**
- Calendar displays new date range

**Basic Flow:**
1. User clicks Previous/Next button
2. Calendar navigates to previous/next range
3. New events fetched for visible range
4. Events rendered in new view

**Shortcuts:**
- "Today" button returns to current date
- "Today" button also selects today if viewing past/future

---

### 2.4 UC-EVT-001: Create Event

**Description:** Create a new calendar event.

**Primary Actor:** User

**Pre-conditions:**
- User authenticated
- Sufficient permissions to create events

**Post-conditions:**
- Event created in database
- Event appears on calendar

**Basic Flow:**
1. User clicks "Add Event" button OR clicks on calendar slot
2. If clicked slot: dates pre-filled
3. User enters event details:
   - Title (required)
   - Start date/time
   - End date/time
   - Source (dropdown)
   - Description (optional)
4. User clicks Save
5. CalendarWidget::createEventFromAjax() processes
6. Event saved to database via CalendarService
7. Calendar refreshes to show new event
8. Success message displayed

**Alternative Flows:**

**A1: Quick Add (click on slot)**
1. User clicks on calendar slot
2. Prompt for event title
3. User enters title
4. Event created with pre-filled dates
5. User can click event to edit details later

**A2: Validation Error**
1. User submits without required title
2. Form shows validation error
3. User provides title
4. Event created successfully

---

### 2.5 UC-EVT-002: View Event Details

**Description:** View full details of a calendar event.

**Primary Actor:** User

**Pre-conditions:**
- Event exists on calendar

**Post-conditions:**
- Event details displayed

**Basic Flow:**
1. User clicks on event
2. FullCalendar eventClick callback fires
3. Page navigates to /calendar/entry/{id}
4. Entry form displays with details
5. User can view description, attendees, etc.

---

### 2.6 UC-EVT-003: Edit Event

**Description:** Modify an existing calendar event.

**Primary Actor:** User

**Pre-conditions:**
- Event exists
- User has edit permissions

**Post-conditions:**
- Event updated in database
- Calendar reflects changes

**Basic Flow:**
1. User clicks on event (UC-EVT-002)
2. User clicks Edit button
3. Form becomes editable
4. User modifies fields
5. User clicks Save
6. CalendarWidget::updateEventFromAjax() processes
7. Database updated
8. Calendar reflects new event data

**Alternative Flows:**

**A1: Drag to Reschedule**
1. User drags event to new date/time
2. FullCalendar eventDrop callback fires
3. CalendarWidget::updateEventFromAjax() called with new dates
4. Database updated
5. Event stays in new position

**A2: Resize Duration**
1. User drags event edge
2. FullCalendar eventResize callback fires
3. CalendarWidget::updateEventFromAjax() called with new end date
4. Database updated
5. Event shows new duration

**A3: Server Error on Update**
1. User drags event
2. AJAX update request sent
3. Server returns error
4. event.revert() called
5. Event returns to original position
6. Error message displayed

---

### 2.7 UC-EVT-004: Delete Event

**Description:** Remove an event from the calendar.

**Primary Actor:** User

**Pre-conditions:**
- Event exists
- User has delete permissions

**Post-conditions:**
- Event removed from database
- Event removed from calendar display

**Basic Flow:**
1. User clicks on event
2. User clicks Delete button
3. Confirmation dialog appears
4. User confirms deletion
5. CalendarWidget::deleteEventFromAjax() called
6. Event removed from database
7. Event removed from calendar view
8. Success message displayed

**Alternative Flows:**

**A1: Cancel Delete**
1. User clicks Delete
2. Confirmation dialog appears
3. User clicks Cancel
4. No action taken
5. Dialog closes

---

### 2.8 UC-EVT-005: Drag Event

**Description:** Move an event by dragging to a new position.

**Primary Actor:** User

**Pre-conditions:**
- Calendar editable = true
- Event exists

**Post-conditions:**
- Event dates updated to new position

**Basic Flow:**
1. User sees event on calendar
2. User clicks and drags event
3. Ghost element shows new position
4. User drops event
5. Dates updated via AJAX
6. Event stays in new position

**Cross-source Constraints:**
- Events cannot be dragged between incompatible sources
- Some sources may be read-only

---

### 2.9 UC-EVT-006: Resize Event

**Description:** Change event duration by dragging edges.

**Primary Actor:** User

**Pre-conditions:**
- Calendar editable = true
- Event is not all-day

**Post-conditions:**
- Event duration updated

**Basic Flow:**
1. User hovers over event
2. Resize handles appear at edges
3. User drags handle to adjust duration
4. New duration shown
5. User releases
6. End date updated via AJAX
7. Event shows new duration

---

### 2.10 UC-FLT-001: Toggle Source Visibility

**Description:** Show or hide events from a specific calendar source.

**Primary Actor:** User

**Pre-conditions:**
- Calendar loaded
- Multiple sources configured

**Post-conditions:**
- Source events added/removed from view

**Basic Flow:**
1. User sees source filter toggles in legend
2. User unchecks a source checkbox
3. FullCalendar removes that source's events
4. Checked sources remain visible
5. User preference saved

---

### 2.11 UC-FLT-002: Filter by Date Range

**Description:** View events within a specific date range.

**Primary Actor:** User

**Pre-conditions:**
- Calendar loaded

**Post-conditions:**
- Only events in range displayed

**Basic Flow:**
1. User selects start date
2. User selects end date
3. CalendarWidget fetches for range
4. Only matching events displayed

---

### 2.12 UC-ICAL-001: Export iCal Feed

**Description:** Generate iCal URL to subscribe in external calendar app.

**Primary Actor:** User

**Pre-conditions:**
- User authenticated
- Valid calendar source selected

**Post-conditions:**
- iCal URL generated

**Basic Flow:**
1. User selects calendar source
2. User clicks Export iCal button
3. System generates unique URL: /api/calendar/ical/{source_id}
4. URL displayed/copied to clipboard
5. User can paste into external calendar app

---

### 2.13 UC-ICAL-002: Subscribe to External Calendar

**Description:** Import events from external iCal URL.

**Primary Actor:** User

**Pre-conditions:**
- Valid iCal URL available

**Post-conditions:**
- External events imported as new source

**Basic Flow:**
1. User clicks Subscribe to External Calendar
2. User enters iCal URL
3. User provides display name
4. User clicks Subscribe
5. System fetches and parses iCal
6. External source created in system
7. Source appears in filter toggles
8. Events imported to calendar

---

## 3. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |