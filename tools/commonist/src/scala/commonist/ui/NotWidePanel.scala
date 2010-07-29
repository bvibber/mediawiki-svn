package commonist.ui

import java.awt.Dimension

import javax.swing.JPanel

// TODO make this a trait
class NotWidePanel(minimumWidth:Int) extends JPanel {
	override def getMinimumSize():Dimension = new Dimension(
			minimumWidth, 
			super.getMinimumSize.height)
}