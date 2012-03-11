<?

	require_once('db_connect.php');

?>
<html>
<!-- James Ebentier -->
<!-- Vincent Alindogan -->
<head><title>DeviceRUs</title></head>
<body>
  <h1>Welcome to DevicesRUs</h1>
  <div class="index_container" style="width=500px;">
    <div class="login" style="float:left; padding-right:20px;">
      <h2>Login</h2>
      <form action="#login" method="post">
        <table>
          <tr><td style="text-align:right">
            <label for="username">Username: </label></td><td><input type="text" name="username" placeholder="jdoe" />
          </td></tr>
          <tr><td style="text-align:right">
            <label for="password">Password: </label></td><td><input type="password" name="password" placeholder="Password" />
          </td></tr>
          <tr><td colspan=2 style="text-align:center; font-style:italic;">or</td></tr>
          <tr><td style="text-align:right">
            <label for="username">Pin: </label></td><td><input type="text" name="pin" placeholder="0000" />
          </td></tr>
          <tr><td colspan=2 style="text-align:right;"><input type="submit" value="Login" /></td></tr>
        </table>
      </form>
    </div>
    <div class="register" style="display:inline; float:left; padding-left:20px; border-left:2px solid #000000;">
      <h2>Register</h2>
      <form action="#register" method="post">
        <table>
          <tr><td style="text-align:right">
            <label for="username">Username: </label></td><td><input type="text" name="username" placeholder="jdoe" style="width:250px" />
          </td></tr>
          <tr><td style="text-align:right">
            <label for="full_name">Full Name: </label></td><td><input type="text" name="full_name" placeholder="John Doe" style="width:250px" />
          </td></tr>
          <tr><td style="text-align:right">
            <label for="phone">Phone #: </label></td><td><input type="text" name="phone" placeholder="8055555555" style="width:250px" />
          </td></tr>
          <tr><td style="text-align:right">
            <label for="email">Email: </label></td><td><input type="text" name="email" placeholder="john.doe@online_email.com" style="width:250px" />
          </td></tr>
          <tr><td style="text-align:right">
            <label for="password">Password: </label></td><td><input type="password" name="password" placeholder="Password" style="width:250px" />
          </td></tr>
          <tr><td style="text-align:right">
            <input type="checkbox" name="is_manager" value="1" /></td><td><label for="is_manager">Manager Account</label>
          </td></tr>
          <tr><td colspan=2 style="text-align:right;"><input type="button" value="Cancel" /><input type="submit" value="Register" /></td></tr>
        </table>
      </form>
    </div>
  </div>
</body>
</html>
<??>