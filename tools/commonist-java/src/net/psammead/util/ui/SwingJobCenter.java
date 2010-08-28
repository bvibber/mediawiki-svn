package net.psammead.util.ui;

import javax.swing.SwingUtilities;

public final class SwingJobCenter {
	private SwingJobCenter() {}
	
	public static <V> void work(final SwingJob<V> job) {
		new Thread(new Runnable() {
			public void run() {
				try {
					final V value	= job.construct();
					SwingUtilities.invokeLater(new Runnable() {
						public void run() {
							job.finished(value);
						}
					});
				}
				catch (final Exception e) {
					SwingUtilities.invokeLater(new Runnable() {
						public void run() {
							job.failed(e);
						}
					});
				}
			}
		}).start();
	}
}
