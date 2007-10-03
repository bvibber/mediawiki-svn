2007-05-28  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jheora/DCTDecode.java:
	(DCTDecode.....ExpandKFBlock), (DCTDecode.....ExpandBlock):
	* src/com/fluendo/jheora/Info.java: (Info):
	* src/com/fluendo/jheora/Playback.java: (Playback),
	(Playback.clearTmpBuffers), (Playback.initTmpBuffers),
	(Playback.initHuffmanTrees), (Playback.copyQTables):
	* src/com/fluendo/jheora/Quant.java: (Quant.readQTables), (Quant),
	(Quant.BuildQuantIndex_Generic), (Quant.init_dequantizer):
	Add support for 6 matrices in the decoder. Fixes #611.

2006-12-11  Wim Taymans  <wim@fluendo.com>

	Patch by: Tahseen Mohammad <tahseen dot mohammad at gmail dot com>

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux.....OggStream.OggChain.pushPage):
	Ignore annodex streams when discovering streams.

2006-11-21  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/player/Cortado.java: (Cortado...paint):
	  Log when appletDimension is wrong.
	  height == statusHeight should be acceptable.

2006-11-17  Michael Smith  <msmith@fluendo.com>

	* src/com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2.open):
	  Attempt to use Real Alsa Devices instead of the software mixer,
	  which doesn't provide good sync, if possible.

2006-11-16  Michael Smith  <msmith@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado...init):
	  Use seekable parameter instead of live to determine seekability.

2006-11-16  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado...actionPerformed),
	(Cortado...realRun), (Cortado...mouseDragged),
	(Cortado...mouseMoved), (Cortado...handleMessage),
	(Cortado...newState), (Cortado...newSeek), (Cortado...start):
	Handle resize correctly.

	* src/com/fluendo/plugin/VideoSink.java: (VideoSink.setCapsFunc),
	(VideoSink.render), (VideoSink), (VideoSink.getFactoryName),
	(VideoSink.setProperty):
	Fix keep-aspect. Added scale property.

2006-11-16  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Message.java: (Message), (Message.toString),
	(Message.newEOS), (Message.newError), (Message.newWarning),
	(Message.newStateDirty):
	Add duration message.

	* README:
	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado...getAppletInfo), (Cortado...getRevision),
	(Cortado...getParameterInfo), (Cortado...init),
	(Cortado...handleMessage):
	Make live and seekable enums so that they can be configured
	automatically on request.
	When we get a duration message, make the stream seekable and
	non-live in auto mode.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.getInputStream):
	Post duration message when the length is known.

2006-11-16  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/OggDemux.java: (OggDemux.....OggStream),
	(OggDemux.....OggStream.OggStream),
	(OggDemux.....OggStream.deActivate),
	(OggDemux.....OggStream.reStart):
	Get the time right for streams that don't start with timestamp 0.

2006-11-16  Wim Taymans  <wim@fluendo.com>

	* README:
	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado..getParameterInfo), (Cortado..init):
	* src/com/fluendo/player/Status.java: (Status),
	(Status.paintPercent), (Status.paintButton1), (Status.paintTime),
	(Status.paintSpeaker), (Status.paint), (Status.setMessage),
	(Status.setHaveAudio), (Status.setHavePercent),
	(Status.setSeekable), (Status.mousePressed),
	(Status.mouseReleased):
	Added a live property to disable PAUSE.

2006-11-16  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux.....OggStream.reStart):
	Stream time starts from 0

2006-11-03  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	* scripts/update-source-info:
	  ready for automated building

2006-11-03  Thomas Vander Stichele  <thomas at apestaart dot org>

	* cortado.spec:
	  delete
	* cortado.spec.in:
	  template for cortado.spec
	* scripts/update-spec-file:
	  add a script to generate cortado.spec from template, filling
	  in Version and Release information
	  Use Release tag to keep track of svn revision, branch and status
	  if the nano is set
	* build.xml:
	  add a spec target

2006-11-03  Thomas Vander Stichele  <thomas at apestaart dot org>

	* scripts/update-source-info:
	  add a script to generate src/com/fluendo/jst/SourceInfo.java
	  This file describes the repository version of the source code.
	* build.xml:
	  use the new script to generate SourceInfo.java
	* src/com/fluendo/player/Cortado.java:
	(Cortado.AboutFrame.AboutFrame):
	  Get the source info and display it
	This makes sure that a cortado built from a dist still has this
	revision information

2006-11-03  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad),
	(HTTPSrc.openWithConnection), (HTTPSrc.openWithSocket):
	Set default contentLength to -1 and take care we don't use invalid
	contentLength.

2006-11-02  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Status.java: (Status.setBufferPercent):
	Use correct variable...

2006-11-02  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Status.java: (Status.paint),
	(Status.intersectSeekbar), (Status.mouseMoved):
	Don't allow the user to press play/pause when we are buffering.

2006-11-02  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Status.java: (Status.paint):
	Don't paint the time if the applet is buffering.

2006-11-02  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Status.java: (Status.paint):
	Rearange status area, always show start/stop even if the file is not
	seekable.

2006-11-02  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado..stop):
	Ignore potential errors in shutdown.

2006-11-02  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado..init):
	Some more debug.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc), (HTTPSrc.Pad),
	(HTTPSrc.openWithConnection), (HTTPSrc.getInputStream),
	(HTTPSrc.getFactoryName):
	Keep track of offset, Don't rely on the server sending EOS.
	close connection on EOS.
	Work around EOS bug in MS JVM with twisted by not reading the last
	byte.

2006-11-01  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSink.java: (AudioSink.AudioClock),
	(AudioSink.AudioClock.setStarted), (AudioSink.RingBuffer.commit),
	(AudioSink.RingBuffer.setSample), (AudioSink.RingBuffer.pause),
	(AudioSink.RingBuffer.stop):
	Remove the lock of the clock to further avoid deadly embraces.

2006-11-01  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado..getParam),
	(Cortado..getBoolParam), (Cortado.), (Cortado..getDoubleParam),
	(Cortado..getIntParam), (Cortado..shutDown), (Cortado..init):
	Print parameter values and how they are parsed.
	Accept 1 as true for boolean parameters.

2006-11-01  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSink.java: (AudioSink.RingBuffer),
	(AudioSink.RingBuffer.setAutoStart):
	Fix potential deadly embrace.

2006-11-01  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.doSendEvent):
	Wait for preroll befre setting the state to PLAYING after a seek.

2006-11-01  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	Backport race fix in the sink where the state change function could have
	updated the state variables before the streaming thread did, resulting
	in an inconsistent state.

2006-11-01  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Sink.java: (Sink), (Sink.Pad), (Sink.query),
	(Sink.changeState):
	Make position reporting much more accurate.

2006-11-01  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Element.java: (Element.postMessage):
	cleanup

	* src/com/fluendo/jst/SystemClock.java: (SystemClock.waitFunc):
	When we are right on time, we return OK.

	* src/com/fluendo/player/Cortado.java: (Cortado..realRun),
	(Cortado..paint), (Cortado.), (Cortado..mousePressed),
	(Cortado..mouseReleased), (Cortado..start), (Cortado..stop):
	Try to limit the number of repaints.
	Cancel any pending mouse operations in the status bar when we release
	the mouse button outside of it.

	* src/com/fluendo/player/Status.java: (Status.paint),
	(Status.setHaveAudio), (Status), (Status.setHavePercent),
	(Status.setSeekable), (Status.intersectSeekbar),
	(Status.findComponent), (Status.mouseReleased),
	(Status.mouseDragged):
	Try to limit the number of repaints.

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* HACKING:
	* TODO:
	  add some notes
	* build.properties:
	* build.xml:
	* cortado.spec:
	  back to TRUNK
	* cortado.doap:
	  fix

=== release 0.2.2 ===

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* NEWS:
	* RELEASE:
	* build.properties:
	* cortado.doap:
	  Releasing 0.2.2, "Really Tested Verily Exceptionally"

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* TODO:
	* build.xml:
	* cortado.doap:
	* cortado.spec:
	  add doap file

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.build):
	  wrap completely in try block and use extra var

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.build):
	  Microsoft VM does not allow accessing http.agent, so try/catch

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  make sure building the player depends on distinfo target
	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.build):
	  add a space

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  separate dist-time variables in to separate class, since it
	  should be decided at dist time and not build time
	* scripts/get-branch:
	* scripts/get-revision:
	  make scripts work in the absence of .svn dirs, returning (unknown)
	* src/com/fluendo/player/Cortado.java:
	(Cortado.AboutFrame.AboutFrame):
	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.build):
	  Try to get the User-Agent to include http.agent if available

