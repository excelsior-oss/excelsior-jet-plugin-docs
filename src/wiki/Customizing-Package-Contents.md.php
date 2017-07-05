By default, the final package contains just the resulting executable and the necessary Excelsior JET Runtime files.
However, you may want the plugin to add other files to it: README, license, media, help files,
third-party native libraries, and so on. For that, add the following configuration parameter:

<?php param_pattern('packageFilesDir', 'extra-package-files-directory'); ?> 

referencing a directory with all such extra files that you need added to the package.
The contents of the directory will be copied recursively to the final package.

By default, the plugin assumes that the extra package files reside
in the `src/main/jetresources/packagefiles` subdirectory of your project,
but you may dynamically generate the contents of that directory by means
of other <?php tool(); ?> plugins <?php maven_gradle('such as `maven-resources-plugin`', ''); ?>.

If you only need to add a few extra files or folders to the package,
you may find it more convenient to specify them directly rather than prepare a <?php param('packageFilesDir'); ?> directory.
You can do that using the <?php section('packageFiles'); ?> configuration section:

<?php if (MAVEN) : ?>
```xml
<packageFiles>
    <packageFile>
        <path></path>
        <type></type>
        <packagePath></packagePath>
    </packageFile>
    <packageFile>
        <path></path>
        <type></type>
        <packagePath></packagePath>
    </packageFile>
</packageFiles>
```
<?php elseif (GRADLE) : ?>
```gradle
packageFiles {
    file {
        path = new File(project.projectDir, "my.file")
        packagePath = "somePackageFolder1"
    }
    folder {
        path = new File(project.projectDir, "my.folder")
        packagePath = "somePackageFolder2"
    }
    ...
}
```
<?php endif; ?>

where <?php param('path'); ?> is the pathname of the file or folder on the host system,
<?php if (MAVEN) : ?>
<?php param('type'); ?> is either `file` or `folder` (omit this parameter if you do not want
Excelsior JET to check that <?php param('path'); ?> indeed points to a file or folder during packaging),
<?php endif; ?>
and <?php param('packagePath'); ?> is its desired location within the package (root folder if that parameter is omitted).
