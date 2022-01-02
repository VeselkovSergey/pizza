<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    const pusher = new Pusher("{{env('PUSHER_APP_KEY')}}", {
        cluster: 'eu'
    });

    const channel = pusher.subscribe('location-channel');
    channel.bind('updateLocation', function(data) {
        console.log(data)
    });
</script>

</body>
</html>