2006-10-26  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	* scripts/get-branch:
	* scripts/get-revision:
	  two scripts to help identify branch and revision/modification
	* src/com/fluendo/player/Cortado.java:
	(Cortado.AboutFrame.AboutFrame):
	* src/com/fluendo/player/CortadoPipeline.java: (CortadoPipeline),
	(CortadoPipeline.build):
	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc),
	(HTTPSrc.openWithConnection), (HTTPSrc.setProperty):
	  set User-Agent string so that we identify ourselves with
	  version of cortado, version of Java VM, and os name

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado..init):
	Disable seeking for MS JVM.

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/VideoSink.java: (VideoSink.setCapsFunc):
	Add comment.

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/VideoSink.java: (VideoSink.setCapsFunc):
	Don't mess with the component size, the parent does that for us.

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pipeline.java: (Pipeline.reCalcState):
	release lock when continuing the state change.

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pipeline.java: (Pipeline.doChildStateChange):
	More DEBUG.

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux.....OggStream.Pad):
	Make sure the streaming thread is not running when we do our cleanup.

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pipeline.java: (Pipeline.shutDown):
	Don't join the state thread, it might be performing a callback when
	being joined.

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pad.java: (Pad), (Pad.pauseTask):
	Take lock a little differently.

	* src/com/fluendo/jst/Pipeline.java: (Pipeline.shutDown):
	Don't try to join the bus thread as this might cause a deadly embrace.

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux.....OggStream.reStart):
	Add some debug for the segment/timestamps

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pad.java: (Pad.startTask):
	Avoid deadly embrace, the variable is now unprotected but that is fine.

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad), (HTTPSrc),
	(HTTPSrc.openWithConnection), (HTTPSrc.openWithSocket),
	(HTTPSrc.getInputStream):
	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux.....OggStream.OggChain.activate),
	(OggDemux.....OggStream.OggChain.deActivate),
	(OggDemux.....OggStream.OggChain.pushPage),
	(OggDemux.....OggStream.Pad):
	* src/com/fluendo/plugin/Queue.java: (Queue.Pad):
	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec..Pad):
	* src/com/fluendo/plugin/VorbisDec.java: (VorbisDec..Pad):
	Fix debug.
	Don't try to seek percentagesize when we don't know the total length.

2006-10-25  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pad.java: (Pad.startTask), (Pad.stopTask):
	Protect task state with streamLock.

	* src/com/fluendo/jst/Pipeline.java: (Pipeline.doChildStateChange):
	* src/com/fluendo/jst/Sink.java: (Sink.changeState):
	Add debug info for state changes.

	* src/com/fluendo/player/Cortado.java: (Cortado..handleMessage):
	Fix indentation.

	* src/com/fluendo/player/Status.java: (Status.mousePressed),
	(Status.mouseReleased), (Status.mouseMoved):
	Don't try to seek when we are stopped.

	* src/com/fluendo/plugin/AudioSink.java:
	(AudioSink.RingBuffer.setAutoStart):
	Fix potential deadly embracy between clock lock and ringbuffer lock.

	* src/com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2.delay):
	Add debug info.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad),
	(HTTPSrc.openWithConnection), (HTTPSrc), (HTTPSrc.openWithSocket),
	(HTTPSrc.getInputStream):
	Add more debug info.
	Catch seek failures in a better way.
	Add start of IE JVM compatible HTTP range requests. 

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux.....OggStream.Pad):
	Don't deactivate non-existing chains.

2006-10-24  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/player/Cortado.java: (Cortado..getAppletInfo),
	(Cortado..init), (Cortado.AppFrame.windowDeiconified),
	(Cortado.AppFrame), (Cortado.AppFrame.windowIconified),
	(Cortado.AppFrame.windowOpened), (Cortado),
	(Cortado.AboutFrame.AboutFrame):
	  trying to get svn revision in the output

2006-10-24  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Status.java: (Status.paint):
	Don't crash on null pointers.

2006-10-24  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pad.java: (Pad.run), (Pad), (Pad.startTask),
	(Pad.pauseTask):
	Catch and report Exceptions exiting the taskFunc instead of stopping the
	task.
	Fix possible race in shutting down a task.

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	Catch prerol/render Exception and post an error.

	* src/com/fluendo/player/Cortado.java: (Cortado..init),
	(Cortado..newSeek), (Cortado.), (Cortado..start):
	Add some more debug info.

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.doSendEvent):
	Refuse a seek when we are not ready yet

2006-10-24  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Object.java: (Object.Object):
	Call super constructor

	* src/com/fluendo/player/Cortado.java: (Cortado..getEnum),
	(Cortado..init), (Cortado..actionPerformed),
	(Cortado..getGraphics), (Cortado.), (Cortado..run),
	(Cortado..realRun), (Cortado..newState), (Cortado..newSeek),
	(Cortado..start):
	Remove a bunch of synchronized calls that could potentially lock
	up the AWT thread.
	Add synchronized calls to init/start/stop

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.getInputStream):
	Do some stuff differently.

2006-10-24  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Bus.java: (Bus), (Bus.post), (Bus.poll):
	Implement flush().

	* src/com/fluendo/jst/Pipeline.java: (Pipeline),
	(Pipeline.BusThread), (Pipeline.StateThread), (Pipeline.Pipeline):
	cleanup threads.

	* src/com/fluendo/jst/Pad.java: (Pad), (Pad.run):
	* src/com/fluendo/player/Cortado.java: (Cortado..start):
	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad),
	(HTTPSrc.getInputStream):
	* src/com/fluendo/plugin/Queue.java: (Queue.Pad):
	Give Threads a name.
	Don't send range for offset 0.

	* src/com/fluendo/utils/Debug.java: (Debug):
	Add code to generate unique ids.

2006-10-24  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	Add needed locks.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.getInputStream):
	Work around IE bug of parsing absolute URLS in a documentBase.

2006-10-24  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	Only push EOS on fatal errors so we don't screw up seeking.

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* README:
	Adds docs about hideTimeout param.

	* src/com/fluendo/player/Cortado.java: (Cortado..init),
	(Cortado..run), (Cortado.), (Cortado..realRun), (Cortado..paint),
	(Cortado..setStatusVisible), (Cortado..handleMessage),
	(Cortado..doPause), (Cortado..doPlay), (Cortado..doStop):
	Add hideTimeout param.
	Fix stop again.

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* README:
	Document javascrip methods.

	* src/com/fluendo/player/Cortado.java: (Cortado..handleMessage):
	Add doPlay(),doPause(),doStop(),doSeek() methods so that they can be
	used from javascript. Fixes #155

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jheora/YUVBuffer.java:
	(YUVBuffer.prepareRGBData):
	Catch Exceptions while doing color conversions. Fixes #283.

	* src/com/fluendo/player/Cortado.java: (Cortado..handleMessage):
	Don't overwrite error messages.

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.buildMultipart), (CortadoPipeline.cleanup):
	Post an error when the media type cannot be found.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	Post an error when streaming stops.

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado..handleMessage):
	Make sure we never overwrite error messages. Fixes #351.

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* README:
	Add showStatus param

	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado..getParameterInfo), (Cortado..getParam),
	(Cortado..shutdown), (Cortado.), (Cortado..init),
	(Cortado..realRun), (Cortado..paint), (Cortado..setStatusVisible),
	(Cortado..intersectStatus), (Cortado..mouseClicked),
	(Cortado..mouseEntered), (Cortado..mouseExited),
	(Cortado..mousePressed), (Cortado..mouseReleased),
	(Cortado..mouseDragged), (Cortado..mouseMoved),
	(Cortado..handleMessage), (Cortado..newState):
	Add showStatus param.
	Handle hiding/showing of status area based on param. Fixes #389.

	* src/com/fluendo/player/Status.java: (Status.setState):
	Don't error out when the statusbar is invisible when we hover over it.

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* README:
	Add new properties to README

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.handleMessage):
	Handle errors a bit better.

	* src/com/fluendo/player/CortadoPipeline.java: (CortadoPipeline),
	(CortadoPipeline.padAdded), (CortadoPipeline.cleanup):
	Do some more cleanups.

2006-10-23  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pipeline.java: (Pipeline),
	(Pipeline.enumSinks), (Pipeline.reCalcState),
	(Pipeline.doChildStateChange), (Pipeline.changeState):
	Catch, store, replace EOS messages and detect when all sinks posted
	EOS so that we can forward EOS to the app.

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	Remove locks, they are already taken.

	* src/com/fluendo/player/Cortado.java: (Cortado.handleMessage):
	Stop playback on EOS.

	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec..Pad):
	* src/com/fluendo/plugin/VorbisDec.java: (VorbisDec..Pad):
	Add some debugging in EOS.

2006-10-22  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado.init):
	* src/com/fluendo/player/CortadoPipeline.java: (CortadoPipeline),
	(CortadoPipeline.setComponent), (CortadoPipeline.getComponent),
	(CortadoPipeline.build):
	Set documentBase when configuring HTTPSrc.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc),
	(HTTPSrc.getInputStream), (HTTPSrc.HTTPSrc), (HTTPSrc.setProperty):
	Add documentBase property and parse the URL in that context. Fixes #467.

2006-10-22  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pad.java: (Pad):
	Add helper function.

	* src/com/fluendo/plugin/OggDemux.java: (OggDemux.....OggStream),
	(OggDemux.....OggStream.OggStream),
	(OggDemux.....OggStream.markDiscont),
	(OggDemux.....OggStream.reset),
	(OggDemux.....OggStream.isComplete),
	(OggDemux.....OggStream.activate),
	(OggDemux.....OggStream.pushPacket), (OggDemux.....OggStream.Pad):
	Combine the flow of all streams so that we don't crap out when only one
	stream is linked. Fixes #434 

