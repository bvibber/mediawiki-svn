package org.wikimedia.lsearch.suggest.dist;


/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Calculates (restricted) Damerau-Levenshtein edit distance
 * with a custom cost function (modifications at the beginning 
 * of the word yield increase the distance by 2, not 1)
 */
public class EditDistance {

    final char[] sa;
    final int n;
    final int[][][] cache=new int[30][][];


    /**
     * Optimized to run a bit faster than the static getDistance().
     * In one benchmark times were 5.3sec using ctr vs 8.5sec w/ static method, thus 37% faster.
     */
    public EditDistance (String target) {
        sa=target.toCharArray();
        n=sa.length;
    }


    //*****************************
     // Compute Levenshtein distance
     //*****************************
      public final int getDistance (String other) {
          int d[][]; // matrix
          int cost; // cost

          // Step 1
          final char[] ta=other.toCharArray();
          final int m=ta.length;
          if (n==0) {
              return m;
          }
          if (m==0) {
              return n;
          }

          if (m>=cache.length) {
              d=form(n, m);
          }
          else if (cache[m]!=null) {
              d=cache[m];
          }
          else {
              d=cache[m]=form(n, m);

              // Step 3

          }
          for (int i=1; i<=n; i++) {
              final char s_i=sa[i-1];

              // Step 4

              for (int j=1; j<=m; j++) {
                  final char t_j=ta[j-1];

                  // Step 5

                  if (s_i==t_j) { // same
                      cost=0;
                  }
                  else { // not a match
                  	// penalize the initial substition 
                      cost=(i==1 || j==1)? 2 : 1;

                  }
                  // Step 6
                  // penalize insert/deletions at the beginning
                  int insdel = (i==1 || j==1)? 2 : 1;
                  d[i][j]=min3(d[i-1][j]+insdel, d[i][j-1]+insdel, d[i-1][j-1]+cost);
                  // transposition
                  if(i>1 && j>1 && sa[i-1] == ta[j-2] && sa[i-2] == ta[j-1]){
                  	d[i][j] = min2(d[i][j],d[i-2][j-2] + 1);
                  }
              }

          }

          // Step 7
          return d[n][m];

      }


    /**
     *
     */
    private static int[][] form (int n, int m) {
        int[][] d=new int[n+1][m+1];
        // Step 2

        for (int i=0; i<=n; i++) {
            d[i][0]=i*2;

        }
        for (int j=0; j<=m; j++) {
            d[0][j]=j*2;
        }
        return d;
    }


    //****************************
     // Get minimum of three values
     //****************************
      private static int min3 (int a, int b, int c) {
          int mi=a;
          if (b<mi) {
              mi=b;
          }
          if (c<mi) {
              mi=c;
          }
          return mi;

      }
      
      private static int min2 (int a, int b) {
      	if(a <= b)
      		return a;
      	else return b;
     }
}
