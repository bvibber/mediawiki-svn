open Parser
open Render_info
open Tex

let mapjoin f l = (List.fold_left (fun a b -> a ^ (f b)) "" l)
let mapjoine e f = function
    [] -> ""
  | h::t -> (List.fold_left (fun a b -> a ^ e ^ (f b)) (f h) t)

let tex_part = function
    HTMLABLE (_,t,_) -> t
  | HTMLABLEC (_,t,_) -> t
  | HTMLABLE_BIG (t,_) -> t
  | TEX_ONLY t -> t
let rec print = function
    TEX_FQ (a,b,c) -> (print a) ^ "_{" ^ (print  b) ^ "}^{" ^ (print  c) ^ "}"
  | TEX_DQ (a,b) -> (print a) ^ "_{" ^ (print  b) ^ "}"
  | TEX_UQ (a,b) -> (print a) ^ "^{" ^ (print  b) ^ "}" 
  | TEX_LITERAL s -> tex_part s
  | TEX_FUN1 (f,a) -> "{" ^ f ^ " " ^ (print a) ^ "}"
  | TEX_FUN1hl (f,_,a) -> "{" ^ f ^ " " ^ (print a) ^ "}"
  | TEX_FUN1hf (f,_,a) -> "{" ^ f ^ " " ^ (print a) ^ "}"
  | TEX_DECLh (f,_,a) -> "{" ^ f ^ "{" ^ (mapjoin print a) ^ "}}"
  | TEX_FUN2 (f,a,b) -> "{" ^ f ^ " " ^ (print a) ^ (print b) ^ "}"
  | TEX_FUN2h (f,_,a,b) -> "{" ^ f ^ " " ^ (print a) ^ (print b) ^ "}"
  | TEX_FUN2sq (f,a,b) -> "{" ^ f ^ "[ " ^ (print a) ^ "]" ^ (print b) ^ "}"
  | TEX_CURLY (tl) -> "{" ^ (mapjoin print tl) ^ "}"
  | TEX_INFIX (s,ll,rl) -> "{" ^ (mapjoin print ll) ^ " " ^ s ^ "" ^ (mapjoin print rl) ^ "}"
  | TEX_INFIXh (s,_,ll,rl) -> "{" ^ (mapjoin print ll) ^ " " ^ s ^ "" ^ (mapjoin print rl) ^ "}"
  | TEX_BOX (bt,s) -> "{"^bt^"{" ^ s ^ "}}"
  | TEX_MATRIX (t,rows) -> "{\\begin{"^t^"}"^(mapjoine "\\\\" (mapjoine "&" (mapjoin print)) rows)^"\\end{"^t^"}}"

(* HTML Rendering Engine *)

exception Too_difficult_for_html
type context = CTX_NORMAL | CTX_IT | CTX_RM 

let html_conservative = ref true

let new_ctx = function
    FONTFORCE_IT -> CTX_IT
  | FONTFORCE_RM -> CTX_RM
let font_render lit = function
    (_,     FONT_UFH) -> lit
  | (_,     FONT_UF)  -> lit
  | (CTX_IT,FONT_RTI) -> raise Too_difficult_for_html
  | (_,     FONT_RTI) -> lit
  | (CTX_IT,FONT_RM)  -> "<i>"^lit^"</i>"
  | (_,     FONT_RM)  -> lit
  | (CTX_RM,FONT_IT)  -> lit
  | (_,     FONT_IT)  -> "<i>"^lit^"</i>"

let rec html_render_flat ctx = function
    TEX_LITERAL (HTMLABLE (ft,_,sh))::r -> (html_conservative := false; (font_render sh (ctx,ft))^html_render_flat ctx r)
  | TEX_LITERAL (HTMLABLEC(ft,_,sh))::r -> (font_render sh (ctx,ft))^html_render_flat ctx r
  | TEX_LITERAL (HTMLABLE_BIG (_,sh))::r -> (html_conservative := false; sh^html_render_flat ctx r)
  | TEX_FUN1hl (_,(f1,f2),a)::r -> f1^(html_render_flat ctx [a])^f2^html_render_flat ctx r
  | TEX_FUN1hf (_,ff,a)::r -> (html_render_flat (new_ctx ff) [a])^html_render_flat ctx r
  | TEX_DECLh (_,ff,a)::r -> (html_render_flat (new_ctx ff) a)^html_render_flat ctx r
  | TEX_CURLY ls::r -> html_render_flat ctx (ls @ r)
  | TEX_DQ (a,b)::r  -> (html_conservative := false;
			 let bs = html_render_flat ctx [b] in match html_render_size ctx a with
		         true, s -> raise Too_difficult_for_html
		       | false, s -> s^"<sub>"^bs^"</sub>")^html_render_flat ctx r
  | TEX_UQ (a,b)::r  -> (html_conservative := false;
		         let bs = html_render_flat ctx [b] in match html_render_size ctx a with
		         true, s ->  raise Too_difficult_for_html
		       | false, s -> s^"<sup>"^bs^"</sup>")^html_render_flat ctx r
  | TEX_FQ (a,b,c)::r -> (html_conservative := false;
			 (let bs = html_render_flat ctx [b] in let cs = html_render_flat ctx [c] in
		          match html_render_size ctx a with
		          true, s -> raise Too_difficult_for_html
		        | false, s -> s^"<sub>"^bs^"</sub><sup>"^cs^"</sup>")^html_render_flat ctx r)
  | TEX_BOX (_,s)::r -> s^html_render_flat ctx r
  | TEX_LITERAL (TEX_ONLY _)::_ -> raise Too_difficult_for_html
  | TEX_FUN1 _::_ -> raise Too_difficult_for_html
  | TEX_FUN2  _::_ -> raise Too_difficult_for_html
  | TEX_FUN2h  _::_ -> raise Too_difficult_for_html
  | TEX_FUN2sq  _::_ -> raise Too_difficult_for_html
  | TEX_INFIX _::_ -> raise Too_difficult_for_html
  | TEX_INFIXh _::_ -> raise Too_difficult_for_html
  | TEX_MATRIX _::_ -> raise Too_difficult_for_html
  | [] -> ""
and html_render_size ctx = function
    TEX_LITERAL (HTMLABLE_BIG (_,sh)) -> true,sh
  | x -> false,html_render_flat ctx [x]

