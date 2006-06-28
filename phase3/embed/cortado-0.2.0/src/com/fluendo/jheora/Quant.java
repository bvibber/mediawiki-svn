/* Jheora
 * Copyright (C) 2004 Fluendo S.L.
 *  
 * Written by: 2004 Wim Taymans <wim@fluendo.com>
 *   
 * Many thanks to 
 *   The Xiph.Org Foundation http://www.xiph.org/
 * Jheora was based on their Theora reference decoder.
 *   
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public License
 * as published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Library General Public License for more details.
 * 
 * You should have received a copy of the GNU Library General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */

package com.fluendo.jheora;

import com.jcraft.jogg.*;

public class Quant 
{ 
  private static final int MIN_DEQUANT_VAL       = 2;
  private static final int IDCT_SCALE_FACTOR     = 2; /* Shift left bits to improve IDCT precision */
  private static int ilog (long v)
  {
    int ret=0;

    while (v != 0) {
      ret++;
      v>>=1;
    }
    return ret;
  }

  private static int _read_qtable_range(Info ci, Buffer opb, int N) 
  {
    int range;
    int qi = 0;

    opb.readB(ilog(N-1)); /* qi=0 index */
    while(qi<63) {
      range = opb.readB(ilog(62-qi)); /* range to next code q matrix */
      range++;
      if(range<=0)
        return Result.BADHEADER;
      qi+=range;
      opb.readB(ilog(N-1)); /* next index */
    }
  
    return 0;
  }

  public static int readQTables(Info ci, Buffer opb) {
    long bits,value;
    int x,y, N;
    //System.out.println("Reading Q tables...");
    /* AC scale table */
    bits = opb.readB(4); bits++;
    for(x=0; x<Constants.Q_TABLE_SIZE; x++) {
      value = opb.readB((int)bits);
      if(bits<0)return Result.BADHEADER;
      ci.QThreshTable[x]=(int)value;
      //System.out.print(value+" ");
    }
    /* DC scale table */
    bits = opb.readB(4); bits++;
    for(x=0; x<Constants.Q_TABLE_SIZE; x++) {
      value = opb.readB((int)bits);
      if(bits<0)return Result.BADHEADER;
      ci.DcScaleFactorTable[x]=(short)value;
    }
    /* base matricies */
    N = opb.readB(9); N++;
    //System.out.println("  max q matrix index "+N);
    if(N!=3)return Result.BADHEADER; /* we only support the VP3 config */
    ci.qmats= new short[N*64];
    ci.MaxQMatrixIndex = N;
    for(y=0; y<N; y++) {
      //System.out.println("q matrix "+ y+": ");
      for(x=0; x<64; x++) {
        value = opb.readB(8);
        if(bits<0)return Result.BADHEADER;
        ci.qmats[(y<<6)+x]=(short)value;
        //System.out.print(value+" ");
        //if((x+1)%8==0)System.out.println();
      }
      //System.out.println();
    }
    /* table mapping */
    {
      int flag, ret;
      /* intra Y */
      //System.out.print("\n Intra Y:");
      if((ret=_read_qtable_range(ci,opb,N))<0) return ret;
      /* intra U */
      //System.out.print("\n Intra U:");
      flag = opb.readB(1);
      if(flag<0) return Result.BADHEADER;
      if(flag != 0) {
        /* explicitly coded */
        if((ret=_read_qtable_range(ci,opb,N))<0) return ret;
      } else {
        /* same as previous */
        //System.out.print("same as above");
      }
      /* intra V */
      //System.out.print("\n Intra V:");
      flag = opb.readB(1);
      if(flag<0) return Result.BADHEADER;
      if(flag != 0) {
        /* explicitly coded */
        if((ret=_read_qtable_range(ci,opb,N))<0) return ret;
      } else {
         /* same as previous */
        //System.out.print("same as above");
      }
      /* inter Y */
      //System.out.print("\n Inter Y:");
      flag = opb.readB(1);
      if(flag<0) return Result.BADHEADER;
      if(flag != 0) {
        /* explicitly coded */
        if((ret=_read_qtable_range(ci,opb,N))<0) return ret;
      } else {
        flag = opb.readB(1);
        if(flag<0) return Result.BADHEADER;
        if(flag != 0) {
          /* same as corresponding intra */
          //System.out.print("same as intra");
        } else {
          /* same as previous */
          //System.out.print("same as above");
        }
      }
      /* inter U */
      //System.out.print("\n Inter U:");
      flag = opb.readB(1);
      if(flag<0) return Result.BADHEADER;
      if(flag != 0) {
        /* explicitly coded */
        if((ret=_read_qtable_range(ci,opb,N))<0) return ret;
      } else {
        flag = opb.readB(1);
        if(flag<0) return Result.BADHEADER;
        if(flag != 0) {
          /* same as corresponding intra */
          //System.out.print("same as intra");
        } else {
          /* same as previous */
          //System.out.print("same as above");
        }
      }
      /* inter V */
      //System.out.print("n Inter V:");
      flag = opb.readB(1);
      if(flag<0) return Result.BADHEADER;
      if(flag != 0) {
        /* explicitly coded */
        if((ret=_read_qtable_range(ci,opb,N))<0) return ret;
      } else {
        flag = opb.readB(1);
        if(flag<0) return Result.BADHEADER;
        if(flag != 0) {
          /* same as corresponding intra */
          //System.out.print("same as intra");
        } else {
          /* same as previous */
          //System.out.print("same as above");
        }
      }
      //System.out.println();
    }
    
    /* ignore the range table and reference the matricies we use */
    System.arraycopy(ci.qmats,    0, ci.Y_coeffs,     0, 64);
    System.arraycopy(ci.qmats,   64, ci.UV_coeffs,    0, 64);
    System.arraycopy(ci.qmats, 2*64, ci.Inter_coeffs, 0, 64);
  
    return 0;
  }

