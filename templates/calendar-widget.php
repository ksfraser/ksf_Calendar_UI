<?php
/**
 * Calendar Widget Template
 * 
 * Features:
 * - Multi-view calendar (Month, Week, Day, List, Gantt)
 * - Unscheduled task sidebar
 * - Status-based color coding (bright/dim)
 * - Shift schedule view
 * - Source grouping
 */

$uniqId = 'cal_' . uniqid();
$showSidebar = $showSidebar ?? true;
$showGantt = $showGantt ?? false;
$sourceGrouping = $sourceGrouping ?? 'type';
?>
<div class="ksf-calendar-wrapper" id="<?= $uniqId ?>">
    <div class="ksf-cal-main-layout">
        <?php if ($showSidebar): ?>
        <!-- Unscheduled Task Sidebar -->
        <div class="ksf-cal-sidebar" id="taskSidebar">
            <div class="ksf-sidebar-header">
                <h4>Unscheduled Tasks</h4>
                <button type="button" class="ksf-sidebar-toggle" id="toggleSidebar">&#9776;</button>
            </div>
            <div class="ksf-sidebar-content" id="unscheduledTasks">
                <!-- Loaded via AJAX -->
                <div class="ksf-loading">Loading tasks...</div>
            </div>
            <div class="ksf-sidebar-footer">
                <small>Sorted by priority & due date</small>
            </div>
        </div>
        <?php endif; ?>

        <div class="ksf-cal-content">
            <?php if ($showFilters): ?>
            <div class="ksf-calendar-filters">
                <div class="ksf-calendar-legend" id="calendarLegend">
                    <?php
                    // Group sources if requested
                    if ($sourceGrouping === 'customer' || $sourceGrouping === 'project') {
                        $grouped = [];
                        foreach ($sources as $source) {
                            $group = $sourceGrouping === 'customer' 
                                ? ($source->getCustomerName() ?? 'Ungrouped')
                                : ($source->getProjectName() ?? 'Ungrouped');
                            if (!isset($grouped[$group])) {
                                $grouped[$group] = [];
                            }
                            $grouped[$group][] = $source;
                        }
                        foreach ($grouped as $groupName => $groupSources): ?>
                            <div class="ksf-source-group">
                                <span class="ksf-group-name"><?= htmlspecialchars($groupName) ?></span>
                                <?php foreach ($groupSources as $source): ?>
                                    <label class="ksf-cal-source" data-source-id="<?= $source->getId() ?>">
                                        <input type="checkbox" class="ksf-cal-toggle" checked>
                                        <span class="ksf-cal-color" style="background:<?= htmlspecialchars($source->getColor()) ?>"></span>
                                        <span class="ksf-cal-name"><?= htmlspecialchars($source->getName()) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach;
                    } else {
                        foreach ($sources as $source): ?>
                            <label class="ksf-cal-source" data-source-id="<?= $source->getId() ?>">
                                <input type="checkbox" class="ksf-cal-toggle" checked>
                                <span class="ksf-cal-color" style="background:<?= htmlspecialchars($source->getColor()) ?>"></span>
                                <span class="ksf-cal-name"><?= htmlspecialchars($source->getName()) ?></span>
                            </label>
                        <?php endforeach;
                    } ?>
                </div>
                <div class="ksf-calendar-actions">
                    <button type="button" class="btn-primary" id="addEventBtn">+ Add Event</button>
                    <select id="calViewSelect" class="ksf-cal-view-select">
                        <option value="month" <?= $view === 'month' ? 'selected' : '' ?>>Month</option>
                        <option value="agendaWeek" <?= $view === 'week' ? 'selected' : '' ?>>Week</option>
                        <option value="agendaDay" <?= $view === 'day' ? 'selected' : '' ?>>Day</option>
                        <option value="listMonth" <?= $view === 'listMonth' ? 'selected' : '' ?>>List</option>
                        <?php if ($showGantt): ?>
                        <option value="timelineMonth" <?= $view === 'gantt' ? 'selected' : '' ?>>Gantt</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>

            <div class="ksf-calendar-container" style="height:<?= $height ?>px">
                <div id="ksf-fullcalendar"></div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('ksf-fullcalendar');
    var sources = <?= $sourcesJson ?>;
    var userId = '<?= htmlspecialchars($userId) ?>';
    var showSidebar = <?= $showSidebar ? 'true' : 'false' ?>;
    
    // Status-based color opacity mapping
    var statusOpacity = {
        'planned': 1.0,
        'pending': 1.0,
        'confirmed': 1.0,
        'meeting_planned': 1.0,
        'call_planned': 1.0,
        'completed': 0.5,
        'meeting_held': 0.5,
        'call_held': 0.5,
        'cancelled': 0.3,
        'meeting_not_held': 0.3,
        'no_show': 0.3,
        'call_rna': 0.7,
        'call_vmail': 0.7
    };

    // Shift colors
    var shiftColors = {
        'Morning': '#FF9800',
        'Afternoon': '#2196F3',
        'Night': '#9C27B0',
        'Swing': '#F44336'
    };

    // Map view name
    var viewMap = {
        'month': 'dayGridMonth',
        'week': 'timeGridWeek',
        'day': 'timeGridDay',
        'listMonth': 'listMonth',
        'gantt': 'timelineMonth'
    };

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: viewMap['<?= $view ?? 'month' ?>'] || 'dayGridMonth',
        height: '100%',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth<?= $showGantt ? ',timelineMonth' : '' ?>'
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
                textColor: '#ffffff',
                classNames: function(arg) {
                    // Apply status-based opacity
                    var status = arg.event.extendedProps.status || 'planned';
                    var opacity = statusOpacity[status] || 1.0;
                    return 'fc-event-opacity-' + Math.round(opacity * 100);
                }
            };
        }),
        eventDidMount: function(info) {
            // Apply status-based styling
            var status = info.event.extendedProps.status || 'pending';
            var opacity = statusOpacity[status] || 1.0;
            info.el.style.opacity = opacity;
            
            // Add status badge for calls/meetings
            if (info.event.extendedProps.sourceType === 'call' || 
                info.event.extendedProps.sourceType === 'meeting') {
                var badge = document.createElement('span');
                badge.className = 'ksf-event-status-badge';
                badge.textContent = status.replace('call_', '').replace('meeting_', '').toUpperCase();
                info.el.appendChild(badge);
            }
            
            // Color by shift type
            if (info.event.extendedProps.sourceType === 'shift') {
                var shiftType = info.event.extendedProps.shiftType;
                if (shiftColors[shiftType]) {
                    info.el.style.backgroundColor = shiftColors[shiftType];
                    info.el.style.borderColor = shiftColors[shiftType];
                }
            }
        },
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
                } else {
                    // Remove from sidebar if now scheduled
                    removeFromSidebar(entryId);
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

    // Load unscheduled tasks sidebar
    if (showSidebar) {
        loadUnscheduledTasks();
    }

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
        calendar.changeView(viewMap[this.value] || 'dayGridMonth');
    });

    // Toggle sidebar
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        var sidebar = document.getElementById('taskSidebar');
        sidebar.classList.toggle('ksf-sidebar-collapsed');
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

    // Drag from sidebar to calendar
    setupSidebarDragAndDrop();

    // Load unscheduled tasks via AJAX
    function loadUnscheduledTasks() {
        fetch('/api/calendar/unscheduled?user_id=' + userId)
            .then(function(response) {
                return response.json();
            })
            .then(function(tasks) {
                renderUnscheduledTasks(tasks);
            })
            .catch(function(err) {
                document.getElementById('unscheduledTasks').innerHTML = 
                    '<div class="ksf-error">Unable to load tasks</div>';
            });
    }

    function renderUnscheduledTasks(tasks) {
        var container = document.getElementById('unscheduledTasks');
        if (!tasks || tasks.length === 0) {
            container.innerHTML = '<div class="ksf-empty">No unscheduled tasks</div>';
            return;
        }

        var html = '<ul class="ksf-task-list">';
        tasks.forEach(function(task) {
            var priorityClass = 'priority-' + (task.priority || 'medium');
            var dueDate = task.due_date ? new Date(task.due_date) : null;
            var isOverdue = dueDate && dueDate < new Date() && task.status !== 'Completed';
            
            html += '<li class="ksf-task-item ' + priorityClass + '" ' +
                    'data-task-id="' + task.id + '" ' +
                    'data-task-title="' + htmlspecialchars(task.title) + '" ' +
                    'data-task-priority="' + (task.priority || 'medium') + '" ' +
                    'draggable="true">' +
                    '<div class="ksf-task-header">' +
                    '<span class="ksf-task-priority">' + 
                    (task.priority || 'medium').toUpperCase() + '</span>' +
                    '</div>' +
                    '<div class="ksf-task-title">' + htmlspecialchars(task.title) + '</div>' +
                    '<div class="ksf-task-meta">' +
                    '<span class="ksf-task-project">' + (task.project || 'No Project') + '</span>';
            
            if (task.due_date) {
                html += '<span class="ksf-task-due ' + (isOverdue ? 'overdue' : '') + '">' +
                        'Due: ' + formatDate(task.due_date) + '</span>';
            }
            
            html += '</div></li>';
        });
        html += '</ul>';
        container.innerHTML = html;
        
        // Add drag handlers
        container.querySelectorAll('.ksf-task-item').forEach(function(item) {
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', JSON.stringify({
                    id: item.dataset.taskId,
                    title: item.dataset.taskTitle,
                    priority: item.dataset.taskPriority
                }));
                item.classList.add('dragging');
            });
            item.addEventListener('dragend', function() {
                item.classList.remove('dragging');
            });
        });
    }

    function setupSidebarDragAndDrop() {
        calendarEl.addEventListener('dragover', function(e) {
            e.preventDefault();
        });
        
        calendarEl.addEventListener('drop', function(e) {
            e.preventDefault();
            var data = e.dataTransfer.getData('text/plain');
            if (data) {
                var task = JSON.parse(data);
                var rect = calendarEl.getBoundingClientRect();
                var dateStr = prompt('Schedule task "' + task.title + '" for:', 
                    new Date().toISOString().split('T')[0]);
                
                if (dateStr) {
                    fetch('/api/pm/tasks/' + task.id, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ start_date: dateStr })
                    }).then(function(response) {
                        return response.json();
                    }).then(function(data) {
                        if (data.success) {
                            calendar.refetchEvents();
                            loadUnscheduledTasks();
                        }
                    });
                }
            }
        });
    }

    function removeFromSidebar(taskId) {
        var item = document.querySelector('.ksf-task-item[data-task-id="' + taskId + '"]');
        if (item) {
            item.style.opacity = '0';
            setTimeout(function() {
                item.remove();
            }, 300);
        }
    }

    function formatDate(dateStr) {
        var date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    function htmlspecialchars(str) {
        return str.replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&#039;');
    }
});
</script>

