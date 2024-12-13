<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title></title>
</head>
<body>
<center>
   <h4>Welcome</h4>
</center>
<script type="text/javascript">
   var socket = new WebSocket('wss://onlinepoll.trymydemo.com:8099?user_type=admin&user_id=1');
   socket.onopen = function(e) {
      console.log("Connection established!");
   };
</script>
</body>
</html>