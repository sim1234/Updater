<?

  include ("sys/classy.php");

  $db = new e_my_sql;
  $db->connect_to("updater", "lego13", "mysql.cba.pl");
  $db->select_database('updater_y0_pl');

  $project = check_sj($_GET['project']);
  $fileid = check_sj($_GET['file']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<link rel="Stylesheet" type="text/css" href="grafika/style.css" />
<link rel="icon" href="grafika/favicon.ico" type="image/x-icon" />
<meta name="Copyright" content="Sim1234" />
<meta name="Author" content="Sim1234" />
<title>Updater</title>
</head>
<body>
<div class="top">
<?

  if ($project)
  {
      echo '<h5><a href="index.php">Strona Główna</a></h5>';
      $allp = mysql_fetch_row($db->select("project", "id,name,version", "name='" . $project . "'", "1"));
      $projectid = $allp[0];
      $project = ($allp[1] ? $allp[1] : "Projekt nie istnieje");
      echo '<h3><a href="' . $project . '">' . $project . "</a></h3>";
      if ($fileid)
      {
          $allf = mysql_fetch_row($db->select("files", "id,name,path,project,content", "id='" . $fileid . "'", "1"));
          $file = htmlspecialchars($allf[1] ? $allf[1] : "Plik nie istnieje");
          echo '<h4><a href="http://download.updater.y0.pl/' . $project . "-" . $allf[0] . '">' . htmlspecialchars($allf[1]) . '</a> (' . htmlspecialchars($allf[2]) . ")</h4>";
          echo '<div class="file">' . nl2br(htmlspecialchars($allf[4])) . "</div>";
      } else
      {
          echo "<h6>Pliki:</h6><table>";
          echo '<tr><td>Id</td><td style="min-width:200px;">Name</td><td style="min-width:200px;">Path</td></tr>';
          $idzapytania = $db->select("files", "id,name,path,project", "project='" . $projectid . "'", "", "name");
          while ($row = mysql_fetch_array($idzapytania))
          {
              echo "<tr>";
              echo "<td>" . $row[0] . "</td>";
              echo '<td><a href="' . $project . "-" . $row[0] . '">' . htmlspecialchars($row[1]) . "</a></td>";
              echo '<td>' . htmlspecialchars($row[2]) . '</td>';
              echo "</tr>";
          }
          echo "</table>";
      }
  } else
  {
      echo "<h6>Projekty:</h6><table>";
      echo '<tr><td>Id</td><td style="min-width:200px;">Name</td><td style="min-width:50px;">Version</td></tr>';
      $idzapytania = $db->select("project", "id,name,version", "", "");
      while ($row = mysql_fetch_array($idzapytania))
      {
          echo "<tr>";
          echo "<td>" . $row[0] . "</td>";
          echo '<td><a href="' . $row[1] . '">' . $row[1] . "</a></td>";
          echo "<td>" . $row[2] . "</td>";
          echo "</tr>";
      }
      echo "</table>";
  }

  #echo "Project: " . $_GET['project'] . n;
  #echo "File: " . $_GET['file'];
  $f = $_FILES['file'];
  if ($f)
  {
      $c = file_get_contents($f["tmp_name"]);
      $db->insert("files", "id,name,path,project,content", "'', '', '" . $f["name"] . "', '1', '" . mysql_real_escape_string($c) . "'");
      echo "Wstawiono!";
      echo $c;
  }
#echo "get".wyswietl_tablice($_GET).n;
#echo "post".wyswietl_tablice($_POST).n;
#echo "files".wyswietl_tablice($_FILES).n;
#echo "file".wyswietl_tablice($_FILES['file']).n;
?>
<form method='post' enctype='multipart/form-data'>
<input type="file" name="file" />
<input type="submit" title="Wyślij"/>
</form>
</div>
</body>
</html>