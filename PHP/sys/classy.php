<?

  define('n', '<br />
');
  define('l', '
');

  function zle($string) {
      return '<span class="zle" id="pow">' . $string . "</span>" . n;
  }
  function dobrze($string) {
      return '<span class="dobrze" id="pow">' . $string . "</span>" . n;
  }


  function v_error($e) {
      $r = "";
      if ($e == '404')
          $r = 'Strony nie znaleziono';
      if ($e == '403')
          $r = 'Brak dostępu';
      if ($e == '502')
          $r = 'Serwer przeciążony';
      if ($r !== "")
          $r .= '!';
      return $r;
  }

  function mysql_conn() {
      $sql_conn = mysql_connect('mysql.cba.pl', 'naszteam0', 'jonelama0') or wait_conn(); //die('Could not connect to MySQL Server. MySQL returned: <br />'.mysql_error());
      mysql_select_db('naszteam0_cba_pl') or die('Could not select MySQL database. MySQL returned: <br />' . mysql_error());
  }
  function wait_conn() {
      sleep(1);
      mysql_conn();
  }

  function check_sj($string) {
      $wla = $string;
      $string = trim($string);

      $war1 = false;
      if (substr($string, 0, 2) == "\'" || substr($string, 0, 1) == "'")
          $war1 = true;

      if ($war1 && substr($string, -2, 2) == '--')
          die('Złe dane!');
      if ($war1 && strtoupper(substr($string, 2, 2)) == 'OR')
          die('Złe dane!');
      if ($war1 && strtoupper(substr($string, 1, 2)) == 'OR')
          die('Złe dane!');
      return $wla;
  }

  function read_folder($folder, $prefix = '') {
      $dir = opendir($folder);
      if ($folder !== '' && $folder !== '/')
          $folder .= '/';
      if ($prefix !== '')
          $prefix .= '/';
      while ($file = readdir($dir)) {
          if ($file != '.' && $file != '..')
              $r .= '<a href="' . $prefix . $folder . $file . '">' . $file . '</a><br />';
      }
      return $r;
  }
  function do_wyswietlenia($string) {
      return nl2br($string); //htmlspecialchars()

  }

  function by_key($string, $key) {
      $string = substr($string, strpos($string, $key));
      return substr($string, strlen($key) + 1, strpos($string, ";") - strlen($key) - 1);
  }

  function by_all($string) {
      $x = substr_count($string, ";");
      while ($x > 0) {
          $_s = substr($string, 0, strpos($string, ":"));
          $temp[$_s] = by_key($string, $_s);
          $string = substr($string, strpos($string, ";") + 1);
          $x--;
      }
      return $temp;
  }
  function na_string_sys($tablica) {
      foreach ($tablica as $a => $b) {
          $t .= $a . ':' . $b . ';';
      }
      return $t;
  }
  function wyswietl_tablice($tablica) {
      foreach ($tablica as $a => $b) {
          $t .= "[$a]:($b)<br />";
      }
      return $t;
  }

  function wyslij_forme($nazwa, $tresc, $access_key = 0) {
      if ($access_key !== 0)
          $acc = ' accesskey="' . strtoupper($access_key) . '"';
      $a = "'" . $nazwa . "'";
      return '<a href="javascript:void(0)" onclick="javascript:document.forms[' . $a . '].submit();"' . $acc . '>' . $tresc . '</a>';
  }
  function wyslij_forme_uni($nazwa, $tresc, $tryb, $zmienne, $adres = 'index.php', $class = 'hide_form') {
      $a = "'" . $nazwa . "'";
      $b = '<a href="javascript:void(0)" onclick="javascript:document.forms[' . $a . '].submit();">' . $tresc . '</a>';
      if ($class == "") {
          $c = "";
      } else {
          $c = ' class="' . $class . '"';
      }
      $f = '<form action="' . $adres . '" method="' . strtoupper($tryb) . '" name="' . $nazwa . '"' . $c . '>';
      $z = "";
      $zmienne1 = by_all($zmienne);
      foreach ($zmienne1 as $k => $v) {
          $z .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
      }
      return $f . $z . '</form>' . $b;

  }
  function getRealIpAddr() {
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
          {
          $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
      {
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
          $ip = $_SERVER['REMOTE_ADDR'];
      }
      return $ip;
  }
  class e_my_sql {
      private $conn_str;
      private $name;
      private $pass;
      private $serv;
      private $datab;

      private function mysql_conn($a, $b, $c) {
          $r = mysql_connect($a, $b, $c) or $this->wait_conn($a, $b, $c);
          return $r;
      }
      private function wait_conn($a, $b, $c) {
          sleep(1);
          $this->mysql_conn($a, $b, $c);
      }
      private function where(&$where, $from) {
          $where = str_replace('||', 'OR', $where);
          $where = str_replace('&&', 'AND', $where);
          //$where = preg_replace('/(\w+)(\[=+-<>])/', '`$1` $2', $where);
          $where = preg_replace('/(\w+)=/', '`' . $from . "`.`$1`=", $where);
          $where = preg_replace('/(\w+)>/', '`' . $from . "`.`$1`>", $where);
          $where = preg_replace('/(\w+)>=/', '`' . $from . "`.`$1`>=", $where);
          $where = preg_replace('/(\w+)</', '`' . $from . "`.`$1`<", $where);
          $where = preg_replace('/(\w+)<=/', '`' . $from . "`.`$1`<=", $where);
          $where = preg_replace('/(\w+)<>/', '`' . $from . "`.`$1`<>", $where);
      }
      public function connect_to($urzytkownik, $haslo, $server) {
          $this->conn_str = $this->mysql_conn($server, $urzytkownik, $haslo);
          $this->name = $urzytkownik;
          $this->pass = $haslo;
          $this->serv = $server;
      }
      public function select_database($database) {
          mysql_select_db($database) or die('Could not select MySQL database. MySQL returned: <br />' . mysql_error());
          $this->datab = $database;
      }
      public function close() {
          mysql_close($this->conn_str);
      }
      public function select($from, $what = '*', $where = '', $limit = '', $order_by = '', $order_method = 'ASC') {
          if ($where !== '')
              $where = ' WHERE ' . $where;
          if ($what !== '*') {
              $whatt = explode(",", $what);
              $what = '';
              $x = 0;
              foreach ($whatt as $k => $v) {
                  if ($x !== 0)
                      $what .= ', ';
                  $what .= '`' . $v . '`';
                  $x++;
              }
          }
          $this->where($where, $from);
          if ($order_by !== '')
              $order = ' ORDER BY `' . $order_by . '` ' . $order_method;
          if ($limit !== '')
              $limit = ' LIMIT ' . $limit;
          return $this->query('SELECT ' . $what . ' FROM `' . $this->datab . '`.`' . $from . '`' . $where . $order . $limit);
      }
      public function delete($from, $where, $limit = '') {
          if ($limit !== '')
              $limit = ' LIMIT ' . $limit;
          $this->where($where, $from);
          return $this->query('DELETE FROM `' . $this->datab . '`.`' . $from . '` WHERE ' . $where . $limit);
      }
      public function truncate($table) {
          return $this->query('TRUNCATE TABLE `' . $table . '`');
      }
      public function update($table, $where, $set, $limit = '') {
          if ($limit !== '')
              $limit = ' LIMIT ' . $limit;
          $this->where($where, $table);

          $set = preg_replace('/(\w+)=/', '`' . $table . "`.`$1`=", $set);
          $set = preg_replace('/(\w+)+=/', '`' . $table . "`.`$1`+=", $set);
          $set = preg_replace('/(\w+)-=/', '`' . $table . "`.`$1`-=", $set);

          return $this->query('UPDATE `' . $this->datab . '`.`' . $table . '` SET ' . $set . ' WHERE ' . $where . $limit);
      }
      public function insert($table, $pola, $values) {
          $pola = preg_replace('/(\w+)/', "`$1`", $pola);
          return $this->query('INSERT INTO `' . $this->datab . '`.`' . $table . '` (' . $pola . ') VALUES (' . $values . ')');
      }
      public function query($zapytanie) {
          #echo $zapytanie . n;
          return mysql_query($zapytanie);
      }
  }
 

?>