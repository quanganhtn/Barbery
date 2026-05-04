<?php

namespace App\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ConfirmBooking extends AbstractAction
{
    public function getTitle()  //chữ hiện thị trên nút
    {
        return 'Xác nhận';
    }

    public function getIcon()  //icon trên nút
    {
        return 'voyager-check';
    }

    public function getPolicy()   //nút hiện trên trang được cấp quyền
    {
        return 'edit';
    }

    public function getAttributes()  //css cho nut
    {
        return [
            'class' => 'btn btn-sm btn-success',
        ];
    }

    public function shouldActionDisplayOnDataType()  //nut chỉ hiện trang bảng
    {
        return $this->dataType->slug === 'bookings';
    }

    public function shouldActionDisplayOnRow($row) //chỉ hiện thị khi trang thái
    {
        return in_array($row->status, ['pending']);
    }

    public function getDefaultRoute()  //nhấn để chuyển trạng thái
    {
        return route('admin.bookings.action.confirm', $this->data->id);
    }
}
