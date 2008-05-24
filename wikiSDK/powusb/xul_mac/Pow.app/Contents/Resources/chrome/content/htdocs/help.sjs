<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?sjs var head = get_pow_header('Pow - Plain Old Webserver'); document.write(head); ?> 


<link rev="made" href="mailto:root@b28.apple.com" />


<div class="pod"><h1>POW - Plain Old Webserver</h1>

<p><a name="__index__"></a></p>
<!-- INDEX BEGIN -->

<ul>

	<li><a href="#name">NAME</a></li>
	<li><a href="#synopsis">SYNOPSIS</a></li>
	<li><a href="#description">DESCRIPTION</a></li>
	<ul>

		<li><a href="#how_do_i_manage_pow">How do I manage POW?</a></li>
		<li><a href="#where_are_my_files">Where are my files?</a></li>
		<li><a href="#why_can_t_i_access_my_web_server_from_the_outside">Why can't I access my web server from the outside?</a></li>
		<li><a href="#can_i_create_dynamic_content">Can I create dynamic content?</a></li>
		<li><a href="#how_do_i_read_a_file">How do I read a file?</a></li>
		<li><a href="#how_do_i_write_a_file">How do I write a file?</a></li>
		<li><a href="#how_do_i_rewrite_a_header">How do I rewrite a header?</a></li>
		<li><a href="#how_do_i_get_request_headers">How do I get request headers?</a></li>
		<li><a href="#how_do_i_delete_a_file">How do I delete a file?</a></li>
		<li><a href="#how_do_i_exit_the_code">How do I exit the code?</a></li>
		<li><a href="#how_do_i_run_ajax">How do I run AJAX?</a></li>
		<li><a href="#how_do_i_add_a_mimetype">How do I add a mime-type?</a></li>
		<li><a href="#what_is_my_ip_address">What is my IP address?</a></li>
		<li><a href="#how_do_i_include_a_file">How do I include a file?</a></li>
		<li><a href="#how_do_i_execute_sql">How do I execute SQL?</a></li>
		<li><a href="#how_do_i_drop_a_database">How do I drop a database?</a></li>
		<li><a href="#pow_file__readfile__">pow_file( readfile )</a></li>
		<li><a href="#pow_file_put_contents__filename__contents__rwa_flags__">pow_file_put_contents( filename, contents, rwa_flags )</a></li>
		<li><a href="#file_delete__filename__">file_delete( filename )</a></li>
		<li><a href="#pow_header__line__">pow_header( line )</a></li>
		<li><a href="#pow_get_request_header__label__">pow_get_request_header( label )</a></li>
		<li><a href="#pow_exit___">pow_exit( )</a></li>
		<li><a href="#pow_run_ajax___">pow_run_ajax( )</a></li>
		<li><a href="#pow_include__filename__">pow_include( filename )</a></li>
		<li><a href="#server_variables">Server variables</a></li>
	</ul>

</ul>
<!-- INDEX END -->

<hr />
<p>
</p>
<h1><a name="name">NAME</a></h1>
<pre>
 POW - Plain Old Webserver</pre>
<p>
</p>
<hr />
<h1><a name="synopsis">SYNOPSIS</a></h1>
<pre>
 Use POW to distribute files. POW is started by clicking on the blue square in 
 the bottom right-hand corner of your browser. When the play button appears, 
 the server is on. Also, you can manage POW access privileges and other 
 advanced features in the menu, &quot;Tools &gt; POW &gt; Manage POW...&quot;</pre>