let rec html_render_deep ctx = function
    TEX_LITERAL (HTMLABLE (ft,_,sh))::r -> (html_conservative := false; ("",(font_render sh (ctx,ft)),"")::html_render_deep ctx r)
  | TEX_LITERAL (HTMLABLEC(ft,_,sh))::r -> ("",(font_render sh (ctx,ft)),"")::html_render_deep ctx r
  | TEX_LITERAL (HTMLABLE_BIG (_,sh))::r -> (html_conservative := false; ("",sh,"")::html_render_deep ctx r)
  | TEX_FUN2h (_,f,a,b)::r -> (html_conservative := false; (f a b)::html_render_deep ctx r)
  | TEX_INFIXh (_,f,a,b)::r -> (html_conservative := false; (f a b)::html_render_deep ctx r)
  | TEX_CURLY ls::r -> html_render_deep ctx (ls @ r)
  | TEX_DQ (a,b)::r  -> (let bs = html_render_flat ctx [b] in match html_render_size ctx a with
		         true, s ->  "","<font size=+2>"^s^"</font>",bs
		       | false, s -> "",(s^"<sub>"^bs^"</sub>"),"")::html_render_deep ctx r
  | TEX_UQ (a,b)::r  -> (let bs = html_render_flat ctx [b] in match html_render_size ctx a with
		         true, s ->  bs,"<font size=+2>"^s^"</font>",""
		       | false, s -> "",(s^"<sup>"^bs^"</sup>"),"")::html_render_deep ctx r
  | TEX_FQ (a,b,c)::r -> (html_conservative := false;
			 (let bs = html_render_flat ctx [b] in let cs = html_render_flat ctx [c] in
		          match html_render_size ctx a with
		          true, s ->  (cs,"<font size=+2>"^s^"</font>",bs)
		        | false, s -> ("",(s^"<sub>"^bs^"</sub><sup>"^cs^"</sup>"),""))::html_render_deep ctx r)
  | TEX_FUN1hl (_,(f1,f2),a)::r -> ("",f1,"")::(html_render_deep ctx [a]) @ ("",f2,"")::html_render_deep ctx r
  | TEX_FUN1hf (_,ff,a)::r -> (html_render_deep (new_ctx ff) [a]) @ html_render_deep ctx r
  | TEX_DECLh  (_,ff,a)::r -> (html_render_deep (new_ctx ff) a) @ html_render_deep ctx r
  | TEX_BOX (_,s)::r -> ("",s,"")::html_render_deep ctx r
  | TEX_LITERAL (TEX_ONLY _)::_ -> raise Too_difficult_for_html
  | TEX_FUN1 _::_ -> raise Too_difficult_for_html
  | TEX_FUN2 _::_ -> raise Too_difficult_for_html
  | TEX_FUN2sq  _::_ -> raise Too_difficult_for_html
  | TEX_INFIX _::_ -> raise Too_difficult_for_html
  | TEX_MATRIX _::_ -> raise Too_difficult_for_html
  | [] -> []

let rec html_render_table = function
    sf,u,d,("",a,"")::("",b,"")::r -> html_render_table (sf,u,d,(("",a^b,"")::r))
  | sf,u,d,(("",a,"") as c)::r     -> html_render_table (c::sf,u,d,r)
  | sf,u,d,((_,a,"") as c)::r      -> html_render_table (c::sf,true,d,r)
  | sf,u,d,(("",a,_) as c)::r      -> html_render_table (c::sf,u,true,r)
  | sf,u,d,((_,a,_) as c)::r       -> html_render_table (c::sf,true,true,r)
  | sf,false,false,[]              -> mapjoin (function (u,m,d) -> m) (List.rev sf)
  | sf,true,false,[]               -> let ustr,mstr = List.fold_left (fun (us,ms) (u,m,d) -> (us^"<td>"^u,ms^"<td>"^u))
					("","") (List.rev sf) in
					"<table><tr align=center valign=bottom>" ^ ustr ^ "</tr><tr align=center>" ^ mstr ^ "</tr></table>"
  | sf,false,true,[]               -> let mstr,dstr = List.fold_left (fun (ms,ds) (u,m,d) -> (ms^"<td>"^m,ds^"<td>"^d))
					("","") (List.rev sf) in
					"<table><tr align=center>" ^ mstr ^ "</tr><tr align=center valign=top>" ^ dstr ^ "</tr></table>"
  | sf,true,true,[]               -> let ustr,mstr,dstr = List.fold_left (fun (us,ms,ds) (u,m,d) ->
					(us^"<td>"^u,ms^"<td>"^m,ds^"<td>"^d)) ("","","") (List.rev sf) in
					"<table><tr align=center valign=bottom>" ^ ustr ^ "</tr><tr align=center>" ^ mstr ^ "</tr><tr align=center valign=top>" ^ dstr ^ "</tr></table>"

let html_render tree = html_render_table ([],false,false,html_render_deep CTX_NORMAL tree)

(* Dynamic loading*)
type encoding_t = LATIN1 | LATIN2 | UTF8

let modules_ams = ref false
let modules_nonascii = ref false
let modules_encoding = ref UTF8

let tex_use_ams ()     = modules_ams := true
let tex_use_nonascii () = modules_nonascii := true
let tex_mod_reset ()   = (modules_ams := false; modules_nonascii := false; modules_encoding := UTF8)

let get_encoding = function
    UTF8 -> "\\usepackage{ucs}\n\\usepackage[utf8]{inputenc}\n"
  | LATIN1 -> "\\usepackage[latin1]{inputenc}\n"
  | LATIN2 -> "\\usepackage[latin2]{inputenc}\n"

let get_preface ()  = "\\nonstopmode\n\\documentclass[12pt]{article}\n" ^
		      (if !modules_nonascii then get_encoding !modules_encoding else "") ^
		      (if !modules_ams then "\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\usepackage{amssymb}\n" else "") ^
		      "\\pagestyle{empty}\n\\begin{document}\n$$\n"
let get_footer  ()  = "\n$$\n\\end{document}\n"

let set_encoding = function
    "ISO-8859-1" -> modules_encoding := LATIN1
  | "iso-8859-1" -> modules_encoding := LATIN1
  | "ISO-8859-2" -> modules_encoding := LATIN2
  | _ -> modules_encoding := UTF8

(* Turn that into hash table lookup *)
exception Illegal_tex_function of string

