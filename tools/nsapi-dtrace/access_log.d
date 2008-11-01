
sjsws*::dtrace_log_request:log-request
{ 
	self->req = copyin(arg0, 4 * 6);
	self->uri = copyinstr((uint32_t) ((uint32_t *)self->req)[0]);
	self->ip = copyinstr((uint32_t) ((uint32_t *)self->req)[1]);
	self->agent = copyinstr((uint32_t) ((uint32_t *)self->req)[2]);
	self->method = copyinstr((uint32_t) ((uint32_t *)self->req)[3]);
	self->status = copyinstr((uint32_t) ((uint32_t *)self->req)[4]);
	self->referer = copyinstr((uint32_t) ((uint32_t *)self->req)[5]);
	printf("%s %s %s %s (%s)\n", self->ip, self->method, self->uri, self->status, self->referer);
}
