# RTM.md - ksf_Calendar_UI

## Document Information
- **Module**: ksf_Calendar_UI
- **Version**: 1.1.0
- **Date**: 2026-05-14
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Overview

This is a **frontend UI adapter** module. It provides the user interface for calendar functionality, consuming business logic from `ksf_Calendar`.

---

## 2. Adapter Requirements

### Core Features (v1.0.0)

| FR ID | Requirement | Test Cases | Status |
|-------|-------------|------------|--------|
| FR-UI-CAL-001 | Calendar display | UI-CAL-001 | ✓ |
| FR-UI-CAL-002 | Event creation form | UI-CAL-002 | ✓ |
| FR-UI-CAL-003 | Event editing | UI-CAL-003 | ✓ |
| FR-UI-CAL-004 | Drag-and-drop scheduling | UI-CAL-004 | ✓ |
| FR-UI-CAL-005 | Multi-view support (Month/Week/Day/List) | UI-CAL-005 | ✓ |
| FR-UI-CAL-006 | Source filtering | UI-CAL-006 | ✓ |

### Advanced Features (v1.1.0)

| FR ID | Requirement | Test Cases | Status |
|-------|-------------|------------|--------|
| FR-STS-001 | Meeting status tracking (planned/held/not-held/rescheduled) | UI-STS-001 | ✓ |
| FR-STS-002 | Call outcome tracking (RNA/VMail/followup) | UI-STS-002 | ✓ |
| FR-SHF-001 | Shift schedule view (Morning/Afternoon/Night/Swing) | UI-SHF-001 | ✓ |
| FR-SHF-002 | Shift color coding | UI-SHF-002 | ✓ |
| FR-SDB-001 | Unscheduled task sidebar | UI-SDB-001 | ✓ |
| FR-SDB-002 | Drag task to calendar scheduling | UI-SDB-002 | ✓ |
| FR-CLR-001 | Status-based color opacity | UI-CLR-001 | ✓ |
| FR-GRP-001 | Source grouping (customer/project/type) | UI-GRP-001 | ✓ |

---

## 3. Requirement Mapping

### Business Requirements → Functional Requirements

| BR ID | Description | Related FRs |
|-------|-------------|-------------|
| BR-CAL-01 | Display daily/weekly/monthly views | FR-UI-CAL-005 |
| BR-CAL-02 | Different sub-calendars by customer/project/type | FR-GRP-001 |
| BR-CAL-03 | User controllable colors per sub-calendar | FR-UI-CAL-006 |
| BR-CAL-04 | Sidebar showing unscheduled tasks | FR-SDB-001 |
| BR-CAL-05 | Meetings have status (planned/held/not-held) | FR-STS-001 |
| BR-CAL-06 | Calls have outcomes (held/RNA/VMail/followup) | FR-STS-002 |
| BR-CAL-07 | Bright colors for planned, dimmed for complete | FR-CLR-001 |
| BR-CAL-08 | Shift schedule display | FR-SHF-001, FR-SHF-002 |

### Functional Requirements → Test Cases

| FR ID | Test Case ID | Description |
|-------|--------------|-------------|
| FR-UI-CAL-001 | UI-CAL-001 | Render calendar widget |
| FR-UI-CAL-002 | UI-CAL-002 | Create event via AJAX |
| FR-UI-CAL-003 | UI-CAL-003 | Update event via AJAX |
| FR-STS-001 | UI-STS-001 | Meeting status badge display |
| FR-STS-002 | UI-STS-002 | Call outcome badge display |
| FR-SDB-001 | UI-SDB-001 | Load and display unscheduled tasks |
| FR-SDB-002 | UI-SDB-002 | Drag task to calendar |
| FR-CLR-001 | UI-CLR-001 | Apply opacity based on status |
| FR-SHF-001 | UI-SHF-001 | Display shifts with colors |
| FR-GRP-001 | UI-GRP-001 | Group sources by type/customer/project |

---

## 4. Test Cases

### UI-CAL-001: Render Calendar Widget
- **Input**: Valid CalendarService, userId, options
- **Expected**: HTML string with FullCalendar initialized
- **Status**: ✓ PASS

### UI-STS-001: Meeting Status Badge
- **Input**: Event with status=meeting_planned
- **Expected**: Badge shows "PLANNED" on event
- **Status**: ✓ PASS

### UI-SDB-001: Unscheduled Task Sidebar
- **Input**: User with unscheduled PM tasks
- **Expected**: Tasks sorted by priority, draggable
- **Status**: ✓ PASS

### UI-CLR-001: Status-Based Opacity
- **Input**: Events with various statuses
- **Expected**: Planned=100%, Held=50%, RNA=70%, Cancelled=30%
- **Status**: ✓ PASS

---

## 5. Integration

| Component | Interface |
|-----------|-----------|
| Consumes | ksf_Calendar, ksf_ProjectManagement, ksf_Roster |
| Platform | Frontend/UI (FullCalendar.js) |
| API | /api/calendar/* endpoints |

---

## 6. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | 2026-05-12 | KSFII Team | Initial specification |
| 1.1.0 | 2026-05-14 | KSFII Team | Added: Meeting/Call status, Shift schedule, Unscheduled sidebar, Status colors, Source grouping |

*Document Version: 1.1.0*
*Last Updated: 2026-05-14*