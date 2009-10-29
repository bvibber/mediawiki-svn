<?php
/**
 * Internationalisation file for extension Wikilog.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Juliano F. Ravasi
 */
$messages['en'] = array(
	# Extension information
	'wikilog-desc'				=> 'Adds blogging features, creating a wiki-blog hybrid.',
	'wikilog-auto'				=> 'Wikilog Auto',	# reserved username
	'right-wl-postcomment'		=> 'Post comments to wikilog articles',
	'right-wl-moderation'		=> 'Moderation of wikilog article comments',

	# Special:Wikilog
	'wikilog'					=> 'Wikilogs',	# Page title
	'wikilog-specialwikilog'	=> 'Wikilog',	# Special page name

	# Logs
	'wikilog-log-pagename'		=> 'Wikilog actions log',
	'wikilog-log-pagetext'		=> 'Below is a list of wikilog actions.',
	'wikilog-log-cmt-approve'	=> 'approved comment [[$1]]',
	'wikilog-log-cmt-reject'	=> 'rejected comment [[$1]]',
	'wikilog-log-cmt-rejdel'	=> 'Rejected wikilog comment from [[Special:Contributions/$1|$1]]',

	# Wikilog tab
	'wikilog-tab'				=> 'Wikilog',
	'wikilog-tab-title'			=> 'Wikilog actions',
	'wikilog-information'		=> 'Wikilog information',
	'wikilog-post-count-published'	=>
		'There are $1 published {{PLURAL:$1|article|articles}} in this wikilog,',
	'wikilog-post-count-drafts'	=>
		'plus $1 unpublished (draft) {{PLURAL:$1|article|articles}},',
	'wikilog-post-count-all'	=>
		'for a total of $1 {{PLURAL:$1|article|articles}}.',
	'wikilog-new-item'			=> 'Create new wikilog article',
	'wikilog-new-item-go'		=> 'Create',
	'wikilog-item-name'			=> 'Article name:',

	# Generic strings
	'wikilog-published'			=> 'Published',
	'wikilog-updated'			=> 'Updated',
	'wikilog-draft'				=> 'Draft',
	'wikilog-authors'			=> 'Authors',
	'wikilog-wikilog'			=> 'Wikilog',
	'wikilog-title'				=> 'Title',
	'wikilog-actions'			=> 'Actions',
	'wikilog-comments'			=> 'Comments',
	'wikilog-replies'			=> 'Replies',
	'wikilog-view-archives'		=> 'Archives',
	'wikilog-view-summary'		=> 'Summary',
	'wikilog-draft-title-mark'	=> '(draft)',
	'wikilog-anonymous-mark'	=> '(anonymous)',

	# Pager strings
	'wikilog-pager-newer-n'		=> '← newer $1',	# $1 = number of items
	'wikilog-pager-older-n'		=> 'older $1 →',	# $1 = number of items
	'wikilog-pager-newest'		=> '←← newest',
	'wikilog-pager-oldest'		=> 'oldest →→',
	'wikilog-pager-prev'		=> '← previous',
	'wikilog-pager-next'		=> 'next →',
	'wikilog-pager-first'		=> '←← first',
	'wikilog-pager-last'		=> 'last →→',
	'wikilog-pager-empty'		=>
		'<div class="wl-empty">(no items)</div>',

	# Comments page link text
	'wikilog-no-comments'		=> 'no comments',
	'wikilog-has-comments'		=> '{{PLURAL:$1|one comment|$1 comments}}',

	# Wikilog item header and footer
	# $1 = Wikilog URL, $2 = Wikilog Name, $3 = Item URL, $4 = Item Title
	# $5 = Authors, $6 = Publish date, $7 = Comments link
	'wikilog-item-brief-header'	=> ': <i><small>by $5, from [[$1|$2]], $6, $7.</small></i>',
	'wikilog-item-brief-footer'	=> '',
	'wikilog-item-more'			=> '[[$3|→ continue reading...]]',
	'wikilog-item-sub'			=> '',
	'wikilog-item-header'		=> '',
	'wikilog-item-footer'		=> ': <i>&mdash; $5 &#8226; $6 &#8226; $7</i>',

	'wikilog-author-signature'	=> '[[{{ns:User}}:$1|$1]] ([[{{ns:User_talk}}:$1|talk]])',

	# Comments
	'wikilog-comment-by-user'	=> 'Comment by <span class="wl-comment-author">$1</span> ($2)',
	'wikilog-comment-by-anon'	=> 'Comment by <span class="wl-comment-author">$3</span> (anonymous)',
	'wikilog-comment-pending'	=> 'This comment is awaiting approval.',
	'wikilog-comment-deleted'	=> 'This comment was deleted.',
	'wikilog-comment-edited'	=> 'This comment was last edited on $1 ($2).', # $1 = date and time, $2 = history link
	'wikilog-comment-autosumm'	=> 'New comment by $1: $2',
	'wikilog-reply-to-comment'	=> 'Post a reply to this comment',
	'wikilog-comment-page'		=> 'Go to this comment\'s page',
	'wikilog-comment-edit'		=> 'Edit this comment',
	'wikilog-comment-delete'	=> 'Delete this comment',
	'wikilog-comment-history'	=> 'View comment history',
	'wikilog-comment-approve'	=> 'Approve this comment (immediate action)',
	'wikilog-comment-reject'	=> 'Reject this comment (immediate action)',
	'wikilog-newtalk-text'		=> '<!-- blank page created by Wikilog -->',
	'wikilog-newtalk-summary'	=> 'created automatically by wikilog',

	# Atom and RSS feeds
	'wikilog-feed-title'		=> '{{SITENAME}} - $1 [$2]', # $1 = title, $2 = content language
	'wikilog-feed-description'	=> 'Read the most recent posts in this feed.',

	# Item and comments page titles
	'wikilog-title-item-full'	=> '$1 - $2',	#1 = article title, $2 wikilog title
	'wikilog-title-comments'	=> 'Comments - $1', #1 = article title

	# Warning and error messages
	'wikilog-error-msg'			=> 'Wikilog: $1',
	'wikilog-error-title'		=> 'Wikilog error',
	'wikilog-invalid-param'		=> 'Invalid parameter: $1.',
	'wikilog-invalid-author'	=> 'Invalid author: $1.',
	'wikilog-invalid-date'		=> 'Invalid date: $1.',
	'wikilog-invalid-tag'		=> 'Invalid tag: $1.',
	'wikilog-invalid-file'		=> 'Invalid file: $1.',
	'wikilog-file-not-found'	=> 'Non-existing file: $1.',
	'wikilog-not-an-image'		=> 'File is not an image: $1.',
	'wikilog-out-of-context'	=>
			'Warning: Wikilog tags are being used out of context. ' .
			'They should only be used in articles in the Wikilog namespace.',
	'wikilog-too-many-authors'	=>
			'Warning: Too many authors listed in this wikilog post.',
	'wikilog-too-many-tags'		=>
			'Warning: Too many tags listed in this wikilog post.',
	'wikilog-comment-is-empty'	=>
			'Posted comment is blank.',
	'wikilog-comment-too-long'	=>
			'Posted comment is too long.',
	'wikilog-comment-invalid-name' =>
			'Provided name is invalid.',
	'wikilog-no-such-article' =>
			'The requested wikilog article does not exist.',

	'wikilog-reading-draft'		=>
			'<div class="mw-warning">'.
			'<p>This wikilog article is a draft, it was not published yet.</p>'.
			'</div>',

	'wikilog-posting-anonymously' =>	# $1 = "login" link
			'You are currently not logged in; your comment will be posted '.
			'anonymously, identified by your Internet connection address. '.
			'You should either provide a pseudonym above to identify your '.
			'comment or $1 for it to be properly attributed.',
	'wikilog-anonymous-moderated' =>
			'After you submit your comment, it will not be immediately '.
			'visible on this page. The comment will only appear after it '.
			'is reviewed by a moderator.',

	# Forms
	'wikilog-post-comment'		=> 'Post a new comment',
	'wikilog-post-reply'		=> 'Post a new reply',
	'wikilog-form-legend'		=> 'Search for wikilog posts',
	'wikilog-form-wikilog'		=> 'Wikilog:',
	'wikilog-form-category'		=> 'Category:',
	'wikilog-form-name'			=> 'Name:',
	'wikilog-form-author'		=> 'Author:',
	'wikilog-form-tag'			=> 'Tag:',
	'wikilog-form-date'			=> 'Date:',
	'wikilog-form-status'		=> 'Status:',
	'wikilog-form-preview'		=> 'Preview:',
	'wikilog-form-comment'		=> 'Comment:',
	'wikilog-show-all'			=> 'All posts',
	'wikilog-show-published'	=> 'Published',
	'wikilog-show-drafts'		=> 'Drafts',
	'wikilog-submit'			=> 'Submit',
	'wikilog-preview'			=> 'Preview',	# verb
	'wikilog-edit-lc'			=> 'edit',		# verb
	'wikilog-reply-lc'			=> 'reply',		# verb
	'wikilog-delete-lc'			=> 'delete',	# verb
	'wikilog-approve-lc'		=> 'approve',	# verb
	'wikilog-reject-lc'			=> 'reject',	# verb
	'wikilog-page-lc'			=> 'page',		# noun
	'wikilog-history-lc'		=> 'history',	# noun

	# Other
	'wikilog-doc-import-comment' => "Imported Wikilog documentation",

	# Untranslatable strings
	'wikilog-summary'			=> '',			# Special page summary
	'wikilog-backlink'			=> '← $1',
	'wikilog-brackets'			=> '[$1]',
	'wikilog-navigation-bar'	=>
		'<div class="$6 visualClear">'.
		'<div style="float:left">$1 • $2</div>'.
		'<div style="float:right">$3 • $4</div>'.
		'<div style="text-align:center">$5</div>'.
		'</div>',
);

