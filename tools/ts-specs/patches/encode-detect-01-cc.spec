--- Build.PL.old	Fri Jan 30 06:42:48 2009
+++ Build.PL	Fri Jan 30 06:52:37 2009
@@ -13,8 +13,8 @@
 	       },
      xs_files => { 'Detector.xs' => 'lib/Encode/Detect/Detector.xs' },
      dist_version_from => 'Detect.pm',
-     extra_compiler_flags => ['-x', 'c++', '-Iinclude'],
-     extra_linker_flags => ['-lstdc++'],
+     extra_compiler_flags => ['-Iinclude'],
+     extra_linker_flags => ['-lCstd', '-lCrun'],
     );
 $build->create_build_script;
 
