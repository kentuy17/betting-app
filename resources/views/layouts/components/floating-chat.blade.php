<div class="floating-chat enter">
  <span id="badge-chat" style="display: none;z-index: 1000;position: absolute;top: -5px;right: -5px;font-size: 14px;" class="badge bg-danger">1</span>
  <i class="fa fa-comments text-xl" aria-hidden="true"></i>
  <div class="chat">
    <div class="header">
      <span class="title">
        Bicolana Agent
      </span>
      <button>
        <i class="fa fa-times" aria-hidden="true"></i>
      </button>

    </div>
    <ul class="messages">
      <li class="other">👋 Hi! How may i help you today?</li>
      <li class="other other-typing" style="display: none;"><i>typing...</i></li>
    </ul>
    <div class="footer">
      <div class="text-box" id="chat-box" contenteditable="true" disabled="true"></div>
      <button id="sendMessage">send</button>
    </div>
  </div>
</div>
