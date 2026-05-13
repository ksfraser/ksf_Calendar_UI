# Calendar UI Module - UAT Plan

## Document Information

| Field | Value |
|-------|-------|
| Document Title | User Acceptance Testing Plan |
| Module | ksf_Calendar_UI |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. UAT Overview

### 1.1 Purpose

Validate the Calendar UI provides a functional, intuitive calendar interface with multi-source support, drag-and-drop editing, and iCal integration.

### 1.2 Success Criteria

- All critical scenarios pass
- Calendar renders correctly
- Events display from all sources
- Drag-and-drop works reliably
- Performance meets targets

---

## 2. Test Scenarios

### 2.1 Scenario: CAL-001 - View Calendar

**Priority:** Critical

**Pre-conditions:**
- User authenticated
- Calendar page accessible
- Events exist in system

**Test Steps:**
1. Navigate to Calendar page
2. Observe calendar renders
3. Check events from multiple sources visible
4. Verify color coding per source
5. Check filter toggles present

**Expected Results:**
- Calendar displays in month view by default
- Events show with correct colors
- Filter panel shows source toggles

**Pass Criteria:**
- [ ] Calendar renders within 2 seconds
- [ ] Events from PM source visible
- [ ] Events from CRM source visible
- [ ] Source legend displayed

---

### 2.2 Scenario: CAL-002 - Switch Views

**Priority:** High

**Pre-conditions:**
- Calendar page loaded

**Test Steps:**
1. View default month view
2. Click Week view in toolbar
3. Observe week agenda with time slots
4. Click Day view
5. Observe single day with hours
6. Click List view
7. Observe chronological list

**Expected Results:**
- All view types render correctly
- Events display appropriate for view type
- Navigation controls work

**Pass Criteria:**
- [ ] Month view shows grid
- [ ] Week view shows time slots
- [ ] Day view shows hourly breakdown
- [ ] List view shows chronological events

---

### 2.3 Scenario: CAL-003 - Navigate Date Range

**Priority:** High

**Pre-conditions:**
- Calendar page loaded

**Test Steps:**
1. Note current date range (May 1-31, 2026)
2. Click Previous button
3. Observe: April 2026 displayed
4. Click Next twice
5. Observe: June 2026 displayed
6. Click Today button
7. Observe: Current month displayed

**Expected Results:**
- Navigation updates date range
- Events refetch for new range
- Today highlights current date

**Pass Criteria:**
- [ ] Previous navigates one period back
- [ ] Next navigates one period forward
- [ ] Today returns to current date

---

### 2.4 Scenario: EVT-001 - Create Event

**Priority:** Critical

**Pre-conditions:**
- User has create permissions
- Calendar page loaded

**Test Steps:**
1. Click "Add Event" button
2. Enter title: "Test Meeting"
3. Select source: "Project Management"
4. Set date: May 20, 2026
5. Set time: 2:00 PM - 3:00 PM
6. Click Save
7. Verify event appears on calendar

**Expected Results:**
- Event created successfully
- Event displays on calendar immediately
- Event shows correct color for source

**Pass Criteria:**
- [ ] Event created in database
- [ ] Event appears on calendar
- [ ] Event color matches source
- [ ] Event shows correct time

---

### 2.5 Scenario: EVT-002 - Drag to Reschedule

**Priority:** High

**Pre-conditions:**
- Event exists on calendar
- Calendar editable mode

**Test Steps:**
1. Locate existing event
2. Click and drag event to different date
3. Release to drop
4. Verify event moves to new position
5. Verify event persists after page reload

**Expected Results:**
- Event visually moves during drag
- Event stays in new position after drop
- Database updated with new dates

**Pass Criteria:**
- [ ] Drag operation smooth
- [ ] Event stays at new date after drop
- [ ] New date saved to database
- [ ] Reload shows event in new position

---

### 2.6 Scenario: EVT-003 - Resize Event Duration

**Priority:** Medium

**Pre-conditions:**
- Non-all-day event exists
- Calendar editable mode

**Test Steps:**
1. Locate event on calendar
2. Hover over event to show resize handles
3. Drag bottom edge to extend duration
4. Release to confirm
5. Verify event shows longer duration

**Expected Results:**
- Resize handle visible on hover
- Event visually extends during resize
- New end time saved

**Pass Criteria:**
- [ ] Handles appear on hover
- [ ] Duration changes during resize
- [ ] New duration saved

---

### 2.7 Scenario: EVT-004 - Edit Event Details

**Priority:** High

**Pre-conditions:**
- Event exists

