The Excelsior JET build process has three stages:

  * [Test Run](#test-run) (optional)
  * [Compilation](#compilation)
  * [Packaging](#packaging)

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

**Note:** 64-bit versions of Excelsior JET do not collect `.usg` profiles yet.
  So it is recommended to perform a Test Run on the 32-bit version of Excelsior JET at least once.

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
