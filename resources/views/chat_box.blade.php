<!DOCTYPE html>
<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Real-time Chat</title>
    </head>
    <body>
        <h1>Real-time Chat</h1>
        <div>
            <form>
                <label>Username:</label>
                <input type="text" name="username" id="username" required>
                <br><br>
                <label>Message:</label>
                <textarea name="message" id="message" required></textarea>
                <br><br>
                <button type="submit" id="send">Send</button>
            </form>
        </div>
        <div id="chat-box"></div>
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            var pusher = new Pusher('ef7b5bab9016721f8248', {
                cluster: 'ap1',
                encrypted: true
            });

            var channel = pusher.subscribe('chat');
            channel.bind('NewChatMessage', function(data) {
                var message = data.username + ': ' + data.message + ' - ' + data.time;
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(message));
                document.getElementById('chat-box').appendChild(div);
            });

            var form = document.querySelector('form');
            var username = document.getElementById('username');
            var message = document.getElementById('message');

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/chat');
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        username.setAttribute('disabled', 'disabled');
                        message.value = '';
                        message.focus();
                    } else {
                        alert('Something went wrong!');
                    }
                };
                xhr.send(JSON.stringify({
                    username: username.value,
                    message: message.value
                }));
            });
        </script>
    </body>
</html>