**Test Steps:**
1. Click on event
2. Verify event detail view/modal opens
3. Click Edit button
4. Modify title to "Updated Meeting"
5. Click Save
6. Verify changes reflected

**Expected Results:**
- Event detail opens
- Edit form accessible
- Changes saved and displayed

**Pass Criteria:**
- [ ] Detail view shows all event info
- [ ] Edit form allows changes
- [ ] Save updates event
- [ ] Updated title displays on calendar

---

### 2.8 Scenario: EVT-005 - Delete Event

**Priority:** High

**Pre-conditions:**
- Event exists

**Test Steps:**
1. Click on event
2. Click Delete button
3. Confirm deletion in dialog
4. Verify event removed from calendar

**Expected Results:**
- Confirmation dialog appears
- Event removed from calendar
- Event deleted from database

**Pass Criteria:**
- [ ] Confirmation dialog shown
- [ ] Event removed from view
- [ ] Event deleted from database
- [ ] Page reload confirms deletion

---

### 2.9 Scenario: FLT-001 - Toggle Source Visibility

**Priority:** Medium

**Pre-conditions:**
- Multiple sources configured with events

**Test Steps:**
1. View calendar with all sources visible
2. Uncheck "Project Management" in legend
3. Verify PM events disappear
4. Uncheck "CRM Activities"
5. Verify CRM events disappear
6. Re-check "Project Management"
7. Verify PM events reappear

**Expected Results:**
- Unchecking source hides its events
- Checking restores visibility
- Other sources unaffected

**Pass Criteria:**
- [ ] PM checkbox hides/shows PM events
- [ ] CRM checkbox hides/shows CRM events
- [ ] State persists during session

---

### 2.10 Scenario: ICAL-001 - Export iCal

**Priority:** Medium

**Pre-conditions:**
- Events exist in calendar

**Test Steps:**
1. Select "Project Management" source
2. Click "Export iCal" button
3. Copy generated URL
4. Import URL in external calendar app (Google Calendar)
5. Verify events appear in external calendar

**Expected Results:**
- Valid iCal URL generated
- External calendar accepts URL
- Events sync to external calendar

**Pass Criteria:**
- [ ] URL generated without error
- [ ] URL valid format
- [ ] External calendar imports events

---

### 2.11 Scenario: SEC-001 - Unauthorized Access

**Priority:** High

**Pre-conditions:**
- User A is logged in
- User B has event User A cannot access

**Test Steps:**
1. Attempt to access User B's event directly via API
2. Verify access denied

**Expected Results:**
- 403 Forbidden or 404 returned
- No event details leaked

**Pass Criteria:**
- [ ] Unauthorized API calls rejected
- [ ] No data leakage between users

---

### 2.12 Scenario: PERF-001 - Calendar Performance

**Priority:** Medium

**Pre-conditions:**
- 50+ events in system for user

**Test Steps:**
1. Load calendar page
2. Measure initial render time
3. Switch to Week view
4. Measure time
5. Switch to Month view
6. Measure time
7. Navigate to different month
8. Measure time

**Expected Results:**
- Page load < 2 seconds
- View switches < 500ms
- Navigation < 500ms

**Pass Criteria:**
- [ ] All performance targets met
- [ ] No visible lag during operations

---

## 3. Sign-off Checklist

### 3.1 Functional Checkpoints

- [ ] Calendar renders with events
- [ ] Month view displays correctly
- [ ] Week view displays correctly
- [ ] Day view displays correctly
- [ ] List view displays correctly
- [ ] Navigation works (prev/next/today)
- [ ] Events create successfully
- [ ] Events edit successfully
- [ ] Events delete successfully
- [ ] Drag-and-drop moves events
- [ ] Event resize changes duration
- [ ] Source toggles work
- [ ] iCal export generates URL

### 3.2 Visual Checkpoints

- [ ] Colors match source configuration
- [ ] Events display with correct styling
- [ ] Filter legend renders
- [ ] Toolbar buttons styled correctly
- [ ] Modal/form displays properly
- [ ] Empty states handled

### 3.3 Performance Checkpoints

- [ ] Initial load < 2s
- [ ] View switch < 500ms
- [ ] Event operations < 500ms
- [ ] Drag-drop responsive

### 3.4 Security Checkpoints

- [ ] User can only access own events (or authorized)
- [ ] API authentication enforced
- [ ] Input validation on all fields

---

## 4. Sign-off Approval

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Project Manager | | | |
| QA Lead | | | |
| Business Owner | | | |
| Technical Lead | | | |

---

## 5. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial UAT plan |