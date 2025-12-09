<?php 
require_once 'config.php';
?>
<html>

<head>
<title>Brighton Ski Patrol</title>
</head>
<frameset rows="45,*">
  <frame name="header" scrolling="no" noresize target="main" src="login_header.php?ID=<?php echo $ID; ?>">
  <frame name="main" src="login_assignment.php?ID=<?php echo $ID; ?>">
  <noframes>
  <body>

  <p>This page uses frames, but your browser doesn't support them.</p>

  </body>
  </noframes>
</frameset>

</html>
