Incremental compilation is available since Excelsior JET 15 for non x86-targets.
It is implemented via maintaining so called Project Databases (PDB).
A PDB is a directory that the AOT compiler creates at the very start of compilation 
and uses to store all its intermediate and auxiliary files.

## PDB Placement Configuration

To configure the PDB placement, add the following configuration
to the plugin configuration section:

<?php if (MAVEN) : ?>
```xml
<pdb>
</pdb>
```
<?php elseif (GRADLE) : ?>
```gradle
pdb {
}
```
<?php endif; ?>

It may contain one of the following parameters:

* <?php param_value('keepInBuildDir', 'true'); ?> - the PDB directory will be created in the <?php target_dir('jet/build'); ?>
directory and thus will be cleaned on every clean build. By default, the parameter is set to `false`.

* <?php param_pattern('baseDir', 'pdb-base-directory'); ?> - base directory for the PDB. If `keepInBuildDir` is set to `false` and `specificLocation` is not set,
the PDB directory for the current project will be located in the `groupId`/`projectName` subdirectory of `baseDir`.
You may set the parameter either directly from the plugin configuration or using either the `jet.pdb.basedir` system property or `JETPDBBASEDIR` 
environment variable. The default value for `baseDir` is `${user.home}/.ExcelsiorJET/PDB`.

* <?php param_pattern('specificLocation', 'pdb-location-directory'); ?> - in some cases, you may need to fully control the placement of the PDB.
If this parameter is set to a valid directory pathname, the compiler will place 
the PDB for this project in that directory, possibly creating it first.


## Cleaning the PDB

To clean the PDB for the current project, execute the following <?php tool(); ?> command:

<?php if (MAVEN) : ?>
```
mvn jet:clean
```
<?php elseif (GRADLE) : ?>
```
gradlew jetClean
```
<?php endif; ?>

As a result, all dependencies will be recompiled on the next build.
