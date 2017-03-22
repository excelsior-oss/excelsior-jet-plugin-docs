The plugin supports the creation of Excelsior Installer setups -
conventional installer GUIs for Windows or self-extracting archives with command-line interface
for Linux.

To create an Excelsior Installer setup, add the following configuration
into <?php maven_gradle('the plugin `<configuration>` section', 'the `excelsiorJet{}` plugin extension'); ?>:

<?php param_string('packaging', 'excelsior-installer'); ?> 

**Note:** if you use the same <?php project_file(); ?> for all three supported platforms (Windows, OS X, and Linux),
it is recommended to use another configuration:

<?php param_string('packaging', 'native-bundle'); ?> 

to create Excelsior Installer setups on Windows and Linux and an application bundle and installer on OS X.

Excelsior Installer setup, in turn, has the following configurations:

* <?php param_pattern('product', 'product-name'); ?> - default is <?php maven_gradle('`${project.name}`', '`<project.name>`'); ?> 

* <?php param_pattern('vendor', 'vendor-name'); ?> -  default is <?php maven_gradle('`${project.organization.name}`', '`<project.group>`'); ?> 

* <?php param_pattern('version', 'product-version'); ?> - default is <?php maven_gradle('`${project.version}`', '`<project.version>`'); ?> 

The above parameters are also used by Windows Version Information and OS X bundle configurations.

To further configure the Excelsior Installer setup, you need to add the following configuration section:

<?php if (MAVEN) : ?>
```xml
<excelsiorInstaller>
</excelsiorInstaller>
```
<?php elseif (GRADLE) : ?>
```gradle
excelsiorInstaller {
}
```
<?php endif; ?>

that has the following configuration parameters:

* <?php param_pattern('eula', 'end-user-license-agreement-file'); ?> - default is `<?php project_dir(); ?>/src/main/jetresources/eula.txt`

* <?php param_pattern('eulaEncoding', 'eula-file-encoding'); ?> - default is `autodetect`. Supported encodings are `US-ASCII` (plain text) and `UTF16-LE`

* <?php param_pattern('installerSplash', 'installer-splash-screen-image'); ?> - default is `<?php project_dir(); ?>/src/main/jetresources/installerSplash.bmp`

**New in 0.9.5:**

The following parameters are only available for Excelsior JET 11.3 and above:

* <?php param_pattern('language', 'setup-language'); ?> - force the installer to display its messages in a particular language.
    Available languages: `autodetect` (default), `english`, `french`, `german`,
    `japanese`, `russian`, `polish`, `spanish`, `italian`, and `brazilian`.

* <?php param_value('cleanupAfterUninstall', 'true'); ?> -  remove all files from the installation folder on uninstall

*  After-install runnable configuration sections of the form:

<?php if (MAVEN) : ?>
    ```xml
    <afterInstallRunnable>
        <target></target>
        <arguments>
            <argument></argument>
            <argument></argument>
        </arguments>
    </afterInstallRunnable>
    ```
<?php elseif (GRADLE) : ?>
    ```gradle
    afterInstallRunnable {
        target = ""
        arguments = []
    }
    ```
<?php endif; ?>

    where <?php param('target'); ?> is the location of the after-install runnable within the package,
    and <?php param('arguments'); ?> contains its command-line arguments.

* <?php param_pattern('compressionLevel', 'setup-compression-level'); ?> - available values: `fast`, `medium`, `high`

* Installation directory configuration section:

<?php if (MAVEN) : ?>
    ```xml
    <installationDirectory>
        <type></type>
        <path></path>
        <fixed></fixed>
    </installationDirectory>
    ```
<?php elseif (GRADLE) : ?>
    ```gradle
    installationDirectory {
        type = ""
        path = ""
        fixed =
    }
    ```
<?php endif; ?>

    where:
    
    * <?php param('type'); ?> is either `program-files` (default on Windows, Windows only),
      `system-drive` (Windows only, default for Tomcat web applications on Windows),
      `absolute-path`,  `current-directory` (default on Linux), or `user-home` (Linux only)
    * <?php param('path'); ?> - the default pathname of the installation directory
    * <?php param('fixed'); ?> - if set to `true`, prohibits changes of the `path` value at install time

* <?php param_pattern('registryKey', 'registry-key'); ?> - Windows registry key for installation.

* List of Windows shortcuts to create during installation, e.g. in the Start Menu:

<?php if (MAVEN) : ?>
    ```xml
    <shortcuts>
        <shortcut>
            <location></location>
            <target></target>
            <name></name>
            <icon>
                <path></path>
                <packagePath><packagePath>
            </icon>
            <workingDirectory></workingDirectory>
            <arguments>
                <argument></argument>
                <argument></argument>
            </arguments>
        </shortcut>
    </shortcuts>
    ```
<?php elseif (GRADLE) : ?>
    ```gradle
    shortcuts {
        shortcut {
            location = ""
            target = ""
            name = ""
            icon {
                path = new File("")
                packagePath = ""
            }
            workingDirectory = ""
            arguments = []
        }
           .  .  .
    }
    ```
<?php endif; ?>

    where:
    
    * <?php param('location'); ?> - either `program-folder`, `desktop`, `start-menu`, or `startup`

    * <?php param('target'); ?> - location of the shortcut target within the package

    * <?php param('name'); ?> - shortcut name. If not set, the filename of the target will be used, without extension

    * <?php param('icon'); ?> - location of the shortcut icon. If no icon is set for the shortcut, the default icon will be used.

        If the package already contains the desired icon file, configure the <?php param('packagePath'); ?> parameter
        to point to its location within the package. Otherwise, set the <?php param('path'); ?> parameter
        to the pathname of an icon file on the host system,
        and, optionally, <?php param('packagePath'); ?> to the location of the *folder* within the package
        in which that icon file should be placed (root folder by default).

    * <?php param('workingDirectory'); ?> - pathname of the working directory of the shortcut target within the package.
                             If not set, the directory containing the target will be used.

    * <?php param('arguments'); ?> - command-line arguments that shall be passed to the target

