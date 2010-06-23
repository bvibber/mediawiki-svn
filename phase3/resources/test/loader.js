// Registers the modules with the loading system
mw.loader.register( 'test', ['foo'] );
mw.loader.register( 'foo', ['bar'] );
mw.loader.register( 'bar', ['buz', 'baz'] );
mw.loader.register( 'buz' );
mw.loader.register( 'baz' );