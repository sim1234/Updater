<?

  include ("../sys/classy.php");

  $db = new e_my_sql;
  $db->connect_to("updater", "lego13", "mysql.cba.pl");
  $db->select_database('updater_y0_pl');

  $project = check_sj($_GET['project']);
  $fileid = check_sj($_GET['file']);

  if ($project)
  {
      $allp = mysql_fetch_row($db->select("project", "id,name,version", "name='" . $project . "'"));
      $projectid = $allp[0];
      if ($fileid)
      {
          $allf = mysql_fetch_row($db->select("files", "id,name,path,project", "id='" . $fileid . "'"));
          if (!$allf[0])
          {
              header('Content-Type: application/octet-stream');
              header("HTTP/1.0 404 Not Found");
          } else
          {
              $size = 0;
              $content = "";
              $idz = $db->select("filechunks", "id,file,content", "file='$fileid'", "", "id");
              while ($row = mysql_fetch_array($idz)){
                  $size += strlen($row[2]);
                  $content .= $row[2];
              }
              $begin = 0;
              $end = $size;
              if (isset($_SERVER['HTTP_RANGE']))
              {
                  if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
                  {
                      $begin = intval($matches[0]);
                      if (!empty($matches[1]))
                          $end = intval($matches[1]);
                  }
              }

              if ($begin > 0 || $end < $size)
                  header('HTTP/1.0 206 Partial Content');
              else
                  header('HTTP/1.0 200 OK');
              header("Pragma: public"); // required
              header("Expires: 0");
              header("Cache-Control: must-revalidate");#, post-check=0, pre-check=0");
              header("Cache-Control: private", false); // required for certain browsers
              #header("Content-Type: application/force-download");
              header('Content-Type: application/octet-stream');
              header('Content-Description: File Transfer');
              header("Content-Disposition: attachment; filename=\"" . $allf[1] . "\";");
              header("Content-Transfer-Encoding: binary");
              #header("Content-Length: " . $size);
              header('Content-Length:'.($end-$begin));
              header("Content-Range: bytes $begin-$end/$size");
              ob_clean();
              ob_flush();
              flush();
              
              $r = substr($content, $begin, $end - $begin);
              if ($r)
              {
                  echo $r;
              } else
              {
                  header("HTTP/1.0 505 Internal server error");
              }
          }
      } else
      {
          echo "<content>";
          $idzapytania = $db->select("files", "id,name,path,project", "project='" . $projectid . "'", "", "name");
          while ($row = mysql_fetch_array($idzapytania))
          {
              echo "<file>";
              echo "<id>" . $row[0] . "</id>";
              echo '<name>' . $row[1] . "</name>";
              echo '<path>' . $row[2] . '</path>';
              echo "</file>";
          }
          echo "</content>";
      }
  } else
  {
      echo "<content>";
      $idzapytania = $db->select("project", "id,name,version", "", "");
      while ($row = mysql_fetch_array($idzapytania))
      {
          echo "<project>";
          echo "<id>" . $row[0] . "</id>";
          echo '<name>' . $row[1] . "</name>";
          echo "<version>" . $row[2] . "</version>";
          echo "</project>";
      }
      echo "</content>";
  }

?>