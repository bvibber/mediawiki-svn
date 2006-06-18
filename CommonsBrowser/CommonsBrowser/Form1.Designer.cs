namespace CommonsBrowser
{
    partial class Form1
    {
        /// <summary>
        /// Erforderliche Designervariable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Verwendete Ressourcen bereinigen.
        /// </summary>
        /// <param name="disposing">True, wenn verwaltete Ressourcen gelöscht werden sollen; andernfalls False.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Vom Windows Form-Designer generierter Code

        /// <summary>
        /// Erforderliche Methode für die Designerunterstützung.
        /// Der Inhalt der Methode darf nicht mit dem Code-Editor geändert werden.
        /// </summary>
        private void InitializeComponent()
        {
            this.categoryTree = new System.Windows.Forms.TreeView();
            this.label1 = new System.Windows.Forms.Label();
            this.splitter2 = new System.Windows.Forms.Splitter();
            this.thumbnails = new System.Windows.Forms.Panel();
            this.progressBar1 = new System.Windows.Forms.ProgressBar();
            this.SuspendLayout();
            // 
            // categoryTree
            // 
            this.categoryTree.Font = new System.Drawing.Font("Arial", 8.25F);
            this.categoryTree.Location = new System.Drawing.Point(12, 125);
            this.categoryTree.Name = "categoryTree";
            this.categoryTree.Size = new System.Drawing.Size(210, 394);
            this.categoryTree.TabIndex = 0;
            this.categoryTree.AfterSelect += new System.Windows.Forms.TreeViewEventHandler(this.categoryTree_AfterSelect);
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(9, 109);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(70, 13);
            this.label1.TabIndex = 1;
            this.label1.Text = "Category tree";
            // 
            // splitter2
            // 
            this.splitter2.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D;
            this.splitter2.Location = new System.Drawing.Point(0, 0);
            this.splitter2.Name = "splitter2";
            this.splitter2.Size = new System.Drawing.Size(239, 559);
            this.splitter2.TabIndex = 2;
            this.splitter2.TabStop = false;
            // 
            // thumbnails
            // 
            this.thumbnails.AutoScroll = true;
            this.thumbnails.AutoSizeMode = System.Windows.Forms.AutoSizeMode.GrowAndShrink;
            this.thumbnails.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D;
            this.thumbnails.Dock = System.Windows.Forms.DockStyle.Fill;
            this.thumbnails.Location = new System.Drawing.Point(239, 0);
            this.thumbnails.Name = "thumbnails";
            this.thumbnails.Size = new System.Drawing.Size(701, 559);
            this.thumbnails.TabIndex = 3;
            this.thumbnails.MouseDoubleClick += new System.Windows.Forms.MouseEventHandler(this.thumbnails_MouseDoubleClick);
            this.thumbnails.Paint += new System.Windows.Forms.PaintEventHandler(this.my_paint);
            this.thumbnails.SizeChanged += new System.EventHandler(this.thumbnails_SizeChanged);
            // 
            // progressBar1
            // 
            this.progressBar1.Location = new System.Drawing.Point(12, 525);
            this.progressBar1.Name = "progressBar1";
            this.progressBar1.Size = new System.Drawing.Size(210, 22);
            this.progressBar1.TabIndex = 4;
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(940, 559);
            this.Controls.Add(this.progressBar1);
            this.Controls.Add(this.thumbnails);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.categoryTree);
            this.Controls.Add(this.splitter2);
            this.Name = "Form1";
            this.Text = "CommonsBrowser";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.TreeView categoryTree;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Splitter splitter2;
        private System.Windows.Forms.Panel thumbnails;
        private System.Windows.Forms.ProgressBar progressBar1;

    }
}

