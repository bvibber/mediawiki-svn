#import "WaikikiServerManager.h"

#include <stdlib.h>
#include <unistd.h>
#include "httpd.h"

/* private */ char cgi_path[256];
/* private */ void runcgi_waikiki(httpd *server);

@implementation WaikikiServerManager

-(id)init
{
    portNumber = 8080;
    localOnly = FALSE;
    state = wkksInit;
    server_pid = 0;
    errMessage = "";
    [[NSNotificationCenter defaultCenter]
                     addObserver:self
                        selector:@selector(doTerminate:)
                            name:NSApplicationWillTerminateNotification
                          object:NSApp];
    return self;
}

- (int)isRunning
{
    return state == wkksRunning;
}

- (int)isStopped
{
    return (state == wkksStopped || state == wkksInit || state == wkksError);
}

- (void)setError:(char *)msg
{
    errMessage = msg;
    [self setState: wkksError];
}

- (void)setState:(WaikikiState)newstate
{
    state = newstate;
    char *descr[] = {
        "Init...",
        "Starting web server...",
        "Web server running.",
        "Stopping web server...",
        "Stopped.",
        "Error! %s"
    };
    char description[256];
    sprintf(description, descr[state], errMessage);
    [infoLabel setStringValue: [NSString stringWithCString:description]];

    [launchButton setEnabled:(state == wkksRunning)];
    
    switch(state) {
    case wkksInit:
    case wkksStopped:
    case wkksError:
        [serverButton setTitle:@"Start"];
        [serverButton setTarget:self];
        [serverButton setAction:@selector(startServer:)];
        [serverButton setEnabled:TRUE];
        [portInput setEnabled:TRUE];
        [localCheck setEnabled:TRUE];
        break;
    case wkksStarting:
    case wkksStopping:
        [serverButton setEnabled:FALSE];
        [portInput setEnabled:FALSE];
        [localCheck setEnabled:FALSE];
        break;
    case wkksRunning:
        [serverButton setTitle:@"Stop"];
        [serverButton setTarget:self];
        [serverButton setAction:@selector(stopServer:)];
        [serverButton setEnabled:TRUE];
        [portInput setEnabled:TRUE];
        [localCheck setEnabled:TRUE];
        break;
    default:
        // ??
        [serverButton setEnabled:FALSE];
        [portInput setEnabled:FALSE];
        [localCheck setEnabled:FALSE];
    }
}

- (void)setPortNumber:(int)port
{
    int isrunning;
    if(isrunning = [self isRunning]) [self stopWebServer];
    portNumber = port;
    if(isrunning) [self startWebServer];
}

- (int)getPortNumber
{
    return portNumber;
}

- (void)setLocalOnly:(int)islocal
{
    int isit = (islocal ? TRUE : FALSE);
    if(isit != localOnly) {
        int isrunning;
        if(isrunning = [self isRunning]) [self stopWebServer];
        localOnly = isit;
        if(isrunning) [self startWebServer];
    }
}

- (void)startWebServer
{
    httpd *server;
    char *webroot;
    webroot = (char *)[[[NSBundle mainBundle] resourcePath] cString];
    strcpy(cgi_path, webroot);
    
    // NSRunAlertPanel(@"web root", @"'%s'", @"OK", nil, nil, webroot);
    
    if(server_pid) {
        // Can't start, we're already running!
        return;
    }

    [self setState: wkksStarting];

    if(server_pid = fork()) {
        // parent
        if(server_pid == -1) {
            server_pid = 0;
            [self setError: "fork failed"];
            return;
        } else {
            [self setState: wkksRunning];
            return;
        }
    } else {
        // child

        // If we start the server in the parent it's not clear how to kill it
        server = httpdCreate( (localOnly ? "127.0.0.1" : NULL), portNumber );
        if(!server) {
            exit(-1);
        }

        httpdSetFileBase(server, webroot);
        //httpdAddFileContent(server, "/", "index.html", HTTP_TRUE, NULL, "index.html");
        httpdAddWildcardContent(server, "/wiki", NULL, "wiki");
        httpdAddCContent(server, "/cgi-bin", "waikiki.exe", NULL, NULL, runcgi_waikiki);
        while(1) {
            if(httpdGetConnection(server, NULL) < 0) continue;
            if(httpdReadRequest(server) < 0) {
                httpdEndRequest(server);
                continue;
            }
            httpdProcessRequest(server);
            httpdEndRequest(server);
        }
        exit(0); // Shouldn't happen
    }
}

- (void)stopWebServer
{
    if(server_pid == 0) return;
    [self setState: wkksStopping];
    if(kill(server_pid, SIGHUP)) {
        [self setError: "SIGHUP failed"];
    } else {
        server_pid = 0;
        [self setState: wkksStopped];
    }
}

// Actions
- (IBAction)setPort:(id)sender
{
    [self setPortNumber: [sender intValue]];
}

- (IBAction)startServer:(id)sender
{
    [self startWebServer];
}

- (IBAction)stopServer:(id)sender
{
    [self stopWebServer];
}

- (IBAction)setLocalCheck:(id)sender
{
    [self setLocalOnly: [sender intValue]];
}

- (IBAction)launchBrowser:(id)sender
{
    char urlstring[256];
    
    sprintf(urlstring, "http://localhost:%d/cgi-bin/waikiki.exe?title=A", portNumber );
    id url=[NSURL URLWithString:[NSString stringWithCString:urlstring]];
    
    id workspace=[NSWorkspace sharedWorkspace];
    [workspace openURL:url];
}

- (void)doTerminate:(NSNotification *)note
{
    if([self isRunning]) {
        [[NSNotificationCenter defaultCenter] removeObserver:self];
        [self stopWebServer];
    }
}

@end

void runcgi_waikiki(httpd *server)
{
    FILE *pipe;
    int bufsize = 32768, nbytes;
    char *buffer;
    
    chdir(cgi_path);
    putenv("REQUEST_URI=/cgi-bin/waikiki.exe?title=A");
    putenv("QUERY_STRING=title=A");
    
    pipe = popen("./waikiki", "r");
    if(pipe == NULL) {
        // Couldn't open pipe
        return;
    }
    buffer = malloc(bufsize);
    while(!feof(pipe)) {
        // FIXME: strip off the 'Content-type: text/html'
        if(nbytes = fread((void *)buffer, 1, bufsize, pipe)) {
            if(nbytes >= bufsize) nbytes = bufsize - 1;
            buffer[nbytes] = '\0';
            httpdOutput(server, buffer);
        }
    }
    free(buffer);
    if(-1 == pclose(pipe)) {
        // Can't close? That's not right.
    }
}
