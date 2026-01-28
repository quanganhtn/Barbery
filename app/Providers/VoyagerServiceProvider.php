<?php

namespace App\Providers;

use App\Voyager\Actions\CancelBooking;
use App\Voyager\Actions\CompleteBooking;
use App\Voyager\Actions\ConfirmBooking;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class VoyagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Voyager::addAction(ConfirmBooking::class);
        Voyager::addAction(CancelBooking::class);
        Voyager::addAction(CompleteBooking::class);
    }
}
