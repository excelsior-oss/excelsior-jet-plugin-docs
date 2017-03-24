A Windows service, formerly known as NT service, is a special long-running process that may be launched during
operating system bootstrap.
An essential feature of a service is the ability to run even if no user is logged on to the system.
Examples of services are FTP/HTTP servers, print spoolers, file sharing, etc.
Typically, Windows services have not a user interface but are managed through
the Services applet of the Windows Control Panel, or a separate application or applet.
Using the standard Services applet, a user can start/stop, and, optionally, pause/continue a previously installed service.
The common way for a service to report a warning or error is recording an event into the system event log.
The log can be inspected using the Event Viewer from Administrative Tools.
A service program is a conventional Windows executable associated with a unique system name
using which it can be installed to/removed from the system. A service can be installed as automatic
(to be launched at system bootstrap) or manual (to be activated later by a user
through the start button in the Windows Control Panel/Services).

## Contents

  * [Adding Dependency on the Excelsior JET WinService API](#adding-dependency-on-the-excelsior-jet-winservice-api)
  * [Windows Service Configuration](#windows-service-configuration)
  * [Test Run](#test-run)


## Adding Dependency on the Excelsior JET WinService API

A Windows service program must register a callback routine (so called control handler)
that is invoked by the system on service initialization, interruption, resume, etc.
With Excelsior JET, you achieve this functionality by implementing a subclass of
`com.excelsior.service.WinService` of the Excelsior JET WinService API and specifying it
as the main class of the plugin configuration.
The JET Runtime will instantiate that class on startup and translate calls to the callback routine into calls
of its respective methods, collectively called handler methods. For more details, refer to the
*"Windows Services"* Chapter of the Excelsior JET User's Guide.

To compile your implementation of `WinService` to Java bytecode, you will need to reference
the Excelsior JET WinService API from your
<?php if (MAVEN) : ?>
<?php tool(); ?> project. For that, add the following dependency
to the <?php section('dependencies'); ?> section  of your <?php project_file(); ?> file:

```xml
<dependency>
    <groupId>com.excelsiorjet</groupId>
    <artifactId>excelsior-jet-winservice-api</artifactId>
    <version>1.0.0</version>
    <scope>provided</scope>
</dependency>
```
<?php elseif (GRADLE) : ?>
Gradle build script.
For that, add the following dependency to your <?php project_file(); ?> file:

```gradle
dependencies {
    compileOnly "com.excelsiorjet:excelsior-jet-winservice-api:1.0.0"
}
```
<?php endif; ?>


## Windows Service Configuration

To create a Windows Service,
<?php if (MAVEN) : ?>
add the following Excelsior JET <?php tool(); ?> plugin configuration:

```xml
<plugin>
	<groupId>com.excelsiorjet</groupId>
	<artifactId>excelsior-jet-maven-plugin</artifactId>
	<version>0.9.5</version>
	<configuration>
        <appType>windows-service</appType>
        <mainClass>*service-main*</mainClass>
        <windowsService>
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
        </windowsService>
	</configuration>
</plugin>
```
<?php elseif (GRADLE) : ?>
you need to add the plugin dependency to the <?php section('buildscript'); ?> configuration
of the <?php project_file(); ?> file, e.g.:

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
    appType = "windows-service"
    mainClass = "" //WinService class implementation
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
```
<?php endif; ?>

Where:

* <?php param('mainClass'); ?> - a class extending the `com.excelsior.service.WinService` class
  of the Excelsior JET WinService API.

* <?php param('name'); ?> -  the system name of the service. It is used to install, remove and otherwise manage the service.
  It can also be used to recognize messages from this service in the system event log.
  This name is set during the creation of the service executable.
  By default, the value of the <?php param('outputName'); ?> parameter is used as the system name of the service.

* <?php param('displayName'); ?> - the descriptive name of the service.
  It is shown in the Event Viewer system tool and in the Services applet of the Windows Control Panel.
  By default, the value of the <?php param('name'); ?> parameter
  of the <?php section('windowsService'); ?> section is used as the display name.

* <?php param('description'); ?> - the user description of the service. It must not exceed 1000 characters.

* <?php param('arguments'); ?> - command-line arguments that shall be passed to the service upon startup.

* <?php param('logOnType'); ?> - specifies an account to be used by the service.
  Valid values are: `local-system-account` (default), `user-account`.
  - `local-system-account` - run the service under the built-in system account.
  - `user-account` - run the service under a user account.
     When installing the package, the user will be prompted for an account name
     and password necessary to run the service.

* <?php param('allowDesktopInteraction'); ?> - specifies if the service needs to interact with the system desktop,
  e.g. open/close other windows, etc. This option is only available if the service is installed
  under the local system account.

* <?php param('startupType'); ?> -  specifies how to start the service. Valid values are `automatic` (default), `manual`, `disabled`.
  - `automatic` - specifies that the service should start automatically when the system starts.
  - `manual` - specifies that a user or a dependent service can start the service.
     Services with Manual startup type do not start automatically when the system starts.
  - `disabled` - prevents the service from being started by the system, a user, or any dependent service.

* <?php param('startServiceAfterInstall'); ?> -  specifies if the service should be started immediately after installation.

*  <?php param('dependencies'); ?> - list of other service names on which the service depends.

Based on the above parameters, the plugin will create the `install.bat`/`uninstall.bat` scripts
in the <?php target_dir('jet/app'); ?> directory to enable you to install and uninstall the service manually to test it.
If you opt for the `excelsior-installer` packaging type, the service will be registered automatically
during package installation.

**Note:** The plugin does not support creation of Excelsior Installer packages for Windows Services
using Excelsior JET 11.0, as the respective functionality is missing in the `xpack` utility.
It only works for Excelsior JET 11.3 and above.

**Note:** You may build a multi-app executable runnable as both plain application and Windows service.
For that, set the <?php param('appType'); ?> parameter to `windows-service` and <?php param('multiApp'); ?> to `true`.
Please note that in this case <?php param('arguments'); ?> will have the syntax of multi-app executables,
so to pass arguments to your service and not to the Excelsior JET JVM, 
add `"-args"` (without the quotes) as the first argument.


## Test Run

Unfortunately, a service cannot be registered in the system before its compilation,
so a fully functional Test Run is not available for Windows Services. However, it is recommended
to add a `public static void main(String args[])` method to your Windows Service main class
to test your basic application functionality with Test Run.

