<?php

namespace App\Rules;

use App\Models\Event;
use Illuminate\Contracts\Validation\Rule;

class UniqueEventLocation implements Rule
{
    protected $excludeId;
    protected $location;
    protected $eventLocation;
    protected $eventDate;
    protected $endTime;

    public function __construct(?int $excludeId = null)
    {
        $this->excludeId = $excludeId;
    }

    public function passes($attribute, $value)
    {
        $this->location = request('location');
        $this->eventLocation = request('event_location');
        $this->eventDate = request('event_date');
        $this->endTime = request('end_time');

        if (!$this->location || !$this->eventLocation || !$this->eventDate || !$this->endTime) {
            return true;
        }

        return !Event::hasLocationConflict(
            $this->location,
            $this->eventLocation,
            $this->eventDate,
            $this->endTime,
            $this->excludeId
        );
    }

    public function message()
    {
        return 'Another event already exists at this location (' . $this->eventLocation . ' — ' . $this->location . ') during the selected time period.';
    }
}
