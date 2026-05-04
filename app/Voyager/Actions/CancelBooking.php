<?php

namespace App\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class CancelBooking extends AbstractAction
{
    public function getTitle() //nút hủy
    {
        return 'Hủy';
    }

    public function getIcon()  //icon hủy booking
    {
        return 'voyager-x';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes() //CSS cho nút hủy
    {
        return ['class' => 'btn btn-sm btn-danger'];
    }

    public function shouldActionDisplayOnDataType()  //đặt nút hủy ở trang bookings
    {
        return $this->dataType->slug === 'bookings';
    }

    public function shouldActionDisplayOnRow($row)  //quyết định nút hủy hiện khi nào
    {
        return in_array($row->status, ['pending', 'confirmed']);
    }

    public function getDefaultRoute()  //route chạy khi admin bấm hủy
    {
        return route('admin.bookings.action.cancel', $this->data->id);
    }
}