2006-10-22  Wim Taymans  <wim@fluendo.com>

	* README:
	Review readme, Fixes #390.

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	Small cleanup.

	* src/com/fluendo/player/Cortado.java: (Cortado.newState):
	Remove superfluous setState call.

2006-10-22  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Element.java: (Element),
	(Element.getStateNext), (Element.continueState):
	Make state transitions more debuggable.
	Mark element as busy while it performs a state change.

	* src/com/fluendo/jst/Pipeline.java: (Pipeline),
	(Pipeline.Pipeline), (Pipeline.add), (Pipeline.remove),
	(Pipeline.SortedEnumerator.queueNextElement),
	(Pipeline.SinkEnumerator.nextElement), (Pipeline.SinkEnumerator),
	(Pipeline.enumSinks), (Pipeline.handleSyncMessage),
	(Pipeline.getState), (Pipeline.reCalcState):
	Handle switching of clock providers.
	Mark state dirty when removing elements.
	Add helper method to mark state dirty and force a state recalc.

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.padRemoved):
	Remove unused sinks after all media is detected. (Fixes #157)

2006-10-21  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pipeline.java: (Pipeline.doChildStateChange):
	changeState is supposed to be protected.

	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.getParameterInfo), (Cortado.init), (Cortado.run),
	(Cortado.start):
	Fix statusThread not working after going to STOP
	Move pipeline construction to the state change.

	* src/com/fluendo/player/CortadoPipeline.java: (CortadoPipeline),
	(CortadoPipeline.setupAudioDec), (CortadoPipeline.setupVideoDec),
	(CortadoPipeline.padAdded), (CortadoPipeline.setUrl),
	(CortadoPipeline.getUrl), (CortadoPipeline.setUserId),
	(CortadoPipeline.setPassword), (CortadoPipeline.enableAudio),
	(CortadoPipeline.isAudioEnabled), (CortadoPipeline.enableVideo),
	(CortadoPipeline.isVideoEnabled), (CortadoPipeline.setComponent),
	(CortadoPipeline.getComponent), (CortadoPipeline.setBufferSize),
	(CortadoPipeline.getBufferSize), (CortadoPipeline.setBufferLow),
	(CortadoPipeline.getBufferLow), (CortadoPipeline.setBufferHigh),
	(CortadoPipeline.getBufferHigh), (CortadoPipeline.buildOgg),
	(CortadoPipeline.buildMultipart), (CortadoPipeline.capsChanged),
	(CortadoPipeline.noSuchElement), (CortadoPipeline.build):
	Fix cleanup when going to STOP.
	Give better error messages for missing codecs.

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux.....OggStream.Pad):
	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	* src/com/fluendo/plugin/Queue.java: (Queue.Pad):
	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec..changeState):
	Do some more cleanups when stopping/starting.

2006-10-19  Wim Taymans  <wim@fluendo.com>

	Patch by <j at v2v dot cc>.

	* src/com/fluendo/jst/ElementFactory.java:
	Don't try to load the .ini file from the webserver. Fixes #387.

	* src/com/fluendo/player/Status.java: (Status.paintPlayPause),
	(Status.paintStop), (Status.paintSeekBar):
	Change the colors a bit to make the buttons more visible.

2006-10-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pipeline.java: (Pipeline), (Pipeline.doSeek):
	Improve handling of seek wrt state changes and errors.

	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.getParameterInfo), (Cortado.init), (Cortado.run),
	(Cortado.handleMessage), (Cortado.newState), (Cortado.newSeek),
	(Cortado.start):
	Add autoPlay option. Fixes #384.
	Simplify state management a little.

2006-10-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Status.java: (Status),
	(Status.intersectButton2), (Status.intersectSeeker),
	(Status.findComponent), (Status.mouseClicked),
	(Status.mouseEntered), (Status.mouseExited), (Status.mousePressed),
	(Status.mouseReleased), (Status.mouseDragged), (Status.mouseMoved):
	Allow seeking by clicking on the timeline instead of trying to grab the
	slider. Fixes #358.

2006-10-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jheora/DCTDecode.java: (DCTDecode.....CopyRecon),
	(DCTDecode.....CopyNotRecon), (DCTDecode.....ReconRefFrames):
	Move UpdateUMVBorder after the loop functions.

	* src/com/fluendo/jheora/iDCT.java: (iDCT.IDctSlow), (iDCT.IDct10):
	Update IDCT 16 bit truncating updates. 

	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.getParameterInfo), (Cortado.init), (Cortado.getGraphics),
	(Cortado.paint):
	Remove framerate property
	Added statusHeight property to configure the size of the status area.

	* src/com/fluendo/player/Status.java: (Status), (Status.paintStop),
	(Status.paintMessage), (Status.paintBuffering), (Status.paint),
	(Status.setHavePercent), (Status.setSeekable), (Status.setState),
	(Status.intersectButton1), (Status.intersectButton2),
	(Status.intersectSeek), (Status.mouseReleased),
	(Status.mouseDragged):
	Make statusHeight configurable.
	Make the slider handle a bit bigger.

=== release 0.2.1 ===

2006-09-14  Thomas Vander Stichele  <thomas at apestaart dot org>

	* NEWS:
	* RELEASE:
	* build.properties:
	  releasing 0.2.1, "Flapoorke"

2006-09-14  Thomas Vander Stichele  <thomas at apestaart dot org>

	patch by: <j at v2v dot cc>

	* README:
	  fix bufferSize default values 

2006-08-31  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  separate out install_applet, install_class, install_doc
	* cortado.spec:
	  add a first spec file

2006-08-23  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Message.java: (Message.newError):
	Add constructor for WARNING messages.

	* src/com/fluendo/plugin/OggDemux.java: (OggDemux),
	(OggDemux.....OggStream),
	(OggDemux.....OggStream.bufferFromPacket),
	(OggDemux.....OggStream.OggChain),
	(OggDemux.....OggStream.OggChain.OggChain),
	(OggDemux.....OggStream.OggChain.isActive),
	(OggDemux.....OggStream.OggChain.activate),
	(OggDemux.....OggStream.OggChain.deActivate),
	(OggDemux.....OggStream.OggChain.reStart),
	(OggDemux.....OggStream.OggChain.findStream),
	(OggDemux.....OggStream.OggChain.resetStreams),
	(OggDemux.....OggStream.OggChain.forwardEvent),
	(OggDemux.....OggStream.OggChain.pushPage),
	(OggDemux.....OggStream.Pad),
	(OggDemux.....OggStream.getFactoryName),
	(OggDemux.....OggStream.getMime):
	Improve how new streams are typefound and handled. 
	Added field to mark the stream type.
	Add skeleton and CMML stream detection very losely based on a
	patch by Tahseen Mohammad. 
	Skip unknown and non media streams.
	Fix compilation.
	Use startsWith in typefind to reduce code.

	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec..getMime):
	* src/com/fluendo/plugin/VorbisDec.java: (VorbisDec..getMime):
	Use startsWith to reduce code.

	* src/com/fluendo/utils/MemUtils.java: (MemUtils..cmp),
	(MemUtils..set):
	Add startsWith mostly to check for signatures in typefinding.
	Make functions final to improve inlining.

2006-07-07  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/OggDemux.java:
	Improve user experience (tm)

2006-07-07  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/OggDemux.java:
	Don't try to push events when we have no chain.
	Post an error if we run EOS and still haven't found a chain.

2006-05-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.properties:
	  back to TRUNK

=== release 0.2.0 ===

2006-05-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* HACKING:
	* NEWS:
	* README:
	* RELEASE:
	* build.xml:
	  make release target build everything, plus distcheck
	  add release notes, update other notes
	  Releasing 0.2.0, "Broken Record"

2006-05-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  add a release target

2006-05-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.config.sample:
	* build.properties:
	* build.xml:
	  change "release" type to "stripped" type

2006-05-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  now actually fix make distcheck by disting jst code

2006-05-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  fix distcheck; it was actually building inplace and thus
	  not catching missing jst code

2006-05-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* README:
	  update properties in use
	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.getParameterInfo), (Cortado.shutdown), (Cortado.init),
	(AboutFrame.AboutFrame):
	* src/com/fluendo/player/Status.java:
	  use a double for duration, is more intuitive than HMMSS
	  only mention audio backend details if the applet is configured to
	  play audio
	  show VM information in about box

2006-05-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.getParameterInfo), (Cortado.init):
	Remove some unused properties

2006-05-17  Zaheer Abbas Merali  <zaheerabbas at merali dot org>

	* src/com/fluendo/player/Cortado.java: (Cortado.stringToTime):
	Bounds checking!

2006-03-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2.write):
	* src/com/fluendo/plugin/AudioSinkSA.java:
	(AudioSinkSA...RingReader.RingReader):
	  fix more warnings, one error left

