OBJ=render_info.cmo tex.cmo texutil.cmo parser.cmo lexer.cmo texvc.cmo \
render_info.cmx tex.cmx texutil.cmx parser.cmx lexer.cmx texvc.cmx \
lexer.cmi parser.cmi render_info.cmi tex.cmi texutil.cmi texvc.cmi \
lexer.o parser.o render_info.o tex.o texutil.o texvc.o \
lexer.ml parser.ml parser.mli texvc texvc.bc texvc_test.cmo \
texvc_test.cmx texvc_test.cmi texvc_test.o texvc_test util.o \
util.cmo util.cmx util.cmi texvc_cgi.cmi texvc_cgi texvc_cgi.cmo \
render.o render.cmi render.cmo render.cmx texvc_tex.cmx \
texvc_tex.o texvc_tex.cmi texvc_tex
CGIPATH=-I /usr/lib/ocaml/cgi -I /usr/lib/ocaml/netstring -I /usr/lib/ocaml/pcre

all: texvc texvc_test texvc_tex
texvc.bc: util.cmo parser.cmo texutil.cmo render.cmo lexer.cmo texvc.cmo
	ocamlc -o $@ unix.cma $^
texvc: util.cmx parser.cmx texutil.cmx render.cmx lexer.cmx texvc.cmx
	ocamlopt -o $@ unix.cmxa $^
texvc_test: parser.cmx texutil.cmx lexer.cmx texvc_test.cmx
	ocamlopt -o $@ $^
texvc_tex: parser.cmx texutil.cmx lexer.cmx texvc_tex.cmx
	ocamlopt -o $@ $^
%.ml: %.mll
	ocamllex $<
%.mli %.ml: %.mly
	ocamlyacc $<
%.cmo: %.ml
	ocamlc -c $<
%.cmx: %.ml
	ocamlopt -c $<
%.cmi: %.mli
	ocamlc -c $<
texvc_cgi.cmo: texvc_cgi.ml
	ocamlc -c $(CGIPATH) $<
texvc_cgi: util.cmo parser.cmo texutil.cmo render.cmo lexer.cmo texvc_cgi.cmo
	ocamlc -o $@ unix.cma $(CGIPATH) pcre.cma netstring.cma cgi.cma $^
	chmod g-w $@
clean:
	rm -f $(OBJ)

lexer.cmo: parser.cmi render_info.cmi tex.cmi texutil.cmo 
lexer.cmx: parser.cmx render_info.cmi tex.cmi texutil.cmx 
parser.cmo: render_info.cmi tex.cmi parser.cmi 
parser.cmx: render_info.cmi tex.cmi parser.cmi 
parser.cmi: render_info.cmi tex.cmi 
render.cmo: texutil.cmo util.cmo 
render.cmx: texutil.cmx util.cmx 
tex.cmi: render_info.cmi 
texutil.cmo: parser.cmi render_info.cmi tex.cmi 
texutil.cmx: parser.cmx render_info.cmi tex.cmi 
texvc.cmo: lexer.cmo parser.cmi render.cmo texutil.cmo 
texvc.cmx: lexer.cmx parser.cmx render.cmx texutil.cmx 
texvc_cgi.cmo: lexer.cmo parser.cmi render.cmo texutil.cmo util.cmo 
texvc_cgi.cmx: lexer.cmx parser.cmx render.cmx texutil.cmx util.cmx 
texvc_test.cmo: lexer.cmo parser.cmi texutil.cmo 
texvc_test.cmx: lexer.cmx parser.cmx texutil.cmx 
texvc_tex.cmo: lexer.cmo parser.cmi texutil.cmo 
texvc_tex.cmx: lexer.cmx parser.cmx texutil.cmx 
