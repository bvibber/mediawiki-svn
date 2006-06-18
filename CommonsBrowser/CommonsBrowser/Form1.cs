using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Text;
using System.IO;
using System.Net;
using System.Windows.Forms;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Drawing.Imaging;
using System.Drawing.Text;
using System.Security.Cryptography;
using System.Threading;
using System.Globalization;

namespace CommonsBrowser
{

    public partial class Form1 : Form
    {
        List<MyImageType> images = new List<MyImageType>();
        String last_cat_read;

        public Form1()
        {
            InitializeComponent();
            thumbnails.Paint += new PaintEventHandler(my_paint);
            categoryTree.Nodes.Add("CommonsRoot");
        }

        private String md5 ( String text )
        {
            System.Security.Cryptography.MD5CryptoServiceProvider x = new System.Security.Cryptography.MD5CryptoServiceProvider();
            byte[] bs = System.Text.Encoding.UTF8.GetBytes(text);
            bs = x.ComputeHash(bs);
            System.Text.StringBuilder s = new System.Text.StringBuilder();
            foreach (byte b in bs)
            {
               s.Append(b.ToString("x2").ToLower());
            }
            return s.ToString();
        }

        private TreeNode[] GetCategories ( String category )
        {
            Cursor.Current = Cursors.WaitCursor;
            String filename = GetCatagoryCacheFile(category);
            String url = "http://tools.wikimedia.de/~magnus/cat-as-trophe.php?category=" + category;
            String text ;
            bool do_cache;
            if (File.Exists(filename))
            {
                text = ReadLocalFile(filename);
                do_cache = false;
            }
            else
            {
                text = ReadWebPage(url);
                do_cache = true;
            }
            text = text.Replace("_", " ") ;
            String[] lines = text.Split(new Char[] { '\n' });

            int count = 0;
            foreach (String l in lines)
            {
                if (l.Trim() == "") continue;
                count++;
            }

            TreeNode [] node = new TreeNode [count] ;
            int a=0;
            foreach (String l in lines)
            {
                if (l.Trim() == "") continue;
                node[a] = new TreeNode(l);
                a++;
            }
            if ( do_cache ) CacheCategories(category, node);
            Cursor.Current = Cursors.Arrow;
            return node;
        }

        private String ReadLocalFile(String filename)
        {
            String ret = "";
            StreamReader sr = File.OpenText(filename);
            String input;
            while ((input = sr.ReadLine()) != null)
            {
                ret += input + "\n" ;
            }
            sr.Close();
            return ret.Trim();
        }

        private String ReadWebPage(String url)
        {
            // used to build entire input
            StringBuilder sb = new StringBuilder();

            // used on each read operation
            byte[] buf = new byte[8192];

            // prepare the web page we will be asking for
            HttpWebRequest request = (HttpWebRequest)
                WebRequest.Create(url);

            // execute the request
            HttpWebResponse response = (HttpWebResponse)
                request.GetResponse();

            // we will read data via the response stream
            Stream resStream = response.GetResponseStream();

            string tempString = null;
            int count = 0;

            do
            {
                // fill the buffer with data
                count = resStream.Read(buf, 0, buf.Length);

                // make sure we read some data
                if (count != 0)
                {
                    // translate from bytes to UTF8 text
                    tempString = Encoding.UTF8.GetString(buf, 0, count);

                    // continue building the string
                    sb.Append(tempString);
                }
            }
            while (count > 0); // any more data to read?
            return sb.ToString();
        }

        private void DrawThumbnail(Graphics g,MyImageType image)
        {
            if (image.bitmap == null) return;
            g.DrawRectangle(new Pen(Color.Tomato), image.thumbnail);
            Rectangle nr = image.thumbnail;
            nr.X += (nr.Width - image.bitmap.Width) / 2;
            nr.Y += 2;
            nr.Width = image.bitmap.Width;
            nr.Height = image.bitmap.Height;
            g.DrawImage(image.bitmap, nr);


            g.DrawString(image.name, new Font("Verdana", 6), new SolidBrush(Color.Black),
                    image.thumbnail.Left, image.thumbnail.Bottom-25);
        }

        private void my_paint(object sender, PaintEventArgs e)
        {
            Graphics g = e.Graphics;
            RectangleF rec = g.ClipBounds;
            g.Clear(Color.White);
            int w = 140 , h = 150 ;

            Rectangle r = new Rectangle(0, 0, w-1, h-1);
            int a;
            for ( a = 0 ; a < images.Count ; a++ )
            {
                images[a].thumbnail = r;
                r.X += w;
                if (r.Right > thumbnails.Width)
                {
                    r.X = 0;
                    r.Y += h;
                }
                DrawThumbnail(g,images[a]);
            }
        }

        private void LoadThumbnails(String category)
        {
            if (last_cat_read == category) 
            {
                this.Refresh();
                return; 
            }

            last_cat_read = category;

            Cursor.Current = Cursors.WaitCursor;
            String url = "http://tools.wikimedia.de/~daniel/WikiSense/CategoryIntersect.php?wikifam=commons.wikimedia.org&basecat=" + category + "&basedeep=1&mode=iul&go=Scan&raw=on&userlang=en";
            String text = ReadWebPage(url).Replace("_"," ");
            String[] lines = text.Split(new Char[] { '\n' });

            images.Clear();
            progressBar1.Maximum = lines.Length;
            progressBar1.Value = 0;
            foreach (String l in lines)
            {
                progressBar1.Increment(1);
                if (l.Trim() == "") continue;
                MyImageType image = new MyImageType();
                String[] parts = l.Split(new Char[] { '\t' });
                image.name = parts[1];
                image.md5 = md5(image.url_name());
                if (image.MakeOrLoadThumbnail())
                {
                    images.Add(image);
                }
                this.Refresh();
            }
            Cursor.Current = Cursors.Arrow;
        }

