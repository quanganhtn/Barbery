<?php

namespace App\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ConfirmBooking extends AbstractAction
{
    public function getTitle()
    {
        return 'Xác nhận';
    }

    public function getIcon()
    {
        return 'voyager-check';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success',
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'bookings';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return in_array($row->status, ['pending']);
    }

    public function getDefaultRoute()
    {
        return route('admin.bookings.action.confirm', $this->data->id);
    }
}
