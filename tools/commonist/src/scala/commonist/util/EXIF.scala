package commonist.util

import java.io._
import java.util._
import java.text._
import java.math.MathContext
import java.math.RoundingMode

import org.apache.sanselan.ImageReadException
import org.apache.sanselan.Sanselan
import org.apache.sanselan.common.IImageMetadata
import org.apache.sanselan.common.RationalNumber
import org.apache.sanselan.formats.jpeg.JpegImageMetadata
import org.apache.sanselan.formats.tiff.TiffField
import org.apache.sanselan.formats.tiff.TiffImageMetadata
import org.apache.sanselan.formats.tiff.constants.TagInfo
//import org.apache.sanselan.formats.tiff.constants.ExifTagConstants._
import org.apache.sanselan.formats.tiff.constants.TiffTagConstants._
import org.apache.sanselan.formats.tiff.constants.TiffDirectoryConstants._
import org.apache.sanselan.formats.tiff.constants.GPSTagConstants._

import scutil.Log._
import scutil.BigRational
import scutil.ext.AnyRefExt._
import scutil.ext.OptionExt._
import scutil.ext.BooleanExt._

object EXIF {
	val NONE	= EXIF(None, None)
	
	def extract(file:File):EXIF = 
			try {
				(Sanselan getMetadata file).optionInstance[JpegImageMetadata].fold{ meta =>
					INFO("found EXIF data", file)
					EXIF(getDate(meta), getGPS(meta))
				}{ 
					INFO("no EXIF data found", file)
					NONE
				}
			}
			catch {
				case e:ImageReadException =>
					DEBUG("cannot read file", file, e.getMessage)
					NONE
			}
	
	//------------------------------------------------------------------------------
	
	/*
	// NOTE this doesn't work in sanselan 0.97 because FieldTypeRational#getSimpleValue
	// returns either a RationalNumber or an Array of RationalNumber whereas
	// EXIF#getGPS expects to get an Array of exactly 3 RationalNumber objects
	private def getGPS(metaData:JpegImageMetadata):Option[GPS] =
			for {
				exif	<- metaData.getExif.nullOption
				gps		<- exif.getGPS.nullOption
			}
			yield GPS(gps.getLatitudeAsDegreesNorth, gps.getLongitudeAsDegreesEast)
	*/
	
	import org.apache.sanselan.common.RationalNumber
	
	private def getGPS(metaData:JpegImageMetadata):Option[GPS] =
			for {
				exif			<- metaData.getExif.nullOption
				gpsDir			<- (exif findDirectory DIRECTORY_TYPE_GPS).nullOption
				
				latitudeRef		<- (gpsDir findField GPS_TAG_GPS_LATITUDE_REF).nullOption
				latitudeVal		<- (gpsDir findField GPS_TAG_GPS_LATITUDE).nullOption
				latitude		<- part(latitudeVal, latitudeRef, scala.collection.immutable.Map("n" -> 1, "s" -> -1))
				
				longitudeRef	<- (gpsDir findField GPS_TAG_GPS_LONGITUDE_REF).nullOption
				longitudeVal	<- (gpsDir findField GPS_TAG_GPS_LONGITUDE).nullOption
				longitude		<- part(longitudeVal, longitudeRef, scala.collection.immutable.Map("e" -> 1, "w" -> -1))
			}
			yield {
				GPS(latitude, longitude)
			}
	
	private def part(valueField:TiffField, signField:TiffField, signCalc:PartialFunction[String,Int]):Option[BigDecimal] =
			for {
				sign	<- signCalc.lift(signField.getStringValue.trim.toLowerCase)
				value	<- decimal(valueField.getValue)
			}
			yield {
				value * sign
			}
	
	private def decimal(value:AnyRef):Option[BigDecimal] = value match {
		case dms:Array[RationalNumber] if dms.length == 3	=>
			val	all	= dms map bigRational
			val sum	= all(0) / BigRational(1) + all(1) / BigRational(60) + all(2) / BigRational(3600)
			Some(bigDecimal(sum))
		case d:RationalNumber =>
			val	sum	= bigRational(d)
			Some(bigDecimal(sum))
		case x =>
			DEBUG("unexpected value: " + x)
			None			
	}
	private def bigRational(value:RationalNumber):BigRational	= BigRational(value.numerator, value.divisor)
	private def bigDecimal(value:BigRational):BigDecimal		= new BigDecimal(value toBigDecimal gpsPrecision)
	private val gpsPrecision:MathContext						= new MathContext(9, RoundingMode.HALF_EVEN)
		 
	//------------------------------------------------------------------------------
	
	// TODO newest of several dates: TIFF_TAG_DATE_TIME, EXIF_TAG_DATE_TIME_ORIGINAL, ...
	private def getDate(metaData:JpegImageMetadata):Option[Date] =
			for {
				tag		<- (metaData findEXIFValue TIFF_TAG_DATE_TIME).nullOption
				date	<- parseDate(tag.getValueDescription)
			} 
			yield date
	
	// @see http://www.awaresystems.be/imaging/tiff/tifftags/privateifd/exif/datetimeoriginal.html
	private def parseDate(s:String):Option[Date] = 
			try { Some(new SimpleDateFormat("''yyyy:MM:dd HH:mm:ss''") parse s) }
			catch { case e:ParseException => DEBUG("cannot parse date", s); None }
}

case class EXIF(date:Option[Date], gps:Option[GPS])
case class GPS(latitude:BigDecimal, longitude:BigDecimal)