2006-03-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/plugin/AudioSink.java:
	(AudioSink.RingBuffer.acquire):
	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux...OggStream.pushPacket):
	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec.),
	(TheoraDec..getFirstTs):
	* src/com/fluendo/plugin/VorbisDec.java: (VorbisDec.),
	(VorbisDec..getFirstTs):
	  Fix some warnings

2006-03-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  apparently 1.2 is not accepted, and it needs to be 1.3 ? Hope this
	  is ok, need to double check on 1.1 VM's.

2006-03-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  add source="1.2" to javac targets, because some javac's and ant
	  complain about this

2006-03-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad), (HTTPSrc),
	(HTTPSrc.getInputStream):
	Do typefind on the data instead of trusting the servers
	contentType. Fixes #317

	* src/com/fluendo/plugin/OggDemux.java: (OggDemux),
	(OggDemux...OggStream.getMime):
	Implement typeFind function.

2006-03-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	  removed
	* build.xml:
	  added dist, distcheck and distclean targets

2006-03-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pad.java: (Pad.Pad), (Pad.eventFunc), (Pad),
	(Pad.sendEvent):
	Don't crash when printing a pad name without parent.
	Grab streamLock when needed.

	* src/com/fluendo/player/Cortado.java: (Cortado.init):
	Build pipeline after we have the needed infrastructure to
	handle errors and such.

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.padAdded), (CortadoPipeline.setBufferLow),
	(CortadoPipeline), (CortadoPipeline.getBufferLow),
	(CortadoPipeline.setBufferHigh), (CortadoPipeline.getBufferHigh),
	(CortadoPipeline.buildOgg), (CortadoPipeline.buildMultipart),
	(CortadoPipeline.capsChanged), (CortadoPipeline.noSuchElement),
	(CortadoPipeline.build):
	Post an error when we cannot create an element.

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux..OggStream.deActivate), (OggDemux..OggStream.pushPacket),
	(OggDemux..OggStream.OggChain.isActive),
	(OggDemux..OggStream.OggChain),
	(OggDemux..OggStream.OggChain.activate), (OggDemux..OggStream.Pad):
	First remove pad so we can throw away the eos event.
	Create new instance of the payload
	Fire noMorePads.
	post error messages when something is wrong.
	No need to take streamLock, core does that now.

	* src/com/fluendo/plugin/JPEGDec.java: (JPEGDec.Pad):
	* src/com/fluendo/plugin/MulawDec.java: (MulawDec.Pad):
	* src/com/fluendo/plugin/SmokeDec.java: (SmokeDec.Pad):
	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec..Pad):
	* src/com/fluendo/plugin/VorbisDec.java: (VorbisDec..Pad):
	No need to take streamLock, core does that for us.

	* src/com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2.open):
	Post better error message.

	* src/com/fluendo/plugin/Queue.java: (Queue.Pad), (Queue):
	Stop flow on EOS.
	No need for streamLock.

2006-03-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/examples/Player2.java:
	  removed
	* src/com/fluendo/examples/Player3.java:
	  renamed to Player.java

2006-03-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* README:
	  update
	* build.xml:
	  make sure that "all" target builds in order, so the examples work

2006-03-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Clock.java: (Clock):
	Time are longs, avoids overflows.

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	First pass at posting EOS.

	* src/com/fluendo/player/Cortado.java: (Cortado.handleMessage):
	Some more debugging about buffering and EOS.

	* src/com/fluendo/plugin/OggDemux.java:
	(OggDemux..OggStream.bufferFromPacket):
	Timestamp outgoing buffers.

	* src/com/fluendo/plugin/Queue.java: (Queue.updateBuffering),
	(Queue.Pad):
	Handle EOS when we are buffering. Fixes #247.

	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec..takeHeader),
	(TheoraDec..getFirstTs), (TheoraDec..Pad):
	Don't try to convert granulepos if we did not receive headers
	yet.

2006-03-20  Wim Taymans  <wim@fluendo.com>

	* TODO:
	Updated TODO.

	* src/com/fluendo/jst/Sink.java: (Sink.Pad):
	Set flushing flag correctly when we shutdown.

	* src/com/fluendo/plugin/AudioSink.java: (AudioSink.RingBuffer),
	(AudioSink.RingBuffer.acquire), (AudioSink.RingBuffer.setSample),
	(AudioSink.setCapsFunc), (AudioSink), (AudioSink.changeState):
	Fix race in going to paused.
	Don't deadlock when we try to release the audio device.

2006-02-28  Andy Wingo  <wingo@pobox.com>

	* TESTIMONIALS: New file, random testimonials culled from the
	mailing list.

2006-01-23  Wim Taymans  <wim@fluendo.com>

	* TODO:
	Updated TODO

	* src/com/fluendo/examples/Player2.java:
	(PlayPipeline.doSendEvent):
	Fix for API removal.

	* src/com/fluendo/jst/Buffer.java: (Buffer), (Buffer.create),
	(Buffer.Buffer):
	Added buffer flags.
	Prepare for using timestampEnd.

	* src/com/fluendo/jst/Event.java: (Event),
	Update newsegment event info to match GStreamer.

	(Event.parseSeekPosition), (Event.parseSeekFormat):
	* src/com/fluendo/jst/Pipeline.java: (Pipeline.doChildStateChange):
	Remove preroll hack.

	* src/com/fluendo/jst/Sink.java: (Sink), (Sink.Pad), (Sink.doSync),
	(Sink.query), (Sink.changeState):
	Remove preroll hack, parse segment info. Clip buffers outside of
	the segment. Handle DISCONT and clocking.
	
	* src/com/fluendo/plugin/AudioSink.java:
	(AudioSink.RingBuffer.commit), (AudioSink.doEvent):
	Clip around segments.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc), (HTTPSrc.Pad),
	(HTTPSrc.getInputStream):
	* src/com/fluendo/plugin/OggDemux.java: (OggDemux),
	(OggDemux..OggStream), (OggDemux..OggStream.OggStream),
	(OggDemux..OggStream.deActivate), (OggDemux..OggStream.reStart),
	(OggDemux..OggStream.getFirstTs),
	(OggDemux..OggStream.bufferFromPacket),
	(OggDemux..OggStream.pushPacket), (OggDemux..OggStream.pushPage),
	(OggDemux..OggStream.eventFunc), (OggDemux..OggStream.OggChain),
	(OggDemux..OggStream.OggChain.OggChain),
	(OggDemux..OggStream.OggChain.isActive),
	(OggDemux..OggStream.OggChain.activate),
	(OggDemux..OggStream.OggChain.deActivate),
	(OggDemux..OggStream.OggChain.reStart),
	(OggDemux..OggStream.OggChain.addStream),
	(OggDemux..OggStream.OggChain.markDiscont),
	(OggDemux..OggStream.OggChain.findStream),
	(OggDemux..OggStream.OggChain.resetStreams),
	(OggDemux..OggStream.OggChain.forwardEvent),
	(OggDemux..OggStream.OggChain.pushPage), (OggDemux..OggStream.Pad),
	(OggDemux..OggStream.getFactoryName),
	(OggDemux..OggStream.getMime), (OggDemux..OggStream.typeFind),
	(OggDemux..OggStream.OggDemux):
	Preroll sync and calc timestamps of payloads.
	Added preliminary support for chained oggs.
	Handle discont.
	
	* src/com/fluendo/plugin/OggPayload.java:
	Interface for payloads in ogg containers.

	* src/com/fluendo/plugin/TheoraDec.java: (TheoraDec..Pad),
	(TheoraDec.), (TheoraDec..TheoraDec), (TheoraDec..changeState),
	(TheoraDec..getFactoryName), (TheoraDec..getMime):
	* src/com/fluendo/plugin/VorbisDec.java: (VorbisDec),
	(VorbisDec..Pad), (VorbisDec.), (VorbisDec..VorbisDec),
	(VorbisDec..changeState), (VorbisDec..getFactoryName),
	(VorbisDec..getMime):
	Implement OggPayload.
	Handle discont.
	Remove timestamp hacks.

2006-01-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	* src/com/fluendo/player/Cortado.java: (Cortado.init),
	(Cortado.stop):
	* src/com/fluendo/player/CortadoPipeline.java: (CortadoPipeline),
	(CortadoPipeline.build):
	* src/com/fluendo/plugin/AudioSinkSA.java: (AudioSinkSA):
	  added an about dialog

2006-01-20  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  add time of build as well, useful when debugging builds

2006-01-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSinkSA.java:
	(AudioSinkSA...RingReader.read),
	(AudioSinkSA...RingReader.RingBufferSA.read):
	Change read behaviour to please both IE and jdk

2006-01-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pad.java: (Pad.setCaps):
	Don't call the listeners with null caps.

	* src/com/fluendo/plugin/AudioSinkSA.java:
	(AudioSinkSA...RingReader.stop):
	Some debugging

2006-01-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSinkSA.java: (AudioSinkSA),
	(AudioSinkSA...toUlaw), (AudioSinkSA...RingReader.pause),
	(AudioSinkSA...RingReader.stop):
	Redid sun.audio backend to work on bytes and do more
	accurate resampling.

