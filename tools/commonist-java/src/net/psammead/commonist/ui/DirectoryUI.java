package net.psammead.commonist.ui;

import java.io.File;
import java.util.Enumeration;
import java.util.Stack;

import javax.swing.JScrollPane;
import javax.swing.JTree;
import javax.swing.event.TreeExpansionEvent;
import javax.swing.event.TreeExpansionListener;
import javax.swing.event.TreeSelectionEvent;
import javax.swing.event.TreeSelectionListener;
import javax.swing.tree.DefaultTreeModel;
import javax.swing.tree.TreePath;
import javax.swing.tree.TreeSelectionModel;

import net.psammead.commonist.Constants;
import net.psammead.commonist.util.Settings;
import net.psammead.util.Logger;

/** a JTree used to select a directory in the filesystem */
public final class DirectoryUI extends JScrollPane {
	private static final Logger log = new Logger(DirectoryUI.class);
	
	/** action events this UI sends */
	public interface Callback {
		void changeDirectory(File currentDirectory);
	}
	
	// components
	private	final JTree		directoryTree;
	
	// state
	private File		currentDirectory;
	private	final FileNode	baseNode;

	/** an UI to select the current directory */
	public DirectoryUI(final Callback callback) {
		//------------------------------------------------------------------------------
		//## init
		
		currentDirectory	= new File(System.getProperty("user.home"));

		// make a root tree node
		
		final File[]		rootDirs	= File.listRoots();
		final boolean	multiRoot	= rootDirs.length > 1;
		if (multiRoot) {
			baseNode	= new FileNode(new File("/"));	//### FAKE..
			for (int i=0; i<rootDirs.length; i++) {
				final File		rootDir 	= rootDirs[i];
				final FileNode	rootNode	= new FileNode(rootDir);
				baseNode.add(rootNode);
				//### A:\ removed, the tree expand will update later
				// rootNode.update();
			}
		}
		else {
			baseNode	= new FileNode(rootDirs[0]);
			baseNode.update();
		}
		
		//------------------------------------------------------------------------------
		//## components
		
		final DefaultTreeModel	directoryModel	= new DefaultTreeModel(baseNode);
		directoryTree	= new JTree();
		directoryTree.setModel(directoryModel);
		//directoryTree.setRootVisible(false);
		directoryTree.getSelectionModel().setSelectionMode(TreeSelectionModel.SINGLE_TREE_SELECTION);
		
		setViewportView(directoryTree);
		setBorder(Constants.PANEL_BORDER);
		
		//------------------------------------------------------------------------------
		//##  wiring
		
		directoryTree.addTreeExpansionListener(new TreeExpansionListener() {
			public void treeExpanded(TreeExpansionEvent ev) {
				final TreePath path	= ev.getPath();
				final FileNode node	= (FileNode)path.getLastPathComponent();
				node.update();
				directoryModel.nodeStructureChanged(node);
			}
			public void treeCollapsed(TreeExpansionEvent ev) {}
		});
		directoryTree.addTreeSelectionListener(new TreeSelectionListener() {
			public void valueChanged(TreeSelectionEvent ev) {
				final FileNode	node	= (FileNode)ev.getPath().getLastPathComponent();
				currentDirectory	= node.getFile();
				callback.changeDirectory(currentDirectory);
			}
		});
	}
	
	//------------------------------------------------------------------------------
	//## BrowseDirectory action

	/** browses all directories from the root to a given directory */
	@SuppressWarnings("unchecked")
	public void browseDirectory(File directory) {
		// build stack
		final Stack<File>	stack	= new Stack<File>();
		for (;;) {
			stack.push(directory);
			directory	= directory.getParentFile();
			if (directory == null)	break;
		}
		
		// find root node
		directory	= stack.pop();
		FileNode	node		= null;
		if (baseNode.getFile().equals(directory)) {
			node	= baseNode;
		}
		else {
			//### the baseNode has to be expanded here (!?)
			for (Enumeration<FileNode> it=baseNode.children(); it.hasMoreElements();) {
				FileNode test = it.nextElement();
				if (test.getFile().equals(directory)) {
					node	= test;							
					break;
				}
			}
		}
		if (node == null)	{ log.warn("first node not found!"); return; }
	
		final TreePath	path1	= new TreePath(node.getPath());
		directoryTree.expandPath(path1);
		
		//------------------------------------------------------------------------------
		
		while (!stack.empty()) {
			directory	= stack.pop();
			FileNode	child	= null;
			for (Enumeration<FileNode> it=node.children(); it.hasMoreElements();) {
				FileNode test = it.nextElement();
				if (test.getFile().equals(directory)) {
					child	= test;
					break;
				}
			}
			node	= child;
			if (node == null)	{ log.warn("child node not found!"); return; }
			
			final TreePath	path2	= new TreePath(node.getPath());
			directoryTree.expandPath(path2);
		}
	
		// does not get visible.. why?
		final TreePath	path3	= new TreePath(node.getPath());
		directoryTree.expandPath(path3);	
		directoryTree.makeVisible(path3);
		directoryTree.setSelectionPath(path3);
	}
	
	//------------------------------------------------------------------------------
	//## Settings

	/** loads this UI's state from the properties */
	public void loadSettings(Settings settings) {
		currentDirectory	= new File(settings.get("directoryTree.currentDirectory", 	System.getProperty("user.home")));
		browseDirectory(currentDirectory);
	}
	
	/** stores this UI's state in the properties */
	public void saveSettings(Settings settings) {
		settings.set("directoryTree.currentDirectory",	currentDirectory.getPath());
	}
}
