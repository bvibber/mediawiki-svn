package net.psammead.functional.data;

import java.util.Collections;
import java.util.Iterator;
import java.util.List;
import java.util.NoSuchElementException;

import net.psammead.functional.Acceptor;
import net.psammead.functional.Function;
import net.psammead.util.ListUtil;
import net.psammead.util.ToString;
import net.psammead.util.annotation.ImmutableValue;

// TODO functional: Execution function and return left with value or right with Throwable 
// static <A> Either<A,Throwable> tryCatch(Function0<A> f)

@ImmutableValue 
public abstract class Either<L,R> {
	public static <L,R> Either<L,R> cond(boolean cond, L lvalue, R rvalue) { return cond ? new Left<L,R>(lvalue) : new Right<L,R>(rvalue); }
	public static <L,R> Either<L,R> left(L value)  { return new Left<L,R>(value); }
	public static <L,R> Either<L,R> right(R value) { return new Right<L,R>(value); }

	private Either() {}
	
	public abstract boolean isLeft();
	public abstract boolean isRight();
	public abstract LeftProjection<L,R>  left();
	public abstract RightProjection<L,R> right();
    public abstract Either<R,L> swap();
	
	public abstract void forEach(Acceptor<? super L> leffect, Acceptor<? super R> reffect);
	public abstract <X> X fold(Function<? super L,? extends X> lfunc, Function<? super R,? extends X> rfunc);
	/** aka either */
	public abstract <X,Y> Either<X,Y> map(Function<? super L,? extends X> lfunc, Function<? super R,? extends Y> rfunc);
	public abstract void visit(EitherVisitor<? super L,? super R> visitor);
	
	public interface LeftProjection<L,R> extends Iterable<L> {
		/** Returns true if Right or returns the result of the application of the given function to the Left value. */
		public boolean forAll(Function<? super L,Boolean> predicate);
		/** Returns false if Right or returns the result of the application of the given function to the Left value. */
		public boolean exists(Function<? super L,Boolean> predicate);
		/** Executes the given side-effect if this is a Left. */
		public void forEach(Acceptor<? super L> effect);
		/** Returns None if this is a Right or if the given predicate p does not hold for the left value, otherwise, returns a Left. */
		public Option<Either<L,R>> filter(Function<? super L,Boolean> predicate);
		/** Maps the function argument through Left. */
		public <X> Either<X,R> map(Function<? super L,X> function);
		/** Binds the given function across Left. */
		public <X> Either<X,R> flatMap(Function<? super L,Either<X,R>> function);
		/** Returns the value from this Left or throws {@link NoSuchElementException} if this is a {@link Right}. */
		public L getOrThrow();
		public L getOrNull();
		/** Returns the value from this Left or the given argument if this is a Right. */
		public <T extends L> L getOrElse(T or);
		public Iterator<L> iterator();
		/** Returns a Some containing the Left value if it exists or a None if this is a Right. */
		public Option<L> toOption();
		public List<L> asList();
	}
	
	public interface RightProjection<L,R> extends Iterable<R> {
		/** Returns true if Left or returns the result of the application of the given function to the Right value.. */
		public boolean forAll(Function<? super R,Boolean> predicate);
		/** Returns false if Left or returns the result of the application of the given function to the Right value. */
		public boolean	exists(Function<? super R,Boolean> predicate);
		/** Executes the given side-effect if this is a Reft. */
		public void forEach(Acceptor<? super R> effect);
		/** Returns None if this is a Left or if the given predicate p does not hold for the right value, otherwise, returns a Right. */
		public Option<Either<L,R>> filter(Function<? super R,Boolean> predicate);
		/** Maps the function argument through Right. */
		public <X> Either<L,X> map(Function<? super R,X> function);
		/** Binds the given function across Right. */
		public <X> Either<L,X> flatMap(Function<? super R,Either<L,X>> function);
		/** Returns the value from this Left or throws {@link NoSuchElementException} if this is a {@link Left}. */
		public R getOrThrow();
		public R getOrNull();
		/** Returns the value from this Left or the given argument if this is a Right. */
		public <T extends R> R getOrElse(T or);
		public Iterator<R> iterator();
		/** Returns a Some containing the Right value if it exists or a None if this is a Left. */
		public Option<R> toOption();
		public List<R> asList();
	}
	