/** Portuguese (Português)
 * @author Juliano F. Ravasi
 */
$messages['pt'] = array(
	# Extension information
	'wikilog-desc'				=> 'Adiciona recursos de blog, criando um híbrido wiki-blog.',
	'wikilog-auto'				=> 'Wikilog Auto',	# reserved username
	'right-wl-postcomment'		=> 'Postar comentários em artigos wikilog',
	'right-wl-moderation'		=> 'Moderação de comentários de artigos wikilog',

	# Special:Wikilog
	'wikilog'					=> 'Wikilogs',
	'wikilog-specialwikilog'	=> 'Wikilog',

	# Logs
	'wikilog-log-pagename'		=> 'Registro de ações wikilog',
	'wikilog-log-pagetext'		=> 'Abaixo está uma lista das ações wikilog.',
	'wikilog-log-cmt-approve'	=> 'aprovou o comentário [[$1]]',
	'wikilog-log-cmt-reject'	=> 'rejeitou o comentário [[$1]]',
	'wikilog-log-cmt-rejdel'	=> 'Comentário wikilog de [[Special:Contributions/$1|$1]] rejeitado',

	# Wikilog tab
	'wikilog-tab'				=> 'Wikilog',
	'wikilog-tab-title'			=> 'Ações wikilog',
	'wikilog-information'		=> 'Informações do wikilog',
	'wikilog-post-count-published'	=>
		'Há $1 {{PLURAL:$1|artigo publicado|artigos publicados}} neste wikilog,',
	'wikilog-post-count-drafts'	=>
		'mais $1 {{PLURAL:$1|artigo não-publicado (rascunho)|artigos não-publicados (rascunhos)}},',
	'wikilog-post-count-all'	=>
		'para um total de $1 {{PLURAL:$1|artigo|artigos}}.',
	'wikilog-new-item'			=> 'Criar novo artigo wikilog',
	'wikilog-new-item-go'		=> 'Criar',
	'wikilog-item-name'			=> 'Nome do artigo:',

	# Generic strings
	'wikilog-published'			=> 'Publicado',
	'wikilog-updated'			=> 'Atualizado',
	'wikilog-draft'				=> 'Rascunho',
	'wikilog-authors'			=> 'Autores',
	'wikilog-wikilog'			=> 'Wikilog',
	'wikilog-title'				=> 'Título',
	'wikilog-actions'			=> 'Ações',
	'wikilog-comments'			=> 'Comentários',
	'wikilog-replies'			=> 'Respostas',
	'wikilog-view-archives'		=> 'Arquivos',
	'wikilog-view-summary'		=> 'Resumo',
	'wikilog-draft-title-mark'	=> '(rascunho)',
	'wikilog-anonymous-mark'	=> '(anônimo)',

	# Pager strings
	'wikilog-pager-newer-n'		=> '← $1 próximos',	# $1 = number of items
	'wikilog-pager-older-n'		=> '$1 anteriores →',	# $1 = number of items
	'wikilog-pager-newest'		=> '←← mais recentes',
	'wikilog-pager-oldest'		=> 'mais antigos →→',
	'wikilog-pager-prev'		=> '← anterior',
	'wikilog-pager-next'		=> 'próxima →',
	'wikilog-pager-first'		=> '←← primeira',
	'wikilog-pager-last'		=> 'última →→',
	'wikilog-pager-empty'		=>
		'<div class="wl-empty">(não há itens)</div>',

	# Comments page link text
	'wikilog-no-comments'		=> 'não há comentários',
	'wikilog-has-comments'		=> '{{PLURAL:$1|um comentário|$1 comentários}}',

	# Wikilog item header and footer
	'wikilog-item-brief-header'	=> ': <i><small>por $5, em [[$1|$2]], $6, $7.</small></i>',
	'wikilog-item-brief-footer'	=> '',
	'wikilog-item-more'			=> '[[$3|→ continuar lendo...]]',
	'wikilog-item-sub'			=> '',
	'wikilog-item-header'		=> '',
	'wikilog-item-footer'		=> ': <i>&mdash; $5 &#8226; $6 &#8226; $7</i>',

	'wikilog-author-signature'	=> '[[{{ns:User}}:$1|$1]] ([[{{ns:User_talk}}:$1|discussão]])',

	# Comments
	'wikilog-comment-by-user'	=> 'Comentário por <span class="wl-comment-author">$1</span> ($2)',
	'wikilog-comment-by-anon'	=> 'Comentário por <span class="wl-comment-author">$3</span> (anônimo)',
	'wikilog-comment-pending'	=> 'Este comentário está aguardando aprovação.',
	'wikilog-comment-deleted'	=> 'Este comentário foi apagado.',
	'wikilog-comment-edited'	=> 'Este comentário foi editado pela última vez em $1 ($2).', # $1 = date and time, $2 = history link
	'wikilog-comment-autosumm'	=> 'Novo comentário por $1: $2',
	'wikilog-reply-to-comment'	=> 'Postar uma resposta a esse comentário',
	'wikilog-comment-page'		=> 'Ir para a página deste comentário',
	'wikilog-comment-edit'		=> 'Editar este comentário',
	'wikilog-comment-delete'	=> 'Apagar este comentário',
	'wikilog-comment-history'	=> 'Ver histórico do comentário',
	'wikilog-comment-approve'	=> 'Aprovar este comentário (ação imediata)',
	'wikilog-comment-reject'	=> 'Rejeitar este comentário (ação imediata)',
	'wikilog-newtalk-text'		=> '<!-- página em branco criada pelo Wikilog -->',
	'wikilog-newtalk-summary'	=> 'criado automaticamente pelo wikilog',

	# Atom and RSS feeds
	'wikilog-feed-title'		=> '{{SITENAME}} - $1 [$2]', # $1 = title, $2 = content language
	'wikilog-feed-description'	=> 'Leia as postagens mais recentes neste feed.',

	# Item and comments page titles
	'wikilog-title-item-full'	=> '$1 - $2',	#1 = article title, $2 wikilog title
	'wikilog-title-comments'	=> 'Comentários - $1', #1 = article title

	# Warning and error messages
	'wikilog-error-msg'			=> 'Wikilog: $1',
	'wikilog-invalid-param'		=> 'Parâmetro inválido: $1.',
	'wikilog-invalid-author'	=> 'Autor inválido: $1.',
	'wikilog-invalid-date'		=> 'Data inválida: $1.',
	'wikilog-invalid-tag'		=> 'Rótulo inválido: $1.',
	'wikilog-invalid-file'		=> 'Arquivo inválido: $1.',
	'wikilog-file-not-found'	=> 'Arquivo não-existente: $1.',
	'wikilog-not-an-image'		=> 'Arquivo não é uma imagem: $1.',
	'wikilog-out-of-context'	=>
			'Aviso: Rótulos wikilog estão sendo utilizados fora de contexto. ' .
			'Eles devem ser usados apenas em artigos no espaço de nomes do Wikilog.',
	'wikilog-too-many-authors'	=>
			'Aviso: Autores demais listados nesta postagem wikilog.',
	'wikilog-too-many-tags'	=>
			'Aviso: Rótulos demais listados nesta postagem wikilog.',
	'wikilog-comment-is-empty'	=>
			'O comentário postado está em branco.',
	'wikilog-comment-too-long'	=>
			'O comentário postado é muito longo.',
	'wikilog-comment-invalid-name' =>
			'O nome fornecido é inválido.',
	'wikilog-no-such-article' =>
			'O artigo wikilog solicitado não existe.',

	'wikilog-reading-draft'		=>
			'<div class="mw-warning">'.
			'<p>Este artigo wikilog é um rascunho, ainda não foi publicado.</p>'.
			'</div>',

	'wikilog-posting-anonymously' =>	# $1 = "login" link
			'Você não está autenticado neste momento; seu comentário será '.
			'postado anonimamente, identificado pelo endereço de sua conexão '.
			'Internet. Você deve fornecer um pseudônimo acima para '.
			'identificar seu comentário ou $1 para que ele seja creditado '.
			'apropriadamente.',

	'wikilog-posting-anonymously' =>
			"Seu comentário será postado anonimamente. Você pode " .
			"$1 para que seu comentário seja identificado.",
	'wikilog-anonymous-moderated' =>
			'Após submeter seu comentário, este não será imediatamente '.
			'visível nesta página. O comentário somente aparecerá após ser '.
			'revisado por um moderador.',

	# Forms
	'wikilog-post-comment'		=> 'Postar um novo comentário',
	'wikilog-post-reply'		=> 'Postar uma nova resposta',
	'wikilog-form-legend'		=> 'Procurar por postagens wikilog',
	'wikilog-form-wikilog'		=> 'Wikilog:',
	'wikilog-form-category'		=> 'Categoria:',
	'wikilog-form-name'			=> 'Nome:',
	'wikilog-form-author'		=> 'Autor:',
	'wikilog-form-tag'			=> 'Rótulo:',
	'wikilog-form-date'			=> 'Data:',
	'wikilog-form-status'		=> 'Estado:',
	'wikilog-form-preview'		=> 'Previsão:',
	'wikilog-form-comment'		=> 'Comentário:',
	'wikilog-show-all'			=> 'Todas as postagens',
	'wikilog-show-published'	=> 'Publicados',
	'wikilog-show-drafts'		=> 'Rascunhos',
	'wikilog-submit'			=> 'Submeter',
	'wikilog-preview'			=> 'Previsão',		# verb
	'wikilog-edit-lc'			=> 'editar',		# verb
	'wikilog-reply-lc'			=> 'responder',		# verb
	'wikilog-delete-lc'			=> 'apagar',		# verb
	'wikilog-approve-lc'		=> 'aprovar',		# verb
	'wikilog-reject-lc'			=> 'rejeitar',		# verb
	'wikilog-page-lc'			=> 'página',		# noun
	'wikilog-history-lc'		=> 'histórico',		# noun

	# Other
	'wikilog-doc-import-comment' => "Documentação Wikilog importada",
);

