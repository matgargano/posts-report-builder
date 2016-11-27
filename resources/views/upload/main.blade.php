@extends('master');

@section('content')
    @if(Session::get('error'))
        <div class="alert alert-danger">
            {!!  Session::get('error') !!}
        </div>
    @endif

    @if(Session::get('success'))
        <div class="alert alert-success">
            {!!  Session::get('success') !!}
        </div>
    @endif

    <h1>Posts Dashboard</h1>
    <hr>
    <div class="row">
        <div class="col-xs-12 col-md-3">
            <p>Post Count: {{ $postCount  }}</p>
        </div>
        @if($postCount > 0)
            <div class="col-xs-12 col-md-3">
                {{ Form::open(['route' => 'csv.delete.all', 'method' => 'delete', 'class' => 'verify', 'data-custom-confirm' => 'This will delete all posts, are you sure you wish to continue?']) }}
                {{ Form::submit('Delete All Posts', ['class' => 'btn btn-primary']) }}
                {{ Form::close() }}
            </div>
        @endif
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12 col-md-3">
            {{ Form::open(['route' => 'csv.store', 'files' => true]) }}
            <div class="form-group">
                {{ Form::label('csv-file', 'Upload a CSV File of Posts') }}
                {{ Form::file('csv-file') }}


            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="form-group">
                {{ Form::submit('Upload', ['class' => 'btn btn-primary']) }}
            </div>

            {{ Form::close() }}
        </div>
    </div>

    <hr>
    <h2>Reports</h2>

    @if(count($reports)>0 && $postCount > 0)
        @foreach($reportTypes as $reportType)
            <h2>{{ $reportType }}</h2>
            @foreach($reports as $slug=>$report)


                {{ HTML::linkRoute('download', $report['label'] . '(' . $reportType . ')', ['reportType' => $slug, 'reportFormat' => $reportType], ['class' => 'btn btn-primary']) }}
            @endforeach

        @endforeach
    @else
        <p>Please upload posts to enable report running</p>
    @endif

@stop