2006-01-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSinkSA.java:
	(AudioSinkSA...RingReader.stop):
	Convert byte to int.

2006-01-20  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSinkSA.java:
	(AudioSinkSA...RingReader), (AudioSinkSA...RingReader.RingReader),
	(AudioSinkSA...RingReader.pause), (AudioSinkSA...RingReader.stop),
	(AudioSinkSA...RingReader.read),
	(AudioSinkSA...RingReader.RingBufferSA.play),
	(AudioSinkSA...RingReader.RingBufferSA),
	(AudioSinkSA...RingReader.RingBufferSA.pause),
	(AudioSinkSA...RingReader.RingBufferSA.stop),
	(AudioSinkSA...RingReader.RingBufferSA.read):
	Implement read() for IE JVM.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* TODO:
	* src/com/fluendo/plugin/Queue.java: (Queue), (Queue.clearQueue),
	(Queue.Pad):
	Handle EOS gracefully.

2006-01-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/jst/Bus.java: (Bus.peek):
	* src/com/fluendo/jst/Pipeline.java: (Pipeline.SortedEnumerator),
	(Pipeline.SortedEnumerator.queueNextElement):
	* src/com/fluendo/player/Status.java:
	* src/com/fluendo/plugin/Queue.java: (Queue.Pad):
	  Fix Vector calls to work with 1.1 API - partially fixes applet
	  in Explorer

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSinkSA.java:
	(AudioSinkSA...RingReader.stop):
	Print some errors when unimplemented methods are called.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado.paint):
	Don't try to render with bogus width/height values.

2006-01-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* README:
	* TODO:
	  update
	* build.xml:
	  build non-jikes with correct debug as well

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Pipeline.java: (Pipeline.useClock),
	(Pipeline.add), (Pipeline), (Pipeline.remove):
	Don't accept null elements in add and remove.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado), (Cortado.init),
	(Cortado.handleMessage):
	Cleanups.
	Pause pipeline while buffering.
	
	* src/com/fluendo/plugin/AudioSink.java:
	(AudioSink.RingBuffer.setSample), (AudioSink.RingBuffer.play),
	(AudioSink.RingBuffer.pause):
	* src/com/fluendo/plugin/AudioSinkJ2.java:
	Compile fixes.

	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	Post stream status messages.

	* src/com/fluendo/plugin/Queue.java: (Queue.updateBuffering),
	(Queue.Pad):
	Never pause the stream when we are buffering so that we can
	preroll while buffering.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/Cortado.java: (Cortado.stringToTime),
	(Cortado.handleMessage):
	Update buffering info.

	* src/com/fluendo/player/Status.java: (Status):
	Print buffering nicely.

	* src/com/fluendo/plugin/AudioSink.java:
	(AudioSink.RingBuffer.stop):
	* src/com/fluendo/plugin/AudioSinkSA.java: (AudioSinkSA),
	(AudioSinkSA...RingReader.RingBufferSA),
	(AudioSinkSA...RingReader.RingBufferSA.startWriteThread),
	(AudioSinkSA...RingReader.open), (AudioSinkSA...RingReader.write):
	Some cleanups.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.build):
	Autodetect audio backend.

	* src/com/fluendo/plugin/AudioSinkSA.java:
	(AudioSinkSA...RingReader.createRingBuffer),
	(AudioSinkSA...RingReader.write):
	Fix sync in sun.audio backend.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/plugin/AudioSink.java: (AudioSink),
	(AudioSink.AudioClock.getInternalTime), (AudioSink.RingBuffer.run),
	(AudioSink.RingBuffer.acquire),
	(AudioSink.RingBuffer.samplesPlayed), (AudioSink.doEvent):
	* src/com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2),
	(AudioSinkJ2.open), (AudioSinkJ2.close):
	* src/com/fluendo/plugin/AudioSinkSA.java:
	* src/com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.getInputStream):
	* src/com/fluendo/plugin/MultipartDemux.java:
	* src/com/fluendo/plugin/OggDemux.java: (OggDemux.Pad):
	* src/com/fluendo/plugin/Queue.java:
	Code cleanups. Remove unused vars.
	Calculate delay more accurately in javax.sound backend.
	Don't set caps on HTTPSrc when already set.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/examples/Player2.java:
	* src/com/fluendo/examples/Player3.java:
	* src/com/fluendo/player/Cortado.java:
	* src/com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.build):
	* src/com/fluendo/player/Status.java:
	Code reformatting and cleanups.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jst/Buffer.java: (Buffer.ensureSize):
	* src/com/fluendo/jst/Pad.java: (Pad):
	* src/com/fluendo/jst/Pipeline.java: (Pipeline):
	Remove unused vars.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/codecs/SmokeCodec.java: (SmokeCodec),
	(SmokeCodec.parseHeader):
	Fix unused vars.

2006-01-19  Wim Taymans  <wim@fluendo.com>

	* src/com/fluendo/jheora/Colorspace.java:
	* src/com/fluendo/jheora/DCTDecode.java:
	(DCTDecode.....ReconRefFrames):
	* src/com/fluendo/jheora/Decode.java:
	* src/com/fluendo/jheora/Filter.java: (Filter..LoopFilter):
	* src/com/fluendo/jheora/JTheoraException.java:
	* src/com/fluendo/jheora/Playback.java:
	* src/com/fluendo/jheora/Quant.java:
	Remove lots of unused vars

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  compile is now a virtual target

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* HACKING:
	  expand on the file layout
	* src/com/fluendo/examples/OggPerf.java:
	* src/com/fluendo/examples/OggTheoraPerf.java:
	* src/com/fluendo/examples/Player2.java:
	* src/com/fluendo/examples/Player3.java:
	* src/com/fluendo/jst/Sink.java:
	* src/com/fluendo/plugin/VideoSink.java:
	  more build cleanups

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  remove plugins.ini rule
	  invoke includes rule for applet builds and compile-examples

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  fix up various build issues; can now run the Player2 example with
	  java --cp output/build/debug com.fluendo.examples.Player2 http://stream.fluendo.com:8800

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  various build fixes so that plugins are correctly found
	  add compile-examples

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.properties:
	* build.xml:
	  various cleanups for the build
	* src/com/fluendo/jst/Pipeline.java:
	* src/com/fluendo/jst/Sink.java:
	* src/com/fluendo/plugin/Sink.java:
	  move Sink.java to jst, as it's used in Pipeline

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.config.sample:
	  add a sample build.config file to customize
	* build.xml:
	  fix up clean rule
	  add more jars

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	* build.xml:
	* gen-Configure:
	  remove gen-Configure; implement it in ant; output during build

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  split out more of the jars and compiles

2006-01-18  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  always include stubs in classpath for now;
	  see what this does with the Sun compiler
	* src/com/fluendo/jst/Element.java:
	* src/com/fluendo/plugin/AudioSink.java:
	  remove unneeded imports

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  clean up rules further
	  set out.build to a build-type-specific location for all rules
	  add applet and jar rules that build all of them
	  add debug and release targets

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* HACKING:
	* Makefile:
	* build.properties:
	* build.xml:
	  allow for debug and release builds
	  various cleanups, learnt about ant -projecthelp, looks nice now

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/jst/Element.java: (Element):
	* src/com/fluendo/jst/Object.java: (Object):
	* src/com/fluendo/jst/Pad.java: (Pad):
	* src/com/fluendo/jst/Pipeline.java: (Pipeline),
	(Pipeline.SortedEnumerator.updateDegree):
	* src/com/fluendo/plugin/Sink.java: (Sink.changeState):
	  further compile cleanups

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  make compile-jcraft and jar-jcraft targets and shut up warnings
	  for them

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* src/com/fluendo/codecs/SmokeCodec.java: (SmokeCodec):
	* src/com/fluendo/jheora/DCTDecode.java:
	(DCTDecode.....UpdateUMV_HBorders):
	* src/com/fluendo/jheora/Info.java: (Info._tp_readlsbint):
	* src/com/fluendo/jheora/iDCT.java: (iDCT.IDct10):
	* src/com/fluendo/jst/Element.java: (Element.getPad):
	* src/com/fluendo/jst/Pad.java: (Pad.doCapsListeners):
	* src/com/fluendo/jst/Pipeline.java: (Pipeline.doChildStateChange):
	* src/com/fluendo/player/CortadoPipeline.java: (CortadoPipeline):
	  remove unused vars

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.properties:
	* src/com/fluendo/codecs/SmokeCodec.java:
	* src/com/fluendo/jheora/DCTDecode.java:
	* src/com/fluendo/jheora/Info.java:
	* src/com/fluendo/jheora/YUVBuffer.java:
	* src/com/fluendo/jst/Element.java:
	* src/com/fluendo/jst/Event.java:
	* src/com/fluendo/jst/Message.java:
	* src/com/fluendo/jst/Pipeline.java:
	* src/com/fluendo/jst/Query.java:
	* src/com/fluendo/player/Cortado.java:
	* src/com/fluendo/player/CortadoPipeline.java:
	* src/com/fluendo/player/Status.java:
	* src/com/fluendo/plugin/JPEGDec.java:
	* src/com/fluendo/plugin/Sink.java:
	* src/com/fluendo/plugin/SmokeDec.java:
	* src/com/fluendo/plugin/TheoraDec.java:
	  remove unneeded imports

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* merged "build" branch:
	  svn merge -r 2703:HEAD branches/build/ trunk/

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	* build.properties:
	* build.xml:
	  add build.properties
	  merge sun and jikes build in one, should now be adaptable
	  to all compilers

