<?PHP

###Der folgende Code wurde von Marco Schuster bereit gestellt und ist unter der GPL veröffentlicht. Quelle: http://code.harddisk.is-a-geek.org/filedetails.php?repname=hd_bot&path=%2Fhttp.inc.php&sc=1&rev=66

// the HTTP wrapper class. replace this file with another for e.g. TOR/Proxy/CURL support
// use like $http=new http_w("de.wikipedia.org","/w/index.php"); $http->get("");
// provides functions post, get
// result is stored in data, header. for a full HTTP response do $full=$http->header."\r\n\r\n".$http->data;

//let's fake some browser agent to bypass MediaWiki squid block (evil!)
define("USERAGENT","Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1");  //Mozilla Firefox
define("COOKIEJAR","private/cookiejar.txt");
define("USE_COOKIEJAR",false);
class http_w {
    public $data;
    public $headers;
    
    var $server;
    var $file;
    var $raw;
    
    
    function __construct($server, $file) {
        global $cookies;
        //debug(__FUNCTION__,"new instance of http_w initialized",1);
        $this->data="";
        $this->headers="";
        $this->raw="";
        $this->server=$server;
        $this->file=$file;
        if(USE_COOKIEJAR) {
            $fp=fopen(COOKIEJAR,"r");
            while (!feof($fp)) {
                $buf.= fgets($fp,128);
            }
            fclose($fp);
            $cookies=$buf;
        }
    }
    
    function get($param="", $ignore_redir=false) {
        //debug(__FUNCTION__,"get called. param='$param', ignore_redir='$ignore_redir', server='".$this->server."', file='".$this->file."'",1);
        
        if($param!="") { $p="?".$param; } else { $p=""; }
        
        $cookies=$this->cookiestring();
        
        $fp = fsockopen ($this->server, 80, $errno, $errstr, 10);
        
        if ($fp) {
            $query="GET ".$this->file.$p." HTTP/1.1
Host: ".$this->server."
Cookie: $cookies
User-Agent: ".USERAGENT."
\r\n\r\n";
            //debug(__FUNCTION__,$query,3);
            fputs ($fp,$query);
            $buf = "";
            while (!feof($fp)) {
                $buf.= fgets($fp,128);
            }
            fclose($fp);
            $this->raw=$buf;
            
            $this->headers=$this->getheaders();
            $this->data=$this->removeheaders();
            //debug(__FUNCTION__,$this->headers,3);
            //debug(__FUNCTION__,$this->data,3);
            
            $this->update_cookies();            
            preg_match('@Location: http://(.*)/(.*)\r\n@iU',$this->headers,$hit);
            if($hit[1]!="" && (!$ignore_redir)) {
                $this->server=$hit[1];
                $this->file="/".$hit[2];
                //debug(__FUNCTION__,"following http redirect to ".$this->server." // ".$this->file,1);
                $this->data=$this->get("");
            }
            flush();
            return $this->data;
        } else {
            echo "$errno: $errstr";
        }
    }
    
    function post($param="", $content="", $ignore_redir=false) {
        //debug(__FUNCTION__,"post called. param='$param', ignore_redir='$ignore_redir', server='".$this->server."', file='".$this->file."', post content='".$content["message"]."'",1);
        
        if($content=="") { die("thou shalt not use post without content!"); }
        
        if($param!="") { $p="?".$param; } else { $p=""; }
        
        $cookies=$this->cookiestring();
        
        $fp = fsockopen ($this->server, 80, $errno, $errstr, 10);
        
        if ($fp) {
            $query="POST ".$this->file.$p." HTTP/1.1
Host: ".$this->server."
Cookie: $cookies
User-Agent: ".USERAGENT."
Content-Type: ".$content["type"]."
Content-Length: ".strlen($content["message"])."

".$content["message"]."
\r\n\r\n";
            //debug(__FUNCTION__,$query,3);
            fputs ($fp,$query);
            while (!feof($fp)) {
                $buf.= fgets($fp,128);
            }
            fclose($fp);
            $this->raw=$buf;
            
            $this->headers=$this->getheaders();
            $this->data=$this->removeheaders();
            //debug(__FUNCTION__,$this->headers,3);
            //debug(__FUNCTION__,$this->data,3);
            $this->update_cookies();           
            preg_match('@Location: http://(.*)/(.*)\r\n@iU',$this->headers,$hit);
            if($hit[1]!="" && (!$ignore_redir)) {
                $this->server=$hit[1];
                $this->file="/".$hit[2];
                //debug(__FUNCTION__,"following http redirect to ".$this->server." // ".$this->file,1);
                $this->data=$this->post("",$content);
            }
            flush();
            return $this->data;
        } else {
            echo "$errno: $errstr";
        }
    }
    
    function removeheaders() {
        //debug(__FUNCTION__,__FUNCTION__." called",1);
        preg_match ("/\r\n\r\n(.*)$/is",$this->raw,$hit);
        return $hit[1];
    }
    function getheaders() {
        //debug(__FUNCTION__,__FUNCTION__." called",1);
        preg_match ("/^(.*)\r\n\r\n/is",$this->raw,$hit);
        return $hit[1];
    }
    
    function cookiestring() {
        global $cookies;
        //debug(__FUNCTION__,__FUNCTION__." called",1);
        return $cookies;
    }
    function update_cookies() {
        global $cookies;
        //debug(__FUNCTION__,__FUNCTION__." called",1);
        $hits=preg_match_all('@Set-Cookie: (.*)=(.*); (expires|path)(.*)@isU',$this->headers,$hit,PREG_SET_ORDER);
        //debug(__FUNCTION__,"cookie preg hits $hits",3);
        foreach($hit as $k=>$v) {
            $cookies.=$v[1]."=".$v[2].";";
        }
        //debug(__FUNCTION__,"cookies updated to $cookies",3);
        if(USE_COOKIEJAR) {
            $fp=fopen(COOKIEJAR,"w");
            fputs($fp,$cookies);
            fclose($fp);
        }
    }
}
