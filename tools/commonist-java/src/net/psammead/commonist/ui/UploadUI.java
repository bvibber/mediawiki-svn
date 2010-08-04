package net.psammead.commonist.ui;

import java.awt.GridBagLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.JButton;
import javax.swing.JPanel;

import net.psammead.commonist.util.Messages;
import net.psammead.util.ui.GridBagConstraints2;

public final class UploadUI extends JPanel {
	/** action events this UI sends */
	public interface Callback {
		void startUpload();
		void stopUpload();
	}
	
	/** displays upload status and starts and stops uploading */
	public UploadUI(final Callback callback) {
		final JButton	uploadButton	= new JButton(Messages.text("upload.upload"));
		final JButton	abortButton		= new JButton(Messages.text("upload.abort"));
		
		setLayout(new GridBagLayout());
		add(abortButton,	new GridBagConstraints2().pos(0,0).size(1,1).weight(1,0).fillHorizontal());
		add(uploadButton,	new GridBagConstraints2().pos(1,0).size(1,1).weight(1,0).fillHorizontal());
		
		uploadButton.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent ev) {
				callback.startUpload();
			}
		});
		
		abortButton.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent ev) {
				callback.stopUpload();
			}
		});
	}
}
