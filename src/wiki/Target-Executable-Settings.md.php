## Data Protection

If you do not wish constant data, such as reflection info, Java string literals, or packed resource files,
to be visible in the resulting executable, enable data protection by specifying the following configuration:

<?php param_value('protectData', 'true'); ?> 

For more details on data protection, refer to the *"Data Protection"* section of
the *"Intellectual Property Protection"* chapter of the Excelsior JET User's Guide.

## Multi-app Executables

The plugin may compile more than one application into a single executable and
let you select a particular application at launch time via command-line arguments.

The command line syntax of multi-app executables 
is an extension of the `java` launcher command
line syntax that allows specifying the main class, VM options, Java system properties,
and the arguments of the application:

```
    Exe-name [Properties-and-options] Main-classname [App-arguments]
```

To enable the multi-app mode add the following configuration parameter:

<?php param_value('multiApp', 'true'); ?> 

See Excelsior JET User's Guide, Chapter *"Application Consideration"*,
section *"Multi-app Executables"* for details.


## Trial Versions

You can create a trial version of your Java application that will expire in a specified number of days
after the build date of the executable, or on a fixed date.
Once the trial period is over, the application will refuse to start up,
displaying a custom message.

To enable trial version generation, copy and paste into your <?php project_file(); ?> file the following plugin configuration:

<?php if (MAVEN) : ?>
```xml
<trialVersion>
    <expireInDays></expireInDays>
    <expireMessage></expireMessage>
</trialVersion>
```
<?php elseif (GRADLE) : ?>
```gradle
trialVersion {
    expireInDays = 0
    expireMessage = ''
}
```
<?php endif; ?>

and specify the number of calendar days after the build date when you want the application
to expire, and the error message that the expired binary should display to the user on a launch attempt.

You can also set a particular, fixed expiration date by using the <?php param('expireDate'); ?> parameter
instead of <?php param('expireInDays'); ?>. The format of the <?php param('expireDate'); ?> parameter value
is *ddMMMyyyy*, for example `15Sep2020`.

**Note:** If you choose the <?php param('packaging'); ?> type `excelsior-installer`, the generated setup
package will also expire, displaying the same message to the user.

One common usage scenario of this functionality is setting the hard expiration date further into the future,
while using some other mechanism to enforce a (shorter) trial period.
Typically, you would set the hard expiration date somewhat beyond the planned release
date of the next version of your application. This way, you would ensure that nobody uses
an outdated trial copy for evaluation.


## Stack Trace Support
The Excelsior JET Runtime supports three modes of stack trace printing: `minimal`, `full`, and `none`.

In the `minimal` mode (default), line numbers and names of some methods are omitted in call stack entries,
but class names are exact.

In the `full` mode, the stack trace info includes all line numbers and method names.
However, enabling the full stack trace has a side effect &mdash; substantial growth of the resulting executable in size, approximately by 30%.

In the `none` mode, `Throwable.printStackTrace()` methods print a few fake elements.
It may result in a performance improvement, if the application throws and catches exceptions repeatedly.
Note, however, that certain third-party APIs rely on stack trace printing. One example is the Log4J API that provides logging services.

To set the stack trace support mode, use the <?php param('stackTraceSupport'); ?> configuration parameter:

<?php param_pattern('stackTraceSupport', 'stack-trace-mode'); ?> 

## Windows Version-Information Resource Configurations

On Windows, the plugin automatically adds a
[version-information resource](https://msdn.microsoft.com/en-us/library/windows/desktop/ms646981%28v=vs.85%29.aspx)
to the resulting executable. This can be disabled by specifying the following
configuration:

<?php param_value('addWindowsVersionInfo', 'false'); ?> 

By default, the values of version-information resource strings are derived from project settings.
The values of <?php param('product'); ?> and <?php param('vendor'); ?> configurations are used verbatim as
`ProductName` and `CompanyName` respectively;
other defaults can be changed using the <?php section('windowsVersionInfo'); ?> configuration section
that has the following parameters:

  * <?php param_pattern('version', 'version-string'); ?> 
  
    Version number (both `FileVersion` and `ProductVersion` strings are set to this same value)

    **Notice:** unlike <?php maven_gradle('Maven `${project.version}`', 'Gradle `project.version`'); ?>, this string
    must have format `v1.v2.v3.v4`, where vi is a number.
    The plugin would use heuristics to derive a correct version string from the specified value
    if the latter does not meet this requirement,
    or from `<?php maven_gradle('${project.version}', 'project.version'); ?>` if this configuration is not present.

  * <?php param_pattern('copyright', 'legal-copyright'); ?> 
  
    `LegalCopyright` string, with default value derived from other parameters

  * <?php param_pattern('description', 'executable-description'); ?> 
  
    `FileDescription` string, default is `<?php maven_gradle('${project.name}', 'project.name'); ?>`

