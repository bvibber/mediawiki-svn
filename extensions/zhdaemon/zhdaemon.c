#include "include.h"
#include "ttree.h"
#include "dict.h"
#include "segment.h"
#include "convert.h"
#include "zhdaemon.h"

void *thread_main(void *clientSocket);

/* the dictionaries. not all of them will be loaded, depending on the
   service that we are going to provide
*/
Tnode *dictSeg=NULL; //for word segmentation
Tnode *dictToCN=NULL; //conversion to zh-cn
Tnode *dictToTW=NULL; //zh-tw
Tnode *dictToHK=NULL; //zh-hk
Tnode *dictToSG=NULL; //zh-sg

/* command line options */
char *optConfFile="./zhdaemon.conf";
int optSilent=0, optWarning=1;

void usage() {
  exit(0);
}

void processArg(int argc, char *argv[]) {

}


/* configuration options */
int confServerPort;
char *confDictSeg, *confDictToCN, *confDictToTW, *confDictToHK, *confDictToSG;
int confInputLimit;

void loadConf(char *conffile) {
  cfg_opt_t opts[] =
    {
      CFG_STR("dictSeg",  "/usr/local/share/zhdaemons/wordlist", CFGF_NONE),
      CFG_STR("dictToCN", "/usr/local/share/zhdaemons/tocn.dict", CFGF_NONE),
      CFG_STR("dictToTW", "/usr/local/share/zhdaemons/totw.dict", CFGF_NONE),
      CFG_STR("dictToHK", "/usr/local/share/zhdaemons/tohk.dict", CFGF_NONE),
      CFG_STR("dictToSG", "/usr/local/share/zhdaemons/tosg.dict", CFGF_NONE),
      CFG_INT("serverPort", 2004, CFGF_NONE),
      CFG_INT("inputLimit", 1048576, CFGF_NONE),
      CFG_END()
    };
  cfg_t *cfg;
  
  cfg = cfg_init(opts, CFGF_NONE);
  if(cfg_parse(cfg, conffile) == CFG_PARSE_ERROR) {
    exit(-1);
  }
  confServerPort = cfg_getint(cfg, "serverPort");
  confInputLimit = cfg_getint(cfg, "inputLimit");
  confDictSeg = cfg_getstr(cfg, "dictSeg");
  confDictToCN = cfg_getstr(cfg, "dictToCN");
  confDictToTW = cfg_getstr(cfg, "dictToTW");
  confDictToHK = cfg_getstr(cfg, "dictToHK");
  confDictToSG = cfg_getstr(cfg, "dictToSG");

  printf("\nConfiguration:\n");
  printf("serverPort = %d\n", confServerPort);
  printf("inputLimit = %d\n", confInputLimit);
  printf("dictSeg = %s\n", confDictSeg);
  printf("dictToCN = %s\n", confDictToCN);
  printf("dictToTW = %s\n", confDictToTW);
  printf("dictToHK = %s\n", confDictToHK);
  printf("dictToSG = %s\n\n", confDictToSG);

  
}

void *thread_main(void *data);

int main(int argc, char *argv[])
{
  int serverSocket;
  struct sockaddr_in serverAddress;
  int status;


  processArg(argc, argv);
  loadConf(optConfFile);

  /* load dictionaries */
  printf("Loading segmentation dictionary from %s...\n",
	 confDictSeg); fflush(0);
  dictSeg = loadSegmentationDictionary(confDictSeg);
  if(!dictSeg) {
    exit(-1);
  }

  printf("Loading conversion dictionary %s...", confDictToCN);fflush(0);
  dictToCN = loadConversionDictionary(confDictToCN);
  if(!dictToCN) 
    exit(-1);
  
  printf("Loading conversion dictionary %s...", confDictToTW);fflush(0);
  dictToTW = loadConversionDictionary(confDictToTW);
  if(!dictToTW)
    exit(-1);
  
  printf("Loading conversion dictionary %s (as the basis for toHK)...\n", confDictToTW);fflush(0);
  dictToHK = loadConversionDictionary(confDictToTW);
  if(!dictToHK) {
    exit(-1);
  }
  printf("Loading conversion dictionary %s...\n", confDictToHK);fflush(0);
  dictToHK = loadAdditionalConversionDictionary(dictToHK, confDictToHK);
  if(!dictToHK) {
    exit(-1);
  }
  
  printf("Loading conversion dictionary %s (as the basis for toSG)...", confDictToCN); fflush(0);
  dictToSG = loadConversionDictionary(confDictToCN);
  if(!dictToSG) {
    exit(-1);
  }
  printf("Loading conversion dictionary %s...\n", confDictToSG);fflush(0);
  dictToSG = loadAdditionalConversionDictionary(dictToSG, confDictToSG);
  if(!dictToSG) {
    exit(-1);
  }
  printf("\n\n");

  /* open server socket */  
  serverSocket = socket(PF_INET,SOCK_STREAM,0);
  if (serverSocket <= 0) {
    fprintf(stderr, "server: Failed creating socket.\n");
    exit(-1);
  }

  serverAddress.sin_family=AF_INET;
  serverAddress.sin_addr.s_addr = INADDR_ANY;
  serverAddress.sin_port  = htons(confServerPort);
  
  status = bind(serverSocket,
		(struct sockaddr*) &serverAddress, 
		sizeof(serverAddress));
  
  if (status != 0) {
    fprintf(stderr, "error: bind() failed.\n");
    exit(-1);
  }
  
  while (1) {
    int clientSocket, addrlen;
    int terr;
    struct sockaddr_in clientAddress;
    pthread_t *tid ;
    printf("Listening...\n");fflush(0);
    listen(serverSocket,1024);
    addrlen = sizeof(clientAddress);
    
    clientSocket = accept(serverSocket,
			  (struct sockaddr*) &clientAddress,
			  (socklen_t *) &addrlen);

    threadData *td = (threadData*)malloc(sizeof(threadData));
    td->clientSocket = clientSocket;
    td->clientAddress = clientAddress;

    tid = (pthread_t *)malloc(sizeof(pthread_t));
    terr = pthread_create(tid, NULL, thread_main, td);
    
    if(terr) {
      fprintf(stderr, "Thread creation failed.\n");
    }
  }
  // should never get here...
  return 1;
}


