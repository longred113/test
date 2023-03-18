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

        var count = 0;
        var channel = pusher.subscribe('all-channel');
        channel.bind('all-channel-event', function(data) {
            count++;
            alert(JSON.stringify(data));
            console.log("Event received: " + JSON.stringify(data));
            console.log("Number of data sent: " + count);
        });
    </script>
</head>

<body>
    <form action="" method="POST">
        <input type="text" name="name">
        <input type="submit" value="Submit">
    </form>
</body>

</html>