exception LexerException of string
let lexer_token_safe lexbuf =
    try Lexer.token lexbuf
    with Failure s -> raise (LexerException s)

let render tmppath finalpath tree =
    let outtex = Texutil.mapjoin Texutil.print tree in
    let md5 = Digest.to_hex (Digest.string outtex) in
    begin
	print_string (try 
	    let htmldoc = Texutil.html_render tree in
		(if !Texutil.html_conservative then "C" else "+") ^ md5 ^ htmldoc
	    with _ ->
		"+" ^ md5
	);
	Render.render tmppath finalpath outtex md5
    end
let _ =
    Texutil.set_encoding (try Sys.argv.(4) with _ -> "UTF-8");
    try render Sys.argv.(1) Sys.argv.(2) (Parser.tex_expr lexer_token_safe (Lexing.from_string Sys.argv.(3)))
    with Parsing.Parse_error -> print_string "S"
       | LexerException _ -> print_string "L"
       | Texutil.Illegal_tex_function s -> print_string ("F" ^ s)
       | Util.FileAlreadyExists -> print_string "-"
       | Invalid_argument _ -> print_string "-"
       | Failure _ -> print_string "-"
       | Render.ExternalCommandFailure s -> ()
       | _ -> print_string "-"