  static void BuildQuantIndex_Generic(Playback pbi){
    int i,j;

    /* invert the dequant index into the quant index */
    for ( i = 0; i < Constants.BLOCK_SIZE; i++ ){
      j = Constants.dequant_index[i];
      pbi.quant_index[j] = i;
    }
  }

  static void init_dequantizer (Playback pbi,
                        int scale_factor,
                        byte  QIndex ){
    int i, j;

    short[] Inter_coeffs;
    short[] Y_coeffs;
    short[] UV_coeffs;
    short[] DcScaleFactorTable;
    short[] UVDcScaleFactorTable;

    Inter_coeffs = pbi.Inter_coeffs;
    Y_coeffs = pbi.Y_coeffs;
    UV_coeffs = pbi.UV_coeffs;
    DcScaleFactorTable = pbi.DcScaleFactorTable;
    UVDcScaleFactorTable = pbi.DcScaleFactorTable;

    /* invert the dequant index into the quant index
       the dxer has a different order than the cxer. */
    BuildQuantIndex_Generic(pbi);

    /* Reorder dequantisation coefficients into dct zigzag order. */
    for ( i = 0; i < Constants.BLOCK_SIZE; i++ ) {
      j = pbi.quant_index[i];
      pbi.dequant_Y_coeffs[j] = Y_coeffs[i];
      pbi.dequant_Inter_coeffs[j] = Inter_coeffs[i];
      pbi.dequant_UV_coeffs[j] = UV_coeffs[i];
      pbi.dequant_InterUV_coeffs[j] = Inter_coeffs[i];
    }

    /* Intra Y */
    pbi.dequant_Y_coeffs[0] = (short)
      ((DcScaleFactorTable[QIndex] * pbi.dequant_Y_coeffs[0])/100);
    if ( pbi.dequant_Y_coeffs[0] < MIN_DEQUANT_VAL * 2 )
      pbi.dequant_Y_coeffs[0] = MIN_DEQUANT_VAL * 2;
    pbi.dequant_Y_coeffs[0] = (short) 
      (pbi.dequant_Y_coeffs[0] << IDCT_SCALE_FACTOR);

    /* Intra UV */
    pbi.dequant_UV_coeffs[0] = (short)
      ((UVDcScaleFactorTable[QIndex] * pbi.dequant_UV_coeffs[0])/100);
    if ( pbi.dequant_UV_coeffs[0] < MIN_DEQUANT_VAL * 2 )
      pbi.dequant_UV_coeffs[0] = MIN_DEQUANT_VAL * 2;
    pbi.dequant_UV_coeffs[0] = (short)
      (pbi.dequant_UV_coeffs[0] << IDCT_SCALE_FACTOR);

    /* Inter Y */
    pbi.dequant_Inter_coeffs[0] = (short)
      ((DcScaleFactorTable[QIndex] * pbi.dequant_Inter_coeffs[0])/100);
    if ( pbi.dequant_Inter_coeffs[0] < MIN_DEQUANT_VAL * 4 )
      pbi.dequant_Inter_coeffs[0] = MIN_DEQUANT_VAL * 4;
    pbi.dequant_Inter_coeffs[0] = (short)
      (pbi.dequant_Inter_coeffs[0] << IDCT_SCALE_FACTOR);

    /* Inter UV */
    pbi.dequant_InterUV_coeffs[0] = (short)
      ((UVDcScaleFactorTable[QIndex] * pbi.dequant_InterUV_coeffs[0])/100);
    if ( pbi.dequant_InterUV_coeffs[0] < MIN_DEQUANT_VAL * 4 )
      pbi.dequant_InterUV_coeffs[0] = MIN_DEQUANT_VAL * 4;
    pbi.dequant_InterUV_coeffs[0] = (short)
      (pbi.dequant_InterUV_coeffs[0] << IDCT_SCALE_FACTOR);
  
    for ( i = 1; i < 64; i++ ){
      /* now scale coefficients by required compression factor */
      pbi.dequant_Y_coeffs[i] = (short)
        (( scale_factor * pbi.dequant_Y_coeffs[i] ) / 100);
      if ( pbi.dequant_Y_coeffs[i] < MIN_DEQUANT_VAL )
        pbi.dequant_Y_coeffs[i] = MIN_DEQUANT_VAL;
      pbi.dequant_Y_coeffs[i] = (short)
        (pbi.dequant_Y_coeffs[i] << IDCT_SCALE_FACTOR);
  
      pbi.dequant_UV_coeffs[i] = (short)
        (( scale_factor * pbi.dequant_UV_coeffs[i] ) / 100);
      if ( pbi.dequant_UV_coeffs[i] < MIN_DEQUANT_VAL )
        pbi.dequant_UV_coeffs[i] = MIN_DEQUANT_VAL;
      pbi.dequant_UV_coeffs[i] = (short)
        (pbi.dequant_UV_coeffs[i] << IDCT_SCALE_FACTOR);

      pbi.dequant_Inter_coeffs[i] = (short)
        (( scale_factor * pbi.dequant_Inter_coeffs[i] ) / 100);
      if ( pbi.dequant_Inter_coeffs[i] < (MIN_DEQUANT_VAL * 2) )
        pbi.dequant_Inter_coeffs[i] = MIN_DEQUANT_VAL * 2;
      pbi.dequant_Inter_coeffs[i] = (short)
        (pbi.dequant_Inter_coeffs[i] << IDCT_SCALE_FACTOR);

      pbi.dequant_InterUV_coeffs[i] = (short)
        (( scale_factor * pbi.dequant_InterUV_coeffs[i] ) / 100);
      if ( pbi.dequant_InterUV_coeffs[i] < (MIN_DEQUANT_VAL * 2) )
        pbi.dequant_InterUV_coeffs[i] = MIN_DEQUANT_VAL * 2;
      pbi.dequant_InterUV_coeffs[i] = (short)
        (pbi.dequant_InterUV_coeffs[i] << IDCT_SCALE_FACTOR);
    }
  }

  public static void UpdateQ(Playback pbi, int NewQ ){
    int qscale;

    /* Do bounds checking. */
    qscale = NewQ;
    if ( qscale < pbi.QThreshTable[Constants.Q_TABLE_SIZE-1] )
      qscale = pbi.QThreshTable[Constants.Q_TABLE_SIZE-1];
    else if ( qscale > pbi.QThreshTable[0] )
      qscale = pbi.QThreshTable[0];

    /* Set the inter/intra descision control variables. */
    pbi.FrameQIndex = Constants.Q_TABLE_SIZE - 1;
    while ( (int) pbi.FrameQIndex >= 0 ) {
      if ( (pbi.FrameQIndex == 0) ||
           ( pbi.QThreshTable[pbi.FrameQIndex] >= NewQ) )
        break;
      pbi.FrameQIndex --;
    }

    /* Re-initialise the q tables for forward and reverse transforms. */
    init_dequantizer (pbi, qscale, (byte)pbi.FrameQIndex );
  }
}
