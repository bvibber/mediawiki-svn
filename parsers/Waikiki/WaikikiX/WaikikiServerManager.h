/* WaikikiServerManager */

#import <Cocoa/Cocoa.h>

#include <sys/types.h>

typedef enum {
    wkksInit,
    wkksStarting,
    wkksRunning,
    wkksStopping,
    wkksStopped,
    wkksError
} WaikikiState;

@interface WaikikiServerManager : NSObject
{
    IBOutlet id infoLabel;
    IBOutlet id launchButton;
    IBOutlet id serverButton;
    IBOutlet id portInput;
    IBOutlet id localCheck;
    
    int portNumber;
    int localOnly;
    WaikikiState state;
    pid_t server_pid;
    char *errMessage;
}

// Actions
- (IBAction)setPort:(id)sender;
- (IBAction)startServer:(id)sender;
- (IBAction)stopServer:(id)sender;
- (IBAction)setLocalCheck:(id)sender;
- (IBAction)launchBrowser:(id)sender;
- (void)doTerminate:(NSNotification *)note;

- (id)init;
- (int)isRunning;
- (int)isStopped;
- (void)setError:(char *)msg;
- (void)setState:(WaikikiState)newstate;
- (void)setPortNumber:(int)port;
- (int)getPortNumber;
- (void)setLocalOnly:(int)islocal;
- (void)startWebServer;
- (void)stopWebServer;

@end
