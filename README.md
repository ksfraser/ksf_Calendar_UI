# ksf_Calendar_UI

Standalone Calendar UI for `ksfraser/ksf-calendar`.

Built on **FullCalendar.js** for a Google Calendar-like experience.

## Features

- **Multi-calendar views**: Display multiple calendars simultaneously (PM, CRM, HRM, Client Dates)
- **Drag-and-drop**: Move and resize events inline
- **Filter toggles**: Turn calendars on/off per source
- **iCal sync**: Import/export .ics files
- **Color coding**: Each source has a distinct color
- **Date navigation**: Month, week, day, list views
- **Responsive**: Works on desktop and mobile

## Architecture

```
ksf_Calendar_UI/
├── src/
│   ├── API/CalendarApiController.php   # REST endpoints
│   ├── UI/CalendarPageController.php    # Server-rendered pages
│   └── Widget/
│       ├── CalendarWidget.php           # FullCalendar integration
│       ├── FilterWidget.php            # Calendar toggles
│       └── EntryFormWidget.php         # Create/edit modal
├── templates/
│   ├── calendar.php                    # Main calendar page
│   ├── calendar-list.php               # List view
│   ├── entry-form.php                 # Create/edit form
│   └── filters.php                    # Filter panel
└── assets/
    └── js/calendar.js                  # FullCalendar init
```

## FullCalendar.js Integration

The widget generates the JSON data from `CalendarEntryDTO` and initializes FullCalendar with:

```javascript
$('#calendar').fullCalendar({
  events: '/api/calendar/events',
  editable: true,
  eventDrop: function(revertFunc) { ... },
  eventResize: function(revertFunc) { ... },
  eventClick: function(calEvent, jsEvent, view) { ... },
  selectable: true,
  selectHelper: true,
  select: function(start, end) { ... }
});
```

## Multiple Calendar Sources

Users can toggle visibility of:
- Project Management tasks
- CRM activities (calls, meetings)
- HRM time tracking entries
- Client dates (birthdays, anniversaries, renewals)
- Personal events
- External iCal subscriptions

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/calendar/entries | List entries for date range |
| GET | /api/calendar/entries/:id | Get single entry |
| POST | /api/calendar/entries | Create entry |
| PUT | /api/calendar/entries/:id | Update entry |
| DELETE | /api/calendar/entries/:id | Delete entry |
| GET | /api/calendar/sources | List calendar sources |
| GET | /api/calendar/ical/:id | Export iCal feed |
| POST | /api/calendar/subscribe | Subscribe to external iCal |

## License

GPL-3.0