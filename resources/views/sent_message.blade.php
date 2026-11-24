@extends('layouts.master')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="clearfix" style="margin-bottom: 20px;">
            <h3 class="pull-left" style="margin: 0;">Message Details</h3>
            <button type="button" class="btn btn-warning pull-right" onclick="location.reload();">
                <i class="glyphicon glyphicon-refresh"></i> Refresh Page
            </button>
        </div>
    </div>
    <hr>

    <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
        @endif
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <div class="pull-left">
                    <h3 class="panel-title">{{ $message->subject }}</h3>
                </div>

                <div class="pull-right">
                    <form method="POST" action="/send_message/{{ $message->id }}" style="display:inline-block; margin-left:10px;">
                        @if($message->status == 'sent')
                        <button type="button" disabled class="btn btn-xs btn-success">
                            Sent
                        </button>
                        @elseif($message->status == 'failed')
                        <button type="button" disabled class="btn btn-xs btn-danger">
                            Failed
                        </button>
                        @endif

                        @csrf
                        @method('PATCH')
                        @if($message->status !== 'sent' && $message->replies->isEmpty())
                        <button type="submit" class="btn btn-warning btn-xs">
                            <i class="glyphicon glyphicon-refresh"></i> Retry
                        </button>
                        @endif
                    </form>
                </div>
            </div>
            <div class="panel-body">
                <p><strong>From:</strong> {{ $message->user->nickname ?? 'N/A' }}</p>
                <p><strong>Sent At:</strong> {{ $message->created_at->format('Y-m-d H:i') }}</p>
                <p><strong>Groups:</strong>
                    {{ $message->groups->map(function($group) {
                    return $group->group->description ?? 'Unknown';
                })->implode(', ') }}
                </p>
                <hr>
                <p>{!! nl2br(e($message->content)) !!}</p>
            </div>
        </div>

        <h4>Replies</h4>

        <table class="table table-bordered table-hover">
            <thead>
                <tr class="active">
                    <th>Name</th>
                    <th>Status</th>
                    <th>Reply</th>
                    <th>Received At</th>
                    <th>Replied At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($message->replies->sortByDesc('updated_at') as $reply)
                <tr>
                    <td>{{ $reply->user->nickname ?? 'N/A' }}</td>
                    <td>
                        @if($reply->status == 'sent')
                        <span class="label label-success">Sent</span>
                        @elseif($reply->status == 'failed')
                        <span class="label label-danger">Failed</span>
                        @endif
                    </td>
                    <td>{{ $reply->response ?? '-' }}</td>
                    <td>{{ $reply->created_at ?  Carbon\Carbon::parse($reply->created_at)->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $reply->updated_at ? Carbon\Carbon::parse($reply->updated_at)->format('Y-m-d H:i') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No replies yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection