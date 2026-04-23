<?php
/**
 * Calendar Widget Template
 */

$uniqId = 'cal_' . uniqid();
?>
<div class="ksf-calendar-wrapper" id="<?= $uniqId ?>">
    <?php if ($showFilters): ?>
    <div class="ksf-calendar-filters">
        <div class="ksf-calendar-legend">
            <?php foreach ($sources as $source): ?>
                <label class="ksf-cal-source" data-source-id="<?= $source->getId() ?>">
                    <input type="checkbox" class="ksf-cal-toggle" checked>
                    <span class="ksf-cal-color" style="background:<?= htmlspecialchars($source->getColor()) ?>"></span>
                    <span class="ksf-cal-name"><?= htmlspecialchars($source->getName()) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="ksf-calendar-actions">
            <button type="button" class="btn-primary" id="addEventBtn">+ Add Event</button>
            <select id="calViewSelect" class="ksf-cal-view-select">
                <option value="month" <?= $view === 'month' ? 'selected' : '' ?>>Month</option>
                <option value="agendaWeek" <?= $view === 'week' ? 'selected' : '' ?>>Week</option>
                <option value="agendaDay" <?= $view === 'day' ? 'selected' : '' ?>>Day</option>
                <option value="listMonth" <?= $view === 'listMonth' ? 'selected' : '' ?>>List</option>
            </select>
        </div>
    </div>
    <?php endif; ?>

    <div class="ksf-calendar-container" style="height:<?= $height ?>px">
        <div id="ksf-fullcalendar"></div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('ksf-fullcalendar');
    var sources = <?= $sourcesJson ?>;
    var userId = '<?= htmlspecialchars($userId) ?>';

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: '<?= $view === 'week' ? 'agendaWeek' : ($view === 'day' ? 'agendaDay' : ($view === 'listMonth' ? 'listMonth' : 'dayGridMonth')) ?>',
        height: '100%',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        editable: <?= $editable ? 'true' : 'false' ?>,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: 5,
        weekends: true,
        nowIndicator: true,
        eventSources: sources.map(function(src) {
            return {
                id: src.id,
                url: '/api/calendar/entries?source=' + src.source + '&user_id=' + userId,
                color: src.color,
                textColor: '#ffffff'
            };
        }),
        eventDrop: function(info) {
            var entryId = info.event.id;
            var newStart = info.event.startStr;
            var newEnd = info.event.endStr ? info.event.endStr : null;

            fetch('/api/calendar/entries/' + entryId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ start_date: newStart, end_date: newEnd })
            }).then(function(response) {
                if (!response.ok) {
                    info.revert();
                }
            }).catch(function() {
                info.revert();
            });
        },
        eventResize: function(info) {
            var entryId = info.event.id;
            var newStart = info.event.startStr;
            var newEnd = info.event.endStr ? info.event.endStr : null;

            fetch('/api/calendar/entries/' + entryId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ start_date: newStart, end_date: newEnd })
            }).then(function(response) {
                if (!response.ok) {
                    info.revert();
                }
            }).catch(function() {
                info.revert();
            });
        },
        eventClick: function(info) {
            window.location.href = '/calendar/entry/' + info.event.id;
        },
        select: function(info) {
            var title = prompt('Enter event title:');
            if (title) {
                fetch('/api/calendar/entries', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        title: title,
                        start_date: info.startStr,
                        end_date: info.endStr,
                        all_day: info.allDay,
                        assigned_to: userId
                    })
                }).then(function(response) {
                    return response.json();
                }).then(function(data) {
                    if (data.success) {
                        calendar.refetchEvents();
                    }
                });
            }
            calendar.unselect();
        }
    });

    calendar.render();

    // Toggle calendar visibility
    document.querySelectorAll('.ksf-cal-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            var sourceId = this.closest('.ksf-cal-source').dataset.sourceId;
            var sourceIndex = sources.findIndex(function(s) { return s.id == sourceId; });
            if (sourceIndex >= 0) {
                var source = calendar.getEventSourceById(sourceId);
                if (source) {
                    if (this.checked) {
                        source.refetch();
                    } else {
                        source.remove();
                    }
                }
            }
        });
    });

    // View change
    document.getElementById('calViewSelect').addEventListener('change', function() {
        var viewMap = {
            'month': 'dayGridMonth',
            'week': 'timeGridWeek',
            'day': 'timeGridDay',
            'listMonth': 'listMonth'
        };
        calendar.changeView(viewMap[this.value]);
    });

    // Add event button
    document.getElementById('addEventBtn').addEventListener('click', function() {
        var now = new Date();
        var today = now.toISOString().split('T')[0];
        var title = prompt('Enter event title:');
        if (title) {
            fetch('/api/calendar/entries', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    title: title,
                    start_date: today,
                    assigned_to: userId
                })
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                if (data.success) {
                    calendar.refetchEvents();
                }
            });
        }
    });
});
</script>

<style>
.ksf-calendar-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.ksf-calendar-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f5f5f5;
    border-radius: 4px;
    margin-bottom: 10px;
}
.ksf-calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.ksf-cal-source {
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
}
.ksf-cal-color {
    width: 14px;
    height: 14px;
    border-radius: 2px;
}
.ksf-cal-name {
    font-size: 13px;
}
.ksf-calendar-actions {
    display: flex;
    gap: 10px;
}
.btn-primary {
    background: #2196F3;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}
.fc {
    background: white;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
</style>