## Contents

  * [Method Inlining](#method-inlining)
  * [Allocating Objects on the Stack](#allocating-objects-on-the-stack)
  * [Startup Accelerator](#startup-accelerator)
  * [Global Optimizer](#global-optimizer)


## Method Inlining

When optimizing a Java program, the compiler often replaces method call statements with bodies of the methods
that would be called at run time. This optimization, known as method inlining, improves application performance,
especially when tiny methods, such as get/set accessors, are inlined.
However, inlining of larger methods increases code size, and its impact on performance may be uncertain.
To control the aggressiveness of method inlining, use the <?php param('inlineExpansion'); ?> plugin parameter:

<?php param_pattern('inlineExpansion', 'inline-expasnion-mode'); ?> 

The available modes are:
  `aggressive` (default), `very-aggressive`, `medium`, `low`, and `tiny-methods-only`

If you need to reduce the size of the executable, opt for the `low` or `tiny-methods-only` setting.
Note that it does not necessarily worsen application performance.

## Allocating Objects on the Stack
As you know, the Java memory model has no stack objects - class instances put on the stack frame. 
All objects have to be allocated on the heap by the new operator and reclaimed by the garbage collector, 
even though the lifetimes of some objects are obvious. 
The JET compiler performs so called escape analysis to approximate object lifetimes and allocate 
some objects and small arrays on the stack where possible. 
As a result, compiled applications benefit from both faster object allocation and less intensive garbage collection.

This optimization may however result in higher consumption of stack memory by application's threads. 
Therefore, in some cases, you need to increase the maximum stack size, which may compromise program's scalability. 
If you are compiling a server-side Java application that runs thousands of threads simultaneously, 
you may wish to disable this optimization using <?php param('stackAllocation'); ?> plugin parameter:

<?php param_pattern('stackAllocation', 'false'); ?> 

## Startup Accelerator

The Startup Accelerator improves the startup time of applications compiled with Excelsior JET.
The plugin automatically runs the compiled application immediately after build,
collects the necessary profile information and hard-wires it into the executable just created.
The JET Runtime will then use the information to reduce the application startup time.
The Startup Accelerator is enabled by default, but you may disable it by specifying the following
configuration:

<?php param_value('profileStartup', 'false'); ?> 

You may also specify the duration of the profiling session in seconds by specifying the following
configuration:

<?php param_pattern('profileStartupTimeout', 'duration-in-seconds'); ?> 

As soon as the specified period elapses, profiling stops and the application is automatically terminated,
so ensure that the timeout value is large enough to capture all actions the application normally carries out
during startup. (It is safe to close the application manually if the profiling period proves to be excessively long.)

If your application requires command-line arguments to run, set the `runArgs` plugin parameter
in the same way as for a [Test Run](#performing-a-test-run).

## Global Optimizer

The 32-bit versions of Excelsior JET feature the Global Optimizer - a powerful facility that has several
important advantages over the default compilation mode:

* single component linking yields an executable that does not require the dynamic libraries
  containing the standard Java library classes,
  thus reducing the size of the installation package and the disk footprint of the compiled application
* global optimizations improve application performance and reduce the startup time and memory usage

By default, Excelsior JET uses the *dynamic link model*. It only compiles application classes,
linking them into an executable that depends on dynamic libraries containing precompiled
Java SE platform classes. These dynamic libraries, found in the JET Runtime, have to be
distributed together with the executable.

The Global Optimizer detects the platform classes that are actually used by the application
and compiles them along with application classes into a single executable.
Even though the resulting binary occupies more disk space compared with the one built
in the default mode, it no longer requires the dynamic libraries with platform classes.
This results, among other benefits, in a considerable reduction of the application
installation package size.

To enable the Global Optimizer, add the following configuration parameter:

<?php param_value('globalOptimizer', 'true'); ?> 

**Note:** performing a Test Run is mandatory if the Global Optimizer is enabled.
