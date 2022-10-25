<div class="card">
    <div class="card-header font-12 font-weight-bold bg-light">
        {{ $chat->display()->name }}
    </div>
    <div class="card-body">
        <div id="chatBox" class="pb-5" style="height: 67vh; overflow: scroll;"> 
            
            {{-- <div class="d-flex mb-2">
                <div class="ml-auto d-flex">
                    <div class="bg-light py-2 px-3">
                        <p class="font-11 font-weight-bold mb-1">RIght Name</p>
                        <p class="font-11">Ini cadalah contoh Message right</p>
                        <p class="font-9 text-muted m-0 text-right">2020-09-09</p>
                    </div>
                </div>
            </div>
            
            <div class="d-flex mb-2">                                    
                <div class="d-flex">
                    <div class="bg-light py-2 px-3">
                        <p class="font-11 font-weight-bold mb-1">Left Name</p>
                        <p class="font-11">Ini adalah contoh Message Left</p>
                        <p class="font-9 text-muted m-0 text-right">2020-09-09</p>
                    </div>
                </div>
            </div> --}}

        </div>

        <div class="py-3 bg-white" style="position: absolute; bottom: 0; width: 100%;">                                    
            <div class="d-flex pr-5">
                <textarea id="txt_message" class="form-control mr-2 mb-1" rows="1" cols="60"></textarea>
                <button id="btn_send" class="btn btn-primary">Send</button>
            </div>
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
            complete: () => { $('#txt_message').val('') },
            success: (res) => {  },
            error: (res) => { }
        });
    })
    

    // listen channel message
    Echo.private(`channel-messages-{{ $chat->id }}`).listen('MessagesEvent', (e) => {
        console.log('channel-message', e);
        showMessages();
    });

    

    // creating list of messages
    showMessages();
        
    // FUNCTIONS LIST
    async function getMessage()
    {
        var response = (await axios.get(`{{ URL::to("/chat/".$chat->id."/messages") }}`)).data;
        return response;
    }

    async function showMessages() {
        var userId = `{{ Auth::id() }}`;
        var msgHtml = "";
        var messages = await getMessage();
        if (messages.data.length > 0)
        {
            messages.data.forEach(msg => {

                var date = new Date(msg.unix_created_at * 1000);

                
                if(msg.sender == userId) {
                    msgHtml += `
                    <div class="d-flex mb-2">
                        <div class="ml-auto d-flex">
                            <div class="bg-light py-2 px-3">
                                <p class="font-11 font-weight-bold mb-1">${msg.sender_data.name}</p>
                                <p class="font-11">${msg.message}</p>
                                <p class="font-9 text-muted m-0 text-right">
                                    ${date.getDate()}/${date.getMonth()+1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}
                                </p>
                            </div>
                        </div>
                    </div>
                    `;
                } else {                        
                    msgHtml += `
                    <div class="d-flex mb-2">                                    
                        <div class="d-flex">
                            <div class="bg-light py-2 px-3">
                                <p class="font-11 font-weight-bold mb-1">${msg.sender_data.name}</p>
                                <p class="font-11">${msg.message}</p>
                                <p class="font-9 text-muted m-0 text-right">
                                    ${date.getDate()}/${date.getMonth()+1}/${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}
                                </p>
                            </div>
                        </div>
                    </div>
                    `;
                }
                
            });
        }

        $("#chatBox").html(msgHtml);
        $("#chatBox").scrollTop($("chatBox").height());
    }
</script>
@endpush