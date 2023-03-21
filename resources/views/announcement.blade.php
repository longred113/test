<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('ef7b5bab9016721f8248', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('send-all-announcement');
        channel.bind('send-all-announcement-event', function(data) {
            alert(JSON.stringify(data));
        });
    </script>
</head>

<body>
    <form action="http://localhost:8000/api/admin-image-management/create-image" method="POST">
        <input type="file" name="image">
        <input type="submit" value="Submit">
    </form>
</body>

</html>