        private String GetCatagoryCacheFile(String category)
        {
            category = category.Replace('/', '_');
            category = category.Replace('\\', '_');
            category = category.Replace(':', '_');
            String PersonalFolder = Environment.GetFolderPath(Environment.SpecialFolder.Personal);
            String filename = PersonalFolder + "/.CommonsThumbs";
            if (!Directory.Exists(filename)) Directory.CreateDirectory(filename);
            filename += "/categories";
            if (!Directory.Exists(filename)) Directory.CreateDirectory(filename);
            filename += "/" + category + ".txt";
            return filename;
        }

        private void CacheCategories ( String category , TreeNode[] subs)
        {
            String filename = GetCatagoryCacheFile(category);
            FileStream fs = File.Open(filename, FileMode.Create);
            foreach ( TreeNode n in subs )
            {
                if (n == null) continue;
                AddText(fs, n.Text + "\n" );
            }
            fs.Close() ;
        }

        private static void AddText(FileStream fs, string value)
        {
            byte[] info = new UTF8Encoding(true).GetBytes(value);
            fs.Write(info, 0, info.Length);
        }

        private void GetCategories(TreeNode node)
        {
            String category = node.Text;
            TreeNode[] tns = GetCategories(node.Text);
            if (tns == null) return; // Something's wrong
            node.Nodes.AddRange(tns);
        }

        private void categoryTree_AfterSelect(object sender, TreeViewEventArgs e)
        {
            TreeNode node = e.Node;
            LoadThumbnails(node.Text);
            if (node.Nodes.Count > 0) return; // Already has children
            GetCategories(node);
            node.Expand();
        }

        private void thumbnails_MouseDoubleClick(object sender, MouseEventArgs e)
        {
            int a;
            Point p = new Point(e.X, e.Y);
            for (a = 0; a < images.Count; a++)
            {
                if (images[a].thumbnail.Contains(p)) break;
            }
            if (a == images.Count) return;
            Help.ShowHelp(this, images[a].desc_url());
        }

        private void progressBar1_Click(object sender, EventArgs e)
        {

        }

        private void thumbnails_SizeChanged(object sender, EventArgs e)
        {
            this.Refresh();
        }

    }

    public class MyImageType
    {
        public String name, md5;
        public Rectangle thumbnail;
        public Bitmap bitmap;

        public String url_name()
        {
            return name.Replace(" ", "_");
        }

        public String desc_url()
        {
            return "http://commons.wikimedia.org/wiki/Image:" + url_name();
        }

        public String url()
        {
            return "http://upload.wikimedia.org/wikipedia/commons/" +
                md5.Substring(0, 1) + "/" +
                md5.Substring(0, 2) + "/" +
                url_name();
        }

        public String thumbnail_url( String pixel )
        {
            return "http://upload.wikimedia.org/wikipedia/commons/thumb/" +
                md5.Substring(0, 1) + "/" +
                md5.Substring(0, 2) + "/" +
                url_name() + "/" +
                pixel + "px-" + url_name();
        }

        public bool MakeOrLoadThumbnail()
        {
            String PersonalFolder = Environment.GetFolderPath(Environment.SpecialFolder.Personal);
            String thumbnail_name = url_name();
            thumbnail_name = thumbnail_name.Substring(0, thumbnail_name.LastIndexOf('.')) + ".png";
            thumbnail_name = thumbnail_name.Replace('/', '_');
            thumbnail_name = thumbnail_name.Replace('\\', '_');
            thumbnail_name = thumbnail_name.Replace(':', '_');
            String thumbnail_path = PersonalFolder + "/.CommonsThumbs/" +
                md5.Substring(0, 1) + "/" +
                md5.Substring(0, 2) + "/" +
                thumbnail_name;
            if (File.Exists(thumbnail_path))
            {
                bitmap = new Bitmap(thumbnail_path);
            }
            else
            {
                if (!MakeThumbnail(thumbnail_path)) return false;
                String d1 = PersonalFolder + "/.CommonsThumbs";
                if (!Directory.Exists(d1)) Directory.CreateDirectory(d1);
                d1 += "/" + md5.Substring(0, 1);
                if (!Directory.Exists(d1)) Directory.CreateDirectory(d1);
                d1 += "/" + md5.Substring(0, 2);
                if (!Directory.Exists(d1)) Directory.CreateDirectory(d1);
                bitmap.Save(thumbnail_path);
            }
            return true;
        }

        private bool LoadImage(String the_url)
        {
            try
            {
                HttpWebRequest request = (HttpWebRequest)WebRequest.Create(the_url);
                HttpWebResponse response = (HttpWebResponse)request.GetResponse();
                Stream resStream = response.GetResponseStream();
                bitmap = new Bitmap(resStream);
            }
            catch
            {
                return false;
            }
            return true;
        }

        private bool MakeThumbnail(String thumbnail_path)
        {
            try
            {
                if (!LoadImage(thumbnail_url("180")))
                    if (!LoadImage(thumbnail_url("120")))
                       LoadImage(url());

                Bitmap bmp = bitmap;
                int w = bmp.Width, h = bmp.Height, dest = 120;
                if (w > h)
                {
                    h = dest * h / w;
                    w = dest;
                }
                else
                {
                    w = dest * w / h;
                    h = 120;
                }
                Size ns = new Size(w, h);
                bitmap = new Bitmap(bmp, ns);
            }
            catch
            {
                return false;
            }
            return true;
        }

    }

}