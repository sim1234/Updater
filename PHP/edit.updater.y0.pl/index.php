<content>
<?

  function cmm($str)
  {
      echo '<a href="index.php?c=' . $str . '">' . $str . '</a>' . n;
  }


  include("../sys/classy.php");
  include("../sys/commands.php");
  $db = new e_my_sql;
  $db->connect_to("updater", "lego13", "mysql.cba.pl");
  $db->select_database('updater_y0_pl');
  

  $command = check_sj($_GET['c']);
  switch ($command)
  {
      case "newp":
          newp($db, $_POST['name'], $_POST['pass'], $_POST['v']);
          break;

      case "newf":
          $f = $_FILES['file'];
          newf($db, file_get_contents($f["tmp_name"]), $f["name"], $_POST['path'], $_POST['project'], $_POST['pass'], 1);
          break;

      case "delp":
          delp($db, $_POST['name'], $_POST['pass'], 1);
          break;

      case "delf":
          delf($db, $_POST['project'], $_POST['pass'], $_POST['name'], $_POST['path']);
          break;
          
      case "sumf":
          $f = $_FILES['file'];
          sumf($db, file_get_contents($f["tmp_name"]), $f["name"], $_POST['path'], $_POST['project'], $_POST['pass']);
          break;

      case "":
      default:
          echo "Commands:" . n;
          cmm("newp");
          cmm("newf");
          cmm("delp");
          cmm("delf");
          cmm("sumf");
          break;
  }

?>
</content>
<form method='post' enctype='multipart/form-data' action="index.php?c=newf">
<input type="file" name="file" />
<input type="text" name="pass" value="pass" />
<input type="text" name="path" value="path" />
<input type="text" name="project" value="project" />
<input type="submit" title="Wyślij"/>
</form>