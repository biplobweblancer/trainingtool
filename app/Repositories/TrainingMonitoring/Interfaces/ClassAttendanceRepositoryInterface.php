<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface ClassAttendanceRepositoryInterface
{
    public function checkAttendants($batchScheduleId, $userId);

    public function store($data);

    public function markAsAbsent($attendances, $scheduleId);
}
