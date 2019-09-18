<div class="card">
    <div class="card-status bg-blue"></div>
    <div class="card-header">
        <h3 class="card-title">Analytics Metrics</h3>
        @if(isset($metrics) && $metrics)
            <div class="card-options">
                {!! form()->open(['url' => request()->url(), 'method' => 'get', 'class' => 'form-inline']) !!}
                    <div class="input-group">
                        <label for="start_date-input" class="mr-2">From:</label>
                        <input id="start_date-input" class="form-control form-control-sm" data-mask="0000-00-00" data-mask-clearifnotmatch="true" placeholder="yyyy-mm-dd" name="start_date" type="text" value="{{ request('start_date') ?: '' }}" autocomplete="off" maxlength="10">
                        <label for="end_date-input" class="mr-2 ml-4">From:</label>
                        <input id="end_date-input" class="form-control form-control-sm" data-mask="0000-00-00" data-mask-clearifnotmatch="true" placeholder="yyyy-mm-dd" name="end_date" type="text" value="{{ request('end_date') ?: '' }}" autocomplete="off" maxlength="10">
                        <span class="input-group-btn ml-4">
                            <button class="btn btn-sm btn-primary btn-square mr-2" type="submit">
                                <span class="fe fe-filter mr-2"></span>Filter
                            </button>
                        </span>
                    </div>
                {!! form()->close() !!}
            </div>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                @if(isset($metrics) && $metrics)
                    <div id="analytics-chart"></div>
                @else
                    <span class="text-gray">No data to show because the Google Analytics is not configured within the application.</span>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @if(isset($metrics) && $metrics)
        <!--Load the AJAX API-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {'packages':['line']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var chart = new google.charts.Line(document.getElementById('analytics-chart'));
                var data = new google.visualization.DataTable({!! $metrics !!});

                var options = {
                    width: '100%',
                    height: 400,
                    colors: [
                        '#4AAEE3',
                        '#55D98D',
                        '#DD7467',
                        '#FFC65D'
                    ]
                };

                chart.draw(data, options);
            }
        </script>
    @endif
@endpush