* <?php param_value('noDefaultPostInstallActions', 'true'); ?> -
     if you do not want to add the default post-install actions, e.g.
     prompting the user to run your main executable after installation.

* Windows post-install actions that will be shown to the user as a set of checkboxes at the end of installation:

<?php if (MAVEN) : ?>
    ```xml
    <postInstallCheckboxes>
        <postInstallCheckbox>
            <type></type>
            <target></target>
            <workingDirectory></workingDirectory>
            <arguments>
                <argument></argument>
                <argument></argument>
            </arguments>
            <checked></checked>
        </postInstallCheckbox>
    </postInstallCheckboxes>
    ```
<?php elseif (GRADLE) : ?>
    ```gradle
    postInstallCheckboxes {
        postInstallCheckbox {
            type = ""
            target = ""
            workingDirectory = ""
            arguments = []
            checked =
        }
           .  .  .
    }
    ```
<?php endif; ?>

    where:
    
    * <?php param('type'); ?> - `run` (default), `open`, or `restart`
    * <?php param('target'); ?> - location of the target within the package (not valid for `restart`)
    * <?php param('workingDirectory'); ?> - pathname of the working directory of the target within the package.
                             If not set, the directory containing the target will be used.
                             Valid for the `run` type only.
    * <?php param('arguments'); ?> - command-line arguments that shall be passed to the target.
                      Valid for the `run` type only.
    * <?php param('checked'); ?> - whether the checkbox should be checked by default (`true` or `false`)

* List of Windows file associations in the form:

<?php if (MAVEN) : ?>
    ```xml
    <fileAssociations>
        <fileAssociation>
            <extension></extension>
            <target></target>
            <description></description>
            <targetDescription></targetDescription>
            <icon>
                <path></path>
                <packagePath><packagePath>
            </icon>
            <arguments>
                <argument></argument>
                <argument></argument>
            </arguments>
            <checked></checked>
        </fileAssociation>
    </fileAssociations>
    ```
<?php elseif (GRADLE) : ?>
    ```gradle
    fileAssociations {
        fileAssociation {
            extension = ""
            target = ""
            description = ""
            targetDescription = ""
            icon {
                path = ""
                packagePath = ""
            }
            arguments = []
            checked =
        }
           .  .  .
    }
    ```
<?php endif; ?>

    where:
    
    * <?php param('extension'); ?> - file name extension *without the leading dot*

    * <?php param('target'); ?> - location within the package of the executable program being associated with <?php param('extension'); ?> 

    * <?php param('description'); ?> - description of the file type. For example, the description of .mp3 files is "MP3 Format Sound".

    * <?php param('targetDescription'); ?> -  string to be used in the prompt displayed by the Excelsior Installer wizard:
                               "Associate *.extension files with <?php param('targetDescription'); ?>".

    * <?php param('icon'); ?> - the location of the association icon.  If not set, the default icon will be used
               (e.g. the icon associated with the executable target).

        If the package already contains the desired icon file, configure the <?php param('packagePath'); ?> parameter
        to point to its location within the package. Otherwise, set the <?php param('path'); ?> parameter
        to the pathname of an icon file on the host system,
        and, optionally, <?php param('packagePath'); ?> to the location of the *folder* within the package
        in which that icon file should be placed (root folder by default).

    * <?php param('arguments'); ?> - command-line arguments that shall be passed to the target

    * <?php param('checked'); ?> - initial state of the respective checkbox "Associate *.extension files with <?php param('targetDescription'); ?>"
                    in the Excelsior Installer wizard. Default value is `true`.

* <?php param_pattern('installCallback', 'dynamic-library'); ?> - install callback dynamic library.
  Default is `<?php project_dir(); ?>/src/main/jetresources/install.dll|libinstall.so`

* Uninstall callback dynamic library:

<?php if (MAVEN) : ?>
    ```xml
    <uninstallCallback>
        <path></path>
        <packagePath></packagePath>
    </uninstallCallback>
    ```
<?php elseif (GRADLE) : ?>
    ```gradle
    uninstallCallback {
        path = new File("")
        packagePath = ""
    }
    ```
<?php endif; ?>

    If <?php param('packageFilesDir'); ?> or <?php param('packageFiles'); ?> add a library to the package, you need to configure
    <?php param('packagePath'); ?> parameter of <?php param('uninstallCallback'); ?> locating the library in the package, else set <?php param('path'); ?> parameter
    locating the library on the host system and <?php param('packagePath'); ?> specifying a folder within the package where
    the library should be placed (root folder by default). Default value for <?php param('path'); ?> is
    `<?php project_dir(); ?>/src/main/jetresources/uninstall.dll|libuninstall.so`

* <?php param_pattern('welcomeImage', 'welcome-image'); ?> - (Windows) image to display on the first screen of
  the installation wizard. Recommended size: 177*314px.
  Default is `<?php project_dir(); ?>/src/main/jetresources/welcomeImage.bmp`.

* <?php param_pattern('installerImage', 'installer-image'); ?> - (Windows) image to display in the upper-right corner
  on subsequent Excelsior Installer screens. Recommended size: 109*59px.
  Default is `<?php project_dir(); ?>/src/main/jetresources/installerImage.bmp`.

* <?php param_pattern('uninstallerImage', 'uninstaller-image'); ?> - (Windows) Image to display on the first screen
  of the uninstall wizard. Recommended size: 177*314px.
  Default is `<?php project_dir(); ?>/src/main/jetresources/uninstallerImage.bmp`.


