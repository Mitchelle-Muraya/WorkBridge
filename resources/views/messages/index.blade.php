@extends('layouts.app')

@section('content')

<style>
:root {
  --primary: #00b3ff;
  --accent: #00c9a7;
  --bg: #f8fafc;
  --card-bg: #ffffff;
  --text: #0f172a;
  --muted: #64748b;
  --border: rgba(0,0,0,0.08);
}

.chat-wrapper {
  display: flex;
  justify-content: center;
  padding: 40px 0;
}

.chat-container {
  display: grid;
  grid-template-columns: 280px 540px;
  gap: 20px;
}

/* sidebar */
.chat-sidebar {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 18px;
  overflow: hidden;
}

.chat-sidebar .header {
  background: linear-gradient(90deg, var(--primary), var(--accent));
  color: #fff;
  padding: 14px;
  font-weight: 600;
}

.chat-sidebar .list-group {
  max-height: 500px;
  overflow-y: auto;
}

/* chat box */
.chat-box {
  background: var(--card-bg);
  border-radius: 18px;
  border: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  height: 580px;
}

.chat-header {
  background: linear-gradient(90deg, var(--primary), var(--accent));
  color: white;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.chat-body {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: var(--bg);
}

.msg-bubble {
  max-width: 70%;
  padding: 12px 14px;
  border-radius: 16px;
  margin-bottom: 10px;
}

.msg-sent {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: white;
  margin-left: auto;
}

.msg-received {
  background: #e8edf3;
  color: black;
}

.chat-footer {
  padding: 12px;
  border-top: 1px solid var(--border);
  display: flex;
  gap: 10px;
}

.chat-footer input {
  flex-grow: 1;
  border-radius: 20px;
  border: 1px solid var(--border);
  padding: 10px 15px;
}

.chat-footer button {
  background: var(--accent);
  border: none;
  color: white;
  border-radius: 50%;
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
}

</style>

<div class="chat-wrapper">
    <div class="chat-container">

        <!-- SIDEBAR -->
        <div class="chat-sidebar">
            <div class="header">Messages</div>
            <div id="chatList" class="list-group list-group-flush">
                <div class="text-center text-muted py-4">Loading...</div>
            </div>
        </div>

        <!-- CHAT BOX -->
        <div class="chat-box">

            <div class="chat-header">
                <div class="d-flex align-items-center gap-2">
                    <img id="chatAvatar" src="https://ui-avatars.com/api/?name=User" width="38" height="38" style="border-radius:50%;">
                    <div>
                        <h6 id="chatName" class="mb-0">Select a chat</h6>
                        <small id="chatStatus">Offline</small>
                    </div>
                </div>
            </div>

            <div id="chatContent" class="chat-body">
                <div class="text-center text-muted py-5">Select a user to start chatting</div>
            </div>

            <div class="chat-footer">
                <form id="chatForm" class="d-flex w-100 gap-2">
                    <input id="chatMessage" type="text" placeholder="Type a message..." required>
                    <button type="submit"><i class="bi bi-send-fill"></i></button>
                </form>
            </div>

        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let activeJob=null, activeReceiver=null;

/* LOAD CHAT LIST */
function loadChatList(){
    $.get("/messages/list", function(list){
        $("#chatList").empty();

        if(list.length==0){
            $("#chatList").html(`<div class="text-center py-3">No messages</div>`);
            return;
        }

        list.forEach(c=>{
           $("#chatList").append(`
    <a href="#" class="list-group-item openChat"
        data-job="${c.job_id}"
        data-receiver="${c.receiver_id}"
        data-name="${c.name}"
        data-avatar="${c.avatar}">

        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <img src="${c.avatar}" width="30" class="rounded-circle">
                ${c.name}
            </div>

            ${c.unread > 0 ? `<span class="badge bg-danger">${c.unread}</span>` : ""}
        </div>
    </a>
`);

        });
    });
}

/* OPEN CHAT */
$(document).on("click",".openChat",function(e){
    e.preventDefault();

    activeJob=$(this).data("job");
    activeReceiver=$(this).data("receiver");

    $("#chatName").text($(this).data("name"));
    $("#chatAvatar").attr("src",$(this).data("avatar"));
    $("#chatStatus").text("online 🟢");

    loadMessages(true);

    $.post(`/chat/read/${activeJob}/${activeReceiver}`, {
        _token: "{{ csrf_token() }}"
    }, ()=>loadChatList());
});

/* LOAD MESSAGES */
function loadMessages(scroll){
    if(!activeJob) return;

    $.get(`/chat/fetch/${activeJob}/${activeReceiver}`, function(msgs){
        let auth={{ Auth::id() }};
        let html="";

        msgs.forEach(m=>{
           html += `
    <div class="d-flex ${m.sender_id==auth ? 'justify-content-end' : 'justify-content-start'}">
        <div class="msg-bubble ${m.sender_id==auth ? 'msg-sent' : 'msg-received'}">
            ${m.message}
        </div>
    </div>
`;

        });

        $("#chatContent").html(html);

        if(scroll) $("#chatContent").scrollTop($("#chatContent")[0].scrollHeight);
    });
}

/* SEND MESSAGE */
$("#chatForm").on("submit", function(e){
    e.preventDefault();

    let message=$("#chatMessage").val();
    if(!message.trim()) return;

    $.post("/chat/send",{
        _token:"{{ csrf_token() }}",
        job_id:activeJob,
        receiver_id:activeReceiver,
        message:message
    },()=>{
        $("#chatMessage").val("");
        loadMessages(true);
        loadChatList();
    });
});

loadChatList();
</script>

@endsection