2006-01-17  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	* build.xml:
	* HACKING:
	  reorganize build further.  "make ovt" and "make mmjs" seem to
	  produce applets. add HACING notes

2006-01-16  Thomas Vander Stichele  <thomas at apestaart dot org>

	* custom/mp-jpeg.sh:
	* custom/theora-ogg.sh:
	  remove old custom scripts

2006-01-10  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/player/CortadoPipeline.java: (CortadoPipeline.build):
	Enable sun.audio backend.

	* com/fluendo/plugin/AudioSink.java: (AudioSink.provideClock),
	(AudioSink.RingBuffer.run), (AudioSink.RingBuffer.setFlushing),
	(AudioSink.RingBuffer.startWriteThread), (AudioSink.RingBuffer),
	(AudioSink.RingBuffer.acquire), (AudioSink.RingBuffer.release),
	(AudioSink.RingBuffer.waitSegment), (AudioSink.RingBuffer.commit),
	(AudioSink.RingBuffer.samplesPlayed):
	Make fields protected.
	Abstract the reader thread.

	* com/fluendo/plugin/AudioSinkSA.java:
	* plugins.ini:
	Added sun.audio backend.

2006-01-09  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Caps.java: (Caps), (Caps.Caps), (Caps.toString),
	(Caps.setField), (Caps.setFieldInt), (Caps.getField),
	(Caps.getFieldInt):
	Parse extra properties on caps.

	* com/fluendo/jst/CapsListener.java:
	Added capslistener.

	* com/fluendo/jst/Pad.java: (Pad), (Pad.Pad), (Pad.toString),
	(Pad.query), (Pad.getCaps), (Pad.setCapsFunc), (Pad.setCaps):
	Notify capslisteners when caps changed.

	* com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.padAdded), (CortadoPipeline.getComponent),
	(CortadoPipeline), (CortadoPipeline.setBufferSize),
	(CortadoPipeline.getBufferSize), (CortadoPipeline.setBufferLow),
	(CortadoPipeline.getBufferLow), (CortadoPipeline.setBufferHigh),
	(CortadoPipeline.getBufferHigh), (CortadoPipeline.buildOgg),
	(CortadoPipeline.build):
	Autoplug based on mime type.

	* com/fluendo/player/Status.java:
	Don't show unknown position values.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc), (HTTPSrc.Pad),
	(HTTPSrc.getInputStream):
	Parse mimetype and set as caps on the buffers and pad.

	* com/fluendo/plugin/MultipartDemux.java:
	(MultipartDemux.MultipartStream), (MultipartDemux),
	(MultipartDemux.Pad), (MultipartDemux.getFactoryName):
	Parse input caps and use caps boundary string field.

2006-01-09  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/ElementFactory.java:
	Fix deprecated method.

	* com/fluendo/jst/Pipeline.java: (Pipeline.calcPrerollTime):
	Max/min syncOffset handling.

	* com/fluendo/plugin/AudioSink.java: (AudioSink.RingBuffer.stop),
	(AudioSink.render):
	* com/fluendo/plugin/Sink.java: (Sink.Pad), (Sink), (Sink.preroll),
	(Sink.doEvent):
	Don't render buffers before the syncOffset.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	Remove debug.

2006-01-05  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/examples/Player3.java: (Player3.Player3):
	New testcase.

	* com/fluendo/jst/ElementFactory.java:
	Print real element names.

	* com/fluendo/jst/Pipeline.java: (Pipeline.calcPrerollTime),
	(Pipeline.changeState):
	Remove debugging.

	* com/fluendo/player/Cortado.java: (Cortado.handleMessage):
	More debugging.

	* com/fluendo/plugin/AudioSink.java:
	(AudioSink.RingBuffer.acquire), (AudioSink.RingBuffer.commit),
	(AudioSink.RingBuffer), (AudioSink.RingBuffer.samplesPlayed):
	* com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2.open):
	Make sure we have at least 4 segments to work with.
	Detect disconts.

	* com/fluendo/plugin/Sink.java: (Sink.doSync):
	Small cleanups.


2006-01-05  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Clock.java: (Clock):
	* com/fluendo/player/Cortado.java: (Cortado.handleMessage):
	Small cleanups.

	* com/fluendo/jst/Element.java: (Element.getMime):
	An element must provide a factoryName.

	* com/fluendo/jst/ElementFactory.java:
	Allow setting custom element names.

	* com/fluendo/jst/Message.java: (Message.toString),
	(Message.newStateDirty):
	More info in stream status messages.

	* com/fluendo/jst/Pad.java: (Pad.isFlowFatal):
	Make flowname usable by other classes.

	* com/fluendo/jst/Pipeline.java: (Pipeline.Pipeline),
	(Pipeline.calcPrerollTime), (Pipeline), (Pipeline.changeState):
	Calc syncOffset and distribute after a seek.

	* com/fluendo/examples/Player2.java: (PlayPipeline):
	* com/fluendo/player/CortadoPipeline.java: (CortadoPipeline),
	(CortadoPipeline.buildOgg), (CortadoPipeline.build):
	Name elements properly.

	* com/fluendo/plugin/AudioSink.java: (AudioSink.AudioClock),
	(AudioSink.AudioClock.getInternalTime),
	(AudioSink.RingBuffer.commit),
	(AudioSink.RingBuffer.samplesPlayed), (AudioSink.RingBuffer),
	(AudioSink.RingBuffer.clear), (AudioSink.RingBuffer.clearAll),
	(AudioSink.RingBuffer.setSample), (AudioSink.RingBuffer.play),
	(AudioSink.RingBuffer.pause), (AudioSink.doEvent), (AudioSink),
	(AudioSink.render), (AudioSink.setCapsFunc),
	(AudioSink.changeState):
	* com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2):
	* com/fluendo/plugin/FakeSink.java: (FakeSink.render):
	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad),
	(HTTPSrc.getInputStream):
	* com/fluendo/plugin/JPEGDec.java: (JPEGDec.getProperty):
	* com/fluendo/plugin/MulawDec.java: (MulawDec.MulawDec):
	* com/fluendo/plugin/MultipartDemux.java: (MultipartDemux.Pad):
	* com/fluendo/plugin/OggDemux.java: (OggDemux.Pad):
	* com/fluendo/plugin/Queue.java: (Queue.updateBuffering), (Queue),
	(Queue.start), (Queue.Pad), (Queue.Queue):
	* com/fluendo/plugin/Sink.java: (Sink), (Sink.getPrerollTime),
	(Sink.Pad), (Sink.doSync), (Sink.query), (Sink.changeState):
	* com/fluendo/plugin/SmokeDec.java: (SmokeDec.getProperty):
	* com/fluendo/plugin/TheoraDec.java: (TheoraDec.changeState):
	* com/fluendo/plugin/VideoSink.java: (VideoSink.render):
	* com/fluendo/plugin/VorbisDec.java: (VorbisDec.changeState):
	Fix factory names.
	Remove ringbuffer sync hack.
	Preroll and syncOffset implemented in Sink.
	Post stream status.
	Don't run the clock when we are paused in the audiosink.


2006-01-04  Wim Taymans  <wim@fluendo.com>

	* Makefile:
	Some updates.
	Compile with deprecation warnings.

	* com/fluendo/player/CortadoPipeline.java:
	(CortadoPipeline.padAdded), (CortadoPipeline.buildOgg),
	(CortadoPipeline.buildMultipartJPEG),
	(CortadoPipeline.buildMultipartSmoke), (CortadoPipeline),
	(CortadoPipeline.build):
	Added Smoke pipeline,

	* com/fluendo/plugin/MultipartDemux.java: (MultipartDemux.Pad):
	Small cleanup.

	* com/fluendo/plugin/SmokeDec.java: (SmokeDec.Pad), (SmokeDec),
	(SmokeDec.SmokeDec):
	Add property to configure component.

	* com/fluendo/plugin/VideoSink.java: (VideoSink.setCapsFunc):
	Replace deprecated method.

	* plugins.ini:
	Add smoke plugins to registry.

2005-12-21  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/examples/Player3.java: (Player3.Player3):
	Enable multipart.

	* com/fluendo/jst/Pad.java: (Pad):
	Return NOT_LINKED when there is no peer.

	* com/fluendo/player/CortadoPipeline.java: (CortadoPipeline),
	(CortadoPipeline.padAdded), (CortadoPipeline.setBufferHigh),
	(CortadoPipeline.getBufferHigh), (CortadoPipeline.buildOgg),
	(CortadoPipeline.buildMultipart), (CortadoPipeline.build):
	Check for null caps.
	Add multipart jpeg pipelines,

	* com/fluendo/plugin/JPEGDec.java: (JPEGDec), (JPEGDec.Pad),
	(JPEGDec.JPEGDec), (JPEGDec.changeState):
	Send images downstream after we created them in the media tracker.

	* com/fluendo/plugin/MulawDec.java: (MulawDec), (MulawDec.Pad),
	(MulawDec.MulawDec):
	Make variable names java-like.

	* com/fluendo/plugin/MultipartDemux.java: (MultipartDemux),
	(MultipartDemux.MultipartStream),
	(MultipartDemux.MultipartStream.MultipartStream),
	(MultipartDemux.MultipartStream.eventFunc), (MultipartDemux.Pad),
	(MultipartDemux.getName), (MultipartDemux.getMime),
	(MultipartDemux.typeFind), (MultipartDemux.MultipartDemux):
	Implement the multipart demuxer.


