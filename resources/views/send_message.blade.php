@extends('layouts.master')

@section('content')

<style>
    .message-content {
        white-space: pre-wrap;
    }

    .has-error .help-block {
        color: red;
    }

    .status-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 6px;
        vertical-align: middle;
    }

    .status-dot.pending {
        background-color: orange;
    }

    .status-dot.sent {
        background-color: green;
    }

    .status-dot.failed {
        background-color: red;
    }
</style>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Send Message to Groups</h3>
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

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Compose New Message</strong>
            </div>
            <div class="panel-body">
                <form action="/send_message" method="POST">
                    @csrf

                    {{-- Groups --}}
                    <div class="form-group {{ $errors->has('groups') ? 'has-error' : '' }}">
                        <label for="groups">Groups</label>
                        <select name="groups[]" id="groups" class="form-control standardSelect" multiple>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ collect(old('groups'))->contains($group->id) ? 'selected' : '' }}>
                                {{ $group->description }}
                            </option>
                            @endforeach
                        </select>
                        @if ($errors->has('groups'))
                        <span class="help-block">{{ $errors->first('groups') }}</span>
                        @endif
                    </div>

                    {{-- Subject --}}
                    <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" maxlength="200">
                        @if ($errors->has('subject'))
                        <span class="help-block">{{ $errors->first('subject') }}</span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                        <label for="content">Message Content</label>
                        <textarea name="content" id="content" rows="5" class="form-control" maxlength="4000">{{ old('content') }}</textarea>
                        @if ($errors->has('content'))
                        <span class="help-block">{{ $errors->first('content') }}</span>
                        @endif
                    </div>

                    <div class="form-group {{ $errors->has('response_type') ? 'has-error' : '' }}">
                        <label for="response_type">Response Type</label>
                        <select name="response_type" id="response_type" class="form-control">
                            <option value="free" {{ old('response_type') == 'free' ? 'selected' : '' }}>Free Entry</option>
                            <option value="predefined" {{ old('response_type') == 'predefined' ? 'selected' : '' }}>Predefined Choices</option>
                        </select>
                        @if ($errors->has('response_type'))
                        <span class="help-block">{{ $errors->first('response_type') }}</span>
                        @endif
                    </div>

                    {{-- Predefined Options --}}
                    <div class="form-group {{ $errors->has('response_options') ? 'has-error' : '' }}">
                        <label for="response_options">Predefined Options <small>(comma-separated)</small></label>
                        <input type="text" name="response_options" id="response_options" class="form-control" value="{{ old('response_options') }}">
                        @if ($errors->has('response_options'))
                        <span class="help-block">{{ $errors->first('response_options') }}</span>
                        @endif
                    </div>


                    <button type="submit" class="btn btn-success">Send Message</button>
                </form>
            </div>
        </div>

        {{-- Previous messages --}}
        <div class="row">
            <div class="col-md-12">
                <h4>Previous Messages</h4>
                <div class="row">
                    @forelse($previous as $message)
                    <div class="col-sm-6 col-md-4">
                        <a href="/send_message/{{ $message->id }}" target="_blank" style="text-decoration: none; color: inherit;">
                            <div class="panel panel-default hoverable-card">
                                <div class="panel-heading">
                                    <span class="status-dot {{ $message->status }}"></span>
                                    <strong>{{ $message->subject }}</strong>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Groups:</strong>
                                        {{ $message->groups->map(function($mg) {
        return optional($mg->group)->description;
    })->filter()->implode(', ') }}
                                    </p>
                                    <p class="message-content">{{ \Illuminate\Support\Str::limit($message->content, 200) }}</p>
                                    <p class="text-right text-muted"><small>{{ $message->created_at->format('Y-m-d H:i') }}</small></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @empty
                    <p class="text-muted">No messages sent yet.</p>
                    @endforelse
                </div>

                <div class="text-center">
                    {{ $previous->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".standardSelect").chosen({
            disable_search_threshold: 3,
            no_results_text: "Oops, no group found!",
            width: "100%"
        });
    });
</script>
@endsection