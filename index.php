<?

	require_once('db_connect.php');

?>
<html>
<!-- James Ebentier -->
<!-- Vincent Alindogan -->
<head><title>DeviceRUs</title></head>
<body>
  <h1>Login</h1>
  <form action="../php/login.php" method="post">
    <input type="text" name="username" placeholder="Username" />
    <input type="password" name="password" placeholder="Password" />
    <input type="submit" value="LogIn" />
  </form>
</body>
</html>
<??>