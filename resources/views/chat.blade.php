<!DOCTYPE html>
<html>
    <head>
        <title>World Chat</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body>
        <div id="app">
            <chat-room></chat-room>
        </div>
    </body>
</html>