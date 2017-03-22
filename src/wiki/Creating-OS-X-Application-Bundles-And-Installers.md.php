The plugin supports the creation of OS X application bundles and installers.

To create an OS X application bundle, add the following configuration
into <?php maven_gradle('the plugin `<configuration>` section', 'the `excelsiorJet{}` plugin extension'); ?>:

<?php param_string('packaging', 'osx-app-bundle'); ?> 

**Note:** if you use the same <?php project_file(); ?> for all three supported platforms (Windows, OS X, and Linux), it is recommended to use another configuration:

<?php param_string('packaging', 'native-bundle'); ?> 

to create Excelsior Installer setups on Windows and Linux and an application bundle and installer on OS X.

To configure the OS X application bundle, you need to add the following configuration section:

<?php if (MAVEN) : ?>
```xml
<osxBundle>
</osxBundle>
```
<?php elseif (GRADLE) : ?>
```gradle
osxBundle {
}
```
<?php endif; ?>

The values of most bundle parameters are derived automatically from the other parameters of your <?php project_file(); ?>.
The complete list of the parameters can be obtained
[here](https://github.com/excelsior-oss/excelsior-jet-api/blob/master/src/main/java/com/excelsiorjet/api/tasks/config/OSXAppBundleConfig.java).

You still need to tell the plugin where the OS X icon (`.icns` file) for your bundle is located.
Do that using the <?php param('icon'); ?> parameter of the <?php section('osxBundle'); ?> section, or simply place the icon file at
`<?php project_dir(); ?>/src/main/jetresources/icon.icns` to let the plugin pick it up automatically.

By default, the plugin will create an OS X application bundle only,
but to distribute your application to your customers you probably need to sign it and package as an
OS X installer (`.pkg` file).
The plugin enables you to do that using the following parameters within the <?php section('osxBundle'); ?> section:

* <?php param_pattern('developerId', 'developer-identity-certificate'); ?> - "Developer ID Application" or "Mac App Distribution" certificate name for signing resulting OSX app bundle with `codesign` tool.
* <?php param_pattern('publisherId', 'publisher-identity-certificate'); ?> - "Developer ID Installer" or "Mac Installer Distribution"
certificate name for signing the resulting OS X Installer Package (`.pkg` file) with the `productbuild` tool.

If you do not want to expose above parameters via <?php project_file(); ?>, you may pass them as system properties
to the `<?php maven_gradle('mvn', 'gradlew'); ?>` command instead, using the arguments `-Dosx.developer.id` and `-Dosx.publisher.id` respectively.

**Troubleshooting:** If you would like to test the created installer file on the same OS X system on which
it was built, you need to first remove the OS X application bundle created by the plugin and located
next to the installer. Otherwise, the installer will overwrite that existing OS X application bundle
instead of installing the application into the `Applications` folder.

