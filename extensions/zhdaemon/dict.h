#ifndef DICT_H
#define DICT_H
Tnode *loadSegmentationDictionary(const char *fname);
Tnode *loadConversionDictionary(const char *fname);
Tnode *loadAdditionalConversionDictionary(Tnode *tree, const char *fname);
#endif