<p>
</p>
<hr />
<h1><a name="description">DESCRIPTION</a></h1>
<p>
</p>
<h2><a name="how_do_i_manage_pow">How do I manage POW?</a></h2>
<p>You can reach the management window through 'Tools &gt; POW &gt; Manage POW ...'</p>
<dl>
<dt><strong><a name="item_password_protect_site">Password Protect Site</a></strong><br />
</dt>
<dd>
Check this to prevent access except through the listed username and password.
</dd>
<p></p>
<dt><strong><a name="item_login_id__26_password">Login ID &amp; Password</a></strong><br />
</dt>
<dd>
These will be required by anyone accessing the site if 'Password Protect Site'
is checked.
</dd>
<p></p>
<dt><strong><a name="item_port">Port</a></strong><br />
</dt>
<dd>
The standard port is 6670, as in localhost:6670. Do not use ports under 
1025, unless you know what you are doing.
</dd>
<p></p>
<dt><strong><a name="item_add_mime_type">Add Mime Type</a></strong><br />
</dt>
<dd>
Add extra mime-types, like 'audio/mpeg' for the mp3 extension. Remember
to check the binary checkbox, if it is a non-text format. Use the 'Add'
button, not enter to add a mime-type.
</dd>
<p></p></dl>
<p>
</p>
<h2><a name="where_are_my_files">Where are my files?</a></h2>
<p>Make a shortcut to this folder for easy access.</p>
<pre>
        On a Mac:
        Users/YOUR NAME/Library/Application Support/Firefox/Profiles/RANDOM 
        TEXT/default/pow/htdocs
        &gt; ln -s /Users/YOUR NAME/Library/Application\ Support/Firefox/Profiles/
        RANDOM.default/pow/htdocs ./pow_docs</pre>
<pre>
        On a PC:
        C:\Documents and Settings\YOUR NAME\Application Data\Mozilla\Firefox\
        Profiles\RANDOM TEXT\pow\htdocs</pre>
<pre>
        On a Linux:
        /home/USER NAME/.mozilla/firefox/default.NUMBER/pow/htdocs</pre>
<pre>
        You can check the exact path at '<a href="http://localhost:6670/get_path.sjs">http://localhost:6670/get_path.sjs</a>'</pre>
<p>
</p>
<h2><a name="why_can_t_i_access_my_web_server_from_the_outside">Why can't I access my web server from the outside?</a></h2>
<p>You might have a firewall blocking your server, so you can only access it from the server itself. Punch a hole in the firewall if your sys-admin allows. Try a different port in 'Tools &gt; Pow &gt; Manage Pow'. You might also find yourself behind NAT, in which case you are out of luck.</p>
<p>
</p>
<h2><a name="can_i_create_dynamic_content">Can I create dynamic content?</a></h2>
<p>Yes. SJS is a Javascript-based server-side scripting language. Code enclosed in ``&lt;?sjs ?&gt;'' is executed by Firefox on the server side. Try this, and save it to <code>htdocs/test.sjs</code>:</p>
<pre>
        &lt;?sjs
                document.writeln(&quot;Hello world!&quot;);
        ?&gt;</pre>
<p>
</p>
<h2><a name="how_do_i_read_a_file">How do I read a file?</a></h2>
<pre>

        &lt;?sjs
                var contents = pow_file(&quot;/pow/htdocs/pow.css&quot;);
                document.writeln(contents);
        ?&gt;
        OR
        &lt;?sjs
                var contents = pow_file(&quot;pow.css&quot;);
                document.writeln(contents);
        ?&gt;
        OR
        &lt;?sjs
                var contents = pow_file('<a href="http://www.yahoo.com/">http://www.yahoo.com/</a>');
                document.writeln(contents);
        ?&gt;</pre>
<p>
</p>
<h2><a name="how_do_i_write_a_file">How do I write a file?</a></h2>
<pre>
        &lt;?sjs
                var text = &quot;This is a test.\n&quot;;
                // TO WRITE TO A NEW FILE
                pow_put_file_contents(&quot;file.txt&quot;, text, &quot;w&quot; );
                var text = &quot;Here is another line.&quot;;
                // TO APPEND
                pow_put_file_contents(&quot;file.txt&quot;, text, &quot;wa&quot; );
        ?&gt;</pre>
<p>
</p>
<h2><a name="how_do_i_rewrite_a_header">How do I rewrite a header?</a></h2>
<pre>
        &lt;?sjs
                pow_header(&quot;Content-Type: text/plain&quot;);
                document.write(&quot;OK&lt;br&gt;OK&quot;);
        ?&gt;
        OR to add a header
        &lt;?sjs
                pow_header(&quot;X-Header: 123fd1294&quot;);
                pow_header(&quot;Set-Cookie: session=d1294&quot;);
        ?&gt;</pre>
<p>
</p>
<h2><a name="how_do_i_get_request_headers">How do I get request headers?</a></h2>
<pre>

        document.writeln(&quot;Headers are: &quot;+pow_server.REQUEST);
        OR
        document.writeln(&quot;Cookie is &quot;+get_request_header('Cookie'));</pre>