<style>
.ksf-calendar-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.ksf-cal-main-layout {
    display: flex;
    gap: 10px;
}

.ksf-cal-content {
    flex: 1;
    min-width: 0;
}

/* Sidebar */
.ksf-cal-sidebar {
    width: 280px;
    background: #fafafa;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    transition: width 0.3s;
}

.ksf-cal-sidebar.ksf-sidebar-collapsed {
    width: 40px;
}

.ksf-sidebar-header {
    padding: 10px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ksf-sidebar-header h4 {
    margin: 0;
    font-size: 14px;
}

.ksf-sidebar-collapsed .ksf-sidebar-header h4 {
    display: none;
}

.ksf-sidebar-toggle {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

.ksf-sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    max-height: 500px;
}

.ksf-sidebar-footer {
    padding: 8px 10px;
    border-top: 1px solid #e0e0e0;
    font-size: 11px;
    color: #888;
}

/* Task list */
.ksf-task-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.ksf-task-item {
    padding: 10px;
    margin-bottom: 8px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    cursor: grab;
    transition: opacity 0.3s, transform 0.3s;
}

.ksf-task-item:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ksf-task-item.dragging {
    opacity: 0.5;
}

.ksf-task-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
}

.ksf-task-priority {
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 2px;
    text-transform: uppercase;
}

