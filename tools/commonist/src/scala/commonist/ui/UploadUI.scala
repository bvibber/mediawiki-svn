package commonist.ui

import java.awt.GridBagLayout
import java.awt.GridBagConstraints
import java.awt.event.ActionEvent
import java.awt.event.ActionListener

import javax.swing.JButton
import javax.swing.JPanel

import commonist.util.Messages

import scutil.ext.GridBagConstraintsExt._

/** action events this UI sends */
trait UploadUICallback {
	def startUpload()
	def stopUpload()
}

final class UploadUI(callback:UploadUICallback) extends JPanel {
	private val uploadButton	= new JButton(Messages.text("upload.upload"))
	private val abortButton		= new JButton(Messages.text("upload.abort"))
	
	setLayout(new GridBagLayout())
	add(abortButton,	GBC.pos(0,0).size(1,1).weight(1,0).fillHorizontal())
	add(uploadButton,	GBC.pos(1,0).size(1,1).weight(1,0).fillHorizontal())
	
	uploadButton.addActionListener(new ActionListener {
		def actionPerformed(ev:ActionEvent) {
			callback.startUpload()
		}
	})
	
	abortButton.addActionListener(new ActionListener {
		def actionPerformed(ev:ActionEvent) {
			callback.stopUpload()
		}
	})
}
