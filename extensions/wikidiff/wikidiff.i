%module wikidiff

%inline {
 	const char *wikidiff_do_diff(const char *text1, const char *text2, int num_lines_context);
}