void *thread_main(void *data)
{
  threadData *td = (threadData*)data;
  int tid;
  unsigned char *buf, cmdline[512];
  unsigned char cmd[32], param[32];
  int i, len;
  FILE *sockin=NULL, *sockout=NULL;
  tid = (int)pthread_self();


  if (td->clientSocket <= 0) {
    if(optWarning) {
      fprintf(stderr,
	      "%d: ** accept() failed: ", tid );
      fprintf(stderr, "%s\n", strerror(td->clientSocket));
    }
    free(data);
    pthread_exit(NULL);
  }

  if(!optSilent) {
    printf("%d: connected with %s.\n",
	   tid,inet_ntoa(td->clientAddress.sin_addr));
  }

  sockin = fdopen(td->clientSocket, "r");
  if(sockin==NULL) {
    if(optWarning)
      fprintf(stderr, "%d: fdopen() for read failed.\n", tid);
    free(data);
    pthread_exit(NULL);
  }
  sockout = fdopen(td->clientSocket, "w");
  if(sockout==NULL) {
    if(optWarning)
      fprintf(stderr, "%d: fdopen() for write failed.\n", tid);
    fclose(sockin);
    free(data);
    pthread_exit(NULL);
  }
  
  
  while(1) {
    if(!fgets(cmdline, sizeof(cmdline), sockin)) {
      if(feof(sockin)) {
	if(!optSilent)
	  printf("%d: client closes connection.\n", tid);
      }
      else {
	if(optWarning) {
	  fprintf(stderr, "%d: read error.\n", tid);
	}
      }
      break;
    }
    sscanf(cmdline, "%30s", cmd);
    // word segmentation
    if(strcmp(cmd, "SEG")==0) {
      if(sscanf(cmdline, "%30s %d", cmd, &len)!=2) {
	if(optWarning) {
	  fprintf(stderr, "%d: ** Error in SEG command: %s\n", tid, cmdline);
	}
	continue;
      }
      if(len > confInputLimit) {
	if(optWarning) {
	  fprintf(stderr, "%d: ** Client data is too large.\n", tid);
          //eat the rest of the request
          while(len>0) {
            if(!fgets(cmdline, sizeof(cmdline), sockin))
              break;
            len-=strlen(cmdline);
          }
	  sprintf(cmdline, "ERROR\r\n");
	  fputs(cmdline, sockout);
          continue;
	}
      }
      if(!optSilent) {
	printf("%d: %s %d\n", tid, cmd, len);
      }
    }
    // variant conversion
    else if(strcmp(cmd, "CONV")==0) {
      if(sscanf(cmdline, "%30s %30s %d", cmd, param, &len)!=3) {
	if(optWarning) {
	  fprintf(stderr, "%d: ** Error in CONV command: %s\n", tid, cmdline);
	}
	continue;
      }
      if(len > confInputLimit) {
	if(optWarning) {
	  fprintf(stderr, "%d: ** Client data is too large.\n", tid);
	  // should handle this more gracefully...
          while(len>0) {
            if(!fgets(cmdline, sizeof(cmdline), sockin))
              break;
            len-=strlen(cmdline);
          }
	  sprintf(cmdline, "ERROR\r\n");
	  fputs(cmdline, sockout);	  
          continue;
	}
      }
      if(!optSilent) {
	printf("%d: %s %s %d\n", tid, cmd, param, len);
      }
    }
    else {
      if(optWarning) {
	fprintf(stderr, "%d: ** Unknown command: %s\n", tid, cmdline);
      }
      continue;
    }

    buf = (unsigned char*)malloc(len*sizeof(unsigned char));
    if(!buf) {
      if(optWarning) {
	fprintf(stderr, "%d: ** Out of memory allocating input buffer.\n", tid);
      }
      break;
    }
    
    i = fread(buf, sizeof(unsigned char), len, sockin);

    if(i>=0) {
      unsigned char *result=NULL;
      if(strcmp(cmd, "SEG")==0) {
	result = doSegment(tid, buf, i);
      }
      else if(strcmp(cmd, "CONV")==0) {
	result = doConvert(tid, buf, i, param);
      }
      if(!result) {
	sprintf(cmdline, "ERROR\r\n");
	fputs(cmdline, sockout);
      }
      else {
	sprintf(cmdline, "OK %d\r\n", strlen(result));
	fputs(cmdline, sockout);
	fprintf(sockout, "%s", result);
	fflush(sockout);
	free(result);
      }
    }
    free(buf);
  }
  if(!optSilent)
    printf("%d: Closing down.\n", tid);
  if(sockin)
    fclose(sockin);
  if(sockout)
    fclose(sockout);
  close(td->clientSocket);
  free(td);
  pthread_exit(NULL);
}

