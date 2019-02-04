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
 *  in the first place. The following code block self-validates this script:
 */
    $matches = array();
    $self = file_get_contents($argv[0]);
    $test = preg_match_all('/^.+\); \?>\R/m', $self, $matches);
    if ($test !== 0) {
        print "*** ERROR: Self-validation failed $test times (see top comment in " . $argv[0] . ")\n";
        print "The offending lines are:\n";
        foreach($matches[0] as $match) print "$match\n";
        exit (1);
    }

    if (empty($argv[1]) || !empty($argv[2])) {
        print 'ERROR: Expected one command-line argument: "maven" or "gradle"';
        exit (1);
    }
    function version() {echo '1.3.1';}
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
?>
<?php maven_gradle(
'[![Maven Central](https://img.shields.io/maven-central/v/com.excelsiorjet/excelsior-jet-maven-plugin.svg)](https://maven-badges.herokuapp.com/maven-central/com.excelsiorjet/excelsior-jet-maven-plugin)',
'[![Maven Central](https://img.shields.io/maven-central/v/com.excelsiorjet/excelsior-jet-gradle-plugin.svg)](https://maven-badges.herokuapp.com/maven-central/com.excelsiorjet/excelsior-jet-gradle-plugin)');
?> 
Excelsior JET <?php tool(); ?> Plugin
=====

