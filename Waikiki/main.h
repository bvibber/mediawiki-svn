#ifndef _COMMON_H_
#define _COMMON_H_

#define WINDOWS

#define FROM_TEXT 0
#define FROM_URL 1
#define FROM_DBKEY 2

#include <fstream>
#include "TUCS.h"
#include "TLanguage.h"
#include "TParser.h"
#include "TOutput.h"
#include "TSkin.h"
#include "TUser.h"
#include "TTitle.h"
#include "TArticle.h"
#include "TDatabase.h"
#include "TSpecialPages.h"

#define FOREACH(_a,_b) for ( _b = 0 ; _b < _a.size() ; _b++ )

#define USER (TUser::current)
#define SKIN (TUser::current->getSkin())
#define OUTPUT (TOutput::current)
#define LANG (TLanguage::current)
#define DB (TDatabase::current)

#define LNG(_x) (TLanguage::current->getTranslation(_x))
#define UC1(_x) (TLanguage::current->getUCfirst(_x))

using namespace std ;

#endif

