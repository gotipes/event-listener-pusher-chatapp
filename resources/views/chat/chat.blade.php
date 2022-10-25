@extends('layouts.app')
@section('content')

@php
    $currentChatId = explode( '/', \Request::getRequestUri() )[2] ?? null;
@endphp

<div class="container">
    <div class="card">
        <div class="card-body row">
            <div class="col-md-3 mb-3">
                <div class="btn btn-primary btn-sm btn-block mb-3" data-toggle="modal" data-target="#modal-newchat">New Chat</div>

                <div class="modal fade" id="modal-newchat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form id="form-newchat" method="post" action="{{ url('chat') }}" class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-12">Create New Chat</h5>
                                <button type="button" class="close font-14" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        {{-- @csrf --}}
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="email" autocomplete="off" >
                                        @error('email') 
                                            <span class="invalid-feedback font-11">
                                                {{ $message }} 
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm btn-block px-4">Start Chat</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="chat-lists" class="list-group font-12 font-weight-bold overflow-y">
                    @foreach ($chats as $c)                        
                        <a href="{{ url('chat/'.$c->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center @if($currentChatId == $c->id) active @endif">
                            {{ $c->display()->name }}
                            @if(! $c->is_group ) 
                                @if($c->display()->status == 'online')
                                    <span id="presence-{{ $c->display()->id }}" class="badge badge-success badge-pill">online</span>
                                @else 
                                    <span id="presence-{{ $c->display()->id }}" class="badge badge-secondary badge-pill">offline</span>
                                @endif
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-12">
                        @isset($chat)
                            @include('chat.messages')
                        @endisset

                        @empty($chat)
                            <div class="card-body" style="height: 67vh; overflow: scroll;">
                            </div>
                        @endempty
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $("#form-newchat").submit((e) => {
        e.preventDefault();
        var values = {};

        $.each($('#form-newchat input').serializeArray(), function(i, field) {
            values[field.name] = field.value;
        });
        
        var data = values;
        data._token = `{{ csrf_token() }}`;
        var url = $("#form-newchat").attr('action');
        
        $.ajax({
            url, data, method: 'post',           
            success: (response) => {
                var { data }= response;
                window.location.href = `/chat/${data.chat_id}`;
            },
            error: (response) => {
                console.log(response);
                var errors = response.responseJSON.errors;
                for( var key in errors) {
                    $(`input[name=${key}]`).next('span').remove();
                    $(`input[name=${key}]`).addClass('is-invalid');
                    $(`<span class="invalid-feedback font-10">${errors[key]}</span> `).insertAfter(`input[name=${key}]`);
                }
            }
        })
    });

    
    // listen channel status
    Echo.join(`status`)
        .joining((member) => {
            // console.log('joining', member);
            $(`#presence-${member.id}`).attr('class', 'badge badge-success badge-pill');
            $(`#presence-${member.id}`).html('online');
            axios.put(`/user/${member.id}/online`);
        })
        .leaving((member) => {
            // console.log('leaving', member);
            $(`#presence-${member.id}`).attr('class', 'badge badge-secondary badge-pill');
            $(`#presence-${member.id}`).html('offline');
            axios.put(`/user/${member.id}/offline`);
        })
        .listen('UserOnline', async (member) => {            
            // console.log('UserOnline', member.user );
            var response = (await axios.get(`{{ URL::to("/chats") }}`)).data;
            var chatsHtml = '';

            response.data?.forEach(chat => {
                // console.log(chat)
                if(chat.user.status == 'online') {                    
                    $(`#presence-${chat.user.id}`).attr('class', 'badge badge-success badge-pill');
                    $(`#presence-${chat.user.id}`).html('online');
                }
                else {      
                    $(`#presence-${chat.user.id}`).attr('class', 'badge badge-secondary badge-pill');
                    $(`#presence-${chat.user.id}`).html('offline');
                }                
            });
        })
</script>
@endpush

@push('css')
    <style>
        .font-9 { font-size: 9px; }
        .font-10 { font-size: 10px; }
        .font-11 { font-size: 11px; }
        .font-12 { font-size: 12px; }
        .font-13 { font-size: 13px; }
        .font-14 { font-size: 14px; }
        .font-weight-bold { font-weight: 700; }

        .list-group-item.active { 
            background: rgb(240 247 255); 
            color: #000; 
            border-color: rgb(231 231 231); 
        }

        input[type=radio], input[type=checkbox] {
            display:none;
        }

        input[type=radio] + label, input[type=checkbox] + label {
            display:inline-block;
            margin:-2px;
            padding: 4px 12px;
            margin-bottom: 0;
            font-size: 14px;
            line-height: 20px;
            color: #333;
            text-align: center;
            text-shadow: 0 1px 1px rgba(255,255,255,0.75);
            vertical-align: middle;
            cursor: pointer;
            background-color: #f5f5f5;
            background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
            background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
            background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
            background-image: -o-linear-gradient(top,#fff,#e6e6e6);
            background-image: linear-gradient(to bottom,#fff,#e6e6e6);
            background-repeat: repeat-x;
            border: 1px solid #ccc;
            border-color: #e6e6e6 #e6e6e6 #bfbfbf;
            border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
            border-bottom-color: #b3b3b3;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
            -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        }

        input[type=radio]:checked + label, input[type=checkbox]:checked + label{
            background-image: none;
            outline: 0;
            -webkit-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
            -moz-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
            background-color:#e0e0e0;
        }
    </style>
@endpush
