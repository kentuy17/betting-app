$(function () {
  var element = $('.floating-chat');
  var myStorage = localStorage;
  var visited = false;

  if (!myStorage.getItem('chatID')) {
    myStorage.setItem('chatID', createUUID());
  }

  // setTimeout(function () {
  //   element.addClass('enter');
  // }, 1000);

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
    getUserMsg().then(userMsg => {
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
    setTimeout(function () {
      element.find('.chat').removeClass('enter').show()
      element.click(openElement);
    }, 500);
  }

  function createUUID() {
    // http://www.ietf.org/rfc/rfc4122.txt
    var s = [];
    var hexDigits = "0123456789abcdef";
    for (var i = 0; i < 36; i++) {
      s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
    }
    s[14] = "4"; // bits 12-15 of the time_hi_and_version field to 0010
    s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1); // bits 6-7 of the clock_seq_hi_and_reserved to 01
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
    var typing = $('.other-typing');
    var autoReply = 'Thank you for messaging us! We are currently processing your request.';
    var reply = $(`<li class="other">${autoReply}</li>`);
    typing.before('<li class="self">'+newMessage+'</li>');
    setTimeout(() => {
      typing.show();
      scrollDown();
      setTimeout(() => {
        typing.hide().before(reply);
        sendMsg(autoReply,'other')
        scrollDown();
      }, 3000);
    }, 3000);
    userInput.html(''); // clean out old message
    userInput.focus(); // focus on input
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
      // console.log(playerMessage);
    }
    catch (error) {
      console.log(error);
    }
  }
})
