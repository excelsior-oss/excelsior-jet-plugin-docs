Excelsior JET <?php tool(); ?> plugin automatically picks up and compiles 
the run time dependencies of your  project.
In addition, the plugin enables you to specify certain processing rules separately
for each dependency, or for groups of dependencies:

  - enforce code protection for all classes
  - enable selective optimization of classes
  - control packing of resource files into the resulting executable

## Contents

  * [Dependencies Configuration](#dependencies-configuration)
  * [Code Protection](#code-protection)
  * [Selective Optimization](#selective-optimization)
  * [Optimization Presets](#optimization-presets)
  * [Resource Packing](#resource-packing)
  * [Ignoring Project Dependencies](#ignoring-project-dependencies)


## Dependencies Configuration

To set these properties for a particular dependency, add the following configuration
to the plugin configuration section:

<?php if (MAVEN) : ?>
```xml
<dependencies>
    <dependency>
        <groupId>groupId</groupId>
        <artifactId>artifactId</artifactId>
        <version>version</version>
        <protect></protect>     <!-- all | not-required -->
        <optimize></optimize>   <!-- all | auto-detect -->
        <pack></pack>           <!-- all | auto-detect | none -->
    </dependency>
</dependencies>
```
<?php elseif (GRADLE) : ?>
```gradle
dependencies {
    dependency {
        groupId = 'groupId'
        artifactId = 'artifactId'
        version = 'version'
        protect = ''         // all | not-required
        optimize = ''        // all | auto-detect
        pack = ''            // all | auto-detect | none
    }
}
```
<?php endif; ?>

where <?php param('groupId'); ?>, <?php param('artifactId'); ?>, and <?php param('version'); ?> identify the dependency in the same way as in
the respective global <?php section('dependencies'); ?> section of the <?php tool(); ?> project,
and <?php param('protect'); ?>, <?php param('optimize'); ?>, and <?php param('pack'); ?> are Excelsior JET-specific properties for the dependency,
described below.

You may omit <?php param('groupId'); ?> and/or <?php param('version'); ?> from the configuration, if you are sure that there is
exactly one dependency with the given <?php param('artifactId'); ?> in the project. The plugin will issue an
ambiguous dependency resolution error if that is not the case.

You may also specify just the <?php param('groupId'); ?> parameter to set the same properties for all dependencies
sharing the same `groupId` at once.

Finally, if you need some additional dependencies that are not listed in the project explicitly
to appear in the application classpath (for example, you need to access some resources in a directory
via `ResourceBundle.getResource()`), add, for each of them, a <?php param('dependency'); ?> configuration
with the <?php param('path'); ?> parameter pointing to the respective directory or jar/zip file,
*instead of* <?php param('groupId'); ?>, <?php param('artifactId'); ?>, and/or <?php param('version'); ?>:

<?php if (MAVEN) : ?>
```xml
<dependencies>
    <dependency>
        <path>path</path>
        <protect></protect>
        <optimize></optimize>
        <pack></pack>
    </dependency>
</dependencies>
```
<?php elseif (GRADLE) : ?>
```gradle
dependencies {
    dependency {
       path = new File("path")
       protect = ''         // all | not-required
       optimize = ''        // all | auto-detect
       pack = ''            // all | auto-detect | none
    }
}
```
<?php endif; ?>

You may also use the <?php param('path'); ?> parameter to identify project dependencies that are described with 
<?php if (MAVEN) : ?>
the <?php param('systemPath'); ?> parameter.
<?php elseif (GRADLE) : ?>
<?php param('files'); ?> or <?php param('fileTree'); ?> parameters.
<?php endif; ?>

## Code Protection

If you need to protect your classes from decompilers,
make sure that the respective dependencies have the <?php param('protect'); ?> property set to `all`.
If you do not need to protect classes for a certain dependency (e.g. a third-party library),
set it to the `not-required` value instead. The latter setting may reduce the build time and the size of
the resulting executable in some cases.


## Selective Optimization

To optimize all classes and all methods of each class of a dependency for performance,
set its <?php param('optimize'); ?> property to `all`. The other valid value of that property is `auto-detect`.
It means that the Optimizer detects which classes from the dependency are used by the application
and compiles the dependency selectively, leaving the unused classes in bytecode or non-optimized form.
That helps reduce the compilation time and download size of the application.

You may want to enable selective optimization for the third-party dependencies of which your application
uses only a fraction of their implementing classes. However, it is not recommended to choose the
`auto-detect` value for the dependencies containing your own classes, because, in general,
the Excelsior JET Optimizer cannot determine the exact set of used classes due to possible access
via the Reflection API at run time. That said, you can help it significantly to detect such
dynamic class usage by performing a [Test Run](Plain-Java-SE-Applications#performing-a-test-run)
prior to the build.


## Optimization Presets

If you do not configure the above settings for any dependencies, all classes from
all dependencies will be compiled to native code.
That is a so called `typical` optimization preset.

However, as mentioned above, you may wish to set the <?php param('optimize'); ?> property to `auto-detect`
and the <?php param('protect'); ?> property to `not-required` for third-party dependencies, and
set both properties to `all` for the dependencies containing your own classes,
so as to reduce the compilation time and executable size.
You may also let the plugin do that automatically by choosing the `smart` optimization
preset in the plugin configuration:

<?php param_string('optimizationPreset', 'smart'); ?> 

When the `smart` preset is enabled, the plugin distinguishes between application classes
and third-party library classes using the following heuristic: it treats all dependencies
sharing the `groupId` with the main artifact as application classes, and all other dependencies
as third-party dependencies.

Therefore, if some of your application classes reside in a dependency with a different `groupId`
than your main artifact, make sure to set the `optimize` and `protect` properties for them
explicitly when you enable the `smart` mode, for instance:

<?php if (MAVEN) : ?>
```xml
<dependencies>
    <dependency>
        <groupId>my.company.project.group</groupId>
        <protect>all</protect>
        <optimize>all</optimize>
    </dependency>
</dependencies>
```
<?php elseif (GRADLE) : ?>
```gradle
dependencies {
    dependency {
       groupId = 'my.company.project.group'
       protect = 'all'
       optimize = 'all'
    }
}
```
<?php endif; ?>

Instead of setting the <?php param('protect'); ?> and <?php param('optimize'); ?> properties,
you may provide a semantic hint to the future maintainers
of the <?php maven_gradle('POM file', 'Gradle build script'); ?> that a particular dependency is a third party library
by setting its <?php param('isLibrary'); ?> property to `true`. The plugin will then set <?php param('protect'); ?> 
to `not-required` and <?php param('optimize'); ?> to `auto-detect` when the `smart` optimization preset is enabled.
Conversely, if you set <?php param('isLibrary'); ?> to `false`, both those properties will be set to `all`.
The following configuration is therefore equivalent to the above example:

<?php if (MAVEN) : ?>
```xml
<dependencies>
    <dependency>
        <groupId>my.company.project.group</groupId>
        <isLibrary>false</isLibrary>
    </dependency>
</dependencies>
```
<?php elseif (GRADLE) : ?>
```gradle
dependencies {
    dependency {
       groupId = 'my.company.project.group'
       isLibrary = false
    }
}
```
<?php endif; ?>

## Resource Packing

**Note:** This section only applies to dependencies that are jar or zip files.

Dependencies often contain resource files, such as images, icons, media files, etc.
By default, the Excelsior JET Optimizer packs those files into the resulting executable.
If protection is disabled and selective optimization is enabled for a dependency,
the classes that were not compiled also get packed into the executable and will be
handled by the JIT compiler at run time on an attempt to load them. As a result, the
original jar files are no longer needed for the running application to work.

The above describes the behavior for dependencies that have the <?php param('pack'); ?> property
omitted or set to the default value of `auto-detect`. However, certain dependencies
may require presence of the original class files at application run time.
For instance, some third-party security providers, e.g. Bouncy Castle, check the sizes of
their class files during execution. In such a dependency, class files serve as both program code
*and* resources: even if all classes get pre-compiled,
you still have to make them available to the running application.
Setting the <?php param('pack'); ?> property of that dependency to `all` resolves the problem.

You may also opt to not pack a particular dependency into the executable at all by
setting its <?php param('pack'); ?> property to `none`. The dependency will then be copied
to the final package as-is.
To control its location in the package, use the <?php param('packagePath'); ?> parameter of
the <?php param('dependency'); ?> configuration. By default, non-packed jar files are copied to
the `lib` subfolder of the package, while directories
(referenced by the <?php param('path'); ?> parameter) are copied to the root of the package.

Finally, if you are sure that a certain dependency does not contain any resources
*and* all its classes get compiled, you can disable copying of such a (non-packed)
dependency to the package by setting its <?php param('disableCopyToPackage'); ?> parameter to `true`.

Example of an additional dependency configuration:

<?php if (MAVEN) : ?>
```xml
<dependencies>
    <dependency>
        <path>${basedir}/target/extra-resources</path>
        <packagePath>my-extra-files</packagePath>
    </dependency>
</dependencies>
```
<?php elseif (GRADLE) : ?>
```gradle
dependencies {
   dependency {
        path = new File(project.projectDir, "target/extra-resources")
        packagePath = 'my-extra-files'
   }
}
```
<?php endif; ?>

Here we add the `extra-resources` directory to the application classpath, telling
the plugin to place it under the `my-extra-files` directory of the package
(thus `extra-resources` directory will appear in the `my-extra-files` directory
of the final package).

Note that the only valid value of the <?php param('pack'); ?> property for directories is `none`,
so there is no need to set it in the respective <?php param('dependency'); ?> configuration.


## Ignoring Project Dependencies

If you build your main artifact as a so called fat jar (using
<?php maven_gradle('`maven-assembly-plugin` with `jar-with-dependencies`',
                   'the `com.github.johnrengelman.shadow` plugin'); ?>,
for example), you most likely do not need Excelsior JET
to compile any of its dependencies, because the main artifact will contain all
classes and resources of the application.
In this case, you may set the <?php param('ignoreProjectDependencies'); ?> plugin parameter to `true`
to disable compilation of project dependencies.
Then you will only need to set the `protect/optimize/pack` properties for your main artifact
and for the entries of the <?php section('dependencies'); ?> section of the plugin that are identified
with the <?php param('path'); ?> parameter, if any.
