The plugin enables you to compile Spring Boot applications down
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

  * [Supported Spring Boot Versions](#supported-spring-boot-versions)
  * [Configuration](#configuration)
  * [Test Run](#test-run)
  * [Profiling](#profiling)


## Supported Spring Boot Versions

Excelsior JET 15.3 supports Spring Boot versions starting with 1.4.

## Configuration

<?php if (MAVEN) : ?>
To enable native compliation of your Spring Boot application, you need to copy and paste the following configuration into the <?php section('plugins'); ?> section of your <?php project_file(); ?> file:

```xml
<plugin>
	<groupId>com.excelsiorjet</groupId>
	<artifactId>excelsior-jet-maven-plugin</artifactId>
	<version><?php version(); ?></version>
	<configuration>
          <appType>spring-boot</appType>
	</configuration>
</plugin>
```
<?php elseif (GRADLE) : ?>
To enable native compilation of your Spring Boot application, you need to add the plugin dependency to the `buildscript` configuration of the `build.gradle` file, e.g.:

```gradle
buildscript {
    def jetPluginVersion = '<?php version(); ?>'
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
    appType = "spring-boot"
}
```

<?php endif; ?>

## Test Run

You can launch your Spring Boot application on Excelsior JET JVM using a JIT compiler
before pre-compiling it to native code using the
<?php maven_gradle('`jet:testrun` Mojo', '`jetTestRun` task'); ?> the same way
as with plain Java SE applications.

Use <?php maven_gradle('`jet:stop` Mojo', '`jetStop` task'); ?> 
for correct application termination at the end of a Test Run. 
Technically, you can terminate the application using <key>Ctrl-C</key>, but that would terminate the entire <?php tool(); ?> build
and would not constitute a correct termination.

## Profiling

Profiling Spring Boot applications is supported via the <?php maven_gradle('`jet:profile` Mojo', '`jetProfile` task'); ?>.
However, the same notice as for the Test Run applies: use <?php maven_gradle('`jet:stop` Mojo', '`jetStop` task'); ?> 
to ensure correct termination.
