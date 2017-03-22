Before using this plugin, you need to install Excelsior JET.
You may find a fully functional evaluation version of Excelsior JET [here](http://www.excelsiorjet.com/evaluate).
It is free for evaluation purposes and the only limitation it has is that it expires 90 days
after installation, along with all compiled applications.

**Note:** Excelsior JET does not yet support cross-compilation, so you need to build your application on each target platform
separately. The supported platforms are Windows (32- and 64-bit), Linux (32- and 64-bit), and OS X (64-bit).

## Excelsior JET Installation Directory Lookup

In order to do its job, the plugin needs to locate an Excelsior JET installation.
You have three ways to specify the Excelsior JET installation directory explicitly:

  - add the <?php param('jetHome'); ?> parameter to the <?php
    maven_gradle('`<configuration>` section of the plugin',
                 '`excelsiorJet{}` plugin extension'); ?> 
  - pass the `jet.home` system property on the <?php tool(); ?> command line as follows:

<?php if (MAVEN) : ?>
    ```
    mvn jet:build -Djet.home=[JET-Home]
    ```
<?php elseif (GRADLE) : ?>
    ```
    gradlew jetBuild -Djet.home=[JET-Home]
    ```
<?php endif; ?>

  - or set the `JET_HOME` O/S environment variable

If none of above is set, the plugin searches for an Excelsior JET installation along the `PATH`.
So if you only have one copy of Excelsior JET installed, the plugin should be able to find it on Windows right away,
and on Linux and OS X - if you have run the Excelsior JET `setenv` script prior to launching <?php tool(); ?>.
