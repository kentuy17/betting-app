// import WebSocket from 'ws';

const ws = new WebSocket('wss://sww23-go.live:56112');

ws.onerror = function (err) {
  console.log('error: %s', JSON.stringify(err));
}

// Connection opened
ws.addEventListener("open", (event) => {
  ws.send("Hello Server!");
});

ws.addEventListener('close', () => {
  ws.send("Goodbye!");
})

// Listen for messages
ws.addEventListener("message", (event) => {
  // console.log("Message from server ", event.data);
  const message1 = event.data.stream();
  const message2 = event.data.text()
  console.log(message1);
  console.log(message2);
  console.log(`user sended:${event.data}`)
});
