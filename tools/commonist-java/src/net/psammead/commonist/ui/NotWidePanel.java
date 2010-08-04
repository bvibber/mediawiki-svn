package net.psammead.commonist.ui;

import java.awt.Dimension;

import javax.swing.JPanel;

public class NotWidePanel extends JPanel {
	private final int	minimumWidth;
	
	public NotWidePanel(int minimumWidth) {
		this.minimumWidth = minimumWidth;
	}
	
	@Override
	public Dimension getMinimumSize() {
		return new Dimension(
				minimumWidth, 
				super.getMinimumSize().height);
	}
}