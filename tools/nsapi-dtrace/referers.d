
sjsws*::dtrace_log_request:log-request
{ 
	self->req = copyin(arg0, 4 * 6);
	self->referer = copyinstr((uint32_t) ((uint32_t *)self->req)[5]);
	@refers[self->referer] = count();
}