let find = function
      "\\alpha"            -> LITERAL (HTMLABLEC (FONT_UF,  "\\alpha ", "&alpha;"))
    | "\\Alpha"            -> LITERAL (HTMLABLEC (FONT_RTI, "A", "&Alpha;"))
    | "\\beta"             -> LITERAL (HTMLABLEC (FONT_UF,  "\\beta ",  "&beta;"))
    | "\\Beta"             -> LITERAL (HTMLABLEC (FONT_RTI, "B",  "&Beta;"))
    | "\\gamma"            -> LITERAL (HTMLABLEC (FONT_UF,  "\\gamma ", "&gamma;"))
    | "\\Gamma"            -> LITERAL (HTMLABLEC (FONT_RTI, "\\Gamma ", "&Gamma;"))
    | "\\delta"            -> LITERAL (HTMLABLEC (FONT_UF,  "\\delta ", "&delta;"))
    | "\\Delta"            -> LITERAL (HTMLABLEC (FONT_RTI, "\\Delta ", "&Delta;"))
    | "\\epsilon"          -> LITERAL (HTMLABLEC (FONT_UF,  "\\epsilon ", "&epsilon;"))
    | "\\Epsilon"          -> LITERAL (HTMLABLEC (FONT_RTI, "E", "&Epsilon;"))
    | "\\varepsilon"       -> LITERAL (TEX_ONLY "\\varepsilon ")
    | "\\zeta"             -> LITERAL (HTMLABLEC (FONT_UF,  "\\zeta ", "&zeta;"))
    | "\\Zeta"             -> LITERAL (HTMLABLEC (FONT_RTI, "Z", "&Zeta;"))
    | "\\eta"              -> LITERAL (HTMLABLEC (FONT_UF,  "\\eta ", "&eta;"))
    | "\\Eta"              -> LITERAL (HTMLABLEC (FONT_RTI, "H", "&Eta;"))
    | "\\theta"            -> LITERAL (HTMLABLEC (FONT_UF,  "\\theta ", "&theta;"))
    | "\\Theta"            -> LITERAL (HTMLABLEC (FONT_RTI, "\\Theta ", "&Theta;"))
    | "\\vartheta"         -> LITERAL (HTMLABLE  (FONT_UF,  "\\vartheta ", "&thetasym;"))
    | "\\thetasym"         -> LITERAL (HTMLABLE  (FONT_UF,  "\\vartheta ", "&thetasym;"))
    | "\\iota"             -> LITERAL (HTMLABLEC (FONT_UF,  "\\iota ", "&iota;"))
    | "\\Iota"             -> LITERAL (HTMLABLEC (FONT_RTI, "I", "&Iota;"))
    | "\\kappa"            -> LITERAL (HTMLABLEC (FONT_UF,  "\\kappa ", "&kappa;"))
    | "\\Kappa"            -> LITERAL (HTMLABLEC (FONT_RTI, "K", "&Kappa;"))
    | "\\lambda"           -> LITERAL (HTMLABLEC (FONT_UF,  "\\lambda ", "&lambda;"))
    | "\\Lambda"           -> LITERAL (HTMLABLEC (FONT_RTI, "\\Lambda ", "&Lambda;"))
    | "\\mu"               -> LITERAL (HTMLABLEC (FONT_UF,  "\\mu ", "&mu;"))
    | "\\Mu"               -> LITERAL (HTMLABLEC (FONT_RTI, "M", "&Mu;"))
    | "\\nu"               -> LITERAL (HTMLABLEC (FONT_UF,  "\\nu ", "&nu;"))
    | "\\Nu"               -> LITERAL (HTMLABLEC (FONT_RTI, "N", "&Nu;"))
    | "\\pi"               -> LITERAL (HTMLABLEC (FONT_UF,  "\\pi ", "&pi;"))
    | "\\Pi"               -> LITERAL (HTMLABLEC (FONT_RTI, "\\Pi ", "&Pi;"))
    | "\\varpi"            -> LITERAL (TEX_ONLY "\\varpi ")
    | "\\rho"              -> LITERAL (HTMLABLEC (FONT_UF,  "\\rho ", "&rho;"))
    | "\\Rho"              -> LITERAL (HTMLABLEC (FONT_RTI, "P", "&Rho;"))
    | "\\varrho"           -> LITERAL (TEX_ONLY "\\varrho ")
    | "\\sigma"            -> LITERAL (HTMLABLEC (FONT_UF,  "\\sigma ", "&sigma;"))
    | "\\Sigma"            -> LITERAL (HTMLABLEC (FONT_RTI, "\\Sigma ", "&Sigma;"))
    | "\\varsigma"         -> LITERAL (TEX_ONLY "\\varsigma ")
    | "\\tau"              -> LITERAL (HTMLABLEC (FONT_UF,  "\\tau ", "&tau;"))
    | "\\Tau"              -> LITERAL (HTMLABLEC (FONT_RTI, "T", "&Tau;"))
    | "\\upsilon"          -> LITERAL (HTMLABLEC (FONT_UF,  "\\upsilon ", "&upsilon;"))
    | "\\Upsilon"          -> LITERAL (HTMLABLEC (FONT_RTI, "\\Upsilon ", "&Upsilon;"))
    | "\\phi"              -> LITERAL (HTMLABLEC (FONT_UF,  "\\phi ", "&phi;"))
    | "\\Phi"              -> LITERAL (HTMLABLEC (FONT_RTI, "\\Phi ", "&Phi;"))
    | "\\varphi"           -> LITERAL (TEX_ONLY "\\varphi ")
    | "\\chi"              -> LITERAL (HTMLABLEC (FONT_UF,  "\\chi ", "&chi;"))
    | "\\Chi"              -> LITERAL (HTMLABLEC (FONT_RTI, "X", "&Chi;"))
    | "\\psi"              -> LITERAL (HTMLABLEC (FONT_UF,  "\\psi ", "&psi;"))
    | "\\Psi"              -> LITERAL (HTMLABLEC (FONT_RTI, "\\Psi ", "&Psi;"))
    | "\\omega"            -> LITERAL (HTMLABLEC (FONT_UF,  "\\omega ", "&omega;"))
    | "\\Omega"            -> LITERAL (HTMLABLEC (FONT_RTI, "\\Omega ", "&Omega;"))
    | "\\xi"               -> LITERAL (HTMLABLEC (FONT_UF,  "\\xi ", "&xi;"))
    | "\\Xi"               -> LITERAL (HTMLABLEC (FONT_RTI, "\\Xi ", "&Xi;"))
    | "\\aleph"            -> LITERAL (HTMLABLE  (FONT_UF,  "\\aleph ", "&alefsym;"))
    | "\\alef"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\aleph ", "&alefsym;"))
    | "\\alefsym"          -> LITERAL (HTMLABLE  (FONT_UF,  "\\aleph ", "&alefsym;"))
    | "\\larr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\leftarrow ", "&larr;"))
    | "\\leftarrow"        -> LITERAL (HTMLABLE  (FONT_UF,  "\\leftarrow ", "&larr;"))
    | "\\rarr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\rightarrow ", "&rarr;"))
    | "\\to"               -> LITERAL (HTMLABLE  (FONT_UF,  "\\to ", "&rarr;"))
    | "\\gets"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\gets ", "&larr;"))
    | "\\rightarrow"       -> LITERAL (HTMLABLE  (FONT_UF,  "\\rightarrow ", "&rarr;"))
    | "\\longleftarrow"    -> LITERAL (HTMLABLE  (FONT_UF,  "\\longleftarrow ", "&larr;"))
    | "\\longrightarrow"   -> LITERAL (HTMLABLE  (FONT_UF,  "\\longrightarrow ", "&rarr;"))
    | "\\Larr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftarrow ", "&lArr;"))
    | "\\lArr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftarrow ", "&lArr;"))
    | "\\Leftarrow"        -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftarrow ", "&lArr;"))
    | "\\Rarr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Rightarrow ", "&rArr;"))
    | "\\rArr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Rightarrow ", "&rArr;"))
    | "\\Rightarrow"       -> LITERAL (HTMLABLE  (FONT_UF,  "\\Rightarrow ", "&rArr;"))
    | "\\mapsto"           -> LITERAL (HTMLABLE  (FONT_UF,  "\\mapsto ", "&rarr;"))
    | "\\longmapsto"       -> LITERAL (HTMLABLE  (FONT_UF,  "\\longmapsto ", "&rarr;"))
    | "\\Longleftarrow"    -> LITERAL (HTMLABLE  (FONT_UF,  "\\Longleftarrow ", "&lArr;"))
    | "\\Longrightarrow"   -> LITERAL (HTMLABLE  (FONT_UF,  "\\Longrightarrow ", "&rArr;"))
    | "\\uarr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\uparrow ", "&uarr;"))
    | "\\uparrow"          -> LITERAL (HTMLABLE  (FONT_UF,  "\\uparrow ", "&uarr;"))
    | "\\uArr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Uparrow ", "&uArr;"))
    | "\\Uarr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Uparrow ", "&uArr;"))
    | "\\Uparrow"          -> LITERAL (HTMLABLE  (FONT_UF,  "\\Uparrow ", "&uArr;"))
    | "\\darr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\downarrow ", "&darr;"))
    | "\\downarrow"        -> LITERAL (HTMLABLE  (FONT_UF,  "\\downarrow ", "&darr;"))
    | "\\dArr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Downarrow ", "&dArr;"))
    | "\\Darr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Downarrow ", "&dArr;"))
    | "\\Downarrow"        -> LITERAL (HTMLABLE  (FONT_UF,  "\\Downarrow ", "&dArr;"))
    | "\\updownarrow"      -> LITERAL (TEX_ONLY "\\updownarrow ")
    | "\\Updownarrow"      -> LITERAL (TEX_ONLY "\\Updownarrow ")
    | "\\leftrightarrow"   -> LITERAL (HTMLABLE  (FONT_UF,  "\\leftrightarrow ", "&harr;"))
    | "\\lrarr"            -> LITERAL (HTMLABLE  (FONT_UF,  "\\leftrightarrow ", "&harr;"))
    | "\\harr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\leftrightarrow ", "&harr;"))
    | "\\Leftrightarrow"   -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftrightarrow ", "&hArr;"))
    | "\\Lrarr"            -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftrightarrow ", "&hArr;"))
    | "\\Harr"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftrightarrow ", "&hArr;"))
    | "\\lrArr"            -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftrightarrow ", "&hArr;"))
    | "\\hAar"             -> LITERAL (HTMLABLE  (FONT_UF,  "\\Leftrightarrow ", "&hArr;"))
    | "\\Longleftrightarrow"->LITERAL (HTMLABLE  (FONT_UF,  "\\Longleftrightarrow ", "&harr;"))
    | "\\iff"              -> LITERAL (HTMLABLE  (FONT_UF,  "\\iff ", "&harr;"))
    | "\\searrow"          -> LITERAL (TEX_ONLY "\\searrow ")
    | "\\nearrow"          -> LITERAL (TEX_ONLY "\\nearrow ")
    | "\\swarrow"          -> LITERAL (TEX_ONLY "\\swarrow ")
    | "\\nwarrow"          -> LITERAL (TEX_ONLY "\\nwarrow ")
    | "\\sim"              -> LITERAL (TEX_ONLY "\\sim ")
    | "\\simeq"            -> LITERAL (TEX_ONLY "\\simeq ")
    | "\\star"             -> LITERAL (TEX_ONLY "\\star ")
    | "\\ell"              -> LITERAL (TEX_ONLY "\\ell ")
    | "\\P"                -> LITERAL (TEX_ONLY "\\P ")
    | "\\smile"            -> LITERAL (TEX_ONLY "\\smile ")
    | "\\frown"            -> LITERAL (TEX_ONLY "\\frown ")
    | "\\bigcap"           -> LITERAL (TEX_ONLY "\\bigcap ")
    | "\\bigodot"          -> LITERAL (TEX_ONLY "\\bigodot ")
    | "\\bigcup"           -> LITERAL (TEX_ONLY "\\bigcup ")
    | "\\bigotimes"        -> LITERAL (TEX_ONLY "\\bigotimes ")
    | "\\coprod"           -> LITERAL (TEX_ONLY "\\coprod ")
    | "\\bigsqcup"         -> LITERAL (TEX_ONLY "\\bigsqcup ")
    | "\\bigoplus"         -> LITERAL (TEX_ONLY "\\bigoplus ") 
    | "\\bigvee"           -> LITERAL (TEX_ONLY "\\bigvee ") 
    | "\\biguplus"         -> LITERAL (TEX_ONLY "\\biguplus ")
    | "\\oint"	           -> LITERAL (TEX_ONLY "\\oint ")
    | "\\bigwedge"         -> LITERAL (TEX_ONLY "\\bigwedge ")
    | "\\models"           -> LITERAL (TEX_ONLY "\\models ")
    | "\\vdash"            -> LITERAL (TEX_ONLY "\\vdash ")
    | "\\triangle"         -> LITERAL (TEX_ONLY "\\triangle ")
    | "\\wr"		   -> LITERAL (TEX_ONLY "\\wr ")
    | "\\triangleleft"     -> LITERAL (TEX_ONLY "\\triangleleft ")
    | "\\triangleright"    -> LITERAL (TEX_ONLY "\\triangleright ")
    | "\\textvisiblespace" -> LITERAL (TEX_ONLY "\\textvisiblespace ")
    | "\\ker"	           -> LITERAL (TEX_ONLY "\\ker ")
    | "\\lim"	           -> LITERAL (TEX_ONLY "\\lim ")
    | "\\limsup"           -> LITERAL (TEX_ONLY "\\limsup ")
    | "\\liminf"           -> LITERAL (TEX_ONLY "\\liminf ")
    | "\\sup"	           -> LITERAL (TEX_ONLY "\\sup ")
    | "\\Pr"	           -> LITERAL (TEX_ONLY "\\Pr ")
    | "\\hom"	           -> LITERAL (TEX_ONLY "\\hom ")
    | "\\arg"	           -> LITERAL (TEX_ONLY "\\arg ")
    | "\\dim"	           -> LITERAL (TEX_ONLY "\\dim ")
    | "\\inf"	           -> LITERAL (TEX_ONLY "\\inf ")
    | "\\circ"	           -> LITERAL (TEX_ONLY "\\circ ")
    | "\\hbar"	           -> LITERAL (TEX_ONLY "\\hbar ")
    | "\\imath"	           -> LITERAL (TEX_ONLY "\\imath ")
    | "\\lnot"	           -> LITERAL (TEX_ONLY "\\lnot ")
    | "\\hookrightarrow"   -> LITERAL (TEX_ONLY "\\hookrightarrow ")
    | "\\hookleftarrow"    -> LITERAL (TEX_ONLY "\\hookleftarrow ")
    | "\\mp"               -> LITERAL (TEX_ONLY "\\mp ")
    | "\\approx"           -> LITERAL (TEX_ONLY "\\approx ")
    | "\\flat"             -> LITERAL (TEX_ONLY "\\flat ")
    | "\\sharp"            -> LITERAL (TEX_ONLY "\\sharp ")
    | "\\natural"          -> LITERAL (TEX_ONLY "\\natural ")
    | "\\int"	           -> LITERAL (HTMLABLE_BIG ("\\int ", "&int;"))
    | "\\sum"	           -> LITERAL (HTMLABLE_BIG ("\\sum ", "&sum;"))
    | "\\prod"	           -> LITERAL (HTMLABLE_BIG ("\\prod ", "&prod;"))
    | "\\vdots"            -> LITERAL (TEX_ONLY "\\vdots ")
    | "\\top"              -> LITERAL (TEX_ONLY "\\top ")
    | "\\sin"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\sin ","sin"))
    | "\\cos"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\cos ","cos"))
    | "\\sinh"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\sinh ","sinh"))
    | "\\cosh"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\cosh ","cosh"))
    | "\\tan"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\tan ","tan"))
    | "\\tanh"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\tanh ","tanh"))
    | "\\sec"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\sec ","sec"))
    | "\\csc"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\csc ","csc"))
    | "\\arcsin"           -> LITERAL (HTMLABLEC(FONT_UFH,"\\arcsin ", "arcsin"))
    | "\\arctan"           -> LITERAL (HTMLABLEC(FONT_UFH,"\\arctan ","arctan"))
    | "\\cot"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\cot ","cot"))
    | "\\coth"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\coth ","coth"))
    | "\\log"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\log ", "log"))
    | "\\lg"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\lg ", "lg"))
    | "\\ln"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\ln ", "ln"))
    | "\\exp"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\exp ", "exp"))
    | "\\min"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\min ", "min"))
    | "\\max"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\max ", "max"))
    | "\\gcd"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\gcd ", "gcd"))
    | "\\deg"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\deg ", "deg"))
    | "\\det"	           -> LITERAL (HTMLABLEC(FONT_UFH,"\\det ", "det"))
    | "\\bullet"           -> LITERAL (HTMLABLE (FONT_UFH, "\\bullet ", "&bull;"))
    | "\\bull"             -> LITERAL (HTMLABLE (FONT_UFH, "\\bullet ", "&bull;"))
    | "\\angle"            -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UF, "\\angle ", "&ang;")))
    | "\\dagger"           -> LITERAL (HTMLABLE (FONT_UFH, "\\dagger ", "&dagger;"))
    | "\\ddagger"          -> LITERAL (HTMLABLE (FONT_UFH, "\\ddagger ", "&Dagger;"))
    | "\\Dagger"           -> LITERAL (HTMLABLE (FONT_UFH, "\\ddagger ", "&Dagger;"))
    | "\\colon"            -> LITERAL (HTMLABLEC(FONT_UFH, "\\colon ", ":"))
    | "\\Vert"             -> LITERAL (HTMLABLE (FONT_UFH, "\\Vert ", "||"))
    | "\\vert"             -> LITERAL (HTMLABLE (FONT_UFH, "\\vert ", "|"))
    | "\\wp"               -> LITERAL (HTMLABLE (FONT_UF,  "\\wp ", "&weierp;"))
    | "\\weierp"           -> LITERAL (HTMLABLE (FONT_UF,  "\\wp ", "&weierp;"))
    | "\\wedge"            -> LITERAL (HTMLABLE (FONT_UF,  "\\wedge ", "&and;"))
    | "\\and"              -> LITERAL (HTMLABLE (FONT_UF,  "\\land ", "&and;"))
    | "\\land"             -> LITERAL (HTMLABLE (FONT_UF,  "\\land ", "&and;"))
    | "\\vee"              -> LITERAL (HTMLABLE (FONT_UF,  "\\vee ", "&or;"))
    | "\\or"               -> LITERAL (HTMLABLE (FONT_UF,  "\\lor ", "&or;"))
    | "\\lor"              -> LITERAL (HTMLABLE (FONT_UF,  "\\lor ", "&or;"))
    | "\\sub"              -> LITERAL (HTMLABLE (FONT_UF,  "\\subset ", "&sub;"))
    | "\\supe"             -> LITERAL (HTMLABLE (FONT_UF,  "\\supseteq ", "&supe;"))
    | "\\sube"             -> LITERAL (HTMLABLE (FONT_UF,  "\\subseteq ", "&sube;"))
    | "\\supset"           -> LITERAL (HTMLABLE (FONT_UF,  "\\supset ", "&sup;"))
    | "\\subset"           -> LITERAL (HTMLABLE (FONT_UF,  "\\subset ", "&sub;"))
    | "\\supseteq"         -> LITERAL (HTMLABLE (FONT_UF,  "\\supseteq ", "&supe;"))
    | "\\subseteq"         -> LITERAL (HTMLABLE (FONT_UF,  "\\subseteq ", "&sube;"))
    | "\\perp"             -> LITERAL (HTMLABLE (FONT_UF,  "\\perp ", "&perp;"))
    | "\\bot"              -> LITERAL (HTMLABLE (FONT_UF,  "\\bot ", "&perp;"))
    | "\\lfloor"           -> LITERAL (HTMLABLE (FONT_UF,  "\\lfloor ", "&lfloor;"))
    | "\\rfloor"           -> LITERAL (HTMLABLE (FONT_UF,  "\\rfloor ", "&rfloor;"))
    | "\\lceil"            -> LITERAL (HTMLABLE (FONT_UF,  "\\lceil ", "&lceil;"))
    | "\\rceil"            -> LITERAL (HTMLABLE (FONT_UF,  "\\rceil ", "&rceil;"))
    | "\\lbrace"           -> LITERAL (HTMLABLEC(FONT_UFH, "\\lbrace ", "{"))
    | "\\rbrace"           -> LITERAL (HTMLABLEC(FONT_UFH, "\\rbrace ", "}"))
    | "\\infty"            -> LITERAL (HTMLABLE (FONT_UF,  "\\infty ", "&infin;"))
    | "\\infin"            -> LITERAL (HTMLABLE (FONT_UF,  "\\infty ", "&infin;"))
    | "\\isin"             -> LITERAL (HTMLABLE (FONT_UF,  "\\in ", "&isin;"))
    | "\\in"               -> LITERAL (HTMLABLE (FONT_UF,  "\\in ", "&isin;"))
    | "\\ni"               -> LITERAL (HTMLABLE (FONT_UF,  "\\ni ", "&ni;"))
    | "\\notin"            -> LITERAL (HTMLABLE (FONT_UF,  "\\notin ", "&notin;"))
    | "\\smallsetminus"    -> (tex_use_ams (); LITERAL (TEX_ONLY "\\smallsetminus "))
    | "\\And"              -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH, "\\And ", "&nbsp;&amp;&nbsp;")))
    | "\\forall"           -> LITERAL (HTMLABLE (FONT_UFH, "\\forall ", "&forall;"))
    | "\\exists"           -> LITERAL (HTMLABLE (FONT_UFH, "\\exists ", "&exist;"))
    | "\\exist"            -> LITERAL (HTMLABLE (FONT_UFH, "\\exists ", "&exist;"))
    | "\\equiv"            -> LITERAL (HTMLABLE (FONT_UFH, "\\equiv ", "&equiv;"))
    | "\\ne"               -> LITERAL (HTMLABLE (FONT_UFH, "\\neq ", "&ne;"))
    | "\\neq"              -> LITERAL (HTMLABLE (FONT_UFH, "\\neq ", "&ne;"))
    | "\\Re"               -> LITERAL (HTMLABLE (FONT_UF,  "\\Re ", "&real;"))
    | "\\real"             -> LITERAL (HTMLABLE (FONT_UF,  "\\Re ", "&real;"))
    | "\\Im"               -> LITERAL (HTMLABLE (FONT_UF,  "\\Im ", "&image;"))
    | "\\image"            -> LITERAL (HTMLABLE (FONT_UF,  "\\Im ", "&image;"))
    | "\\prime"            -> LITERAL (HTMLABLE (FONT_UFH,"\\prime ", "&prime;"))
    | "\\backslash"        -> LITERAL (HTMLABLE (FONT_UFH,"\\backslash ", "\\"))
    | "\\setminus"         -> LITERAL (HTMLABLE (FONT_UFH,"\\setminus ", "\\"))
    | "\\times"            -> LITERAL (HTMLABLE (FONT_UFH,"\\times ", "&times;"))
    | "\\pm"               -> LITERAL (HTMLABLE (FONT_UFH,"\\pm ", "&plusmn;"))
    | "\\plusmn"           -> LITERAL (HTMLABLE (FONT_UFH,"\\pm ", "&plusmn;"))
    | "\\cdot"             -> LITERAL (HTMLABLE (FONT_UFH,"\\cdot ", "&sdot;"))
    | "\\cdots"            -> LITERAL (HTMLABLE (FONT_UFH,"\\cdots ", "&sdot;&sdot;&sdot;"))
    | "\\sdot"             -> LITERAL (HTMLABLE (FONT_UFH,"\\cdot ", "&sdot;"))
    | "\\oplus"            -> LITERAL (HTMLABLE (FONT_UF, "\\oplus ", "&oplus;"))
    | "\\otimes"           -> LITERAL (HTMLABLE (FONT_UF, "\\otimes ", "&otimes;"))
    | "\\cap"              -> LITERAL (HTMLABLE (FONT_UF, "\\cap ", "&cap;"))
    | "\\cup"              -> LITERAL (HTMLABLE (FONT_UF, "\\cup ", "&cup;"))
    | "\\empty"            -> LITERAL (HTMLABLE (FONT_UF, "\\emptyset ", "&empty;"))
    | "\\emptyset"         -> LITERAL (HTMLABLE (FONT_UF, "\\emptyset ", "&empty;"))
    | "\\O"                -> LITERAL (HTMLABLE (FONT_UF, "\\emptyset ", "&empty;"))
    | "\\S"                -> LITERAL (HTMLABLE (FONT_UFH,"\\S ", "&sect;"))
    | "\\sect"             -> LITERAL (HTMLABLE (FONT_UFH,"\\S ", "&sect;"))
    | "\\nabla"            -> LITERAL (HTMLABLE (FONT_UF, "\\nabla ", "&nabla;"))
    | "\\geq"              -> LITERAL (HTMLABLE (FONT_UFH,"\\geq ", "&ge;"))
    | "\\ge"               -> LITERAL (HTMLABLE (FONT_UFH,"\\geq ", "&ge;"))
    | "\\leq"              -> LITERAL (HTMLABLE (FONT_UFH,"\\leq ", "&le;"))
    | "\\le"               -> LITERAL (HTMLABLE (FONT_UFH,"\\leq ", "&le;"))
    | "\\cong"             -> LITERAL (HTMLABLE (FONT_UF, "\\cong ", "&cong;"))
    | "\\ang"              -> LITERAL (HTMLABLE (FONT_UF, "\\angle ", "&ang;"))
    | "\\part"             -> LITERAL (HTMLABLE (FONT_UF, "\\partial ", "&part;"))
    | "\\partial"          -> LITERAL (HTMLABLE (FONT_UF, "\\partial ", "&part;"))
    | "\\ldots"            -> LITERAL (HTMLABLE (FONT_UFH,"\\ldots ", "..."))
    | "\\dots"             -> LITERAL (HTMLABLE (FONT_UFH,"\\dots ", "..."))
    | "\\quad" 		   -> LITERAL (HTMLABLE (FONT_UF, "\\quad ","&nbsp;&nbsp;"))
    | "\\qquad"		   -> LITERAL (HTMLABLE (FONT_UF, "\\qquad ","&nbsp;&nbsp;&nbsp;&nbsp;"))
    | "\\mid"              -> LITERAL (HTMLABLE (FONT_UFH,"\\mid ", "|"))
    | "\\neg"              -> LITERAL (HTMLABLE (FONT_UFH,"\\neg ", "&not;"))
    | "\\langle"           -> LITERAL (HTMLABLE (FONT_UFH,"\\langle ","&lang;"))
    | "\\rangle"           -> LITERAL (HTMLABLE (FONT_UFH,"\\rangle ","&rang;"))
    | "\\lang"             -> LITERAL (HTMLABLE (FONT_UFH,"\\langle ","&lang;"))
    | "\\rang"             -> LITERAL (HTMLABLE (FONT_UFH,"\\rangle ","&rang;"))
    | "\\clubs"            -> LITERAL (TEX_ONLY "\\clubsuit ")
    | "\\clubsuit"         -> LITERAL (TEX_ONLY "\\clubsuit ")
    | "\\spades"           -> LITERAL (TEX_ONLY "\\spadesuit ")
    | "\\spadesuit"        -> LITERAL (TEX_ONLY "\\spadesuit ")
    | "\\hearts"           -> LITERAL (TEX_ONLY "\\heartsuit ")
    | "\\heartsuit"        -> LITERAL (TEX_ONLY "\\heartsuit ")
    | "\\diamonds"         -> LITERAL (TEX_ONLY "\\diamondsuit ")
    | "\\diamondsuit"      -> LITERAL (TEX_ONLY "\\diamondsuit ")
    | "\\implies"          -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UF, "\\implies ", "&rArr;")))
    | "\\mod"	           -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mod ", "mod")))
    | "\\Diamond"          -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UF, "\\Diamond ", "&loz;")))
    | "\\dotsb"            -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UF, "\\dotsb ", "&sdot;&sdot;&sdot;")))
    | "\\reals"            -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{R}", "<b>R</b>")))
    | "\\Reals"            -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{R}", "<b>R</b>")))
    | "\\R"                -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{R}", "<b>R</b>")))
    | "\\cnums"            -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{C}", "<b>C</b>")))
    | "\\Complex"          -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{C}", "<b>C</b>")))
    | "\\Z"                -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{Z}", "<b>Z</b>")))
    | "\\natnums"          -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{N}", "<b>N</b>")))
    | "\\N"		   -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\mathbb{N}", "<b>N</b>")))
    | "\\lVert"            -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\lVert ", "||")))
    | "\\rVert"            -> (tex_use_ams (); LITERAL (HTMLABLE (FONT_UFH,"\\rVert ", "||")))
    | "\\left"             -> LITERAL (HTMLABLE (FONT_UF, "\\left ", ""))
    | "\\right"            -> LITERAL (HTMLABLE (FONT_UF, "\\right ", ""))
    | "\\nmid"             -> (tex_use_ams (); LITERAL (TEX_ONLY "\\nmid "))
    | "\\lesssim"          -> (tex_use_ams (); LITERAL (TEX_ONLY "\\lesssim "))
    | "\\ngeq"             -> (tex_use_ams (); LITERAL (TEX_ONLY "\\ngeq "))
    | "\\smallsmile"       -> (tex_use_ams (); LITERAL (TEX_ONLY "\\smallsmile "))
    | "\\smallfrown"       -> (tex_use_ams (); LITERAL (TEX_ONLY "\\smallfrown "))
    | "\\nleftarrow"       -> (tex_use_ams (); LITERAL (TEX_ONLY "\\nleftarrow "))
    | "\\nrightarrow"      -> (tex_use_ams (); LITERAL (TEX_ONLY "\\nrightarrow "))
    | "\\trianglelefteq"   -> (tex_use_ams (); LITERAL (TEX_ONLY "\\trianglelefteq "))
    | "\\trianglerighteq"  -> (tex_use_ams (); LITERAL (TEX_ONLY "\\trianglerighteq "))
    | "\\square"           -> (tex_use_ams (); LITERAL (TEX_ONLY "\\square "))
    | "\\supsetneq"        -> (tex_use_ams (); LITERAL (TEX_ONLY "\\supsetneq "))
    | "\\subsetneq"        -> (tex_use_ams (); LITERAL (TEX_ONLY "\\subsetneq "))
    | "\\Box"              -> (tex_use_ams (); LITERAL (TEX_ONLY "\\Box "))
    | "\\nleq"             -> (tex_use_ams (); LITERAL (TEX_ONLY "\\nleq "))
    | "\\upharpoonright"   -> (tex_use_ams (); LITERAL (TEX_ONLY "\\upharpoonright "))
    | "\\upharpoonleft"    -> (tex_use_ams (); LITERAL (TEX_ONLY "\\upharpoonleft "))
    | "\\downharpoonright" -> (tex_use_ams (); LITERAL (TEX_ONLY "\\downharpoonright "))
    | "\\downharpoonleft"  -> (tex_use_ams (); LITERAL (TEX_ONLY "\\downharpoonleft "))
    | "\\nless"            -> (tex_use_ams (); LITERAL (TEX_ONLY "\\nless "))
    | "\\Vdash"            -> (tex_use_ams (); LITERAL (TEX_ONLY "\\Vdash "))
    | "\\vDash"            -> (tex_use_ams (); LITERAL (TEX_ONLY "\\vDash "))
    | "\\varkappa"         -> (tex_use_ams (); LITERAL (TEX_ONLY "\\varkappa "))
    | "\\digamma"          -> (tex_use_ams (); LITERAL (TEX_ONLY "\\digamma "))
    | "\\beth"             -> (tex_use_ams (); LITERAL (TEX_ONLY "\\beth "))
    | "\\daleth"           -> (tex_use_ams (); LITERAL (TEX_ONLY "\\daleth "))
    | "\\gimel"            -> (tex_use_ams (); LITERAL (TEX_ONLY "\\gimel "))
    | "\\complement"       -> (tex_use_ams (); LITERAL (TEX_ONLY "\\complement "))
    | "\\eth"              -> (tex_use_ams (); LITERAL (TEX_ONLY "\\eth "))
    | "\\hslash"           -> (tex_use_ams (); LITERAL (TEX_ONLY "\\hslash "))
    | "\\mho"              -> (tex_use_ams (); LITERAL (TEX_ONLY "\\mho "))
    | "\\Finv"             -> (tex_use_ams (); LITERAL (TEX_ONLY "\\Finv "))
    | "\\Game"             -> (tex_use_ams (); LITERAL (TEX_ONLY "\\Game "))
    | "\\varlimsup"        -> (tex_use_ams (); LITERAL (TEX_ONLY "\\varlimsup "))
    | "\\varliminf"        -> (tex_use_ams (); LITERAL (TEX_ONLY "\\varliminf "))
    | "\\varinjlim"        -> (tex_use_ams (); LITERAL (TEX_ONLY "\\varinjlim "))
    | "\\varprojlim"       -> (tex_use_ams (); LITERAL (TEX_ONLY "\\varprojlim "))
    | "\\injlim"           -> (tex_use_ams (); LITERAL (TEX_ONLY "\\injlim "))
    | "\\projlim"          -> (tex_use_ams (); LITERAL (TEX_ONLY "\\projlim "))
    | "\\iint"             -> (tex_use_ams (); LITERAL (TEX_ONLY "\\iint "))
    | "\\iiint"            -> (tex_use_ams (); LITERAL (TEX_ONLY "\\iiint "))
    | "\\iiiint"           -> (tex_use_ams (); LITERAL (TEX_ONLY "\\iiiint "))
    | "\\varnothing"       -> (tex_use_ams (); LITERAL (TEX_ONLY "\\varnothing "))
    | "\\hat"	           -> FUN_AR1 "\\hat "
    | "\\widehat"          -> FUN_AR1 "\\widehat "
    | "\\overline"         -> FUN_AR1 "\\overline "
    | "\\overbrace"        -> FUN_AR1 "\\overbrace "
    | "\\underline"        -> FUN_AR1 "\\underline "
    | "\\underbrace"       -> FUN_AR1 "\\underbrace "
    | "\\overleftarrow"    -> FUN_AR1 "\\overleftarrow "
    | "\\overrightarrow"   -> FUN_AR1 "\\overrightarrow "
    | "\\overleftrightarrow"->FUN_AR1 "\\overleftrightarrow "
    | "\\check"	           -> FUN_AR1 "\\check "
    | "\\acute"	           -> FUN_AR1 "\\acute "
    | "\\grave"	           -> FUN_AR1 "\\grave "
    | "\\bar"	           -> FUN_AR1 "\\bar "
    | "\\vec"	           -> FUN_AR1 "\\vec "
    | "\\dot"	           -> FUN_AR1 "\\dot "
    | "\\ddot"	           -> FUN_AR1 "\\ddot "
    | "\\breve"	           -> FUN_AR1 "\\breve "
    | "\\tilde"	           -> FUN_AR1 "\\tilde "
    | "\\not"	           -> FUN_AR1 "\\not "
    | "\\choose"           -> FUN_INFIX "\\choose "
    | "\\atop"             -> FUN_INFIX "\\atop "
    | "\\binom"            -> FUN_AR2 "\\binom "
    | "\\frac"             -> FUN_AR2h ("\\frac ", fun num den -> html_render [num], "<hr style=\"{background: black}\">", html_render [den])
    | "\\over"             -> FUN_INFIXh ("\\over ", fun num den -> html_render num, "<hr style=\"{background: black}\">", html_render den)
