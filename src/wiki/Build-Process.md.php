The Excelsior JET build process has four stages:

  * [Test Run](#test-run) (optional)
  * [Profiling](#profiling) (optional, requires Excelsior JET, Enterprise Edition or Excelsior JET Embedded, version 12 or above)
  * [Compilation](#compilation)
  * [Packaging](#packaging)

There is also a task for [running](#running) the natively compiled application after the build,
and a task for [stopping](#stopping) the application
that was previously launched via either the Test Run, Profile, or Run plugin task.

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

It's no secret that the Oracle HotSpot VM and other advanced language runtimes collect application
execution profile data and feed it to the JIT compiler for better code optimization.
A lesser known fact is that all popular C++ compilers, free and commercial, implement profile-guided optimization (PGO) as well.
The main difference is that profile data only has to be collected once on the developer's system
as a separate build step.

Starting from version 12, Excelsior JET, Enterprise Edition and Excelsior JET Embedded offer PGO as an option.
In order to use it, you need to invoke an additional profiling task with the following command:

<?php if (MAVEN) : ?>
    mvn jet:profile
<?php elseif (GRADLE) : ?>
    gradlew jetProfile
<?php endif; ?>

The plugin will prepare a special profiling image of the application at <?php target_dir('jet/appToProfile'); ?>
and launch it from that directory. At that point, you will need to supply a typical load to your application
in order to collect a representative execution profile.
Upon application exit, the gathered profile (`.jprof` file) will be placed into the
`<?php project_dir(); ?>/src/main/jetresources` directory.
Just as for the profiles collected during a Test Run, it is recommended to commit that file
to VCS to avoid running the Profile task during subsequent builds.
Just make sure to re-profile your application after making substantial changes to its source code.

If the application requires command-line arguments, set the <?php param('runArgs'); ?> plugin parameter
the same way as for the Test Run task.

**Notice**: Multi-app executables have a special command line syntax that enables you to specify a particular main class
and/or VM arguments in addition to the normal agruments that get passed to the `main()` method.
If you need to specify any of those, use the <?php param('multiAppRunArgs'); ?> parameter instead of the <?php param('runArgs'); ?> one.
You may also pass the arguments to a multi-app aplication via the `jet.multiAppRunArgs` system property as a comma-separated string.

Profiling invocation dynamic libraries and Windows services is a bit more complicated.
As the plugin cannot run them automatically, it just creates an image at <?php target_dir('jet/appToProfile'); ?>.
You then need to load the library or start the service from that directory and supply a typical load to it.
The gathered profile will also be placed into the `<?php project_dir(); ?>/src/main/jetresources` directory.

## Execution Profiles Configuration Parameters

The plugin has a few configuration parameters for the Test Run and Profile tasks
that you can specify in the <?php section('execProfiles'); ?> configuration section:

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

That section may contain parameters described below.

  * It may be necessary to profile the natively compiled application on a computer other than the one
    conducting the build, e.g. because profiling requires a specially configured environment.
    Setting the parameter

    <?php param_value('profileLocally', 'false'); ?> 

    forces the plugin to create a special *profiling image*
    that you can then deploy to such an environment to collect an application execution profile.
    Note that this parameter is always set to `false` for the cross-compiling flavors of Excelsior JET,
    e.g. those targeting Linux/ARM.
 
    You can also set the `jet.create.profiling.image` system property to force the Profile task to create
    such an image instead of running the generated binary locally.

    An execution profile (`.jprof` file) will be created in the application launch directory upon its exit,
    and you will need to copy it manually to the `<?php project_dir(); ?>/src/main/jetresources` on the build machine
    to enable PGO.

  * <?php param_pattern('profilingImageDir', 'profiling-image-dir'); ?> - directory where the special "profiling" image
    of the natively compiled application has to be placed.

    By default, points to the <?php target_dir('jet/appToProfile'); ?> directory.

    To facilitate deployment of the profiling image to a reference system when <?php param('profileLocally'); ?> 
    is set to `false`, the plugin also creates a zip archive that contains a copy of that image,
    gives it the same base name and places it next to this directory.

  * <?php param_pattern('daysToWarnAboutOutdatedProfiles', 'days'); ?> - profile validity threshold in days.

    It is recommended to re-collect all profiles (`.startup`, `.usg`, `.jprof`) periodically as your code base evolves.
    The plugin issues a warning upon detecting an outdated profile during a build.
    With this parameter, you can adjust the respective threshold, measured in days,
    or set it to `0` to disable the warning. The default value is 30.

  * <?php param_pattern('checkExistence', 'profile-type'); ?> - force the plugin to check that all or certain
    application profiles are available before starting a build.
    Valid values are: `all` , `test-run`, `profile`, `none` (default):
      - `test-run` - profiles collected by the Test Run task (`.usg`, `.startup`).
      - `profile` - application execution profile collected by the Profile task (`.jprof`).
      - `all` - all profiles (`.usg`, `.startup`, and `.jprof`).

  * <?php param('outputDir'); ?> and <?php param('outputName'); ?> parameters control the placement of gathered profiles
    for both Test Run and Profile tasks.

  * <?php param('testRunTimeout'); ?> and <?php param('profileRunTimeout'); ?> parameters set the timeout in seconds
    for the Test Run and Profile tasks respectively. The parameters are useful for automating those tasks.

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
To create `tar.gz` archive instead of `zip` use the <?php param_string('packaging', 'tar-gz'); ?> 
configuration parameter.

On Windows and Linux, you can also set the <?php param_string('packaging', 'excelsior-installer'); ?> 
configuration parameter to have the plugin create an Excelsior Installer setup instead,
and on OS X, setting <?php param_string('packaging', 'osx-app-bundle'); ?> will result in the creation
of an application bundle and, optionally, a native OS X installer package (`.pkg` file).

See also:

  * [Excelsior Installer Configurations](Excelsior-Installer-Configurations) (Windows/Linux)
  * [Creating OS X App Bundles And Installers](Creating-OS-X-Application-Bundles-And-Installers)

## Running 

After a sucessfull build of the application you may want to run it using the plugin to verify
that it works as expected.

To run a plain Java SE application, a Spring Boot application, or a Tomcat Web application, execute the following <?php tool(); ?> command:

<?php if (MAVEN) : ?>
    mvn jet:run
<?php elseif (GRADLE) : ?>
    gradlew jetRun
<?php endif; ?>

The Run task uses the `runArgs` and `multiAppRunArgs` plugin parameters described above.

## Stopping

Closing a desktop GUI application is usually straightforward, but there is no obvious way
to terminate many console and server applications from the plugin.
Two common examples are Spring Boot and Tomcat Web applications. Technically, you can terminate them by pressing <key>Ctrl-C</key>,
but that would terminate the entire <?php tool(); ?> build and would not constitute a correct termination.
To terminate such an application started by the Test Run, Profile or Run plugin task, execute the following <?php tool(); ?> command:

<?php if (MAVEN) : ?>
    mvn jet:stop
<?php elseif (GRADLE) : ?>
    gradlew jetStop
<?php endif; ?>

By default, the command sends the <key>Ctrl-C</key> event to the application to terminate it.
If your application does not terminate by <key>Ctrl-C</key> by any reason
you may change the default termination policy to a behavior that is equivalent to calling `System.exit()`
from within the application by specifying the following configuration:

<?php param_value('terminationPolicy', 'halt'); ?> 

The plugin uses files in a temporary directory to notify a running application to stop.
By default it is <?php target_dir('jet/termination'); ?>.
If you need to run and stop multiple instances of the application simultaneously you may override the temporary directory name
for a particular run/stop pair using the `-Djet.run.temp.dir=` system property to avoid possible conflicts.

Please also note that the Stop task does not work for applications that were run manually, without the plugin.

<?php if (MAVEN) : ?>
## Goals Integation into Maven `pom.xml`

The above `jet:build`, `jet:testrun`, `jet:profile` plugin goals execute the Maven `package` goal automatically,
enabling you to not specify it explicitly on the command line. 
However, should you need to configure the plugin to execute one of those goals on a paricular Maven phase,
such as `packaging`, using the above goals would result in a repeated execution of the entire Maven lifecycle. 
Therefore the plugin provides three additional goals, `jet-build`, `jet-testrun`, and `jet-profile`,
for use inside `<goal>` Maven declarations. These goals do not fork the Maven lifecycle.
For example, with the following plugin configuration:

```xml
<plugin>
	<groupId>com.excelsiorjet</groupId>
	<artifactId>excelsior-jet-maven-plugin</artifactId>
	<version><?php version(); ?></version>
	<configuration>
		<mainClass></mainClass>
	</configuration>
	<executions>
		<execution>
			<id>build</id>
			<goals>
				<goal>jet-build</goal>
			</goals>
			<phase>package</phase>
		</execution>
	</executions>
</plugin>
```

you can trigger an Excelsior JET build with a regular Maven command such as:

```
mvn package
```

<?php endif; ?>