2005-12-20  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Event.java: (Event.newSeek),
	(Event.newNewsegment):
	Make method names consistent as in Query/Message.

	* com/fluendo/jst/Pipeline.java: (Pipeline.doChildStateChange),
	(Pipeline.calcPrerollTime), (Pipeline), (Pipeline.changeState),
	(Pipeline.doSendEvent), (Pipeline.doSeek), (Pipeline.sendEvent),
	(Pipeline.query):
	Do correct preroll time distribution in pipeline.

	* com/fluendo/player/Cortado.java: (Cortado.newState),
	(Cortado.newSeek):
	Warn when seek failed.

	* com/fluendo/player/CortadoPipeline.java:
	The pipeline used by the applet.

	* com/fluendo/player/StatusListener.java:
	Get info on actions performed in the Status area.

	* com/fluendo/plugin/JPEGDec.java:
	* com/fluendo/plugin/MulawDec.java:
	* com/fluendo/plugin/MultipartDemux.java:
	* com/fluendo/plugin/SmokeDec.java:
	* plugins.ini:
	New plugins added.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	* com/fluendo/plugin/Sink.java: (Sink.Pad):
	Update for name change.

2005-12-19  Wim Taymans  <wim@fluendo.com>

	* README:
	Add some new params to docs.

	* com/fluendo/examples/Player2.java: (PlayPipeline.PlayPipeline):
	* com/fluendo/examples/Player3.java:
	Added player that instantiates the Cortado applet.

	* com/fluendo/jst/Element.java: (Element.getState):
	the timeout for wait is in milliseconds, make correct conversion
	from gst time.

	* com/fluendo/jst/Message.java: (Message), (Message.toString),
	(Message.newError), (Message.parseErrorString):
	Added buffering message.

	* com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.getParameterInfo), (Cortado.init), (Cortado.paint),
	(Cortado.handleMessage), (Cortado.newState), (Cortado.newSeek),
	(Cortado.start), (Cortado.stop):
	Added buffering.

	* com/fluendo/player/Status.java:
	Print buffering percent.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	Small debug updates.
	Don't try to use a null inputstream.

	* com/fluendo/plugin/Queue.java: (Queue), (Queue.isFilled),
	(Queue.isEmpty), (Queue.clearQueue), (Queue.updateBuffering),
	(Queue.start), (Queue.stop), (Queue.Pad), (Queue.Queue),
	(Queue.getName):
	Added buffering to queue.

2005-12-19  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Pad.java: (Pad.run), (Pad.startTask):
	Make method public.

	* com/fluendo/jst/Queue.java:
	* com/fluendo/plugin/Queue.java:
	* plugins.ini:
	Move queue to plugins.

2005-12-19  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Element.java: (Element.sendEvent):
	Added query method.

	* com/fluendo/jst/Pad.java: (Pad.unlink), (Pad.sendEvent):
	Add query and getpeer methods.

	* com/fluendo/jst/Pipeline.java: (Pipeline.sendEvent):
	Forward queries to the sinks,

	* com/fluendo/jst/Query.java: (Query.newPosition),
	(Query.parsePositionValue), (Query), (Query.newDuration):
	Implement setting query result.

	* com/fluendo/player/Cortado.java: (Cortado),
	(Cortado.getParameterInfo), (Cortado.shutdown),
	(Cortado.stringToTime), (Cortado.init), (Cortado.getGraphics),
	(Cortado.getSize), (Cortado.update), (Cortado.run),
	(Cortado.realRun), (Cortado.newState), (Cortado.newSeek),
	(Cortado.start):
	Added option to configure the duration.
	start thread to update current position in statusbar.

	* com/fluendo/player/Status.java:
	Paint current position in time.

	* com/fluendo/plugin/Sink.java: (Sink.sendEvent):
	Implement POSITION query.

2005-12-15  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Pipeline.java: (Pipeline.doSeek):
	A seek sets the stream time to 0 since we always do 
	flushing seeks.

	* com/fluendo/jst/Queue.java: (Queue.Pad):
	Fix nasty deadlock.

	* com/fluendo/player/Configure.java:
	* com/fluendo/player/Cortado.java: (Cortado), (Cortado.init),
	(Cortado.setStatusVisible), (Cortado.mouseClicked),
	(Cortado.mouseEntered), (Cortado.mouseExited),
	(Cortado.mousePressed), (Cortado.mouseReleased),
	(Cortado.mouseDragged), (Cortado.mouseMoved),
	(Cortado.handleMessage), (Cortado.newState):
	Forward dragging events in the status area.
	React to seek signals.
	Try not to hide the statusbar when the pointer is in it.

	* com/fluendo/player/Status.java: (Status):
	Refactor painting a bit.
	Implement seek bar and make it emit a newSeek signal.
	
	* com/fluendo/plugin/AudioSink.java: (AudioSink.RingBuffer.commit),
	(AudioSink.RingBuffer.samplesPlayed), (AudioSink.render),
	(AudioSink.changeState):
	Do timestamp calc.

	* com/fluendo/plugin/AudioSinkJ2.java:
	(AudioSinkJ2.createRingBuffer):
	Reenable syncing.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad), (HTTPSrc),
	(HTTPSrc.getInputStream):
	* com/fluendo/plugin/OggDemux.java: (OggDemux.Pad):
	* com/fluendo/plugin/TheoraDec.java: (TheoraDec.Pad):
	Nicer debug and error messages.

	* com/fluendo/plugin/Sink.java: (Sink), (Sink.Pad), (Sink.doEvent),
	(Sink.doSync), (Sink.changeState):
	Calc correct timestamps based on prerolled time and basetime.

2005-12-15  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Pad.java: (Pad.setCapsFunc), (Pad.activate):
	Allow setting null caps.

	* com/fluendo/jst/Queue.java: (Queue.Pad):
	Properly shut down the queue, avoid deadlock.

	* com/fluendo/player/Configure.java:
	* com/fluendo/player/Cortado.java: (Cortado.handleMessage):
	Also print errors and stream status to console.

	* com/fluendo/player/Status.java:
	Allow setting playing state when in stopped.

	* com/fluendo/plugin/AudioSink.java: (AudioSink),
	(AudioSink.AudioClock.getInternalTime), (AudioSink.RingBuffer),
	(AudioSink.RingBuffer.run), (AudioSink.RingBuffer.samplesPlayed),
	(AudioSink.RingBuffer.play), (AudioSink.RingBuffer.pause),
	(AudioSink.changeState):
	Allow shutdown.

	* com/fluendo/plugin/AudioSinkJ2.java:
	(AudioSinkJ2.createRingBuffer):
	Remove temp hack.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad):
	Remove stupid print.

	* com/fluendo/plugin/OggDemux.java: (OggDemux.Pad):
	Do proper shutdown.

	* com/fluendo/plugin/Sink.java: (Sink.Pad), (Sink.changeState):
	* com/fluendo/plugin/TheoraDec.java: (TheoraDec), (TheoraDec.Pad),
	(TheoraDec.TheoraDec):
	* com/fluendo/plugin/VorbisDec.java: (VorbisDec), (VorbisDec.Pad),
	(VorbisDec.VorbisDec):
	Fix shutdown.

2005-12-14  Wim Taymans  <wim@fluendo.com>

	* Makefile:
	Added some targets.

	* com/fluendo/jst/Clock.java: (Clock.ClockID.waitID):
	No need to check for unscheduled here, it'll be done in 
	the waitFunc.

	* com/fluendo/jst/Element.java: (Element.getTransitionNext),
	(Element.continueState), (Element.changeState), (Element),
	(Element.doChangeState):
	Bring state changes up to data with GStreamer core.
	Fix race condition that probably also exist in GST when we update
	the lastReturn right after the element commited state ASYNC.
	
	* com/fluendo/jst/Pad.java: (Pad.isFlowFatal):
	Added debugging function
	
	* com/fluendo/jst/Pipeline.java: (Pipeline.StateThread.run),
	(Pipeline.reCalcState):
	Use new API to continue state change.

	* com/fluendo/jst/Query.java: (Query), (Query.newPosition),
	(Query.newDuration):
	Fix compilation

	* com/fluendo/jst/Queue.java: (Queue.Pad):
	Better debugging. Don't post an error when we did not cause the
	error but instead post a stream-status message to notify that we
	stopped streaming.

	* com/fluendo/player/Configure.java:
	* com/fluendo/player/Cortado.java:
	* com/fluendo/player/CortadoPipeline.java:
	* com/fluendo/player/Status.java:
	New Applet using JST along with JST pipelines and gui.

	* com/fluendo/plugin/AudioSink.java: (AudioSink.render),
	(AudioSink.changeState):
	* com/fluendo/plugin/Sink.java: (Sink.Pad), (Sink.changeState):
	Update to latest GST base classes.

	* com/fluendo/plugin/AudioSinkJ2.java: (AudioSinkJ2.open):
	Post error when we cannot open the device.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad), (HTTPSrc),
	(HTTPSrc.getInputStream):
	Better error handling.

	* com/fluendo/plugin/VideoSink.java: (VideoSink),
	(VideoSink.setCapsFunc), (VideoSink.preroll), (VideoSink.render),
	(VideoSink.getName):
	Make videosink work with foreign Components.

	* com/fluendo/plugin/VorbisDec.java: (VorbisDec.Pad):
	Forward flow errors.

