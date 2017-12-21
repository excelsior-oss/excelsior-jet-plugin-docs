<?php
/***
 *  ##      ##    ###    ########  ##    ## #### ##    ##  ######    ##  
 *  ##  ##  ##   ## ##   ##     ## ###   ##  ##  ###   ## ##    ##  #### 
 *  ##  ##  ##  ##   ##  ##     ## ####  ##  ##  ####  ## ##         ##  
 *  ##  ##  ## ##     ## ########  ## ## ##  ##  ## ## ## ##   ####      
 *  ##  ##  ## ######### ##   ##   ##  ####  ##  ##  #### ##    ##   ##  
 *  ##  ##  ## ##     ## ##    ##  ##   ###  ##  ##   ### ##    ##  #### 
 *   ###  ###  ##     ## ##     ## ##    ## #### ##    ##  ######    ##
 *
 *  NEVER REMOVE TRAILING SPACES FROM THIS FILE BECAUSE PHP.
 *  
 *  Specifically, PHP removes newline characters _immediately_ following
 *  its "?>" closing tag.
 *
 *  For instance, 
 *
 *      <?php echo "foo"; ?>
 *      bar
 *
 *  becomes
 *
 *      foobar
 *
 *  and not
 *
 *      foo
 *      bar
 *
 *  as you might expect.
 *  
 *  As newlines are important in Markdown, it is necessary to handle
 *  those closing tags with care when they appear last on a line.
 *  Either insert a space after such tags, or do not let them appear there
 *  in the first place. The following function block validates the page source:
 */
    function validate_trailing_spaces($source) {
        $matches = array();
        $self = file_get_contents($source);
        $test = preg_match_all('/^.+\); \?>\R/m', $self, $matches);
        if ($test !== 0) {
            print "*** ERROR: Self-validation failed $test times (see top comment in " . $argv[0] . ")\n";
            print "The offending lines are:\n";
            foreach($matches[0] as $match) print "$match\n";
            exit (1);
        }
    }

    if (empty($argv[1]) || empty($argv[2]) || !empty($argv[3])) {
        print 'ERROR: Expected two command-line arguments: maven|gradle <wiki-page-name>';
        exit (1);
    }
    function version() {echo '1.1.2';}
    if ($argv[1] == 'maven') {
        define('MAVEN', TRUE);
        define('GRADLE', FALSE);
        function tool() {echo 'Maven';}
        function github($uri) {echo "https://github.com/excelsior-oss/excelsior-jet-maven-plugin/$uri";}
        function project_file() {echo '`pom.xml`';}
        function project_dir() {echo '${project.basedir}';}
        function target_dir($dir) {echo "`target/$dir`";}
        function param($n) {echo "`<$n>`";}
        function param_pattern($n, $v) {echo "`<$n>`*`$v`*`</$n>`";}
        function param_value($n, $v) {echo "`<$n>$v</$n>`";}
        function param_string($n, $v) {param_value($n, $v);}
        function section($s) {echo "`<$s>`";}
    } else if ($argv[1] == 'gradle') {
        define('GRADLE', TRUE);
        define('MAVEN', FALSE);
        function tool() {echo 'Gradle';}
        function github($uri) {echo "https://github.com/excelsior-oss/excelsior-jet-gradle-plugin/$uri";}
        function project_file() {echo '`build.gradle`';}
        function project_dir() {echo '<project.projectDir>';}
        function target_dir($dir) {echo "`build/$dir`";}
        function param($n) {echo "`$n`";}
        function param_pattern($n, $v) {echo "`$n = `*`$v`*";}
        function param_value($n, $v) {echo "`$n = $v`";}
        function param_string($n, $v) {echo "`$n = '$v'`";}
        function section($s) {echo "`$s{}`";}
    } else {
        print 'ERROR: Expected "maven" or "gradle" as command-line argument';
        exit(1);
    }
    function maven_gradle($maven_str, $gradle_str) {
        if (MAVEN) echo $maven_str;
        elseif (GRADLE) echo $gradle_str;
        else {
            print 'ERROR: Neither MAVEN nor GRADLE set';
            exit(1);
        }
    }

    require($argv[2]);
?>