	@ImmutableValue
	private static final class Left<L,R> extends Either<L,R> {
		private final L	value;
		private Left(L value) { this.value = value; }
		
		@Override public boolean isLeft() { return true; }
		@Override public boolean isRight() { return false; }
		@Override public Either<R,L> swap() { return right(value); }
		@Override public void visit(EitherVisitor<? super L,? super R> visitor) { visitor.left(value); }
		@Override public <X> X fold(Function<? super L,? extends X> lfunc, Function<? super R,? extends X> rfunc) { return lfunc.apply(value); }
		@Override public <X,Y> Either<X,Y> map(Function<? super L, ? extends X> lfunc, Function<? super R,? extends Y> rfunc) { return new Left<X,Y>(lfunc.apply(value)); }
		@Override public void forEach(Acceptor<? super L> leffect, Acceptor<? super R> reffect) { leffect.set(value); }
		
		@Override public LeftProjection<L,R> left() {
			return new LeftProjection<L,R>() {
				public boolean forAll(Function<? super L,Boolean> predicate) { return predicate.apply(value); }
				public boolean exists(Function<? super L,Boolean> predicate) { return predicate.apply(value); }
				public void forEach(Acceptor<? super L> effect) { effect.set(value); }
				public Option<Either<L,R>> filter(Function<? super L,Boolean> predicate) {
					return predicate.apply(value)
							? Option.<Either<L,R>>some(Left.this)
							: Option.<Either<L,R>>none();
				}
				public <X> Either<X,R> map(Function<? super L,X> function) { return new Left<X,R>(function.apply(value)); }
				public <X> Either<X,R> flatMap(Function<? super L,Either<X,R>> function) { return function.apply(value); }
				public L getOrThrow() { return value; }
				public L getOrNull() { return value; }
				public <T extends L> L getOrElse(T or) { return value; }
				public Iterator<L> iterator() { return asList().iterator(); }
				public Option<L> toOption() { return Option.some(value); }
				public List<L> asList() { return ListUtil.single(value); }
			};
		}
		@Override public RightProjection<L,R> right() {
			return new RightProjection<L,R>() {
				public boolean forAll(Function<? super R,Boolean> predicate) { return true; }
				public boolean	exists(Function<? super R,Boolean> predicate) { return false; }
				public void forEach(Acceptor<? super R> effect) {}
				public Option<Either<L,R>> filter(Function<? super R,Boolean> predicate) { return Option.none(); }
				public <X> Either<L,X> map(Function<? super R,X> function) { return new Left<L,X>(value); }
				public <X> Either<L,X> flatMap(Function<? super R,Either<L,X>> function){ return new Left<L,X>(value); }
				public R getOrThrow() { throw new NoSuchElementException("Left has no right value"); }
				public R getOrNull() { return null; }
				public <T extends R> R getOrElse(T or) { return or; }
				public Iterator<R> iterator() { return asList().iterator(); }
				public Option<R> toOption() { return Option.none(); }
				public List<R> asList() { return Collections.emptyList(); }
			};
		}
		
		@Override
		public String toString() {
			return new ToString(this)
					.append("value", value)
					.toString();
		}
		@Override
		public int hashCode() {
			final int prime = 31;
			int result = 1;
			result = prime * result + ((value == null) ? 0 : value.hashCode());
			return result;
		}
		@SuppressWarnings("unchecked")
		@Override
		public boolean equals(Object obj) {
			if (this == obj) return true;
			if (obj == null) return false;
			if (getClass() != obj.getClass()) return false;
			Left other = (Left)obj;
			if (value == null) {
				if (other.value != null) return false;
			}
			else if (!value.equals(other.value)) return false;
			return true;
		}
	}
	
