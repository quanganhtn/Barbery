@extends('voyager::master')

@section('page_title', 'Bảng lương')

@section('content')
    <div class="page-content container-fluid">
        <h1 class="page-title">
            <i class="voyager-dollar"></i> Bảng lương nhân viên
        </h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="panel panel-bordered">
            <div class="panel-body">
                <form method="GET" action="{{ route('admin.salaries.index') }}" class="form-inline">
                    <div class="form-group">
                        <label>Tháng</label>
                        <select name="month" class="form-control">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group" style="margin-left: 10px;">
                        <label>Năm</label>
                        <input type="number" name="year" class="form-control" value="{{ $year }}">
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                        Xem
                    </button>
                </form>

                <hr>

                <form method="POST" action="{{ route('admin.salaries.calculate') }}" class="form-inline">
                    @csrf

                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <div class="form-group">
                        <label>Công chuẩn</label>
                        <input type="number" step="0.5" name="standard_work_days" class="form-control" value="26">
                    </div>

                    <button type="submit" class="btn btn-success" style="margin-left: 10px;">
                        <i class="voyager-refresh"></i> Tính lương tháng {{ $month }}/{{ $year }}
                    </button>
                </form>
            </div>
        </div>

        <div class="panel panel-bordered">
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nhân viên</th>
                            <th>Tháng</th>
                            <th>Công chuẩn</th>
                            <th>Công thực tế</th>
                            <th>Lương cơ bản</th>
                            <th>Lương theo công</th>
                            <th>Số lịch</th>
                            <th>Công dịch vụ</th>
                            <th>Tổng lương</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->stylist->name ?? 'Không rõ' }}</td>
                                <td>{{ $report->month }}/{{ $report->year }}</td>
                                <td>{{ $report->standard_work_days }}</td>
                                <td>{{ $report->actual_work_days }}</td>
                                <td>{{ number_format($report->base_salary) }}đ</td>
                                <td>{{ number_format($report->earned_base_salary) }}đ</td>
                                <td>{{ $report->total_bookings }}</td>
                                <td>{{ number_format($report->total_commission) }}đ</td>
                                <td><strong>{{ number_format($report->total_salary) }}đ</strong></td>
                                <td>
                                    @if ($report->payment_status === 'paid')
                                        <span class="label label-success">Đã trả</span>
                                    @else
                                        <span class="label label-warning">Chưa trả</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.salaries.show', $report->id) }}" class="btn btn-sm btn-info">
                                        Chi tiết
                                    </a>

                                    @if ($report->payment_status !== 'paid')
                                        <form method="POST" action="{{ route('admin.salaries.paid', $report->id) }}"
                                            style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Đã thanh toán
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">
                                    Chưa có bảng lương. Bấm "Tính lương" để tạo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
