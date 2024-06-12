<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClassSchedule;

class RotateSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rotate-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rotate the class schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch all class schedules
        $schedules = ClassSchedule::all();

        // Define the days of the week
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        foreach ($schedules as $schedule) {
            // Get the current day index
            $currentDayIndex = array_search($schedule->day, $days);

            // Calculate the next day index (rotate)
            $nextDayIndex = ($currentDayIndex + 1) % count($days);

            // Log the current and next day
            $this->info("Current day: {$days[$currentDayIndex]}, Next day: {$days[$nextDayIndex]}");

            // Update the schedule with the next day
            $schedule->day = $days[$nextDayIndex];
            $schedule->save();
        }

        $this->info('Class schedule rotated successfully.');
    }
}
