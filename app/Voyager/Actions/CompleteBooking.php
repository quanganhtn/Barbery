<?php

namespace App\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class CompleteBooking extends AbstractAction
{
    public function getTitle()
    {
        return 'Hoàn thành';
    }

    public function getIcon()
    {
        return 'voyager-star';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return ['class' => 'btn btn-sm btn-primary'];
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'bookings';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return $row->status === 'confirmed';
    }

    public function getDefaultRoute()
    {
        return route('admin.bookings.action.complete', $this->data->id);
    }
}
