#ifndef SERVER_H
#define SERVER_H

typedef struct tdata {
  int clientSocket;
  struct sockaddr_in clientAddress;
} threadData;


extern Tnode *dictSeg; //for word segmentation
extern Tnode *dictToCN; //conversion to zh-cn
extern Tnode *dictToTW; //zh-tw
extern Tnode *dictToHK; //zh-hk
extern Tnode *dictToSG; //zh-sg
extern int optSilent, optWarning;

#endif
