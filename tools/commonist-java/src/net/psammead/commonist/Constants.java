package net.psammead.commonist;

import javax.swing.BorderFactory;
import javax.swing.border.Border;

/** constants used throughout the application */
public final class Constants {
	public static final int THUMBNAIL_DEFAULT_SIZE		= 192;
	public static final int	THUMBNAIL_CACHE_SIZE		= 2000;	// images
	public static final int	THUMBNAIL_SCALE_HEADROOM	= 250;	// percent
	
//	public static final int FULLSIZE_MIN_FRAME_SIZE		= 32;	// pixel
	public static final int	FULLSIZE_MAX_UNIT_INCREMENT	= 64;	// pixel
	
	public static final int	IMAGELIST_UPDATE_DELAY		= 1500;	// millis
	public static final int IMAGELIST_UPDATE_COUNT		= 3;	// count
	
	public static final int	INPUT_FIELD_WIDTH			= 24;	// columns
	public static final int	INPUT_FIELD_HEIGHT			= 5; 	// rows
	
	public static final Border	PANEL_BORDER			= BorderFactory.createEmptyBorder(2,2,2,2);
}
