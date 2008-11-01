struct request {
        char      *uri;
        char      *ip;
        char      *agent;
        char      *method;
        char      *status;
        char      *referer;
};

provider sjsws {
	probe log__request(struct request*);
};
