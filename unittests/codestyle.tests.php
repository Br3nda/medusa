
<?php
class CodeStyleTest extends UnitTestCase {
  function pathToCode($docroot) {
    $dirs = array(realpath($docroot . '/..'));
    $dirs = array_merge($this->addSubFolders($dirs), $dirs);
    $dirs = array_merge($this->addSubFolders($dirs), $dirs);
    return $dirs;
  }
  function testCodeStyle() {
    $docroot = $_SERVER['DOCUMENT_ROOT'];
    if (!$docroot) {
      $docroot = realpath('.');
    }
    $codestyle = $docroot . '/code-style.pl';
    
    foreach ($this->pathToCode($docroot) as $dir) {
      
      if (preg_match('!\.!', $dir)) {
        continue;
      }
      
      $d = dir($dir);
      if (!$d) {
        $this->dump('testCodeStyle: Failed to read dir: "' . $dir .'"');
        return false;
      }
      
      while ($entry = $d->read()) {
        
        if (!preg_match('!\.inc$!', $entry) && !preg_match('!\.php$!', $entry)) {
          continue;
        }
        chdir($dir);
        $git_blame = split("\n", shell_exec('/usr/bin/git blame "'. "$entry" .'"'));
        chdir($docroot);
        $contents = file_get_contents("$dir/$entry");
        $code_lines = split("\n", $contents);
        
        $this->checkForTodos("$dir/$entry", $code_lines, $git_blame);
        
        $line_num = 1;
        $full_code = '';
        foreach ($code_lines as $l) {
          $full_code .= "$line_num $l\n";
          $line_num++;
        }
        $result = shell_exec("$codestyle $dir/$entry");
        $lines = split("\n", $result);
        
        foreach ($lines as $line) {
          if (!$this->asserttrue(empty($line), $line)) {
            preg_match("!$dir/$entry:([0-9]+): !", $line, $matches);
            $line_number = $matches[1] -1;
            $blame = $git_blame[$line_number - 1] ."\n". $git_blame[$line_number] ."    <-- this line\n".  $git_blame[$line_number + 1] ."\n";
            $this->dump($blame);
            
          }
        }
      }
    }
  }

  function checkForTodos($filename, &$code, &$git_blame) {
    $line_number = 0;
    foreach ($code as $line) {
      
      if (!$this->assertFalse(preg_match('!TODO!i', $line), 'TODO item left undone')) {
        $this->dump("$filename\n". $git_blame[$line_number]);
        
      }
      $line_number++;
    }
  }
  
  function addSubFolders($dirs) {
    $dir = array();
    foreach ($dirs as $base) {
      $d = dir($base);
      if (!$d) {
        $this->assertTrue(false, 'Failed to read dir: "'. $dir .'"');
        continue;
      }
      else {
        $ignore_list = array('Zend', 'simpletest', '\.');
        while ($entry = $d->read()) {
          
          if (is_dir($base . '/'. $entry)) {
            $on_ignore = false;
            foreach ($ignore_list as $i) {
              if (preg_match("!$i!", $entry)) {
                $on_ignore = true;
              }
            }
            if (!$on_ignore) {
              $dir[] = $base . '/'. $entry;
            }
          }
        }
      }
      
    }
    return $dir;
  }
}

