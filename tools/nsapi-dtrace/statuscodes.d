
sjsws*::dtrace_log_request:log-request
{ 
	self->req = copyin(arg0, 4 * 6);
	self->status = copyinstr((uint32_t) ((uint32_t *)self->req)[4]);
	@codes[self->status] = count();
}
