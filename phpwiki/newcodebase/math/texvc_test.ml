exception LexerException of string
let lexer_token_safe lexbuf =
    try Lexer.token lexbuf
    with Failure s -> raise (LexerException s)

let rec foo () =
    try
	let line = input_line stdin in
	(try
	    let tree = Parser.tex_expr lexer_token_safe (Lexing.from_string line) in
    	    let out = Texutil.mapjoin Texutil.print tree in
	    (try
	    ignore(Texutil.html_render tree); print_string "$^\n";
	    with _ -> print_string "$_\n";
	    )
        with
	    Texutil.Illegal_tex_function s -> print_string ("$T" ^ s ^ " " ^ line ^ "\n")
	  | LexerException s		   -> print_string ("$L" ^ line ^ "\n")
	  | _ 				   -> print_string ("$ " ^ line ^ "\n"));
	flush stdout;
	foo ();
    with
	End_of_file -> ()
;;
foo ();;
