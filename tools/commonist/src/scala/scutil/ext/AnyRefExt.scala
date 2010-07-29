package scutil.ext

import scala.reflect.Manifest

object AnyRefExt {
	implicit def toAnyRefExt[T <: AnyRef](delegate:T):AnyRefExt[T] = new AnyRefExt[T](delegate)
}
final class AnyRefExt[T <: AnyRef](delegate:T) {
	def nullOption:Option[T] = Option(delegate)
	/*
	def nullOption:Option[T] =
			if (delegate eq null)	None
			else					Some(delegate)
	*/
			
	def optionInstance[T](implicit m:Manifest[T]):Option[T] =
			if (!(delegate eq null) && Manifest.singleType(delegate) <:< m)	Some(delegate.asInstanceOf[T])
			else															None
			
	// def |>[X](f:Function1[T,X]):X = f(delegate) 
}
