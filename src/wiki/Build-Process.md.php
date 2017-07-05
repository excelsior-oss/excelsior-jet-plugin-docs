The Excelsior JET build process has four stages:

  * [Test Run](#test-run) (optional)
  * [Profiling](#profiling) (optional)
  * [Compilation](#compilation)
  * [Packaging](#packaging)
  * [Running](#running)

## Test Run

The plugin can run your Java application on the Excelsior JET JVM
using a JIT compiler before pre-compiling it to native code. This so-called *Test Run*
helps Excelsior JET:

* Verify that your application can be executed successfully on the Excelsior JET JVM.
  Usually, if the Test Run completes normally, the natively compiled application also works well.

* Detect the optional parts of Excelsior JET Runtime that are used by your application.
  For instance, JavaFX Webkit is not included in the resulting package by default
  due to its size, but if the application used it during a Test Run, it gets included automatically.

* Better optimize your application using the collected execution profile information.

To conduct a Test Run of a plain Java SE application, execute the following <?php tool(); ?> command:

<?php if (MAVEN) : ?>
```
mvn jet:testrun
```
<?php elseif (GRADLE) : ?>
```
gradlew jetTestRun
```
<?php endif; ?>

**Note:** During a Test Run, the application executes in a special profiling mode,
  so disregard its modest start-up time and performance.

The plugin will place the gathered profiles in the `<?php project_dir(); ?>/src/main/jetresources` directory.
Incremental changes of application code do not typically invalidate the profiles, so
it is recommended to commit the profiles (`.usg`, `.startup`) to VCS to allow the plugin
to re-use them during automatic application builds without performing a Test Run.

It is recommended to perform a Test Run at least once before building your application.

The profiles will be used by the Startup Optimizer and the 
[Global Optimizer](Optimization-Settings#global-optimizer).

Your application may require command-line arguments to run. If that is the case,
set the `runArgs` plugin parameter as follows:

<?php if (MAVEN) : ?>
```xml
<runArgs>
   <runArg>arg1</runArg>
   <runArg>arg2</runArg>
</runArgs>
```
<?php elseif (GRADLE) : ?>
```gradle
runArgs = ["arg1", "arg2"]
```
<?php endif; ?>

You may also pass the arguments via the `jet.runArgs` system property as a comma-separated string.
(Use "`\`" to escape commas within arguments: `-Djet.runArgs="arg1,Hello\, World"` will be passed
to your application as `arg1 "Hello, World"`.)

The Test Run procedure for each of the other application types 
has its own peculiarities. Refer to the respective subsections for details:

  * [Test Run for Tomcat Web Applications](Tomcat-Web-Applications#test-run)
  * [Test Run for Dynamic Libraries](Invocation-Dynamic-Libraries#test-run)
  * [Test Run for Windows Services](Windows-Services#test-run)

## Profiling

**New in 1.1.0:**

It is widely known that Oracle HotSpot and other advanced JIT compilers collect execution profile data and 
use them to optimize code more effectively. 
A lesser known fact is that all popular C++ compilers, free and commercial, implement profile-guided optimization (PGO) as well. 
The main difference is that the profile data should be collected separately by running the application and then fed to the compiler. 

Starting from version 12, Excelsior JET, Enterprise Edition and Excelsior JET Embedded offer PGO as an option. 
If you wish to use PGO you need to perform an additional profiling task with the following command:

<?php if (MAVEN) : ?>
```
mvn jet:profile
```
<?php elseif (GRADLE) : ?>
```
gradlew jetProfile
```
<?php endif; ?>

During the task a special profiling image will be created at <?php target_dir('jet/appToProfile'); ?> and 
the application will be started from that directory. At that point, you need to provide to your application 
a typical load to collect the profile.
At application stop, the gathered profile will be placed into the `<?php project_dir(); ?>/src/main/jetresources` directory.
As for Test Run, it is recommended to commit the profile (`.jprof`) to VCS to allow the plugin
to re-use it during automatic application builds without performing a Profile task.

Your application may require command-line arguments to run. 
If that is the case, set the `runArgs` plugin parameter the same way as for Test Run.
Note however, that multi-app executables has a special command line syntax where you can change a main class and/or VM arguments, 
so if you opted to create such an executable and would like to pass not only usual arguments during the profile you may use
`multiAppRunArgs` parameter instead of `runArgs` parameter. 
You may also pass the arguments to a mult-app aplication via the `jet.multiAppRunArgs` system property as a comma-separated string.

The Profile procedure for Invocation Dynamic Libraries and Windows Services application types is bit more complicated.
As plugin cannot run automatically such applications, it just creates the image at <?php target_dir('jet/appToProfile'); ?>
and you need to start your application manually from that directory providing a typical load to it.
Note, that at application stop, the gathered profile will also be placed into the `<?php project_dir(); ?>/src/main/jetresources` directory.

## Execution profiles Configuration Parameters

The pluging has a few configuration parameters for a Test Run and the Profile task via the <?php section('execProfiles'); ?> configuration section:

<?php if (MAVEN) : ?>
```xml
<execProfiles>
</execProfiles>
```
<?php elseif (GRADLE) : ?>
```gradle
execProfiles {
}
```
<?php endif; ?>

that may contain parameters described below.

* It may be necessary to profile the natively compiled application on a computer other than the one
  conducting the build, e.g. because profiling requires a specially configured environment.
  Setting the parameter 

   <?php param_value('profileLocally', 'false'); ?>  

  forces the plugin to create a special **profiling image**
  that you can then deploy to such an environment to collect an application execution profile.
 
  You can also set the `jet.create.profiling.image` system property to force the Profile task to create
  such an image instead of running the generated binary locally.
  Note that this parameter is always set to `false` for the cross-compiling flavors of Excelsior JET,
  e.g. those targeting Linux/ARM.

  For the case, the profile (`.jprof`) will be created in the application launching directory at application stop, 
  and you will need to copy it manually to the `<?php project_dir(); ?>/src/main/jetresources` of the build machine
  to enable PGO.

* <?php param_pattern('profilingImageDir', 'profiling-image-dir'); ?> - directory where the special "profiling" image 
  of the natively compiled application has to be placed.

  By default, points to the <?php target_dir('jet/appToProfile'); ?> directory.

  To facilitate deployment of the profiling image to a reference system when <?php param('profileLocally'); ?>
  is set to `false`, the plugin also creates a zip archive that contains a copy of that image,
  gives it the same base name and places it next to this directory.

*  <?php param_pattern('daysToWarnAboutOutdatedProfiles', 'days'); ?> - profile validity threshold in days.

   It is recommended to re-collect all profiles (`.startup`, `.usg`, `.jprof`) periodically as your code base evolves.
   The plugins issue a warning upon detecting an outdated profile during a build.
   With this parameter, you can adjust the respective threshold, measured in days,
   or set it to `0` to disable the warning. The default value is 30.

* <?php param_pattern('checkExistence', 'profile-type'); ?> - force the plugin to check that all or certain application profiles 
   are available before starting a build.
   Valid values are: `all` , `test-run`, `profile`, `none` (default):
     - `test-run` - profiles collected by the Test Run task (`.usg`, `.startup`).
     - `profile` - application executiion profile collected by the Profile task (`.jprof`).
     -  `all` - all profiles (`.usg`, `.startup`, and `.jprof`).

* <?php param('outputDir'); ?> and <?php param('outputName'); ?> parameters control the placement of gathered profiles 
  both for a Test Run and the Profile task.

## Compilation

The native build is performed in the `jet` subdirectory
of the <?php tool(); ?> `<?php target_dir(''); ?>` directory.
First, the plugin copies the main application jar to <?php target_dir('jet/build'); ?>,
and copies all its run time dependencies to <?php target_dir('jet/build/lib'); ?>.
Then it invokes the Excelsior JET AOT compiler to compile all those jars into a native executable.
Upon success, it copies that executable and the required Excelsior JET Runtime files
into the <?php target_dir('jet/app'); ?> directory, 
binds the executable to that copy of the Runtime,
and copies the contents of the <?php param('packageFilesDir'); ?> directory recursively
to `<?php target_dir('jet/app'); ?>`, 
if applicable (see [Customizing Package Contents](Customizing-Package-Contents)).

> Your natively compiled application is ready for distribution at this point: you may copy
> the contents of the <?php target_dir('jet/app'); ?> directory 
> to another computer that has neither Excelsior JET nor
> the Oracle JRE installed, and the executable should work as expected.


## Packaging

Packaging occurs automatically upon successful compilation.
By default, the plugin packs the contents of the 
<?php target_dir('jet/app'); ?> directory into a zip archive named 
`<?php maven_gradle('${project.build.finalName}', '<artifactName>'); ?>.zip`
so as to aid single file re-distribution.

On Windows and Linux, you can also set the <?php param_string('packaging', 'excelsior-installer'); ?> 
configuration parameter to have the plugin create an Excelsior Installer setup instead,
and on OS X, setting <?php param_string('packaging', 'osx-app-bundle'); ?> will result in the creation
of an application bundle and, optionally, a native OS X installer package (`.pkg` file).

See also:

  * [Excelsior Installer Configurations](Excelsior-Installer-Configurations) (Windows/Linux)
  * [Creating OS X App Bundles And Installers](Creating-OS-X-Application-Bundles-And-Installers)

## Running 

After sucessfull build of the application you may want to run it right from the plugin to verify 
that it works as expected.

To run a plain Java SE or Tomcat web application, execute the following <?php tool(); ?> command:

<?php if (MAVEN) : ?>
```
mvn jet:run
```
<?php elseif (GRADLE) : ?>
```
gradlew jetRun
```
<?php endif; ?>

the `runArgs` and `multiAppRunArgs` plugin parameters described above can be used for the Run as well.

