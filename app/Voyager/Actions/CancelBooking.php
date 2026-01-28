<?php

namespace App\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class CancelBooking extends AbstractAction
{
    public function getTitle()
    {
        return 'Hủy';
    }

    public function getIcon()
    {
        return 'voyager-x';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return ['class' => 'btn btn-sm btn-danger'];
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'bookings';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return in_array($row->status, ['pending', 'confirmed']);
    }

    public function getDefaultRoute()
    {
        return route('admin.bookings.action.cancel', $this->data->id);
    }
}
