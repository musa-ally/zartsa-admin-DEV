@extends('components.master')
@section('title')
    Dashboard
@endsection
@section('content')
    <!--Section: Statistics with subtitles-->
    <section>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onchange="switchCurrency(this.id)" />
            <label class="form-check-label" for="flexSwitchCheckDefault" id="switchCurrency">Switch to USD</label>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between p-md-1">
                            <div class="d-flex flex-row">
                                <div class="align-self-center">
                                    <h3 id="dailyRevenueHeader" class="mb-0 me-4">TZS {{ number_format($daily_tzs) }}</h3>
                                </div>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-bar text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0">Daily income</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between p-md-1">
                            <div class="d-flex flex-row">
                                <div class="align-self-center">
                                    <h3 id="weeklyRevenueHeader" class="mb-0 me-4">TZS {{ number_format($weekly_tzs) }}</h3>
                                </div>
                            </div>
                            <div class="align-self-center">
                                <i class="far fa-chart-bar text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0">Weekly income</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between p-md-1">
                            <div class="d-flex flex-row">
                                <div class="align-self-center">
                                    <h3 id="monthlyRevenueHeader" class="mb-0 me-4">TZS {{ number_format($current_month_earnings_tzs) }}</h3>
                                </div>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-bar text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0">Monthly income</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between p-md-1">
                            <div class="d-flex flex-row">
                                <div class="align-self-center">
                                    <h3 id="yearlyRevenueHeader" class="mb-0 me-4">TZS {{ number_format($yearly_tzs) }}</h3>
                                </div>
                            </div>
                            <div class="align-self-center">
                                <i class="far fa-chart-bar text-success fa-2x"></i>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0">Yearly income</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Section: Statistics with subtitles-->

    <section class="mb-4">
        <div class="card pd-b-20">
            <div class="card-body">
                <div class="earning-report">
                    <div class="dropdown">
                        <a class="date-dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                           aria-expanded="false">{{ \Carbon\Carbon::now()->toFormattedDateString() }}</a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">{{ \Carbon\Carbon::now()->toDateString() }}</a>
                        </div>
                    </div>
                </div>
                <div class="earning-chart-wrap">
                    <canvas id="earning-line-chart" width="660" height="400"></canvas>
                </div>
            </div>
        </div>
    </section>

    <!--Section: Port Performance KPIs-->
    <section class="mb-4">
        <div class="card">
            <div class="card-header text-center py-3">
                <h5 class="mb-0 text-center">
                    <strong>Road Performance KPIs</strong>
                </h5>
            </div>
            <?php
            ?>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th scope="col">Services</th>
                            <th scope="col" id="tblColDaily">Daily income (TZS)</th>
                            <th scope="col" id="tblColWeekly">Weekly income (TZS)</th>
                            <th scope="col" id="tblColMonthly">Monthly income (TZS)</th>
                            <th scope="col" id="tblColYearly">Yearly income (TZS)</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <th scope="row">{{ $service->name }}</th>
                                    <td>
                                        <span class="text-success" style="font-weight: bolder; font-size: large">
                                            @if(count($daily_summery) > 0)
                                                @foreach($daily_summery as $daily)
                                                    @if($daily->services_id == $service->id)
                                                        {{ number_format($daily->amount_tzs) }}
                                                    @else
                                                        0.0
                                                    @endif
                                                @endforeach
                                            @else
                                                0.0
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success" style="font-weight: bolder; font-size: large">
                                            @if(count($weekly_summery) > 0)
                                                @foreach($weekly_summery as $weekly)
                                                    @if($weekly->services_id == $service->id)
                                                        {{ number_format($weekly->amount_tzs) }}
                                                    @else
                                                        0.0
                                                    @endif
                                                @endforeach
                                            @else
                                                0.0
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success" style="font-weight: bolder; font-size: large">
                                            @if(count($monthly_summery) > 0)
                                                @foreach($monthly_summery as $monthly)
                                                    @if($monthly->services_id == $service->id)
                                                        {{ number_format($monthly->amount_tzs) }}
                                                    @else
                                                        0.0
                                                    @endif
                                                @endforeach
                                            @else
                                                0.0
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success" style="font-weight: bolder; font-size: large">
                                            @if(count($yearly_summery) > 0)
                                                @foreach($yearly_summery as $yearly)
                                                    @if($yearly->services_id == $service->id)
                                                        {{ number_format($yearly->amount_tzs) }}
                                                    @else
                                                        0.0
                                                    @endif
                                                @endforeach
                                            @else
                                                0.0
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!--Section: Sales Performance KPIs-->

    <script>
        const daily_tzs = '{{ $daily_tzs }}'
        const daily_usd = '{{ $daily_usd }}'

        const weekly_tzs = '{{ $weekly_tzs }}'
        const weekly_usd = '{{ $weekly_usd }}'

        const monthly_tzs = '{{ $current_month_earnings_tzs }}'
        const monthly_usd = '{{ $current_month_earnings_usd }}'

        const yearly_tzs = '{{ $yearly_tzs }}'
        const yearly_usd = '{{ $yearly_usd }}'

        {{--const monthly_graph_tzs = '{{ $monthly_tzs }}'--}}
        {{--const monthly_graph_usd = '{{ $monthly_usd }}'--}}
    </script>

@endsection
@section('function')
    callEarningChart({{ json_encode($monthly_tzs) }},{{ json_encode([]) }});
@endsection
