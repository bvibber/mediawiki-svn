package net.psammead.commonist.thumb;

import java.awt.Dimension;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.geom.AffineTransform;
import java.awt.image.AffineTransformOp;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.Iterator;

import javax.imageio.ImageIO;
import javax.imageio.ImageReadParam;
import javax.imageio.ImageReader;
import javax.imageio.stream.ImageInputStream;
import javax.swing.Icon;
import javax.swing.ImageIcon;

import net.psammead.commonist.Constants;
import net.psammead.commonist.util.Settings;
import net.psammead.util.Logger;

/** manages thumbnail images */
public final class Thumbnails {
	private static final Logger log = new Logger(Thumbnails.class);
	
	private final FileCache	cache;
	private int	maxSize;

	/** creates and caches thumbnails */
	public Thumbnails(FileCache cache) {
		this.cache		= cache;
		this.maxSize	= Constants.THUMBNAIL_DEFAULT_SIZE;
	}

	public void loadSettings(Settings settings) {
		maxSize	= Integer.parseInt(settings.get("thumbnails.maxSize", ""+maxSize));
	}
	
	public void saveSettings(Settings settings) {
		settings.set("thumbnails.maxSize", ""+maxSize);
	}
	
	public int getMaxSize() {
		return maxSize;
	}
	
	/** creates a thumbnail Icon from an image File or returns null */ 
	public Icon thumbnail(File file) {
		try {
			final Image	thumb	= cachedThumbnail(file);
			if (thumb == null)	return null;
			return new ImageIcon(thumb);
		}
		catch (Exception e) {
			log.info("cannot create thumbnail from " + file, e);
			return null;
		}
	}
	
	/** make a thumbnail or return a cached version */
	private Image cachedThumbnail(File file) throws IOException {
		// get cached
		final File	thumbFile1	= cache.get(file);
		if (thumbFile1 != null) {
			return ImageIO.read(thumbFile1);
		}
		
		// read original
		//BufferedImage	image	= ImageIO.read(file);
		final BufferedImage	image	= readSubsampled(file);
		if (image == null)	{
			log.warn("could not read original: " + file);
			return null;
		}
		
		// make thumb
		final BufferedImage	thumb	= makeThumbnail(image);
		
		// cache thumb
		final File	thumbFile2	= cache.put(file);
		boolean	success	= ImageIO.write(thumb, "jpg", thumbFile2);
		if (!success) {
			log.warn("could not create thumbnail: " + thumbFile2);
			cache.remove(file);
		}
		
		return thumb;
	}
	
	/** makes a thumbnail from an image */
	private BufferedImage makeThumbnail(BufferedImage image) {
		final double	scale	= (double)maxSize / Math.max(image.getWidth(), image.getHeight());
		if (scale >= 1.0)	return image;
		
		// TODO: check more image types
		
		// seen: TYPE_BYTE_GRAY and TYPE_BYTE_INDEXED,
		// TYPE_CUSTOM			needs conversion or throws an ImagingOpException at transformation time
		// TYPE_3BYTE_BGR		works without conversion
		// TYPE_BYTE_GRAY		needs conversion or creates distorted grey version
		// TYPE_INT_RGB			needs conversion or creates distorted grey version
		// TYPE_BYTE_INDEXED	works, but inverts color if converted to TYPE_3BYTE_BGR
		
		// normalize image type
		final int	imageType	= image.getType();
		if (imageType != BufferedImage.TYPE_3BYTE_BGR 
		&& imageType != BufferedImage.TYPE_BYTE_INDEXED) {
//		if (imageType == BufferedImage.TYPE_CUSTOM  
//		|| imageType == BufferedImage.TYPE_BYTE_GRAY
//		|| imageType == BufferedImage.TYPE_INT_RGB) {
			final BufferedImage	normalized	= new BufferedImage(image.getWidth(), image.getHeight(), BufferedImage.TYPE_3BYTE_BGR);
			final Graphics		g			= normalized.getGraphics();
			g.drawImage(image, 0, 0, null);
			g.dispose();
			image	= normalized;
		}
		
		final Dimension			size	= new Dimension((int)(image.getWidth() * scale), (int)(image.getHeight() * scale));
		final BufferedImage		thumb	= new BufferedImage(size.width, size.height, image.getType());
		final AffineTransformOp	op		= new AffineTransformOp(
			new AffineTransform(scale, 0, 0, scale, 0, 0),	// AffineTransform.getScaleInstance(sx, sy)
			AffineTransformOp.TYPE_BILINEAR					// TYPE_NEAREST_NEIGHBOR, TYPE_BILINEAR, TYPE_BICUBIC
		);
		
		op.filter(image, thumb);
		return thumb;
	}
	
	/** scales down when the image is too big */
	private BufferedImage readSubsampled(File input) throws IOException {
		final ImageInputStream stream = ImageIO.createImageInputStream(input);
		if (stream == null)	throw new IOException("cannot create ImageInputStream for file: " + input);
		
		final Iterator<ImageReader>	it	= ImageIO.getImageReaders(stream);
        if (!it.hasNext())	return null;	// throw new IOException("cannot create ImageReader for file: " + input);
        
        final ImageReader		reader = it.next();
        reader.setInput(stream, true, true);
        
        final ImageReadParam	param	= reader.getDefaultReadParam();
		
        final int	imageIndex	= 0;

        final int	sizeX		= reader.getWidth(imageIndex);
        final int	sizeY		= reader.getHeight(imageIndex);
        final int	size		= Math.min(sizeX, sizeY);
        final int	scale		= size / maxSize;
        final int	sampling	= smallerPowerOf2(scale * 100 / Constants.THUMBNAIL_SCALE_HEADROOM);
//		System.err.println("#### scale=" + scale + "\t=> sampling=" + sampling);
		
		// TODO: could scale at load time!
        if (sampling > 1)	param.setSourceSubsampling(sampling, sampling, 0, 0);
        final BufferedImage	image	= reader.read(imageIndex, param);
        
        // TODO: finally
        stream.close();	
        reader.dispose();
        
        return image;
	}

	/** returns the biggers power of 2 smaller than or equal to x */
	private int smallerPowerOf2(int x) {
		int	exp	= 1;
		while (x > 0) {
			exp	<<= 1;
			x	>>= 1;
		}
		return exp >> 1;
	}
}
