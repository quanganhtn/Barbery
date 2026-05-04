<?php

namespace App\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class CompleteBooking extends AbstractAction
{
    public function getTitle() //chữ hiện thị
    {
        return 'Hoàn thành';
    }

    public function getIcon()  //icon hiển thị
    {
        return 'voyager-star';
    }

    public function getPolicy()  //nút hiện trên trang được cấp quyền
    {
        return 'edit';
    }

    public function getAttributes()  //css cho nút
    {
        return ['class' => 'btn btn-sm btn-primary'];
    }

    public function shouldActionDisplayOnDataType()  //chỉ hiện trong bookings
    {
        return $this->dataType->slug === 'bookings';
    }

    public function shouldActionDisplayOnRow($row)  //trạng thái để hiện
    {
        return $row->status === 'confirmed';
    }

    public function getDefaultRoute() //bấm để chuyển trạng thái
    {
        return route('admin.bookings.action.complete', $this->data->id);
    }
}
