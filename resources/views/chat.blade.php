<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    // var pusher = new Pusher('ef7b5bab9016721f8248', {
    //   cluster: 'ap1'
    // });

    // var channel = pusher.subscribe('announcements');
    // channel.bind('announcements-event', function(data) {
    //   alert(JSON.stringify(data));
    // });
    import Echo from 'laravel-echo';

    window.Pusher = require('pusher-js');

    window.Echo = new Echo({
      broadcaster: 'pusher',
      key: process.env.MIX_PUSHER_APP_KEY,
      cluster: process.env.MIX_PUSHER_APP_CLUSTER,
      encrypted: true
    });

    window.Echo.channel('announcement')
      .listen('.FunctionAnnounced', (data) => {
        // Handle the announcement data
        console.log(data.title, data.body);
      });
  </script>
</head>
<body>
  <h1>Pusher Test</h1>
  <p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
  </p>
</body>
