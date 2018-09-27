This plugin will transform your application into an optimized native executable for the platform
on which you run <?php tool(); ?>, and place it into a separate directory together with all required
Excelsior JET runtime files. In addition, it can either pack that directory into a zip archive
(all platforms), create an Excelsior Installer setup (Windows and Linux only),
or create an OS X application bundle/installer.

The current version of the plugin can handle four types of applications:

* **Plain Java SE applications**, i.e. applications that have a main class
and have all their dependencies explicitly listed in the JVM classpath at launch time, and

*  **Spring Boot applications**, packaged into Spring Boot executable jar or war.

* **Tomcat Web applications** &mdash; `.war` files that can be deployed to the
  Apache Tomcat application server.

* **Invocation Dynamic Libraries** (e.g. Windows DLLs) callable
  from non-JVM languages via the Invocation API

* **Windows Services**, special long-running processes that may be launched
   during operating system bootstrap and use the
   [Excelsior JET WinService API](https://github.com/excelsior-oss/excelsior-jet-winservice-api)
   (Windows only)

In other words, if your application can be launched using a command line
of the following form:

```
java -cp [dependencies-list] [main class]
```

and loads classes mostly from jars that are present
in the `dependencies-list`, *or* if it is packaged into a `.war` file that can be deployed
to a Tomcat application server instance, then you can use this plugin.
Invocation Dynamic Libraries and Windows Services are essentially special build modes
of plain Java SE applications that yield different executable types: dynamic libraries or Windows services.

## Missing Functionality

The current plugin version supports almost all features accessible through the Excelsior JET GUIs
(JET Control Panel and JetPackII). The only bits of functionality that are missing are as follows:

* Eclipse RCP support.
  <?php if (MAVEN) : ?>
  The problem here is that the [Eclipse Tycho Maven Plugin](https://eclipse.org/tycho/)
  that enables exporting Eclipse RCP applications from Maven is still in incubation phase.
  <?php elseif (GRADLE) : ?>
  The problem here is that there is no official <?php tool(); ?> plugin
  for building Eclipse RCP applications. Even the [Eclipse Tycho Maven Plugin](https://eclipse.org/tycho/)
  that enables exporting Eclipse RCP applications from Maven is still in incubation phase.
  <?php endif; ?>
  If a standard way to build Eclipse RCP applications from <?php tool(); ?> ever appears,
  *and* there will be enough demand, we will support it in the Excelsior JET <?php tool(); ?> plugin.

* Application update packaging - because we plan to overhaul that feature completely in the mid-term future.
  Once it becomes clear how the new update process will look like, we will surely support it in the plugin.
  However, if the absence of that functionality is a show-stopper for you,
  please [let us know](<?php github('issues'); ?>) and
  we'll reprioritize.

* Customization of Excelsior Installer wizard texts.
  Custom texts should be supplied in all languages that Excelsior Installer supports,
  and we have not yet found an easy-to-use way to configure them from the plugin.

If you find that some other functionality is also missing, or you need the plugin to support
an additional feature sooner rather than later, you can help us prioritize the roadmap
by creating a feature request [here](<?php github('issues'); ?>).
