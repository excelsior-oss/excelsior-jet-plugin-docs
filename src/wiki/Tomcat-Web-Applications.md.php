The plugin enables you to compile Apache Tomcat together with your Web applications down
to a native binary using Excelsior JET. Compared to running your
application on a conventional JVM, this has the following benefits:

* More predictable latency for your Web application, as no code de-optimizations
  may occur suddenly at run time

* Better startup time, which may be important if you need to launch a multitude of microservices
  upon updating your distributed application.

* Better initial performance that remains stable later on, which can be important
  for load balancing inside an application cluster

* Security and IP protection, as reverse engineering of sensitive application code
  becomes much more expensive and the exposure of yet unknown to you security vulnerabilities is reduced

## Contents

  * [Supported Tomcat Versions](#supported-tomcat-versions)
  * [Configuration](#configuration)
  * [Build Process](#build-process)
  * [Tomcat Configuration Parameters](#tomcat-configuration-parameters)
  * [Multiple Web Applications and Tomcat Installation Configuration](#multiple-web-applications-and-tomcat-installation-configuration)
  * [Test Run](#test-run)


## Supported Tomcat Versions

Excelsior JET 11 supports Apache Tomcat 5.0.x (starting from version 5.0.1), 5.5.x, 6.0.x,
and 7.0.x up to version 7.0.62. Excelsior JET 11.3 adds support for Tomcat 8.0 and Tomcat 7.0.63+ versions.


## Configuration

<?php if (MAVEN) : ?>
The plugin will treat your <?php tool(); ?> project as a Tomcat Web application project if its <?php param('packaging'); ?> type is `war`.
To enable native compliation of your Tomcat Web application, you need to copy and paste the following configuration into the <?php section('plugins'); ?> section of your <?php project_file(); ?> file:

```xml
<plugin>
	<groupId>com.excelsiorjet</groupId>
	<artifactId>excelsior-jet-maven-plugin</artifactId>
	<version>0.9.5</version>
	<configuration>
        <tomcatConfiguration>
             <tomcatHome></tomcatHome>
        </tomcatConfiguration>
	</configuration>
</plugin>
```
<?php elseif (GRADLE) : ?>
The plugin will treat your Gradle project as a Tomcat Web application project if the `war` plugin is applied **before** the `excelsiorJet` plugin.
To enable native compilation of your Tomcat Web application, you need to add the plugin dependency to the `buildscript` configuration of the `build.gradle` file, e.g.:

```gradle
buildscript {
    def jetPluginVersion = '0.9.5'
    repositories {
        mavenCentral()
    }
    dependencies {
        classpath "com.excelsiorjet:excelsior-jet-gradle-plugin:$jetPluginVersion"
    }
}
```

then apply and configure the `excelsiorJet` plugin as follows:

```gradle
apply plugin: 'excelsiorJet'
excelsiorJet {
    tomcat {
        tomcatHome = ""
    }
}
```

<?php endif; ?>

and then set the <?php param('tomcatHome'); ?> parameter, which has to point to the *master* Tomcat installation &mdash; basically,
a clean Tomcat instance that was never launched.

You may also set the above parameter by passing the `tomcat.home` system property on the <?php tool(); ?> command line as follows:

<?php if (MAVEN) : ?>
```
mvn jet:build -Dtomcat.home=[Tomcat-Home]
```
<?php elseif (GRADLE) : ?>
```
gradlew jetBuild -Dtomcat.home=[Tomcat-Home]
```
<?php endif; ?>

or set the `TOMCAT_HOME` or `CATALINA_HOME` environment variables.

**NOTICE:** The binary distributions of Tomcat that are available from http://tomcat.apache.org/ usually contain
a set of standard examples in the `webapps` directory, which are most likely not needed in your own application distribution.
So it is safe to remove them from the `webapps` directory of the master Tomcat installation, making it empty.


## Build Process

During the build of your application, the plugin first copies the master Tomcat installation to the 
<?php target_dir('jet/build'); ?> subdirectory of your project.
Then it copies your main project artifact (`.war` file) to the `webapps` subdirectory of that copy,
and compiles it all together into a native executable.

Upon success, the plugin creates a directory structure similar to that of the master Tomcat installation 
in the <?php target_dir('jet/app'); ?> directory,
placing the executable into the <?php target_dir('jet/app/bin'); ?> subdirectory. 
It also copies the required Excelsior JET Runtime files
into the <?php target_dir('jet/app'); ?> directory 
and binds the resulting executable to that copy of the Runtime.

> Your natively compiled Tomcat application is ready for distribution at this point: you may copy
> the contents of the <?php target_dir('jet/app'); ?> directory to another computer that has neither Excelsior JET nor
> the Oracle JRE installed, and the executable should work as expected.
> You may also run your application using standard Tomcat scripts that are placed into the resulting
> <?php target_dir('jet/app/bin'); ?> folder by default.

Finally, the plugin packs the contents of the <?php target_dir('jet/app'); ?> directory into
a zip archive named `<?php maven_gradle('${project.build.finalName}', '<artifactName>'); ?>.zip`
so as to aid single file re-distribution.
Other packaging types that are available for plain Java SE applications are supported for Tomcat as well (see above).

## Tomcat Configuration Parameters

Most configuration parameters that are available for 
[plain Java SE applications](Plain-Java-SE-Applications]
are also available for Tomcat Web applications. 
One notable exception is that 
<?php param('path'); ?>, <?php param('packagePath'); ?>, and 
<?php param('disableCopyToPackage'); ?> parameters are not available 
for Tomcat Web application [dependencies](Dependency-Specific-Settings).

There are also a few Tomcat-specific configuration parameters that
you may set within the <?php section('tomcat'); ?> section:

* <?php param('warDeployName'); ?> - the name of the war file to be deployed into Tomcat.
   By default, Tomcat uses the name of the war file as the context path of the respective web application.
   If you need your web application to be on the "/" context path, set <?php param('warDeployName'); ?> to `ROOT` value.

* <?php param('hideConfig'); ?> - if you do not want your end users to inspect or modify the Tomcat configuration files
  located in `<tomcatHome>/conf/`, set this plugin parameter to `true`
  to have those files placed inside the executable, so they will not appear in the `conf/` subdirectory
  of end user installations of your Web application.

    **Important:**  For Tomcat to start your Web applications with hidden configuration files,
    you need to either mark the `conf/tomcat-users.xml` file read-only, or move it away from
    the `conf/` directory. If you opt for the latter, that file would remain visible, of course.

    You can do the above respectively by adding the attribute `readonly="true"` to the tag
    `<Resource name="UserDatabase">` in the `conf/server.xml` file of the master Tomcat installation,
    or modifying the `pathname` attribute of that tag. For example:
```
<Resource name="UserDatabase" auth="Container"
 type="org.apache.catalina.UserDatabase"
 description="User database that can be updated and saved"
 factory="org.apache.catalina.users.MemoryUserDatabaseFactory"
 pathname="conf/tomcat-users.xml"
 readonly="true"/>
```
  Also, you would likely want to pre-deploy the XML descriptors of your Web applications
  to `conf/<Engine>/<Host>`. Otherwise, Tomcat will extract those XML files
  from applications and place them in the `conf/` directory on startup,
  thus negating the effect of hiding.

* <?php param('genScripts'); ?> - you may continue to use the standard Tomcat scripts such as `bin/startup`
  and `bin/shutdown` with the natively compiled Tomcat, as by default
  the respective scripts are created in <?php target_dir('jet/app/bin'); ?> along with the executable.
  However, if you are going to launch the created executable directly, you may set
  the <?php param('genScripts'); ?> parameter to `false`.

* <?php param('installWindowsService'); ?> - if you opt for `excelsior-installer` packaging for Tomcat on Windows,
  the installer will register the Tomcat executable as a Windows service by default.
  You may set this parameter to `false` to disable that behavior.
  Otherwise, you may configure Windows Service-specific parameters for the Tomcat service by adding
  a <?php section('windowsService'); ?> configuration section as described [here](Windows-Services#windows-service-configuration).
    **Note:** This functionality is only available in Excelsior JET 11.3 and above.

**New in 0.9.5:**

* <?php param('allowUserToChangeTomcatPort'); ?> -  if you opt for `excelsior-installer` packaging for Tomcat on Windows,
  you may have the Excelsior Installer wizard prompt the user to specify the Tomcat HTTP port during installation
  setting this parameter to `true`.

    **Note:** This functionality is only available in Excelsior JET 11.3 and above.


## Multiple Web Applications and Tomcat Installation Configuration

Excelsior JET can also compile multiple Web applications deployed onto a single Tomcat instance.

To do this with the help of this plugin, you need to do the following:

* Determine what is the *last* Web application in your build process and add the above Excelsior JET
  plugin configuration to its <?php tool(); ?> project.

* To the projects of all other Web applications, add a file copy operation that would copy the
  final `.war` artifact into the `webapps` subdirectory of the master Tomcat installation of
  your last Web application project.

This way, the Excelsior JET AOT compiler will pick up all the Web applications that were built earlier
and compile them into the same executable as the last one.

If you need to add or change some Tomcat configurations specific to your applications,
such as DB configurations, simply make the respective changes in the master Tomcat installation.
Similarly, if you need any additional files included in the resulting installation package, you can
place them in the master Tomcat installation as well: the plugin will copy them into the final package
automatically.


## Test Run

You can launch your Tomcat Web application on Excelsior JET JVM using a JIT compiler
before pre-compiling it to native code using the
<?php maven_gradle('`jet:testrun` Mojo', '`jetTestRun` task'); ?> the same way
as with plain Java SE applications.

However, please note that a running Tomcat instance would not terminate until you run its standard `shutdown` script.
Technically, you can terminate it using <key>Ctrl-C</key>, but that would terminate the entire <?php tool(); ?> build
and would not constitute a correct Tomcat termination.
So it is recommended to use the standard Tomcat `shutdown` script for correct Tomcat termination
at the end of a Test Run. You may launch it from any standard Tomcat installation.
