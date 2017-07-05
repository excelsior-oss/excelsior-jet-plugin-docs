The plugin enables you to configure the Excelsior JET Runtime via the <?php section('runtime'); ?> configuration section:

<?php if (MAVEN) : ?>
```xml
<runtime>
</runtime>
```
<?php elseif (GRADLE) : ?>
```gradle
runtime {
}
```
<?php endif; ?>

that may contain parameters described below.

## Contents

  * [Runtime Flavor Selection](#runtime-flavor-selection)
  * [Changing Default Runtime Location](#changing-default-runtime-location)
  * [Compact Profiles](#compact-profiles)
  * [Locales and Charsets](#locales-and-charsets)
  * [Optional Components](#optional-components)
  * [Disk Footprint Reduction](#disk-footprint-reduction)
  * [Java Runtime Slim-Down Configurations](#java-runtime-slim-down-configurations)


## Runtime Flavor Selection

Excelsior JET VM comes with multiple implementations of the runtime system,
optimized for different hardware configurations and application types.

To select a particular runtime flavor, use the <?php param('flavor'); ?> parameter of the <?php section('runtime'); ?> section.
The flavors available in the Enterprise Edition and the Evaluation Package are
`desktop`, `server`, and `classic`; other Excelsior JET products may not feature some of these.

For details, refer to the Excelsior JET User's Guide, Chapter *"Application
Considerations"*, section *"Runtime Selection"*.

## Changing Default Runtime Location

By default, Excelsior JET places its runtime files required for the
generated executable to work in a folder named `"rt"` located next to that executable.
You may change that default location with the <?php param('location'); ?> parameter of the <?php section('runtime'); ?> section.

**Note:** This functionality is only available in Excelsior JET 11.3 and above.

## Compact Profiles

Java SE 8 defines three subsets of the standard Platform API called compact profiles.
Excelsior JET enables you to deploy your application with one of those subsets.

To specify a particular profile, use the <?php param('profile'); ?> parameter of the <?php section('runtime'); ?> section.
The valid values are `auto` (default), `compact1`, `compact2`, `compact3`, and `full`.

<?php param_string('profile', 'auto'); ?> forces Excelsior JET to detect which parts of the Java SE Platform API are referenced
by the application and select the smallest compact profile that includes them all,
or the entire Platform API (`full`) if there is no such profile.

**Note:** This functionality is only available in Excelsior JET 11.3 and above.

## Locales and Charsets

Additional locales and character encoding sets that may potentially be in use in the regions
where you distribute your application can be added to the package with the following configuration:

<?php if (MAVEN) : ?>
```xml
<runtime>
  <locales>
    <locale>Locale1</locale>
    <locale>Locale2</locale>
  <locales>
</runtime>
```

You may specify `all` as the value of <?php param('locale'); ?> to add all locales and charsets at once or
`none` to not include any of them.
<?php elseif (GRADLE) : ?>
```gradle
runtime {
   locales = ["Locale"`, "Locale2"]
}
```

You may specify `["all"]` as the value of `locales` to add all locales and charsets at once or
`["none"]` to not include any of them.
<?php endif; ?>

The available sets of locales and encodings are:

`European`, `Indonesian`, `Malay`, `Hebrew`, `Arabic`, `Chinese`, `Japanese`, `Korean`, `Thai`,
`Vietnamese`, `Hindi`, `Extended_Chinese`, `Extended_Japanese`, `Extended_Korean`, `Extended_Thai`,
`Extended_IBM`, `Extended_Macintosh`, `Latin_3`

By default, only the `European` locales are added.

## Optional Components

To include optional JET Runtime components in the package, use the following configuration:

<?php if (MAVEN) : ?>
```xml
<runtime>
  <components>
    <component>optComponent1</component>
    <component>optComponent2</component>
  </components>
</runtime>
```

You may specify `all` as the value of <?php param('component'); ?> to add all components at once or
`none` to not include any of them.
<?php elseif (GRADLE) : ?>
```gradle
runtime {
    components = ["optComponent1", "optComponent2"]
}
```

You may specify `["all"]` as the value of `components` to add all components at once or
`["none"]` to not include any of them.
<?php endif; ?>


The available optional components are:

`runtime_utilities`, `fonts`, `awt_natives`, `api_classes`, `jce`, `jdk_tools`, `accessibility` (Windows only),
`javafx`, `javafx-webkit`, `javafx-swing`, `javafx-qtkit` (macOS only), `nashorn`, `cldr`, `dnsns`, `zipfs`

*Note:* by default, the plugin automatically includes the optional components which the compiler detected
   as used when building the executable(s).

## Disk Footprint Reduction

Excelsior JET is capable of reducing the disk footprint of an application
compiled with the [Global Optimizer](#global-optimizer) enabled, by compressing the (supposedly) unused Java SE API
classes.

To enable disk footprint reduction, add the following parameter to the <?php section('runtime'); ?> section:

<?php param_pattern('diskFootprintReduction', 'disk-footprint-reduction-mode'); ?> 

The available modes are:

* `none` - disable compression
* `medium` - use a simple compression with minimal run time overheads and selective decompression
* `high-memory` - (32-bit only) compress all class files as a whole, resulting in a more significant disk footprint reduction
                  compared to medium compression. The downside is that the entire bundle
                  has to be decompressed to retrieve a single class, if it turns out to be
                  required at run time. In the `high-memory` mode, the bundle is decompressed
                  onto the heap and can be garbage collected later.
* `high-disk` - (32-bit only) compress as in the `high-memory` mode, decompress to the temp directory

## Java Runtime Slim-Down Configurations

The 32-bit versions of Excelsior JET feature Java Runtime Slim-Down, a unique
Java application deployment model delivering a significant reduction
of application download size and disk footprint.

The key idea is to select the components of the Java SE API that are not used by the application,
and exclude them from the installation altogether. Such components are called *detached*.
For example, if your application does not use any of Swing, AWT, CORBA or, say, JNDI API,
Excelsior JET enables you to easily exclude from the main setup package the standard library
classes implementing those APIs and the associated files, placing them in a separate *detached package*.

The detached package should be placed on a Web server so that the JET Runtime could download it
if the deployed application attempts to use any of the detached components via JNI or the Reflection API.

**Note:** This functionality is deprecated in Excelsior JET 11.3
          in favor of the newly added [Compact Profiles](#compact-profiles) feature,
          and will be removed in future versions.

To enable Java Runtime Slim-Down, copy and paste the following plugin configuration:

<?php if (MAVEN) : ?>
```xml
<runtime>
    <slimDown>
        <detachedBaseURL></detachedBaseURL>
    </slimDown>
</runtime>
```
<?php elseif (GRADLE) : ?>
```gradle
runtime {
    slimDown {
        detachedBaseURL = ''
    }
}
```
<?php endif; ?>

and specify the base URL of the location where you plan to place the detached package, e.g.
`http://www.example.com/download/myapp/detached/`.

By default, the plugin automatically detects which Java SE APIs your application does not use
and detaches the respective JET Runtime components from the installation package.
Alternatively, you may enforce detaching of particular components using the following parameter
under the <?php section('slimDown'); ?> configuration section:

<?php param_pattern('detachComponents', 'comma-separated list of APIs'); ?> 

Available detachable components: `corba, management, xml, jndi, jdbc, awt/java2d, swing, jsound, rmi, jax-ws`

At the end of the build process, the plugin places the detached package
in the `jet` subdirectory of the <?php tool(); ?> target build directory.
You may configure its name with the <?php param('detachedPackage'); ?> parameter
of the <?php section('slimDown'); ?> section
(by default the name is `<?php maven_gradle('${project.build.finalName}.pkl', 'artifactName.pkl'); ?>`).

Do not forget to upload the detached package to the location specified
in <?php param('detachedBaseURL'); ?> above before deploying your application to end-users.

**Note:** Enabling Java Runtime Slim-Down automatically enables the Global Optimizer,
          so performing a Test Run is mandatory for Java Runtime Slim-Down as well.

**Fixed issue:** Java Runtime Slim-Down did not work with the `excelsior-installer` packaging type
                 due to a bug in Excelsior JET. This issue is fixed in Excelsior JET 11 Maintenance Pack 2.
