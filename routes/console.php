<?php

use Illuminate\Support\Facades\Schedule;

// Daily attendance report
Schedule::command('ems:attendance-report')->dailyAt('18:00');

// Check overdue invoices and apply late fees
Schedule::command('ems:apply-late-fees')->dailyAt('00:00');

// Backup database
Schedule::command('backup:run')->dailyAt('02:00');
Schedule::command('backup:clean')->dailyAt('03:00');

// Check expiring subscriptions
Schedule::command('ems:check-subscriptions')->dailyAt('08:00');
