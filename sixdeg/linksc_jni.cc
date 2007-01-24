/*
 * Six degrees of Wikipedia: JNI client interface.
 * This source code is released into the public domain.
 */

#pragma ident "@(#)linksc_jni.cc	1.1 05/11/21 21:00:23"
 
#include <string>
#include <vector>

#include <jni.h>

#include "linksc.h"

#include "org_wikimedia_links_linksc.h"

extern "C" JNIEXPORT jobjectArray JNICALL 
Java_org_wikimedia_links_linksc_findPath (JNIEnv *env, jobject o, jstring jfrom, jstring jto)
{
	std::string from, to;
	from = env->GetStringUTFChars(jfrom, 0);
	env->ReleaseStringUTFChars(jfrom, 0);
	to = env->GetStringUTFChars(jto, 0);
	env->ReleaseStringUTFChars(jto, 0);

	std::vector<std::string> result;
	jclass error;
	jobjectArray resultarr;
	int status = linksc_findpath(result, from, to);
	const char *errorstr = "An unknown error occured";
	switch (status) {
	case 0: errorstr = "Source article does not exist."; break;
	case 1: errorstr = "Target article does not exist."; break;
	case 3: errorstr = "Could not connect to links server."; break;
	}
	if (status < 2 || status == 3) {
		error = env->FindClass("org/wikimedia/links/ErrorException");
		env->ThrowNew(error, errorstr);
		return resultarr;
	}

	resultarr = (jobjectArray) env->NewObjectArray(result.size(),
			env->FindClass("java/lang/String"), env->NewStringUTF(""));

	for (int i = 0; i < result.size(); ++i) {
		env->SetObjectArrayElement(resultarr, i, env->NewStringUTF(result[i].c_str()));
	}
	return resultarr;	
}

