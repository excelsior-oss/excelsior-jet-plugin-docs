## Contents

  * [Plain Java SE Applications](#plain-java-se-applications)
  * [Other Application Types](#other-application-types)
  * [Configurations Other Than <?php param('mainClass'); ?>](#configurations-other-than-mainclass)


## Plain Java SE Applications

<?php if (MAVEN) : ?>
If your project is a plain Java SE application, you need to copy and paste
the following configuration into the <?php section('plugins'); ?> section
of your <?php project_file(); ?> file:

```xml
<plugin>
	<groupId>com.excelsiorjet</groupId>
	<artifactId>excelsior-jet-maven-plugin</artifactId>
	<version>0.9.5</version>
	<configuration>
		<mainClass></mainClass>
	</configuration>
</plugin>
```

set the <?php param('mainClass'); ?> parameter, and use the following command line to build the application:

```
mvn jet:build
```
<?php elseif (GRADLE) : ?>
Excelsior JET <?php tool(); ?> plugin is hosted on Maven Central, so you need to add
the plugin dependency to the <?php section('buildscript'); ?> configuration
of your <?php project_file(); ?> file first:

```gradle
buildscript {
    ext.jetPluginVersion = '0.9.5'
    repositories {
        mavenCentral()
    }
    dependencies {
        classpath "com.excelsiorjet:excelsior-jet-gradle-plugin:$jetPluginVersion"
    }
}
```

then apply and configure the `excelsiorJet` plugin, using the name of your main class
as the value of the `mainClass` parameter:

```gradle
apply plugin: 'excelsiorJet'
excelsiorJet {
    mainClass = ''
}
```

and use the following command line to build the application:

```
gradlew jetBuild
```

**Note:** The Excelsior JET Gradle plugin requires the Java plugin be applied beforehand: ```apply plugin: 'java'```
<?php else :
          exit(1);
      endif; ?>

## Other Application Types

<sup>\*</sup> For a Tomcat Web application, the <?php param('mainClass'); ?> parameter is not needed.
Instead, you would need to add the <?php param('tomcatHome'); ?> parameter pointing
to a *clean* Tomcat installation, a copy of which will be used
for the deployment of your Web application at build time.
See the [Tomcat Web Applications](Tomcat-Web-Applications) section for more details.

An [Invocation Dynamic Library](Invocation-Dynamic-Libraries) does not need a main class either,
and the main class of a [Windows Service](Windows-Services) application must extend a special class `com.excelsior.service.WinService`
of the [Excelsior JET WinService API](https://github.com/excelsior-oss/excelsior-jet-winservice-api).

## Configurations Other Than <?php param('mainClass'); ?> 

For the complete list of parameters, refer to
<?php if (MAVEN) : ?>
the Javadoc of `@Parameter` field declarations of the
[AbstractJetMojo](<?php github('blob/master/src/main/java/com/excelsiorjet/maven/plugin/AbstractJetMojo.java'); ?>)
and [JetMojo](<?php github('blob/master/src/main/java/com/excelsiorjet/maven/plugin/JetMojo.java'); ?>)
classes.
<?php elseif (GRADLE) : ?>
the Javadoc of field declarations of the
[ExcelsiorJetExtension](https://github.com/excelsior-oss/excelsior-jet-gradle-plugin/blob/master/src/main/groovy/com/excelsiorjet/gradle/plugin/ExcelsiorJetExtension.groovy) class.
<?php endif; ?>
Most of them have default values derived from your <?php project_file(); ?> project,
such as the <?php param('outputName'); ?> parameter specifying the name of the resulting executable.
