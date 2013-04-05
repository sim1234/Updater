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
          echo $f['name'];
          newf($db, file_get_contents($f["tmp_name"]), $f["name"], $_POST['path'], $_POST['project'], $_POST['pass'], 1);
          break;

      case "delp":
          delp($db, $_POST['name'], $_POST['pass'], 1);
          break;

      case "delf":
          delf($db, $_GET['project'], $_POST['pass'], $_POST['name'], $_POST['path']);
          break;
          
      case "sumf":
          $f = $_FILES['file'];
          echo $f['name'];
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

  #echo "Project: " . $_GET['project'] . n;
  #echo "File: " . $_GET['file'];
  $f = $_FILES['file'];
  if ($f && 0)
  {
      $c = file_get_contents($f["tmp_name"]);
      $db->insert("files", "id,name,path,project,content", "'', '" . $f["name"] . "', '', '1', '" . mysql_real_escape_string($c) .
          "'");
      echo "Wstawiono!";
      echo $c;
  }

?>
</content>
<form method='post' enctype='multipart/form-data' action="index.php?c=sumf">
<input type="file" name="file" />
<input type="text" name="pass" value="lol" />
<input type="text" name="path" value="" />
<input type="text" name="project" value="test2" />
<input type="hidden" name="c" value="sumf" />
<input type="submit" title="Wyślij"/>