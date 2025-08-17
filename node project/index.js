const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const { createClient } = require('redis');
const { join } = require('node:path');

const app = express();
const server = http.createServer(app);
const io = new Server(server);

let lastMessage = null;

process.on('uncaughtException', (err) => {
  console.error('Uncaught Exception:', err);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Unhandled Rejection at:', promise, 'reason:', reason);
});

const subscriber = createClient();
const publisher = createClient();

subscriber.on('error', (err) => {
  console.error('Redis subscriber error:', err);
});

publisher.on('error', (err) => {
  console.error('Redis publisher error:', err);
});

subscriber.connect();
publisher.connect();

subscriber.on('connect', () => {
  console.log('Redis subscriber connected');
});

publisher.on('connect', () => {
  console.log('Redis publisher connected');
});

app.get('/', (req, res) => {
  res.sendFile(join(__dirname, 'index.html'));
});

subscriber.subscribe('laravel-events', (message) => {
  
  
  try {
    const parsedMessage = JSON.parse(message);
    if (parsedMessage.user_id === 'NODEJS') {
      if (parsedMessage.fromBrowser) {
        const messageText = parsedMessage.message;
        console.log( `[NODEJS] ${messageText}`);
        return;
      }
            return;
    }
    
    let displayMessage = '';
    
    if (parsedMessage && parsedMessage.data && parsedMessage.data.message) {
      const sender = parsedMessage.data.sender || 'Unknown';
      const messageText = parsedMessage.data.message;
      displayMessage = `[${sender}] ${messageText}`;
    } 
    else if (parsedMessage && parsedMessage.message) {
      const sender = 'LARAVEL'; 
      const messageText = parsedMessage.message;
      const messageKey = `${parsedMessage.user_id}-${messageText}-${parsedMessage.timestamp}`;
      if (lastMessage === messageKey) {
        return;
      }
      lastMessage = messageKey;
      
      displayMessage = `[${sender}] ${messageText}`;
    } 

    
    console.log( displayMessage);
    io.emit('chat message', displayMessage);
    
  } catch (error) {
    console.error('Error parsing message:', error);
    console.log('Message is not valid JSON, treating as plain text');
    io.emit('chat message', message);
  }
});

io.on('connection', (socket) => {
  console.log('a user connected');
  socket.on('chat message', async (msg) => {
    await publisher.publish('laravel-events', JSON.stringify({
      event: 'chat',
      source: 'NODEJS',
      message: msg,
      user_id: 'NODEJS', 
      timestamp: new Date().toISOString(),
      fromBrowser: true  
    }));
  });
});

server.listen(3000, () => {
  console.log('listening on *:3000');
});
