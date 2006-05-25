from distutils.core import setup, Extension

setup(name="dbzutil", version="1.0",
      ext_modules=[Extension("dbzutil", ["dbzutil-module.c"])])
