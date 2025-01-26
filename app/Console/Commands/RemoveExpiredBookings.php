<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
class RemoveExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-expired-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredBookings = Booking::where('status', 'pending')
        ->where('created_at', '<', Carbon::now()->subMinutes(2))
        ->get();

    foreach ($expiredBookings as $booking) {
        $booking->delete();
    }

    $this->info('Expired bookings removed successfully.');

    }
}
