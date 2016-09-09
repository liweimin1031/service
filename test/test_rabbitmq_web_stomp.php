<?php 
require_once(dirname(__FILE__) . '/../inc.php');

global $LAS_CFG;
$username = $LAS_CFG->message_server['username'];
$password = $LAS_CFG->message_server['password'];
$mq_url = $LAS_CFG->message_server['webstomp'];
$vhost = $LAS_CFG->message_server['vhost'];

$correlation_id = $_REQUEST['correlation_id'];
$input_queue = $_REQUEST['input_queue'];
$output_queue = $_REQUEST['output_queue'];
$message = $_REQUEST['message'];
?>
<html>
<head>
	<title>Message Server Connector</title>
	<script type="text/javascript" src="../../../../jslib/jquery-1.11.3.js"></script>
	<script type="text/javascript" src="../../../../jslib/sockjs-0.3.js"></script>
	<script type="text/javascript" src="../../../../jslib/stomp.js"></script>

	<script type="text/javascript">
		var username       = "<?php echo $username; ?>",
			password       = "<?php echo $password; ?>",
			vhost          = "<?php echo $vhost; ?>",
			mq_url            = "<?php echo $mq_url; ?>",
			correlation_id = "<?php echo $correlation_id; ?>",
			input_queue    = "/exchange/elana/elana.quiz",
			output_queue   = "/temp-queue/<?php echo $output_queue; ?>",
			message        = <?php echo $message; ?>;
			
		var webSocket = new SockJS(mq_url);
		var client = Stomp.over(webSocket);
	
		client.heartbeat.outgoing = 0;
	    client.heartbeat.incoming = 0;
		
		client.debug = function(message) {
			console.log(message);
			if (message.indexOf("ERROR") !== -1){
				//throw new Error(message);
			}
		};
			
		client.onreceive = function(message) {
			console.log(message.body);
		}
		
		var connectCallback = function(frame) {
			console.log(frame);
			
			client.send(
				input_queue,
				{
					"reply-to": output_queue, 
					"correlation-id": correlation_id
				}, 
				message
			);
		};
			
		var errorCallback =  function(frame) {
			console.log(frame);
			client.disconnect();
		};
		
		client.connect(username, password, connectCallback, errorCallback, vhost);
	</script>
</head>
<body>	
</body>
</html>
