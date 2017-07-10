@extends('master')

@section('content')
    <div class="page-reports-schedules">

        <div class="container-top">
            <h4>Schedules</h4>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-sm-8">
                    <div class="col-sm-8" style="padding-left:0;">
                        <h5>Feed <small>({{ $totalCount }} Records)</small></h5>
                    </div>
                    <div class="col-sm-4">
                        <button type="button" class="btn btn-sm btn-warning" style="position:absolute;right:0;" onclick="document.location.href='<?php echo route('schedules.clear'); ?>';">
                        <i class="glyphicon glyphicon glyphicon-trash"></i>
                        Clear History</button>
                    </div>

                    <table class="table table-bordered table-hover table-sm" id="feeds-table">
                        <thead>
                        <tr>
                            <th>Channel</th>
                            <th>Started</th>
                            <th>Ended</th>
                            <th>Duration</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>

                        @foreach($syncLogs as $log)
                            <tr>
                                <td>{{ $log->channel}}</td>
                                <td>{{ $log->created_at->format('m/d/Y H:i:s') }}</td>
                                <td>{{ $log->ended_at != null ? $log->ended_at->format('m/d/y, H:i:s') : '' }}</td>
                                <td>{{ $log->ended_at != null ? $log->duration : '' }}</td>
                                <td>
                                    <div class="font-11 color-blue-grey-lighter uppercase">Created</div>
                                    {{ $log->products_created }}</td>
                                <td><div class="font-11 color-blue-grey-lighter uppercase">Updated</div>
                                    {{ $log->products_updated }}</>
                                <td><div class="font-11 color-blue-grey-lighter uppercase">Removed</div>
                                    {{ $log->products_removed }}</>
                                <td><a href="{{ route('schedules.download', $log->id) }}" class="btn btn-sm" title="Download"><i class="glyphicon glyphicon-cloud-download"></i></a></td>
                            </tr>
                        @endforeach
                    </table>
                    
                    <div>{!! $syncLogs->links() !!}</div>
                </div>

                <div class="col-sm-4">
                    <h5>Schedules</h5>

                    <form action="/schedules" method="POST" class="row">
                        @include('common.errors')
                        <div class="col-sm-5">
                            <fieldset class="form-group">
                                <select name="channel" class="form-control">
                                    <option value="">Channel</option>
                                    <option value="amazon">Amazon</option>
                                    <option value="ebay">eBay</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-5">
                            <fieldset class="form-group">
                                <select name="run_at" class="form-control">
                                    <option value="">Hour</option>
                                    @foreach($hours as $hour)
                                        <option value="{{ $hour->format('H:i:s') }}">{{ $hour->format('h A') }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-2 p-a-0">
                            <button class="btn btn-sm btn-primary-outline">Add</button>
                        </div>
                        {{ csrf_field() }}
                    </form>

                    <div class="m-t-1">
                        @foreach($schedules as $schedule)
                            <div class="row">
                                <div class="col-sm-4">{{ $schedule->channel }}</div>
                                <div class="col-sm-4 text-right">{{ $schedule->run_at->format('g:i a') }}</div>
                                <div class="col-sm-4 text-right">
                                    <a href="/schedules/{{ $schedule->id }}" class="text-danger delete-schedule-link">Delete</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
