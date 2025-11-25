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
  --border: rgba(0, 0, 0, 0.08);
}

[data-theme="dark"] {
  --bg: #0b0f19;
  --card-bg: #1a2233;
  --text: #f8fafc;
  --muted: #cfd6e0;
  --border: rgba(255, 255, 255, 0.1);
}

body {
  background: var(--bg);
  color: var(--text);
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
  max-width: 900px;
  width: 100%;
}

/* SIDEBAR */
.chat-sidebar {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 179, 255, 0.1);
}

.chat-sidebar .header {
  background: linear-gradient(90deg, var(--primary), var(--accent));
  color: #fff;
  padding: 14px 18px;
  font-weight: 600;
}

/* CHAT BOX */
.chat-box {
  background: var(--card-bg);
  border-radius: 18px;
  border: 1px solid var(--border);
  box-shadow: 0 4px 18px rgba(0, 179, 255, 0.08);
  display: flex;
  flex-direction: column;
  height: 580px;
  overflow: hidden;
}

.chat-header {
  background: linear-gradient(90deg, var(--primary), var(--accent));
  color: #fff;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.chat-header .user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.chat-header img {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 2px solid #fff;
}

/* BODY */
.chat-body {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: var(--bg);
}

.msg-bubble {
  max-width: 70%;
  padding: 10px 14px;
  border-radius: 16px;
  margin-bottom: 10px;
}

.msg-sent {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: #fff;
  margin-left: auto;
  border-bottom-right-radius: 5px;
}

.msg-received {
  background: #e8edf3;
  color: #0f172a;
  border-bottom-left-radius: 5px;
}

/* FOOTER */
.chat-footer {
  background: var(--card-bg);
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
  color: #fff;
  border-radius: 50%;
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

@php
    $autoOpen = $autoOpen ?? false;
    $jobId = $jobId ?? null;
    $receiverId = $receiverId ?? null;
    $receiverName = $receiverName ?? "Select a chat";
    $receiverAvatar = $receiverAvatar ?? "https://ui-avatars.com/api/?name=User&background=00b3ff&color=fff";
@endphp

<div class="chat-wrapper">
  <div class="chat-container">

    <!-- SIDEBAR -->
    <div class="chat-sidebar">
      <div class="header">Messages</div>
      <div class="list-group list-group-flush" id="chatList">
        <div class="text-center text-muted py-4">Loading...</div>
      </div>
    </div>

    <!-- CHAT BOX -->
    <div class="chat-box">

      <div class="chat-header">
        <div class="user-info">
          <img id="chatAvatar" src="{{ $receiverAvatar }}">
          <div>
            <h6 id="chatName" class="mb-0 fw-semibold">{{ $receiverName }}</h6>
            <small id="chatStatus" class="opacity-75">{{ $autoOpen ? 'online 🟢' : 'Offline' }}</small>
          </div>
        </div>
      </div>

      <div class="chat-body" id="chatContent">
        @if(!$autoOpen)
          <div class="text-center text-muted py-5">Select a user to start chatting</div>
        @endif
      </div>

      <div class="chat-footer">
        <form id="chatForm" class="d-flex w-100 align-items-center gap-2">
          <input type="text" id="chatMessage" placeholder="Type a message..." required>
          <button type="submit"><i class="bi bi-send-fill"></i></button>
        </form>
      </div>

    </div>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

let activeJob = {{ $jobId ? $jobId : 'null' }};
let activeReceiver = {{ $receiverId ? $receiverId : 'null' }};
let autoOpen = {{ $autoOpen ? 'true' : 'false' }};

/* LOAD CHAT LIST */
function loadChatList() {
    $.get("/messages/list", function(list){
        $("#chatList").empty();

        if(list.length == 0){
            $("#chatList").html(`<div class="text-center text-muted py-4">No messages yet</div>`);
            return;
        }

        list.forEach(c => {
            $("#chatList").append(`
                <a href="#" class="list-group-item list-group-item-action openChat"
                   data-job="${c.job_id}"
                   data-receiver="${c.receiver_id}"
                   data-name="${c.name}"
                   data-avatar="${c.avatar}">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                      <img src="${c.avatar}" width="32" class="rounded-circle">
                      <span>${c.name}</span>
                    </div>
                    ${c.unread > 0 ? `<span class="badge bg-danger">${c.unread}</span>` : ''}
                  </div>
                </a>
            `);
        });

        if(autoOpen && activeJob && activeReceiver){
            setTimeout(() => {
                $(`.openChat[data-job='${activeJob}'][data-receiver='${activeReceiver}']`).click();
            }, 300);
        }
    });
}

/* OPEN CHAT */
$(document).on("click", ".openChat", function(e){
    e.preventDefault();

    activeJob = $(this).data("job");
    activeReceiver = $(this).data("receiver");

    $("#chatName").text($(this).data("name"));
    $("#chatAvatar").attr("src", $(this).data("avatar"));
    $("#chatStatus").text("online 🟢");

    loadMessages(true);

    $.post(`/chat/read/${activeJob}/${activeReceiver}`, {
        _token: "{{ csrf_token() }}"
    }, () => loadChatList());
});

/* LOAD MESSAGES */
function loadMessages(scroll){
    if(!activeJob) return;

    $.get(`/chat/fetch/${activeJob}/${activeReceiver}`, function(msgs){
        let auth = {{ Auth::id() }};
        let html = "";

        msgs.forEach(m => {
            html += `
                <div class="d-flex ${m.sender_id == auth ? 'justify-content-end' : 'justify-content-start'}">
                    <div class="msg-bubble ${m.sender_id == auth ? 'msg-sent' : 'msg-received'}">${m.message}</div>
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

    let message = $("#chatMessage").val();
    if(!activeJob || !activeReceiver) return;

    $.post("/chat/send", {
        _token: "{{ csrf_token() }}",
        job_id: activeJob,
        receiver_id: activeReceiver,
        message: message
    }, () => {
        $("#chatMessage").val("");
        loadMessages(true);
        loadChatList();
    });
});

/* AUTO REFRESH */
setInterval(() => {
    loadChatList();
    if(activeJob && activeReceiver) loadMessages(false);
}, 5000);

/* INITIAL LOAD */
loadChatList();
if(autoOpen) loadMessages(true);

</script>

@endsection