(* ? *)
    | "\\sqrt"             -> FUN_AR1 "\\sqrt "
    | "\\pmod"             -> FUN_AR1hl ("\\pmod ", ("(mod ", ")"))
    | "\\bmod"             -> FUN_AR1hl ("\\bmod ", ("mod ", ""))
    | "\\emph"             -> FUN_AR1 "\\emph "
    | "\\texttt"           -> FUN_AR1 "\\texttt "
    | "\\textbf"           -> FUN_AR1 "\\textbf "
    | "\\textit"           -> FUN_AR1hf ("\\textit ", FONTFORCE_IT)
    | "\\textrm"           -> FUN_AR1hf ("\\textrm ", FONTFORCE_RM)
    | "\\rm"               -> DECLh ("\\rm ", FONTFORCE_RM)
    | "\\it"               -> DECLh ("\\it ", FONTFORCE_IT)
    | "\\cal"              -> DECL "\\cal "
    | "\\bf"               -> DECL "\\bf "
    | "\\mathit"           -> (tex_use_ams (); FUN_AR1hf ("\\mathit ", FONTFORCE_IT))
    | "\\mathrm"           -> (tex_use_ams (); FUN_AR1hf ("\\mathrm ", FONTFORCE_RM))
    | "\\boldsymbol"       -> (tex_use_ams (); FUN_AR1 "\\boldsymbol ")
    | "\\bold"             -> (tex_use_ams (); FUN_AR1 "\\mathbf ")
    | "\\Bbb"              -> (tex_use_ams (); FUN_AR1 "\\mathbb ")
    | "\\mathbf"           -> (tex_use_ams (); FUN_AR1 "\\mathbf ")
    | "\\mathsf"           -> (tex_use_ams (); FUN_AR1 "\\mathsf ")
    | "\\mathcal"          -> (tex_use_ams (); FUN_AR1 "\\mathcal ")
    | "\\mathbb"           -> (tex_use_ams (); FUN_AR1 "\\mathbb ")
    | "\\mathfrak"         -> (tex_use_ams (); FUN_AR1 "\\mathfrak ")
    | "\\operatorname"     -> (tex_use_ams (); FUN_AR1 "\\operatorname ")
(* + *)
    | "\\mbox"             -> raise (Failure "malformatted \\mbox")
    | "\\vbox"             -> raise (Failure "malformatted \\vbox")
    | "\\hbox"             -> raise (Failure "malformatted \\hbox")
    | s                    -> raise (Illegal_tex_function s)