<p>
</p>
<h2><a name="how_do_i_delete_a_file">How do I delete a file?</a></h2>
<pre>

        pow_file_delete(&quot;file.txt&quot;);</pre>
<p>
</p>
<h2><a name="how_do_i_exit_the_code">How do I exit the code?</a></h2>
<pre>
        pow_exit();</pre>
<p>
</p>
<h2><a name="how_do_i_run_ajax">How do I run AJAX?</a></h2>
<p>Note: The AJAX API is not yet frozen.</p>
<pre>
        &lt;?sjs
                pow_server.put_text = function() {
                        var date = new Date();
                        var time  = date.getSeconds();
                        var changetext = new Array('changeme', time);
                        return changetext;
                }
                pow_run_ajax();
        ?&gt;
        &lt;form onsubmit=&quot;pow_ajax_load('<a href="http://localhost:6670/test.sjs?AJAX=true">http://localhost:6670/test.sjs?AJAX=true</a>'); 
                return false;&quot; action=&quot;#&quot; method='GET' &gt;
                &lt;input type='submit' value='Go' /&gt;
        &lt;/form&gt;
        &lt;div id=&quot;changeme&quot;&gt;Text to change&lt;/div&gt;
        &lt;/body&gt;
        &lt;/html&gt;</pre>
<p>
</p>
<h2><a name="how_do_i_add_a_mimetype">How do I add a mime-type?</a></h2>
<pre>
        Additional mime-types can be added in the POW Options window at &quot;Tools &gt; 
        POW &gt; Manage POW...&quot; Use the binary checkbox for binary data, such as 
        music and images.</pre>
<p>
</p>
<h2><a name="what_is_my_ip_address">What is my IP address?</a></h2>
<pre>
        See <a href="http://www.ipchicken.com/">http://www.ipchicken.com/</a></pre>
<p>
</p>
<h2><a name="how_do_i_include_a_file">How do I include a file?</a></h2>
<pre>
        &lt;?sjs
                pow_include(&quot;lib.sjs&quot;);
        ?&gt;</pre>
<p>
</p>
<h2><a name="how_do_i_execute_sql">How do I execute SQL?</a></h2>
<p>The SJS version of Mozilla Storage cleans up after itself, casts all numbers to strings in returns from the DB, and allows access through mysql-like pretty printing or a string-only array. SQL is compatible with SQLite. If you need binary data support, use the Storage API directly.</p>
<pre>
        &lt;?sjs
        try {
                var pdb = new pow_DB(&quot;object_db&quot;);
         pdb.exec(&quot;CREATE TABLE foo ('object_id' int, 'name' varchar(10), 
                'name2' varchar(12))&quot;);
        } catch  (e) {
         // noop
        }
        pdb.exec(&quot;INSERT INTO foo (object_id, name, name2) VALUES (1, 'gus', 'bob')&quot;);
        var results = pdb.exec(&quot;SELECT * FROM foo WHERE 1=1&quot;);
        var res = pdb.pretty_print(results);
        document.write(res);
        document.writeln(results[&quot;column_names&quot;].join(&quot; &quot;)+&quot;&lt;br&gt;&quot;);
        for (var i=0;i&lt;results.length;i++) {
         document.writeln(results[i].join(&quot; &quot;)+&quot;&lt;br&gt;&quot;);
        }</pre>
<pre>
        ?&gt;</pre>
<p>
</p>
<h2><a name="how_do_i_drop_a_database__sjs_var_pdb___new_pow_db_object_db___pdb_drop_database_object_db____">How do I drop a database?
	&lt;?sjs
		var pdb = new pow_DB(``object_db'');
		pdb.drop_database(``object_db'');
	?&gt;</a></h2>
<p>
</p>
<h2><a name="pow_file__readfile__">pow_file( readfile )</a></h2>
<dl>
<dt><strong><a name="item_readfile">readfile</a></strong><br />
</dt>
<dd>
<pre>
        File to read. Root (/) is extensions directory.</pre>
</dd>
<dt><strong><a name="item_returns">returns</a></strong><br />
</dt>
<dd>
<pre>
        File contents or &quot;ERROR&quot; if not found.</pre>
</dd>
</dl>
<p>
</p>
<h2><a name="pow_file_put_contents__filename__contents__rwa_flags__">pow_file_put_contents( filename, contents, rwa_flags )</a></h2>
<dl>
<dt><strong><a name="item_filename">filename</a></strong><br />
</dt>
<dd>
<pre>
        File to write.</pre>