.priority-high .ksf-task-priority {
    background: #f44336;
    color: white;
}

.priority-medium .ksf-task-priority {
    background: #ff9800;
    color: white;
}

.priority-low .ksf-task-priority {
    background: #4caf50;
    color: white;
}

.ksf-task-title {
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 4px;
}

.ksf-task-meta {
    font-size: 11px;
    color: #666;
    display: flex;
    justify-content: space-between;
}

.ksf-task-due.overdue {
    color: #f44336;
    font-weight: bold;
}

/* Calendar filters */
.ksf-calendar-filters {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
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

.ksf-source-group {
    border: 1px solid #e0e0e0;
    padding: 8px;
    border-radius: 4px;
    background: white;
}

.ksf-group-name {
    font-weight: bold;
    font-size: 12px;
    display: block;
    margin-bottom: 5px;
    color: #333;
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

/* Status-based opacity */
.fc-event-opacity-100 { opacity: 1.0; }
.fc-event-opacity-70 { opacity: 0.7; }
.fc-event-opacity-50 { opacity: 0.5; }
.fc-event-opacity-30 { opacity: 0.3; }

/* Event status badge */
.ksf-event-status-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #333;
    color: white;
    font-size: 8px;
    padding: 2px 4px;
    border-radius: 2px;
}

/* Loading/empty states */
.ksf-loading, .ksf-empty, .ksf-error {
    text-align: center;
    padding: 20px;
    color: #888;
}

.ksf-error {
    color: #f44336;
}
</style>