*Excelsior JET <?php tool(); ?> Plugin* provides <?php tool(); ?> users with an easy way to compile their applications
down to optimized native Windows, OS X, or Linux executables with [Excelsior JET](http://excelsiorjet.com).
Such precompiled applications start and often work faster, do not depend on the JRE,
and are as difficult to reverse engineer as if they were written in C++.

  * [Basic Usage](#basic-usage)
  * [Full Documentation](#full-documentation)
  * [Sample Project](#sample-project)
  * [Communication](#communication)
  * [Release Notes](#release-notes)
  * [Roadmap](#roadmap)


## Basic Usage

<?php if (GRADLE) : ?>
**Notice:** The Excelsior JET Gradle plugin requires the Java plugin 
be applied beforehand: `apply plugin: 'java'`
<?php endif; ?>

The current version of the plugin supports four types of applications:

*   **Plain Java SE applications**, defined as applications that (a) can be run
    with all dependencies explicitly listed on the command-line 
    of the conventional `java` launcher: 
    `java [-cp` _dependencies-list_ `] `_main-class_
    and (b) load classes mostly from the listed jars,

*   **Spring Boot applications**, packaged into Spring Boot executable jar or war files (since Excelsior JET 15.3),

*   [Tomcat Web applications](https://www.excelsiorjet.com/solutions/protect-java-web-applications)
    — `.war` files that can be deployed to the Apache Tomcat application server,

*   **Invocation dynamic libraries** (e.g. Windows DLLs) callable from non-JVM languages, and

*   Java applications disguised as **Windows services** using the 
    [Excelsior JET WinService API](https://www.excelsiorjet.com/docs/WinService/javadoc/)

Assuming that a copy of Excelsior JET is accessible via the operating system `PATH`, 
here is what you need to do to use it in your <?php tool(); ?> project:

### Configuring

<?php if (MAVEN) : ?>
First, copy and paste the following configuration into the <?php section('plugins'); ?> 
section of your <?php project_file(); ?> file:

    <plugin>
        <groupId>com.excelsiorjet</groupId>
        <artifactId>excelsior-jet-maven-plugin</artifactId>
        <version><?php version(); ?></version>
        <configuration>
        </configuration>
    </plugin>
<?php elseif (GRADLE) : ?>
First, add the plugin dependency in the `buildscript{}` configuration of the `build.gradle` file
and apply the `excelsiorJet` plugin:

    buildscript {
        ext.jetPluginVersion = '<?php version(); ?>'
        repositories {
            mavenCentral()
        }
        dependencies {
            classpath "com.excelsiorjet:excelsior-jet-gradle-plugin:$jetPluginVersion"
        }
    }

    apply plugin: 'excelsiorJet'
<?php endif; ?>

then proceed depending on the type of your application:

  * [Plain Java SE Application](#plain-java-se-application)
  * [Spring Boot Application](#spring-boot-application)
  * [Tomcat Web Application](#tomcat-web-application)
  * [Invocation Library](#invocation-library)
  * [Windows Service](#windows-service)

#### Plain Java SE Application

<?php if (MAVEN) : ?> 
1.  Add the following to the <?php section('configuration'); ?> section:

        <configuration>
            <mainClass></mainClass>
        </configuration>
<?php elseif (GRADLE) : ?> 
1.  Configure the <?php section('excelsiorJet'); ?> section as follows:

        excelsiorJet {
            mainClass = ''
        }
<?php endif; ?>

2.  Set the value of the <?php param('mainClass'); ?> parameter to the
    name of the main class of your application.

3.  Optionally, conduct a Test Run:

<?php if (MAVEN) : ?>
        mvn jet:testrun
<?php elseif (GRADLE) : ?>
        gradlew jetTestRun
<?php endif; ?>

4.  Optionally, collect an execution profile (not available for 32-bit Intel x86 targets yet):

<?php if (MAVEN) : ?>
        mvn jet:profile
<?php elseif (GRADLE) : ?>
        gradlew jetProfile
<?php endif; ?>

5.  [Build the project](#building)

#### Spring Boot Application

<?php if (MAVEN) : ?>
1.  Add the following to the <?php section('configuration'); ?> section:

        <configuration>
            <appType>spring-boot</appType>
        </configuration>
<?php elseif (GRADLE) : ?>
1.  Configure the <?php section('excelsiorJet'); ?> section as follows:

        excelsiorJet {
            appType = "spring-boot"
        }
<?php endif; ?>

2.  Optionally, conduct a Test Run:

<?php if (MAVEN) : ?>
        mvn jet:testrun
<?php elseif (GRADLE) : ?>
        gradlew jetTestRun
<?php endif; ?>

3.  Optionally, collect an execution profile (not available for 32-bit Intel x86 targets yet):

<?php if (MAVEN) : ?>
        mvn jet:profile
<?php elseif (GRADLE) : ?>
        gradlew jetProfile
<?php endif; ?>

4.  [Build the project](#building)

#### Tomcat Web Application

<?php if (MAVEN) : ?> 
1.  Add the following to the <?php section('configuration'); ?> section:

        <configuration>
            <tomcatConfiguration>
                 <tomcatHome></tomcatHome>
            </tomcatConfiguration>
        </configuration>
<?php elseif (GRADLE) : ?> 
1.  Configure the <?php section('excelsiorJet'); ?> section as follows:

        excelsiorJet {
            tomcat {
                tomcatHome = ""
            }
        }
<?php endif; ?>

2.  Set the <?php param('tomcatHome'); ?> parameter to point to the 
    _master_ Tomcat installation — basically, a clean Tomcat instance that was never launched.

3.  Optionally, conduct a Test Run:

<?php if (MAVEN) : ?>
        mvn jet:testrun
<?php elseif (GRADLE) : ?>
        gradlew jetTestRun
<?php endif; ?>

4.  Optionally, collect an execution profile (not available for 32-bit Intel x86 targets yet):

<?php if (MAVEN) : ?>
        mvn jet:profile
<?php elseif (GRADLE) : ?>
        gradlew jetProfile
<?php endif; ?>

5.  [Build the project](#building)


#### Invocation Library

<?php if (MAVEN) : ?> 
1.  Add the following to the <?php section('configuration'); ?> section:

        <configuration>
            <appType>dynamic-library</appType>
        </configuration>
<?php elseif (GRADLE) : ?> 
1.  Configure the <?php section('excelsiorJet'); ?> section as follows:

        excelsiorJet {
            appType = "dynamic-library"
        }
<?php endif; ?>

    **Warning:** Testing and using dynamic libraries that expose Java APIs is tricky. 
    Make sure to read the respective 
    [section](<?php github('wiki/Invocation-Dynamic-Libraries'); ?>)
    of the plugin documentation.

2.  Optionally, create a profiling image (not available for 32-bit Intel x86 targets yet):

<?php if (MAVEN) : ?>
        mvn jet:profile
<?php elseif (GRADLE) : ?>
        gradlew jetProfile
<?php endif; ?>

    and collect an execution profile by running a test application that loads your library from the created image.

3.  [Build the project](#building)


#### Windows Service

1.  Implement a class extending `com.excelsior.service.WinService`,
    as described in the [Excelsior JET WinService API documentation](https://www.excelsiorjet.com/docs/WinService/javadoc/).

2.  Add a dependency on the Excelsior JET WinService API to your <?php tool(); ?> project.
    Copy and paste the following snippet to the <?php section('dependencies'); ?> 
    section of your <?php project_file(); ?> file:

    <?php if (MAVEN) : ?> 
        <dependency>
            <groupId>com.excelsiorjet</groupId>
            <artifactId>excelsior-jet-winservice-api</artifactId>
            <version>1.0.0</version>
            <scope>provided</scope>
        </dependency>
    <?php elseif (GRADLE) : ?> 
        <pre>dependencies {
            compileOnly "com.excelsiorjet:excelsior-jet-winservice-api:1.0.0"
        }</pre>
    <?php endif; ?>

<?php if (MAVEN) : ?> 
3.  Add the following to the <?php section('configuration'); ?> section:

        <configuration>
            <appType>windows-service</appType>
            <mainClass></mainClass>
            <windowsServiceConfiguration>
                <name></name>
                <displayName></displayName>
                <description></description>
                <arguments>
                    <argument></argument>
                </arguments>
                <logOnType></logOnType>
                <allowDesktopInteraction></allowDesktopInteraction>
                <startupType></startupType>
                <startServiceAfterInstall></startServiceAfterInstall>
                <dependencies>
                     <dependency></dependency>
                </dependencies>
            </windowsServiceConfiguration>
        </configuration>
<?php elseif (GRADLE) : ?> 
3.  Configure the <?php section('excelsiorJet'); ?> section as follows:

        excelsiorJet {
            appType = "windows-service"
            mainClass = "" // <--- Your WinService implementation
            windowsService{
                name = ""
                displayName = ""
                description = ""
                arguments  = []
                logOnType = ""
                allowDesktopInteraction = false
                startupType = ""
                startServiceAfterInstall = true
                dependencies = []
            }
        }
<?php endif; ?>

4.  Set <?php param('mainClass'); ?> to the name of the class implemented on Step 1.
    For descriptions of all other parameters, refer to 
    [plugin documentation](<?php github('wiki/Windows-Services'); ?>).

    You may find complete information on Windows services support in Excelsior JET
    in the "Windows Services" Chapter of the 
    [Excelsior JET for Windows User's Guide.](https://www.excelsiorjet.com/docs/jet/jetw)

5.  Optionally, create a profiling image (not available for 32-bit Intel x86 targets yet):

<?php if (MAVEN) : ?>
        mvn jet:profile
<?php elseif (GRADLE) : ?>
        gradlew jetProfile
<?php endif; ?>

    and collect an execution profile by installing and running the service from the created image.

6.  [Build the project](#building)

### Building

<?php if (MAVEN) : ?>
Run Maven with the `jet:build` goal:

    mvn jet:build
<?php elseif (GRADLE) : ?>
Use the following command line to build the project:

    gradlew jetBuild
<?php endif; ?>

At the end of a successful build, the plugin will place your natively compiled 
Java application/library and the required pieces of Excelsior JET Runtime:

  * in the <?php target_dir('jet/app'); ?> subdirectory of your project
  * in a zip archive named `<?php maven_gradle('${project.build.finalName}', '<artifactName>'); ?>.zip`.

If your project is a plain Java SE application or Tomcat Web application, you can then
run it:

<?php if (MAVEN) : ?>
    mvn jet:run
<?php elseif (GRADLE) : ?>
    gradlew jetRun
<?php endif; ?>

Refer to [plugin documentation](<?php github('wiki'); ?>) for further instructions.


## Full Documentation

See the [Wiki](<?php github('wiki'); ?>) for full documentation on the plugin.

<?php require('src/wiki/_Sidebar.md.php'); ?> 

Refer to the [Excelsior JET User's Guide](https://www.excelsiorjet.com/docs)
and [Knowledge Base](https://www.excelsiorjet.com/kb)
for complete usage information.


## Sample Project

<?php if (MAVEN) : ?>
To demonstrate the process and result of plugin usage, we have forked the [JavaFX VNC Client](https://github.com/comtel2000/jfxvnc) project on GitHub, added the Excelsior JET plugin to its <?php project_file(); ?> file, and run it through Maven to build native binaries for three platforms.

You can download the binaries from here:

* [Windows (32-bit, 14MB installer)](http://www.excelsior-usa.com/download/jet/maven/jfxvnc-ui-1.0.0-windows-x86.exe)
* [OS X (64-bit, 45MB installer)](http://www.excelsior-usa.com/download/jet/maven/jfxvnc-ui-1.0.0-osx-amd64.pkg)
* [Linux (64-bit, 30MB installer)](http://www.excelsior-usa.com/download/jet/maven/jfxvnc-ui-1.0.0-linux-amd64.bin)

or clone [the project](https://github.com/pjBooms/jfxvnc) and build it yourself:

```
    git clone https://github.com/pjBooms/jfxvnc
    cd jfxvnc/ui
    mvn jet:build
```
<?php elseif (GRADLE) : ?>
To demonstrate the process and result of plugin usage, we have forked the [Pax Britannica](https://github.com/libgdx/libgdx-demo-pax-britannica) Libgdx demo project on GitHub,
added the Excelsior JET plugin to its `build.gradle` file, and run it through Gradle to build native binaries for three platforms.

You can download the binaries from here:

* [Windows (32-bit, 27MB installer)](http://www.excelsior-usa.com/download/jet/gradle/pax-britannica-windows-x86.exe)
* [OS X (64-bit, 50MB installer)](http://www.excelsior-usa.com/download/jet/gradle/pax-britannica-osx-amd64.pkg)
* [Linux (64-bit, 37MB installer)](http://www.excelsior-usa.com/download/jet/gradle/pax-britannica-linux-amd64.bin)

or clone [the project](https://github.com/excelsior-oss/libgdx-demo-pax-britannica) and build it yourself:

```
    git clone https://github.com/excelsior-oss/libgdx-demo-pax-britannica
    cd libgdx-demo-pax-britannica
    gradlew :desktop:jetBuild
```
<?php endif; ?>


## Communication

To report a bug in the plugin, or suggest an improvement, use
[GitHub Issues](<?php github('issues'); ?>).

To receive alerts on plugin and Excelsior JET updates, subscribe to
the [Excelsior JET RSS feed](https://www.excelsior-usa.com/blog/category/excelsior-jet/feed/),
or follow [@ExcelsiorJET](https://twitter.com/ExcelsiorJET) on Twitter.


## Release Notes

<?php if (MAVEN) : ?>
Version 1.3.2 (31-Jan-2019)

`jet-build`, `jet-testrun`, `jet-profile` goals introduced that do not fork Maven lifecylce 
 and thus can be used within `<goal>` Maven declarations (issue #82).
<?php endif; ?>

Version 1.3.1 (26-Dec-2018)

* `tar-gz` [packaging](<?php github('wiki/Build-Process#packaging'); ?>) type added for creation `tar.gz` archive 
   as the resulting output artifact (#79 Maven plugin issue).

Version 1.3.0 (31-Oct-2018)

  * Support for Spring Boot applications introduced in Excelsior JET 15.3 via <?php param_value('appType', 'spring-boot'); ?> plugin configuration
  * **Stop** task introduced for stopping applications that were run via Test Run, Run, Profile plugin tasks:

    <?php if (MAVEN) : ?>
        mvn jet:stop
    <?php elseif (GRADLE) : ?>
        gradlew jetStop
    <?php endif; ?>

  * <?php param('testRunTimeout'); ?>, <?php param('profileRunTimeout'); ?> parameters were added to
    <?php section('execProfiles'); ?> configuration section to allow automating Test Run and Profile Run tasks for applications
    that do not terminate by themselves.

Version 1.2.0 (08-May-2018)

<?php section('pdb'); ?> configuration section introduced to control the location of the Project Database (PDB).
PDB is used for incremental compilation: once a full build succeeds, only the changed project dependencies
are recompiled during the subsequent builds.
The configuration, as well as the incremental compilation feature, are available only for Excelsior JET 15 and above, and only for targets other than 32-bit x86.
This release of the plugin places the PDB outside of the build directory by default to enable incremental compilation even for clean builds.
In addition, this version of the plugin also introduces the <?php if (MAVEN) : ?>`jet:clean` <?php elseif (GRADLE) : ?> `jetClean` <?php endif; ?> task for cleaning the PDB.

<?php if (MAVEN) : ?>
Version 1.1.3 (20-Apr-2018)

Filter `pom` dependencies (issue #69).
<?php endif; ?>

<?php if (GRADLE) : ?>
Version 1.1.3 (25-Dec-2017)

Fix for issue: "Project task path for nested multiprojects generate incorrect path" (#37)

<?php endif; ?>

Version 1.1.2 (26-Oct-2017)

Fix for `NullPointerException` when a shortcut with no icon is used for Excelsior Installer backend (issue (#62)[https://github.com/excelsior-oss/excelsior-jet-maven-plugin/issues/62])

<?php if (GRADLE) : ?>
Version 1.1.1 (01-Aug-2017)

<?php param('jetHome'); ?> plugin parameter ignoring (issue #31) fix
<?php endif; ?>

Version 1.1.0 (07-Jul-2017)

Support for new features of Excelsior JET 12 and other enhancements:

  * Global Optimizer is now enabled for all target platforms
  * **Profile** task introduced to enable the use of Profile-Guided Optimization
    (not available for 32-bit Intel x86 targets yet):

    <?php if (MAVEN) : ?>
        mvn jet:profile
    <?php elseif (GRADLE) : ?>
        gradlew jetProfile
    <?php endif; ?>

  * **Run** task introduced for running the natively compiled application right after the build:

    <?php if (MAVEN) : ?>
        mvn jet:run
    <?php elseif (GRADLE) : ?>
        gradlew jetRun
    <?php endif; ?>

  * Fix for a file copying [issue](https://github.com/excelsior-oss/excelsior-jet-maven-plugin/issues/57).

Version 1.0.0 (04-May-2017)

First non-beta release. Here is what we have done:

  * Reworked plugin documentation and moved it to the Wiki
  * Tested the plugin against all platforms/editions that Excelsior JET 11.0 and 11.3 support
  * Fixed a handful of minor bugs reported by users and found during testing
  * Added the somehow overlooked <?php param('stackAllocation'); ?> parameter
    that controls allocation of Java objects on the stack

**Backward incompatibile change alert:** Windows version-information resource generation
is now _off_ by default. To revert to the previous behavior, add
<?php param_pattern('addWindowsVersionInfo', 'true'); ?> to the plugin configuration.


Version 0.9.5 aka 1.0 Release Candidate (15-Feb-2017)

This release covers all Excelsior JET features accessible through the JET Control Panel GUI,
and all options of the `xpack` utility as of Excelsior JET 11.3 release, except for three things
that we do not plan to implement in the near future, for different reasons:
creation of update packages, Eclipse RCP applications support, and internationalization
of Excelsior Installer messages.
If you are using any other Excelsior JET functionality that the plugin does not support,
please create a feature request [here](<?php github('issues'); ?>).
Otherwise, think of this version as of 1.0 Release Candidate 1.

Compared with the previous releases, the following functionality was added to the plugin:

* <?php param('packageFiles'); ?> parameter introduced to add separate files/folders to the package
* <?php section('excelsiorInstaller'); ?> configuration section extended with the following parameters:
    - <?php param('language'); ?> - to set installation wizard language
    - <?php param('cleanupAfterUninstall'); ?> - to remove all files on uninstall
    - <?php param('afterInstallRunnable'); ?> - to run an executable after installation
    - <?php param('compressionLevel'); ?> - to control installation package compression
    - <?php param('installationDirectory'); ?> - to change installation directory defaults
    - <?php param('registryKey'); ?> - to customize the registry key used for installation on Windows
    - <?php param('shortcuts'); ?> - to add shortcuts to the Windows Start menu, desktop, etc.
    - <?php param('noDefaultPostInstallActions'); ?> - to not add the default post-install actions
    - <?php param('postInstallCheckboxes'); ?> - to configure post-install actions
    - <?php param('fileAssociations'); ?> - to create file associations
    - <?php param('installCallback'); ?> - to set install callback dynamic library
    - <?php param('uninstallCallback'); ?> - to set uninstall callback dynamic library
    - <?php param('welcomeImage'); ?>, <?php param('installerImage'); ?>, <?php param('uninstallerImage'); ?> - to customize (un)installer appearance
* <?php param('allowUserToChangeTomcatPort'); ?> parameter added to the <?php section('tomcat'); ?> configuration section
  to allow the user to change the Tomcat port at install time

Version 0.9.4 (24-Jan-2017)

* `typical` and `smart` optimization presets introduced.

Version 0.9.3 (19-Jan-2017)

* <?php section('runtime'); ?> configuration section introduced and related parameters moved to it:
   <?php param('locales'); ?>, <?php param('profile'); ?>, <?php param('optRtFiles'); ?> (renamed to <?php param('components'); ?>), <?php param('javaRuntimeSlimDown'); ?> (renamed to <?php param('slimDown'); ?>).
   Old configuration parameters are now deprecated and will be removed in a future release.
   New parameters added to the <?php section('runtime'); ?> section:
    - <?php param('flavor'); ?> to select a runtime flavor
    - <?php param('location'); ?> to change runtime location in the resulting package
    - <?php param('diskFootprintReduction'); ?> to reduce application disk footprint

* Windows version-info resource configuration changed to meet other enclosed configurations style.
  Old way to configure Windows version info is deprecated and will be removed in a future release.

<?php if (MAVEN) : ?>
Version 0.9.2 (12-Jan-2017)

Issue with buildnumber-maven-plugin #49 fixed
<?php endif; ?>

Version 0.9.1 (02-Dec-2016)

* Support for Compact Profiles
* Not working Test Run for 7+ Tomcat versions fixed<?php if (MAVEN) : ?>(issue #42)<?php endif; ?> 

Version 0.9.0 (23-Nov-2016)

Invocation dynamic libraries and Windows services support.

Version 0.8.1 (28-Oct-2016)

The release supports [Excelsior JET Embedded 11.3 for Linux/ARM](https://www.excelsiorjet.com/embedded/).

Version 0.8.0 (20-Oct-2016)

The release adds the capability to set Excelsior JET-specific properties for project dependencies,
such as code protection, selective optimization, and resource packing.

Version 0.7.2 (19-Aug-2016)

This release adds the capability to pass command-line arguments to the application
during startup profiling and the test run.

Version 0.7.1 (10-Aug-2016)

This release covers most of the compiler options that are available in the JET Control Panel UI,
and all options of the `xpack` utility as of Excelsior JET 11.0 release:

  * <?php param('splash'); ?> parameter introduced to control the appearance of your application on startup
  * <?php param('inlineExpansion'); ?> parameter introduced to control aggressiveness of methods inlining
  * <?php param('stackTraceSupport'); ?> parameter introduced to set stack trace support level
  * <?php param('compilerOptions'); ?> parameter introduced to set advanced compiler options and equations
  * <?php param('locales'); ?> parameter introduced to add additional locales and charsets to the resulting package

<?php if (MAVEN) : ?>
Version 0.7.0 (22-June-2016)

* Massive refactoring that introduces `excelsior-jet-api` module: a common part between Maven and Gradle
  Excelsior JET plugins

* <?php param('jetResourcesDir'); ?> parameter introduced to set a directory containing Excelsior JET specific resource files
   such as application icons, installer splash, etc.

Version 0.6.0 (30-May-2016)

* Compilation of Tomcat Web applications is supported

Version 0.5.1 (13-Apr-2016)

* Fix for incorrect default EULA value for Excelsior Installer

Version 0.5.0 (04-Apr-2016)

* Mac OS X application bundles and installers support

Version 0.4.4 (11-Mar-2016)

* <?php param('protectData'); ?> parameter added to enable data protection

Version 0.4.3 (17-Feb-2016)

* <?php param('jvmArgs'); ?> parameter introduced to define system properties and JVM arguments

Version 0.4.2 (11-Feb-2016)

* Trial version generation is supported

Version 0.4.1 (05-Feb-2016)

* <?php param('packageFilesDir'); ?> parameter introduced to add extra files to the final package

Version 0.4.0 (03-Feb-2016)

Reduced the download size and disk footprint of resulting packages by means of supporting:

* Global Optimizer
* Java Runtime Slim-Down

Version 0.3.2 (01-Feb-2016)

* "[Changes are not reflected in compiled app if building without clean #11](<?php github('issues/11'); ?>)" issue fixed
* Error message corrected for "[Cannot find jar if classifier is used #10](<?php github('issues/10'); ?>)",
  explicitly referring the <?php param('mainJar'); ?> plugin parameter that should be set in such cases.

Version 0.3.1 (26-Jan-2016)

* <?php param('optRtFiles'); ?> parameter introduced to add optional JET runtime components

Version 0.3.0 (22-Jan-2016)

* Startup Accelerator supported and enabled by default
* Test Run Mojo implemented that enables:
   - running an application on the Excelsior JET JVM before pre-compiling it to native code
   - gathering application execution profiles to enable the Startup Optimizer

Version 0.2.1 (21-Jan-2016)

* Support of multi-app executables

Version 0.2.0 (14-Dec-2015)

* Support of Excelsior Installer setup generation
* Windows Version Information generation

Version 0.1.0 (08-Dec-2015)
* Initial release supporting compilation of the Maven Project with all dependencies into native executable
and placing it into a separate directory with required Excelsior JET runtime files.
<?php elseif (GRADLE) : ?>
Version 0.7.0 (12-Jul-2016)

* Compilation of Tomcat Web applications is supported

Version 0.3.0 (06-Jul-2016)

* Support of Excelsior Installer setup generation
* Windows Version Information generation
* Support of multi-app executables
* Startup Accelerator supported and enabled by default
* Test Run Task implemented that enables:
   - running an application on the Excelsior JET JVM before pre-compiling it to native code
   - gathering application execution profiles to enable the Startup Optimizer
* `optRtFiles` parameter introduced to add optional JET runtime components
* Reduced the download size and disk footprint of resulting packages by means of supporting:
   * Global Optimizer
   * Java Runtime Slim-Down
* `packageFilesDir` parameter introduced to add extra files to the final package
* Trial version generation is supported
* `jvmArgs` parameter introduced to define system properties and JVM arguments
* `protectData` parameter added to enable data protection
* Mac OS X application bundles and installers support

Version 0.1.0 (24-Jun-2016)
* Initial release supporting compilation of the Gradle Project with all dependencies into native executable
and placing it into a separate directory with required Excelsior JET runtime files.
<?php endif; ?>