	@ImmutableValue
	private static final class Right<L,R> extends Either<L,R> {
		private final R	value;
		private Right(R value) { this.value = value; }
		
		@Override public boolean isLeft() { return false; }
		@Override public boolean isRight() { return true; }
		@Override public Either<R,L> swap() { return left(value); }
		@Override public void visit(EitherVisitor<? super L, ? super R> visitor) { visitor.right(value); }
		@Override public <X> X fold(Function<? super L,? extends X> lfunc, Function<? super R,? extends X> rfunc) { return rfunc.apply(value); }
		@Override public <X,Y> Either<X,Y> map(Function<? super L,? extends X> lfunc, Function<? super R,? extends Y> rfunc) { return new Right<X,Y>(rfunc.apply(value)); }
		@Override public void forEach(Acceptor<? super L> leffect, Acceptor<? super R> reffect) { reffect.set(value); }
		
		@Override public LeftProjection<L,R> left() {
			return new LeftProjection<L,R>() {
				public boolean forAll(Function<? super L,Boolean> predicate) { return true; }
				public boolean exists(Function<? super L,Boolean> predicate) { return false; }
				public void forEach(Acceptor<? super L> effect) {}
				public Option<Either<L,R>> filter(Function<? super L,Boolean> predicate) { return Option.none(); }
				public <X> Either<X,R> map(Function<? super L,X> function) { return new Right<X,R>(value); }
				public <X> Either<X,R> flatMap(Function<? super L,Either<X,R>> function) { return new Right<X,R>(value); }
				public L getOrThrow() { throw new NoSuchElementException("Right has no left value"); }
				public L getOrNull() { return null; }
				public <T extends L> L getOrElse(T or) { return or; }
				public Iterator<L> iterator() { return asList().iterator(); }
				public Option<L> toOption() { return Option.none(); }
				public List<L> asList() { return Collections.emptyList(); }
			};
		}
		@Override public RightProjection<L,R> right() {
			return new RightProjection<L,R>() {
				public boolean forAll(Function<? super R,Boolean> predicate) { return predicate.apply(value); }
				public boolean	exists(Function<? super R,Boolean> predicate) { return predicate.apply(value); }
				public void forEach(Acceptor<? super R> effect) { effect.set(value); }
				public Option<Either<L,R>> filter(Function<? super R,Boolean> predicate) {
					return predicate.apply(value)
							? Option.<Either<L,R>>some(Right.this)
							: Option.<Either<L,R>>none();
				}
				public <X> Either<L,X> map(Function<? super R,X> function) { return new Right<L,X>(function.apply(value)); }
				public <X> Either<L,X> flatMap(Function<? super R,Either<L,X>> function) { return function.apply(value); }
				public R getOrThrow() { return value;}
				public R getOrNull() { return value; }
				public <T extends R> R getOrElse(T or) { return value; }
				public Iterator<R> iterator() { return asList().iterator(); }
				public Option<R> toOption() { return Option.some(value); }
				public List<R> asList() { return ListUtil.single(value); }
			};
		}
		
		@Override
		public String toString() {
			return new ToString(this)
					.append("value", value)
					.toString();
		}
		@Override
		public int hashCode() {
			final int prime = 31;
			int result = 1;
			result = prime * result + ((value == null) ? 0 : value.hashCode());
			return result;
		}
		@SuppressWarnings("unchecked")
		@Override
		public boolean equals(Object obj) {
			if (this == obj) return true;
			if (obj == null) return false;
			if (getClass() != obj.getClass()) return false;
			Right other = (Right)obj;
			if (value == null) {
				if (other.value != null) return false;
			}
			else if (!value.equals(other.value)) return false;
			return true;
		}
	}
}
