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

/* ---- PAGE LAYOUT ---- */
body {
  background: var(--bg);
  color: var(--text);
  transition: background 0.3s, color 0.3s;
}

.chat-wrapper {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 40px 0;
}

.chat-container {
  display: grid;
  grid-template-columns: 280px 540px;
  gap: 20px;
  max-width: 900px;
  width: 100%;
}

/* ---- SIDEBAR ---- */
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

.chat-sidebar .list-group {
  max-height: 500px;
  overflow-y: auto;
}

.chat-sidebar .list-group-item {
  border: none;
  border-bottom: 1px solid var(--border);
  transition: background 0.3s;
}

.chat-sidebar .list-group-item:hover {
  background: rgba(0, 179, 255, 0.05);
}

/* ---- CHAT BOX ---- */
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

/* HEADER */
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
  word-wrap: break-word;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  animation: fadeIn 0.25s ease-in-out;
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

[data-theme="dark"] .msg-received {
  background: #2a3245;
  color: #e4e8f0;
}

/* FOOTER */
.chat-footer {
  background: var(--card-bg);
  padding: 12px;
  border-top: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 10px;
}

.chat-footer input {
  flex-grow: 1;
  border-radius: 20px;
  border: 1px solid var(--border);
  padding: 10px 15px;
  background: var(--bg);
  color: var(--text);
  outline: none;
  transition: border-color 0.3s;
}

.chat-footer input:focus {
  border-color: var(--accent);
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
  transition: all 0.3s;
}

.chat-footer button:hover {
  background: var(--primary);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="chat-wrapper">
  <div class="chat-container">
    <!-- SIDEBAR -->
    <div class="chat-sidebar">
      <div class="header">Messages</div>
      <div class="list-group list-group-flush" id="chatList">
        <div class="text-center text-muted py-4">No messages yet</div>
      </div>
    </div>

    <!-- CHAT BOX -->
    <div class="chat-box">
      <div class="chat-header">
        <div class="user-info">
          <img id="chatAvatar" src="https://ui-avatars.com/api/?name=User&background=00b3ff&color=fff">
          <div>
            <h6 id="chatName" class="mb-0 fw-semibold">Select a chat</h6>
            <small id="chatStatus" class="opacity-75">Offline</small>
          </div>
        </div>
        <i class="bi bi-three-dots-vertical"></i>
      </div>

      <div class="chat-body" id="chatContent">
        <div class="text-center text-muted py-5">Select a user to start chatting</div>
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let activeJob = null, activeReceiver = null;

// Load chat list
function loadChatList() {
  $.get('/messages/list', function(data) {
    const list = $('#chatList');
    list.empty();
    if (data.length === 0) {
      list.append('<div class="text-center text-muted py-4">No messages yet</div>');
      return;
    }

    data.forEach(chat => {
      list.append(`
        <a href="#" class="list-group-item list-group-item-action openChat"
           data-job="${chat.job_id}"
           data-receiver="${chat.receiver_id}"
           data-name="${chat.name}"
           data-avatar="${chat.avatar}">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
              <img src="${chat.avatar}" class="rounded-circle" width="32" height="32">
              <span>${chat.name}</span>
            </div>
            ${chat.unread > 0 ? `<span class="badge bg-danger">${chat.unread}</span>` : ''}
          </div>
        </a>
      `);
    });
  });
}

// Load messages
$(document).on('click', '.openChat', function(e){
  e.preventDefault();
  activeJob = $(this).data('job');
  activeReceiver = $(this).data('receiver');
  $('#chatName').text($(this).data('name'));
  $('#chatAvatar').attr('src', $(this).data('avatar'));
  $('#chatStatus').text('online 🟢');

  $.get(`/chat/fetch/${activeJob}/${activeReceiver}`, function(data){
    const authId = @json(Auth::id());
    let html = '';
    data.forEach(msg => {
      const isSent = msg.sender_id === authId;
      html += `
        <div class="d-flex ${isSent ? 'justify-content-end' : 'justify-content-start'}">
          <div class="msg-bubble ${isSent ? 'msg-sent' : 'msg-received'}">${msg.message}</div>
        </div>`;
    });
    $('#chatContent').html(html).scrollTop($('#chatContent')[0].scrollHeight);
  });
});

// Send message
$('#chatForm').on('submit', function(e){
  e.preventDefault();
  const message = $('#chatMessage').val();
  if (!activeJob || !activeReceiver) return;
  $.post('/chat/send', {
    _token: '{{ csrf_token() }}',
    job_id: activeJob,
    receiver_id: activeReceiver,
    message: message
  }, function(){
    $('#chatMessage').val('');
    $('.openChat[data-job="'+activeJob+'"]').click(); // reload chat
    loadChatList(); // refresh badge counts
  });
});

// Refresh every 5 seconds
setInterval(() => {
  loadChatList();
  if (activeJob && activeReceiver) $('.openChat[data-job="'+activeJob+'"]').click();
}, 50000);

loadChatList();
$(document).on('click', '.openChat', function(e){
  e.preventDefault();
  const jobId = $(this).data('job');
  const receiverId = $(this).data('receiver');

  $.post(`/chat/read/${jobId}/${receiverId}`, {_token: '{{ csrf_token() }}'});

  // (then fetch messages as usual)
});

</script>
@endsection
