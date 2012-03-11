<?
	require_once('db_connect.php');
?>
<html>
<head><title>DeviceRUs | USERNAME</title></head>
<body>
  <div class="header_links">
    Accounts&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="devices.php">Devices</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="admin.php">Manager</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="logout.php">Logout</a>
  </div>
  <br />
  <h2>Welcome USERNAME</h2>
  <div class="accounts" style="text-align:center; width:150px; float:left; padding-right:20px;">
    <ul class="account_list" style="list-style:none; text-align:center;">
      <li><h3>Accounts</h3></li>
      <a href="#"><li>4012</li></a>
      <li>4013</li>
      <a href="#"><li>8739</li></a>
      <a href="#"><li>9032</li></a>
      <a href="#"><li>8342</li></a>
    </ul>
  </div>
  <div class="functionality" style="float:left; padding-left:20px; border-left:2px solid #000000; width: 80%;">
    <div class="balance">
      <h3 style="margin-bottom:5px;">Current Balance: $1002.00</h3>
      <form action="#add_balance" method="post">
        <table><tr>
          <td><label for="amount">Add to Balance: </label></td>
          <td><input type="text" name="amount" placeholder="$12345.00" /></td>
          <td><input type="submit" value="Add" /></td>
        </tr></table>
      </form>
    </div>
    <div class="account_owners" style="width:auto; float:left;">
      <ul class="account_owners_list" style="list-style:none; text-align:center;">
        <li><h3 style="text-decoration:underline;">Account Owners</h3></li>
        <li>james</li>
        <li>peter</li>
        <li>mike</li>
        <li>luke</li>
        <li>admin</li>
      </ul>
    </div>
    <div class="account_proxies" style="width:auto; float:left;">
      <ul class="account_proxies_list" style="list-style:none; text-align:center;">
        <li><h3 style="text-decoration:underline;">Account Proxies</h3></li>
        <li>jake</li>
        <li>allan</li>
        <li>david</li>
        <li>vincent</li>
      </ul>
    </div>
    <!-- CAN ONLY SEE THIS IF CURRENT USER IS AN OWNER FOR CURRENT ACCOUNT -->
    <div class="add_remove_users" style="width:auto; float:left; margin-left:50px;">
      <h3 style="text-decoration:underline;">Add/Remove Users</h3>
      <form action="#add_remove_users" method="post">
        <table>
          <tr><td>
            <label for="uname">Username: </label></td><td><input type="text" name="uname" placeholder="Username" />
          </td></tr>
          <tr><td colspan=2 style="text-align:center;">
            <select name="add_remove_from">
              <option value="0">------</option>
              <option value="add proxy">add to proxies</option>
              <option value="add owners">add to owners</option>
              <option value="remove proxy">remove from proxies</option>
              <option value="remove owners">remove from owners</option>
            </select>
          </td></tr>
          <tr><td colspan=2 style="text-align:center;">
            <input type="submit" value="add/remove" />
          </td></tr>
        </table>
      </form>
    </div>
  </div>
</body>
</html>