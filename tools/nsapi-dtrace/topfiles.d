
sjsws*::dtrace_log_request:log-request
{ 
	self->req = copyin(arg0, 4 * 6);
	self->uri = copyinstr((uint32_t) ((uint32_t *)self->req)[0]);
	@files[self->uri] = count();
}
