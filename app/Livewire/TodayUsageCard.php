<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WaterConsumptionLog;

class TodayUsageCard extends Component
{
    public $totalUsage = 0;

    public function mount()
    {
        $this->loadTodayUsage();
    }

    public function loadTodayUsage()
    {
        $this->totalUsage = WaterConsumptionLog::where('user_id', auth()->id())
            ->whereDate('date', now()->format('Y-m-d'))
            ->sum('total_consumption') ?: 0;
    }

    public function render()
    {
        return view('livewire.today-usage-card', [
            'userName' => explode(' ', auth()->user()->name)[0]
        ]);
    }
}
