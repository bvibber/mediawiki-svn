%module utfnormal

%inline {
 	const char *utf8_normalize(const char *utf8_string, int mode);
}
