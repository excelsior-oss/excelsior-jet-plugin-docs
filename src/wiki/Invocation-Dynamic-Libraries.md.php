## Contents

  * [Configuration](#configuration)
  * [Test Run](#test-run)


## Configuration

To create a dynamic library callable from applications written in a non-JVM language instead of a runnable executable, 
<?php if (MAVEN) : ?>
add the following Excelsior JET <?php tool(); ?> plugin configuration:

```xml
<plugin>
	<groupId>com.excelsiorjet</groupId>
	<artifactId>excelsior-jet-maven-plugin</artifactId>
	<version><?php version(); ?></version>
	<configuration>
        <appType>dynamic-library</appType>
	</configuration>
</plugin>
```
<?php elseif (GRADLE) : ?>
you need to add the plugin dependency to the `buildscript` configuration of the `build.gradle` file, e.g.:

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
    appType = "dynamic-library"
}
```
<?php endif; ?>

Using such libraries is a bit tricky.
Like any other JVM, Excelsior JET executes Java code in a special isolated context
to correctly support exception handling, garbage collection, and so on.
That is why Java methods cannot be directly invoked from a foreign environment.
Instead, you have to use the standard Java SE platform APIs, specifically the Invocation API
and Java Native Interface (JNI).
See `samples/Invocation` in your Excelsior JET installation directory for detailed examples.

## Test Run

To test an invocation dynamic library, you may set
a "test" <?php param('mainClass'); ?> in the plugin configuration. The `main` method of that class
should in turn call methods that are subject for usage from a non-JVM language.

## Profile

To enable PGO for an invocation dynamic library, you may use <?php maven_gradle('`jet:profile` Mojo', '`jetProfile` task'); ?>.
However as the plugin cannot "run" your library, it just creates a special profiling image at <?php target_dir('jet/appToProfile'); ?>
and you need to start your application manually from that directory that will load your invocation library and then
you need to provide a typical load to the library from the application.

