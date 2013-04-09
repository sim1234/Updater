<?

  define('l', '
');


  function newp($db, $name, $pass, $v)
  {
      if ($name && $pass && $v)
      {
          $pass = md5($pass);
          $name = check_sj($name);
          $v = check_sj($v);
          $r = mysql_fetch_row($db->select("project", "id,name", "name='$name'", "1"));
          if ($r[0])
              echo "Error: Projekt o takiej nazwie istnieje!" . l;
          else
          {
              $db->insert("project", "id,name,version,pass", "'', '$name', '$v', '$pass'");
              $r = mysql_fetch_row($db->select("project", "id,name", "name='$name'", "1"));
              $id = $r[0];
              echo "Dodano projekt! ID=$id" . l;
          }
      } else
          echo "Error: Za mało parametrów!" . l;

  }


  function newf($db, $file, $name, $path, $project, $pass, $force = 0)
  {
      if ($name && $project && $pass && $file)
      {
          $name = check_sj($name);
          $path = check_sj($path);
          $project = check_sj($project);
          $pass = md5($pass);
          $file = mysql_real_escape_string($file);
          $r = mysql_fetch_row($db->select("project", "id,name,pass", "name='$project'", "1"));
          if (!$r[0])
              echo "Error: Taki projekt nie istnieje!" . l;
          else
          {
              $projectid = $r[0];
              if ($r[2] != $pass)
                  echo "Error: Podane hasło się nie zgadza!" . l;
              else
              {
                  $r = mysql_fetch_row($db->select("files", "id,project", "name='$name' AND project='$projectid' AND path='$path'", "1"));
                  if ($r[0])
                  {
                      if ($force)
                      {
                          $id = $r[0];
                          #$db->update("files", "id='$id'", "content='$file'", "1");
                          $db->delete("filechunks", "file='$id'");
                          $db->insert("filechunks", "id,file,content", "'', '$id', '$file'");
                          echo "Zaktualizowano plik! ID=$id" . l;
                      } else
                          echo "Error: W tym projekcie już istnieje taki plik!" . l;
                  } else
                  {
                      $db->insert("files", "id,name,path,project", "'', '$name', '$path', '$projectid'");
                      $r = mysql_fetch_row($db->select("files", "id,project", "name='$name' AND project='$projectid' and path='$path'", "1"));
                      $id = $r[0];
                      $db->insert("filechunks", "id,file,content", "'', '$id', '$file'");
                      echo "Dodano plik! ID=$id" . l;

                  }
              }
          }
      } else
          echo "Error: Za mało parametrów!" . l;
  }


  function delp($db, $name, $pass, $files = 1)
  {
      if ($name && $pass)
      {
          $name = check_sj($name);
          $pass = md5($pass);
          $r = mysql_fetch_row($db->select("project", "id,name,pass", "name='$name'", "1"));
          if (!$r[0])
              echo "Error: Taki projekt nie istnieje!" . l;
          else
          {
              $projectid = $r[0];
              if ($r[2] != $pass)
                  echo "Error: Podane hasło się nie zgadza!" . l;
              else
              {
                  $id = $r[0];
                  $db->delete("project", "id='$id'", "1");
                  echo "Usunięto projekt! ID=$id" . l;
                  if ($files)
                  {
                      $idzapytania = $db->select("files", "id,project", "project='$id'");
                      while ($row = mysql_fetch_array($idzapytania))
                      {
                          $id = $row[0];
                          $db->delete("files", "id='$id'", "1");
                          $db->delete("filechunks", "file='$id'");
                          echo "Usunięto plik! ID=$id" . l;
                      }
                  }
              }
          }
      } else
          echo "Error: Za mało parametrów!" . l;
  }


  function delf($db, $project, $pass, $name, $path)
  {
      if ($name && $pass && $project)
      {
          $project = check_sj($project);
          $name = check_sj($name);
          $path = check_sj($path);
          $pass = md5($pass);
          $r = mysql_fetch_row($db->select("project", "id,name,pass", "name='$project'", "1"));
          if (!$r[0])
              echo "Error: Taki projekt nie istnieje!" . l;
          else
          {
              $projectid = $r[0];
              if ($r[2] != $pass)
                  echo "Error: Podane hasło się nie zgadza!" . l;
              else
              {
                  $r = mysql_fetch_row($db->select("files", "id,project", "name='$name' AND project='$projectid' AND path='$path'", "1"));
                  if (!$r[0])
                      echo "Error: Taki plik nie istnieje!" . l;
                  else
                  {
                      $id = $r[0];
                      $db->delete("files", "id='$id'", "1");
                      $db->delete("filechunks", "file='$id'");
                      echo "Usunięto plik! ID=$id" . l;
                  }
              }
          }
      } else
          echo "Error: Za mało parametrów!" . l;
  }

  function sumf($db, $file, $name, $path, $project, $pass)
  {
      if ($name && $project && $pass && $file)
      {
          $name = check_sj($name);
          $path = check_sj($path);
          $project = check_sj($project);
          $pass = md5($pass);
          $r = mysql_fetch_row($db->select("project", "id,name,pass", "name='$project'", "1"));
          if (!$r[0])
              echo "Error: Taki projekt nie istnieje!" . l;
          else
          {
              $projectid = $r[0];
              if ($r[2] != $pass)
                  echo "Error: Podane hasło się nie zgadza!" . l;
              else
              {
                  $r = mysql_fetch_row($db->select("files", "id,project", "name='$name' AND project='$projectid' AND path='$path'", "1"));
                  if (!$r[0])
                      echo "Error: Taki plik nie istnieje!" . l;
                  else
                  {
                      $id = $r[0];
                      $file = mysql_real_escape_string($file);
                      $db->insert("filechunks", "id,file,content", "'', '$id', '$file'");
                      #$db->update("files", "id='$id'", "content=CONCAT(`content`, '$file')", "1");
                      #$db->query("UPDATE `updater_y0_pl`.`files` SET `files`.`content`=CONCAT(`files`.`content`, '$file') WHERE `files`.`id`='$id' LIMIT 1");
                      echo "Zaktualizowano plik! ID=$id" . l;
                  }
              }
          }
      } else
          echo "Error: Za mało parametrów!" . l;
  }

?>