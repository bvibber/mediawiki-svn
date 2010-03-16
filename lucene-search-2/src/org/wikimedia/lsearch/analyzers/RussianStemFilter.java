package org.wikimedia.lsearch.analyzers;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.Token;
import java.io.IOException;
import org.apache.lucene.analysis.ru.RussianCharsets;

/**
 * 
 * Wrapper for Lucene's implementation of Russian stem filter. 
 * Seems the default snowball filter expects badly-decoded KOI8-R
 * java strings.
 *
 */
class RussianStemFilter extends TokenFilter {
    TokenStream input;
    private org.apache.lucene.analysis.ru.RussianStemFilter theFilter;
    public RussianStemFilter( TokenStream input ) {
        super( input );
        theFilter = new org.apache.lucene.analysis.ru.RussianStemFilter( input, RussianCharsets.UnicodeRussian );
    }
    public void close() throws IOException {
        theFilter.close();
    }
    public Token next() throws IOException {
        return theFilter.next();
    }
}