</dd>
<dt><strong><a name="item_contents">contents</a></strong><br />
</dt>
<dd>
<pre>
        Body to write to file.</pre>
</dd>
<dt><strong>returns</strong><br />
</dt>
<dd>
<pre>
        nothing</pre>
</dd>
<dt><strong><a name="item_rwa_flags">rwa_flags</a></strong><br />
</dt>
<dd>
<pre>

rwa_flags can be the following:</pre>
</dd>
<dt><strong><a name="item_w_write_new_ascii_file">w Write new ASCII file</a></strong><br />
</dt>
<dt><strong><a name="item_wb_write_new_binary_file">wb Write new binary file</a></strong><br />
</dt>
<dt><strong><a name="item_wba_write_binary_2c_append">wba Write binary, append</a></strong><br />
</dt>
</dl>
<p>
</p>
<h2><a name="file_delete__filename__">file_delete( filename )</a></h2>
<dl>
<dt><strong>readfile</strong><br />
</dt>
<dd>
<pre>
        File to delete.</pre>
</dd>
<dt><strong>returns</strong><br />
</dt>
<dd>
<pre>
        nothing</pre>
</dd>
</dl>
<p>
</p>
<h2><a name="pow_header__line__">pow_header( line )</a></h2>
<p>Adds a response header.</p>
<dl>
<dt><strong><a name="item_line">line</a></strong><br />
</dt>
<dd>
<pre>
        Header line to add. Location and Content-Type only replace 
        their default headers.</pre>
</dd>
<dt><strong>returns</strong><br />
</dt>
<dd>
<pre>
        nothing</pre>
</dd>
</dl>
<p>
</p>
<h2><a name="pow_get_request_header__label__">pow_get_request_header( label )</a></h2>
<dl>
<dt><strong><a name="item_label">label</a></strong><br />
</dt>
<dd>
<pre>
        Request header key. E.g., &quot;Accept: */*&quot;</pre>
</dd>
<dt><strong>returns</strong><br />
</dt>
<dd>
<pre>
        Header value.</pre>
</dd>
</dl>
<p>
</p>
<h2><a name="pow_exit___">pow_exit( )</a></h2>
<p>Stops printing to output stream. Discontinues script.</p>
<dl>
<dt><strong>returns</strong><br />
</dt>
<dd>
<pre>
        nothing</pre>
</dd>
</dl>
<p>
</p>
<h2><a name="pow_run_ajax___">pow_run_ajax( )</a></h2>
<pre>
        Prints needed functions to run AJAX updates to a webpage.
        See example.</pre>
<dl>
<dt><strong>returns</strong><br />
</dt>
<dd>
<pre>
        nothing</pre>
</dd>
</dl>
<p>
</p>
<h2><a name="pow_include__filename__">pow_include( filename )</a></h2>
<dl>
<dt><strong>filename</strong><br />
</dt>
<dd>
<pre>
        SJS file to include inline. Do not include &lt;?sjs and ?&gt; delimiters
        in this file.</pre>
</dd>
<dt><strong>returns</strong><br />
</dt>
<dd>
<pre>
        nothing</pre>
</dd>
</dl>
<p>
</p>
<h2><a name="server_variables">Server variables</a></h2>
<dl>
<dt><strong><a name="item_pow_server_2erequest_raw_request_headers_and_post_">pow_server.REQUEST Raw request headers and POST content</a></strong><br />
</dt>
<dt><strong><a name="item_pow_server_2eget_query_string_key_2dvalue_pairs">pow_server.GET Query string key-value pairs</a></strong><br />
</dt>
<dt><strong><a name="item_pow_server_2eraw_post_complete_post_body_if_post_r">pow_server.RAW_POST Complete POST body if POST request is made</a></strong><br />
</dt>
<dt><strong><a name="item_pow_server_2epost_filename_filename_of_uploaded_fi">pow_server.POST_FILENAME Filename of uploaded file</a></strong><br />
</dt>
<dt><strong><a name="item_pow_server_2eremote_host_ip_address_of_requesting_">pow_server.REMOTE_HOST IP address of requesting client</a></strong><br />
</dt>
</dl>

</div>
</body>

</html>
