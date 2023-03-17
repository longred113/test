<!DOCTYPE html>
<html>
<head>
    <title>GeeksforGeeks</title>
    <style>
        div {
            font-size: 22px;
        }
    </style>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('ef7b5bab9016721f8248', {
      cluster: 'ap1'
    });

    var count = 0;
    var channel = pusher.subscribe('messages-staging');
    channel.bind('my-event', function(data) {
      count++;
      alert(JSON.stringify(data));
      alert(JSON.stringify(count));
      console.log("Number of data sent: " + count);
    });
    var channel = pusher.subscribe('send-teacher-announcement');
    channel.bind('send-teacher-announcement-event', function(data) {
      alert(JSON.stringify(data));
    });
    var channel = pusher.subscribe('send-student-announcement');
    channel.bind('send-student-announcement-event', function(data) {
      alert(JSON.stringify(data));
    });
    </script>
</head>
<body>
    <div>
        <?php
            if(DB::connection()->getPdo())
            {
                echo "Successfully connected to the database => "
                             .DB::connection()->getDatabaseName();
            }
            phpinfo();
        ?>
    </div>
</body>
</html>