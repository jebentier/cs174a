<?
	require_once('db_connect.php');
?>
<html>
<head><title>DeviceRUs | USERNAME</title></head>
<body>
  <div class="header_links">
    <a href="accounts.html">Accounts</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Devices&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="admin.html">Manager</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="logout.php">Logout</a>
  </div>
  <br />
  <div class="search_devices" style="text-align:center; width:250px; float:left; padding-right:20px;">
    <h3>Search Devices</h3>
    <form action="#search" method="post">
      <table>
        <tr>
          <td style="text-align:right"><label for="dtype">Type: </label></td>
          <td><select name="dtype">
            <option value="any">Any</option>
            <option value="Computer">Computer</option>
            <option value="Printer">Printer</option>
            <option value="Projector">Projector</option>
          </select></td>
        </tr>
        <tr><td style="text-align:right">
          <label for="avail_from">Availible From: </label></td><td><input type="text" name="avail_from" placeholder="MM-DD-YYYY hh:mm" />
        </td></tr>
        <tr><td style="text-align:right">
          <label for="avail_to">Availible To: </label></td><td><input type="text" name="avail_to" placeholder="MM-DD-YYYY hh:mm" />
        </td></tr>
        <tr><td style="text-align:right">
          <input type="checkbox" name="is_availible" value="1" /></td><td><label for="is_availible"> Availible Now</label>
        </td></tr>
        <tr><td colspan=2 style="text-align:center;">
          <input type="submit" value="Search" />
        </td></tr>
      </table>
    </form>
  </div>
  <div class="devices_found" style="float:left; padding-left:20px; padding-right:60px; border-left:2px solid #000000;">
    <ul class="devices_list" style="list-style:none; text-align:center;">
      <li><h3>Devices Found</h3></li>
      <a href="#"><li>Device 1</li></a>
      <li>Device 2</li>
      <a href="#"><li>Device 3</li></a>
      <a href="#"><li>Device 4</li></a>
      <a href="#"><li>Device 5</li></a>
    </ul>
  </div>
  <div class="device_info" style="float:left; padding-left:20px; border-left:2px solid #000000;">
    <ul class="device_info_list" style="list-style:none; text-align:center;">
      <li><h3>Device Info</h3></li>
      <li>field 1</li>
      <li>field 2</li>
      <li>field 3</li>
      <li>field 4</li>
      <li>field 5</li>
    </ul>
  </div>
</body>
</html>