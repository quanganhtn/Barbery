@extends('voyager::master')

@section('page_title', 'Chi tiết lương')

@section('content')
    <div class="page-content container-fluid">
        <h1 class="page-title">
            <i class="voyager-list"></i> Chi tiết lương
        </h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admin.salaries.index', ['month' => $report->month, 'year' => $report->year]) }}"
            class="btn btn-default">
            Quay lại
        </a>

        <div class="panel panel-bordered" style="margin-top: 20px;">
            <div class="panel-heading">
                <h3 class="panel-title">
                    {{ $report->stylist->name ?? 'Không rõ' }} - Tháng {{ $report->month }}/{{ $report->year }}
                </h3>
            </div>

            <div class="panel-body">
                <p><strong>Công chuẩn:</strong> {{ $report->standard_work_days }}</p>
                <p><strong>Công thực tế:</strong> {{ $report->actual_work_days }}</p>
                <p><strong>Lương cơ bản:</strong> {{ number_format($report->base_salary) }}đ</p>
                <p><strong>Lương theo công:</strong> {{ number_format($report->earned_base_salary) }}đ</p>
                <p><strong>Công dịch vụ:</strong> {{ number_format($report->total_commission) }}đ</p>
                <p><strong>Tổng lương:</strong> <span
                        style="font-size: 20px;">{{ number_format($report->total_salary) }}đ</span></p>

                <p>
                    <strong>Trạng thái:</strong>
                    @if ($report->payment_status === 'paid')
                        <span class="label label-success">Đã trả</span>
                    @else
                        <span class="label label-warning">Chưa trả</span>
                    @endif
                </p>

                @if ($report->payment_status !== 'paid')
                    <form method="POST" action="{{ route('admin.salaries.paid', $report->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Đánh dấu đã thanh toán
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="panel panel-bordered">
            <div class="panel-heading">
                <h3 class="panel-title">Chi tiết chấm công</h3>
            </div>

            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Trạng thái</th>
                            <th>Công</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->work_date }}</td>
                                <td>{{ $attendance->status }}</td>
                                <td>{{ $attendance->work_value }}</td>
                                <td>{{ $attendance->note }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Chưa có dữ liệu chấm công.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($attendances->hasPages())
                    <div style="margin-top: 15px; text-align: center;">
                        @if ($attendances->onFirstPage())
                            <span class="btn btn-default disabled">Trước</span>
                        @else
                            <a href="{{ $attendances->previousPageUrl() }}" class="btn btn-default">Trước</a>
                        @endif

                        <span style="margin: 0 15px;">
                            Trang {{ $attendances->currentPage() }} / {{ $attendances->lastPage() }}
                        </span>

                        @if ($attendances->hasMorePages())
                            <a href="{{ $attendances->nextPageUrl() }}" class="btn btn-default">Sau</a>
                        @else
                            <span class="btn btn-default disabled">Sau</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="panel panel-bordered">
            <div class="panel-heading">
                <h3 class="panel-title">Thống kê công dịch vụ</h3>
            </div>

            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Dịch vụ</th>
                            <th>Số lượt</th>
                            <th>Tiền công/lượt</th>
                            <th>Tổng công</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($serviceStats as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['count'] }}</td>
                                <td>{{ number_format($item['commission']) }}đ</td>
                                <td>{{ number_format($item['total']) }}đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Chưa có công dịch vụ.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
