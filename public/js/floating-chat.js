$(function () {
  var element = $('.floating-chat');
  var myStorage = localStorage;
  var visited = false;
  var autoReply = 'Thank you for messaging us! We are currently processing your request.';

  if (!myStorage.getItem('chatID')) {
    myStorage.setItem('chatID', createUUID());
  }

  element.click(openElement);

  $('#chat-box').on('keypress',function(e) {
    let msg = $('#chat-box').text();
    if(e.which == 13) {
      e.preventDefault();
      if(msg == '') return;
      sendNewMessage();
      return;
    }
  });

  getUserMsg().then((msg) => {
    if(!msg) return 0;
    let unseen = 0;
    for (let i = 0; i < msg.data.length; i++) {
      const chat = msg.data[i];
      if(!chat.seen && chat.sender == 'other' && chat.message != autoReply) {
        unseen++;
      }
    }
    return unseen;
  }).then(unseen => {
    if(unseen > 0) {
      $('.floating-chat.enter').css('opacity','1');
      $('#badge-chat').show().text(unseen);
    } else {
      $('.floating-chat.enter').css('opacity','0.6');
      $('#badge-chat').hide().text(unseen);
    }
  })

  function openElement() {
    var textInput = element.find('.text-box');
    element.find('>i').hide();
    element.addClass('expand');
    element.find('.chat').addClass('enter');
    var strLength = textInput.val().length * 2;
    textInput.keydown(onMetaAndEnter).prop("disabled", false).focus();
    element.off('click', openElement);
    element.find('.header button').click(closeElement);
    element.find('#sendMessage').click(sendNewMessage);
    scrollDown();
    $('.floating-chat.enter').css('opacity','1');
    $('#badge-chat').hide();
    getUserMsg()
      .then(userMsg => {
        if(userMsg.data.length == 0 || visited) return;
        var typing = $('.other-typing');
        var reply = '<li class="other">Thank you for messaging us! We are currently processing your request.</li>';
        var msg = '';
        for (let i = 0; i < userMsg.data.length; i++) {
          const element = userMsg.data[i];
          if(i == 0) {
            msg += reply;
          }
          msg += `<li class="${element.sender}">${element.message}</i>`;
        }
        if(userMsg.data.length > 0) {
          seenMessage();
        }
        var msgEl = $(msg);
        typing.before(msgEl);
        scrollDown(0);
        visited = true;
      })
  }

  function closeElement() {
    element.find('.chat').removeClass('enter').hide();
    element.find('>i').show();
    element.removeClass('expand');
    element.find('.header button').off('click', closeElement);
    element.find('#sendMessage').off('click', sendNewMessage);
    element.find('.text-box').off('keydown', onMetaAndEnter).prop("disabled", true).blur();
    $('.floating-chat.enter').css('opacity','0.6');
    setTimeout(function () {
      element.find('.chat').removeClass('enter').show()
      element.click(openElement);
    }, 500);
  }

  function createUUID() {
    var s = [];
    var hexDigits = "0123456789abcdef";
    for (var i = 0; i < 36; i++) {
      s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
    }
    s[14] = "4";
    s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);
    s[8] = s[13] = s[18] = s[23] = "-";

    var uuid = s.join("");
    return uuid;
  }

  function sendNewMessage() {
    var userInput = $('.text-box');
    var newMessage = userInput.html().replace(/\<div\>|\<br.*?\>/ig, '\n').replace(/\<\/div\>/g, '').trim().replace(/\n/g, '<br>');
    if (!newMessage) return;
    sendMsg(newMessage);
    visited = true;

    getUserMsg().then((msg) => {
      var typing = $('.other-typing');
      var reply = $(`<li class="other">${autoReply}</li>`);
      typing.before('<li class="self">'+newMessage+'</li>');
      if(msg.data.length == 0) {
        setTimeout(() => {
          typing.show();
          scrollDown();
          setTimeout(() => {
            typing.hide().before(reply);
              sendMsg(autoReply,'other')
              scrollDown();
          }, 3000);
        }, 3000);
      }
    })

    userInput.html('');
    userInput.focus();
    scrollDown();
  }

  function scrollDown(sex=250) {
    var messagesContainer = $('.messages');
    messagesContainer.finish().animate({
      scrollTop: messagesContainer.prop("scrollHeight")
    }, sex);
  }

  function onMetaAndEnter(event) {
    if ((event.metaKey || event.ctrlKey) && event.keyCode == 13) {
      console.log(event.keyCode);
      sendNewMessage();
    }
  }

  async function getUserMsg() {
    try {
      const response = await fetch('/chat/messages')
      const messages = await response.json()
      return messages
    }
    catch (error) {
      console.log(error);
    }
  }

  async function sendMsg(msg='',sender='self') {
    try {
      const { playerMessage } = axios.post('/chat/send-message', {
        message: msg,
        sender: sender,
      });
    }
    catch (error) {
      console.log(error);
    }
  }

  async function seenMessage() {
    try {
      axios.post('/chat/seen-message',[]);
    }
    catch (error) {
      console.log(error);
    }
  }

  window.Echo.private('user.' + _user_id)
    .listen('Chat', async (e) => {
      console.log(e);
      var audio = new Audio('../music/message-notif.mp3');
      audio.play();
    })
})


