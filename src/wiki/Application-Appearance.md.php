If the startup of your client application takes longer than you would have liked,
the thumb rule is to show a splash screen.
A splash screen provides visial feedback about the loading process to the end user, and
gives you an opportunity to display information about your product and company.
The splash screen functionality appeared in the Java API since Java SE 6. For more details, see
http://docs.oracle.com/javase/tutorial/uiswing/misc/splashscreen.html

If the splash image has been specified in the manifest of the application JAR file,
the respective image will be obtained automatically,
otherwise, you may assign a splash screen image to the application manually:

<?php param_pattern('splash', 'splash-image-file'); ?> 

It is recommended to store the splash image in a VCS, and if you place it at
`<?php project_dir(); ?>/src/main/jetresources/splash.png`, you won't need to specify it
in the configuration explicitly. The plugin uses the location `<?php project_dir(); ?>/src/main/jetresources`
for other Excelsior JET-specific resource files (such as the EULA for Excelsior Installer setups).

There are also two useful Windows-specific configuration parameters:

<?php param_value('hideConsole', 'true'); ?> – hide console

<?php param_pattern('icon', 'icon-file'); ?> – set executable icon (in Windows .ico format)

Just as it works for the splash image, if you place the icon file at
`<?php project_dir(); ?>/src/main/jetresources/icon.ico`, you won't need to specify it
in the configuration explicitly.
