package net.psammead.commonist.ui;

import java.awt.Color;
import java.awt.Dimension;
import java.awt.GridBagLayout;
import java.awt.Image;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.io.File;

import javax.swing.BorderFactory;
import javax.swing.Icon;
import javax.swing.JCheckBox;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import javax.swing.JTextField;

import net.psammead.commonist.Constants;
import net.psammead.commonist.data.ImageData;
import net.psammead.commonist.util.Messages;
import net.psammead.commonist.util.TextUtil2;
import net.psammead.util.ui.GridBagConstraints2;

/** a data editor with a thumbnail preview for an image File */
public final class ImageUI extends JPanel {
	/** action events this UI sends */
	public interface Callback {
		void updateSelectStatus();
	}
	
	// components
	private final JTextField	nameEditor;
	private final JTextArea		descriptionEditor;
	private final JTextField	categoriesEditor;
	private final JCheckBox		uploadEditor;
	
	// state
	private final File		file;
	private final String	programHeading;
	private final Image		programIcon;
	
	/** the icon may be null for no thumbnail */
	public ImageUI(File file, Icon icon, int thumbnailMaxSize, String programHeading, Image programIcon, final Callback callback) {
		this.file	= file;
		
		this.programHeading	= programHeading;
		this.programIcon	= programIcon;
		
		final Dimension	thumbDimension	= new Dimension(thumbnailMaxSize, thumbnailMaxSize);

		//------------------------------------------------------------------------------
		
		final JLabel imageView	= new JLabel(null, null, JLabel.CENTER);
		imageView.setBackground(Color.decode("#eeeeee"));
//		imageView.setBorder(
//			BorderFactory.createBevelBorder(BevelBorder.RAISED)
//		);
		imageView.setOpaque(true);
		/*### fehlt
		imageView.setToolTipText(
			file.Name + " (" + TextUtil.human(file.length()) + " bytes)"
		);
		*/
		imageView.setHorizontalTextPosition(JLabel.CENTER);
		imageView.setVerticalTextPosition(JLabel.CENTER);
		imageView.setPreferredSize(thumbDimension);
		imageView.setMinimumSize(thumbDimension);
		imageView.setMaximumSize(thumbDimension);
	
		final JLabel nameLabel		= new JLabel(Messages.text("image.name"));
		final JLabel descriptionLabel	= new JLabel(Messages.text("image.description"));
		final JLabel categoriesLabel	= new JLabel(Messages.text("image.categories"));
		final JLabel uploadLabel		= new JLabel(Messages.text("image.upload"));
	
		nameEditor			= new JTextField(Constants.INPUT_FIELD_WIDTH);
		descriptionEditor	= new JTextArea(Constants.INPUT_FIELD_HEIGHT, Constants.INPUT_FIELD_WIDTH);
		categoriesEditor	= new JTextField(Constants.INPUT_FIELD_WIDTH);
		uploadEditor		= new JCheckBox((Icon)null, false);
		
		descriptionEditor.setLineWrap(true);
		descriptionEditor.setWrapStyleWord(true);
		
		final JScrollPane descriptionScroll	= new JScrollPane(descriptionEditor, 
				JScrollPane.VERTICAL_SCROLLBAR_AS_NEEDED, JScrollPane.HORIZONTAL_SCROLLBAR_AS_NEEDED);
		
		categoriesEditor.setToolTipText(Messages.text("image.categories.tooltip"));
	
//		setBorder(
//			BorderFactory.createCompoundBorder(
//				//BorderFactory.createCompoundBorder(
//					BorderFactory.createRaisedBevelBorder(),
//				//	BorderFactory.createLoweredBevelBorder()
//				//),
//				BorderFactory.createEmptyBorder(2,0,2,0)
//			)
//		);
		setBorder(BorderFactory.createEmptyBorder(2,0,5,0));
	
		//------------------------------------------------------------------------------
		//## layout
		
		setLayout(new GridBagLayout());
		
		// labels and editors
		
		add(uploadLabel,		new GridBagConstraints2().pos(0,0).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(uploadEditor,		new GridBagConstraints2().pos(1,0).size(1,1).weight(1,0).anchorWest().fillHorizontal().insets(0,0,0,0));			
		
		add(nameLabel,			new GridBagConstraints2().pos(0,1).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(nameEditor,			new GridBagConstraints2().pos(1,1).size(1,1).weight(1,0).anchorWest().fillHorizontal().insets(0,0,0,0));
		
		add(descriptionLabel, 	new GridBagConstraints2().pos(0,2).size(1,1).weight(0,0).anchorNorthEast().fillNone().insets(0,4,0,4));
		add(descriptionScroll,	new GridBagConstraints2().pos(1,2).size(1,1).weight(1,1).anchorWest().fillBoth().insets(0,0,0,0));
		
		add(categoriesLabel,	new GridBagConstraints2().pos(0,3).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(categoriesEditor,	new GridBagConstraints2().pos(1,3).size(1,1).weight(1,0).anchorWest().fillHorizontal().insets(0,0,0,0));
		
		// image
		add(imageView,			new GridBagConstraints2().pos(2,0).size(1,4).weight(0,0).anchorSouthWest().fillNone().insets(0,4,0,4));
		
		//------------------------------------------------------------------------------
		//## wiring
		
		// update select status on upload checkbox changes
		uploadEditor.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent ev) {
				callback.updateSelectStatus();
			}
		});
		
		// open full size view on click
		imageView.addMouseListener(new MouseAdapter() {
			@Override
			public void mouseClicked(MouseEvent ev) {
				// LMB only
				if (ev.getButton() != 1)	return;
				//if (imageView.Icon != null)
				displayFullImage();
			}
		});
		
		//------------------------------------------------------------------------------
		//## init
		
		imageView.setToolTipText(Messages.message("image.tooltip", new Object[] { file.getName(), TextUtil2.human(file.length()) }));
		imageView.setIcon(icon);
		imageView.setText(icon == null ? Messages.text("image.nothumb") : null);
		
		nameEditor.setText(fixImageName(file.getName()));
		descriptionEditor.setText("");
		categoriesEditor.setText("");
		uploadEditor.setSelected(false);
	}
	
	@Override
	public Dimension getMaximumSize() {
		return new Dimension(
			super.getMaximumSize().width,
			super.getPreferredSize().height
		);
	}

	/** returns true when this file should be uploaded */
	public boolean isUploadSelected() {
		return uploadEditor.isSelected();
	}
	
	/** sets whether this file should be uploaded */
	public void setUploadSelected(boolean selected) {
		uploadEditor.setSelected(selected);
	}
	
	/** gets all data edit in this UI */
	public ImageData getData() {
		return new ImageData(
				file,
				fixImageName(nameEditor.getText()),
				descriptionEditor.getText(),
				categoriesEditor.getText(),
				uploadEditor.isSelected());
	}
	
	private void displayFullImage() {
		FullImageWindow.display(file, programHeading, programIcon);
	}
	
	/** trims a String and changes its first letter to upper case */
	private String fixImageName(String imageName) {
		// spaces at the start or end of filenames are not allowed
		final String	str	= imageName.trim();
		if (str.length() < 1)	return str;
		final StringBuilder	b	= new StringBuilder(str);
		b.setCharAt(0, Character.toUpperCase(str.charAt(0)));
		return b.toString();
	}
}
