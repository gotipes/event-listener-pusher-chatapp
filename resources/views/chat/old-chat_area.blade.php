<div class="col-xl-8 col-lg-8 col-md-8 col-sm-9 col-9">
    <div class="selected-user">
        <span class="name">{{$chat->name }}</span>
    </div>
    <div class="chat-container">
        <ul id="chatBox" class="chat-box chatContainerScroll font-12" style="height: 70vh; overflow: scroll;">

            {{-- @foreach ($chat->messages as $msg)
                
            <li class="chat-left">
                <div class="chat-avatar">
                    <img src="https://www.bootdey.com/img/Content/avatar/avatar3.png" alt="Retail Admin">
                    <div class="chat-name">{{ $msg->senderData->name }}</div>
                </div>
                <div class="chat-text">{{ $msg->message }}</div>
                <div class="chat-hour">{{ $msg->created_at }}<span class="fa fa-check-circle"></span></div>
            </li>
            @endforeach --}}

        </ul>
        <div class="form-group d-flex mt-3 mb-0">
            <textarea id="txt_message" class="form-control" rows="1" placeholder="Type your message here..."></textarea>
            <button id="btn_send" class="btn btn-primary">Send</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $("#btn_send").click(() => {
            $.ajax({
                method: 'post',
                url: `<?= url("chat/add-message");?>`,
                data: {
                    _token: `{{ csrf_token() }}`,
                    chat_id: `{{ $chat->id }}`,
                    message: $('#txt_message').val()
                },
                success: (res) => {
                    // console.log(res)
                },
                error: (res) => {
                    // console.log(res)
                }
            });
        })

        // var pusher = new Pusher('5fa0dc5a8a5c516c9bc4', {
        //     cluster: 'ap1'
        // });

        // Echo.channel(`channel-messages`).listen('MessagesEvent', (e) => {
        //     console.log(e);
        //     showMessages();
        // });

        Echo.private(`channel-messages-{{ $chat->id }}`).listen('MessagesEvent', (e) => {
            console.log(e);
            showMessages();
        });


        showMessages();
        
        // FUNCTIONS
        async function getMessage()
        {
            var response = (await axios.get(`{{ URL::to("/chat/".$chat->id."/messages") }}`)).data;
            return response;
        }

        async function showMessages() {
            var userId = `{{ Auth::id() }}`;
            var msgHtml = "";
            var messages = await getMessage();
            // console.log(messages)
            if (messages.data.length > 0)
            {
                messages.data.forEach(msg => {
                    
                    if(msg.sender == userId) {
                        msgHtml += `
                        <li class="chat-right">
                            <div class="chat-text">
                                ${msg.message}
                                <p class="font-10 text-muted mb-0 pb-0 mt-3 text-right">${msg.created_at}</p>
                            </div>
                        </li>
                        `;
                    } else {                        
                        msgHtml += `
                        <li class="chat-left">
                            <div class="chat-avatar">
                                <img src="https://www.bootdey.com/img/Content/avatar/avatar3.png" alt="Retail Admin">
                                <div class="chat-name">${msg.sender_data.name}</div>
                            </div>
                            <div class="chat-text">
                                ${msg.message}
                                <p class="font-10 text-muted mb-0 pb-0 mt-3 text-right">${msg.created_at}</p>
                            </div>
                        </li>
                        `;
                    }
                    
                });
            }

            $("#chatBox").html(msgHtml);
            $("#chatBox").scrollTop($("chatBox").height());
        }
    </script>
@endpush