/** German (Deutsch)
 * @author Erkan Yilmaz
 */
$messages['de'] = array(
	# Extension information
	'wikilog-desc'				=> 'Fügt Blog-Funktionen hinzu, um einen Wiki-Blog Hybrid zu erzeugen.',
	'wikilog-auto'				=> 'Wikilog Auto',	# reserved username
	'right-wl-postcomment'		=> 'Poste Kommentare zu Wikilog-Beiträgen',
	'right-wl-moderation'		=> 'Moderation von Kommentaren zu wikilog-Beiträgen',

	# Special:Wikilog
	'wikilog'					=> 'Wikilogs',	# Page title
	'wikilog-specialwikilog'	=> 'Wikilog',	# Special page name

	# Logs
	##TRANSLATE##	'wikilog-log-pagename'		=> 'Wikilog actions log',
	##TRANSLATE##	'wikilog-log-pagetext'		=> 'Below is a list of wikilog actions.',
	##TRANSLATE##	'wikilog-log-cmt-approve'	=> 'approved comment [[$1]]',
	##TRANSLATE##	'wikilog-log-cmt-reject'	=> 'rejected comment [[$1]]',
	##TRANSLATE##	'wikilog-log-cmt-rejdel'	=> 'Rejected wikilog comment from [[Special:Contributions/$1|$1]]',

	# Wikilog tab
	'wikilog-tab'				=> 'Wikilog',
	'wikilog-tab-title'			=> 'Wikilog Aktionen',
	'wikilog-information'		=> 'Wikilog Information',
	'wikilog-post-count-published'	=>
		'$1 {{PLURAL:$1|Beitrag|Beiträge}} wurden in diesem Wikilog veröffentlicht,',
	'wikilog-post-count-drafts'	=>
		'plus $1 {{PLURAL:$1|Beitrag|Beiträge}}, die unveröffentlicht (Entwurf) sind,',
	'wikilog-post-count-all'	=>
		'insgesamt gibt es $1 {{PLURAL:$1|Beitrag|Beiträge}}.',
	'wikilog-new-item'			=> 'Erstelle einen neuen Wikilog-Beitrag',
	'wikilog-new-item-go'		=> 'Erstellen',
	'wikilog-item-name'			=> 'Beitrag-Name:',

	# Generic strings
	'wikilog-published'			=> 'Veröffentlicht',
	'wikilog-updated'			=> 'Aktualisiert',
	'wikilog-draft'				=> 'Entwurf',
	'wikilog-authors'			=> 'Autoren',
	'wikilog-wikilog'			=> 'Wikilog',
	'wikilog-title'				=> 'Titel',
	'wikilog-actions'			=> 'Aktionen',
	'wikilog-comments'			=> 'Kommentare',
	##TRANSLATE##	'wikilog-replies'			=> 'Replies',

	'wikilog-view-archives'		=> 'Archive',
	'wikilog-view-summary'		=> 'Zusammenfassung',
	'wikilog-draft-title-mark'	=> '(Entwurf)',
	'wikilog-anonymous-mark'	=> '(anonym)',

	# Pager strings
	'wikilog-pager-newer-n'		=> '← neuere $1',	# $1 = number of items
	'wikilog-pager-older-n'		=> 'ältere $1 →',	# $1 = number of items
	'wikilog-pager-newest'		=> '←← neuester',
	'wikilog-pager-oldest'		=> 'ältester →→',
	'wikilog-pager-prev'		=> '← vorheriger',
	'wikilog-pager-next'		=> 'nächster →',
	'wikilog-pager-first'		=> '←← erster',
	'wikilog-pager-last'		=> 'letzter →→',
	'wikilog-pager-empty'		=>
		'<div class="wl-empty">(keine Beiträge)</div>',

	# Comments page link text
	'wikilog-no-comments'		=> 'keine Kommentare',
	'wikilog-has-comments'		=> '{{PLURAL:$1|ein Kommentar|$1 Kommentare}}',

	# Wikilog item header and footer
	'wikilog-item-brief-header'	=> ': <i><small>von $5, aus [[$1|$2]], $6, $7.</small></i>',
	'wikilog-item-brief-footer'	=> '',
	'wikilog-item-more'			=> '[[$3|→ weiterlesen...]]',
	'wikilog-item-sub'			=> '',
	'wikilog-item-header'		=> '',
	'wikilog-item-footer'		=> ': <i>&mdash; $5 &#8226; $6 &#8226; $7</i>',

	'wikilog-author-signature'	=> '[[{{ns:User}}:$1|$1]] ([[{{ns:User_talk}}:$1|Diskussion]])',

	# Comments
	'wikilog-comment-by-user'	=> 'Kommentar von <span class="wl-comment-author">$1</span> ($2)',
	'wikilog-comment-by-anon'	=> 'Kommentar von <span class="wl-comment-author">$3</span> (anonym)',
	'wikilog-comment-pending'	=> 'Dieser Kommentar muss noch zugelassen werden.',
	'wikilog-comment-deleted'	=> 'Dieser Kommentar wurde gelöscht.',
	##TRANSLATE##	'wikilog-comment-edited'	=> 'This comment was last edited on $1 ($2).', # $1 = date and time, $2 = history link
	'wikilog-comment-autosumm'	=> 'Neuer Kommentar von $1: $2',
	'wikilog-reply-to-comment'	=> 'Poste eine Antwort auf diesen Kommentar',
	##TRANSLATE##	'wikilog-comment-page'		=> 'Go to this comment\'s page',
	##TRANSLATE##	'wikilog-comment-edit'		=> 'Edit this comment',
	##TRANSLATE##	'wikilog-comment-delete'	=> 'Delete this comment',
	##TRANSLATE##	'wikilog-comment-history'	=> 'View comment history',
	##TRANSLATE##	'wikilog-comment-approve'	=> 'Approve this comment (immediate action)',
	##TRANSLATE##	'wikilog-comment-reject'	=> 'Reject this comment (immediate action)',
	'wikilog-newtalk-text'		=> '<!-- leere Seite erzeugt durch Wikilog -->',
	'wikilog-newtalk-summary'	=> 'automatisch erzeugt durch Wikilog',

	# Atom and RSS feeds
	'wikilog-feed-title'		=> '{{SITENAME}} - $1 [$2]', # $1 = title, $2 = content language
	'wikilog-feed-description'	=> 'Lese die neuesten Beiträge in diesem Feed.',

	# Item and comments page titles
	'wikilog-title-item-full'	=> '$1 - $2',	#1 = article title, $2 wikilog title
	##TRANSLATE##	'wikilog-title-comments'	=> 'Comments - $1', #1 = article title

	# Warning and error messages
	'wikilog-error-msg'			=> 'Wikilog: $1',
	'wikilog-error-title'		=> 'Wikilog Fehler',
	'wikilog-invalid-param'		=> 'Ungültiger Parameter: $1.',
	'wikilog-invalid-author'	=> 'Ungültiger Autor: $1.',
	'wikilog-invalid-date'		=> 'Ungültiges Datum: $1.',
	'wikilog-invalid-tag'		=> 'Ungültiges Tag: $1.',
	'wikilog-invalid-file'		=> 'Ungültige Datei: $1.',
	'wikilog-file-not-found'	=> 'Nicht vorhandene Datei: $1.',
	'wikilog-not-an-image'		=> 'Datei ist kein Bild: $1.',
	'wikilog-out-of-context'	=>
			'Warnung: Wikilog Tags werden nicht im Zusammenhang (out of context) benutzt. ' .
			'Sie sollten nur in Beiträgen im Wikilog Namensraum benutzt werden.',
	'wikilog-too-many-authors'	=>
			'Warnung: Zu viele Autoren werden in diesem Wikilog-Beitrag aufgeführt.',
	'wikilog-too-many-tags'		=>
			'Warnung: Zu viele Tags werden in diesem Wikilog-Beitrag erfasst.',
	'wikilog-comment-is-empty'	=>
			'Der gesendete Kommentar ist leer.',
	'wikilog-comment-too-long'	=>
			'Der gesendete Kommentar ist zu lang.',
	'wikilog-comment-invalid-name' =>
			'Der angegebene Name ist ungültig.',
	##TRANSLATE##	'wikilog-no-such-article' =>
	##TRANSLATE##			'The requested wikilog article does not exist.',

	'wikilog-reading-draft'		=>
			'<div class="mw-warning">'.
			'<p>Dieser Wikilog-Beitrag ist ein Entwurf, er wurde noch nicht veröffentlicht.</p>'.
			'</div>',

	'wikilog-posting-anonymously' =>	# $1 = "Login" link
			'Sie sind zur Zeit nicht eingeloggt; ihr Kommentar wird anonym '.
			'versendet, identifiziert durch ihre Internetverbindungsadresse. '.
			'Sie sollten oben entweder ein Pseudonym angeben, um ihren '.
			'Kommentar zu identifizieren oder $1, damit er passend zugeordnet wird.',
	##TRANSLATE##	'wikilog-anonymous-moderated' =>
	##TRANSLATE##			'After you submit your comment, it will not be immediately '.
	##TRANSLATE##			'visible on this page. The comment will only appear after it '.
	##TRANSLATE##			'it is reviewed by a moderator.',

	# Forms
	'wikilog-post-comment'		=> 'Poste einen neuen Kommentar',
	'wikilog-post-reply'		=> 'Poste eine neue Antwort',
	'wikilog-form-legend'		=> 'Suche nach Wikilog-Beiträgen',
	'wikilog-form-wikilog'		=> 'Wikilog:',
	'wikilog-form-category'		=> 'Kategorie:',
	'wikilog-form-name'			=> 'Name:',
	'wikilog-form-author'		=> 'Autor:',
	'wikilog-form-tag'			=> 'Tag:',
	'wikilog-form-date'			=> 'Datum:',
	'wikilog-form-status'		=> 'Status:',
	'wikilog-form-preview'		=> 'Vorschau:',
	'wikilog-form-comment'		=> 'Kommentar:',
	'wikilog-show-all'			=> 'Alle Beiträge',
	'wikilog-show-published'	=> 'Veröffentlicht',
	'wikilog-show-drafts'		=> 'Entwürfe',
	'wikilog-submit'			=> 'Abschicken',
	'wikilog-preview'			=> 'Vorher betrachten',	# verb
	'wikilog-edit-lc'			=> 'bearbeiten',		# verb
	'wikilog-reply-lc'			=> 'antworten',		# verb
	##TRANSLATE##	'wikilog-delete-lc'			=> 'delete',	# verb
	##TRANSLATE##	'wikilog-approve-lc'		=> 'approve',	# verb
	##TRANSLATE##	'wikilog-reject-lc'			=> 'reject',	# verb
	##TRANSLATE##	'wikilog-page-lc'			=> 'page',		# noun
	##TRANSLATE##	'wikilog-history-lc'		=> 'history',

	# Other
	##TRANSLATE##	'wikilog-doc-import-comment' => "Imported Wikilog documentation",

);