2005-11-07  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/examples/Player2.java: (PlayPipeline),
	(PlayPipeline.padRemoved), (PlayPipeline.noMorePads), (Player2),
	(Player2.handleMessage):
	Fix player to handle any uri.

	* com/fluendo/jst/ElementFactory.java:
	Added typefind.

	* com/fluendo/jst/Pad.java: (Pad.sendEvent), (Pad.chain):
	Handle setCaps().

	* com/fluendo/plugin/OggDemux.java: (OggDemux.OggStream),
	(OggDemux.OggStream.OggStream), (OggDemux.Pad):
	Set caps.

	* com/fluendo/plugin/AudioSink.java: (AudioSink.render):
	* com/fluendo/plugin/Sink.java: (Sink.Pad), (Sink.doSync):
	* com/fluendo/plugin/VideoSink.java: (VideoSink):
	Handle new caps.

2005-11-04  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/jst/Query.java:
	Added Query API.

2005-11-04  Wim Taymans  <wim@fluendo.com>

	* com/fluendo/examples/Player2.java: (PlayPipeline.PlayPipeline),
	(PlayPipeline.doSendEvent):
	Simplified after changes in the core.

	* com/fluendo/jst/Element.java: (Element.getTransitionNext),
	(Element.commitState), (Element.doChangeState), (Element.setState):
	Propagate state change return values to the caller.

	* com/fluendo/jst/Format.java: (Format):
	Implement PERCENT format constants.

	* com/fluendo/jst/Pad.java: (Pad):
	Added isFlowFatal().

	* com/fluendo/jst/Pipeline.java: (Pipeline.reCalcState),
	(Pipeline.doChildStateChange), (Pipeline.changeState):
	State change updates.
	Send events to all sinks by default.

	* com/fluendo/jst/Queue.java: (Queue), (Queue.isFilled),
	(Queue.isEmpty), (Queue.clearQueue), (Queue.Pad):
	Updates to queue to make it flush faster.
	Fix refcounting of buffers when clearing the queue.

	* com/fluendo/player/Configure.java:
	* com/fluendo/plugin/AudioSink.java: (AudioSink.RingBuffer),
	(AudioSink.RingBuffer.run), (AudioSink.RingBuffer.release),
	(AudioSink.RingBuffer.waitSegment),
	(AudioSink.RingBuffer.samplesPlayed), (AudioSink.RingBuffer.clear),
	(AudioSink.RingBuffer.clearAll), (AudioSink.RingBuffer.setSample),
	(AudioSink.RingBuffer.stop), (AudioSink), (AudioSink.doSync),
	(AudioSink.doEvent):
	Added setFlushing so that we never enter a blocking call again.

	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc), (HTTPSrc.Pad),
	(HTTPSrc.getInputStream), (HTTPSrc.getName), (HTTPSrc.HTTPSrc),
	(HTTPSrc.setProperty):
	Get Content-Length from HTTP response header to get the length of
	the file.
	Implement percent seeking.
	Added property to configure readSize.
	
	* com/fluendo/plugin/OggDemux.java: (OggDemux.Pad):
	Add proper locks around event handling code.

	* com/fluendo/plugin/Sink.java: (Sink.Sink):
	Implement sendEvent.

	* com/fluendo/plugin/TheoraDec.java: (TheoraDec.Pad):
	* com/fluendo/plugin/VorbisDec.java: (VorbisDec.Pad):
	Take proper locks around event handling code.
	free buffers when clearing the queued buffers.

2005-11-03  Wim Taymans  <wim@fluendo.com>

	* Makefile:
	* com/fluendo/examples/Player.java:
	* com/fluendo/examples/Player2.java: (PlayPipeline),
	(PlayPipeline.PlayPipeline):
	* com/fluendo/jst/Element.java: (Element), (Element.typeFind),
	(Element.Element), (Element.setComponent), (Element.setClock),
	(Element.getClock), (Element.getBus), (Element.addPadListener),
	(Element.removePadListener), (Element.doPadListeners),
	(Element.addPad), (Element.removePad), (Element.noMorePads),
	(Element.getState), (Element.padsActivate), (Element.commitState),
	(Element.abortState), (Element.lostState), (Element.changeState):
	* com/fluendo/jst/Event.java: (Event), (Event.newFlushStart),
	(Event.getSeekFormat):
	* com/fluendo/jst/Object.java: (Object), (Object.getParent),
	(Object.unParent):
	* com/fluendo/jst/Pad.java: (Pad), (Pad.eventFunc),
	(Pad.sendEvent), (Pad.configureSink), (Pad.chain):
	* com/fluendo/jst/PadListener.java:
	* com/fluendo/jst/Pipeline.java: (Pipeline.BusThread),
	(Pipeline.BusThread.BusThread), (Pipeline.BusThread.run),
	(Pipeline.BusThread.shutDown), (Pipeline), (Pipeline.StateThread),
	(Pipeline.StateThread.StateThread), (Pipeline.StateThread.run),
	(Pipeline.StateThread.stateDirty), (Pipeline.Pipeline),
	(Pipeline.useClock):
	* com/fluendo/jst/Queue.java: (Queue.Pad):
	* com/fluendo/player/Buffer.java:
	* com/fluendo/player/Caps.java:
	* com/fluendo/player/Clock.java:
	* com/fluendo/player/ClockProvider.java:
	* com/fluendo/player/Configure.java:
	* com/fluendo/player/Element.java:
	* com/fluendo/player/ElementFactory.java:
	* com/fluendo/player/Event.java:
	* com/fluendo/player/Format.java:
	* com/fluendo/player/Object.java:
	* com/fluendo/player/Pad.java:
	* com/fluendo/player/PadListener.java:
	* com/fluendo/player/Pipeline.java:
	* com/fluendo/player/Queue.java:
	* com/fluendo/player/SystemClock.java:
	* com/fluendo/plugin/AudioSink.java: (AudioSink.RingBuffer.commit),
	(AudioSink.RingBuffer.stop), (AudioSink.doEvent),
	(AudioSink.render), (AudioSink.setCaps), (AudioSink.changeState):
	* com/fluendo/plugin/HTTPSrc.java: (HTTPSrc.Pad),
	(HTTPSrc.getInputStream):
	* com/fluendo/plugin/OggDemux.java: (OggDemux.Pad):
	* com/fluendo/plugin/Sink.java: (Sink), (Sink.Pad), (Sink.preroll),
	(Sink.doEvent), (Sink.doSync), (Sink.setCaps), (Sink.render),
	(Sink.Sink), (Sink.changeState):
	* com/fluendo/plugin/TheoraDec.java: (TheoraDec.Pad):
	* com/fluendo/plugin/VideoSink.java: (VideoSink.getProperty):
	* com/fluendo/plugin/VorbisDec.java: (VorbisDec.Pad):
	Updated jst to match latest GStreamer core, updated examples,
	implemented bus, messages. Fix deadlocks while seeking.
	Implemented state changes with topologocal sort.

=== release 0.1.2 ===

2005-04-22  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	* build.xml:
	* gen-Configure:
	  do some more versioning

2005-04-22  Wim Taymans <wim at fluendo dot com>

	  make multiple instances of cortado work correctly

2004-12-24  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	  bump nano

=== release 0.1.0 ===

2004-12-24  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	* README:
	  more notes and fixes

2004-12-24  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	* README:
	* build.xml:
	  updates for public release

=== release 0.0.1 ===

2004-11-30  Thomas Vander Stichele  <thomas at apestaart dot org>

	* Makefile:
	* build.xml:
	  add targets for two kinds of applets and version

	* stubs/sun/audio/AudioPlayer.java:
	* stubs/sun/audio/AudioStream.java:
	  Fix stubs by decompiling the sun java class files

2004-11-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* com/fluendo/player/Cortado.java:
	  fix compiler warning

2004-11-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	* build.xml:
	  allow excluding functionality with -Dexclude=(which,...)

2004-11-19  Thomas Vander Stichele  <thomas at apestaart dot org>

	reviewed by: Wim Taymans

	* com/fluendo/player/Cortado.java:
	* com/fluendo/player/Status.java:
	  Replace getWidth and getHeight with getSize (1.1)
